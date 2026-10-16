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
use Illuminate\Validation\Rules\Password;
use App\Http\Requests\ProfileUpdateRequest;


class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();

        $wilayah = Schema::hasTable('wilayah')
            ? DB::table('wilayah')->select('id','name')->orderBy('name')->get()
            : collect();

        return view('profile.edit', compact('user','wilayah'));
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        $fields = [
            'name', 'email', 'phone', 'angkatan', 'pekerjaan', 'wilayah_id', 'photo_url', 'status',
            'tempat_lahir', 'tanggal_lahir', 'pendidikan_terakhir', 'kampus', 'status_pernikahan', 'ab',
        ];
        $before = $user->only($fields);
        $oldPhotoUrl = $user->photo_url;
        $newPhotoPath = null;

        try {
            if ($request->hasFile('photo')) {
                $newPhotoPath = $request->file('photo')->store('avatars', 'public');
                if (!$newPhotoPath) {
                    throw new \RuntimeException('Gagal menyimpan foto profil.');
                }
            }

            $criticalChanged = DB::transaction(function () use ($request, $user, $fields, $before, $newPhotoPath) {
                $data = $request->safe()->except('photo');
                if ($newPhotoPath !== null) {
                    $data['photo_url'] = Storage::url($newPhotoPath);
                }
                $user->fill($data);

                if ($user->isDirty('email')) {
                    $user->email_verified_at = null;
                }

                $criticalChanged = $user->isDirty('wilayah_id') || $user->isDirty('angkatan');
                if ($criticalChanged) {
                    $user->status = User::STATUS_PENDING;
                }

                $user->save();

                $changes = [];
                foreach ($user->only($fields) as $field => $value) {
                    if (($before[$field] ?? null) != $value) {
                        $changes[$field] = ['before' => $before[$field] ?? null, 'after' => $value];
                    }
                }

                if ($changes) {
                    AuditLog::create([
                        'actor_id' => $user->id,
                        'action' => 'profile.update',
                        'entity_type' => 'user',
                        'entity_id' => $user->id,
                        'payload_json' => $changes,
                        'created_at' => now(),
                    ]);
                }

                if ($criticalChanged) {
                    $user->notify(new \App\Notifications\AccountStatusNotification('pending', [
                        'by' => $user->name,
                    ]));
                }

                return $criticalChanged;
            });
        } catch (\Throwable $error) {
            if ($newPhotoPath !== null) {
                Storage::disk('public')->delete($newPhotoPath);
            }
            throw $error;
        }

        if ($newPhotoPath !== null && is_string($oldPhotoUrl)
            && preg_match('#^/storage/avatars/[^/]+$#', $oldPhotoUrl)) {
            Storage::disk('public')->delete(substr($oldPhotoUrl, strlen('/storage/')));
        }

        return Redirect::to('/profile')->with(
            'success',
            $criticalChanged
                ? 'Profil disimpan. Perubahan wilayah/angkatan memerlukan verifikasi. Status akun: pending.'
                : 'Profil disimpan.'
        );
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['bail', 'required', 'string', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        DB::transaction(function () use ($user) {
            AuditLog::create([
                'actor_id' => $user->id,
                'action' => 'profile.delete',
                'entity_type' => 'user',
                'entity_id' => $user->id,
                'payload_json' => $user->only(['name', 'email']),
                'created_at' => now(),
            ]);
            $user->delete();
        });

        Auth::logoutCurrentDevice();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
