<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerificationApproveRequest;
use App\Http\Requests\AlumniIndexRequest;
use App\Notifications\AccountStatusNotification;
use App\Http\Requests\VerificationRejectRequest;
use App\Models\AuditLog;
use App\Models\Level;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerificationController extends Controller
{
    public function index(AlumniIndexRequest $request)
    {
        $actor = $request->user();
        abort_if(! $actor->hasAnyRole(['super_admin', 'admin']) && ! $actor->wilayah_id, 403, 'Wilayah belum ditetapkan pada akun.');
        $filters = array_intersect_key($request->validated(), array_flip(['q', 'sort', 'dir']));

        return redirect()->route('alumni.index', array_merge($filters, ['view' => 'verification']));
    }

    public function approve(VerificationApproveRequest $request, User $user)
    {
        $me = $request->user();

        // Cegah approve diri sendiri
        if ($user->id === $me->id) {
            return $this->failed($request, 'Tidak boleh memverifikasi akun sendiri.', 403);
        }

        $approved = DB::transaction(function () use ($request, $user, $me) {
            $user = User::query()->lockForUpdate()->findOrFail($user->id);

            if (!$me->hasAnyRole(['super_admin', 'admin'])
                && (!$me->wilayah_id || (int) $user->wilayah_id !== (int) $me->wilayah_id)) {
                abort(403);
            }

            if ($blockedRoles = $this->blockedRoles($me)) {
                abort_if($user->roles()->whereIn('name', $blockedRoles)->exists(), 403);
            }


            if ($user->status !== User::STATUS_PENDING) {
                return false;
            }

            $levelId = (int) $request->input('level_id');

            // set level & aktifkan
            $before = $user->only(['level_id','status']);
            $user->level_id = $levelId;
            $user->status   = User::STATUS_ACTIVE;
            $user->save();
            $user->notify(new AccountStatusNotification('approved', [
                'level_id'   => $user->level_id,
                'level_name' => optional($user->level)->name,
                'note'       => $request->input('note'),
                'by'         => $me->name,
            ]));

            // pastikan user minimal punya role 'alumni'
            $alumni = Role::firstOrCreate(['name' => 'alumni']);
            if (!$user->roles()->where('roles.id', $alumni->id)->exists()) {
                $user->roles()->attach($alumni->id);
            }

            // Audit
            AuditLog::create([
                'actor_id'    => $me->id,
                'action'      => 'verification.approve',
                'entity_type' => 'user',
                'entity_id'   => $user->id,
                'payload_json'=> [
                    'before' => $before,
                    'after'  => ['level_id' => $user->level_id, 'status' => $user->status],
                    'note'   => $request->input('note'),
                ],
                'created_at'  => now(),
            ]);

            return true;
        });

        if (!$approved) {
            return $this->failed($request, 'Akun ini tidak dalam status pending.', 409);
        }

        return $this->completed($request, "Akun {$user->name} disetujui dan diaktifkan.");
    }

    public function reject(VerificationRejectRequest $request, User $user)
    {
        $me = $request->user();

        if ($user->id === $me->id) {
            return $this->failed($request, 'Tidak boleh menolak akun sendiri.', 403);
        }

        $rejected = DB::transaction(function () use ($request, $user, $me) {
            $user = User::query()->lockForUpdate()->findOrFail($user->id);

            if (!$me->hasAnyRole(['super_admin', 'admin'])
                && (!$me->wilayah_id || (int) $user->wilayah_id !== (int) $me->wilayah_id)) {
                abort(403);
            }

            if ($blockedRoles = $this->blockedRoles($me)) {
                abort_if($user->roles()->whereIn('name', $blockedRoles)->exists(), 403);
            }


            if ($user->status !== User::STATUS_PENDING) {
                return false;
            }

            AuditLog::create([
                'actor_id'    => $me->id,
                'action'      => 'verification.reject',
                'entity_type' => 'user',
                'entity_id'   => $user->id,
                'payload_json' => ['reason' => $request->input('reason')],
                'created_at'  => now(),
            ]);

            $user->notify(new AccountStatusNotification('rejected', [
                'reason' => $request->input('reason'),
                'by'     => $me->name,
            ]));

            return true;
        });

        if (!$rejected) {
            return $this->failed($request, 'Akun ini tidak dalam status pending.', 409);
        }

        return $this->completed($request, "Alasan perbaikan untuk {$user->name} tersimpan. Akun tetap menunggu verifikasi.");
    }

    private function failed(Request $request, string $message, int $status)
    {
        return $request->expectsJson()
            ? response()->json(['message' => $message], $status)->header('Cache-Control', 'private, no-store')
            : back()->withErrors(['msg' => $message]);
    }

    private function completed(Request $request, string $message)
    {
        return $request->expectsJson()
            ? response()->json(['message' => $message])->header('Cache-Control', 'private, no-store')
            : ($request->filled('_return') ? redirect($request->input('_return')) : back())->with('success', $message);
    }

    private function blockedRoles(User $actor): array
    {
        if ($actor->hasAnyRole(['super_admin', 'admin'])) {
            return [];
        }

        return ['super_admin', 'admin', 'koorda'];
    }
}
