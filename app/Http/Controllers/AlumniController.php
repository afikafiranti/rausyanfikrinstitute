<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AlumniIndexRequest;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\Wilayah;
use App\Models\Level;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class AlumniController extends Controller
{
    public function index(AlumniIndexRequest $request)
    {
        $me = $request->user();
        $canViewAll = $me->hasAnyRole(['super_admin', 'admin']);
        $scopedUsers = User::alumniListing($me, [])->reorder();
        $filters = $request->validated();
        $q = trim($filters['q'] ?? '');
        $angkatan = (string) ($filters['angkatan'] ?? '');
        $wilayahId = (int) ($filters['wilayah_id'] ?? 0);
        $levelId = (int) ($filters['level_id'] ?? 0);
        $ab = $filters['ab'] ?? '';
        $perPage = (int) ($filters['per_page'] ?? 15);
        $hasFilters = $q !== '' || $angkatan !== '' || $wilayahId > 0 || $levelId > 0 || $ab !== '';
        $sort = $filters['sort'] ?? 'created_at';
        $dir = ($filters['dir'] ?? '') === 'asc' ? 'asc' : 'desc';
        $alumni = User::alumniListing($me, $filters)
            ->with(['wilayah:id,name', 'level:id,description', 'roles:id,name'])
            ->paginate($perPage)->appends($request->query());

        // Dropdown data
        $wilayah = Wilayah::select('id', 'name')
            ->when(!$canViewAll, fn ($query) => $query->whereKey($me->wilayah_id))
            ->orderBy('name')->get();
        $level   = Level::select('id', 'description')->orderBy('description')->get();
        $totalAlumni = (clone $scopedUsers)->count();

        $activeFilters = [];
        if ($q !== '') {
            $activeFilters[] = ['label' => 'Cari', 'value' => $q];
        }
        if ($angkatan !== '') {
            $activeFilters[] = ['label' => 'Angkatan', 'value' => $angkatan];
        }
        if ($wilayahId) {
            $activeFilters[] = [
                'label' => 'Wilayah',
                'value' => optional($wilayah->firstWhere('id', $wilayahId))->name ?? $wilayahId,
            ];
        }
        if ($levelId) {
            $activeFilters[] = [
                'label' => 'Level',
                'value' => optional($level->firstWhere('id', $levelId))->description ?? $levelId,
            ];
        }
        if ($ab !== '') {
            $activeFilters[] = ['label' => 'AB', 'value' => $ab === 'iya' ? 'Iya' : 'Tidak'];
        }

        // List angkatan
        $angkatanList = (clone $scopedUsers)->whereNotNull('angkatan')
                            ->distinct()
                            ->orderBy('angkatan')
                            ->pluck('angkatan');

        return view('alumni.index', compact(
            'alumni', 'wilayah', 'level', 'angkatanList',
            'q', 'angkatan', 'wilayahId', 'levelId', 'perPage', 'sort', 'dir', 'ab',
            'hasFilters', 'activeFilters', 'totalAlumni'
        ));
    }

    public function store(Request $request)
    {
        $actor = $request->user();
        $canChooseRegion = $actor->hasAnyRole(['super_admin', 'admin']);
        abort_if(!$canChooseRegion && !$actor->wilayah_id, 403, 'Wilayah belum ditetapkan pada akun.');

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'wilayah_id' => [$canChooseRegion ? 'required' : 'nullable', 'integer', 'exists:wilayah,id'],
        ]);

        $user = DB::transaction(function () use ($validated, $actor, $canChooseRegion) {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'status'   => User::STATUS_PENDING,
                'wilayah_id' => $canChooseRegion ? $validated['wilayah_id'] : $actor->wilayah_id,
            ]);

            $alumni = Role::firstOrCreate(['name' => 'alumni']);
            $user->roles()->syncWithoutDetaching([$alumni->id]);

            return $user;
        });

        return redirect()
            ->route('alumni.index')
            ->with('success', "Alumni {$user->name} berhasil ditambahkan.");
    }

    public function show($id)
    {
        $me = request()->user();
        $canViewAll = $me->hasAnyRole(['super_admin', 'admin']);
        abort_if(!$canViewAll && !$me->wilayah_id, 403, 'Wilayah belum ditetapkan pada akun.');

        $alumni = User::with([
            'wilayah:id,name',
            'level:id,description',
            'roles:id,name'   // <-- AGAR ROLE TAMPIL DI HALAMAN DETAIL
        ])->when(!$canViewAll, fn ($query) => $query->where('wilayah_id', $me->wilayah_id))
            ->findOrFail($id);

        $levels = Level::where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'name', 'description']);
        $canUpdateLevel = request()->user()?->hasAnyRole(['super_admin', 'admin'])
            && $alumni->hasAnyRole(['alumni', 'koorda'])
            && !$alumni->hasAnyRole(['super_admin', 'admin']);
        $canEditProfile = $this->canManageAlumniProfile(request()->user(), $alumni);

        return view('alumni.show', compact('alumni', 'levels', 'canUpdateLevel', 'canEditProfile'));
    }

    public function edit(Request $request, User $user)
    {
        if (!$this->canManageAlumniProfile($request->user(), $user)) {
            return back()->withErrors(['edit' => 'Hanya super admin dan admin yang dapat mengedit data alumni atau koorda.']);
        }

        $alumni = $user->load(['wilayah:id,name', 'level:id,description', 'roles:id,name']);
        $wilayah = Wilayah::select('id', 'name')->orderBy('name')->get();

        return view('alumni.edit', compact('alumni', 'wilayah'));
    }

    public function update(Request $request, User $user)
    {
        $actor = $request->user();

        if (!$this->canManageAlumniProfile($actor, $user)) {
            return back()->withErrors(['edit' => 'Hanya super admin dan admin yang dapat mengedit data alumni atau koorda.']);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('users', 'phone')->ignore($user->id)],
            'angkatan' => ['nullable', 'integer', 'between:0,65535'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'wilayah_id' => ['nullable', 'integer', 'exists:wilayah,id'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date_format:Y-m-d'],
            'pendidikan_terakhir' => ['nullable', 'string', 'max:100'],
            'kampus' => ['nullable', 'string', 'max:150'],
            'status_pernikahan' => ['nullable', 'in:lajang,menikah,duda/janda'],
            'ab' => ['nullable', 'in:iya,tidak'],
        ]);

        $fields = [
            'name', 'email', 'phone', 'angkatan', 'pekerjaan', 'wilayah_id',
            'tempat_lahir', 'tanggal_lahir', 'pendidikan_terakhir', 'kampus',
            'status_pernikahan', 'ab', 'email_verified_at', 'status',
        ];
        $criticalChanged = DB::transaction(function () use ($user, $actor, $validated, $fields) {
            $before = $user->only($fields);
            $user->fill($validated);
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
            $criticalChanged = $user->isDirty('wilayah_id') || $user->isDirty('angkatan');
            if ($criticalChanged) {
                $user->status = User::STATUS_PENDING;
            }
            $user->save();

            $after = $user->fresh()->only($fields);
            $changes = [];
            foreach ($after as $key => $value) {
                $old = $before[$key] ?? null;
                if ($old != $value) {
                    $changes[$key] = ['before' => $old, 'after' => $value];
                }
            }

            if ($changes) {
                AuditLog::create([
                    'actor_id' => $actor->id,
                    'action' => 'alumni.update',
                    'entity_type' => 'user',
                    'entity_id' => $user->id,
                    'payload_json' => $changes,
                    'created_at' => now(),
                ]);
            }

            if ($criticalChanged) {
                $user->notify(new \App\Notifications\AccountStatusNotification('pending', ['by' => $actor->name]));
            }

            return $criticalChanged;
        });

        return redirect()
            ->route('alumni.show', $user)
            ->with('success', $criticalChanged
                ? "Data alumni {$user->name} diperbarui. Perubahan wilayah/angkatan menunggu verifikasi."
                : "Data alumni {$user->name} berhasil diperbarui.");
    }

    public function updateLevel(Request $request, User $user)
    {
        $actor = $request->user();

        if (!$actor->hasAnyRole(['super_admin', 'admin'])) {
            return back()->withErrors(['level' => 'Hanya super admin dan admin yang dapat mengubah level materi.']);
        }

        if ($user->hasAnyRole(['super_admin', 'admin']) || !$user->hasAnyRole(['alumni', 'koorda'])) {
            return back()->withErrors(['level' => 'Level materi hanya dapat diubah untuk akun alumni dan koorda.']);
        }

        $validated = $request->validate([
            'level_id' => ['required', 'integer', Rule::exists('levels', 'id')->where('is_active', true)],
        ]);

        DB::transaction(function () use ($user, $actor, $validated) {
            $before = $user->level_id;
            $user->forceFill(['level_id' => (int) $validated['level_id']])->save();

            AuditLog::create([
                'actor_id' => $actor->id,
                'action' => 'alumni.level.update',
                'entity_type' => 'user',
                'entity_id' => $user->id,
                'payload_json' => [
                    'before' => ['level_id' => $before],
                    'after' => ['level_id' => $user->level_id],
                ],
                'created_at' => now(),
            ]);
        });

        return redirect()
            ->route('alumni.show', $user)
            ->with('success', "Level materi {$user->name} berhasil diperbarui.");
    }

    public function destroy(Request $request, User $user)
    {
        $actor = $request->user();

        if (!$actor->hasAnyRole(['super_admin', 'admin'])) {
            return back()->withErrors(['delete' => 'Hanya super admin dan admin yang dapat menghapus akun alumni atau koorda.']);
        }

        if ($user->is($request->user())) {
            return back()->withErrors(['delete' => 'Tidak boleh menghapus akun yang sedang digunakan.']);
        }

        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return back()->withErrors(['delete' => 'Akun super admin dan admin tidak dapat dihapus dari daftar alumni.']);
        }

        if (!$user->hasAnyRole(['alumni', 'koorda'])) {
            return back()->withErrors(['delete' => 'Hanya akun alumni dan koorda yang dapat dihapus dari halaman ini.']);
        }

        $name = $user->name;

        DB::transaction(function () use ($user, $actor) {
            AuditLog::create([
                'actor_id' => $actor->id,
                'action' => 'alumni.delete',
                'entity_type' => 'user',
                'entity_id' => $user->id,
                'payload_json' => ['before' => $user->only(['name', 'email', 'status', 'wilayah_id', 'level_id'])],
                'created_at' => now(),
            ]);
            $user->roles()->detach();
            $user->delete();
        });

        return redirect()
            ->route('alumni.index')
            ->with('success', "Data alumni {$name} berhasil dihapus.");
    }

    private function canManageAlumniProfile(User $actor, User $target): bool
    {
        return $actor->hasAnyRole(['super_admin', 'admin'])
            && $target->hasAnyRole(['alumni', 'koorda'])
            && !$target->hasAnyRole(['super_admin', 'admin']);
    }
}
