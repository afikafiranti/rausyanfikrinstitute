<?php

namespace App\Support;

use App\Models\KajianReport;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class AccountIntegrity
{
    public static function lockUser(User $user): User
    {
        // The role rows serialize changes that could remove the last active manager.
        Role::query()->whereIn('name', ['admin', 'super_admin'])
            ->orderBy('id')->lockForUpdate()->get(['id']);

        return User::query()->lockForUpdate()->findOrFail($user->id);
    }

    public static function deactivationError(User $user): ?string
    {
        if ($user->status !== User::STATUS_ACTIVE || ! $user->hasAnyRole(['admin', 'super_admin'])) {
            return null;
        }

        $others = User::query()->where('status', User::STATUS_ACTIVE)
            ->whereKeyNot($user->id);

        if (! (clone $others)->whereHas('roles', fn ($query) => $query->whereIn('name', ['admin', 'super_admin']))->exists()) {
            return 'Pengelola aktif terakhir tidak dapat dinonaktifkan atau dihapus.';
        }

        if ($user->hasRole('super_admin')
            && ! (clone $others)->whereHas('roles', fn ($query) => $query->where('name', 'super_admin'))->exists()) {
            return 'Super admin aktif terakhir tidak dapat dinonaktifkan atau dihapus.';
        }

        return null;
    }

    public static function deletionError(User $user): ?string
    {
        if (KajianReport::query()->where('user_id', $user->id)
            ->orWhere('reviewed_by', $user->id)->exists()) {
            return 'Akun memiliki laporan kajian sebagai penulis atau penanggap. Hapus akun ditolak agar riwayat laporan tetap utuh.';
        }

        return self::deactivationError($user);
    }

    public static function removeOwnedAvatar(?string $url): void
    {
        if (! $url || ! preg_match('#\A/storage/avatars/([a-zA-Z0-9][a-zA-Z0-9._-]*)\z#', $url, $matches)) {
            return;
        }

        try {
            $path = 'avatars/'.$matches[1];
            if (Storage::disk('public')->exists($path) && ! Storage::disk('public')->delete($path)) {
                report(new \RuntimeException('Avatar akun yang dihapus gagal dibersihkan.'));
            }
        } catch (Throwable $error) {
            report($error);
        }
    }
}
