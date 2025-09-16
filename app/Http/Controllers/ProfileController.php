<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest; 
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        $wilayah = DB::table('wilayah')->select('id','name')->orderBy('name')->get();
        return view('profile.edit', compact('user','wilayah'));
    }

    public function update(ProfileUpdateRequest $request) 
    {
        $user = $request->user();

        $before = $user->only([
            'name','email','phone','angkatan','pekerjaan','wilayah_id','photo_url','status',
        ]);

        // Upload foto (opsional)
        $photoUrl = $user->photo_url;
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('avatars','public');
            $photoUrl = Storage::url($path);
        }

        // Isi data baru
        $user->fill([
            'name'       => (string) $request->input('name'),
            'email'      => (string) $request->input('email'),
            'phone'      => $request->input('phone'),
            'angkatan'   => $request->input('angkatan'),
            'pekerjaan'  => $request->input('pekerjaan'),
            'wilayah_id' => $request->integer('wilayah_id') ?: null,
            'photo_url'  => $photoUrl,
        ]);

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
            'name','email','phone','angkatan','pekerjaan','wilayah_id','photo_url','status',
        ]);

        $changes = [];
        foreach ($after as $k => $v) {
            $old = $before[$k] ?? null;
            if ($old != $v) $changes[$k] = ['before'=>$old,'after'=>$v];
        }

        if ($changes) {
            AuditLog::create([
                'actor_id'    => $user->id,
                'action'      => 'profile.update',
                'entity_type' => 'user',
                'entity_id'   => $user->id,
                'payload_json'=> json_encode($changes),
                'created_at'  => now(),
            ]);
        }

        return back()->with('success', $criticalChanged
            ? 'Profil disimpan. Perubahan wilayah/angkatan memerlukan verifikasi. Status akun: pending.'
            : 'Profil disimpan.');
    }
}
