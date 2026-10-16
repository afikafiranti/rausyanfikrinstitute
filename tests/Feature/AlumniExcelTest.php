<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;
use ZipArchive;

class AlumniExcelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_imports_excel_with_text_phone_and_pending_role(): void
    {
        $region = Wilayah::create(['name' => 'Jakarta']);
        $admin = $this->account('admin');
        $sheet = $this->sheet();
        $sheet->setCellValue('A2', 'Nadia');
        $sheet->setCellValue('B2', 'nadia@example.test');
        $sheet->setCellValue('C2', 'Jakarta');
        $sheet->setCellValueExplicit('D2', '08123456789', DataType::TYPE_STRING);
        $sheet->setCellValue('E2', 2020);
        $sheet->setCellValue('F2', 'Guru');

        $this->actingAs($admin)->post(route('alumni.csv.import'), ['file' => $this->upload($sheet->getParent())])
            ->assertOk()->assertSee('1 alumni berhasil diimpor');
        $user = User::where('email', 'nadia@example.test')->firstOrFail();
        $this->assertSame('08123456789', $user->phone);
        $this->assertSame(User::STATUS_PENDING, $user->status);
        $this->assertNull($user->level_id);
        $this->assertTrue($user->hasRole('alumni'));
    }

    public function test_legacy_excel_with_wilayah_id_still_imports(): void
    {
        $region = Wilayah::create(['name' => 'Jakarta']);
        $admin = $this->account('admin');
        $sheet = $this->sheet();
        $sheet->setCellValue('C1', 'wilayah_id');
        $sheet->setCellValue('A2', 'Nadia');
        $sheet->setCellValue('B2', 'nadia@example.test');
        $sheet->setCellValue('C2', $region->id);

        $this->actingAs($admin)->post(route('alumni.csv.import'), ['file' => $this->upload($sheet->getParent())])
            ->assertOk()->assertSee('1 alumni berhasil diimpor');
        $this->assertDatabaseHas('users', ['email' => 'nadia@example.test', 'wilayah_id' => $region->id]);
    }

    public function test_excel_formula_is_reported_without_importing_that_row(): void
    {
        $region = Wilayah::create(['name' => 'Jakarta']);
        $admin = $this->account('admin');
        $sheet = $this->sheet();
        $sheet->setCellValue('A2', 'Formula');
        $sheet->setCellValue('B2', 'formula@example.test');
        $sheet->setCellValue('C2', 'Jakarta');
        $sheet->setCellValue('D2', '=1+1');
        $this->actingAs($admin)->post(route('alumni.csv.import'), ['file' => $this->upload($sheet->getParent())])
            ->assertOk()->assertSee('Baris 2');
        $this->assertDatabaseMissing('users', ['email' => 'formula@example.test']);
    }

    public function test_extra_excel_column_and_late_row_are_rejected_before_import(): void
    {
        $region = Wilayah::create(['name' => 'Jakarta']);
        $admin = $this->account('admin');
        $sheet = $this->sheet();
        $sheet->setCellValue('A2', 'Valid');
        $sheet->setCellValue('B2', 'valid@example.test');
        $sheet->setCellValue('C2', 'Jakarta');
        $sheet->setCellValue('G3', 'admin');
        $this->actingAs($admin)->post(route('alumni.csv.import'), ['file' => $this->upload($sheet->getParent())])
            ->assertSessionHasErrors('file');
        $this->assertDatabaseMissing('users', ['email' => 'valid@example.test']);

        $sheet->removeColumn('G');
        $sheet->setCellValue('A202', 'Too late');
        $this->post(route('alumni.csv.import'), ['file' => $this->upload($sheet->getParent())])
            ->assertSessionHasErrors('file');
        $this->assertDatabaseMissing('users', ['email' => 'valid@example.test']);
    }

    public function test_extra_header_column_is_rejected(): void
    {
        $admin = $this->account('admin');
        $sheet = $this->sheet();
        $sheet->setCellValue('G1', 'role');
        $this->actingAs($admin)->post(route('alumni.csv.import'), ['file' => $this->upload($sheet->getParent())])
            ->assertSessionHasErrors('file');
    }

    public function test_invalid_xlsx_is_a_file_error(): void
    {
        $admin = $this->account('admin');
        $file = UploadedFile::fake()->createWithContent('alumni.xlsx', 'not a workbook');
        $this->actingAs($admin)->post(route('alumni.csv.import'), ['file' => $file])
            ->assertSessionHasErrors('file');
    }

    public function test_numeric_phone_cell_is_reported_instead_of_silently_losing_leading_zero(): void
    {
        $region = Wilayah::create(['name' => 'Jakarta']);
        $admin = $this->account('admin');
        $sheet = $this->sheet();
        $sheet->setCellValue('A2', 'Nadia');
        $sheet->setCellValue('B2', 'nadia@example.test');
        $sheet->setCellValue('C2', 'Jakarta');
        $sheet->setCellValue('D2', 8123456789);

        $this->actingAs($admin)->post(route('alumni.csv.import'), ['file' => $this->upload($sheet->getParent())])
            ->assertOk()->assertSee('Baris 2');
        $this->assertDatabaseMissing('users', ['email' => 'nadia@example.test']);
    }

    public function test_compressed_excel_with_large_expanded_data_is_rejected_before_import(): void
    {
        $region = Wilayah::create(['name' => 'Jakarta']);
        $admin = $this->account('admin');
        $sheet = $this->sheet();
        $sheet->setCellValue('A2', 'Nadia');
        $sheet->setCellValue('B2', 'nadia@example.test');
        $sheet->setCellValue('C2', 'Jakarta');
        $file = $this->upload($sheet->getParent());
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($file->getRealPath()) === true);
        $zip->addFromString('xl/media/large.bin', str_repeat('A', 11 * 1024 * 1024));
        $zip->close();

        $this->actingAs($admin)->post(route('alumni.csv.import'), ['file' => $file])
            ->assertSessionHasErrors('file');
        $this->assertDatabaseMissing('users', ['email' => 'nadia@example.test']);
    }

    public function test_multiple_worksheets_are_rejected_before_import(): void
    {
        $region = Wilayah::create(['name' => 'Jakarta']);
        $admin = $this->account('admin');
        $sheet = $this->sheet();
        $sheet->setCellValue('A2', 'Nadia');
        $sheet->setCellValue('B2', 'nadia@example.test');
        $sheet->setCellValue('C2', 'Jakarta');
        $sheet->getParent()->createSheet()->setTitle('Hidden Data');

        $this->actingAs($admin)->post(route('alumni.csv.import'), ['file' => $this->upload($sheet->getParent())])
            ->assertSessionHasErrors('file');
        $this->assertDatabaseMissing('users', ['email' => 'nadia@example.test']);
    }

    public function test_excel_template_has_exact_headers_and_text_phone_column(): void
    {
        $admin = $this->account('admin');
        $response = $this->actingAs($admin)->get(route('alumni.excel.template'));
        $response->assertOk();
        $sheet = $this->downloadSheet($response->streamedContent());
        $this->assertSame(['name', 'email', 'wilayah', 'phone', 'angkatan', 'pekerjaan'],
            $sheet->rangeToArray('A1:F1')[0]);
        $this->assertSame('@', $sheet->getStyle('D')->getNumberFormat()->getFormatCode());
    }

    public function test_excel_export_uses_scope_and_writes_formula_like_values_as_text(): void
    {
        $own = Wilayah::create(['name' => 'Jakarta']);
        $other = Wilayah::create(['name' => 'Bandung']);
        $koorda = $this->account('koorda', $own->id);
        User::factory()->create(['name' => '=2+2', 'email' => 'own@example.test', 'wilayah_id' => $own->id, 'phone' => '08123']);
        User::factory()->create(['name' => 'Outside', 'email' => 'outside@example.test', 'wilayah_id' => $other->id]);

        $response = $this->actingAs($koorda)->get(route('alumni.excel.export', ['q' => 'own@example.test']));
        $response->assertOk();
        $sheet = $this->downloadSheet($response->streamedContent());
        $this->assertSame('=2+2', $sheet->getCell('A2')->getValue());
        $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('A2')->getDataType());
        $this->assertSame('08123', $sheet->getCell('D2')->getValue());
        $this->assertSame('Jakarta', $sheet->getCell('C2')->getValue());
        $this->assertSame(2, $sheet->getHighestDataRow());

        $all = $this->get(route('alumni.excel.export'));
        $emails = array_column($this->downloadSheet($all->streamedContent())->toArray(), 1);
        $this->assertContains('own@example.test', $emails);
        $this->assertNotContains('outside@example.test', $emails);
    }

    private function account(string $role, ?int $region = null): User
    {
        $user = User::factory()->create(['status' => User::STATUS_ACTIVE, 'wilayah_id' => $region]);
        $user->roles()->attach(Role::firstOrCreate(['name' => $role]));
        return $user;
    }

    private function sheet(): \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
    {
        $book = new Spreadsheet();
        $sheet = $book->getActiveSheet();
        $sheet->fromArray(['name', 'email', 'wilayah', 'phone', 'angkatan', 'pekerjaan']);
        return $sheet;
    }

    private function upload(Spreadsheet $book): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'alumni_excel_');
        (new Xlsx($book))->save($path);
        $this->beforeApplicationDestroyed(fn () => @unlink($path));
        return new UploadedFile($path, 'alumni.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }

    private function downloadSheet(string $content): \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
    {
        $path = tempnam(sys_get_temp_dir(), 'alumni_download_');
        file_put_contents($path, $content);
        $this->beforeApplicationDestroyed(fn () => @unlink($path));
        return IOFactory::load($path)->getActiveSheet();
    }
}
