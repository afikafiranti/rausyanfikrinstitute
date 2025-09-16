<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerificationApproveRequest;
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

        if (!$me->isAdminLike()) {
            // Koorda hanya lihat user di wilayahnya
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

        // Koorda hanya wilayahnya sendiri
        if (!$me->isAdminLike() && $user->wilayah_id !== $me->wilayah_id) {
            abort(403);
        }

        if ($user->status !== User::STATUS_PENDING) {
            return back()->withErrors(['msg' => 'Akun ini tidak dalam status pending.']);
        }

        DB::transaction(function () use ($request, $user, $me) {
            $levelId = (int) $request->input('level_id');

            // set level & aktifkan
            $before = $user->only(['level_id','status']);
            $user->level_id = $levelId;
            $user->status   = User::STATUS_ACTIVE;
            $user->save();

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
                'payload_json'=> json_encode([
                    'before' => $before,
                    'after'  => ['level_id' => $user->level_id, 'status' => $user->status],
                    'note'   => $request->input('note'),
                ]),
                'created_at'  => now(),
            ]);
        });

        return back()->with('success', "Akun {$user->name} disetujui & diaktifkan.");
    }

    public function reject(VerificationRejectRequest $request, User $user)
    {
        $me = $request->user();

        if ($user->id === $me->id) {
            return back()->withErrors(['msg' => 'Tidak boleh menolak akun sendiri.']);
        }

        if (!$me->isAdminLike() && $user->wilayah_id !== $me->wilayah_id) {
            abort(403);
        }

        if ($user->status !== User::STATUS_PENDING) {
            return back()->withErrors(['msg' => 'Akun ini tidak dalam status pending.']);
        }

        // Catat alasan di audit; status biarkan tetap pending (user bisa perbaiki profil)
        AuditLog::create([
            'actor_id'    => $me->id,
            'action'      => 'verification.reject',
            'entity_type' => 'user',
            'entity_id'   => $user->id,
            'payload_json'=> json_encode(['reason' => $request->string('reason')]),
            'created_at'  => now(),
        ]);

        return back()->with('success', "Akun {$user->name} ditolak. Alasan tersimpan.");
    }
}
