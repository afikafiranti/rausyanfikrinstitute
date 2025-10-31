<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;


class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        // $user = $request->user();
        // $wilayah = DB::table('wilayah')->select('id', 'name')->orderBy('name')->get();

        // return view('profile.edit', compact('user', 'wilayah'));
            $user = $request->user();

        $wilayah = Schema::hasTable('wilayah')
            ? DB::table('wilayah')->select('id','name')->orderBy('name')->get()
            : collect(); // CI/testing tanpa tabel 'wilayah'

        return view('profile.edit', compact('user','wilayah'));
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();

        $before = $user->only([
            'name', 'email', 'phone', 'angkatan', 'pekerjaan', 'wilayah_id', 'photo_url', 'status','tempat_lahir','tanggal_lahir','pendidikan_terakhir','kampus','status_pernikahan'
        ]);

        // Upload foto (opsional)
        $photoUrl = $user->photo_url;
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('avatars', 'public');
            $photoUrl = Storage::url($path);
        }

        $newEmail = (string) $request->input('email');

        // Isi data baru
        $user->fill([
            'name'       => (string) $request->input('name'),
            'email'      => $newEmail,
            'phone'      => $request->input('phone'),
            'tempat_lahir'      => $request->input('tempat_lahir'),
            'tanggal_lahir'      => $request->input('tanggal_lahir'),
            'pendidikan_terakhir'      => $request->input('pendidikan_terakhir'),
            'kampus'      => $request->input('kampus'),
            'status_pernikahan'      => $request->input('status_pernikahan'),
            'angkatan'   => $request->input('angkatan'),
            'pekerjaan'  => $request->input('pekerjaan'),
            'wilayah_id' => $request->integer('wilayah_id') ?: null,
            'photo_url'  => $photoUrl,
            
        ]);

        // Reset verifikasi jika email berubah (sesuai ekspektasi test)
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Jika wilayah/angkatan berubah → pending
        $criticalChanged = $user->isDirty('wilayah_id') || $user->isDirty('angkatan');
        if ($criticalChanged) {
            $user->status = User::STATUS_PENDING;
        }

        $user->save();

        if ($criticalChanged) {
            $user->notify(new \App\Notifications\AccountStatusNotification('pending', [
                'by' => $request->user()->name,
            ]));
        }

        // Audit perubahan
        $after = $user->only([
            'name', 'email', 'phone', 'angkatan', 'pekerjaan', 'wilayah_id', 'photo_url', 'status','tempat_lahir','tanggal_lahir','pendidikan_terakhir','kampus','status_pernikahan'
        ]);

        $changes = [];
        foreach ($after as $k => $v) {
            $old = $before[$k] ?? null;
            if ($old != $v) {
                $changes[$k] = ['before' => $old, 'after' => $v];
            }
        }

        if ($changes) {
            AuditLog::create([
                'actor_id'     => $user->id,
                'action'       => 'profile.update',
                'entity_type'  => 'user',
                'entity_id'    => $user->id,
                'payload_json' => json_encode($changes),
                'created_at'   => now(),
            ]);
        }

        // Test mengharapkan redirect ke /profile
        return Redirect::to('/profile')->with(
            'success',
            $criticalChanged
                ? 'Profil disimpan. Perubahan wilayah/angkatan memerlukan verifikasi. Status akun: pending.'
                : 'Profil disimpan.'
        );
    }
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        // Validasi input
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        // Cek apakah password lama sesuai
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak cocok.',
            ]);
        }

        // Update hanya kolom password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        // Wajib: error bag 'userDeletion' agar assertSessionHasErrorsIn lulus saat salah password
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Test mengharapkan redirect ke '/'
        return Redirect::to('/');
    }
    
}
