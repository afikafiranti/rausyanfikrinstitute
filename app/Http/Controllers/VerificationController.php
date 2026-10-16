<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerificationApproveRequest;
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
    public function index(Request $request)
    {
        $me = $request->user();

        $query = User::query()
            ->where('status', User::STATUS_PENDING)
            ->with(['wilayah:id,name', 'level:id,name']);

        if (!$me->hasAnyRole(['super_admin', 'admin'])) {
            abort_unless($me->wilayah_id, 403, 'Wilayah belum ditetapkan pada akun.');
            $query->where('wilayah_id', $me->wilayah_id);
        }

        $pending = $query->orderBy('name')->paginate(12)->withQueryString();
        $levels  = Level::where('is_active', true)->orderBy('id')->get(['id','name']);

        return view('verification.index', compact('pending','levels','me'));
    }

    public function approve(VerificationApproveRequest $request, User $user)
    {
        $me = $request->user();

        // Cegah approve diri sendiri
        if ($user->id === $me->id) {
            return back()->withErrors(['msg' => 'Tidak boleh memverifikasi akun sendiri.']);
        }

        $approved = DB::transaction(function () use ($request, $user, $me) {
            $user = User::query()->lockForUpdate()->findOrFail($user->id);

            if (!$me->hasAnyRole(['super_admin', 'admin'])
                && (!$me->wilayah_id || (int) $user->wilayah_id !== (int) $me->wilayah_id)) {
                abort(403);
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
            return back()->withErrors(['msg' => 'Akun ini tidak dalam status pending.']);
        }

        return back()->with('success', "Akun {$user->name} disetujui & diaktifkan.");
    }

    public function reject(VerificationRejectRequest $request, User $user)
    {
        $me = $request->user();

        if ($user->id === $me->id) {
            return back()->withErrors(['msg' => 'Tidak boleh menolak akun sendiri.']);
        }

        $rejected = DB::transaction(function () use ($request, $user, $me) {
            $user = User::query()->lockForUpdate()->findOrFail($user->id);

            if (!$me->hasAnyRole(['super_admin', 'admin'])
                && (!$me->wilayah_id || (int) $user->wilayah_id !== (int) $me->wilayah_id)) {
                abort(403);
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
            return back()->withErrors(['msg' => 'Akun ini tidak dalam status pending.']);
        }

        return back()->with('success', "Akun {$user->name} ditolak. Alasan tersimpan.");
    }
}
