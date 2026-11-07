<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AlumniIndexRequest;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\Wilayah;
use App\Models\Level;
use App\Models\Role;
use App\Support\AccountIntegrity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class AlumniController extends Controller
{
    public function index(AlumniIndexRequest $request)
    {
        $me = $request->user();
        $canViewAll = $me->hasAnyRole(['super_admin', 'admin']);
        $canReview = $me->can('review-user');
        $filters = array_intersect_key($request->validated(), array_flip(['q', 'sort', 'dir', 'per_page']));
        $q = trim($filters['q'] ?? '');
        $sort = $filters['sort'] ?? 'name';
        $dir = $filters['dir'] ?? 'asc';
        $filters = array_filter(array_merge($filters, compact('q', 'sort', 'dir')), fn ($value) => $value !== null && $value !== '');
        $alumni = User::alumniListing($me, $filters)
            ->with(['wilayah:id,name', 'level:id,name,description', 'roles' => fn ($query) => $query->select('roles.id', 'roles.name')->orderBy('roles.name')])
            ->paginate((int) ($filters['per_page'] ?? 15))->appends($filters);
        if ($alumni->currentPage() > $alumni->lastPage()) {
            return redirect()->route('alumni.index', $filters + ($alumni->lastPage() > 1 ? ['page' => $alumni->lastPage()] : []));
        }
        $reviewableIds = $canReview
            ? User::reviewableBy($me)->whereIn('users.id', $alumni->modelKeys())->pluck('users.id')->all()
            : [];
        $wilayah = Wilayah::select('id', 'name')->when(! $canViewAll, fn ($query) => $query->whereKey($me->wilayah_id))
            ->orderBy('name')->get();
        $levels = Level::where('is_active', true)->orderBy('id')->get(['id', 'name', 'description']);

        return response()->view('alumni.index', compact('alumni', 'wilayah', 'levels', 'q', 'sort', 'dir',
            'filters', 'canViewAll', 'canReview', 'reviewableIds'))
            ->header('Cache-Control', 'private, no-store');
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

        $message = "Alumni {$user->name} berhasil ditambahkan.";
        return $request->expectsJson() ? response()->json(compact('message'))->header('Cache-Control', 'private, no-store')
            : redirect()->route('alumni.index')->with('success', $message);
    }

    public function show(AlumniIndexRequest $request, $id)
    {
        $me = request()->user();
        $canViewAll = $me->hasAnyRole(['super_admin', 'admin']);
        abort_if(!$canViewAll && !$me->wilayah_id, 403, 'Wilayah belum ditetapkan pada akun.');

        $alumni = User::with([
            'wilayah:id,name',
            'level:id,name,description',
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

        $canVerify = request()->user()->can('review-user') && User::reviewableBy($me)->whereKey($alumni->id)->exists();

        $returnFilters = array_intersect_key($request->validated(), array_flip(['q', 'sort', 'dir', 'per_page', 'page']));
        $returnTo = route('alumni.index', array_merge(['sort' => 'name', 'dir' => 'asc'], $returnFilters), false);

        $modal = $request->validated('modal');
        if ($modal === 'delete') {
            abort_unless($canEditProfile && !$alumni->is($me), 403);
            return response()->view('alumni.partials.delete', compact('alumni'))
                ->header('Cache-Control', 'private, no-store')->header('X-Alumni-Modal', '1');
        }
        if ($modal === 'verification') {
            abort_unless($canVerify, 403);
            return response()->view('verification.forms', ['target' => $alumni, 'levels' => $levels, 'returnTo' => $returnTo])
                ->header('Cache-Control', 'private, no-store')->header('X-Alumni-Modal', '1');
        }
        if (in_array($modal, ['1', 'detail'], true)) {
            return response()->view('alumni.partials.detail', compact('alumni', 'levels', 'canUpdateLevel', 'canEditProfile', 'canVerify', 'returnTo'))
                ->header('Cache-Control', 'private, no-store')->header('X-Alumni-Modal', '1');
        }

        return response()->view('alumni.show', compact('alumni', 'levels', 'canUpdateLevel', 'canEditProfile', 'canVerify', 'returnTo'))
            ->header('Cache-Control', 'private, no-store');
    }

    public function edit(Request $request, User $user)
    {
        if (!$this->canManageAlumniProfile($request->user(), $user)) {
            return $this->failed($request, 'edit', 'Hanya super admin dan admin yang dapat mengedit data alumni atau koorda.');
        }

        $alumni = $user->load(['wilayah:id,name', 'level:id,name,description', 'roles:id,name']);
        $wilayah = Wilayah::select('id', 'name')->orderBy('name')->get();

        $modal = $request->query('modal') === '1';
        $response = response()->view($modal ? 'alumni.partials.edit' : 'alumni.edit', compact('alumni', 'wilayah'))
            ->header('Cache-Control', 'private, no-store');
        return $modal ? $response->header('X-Alumni-Modal', '1') : $response;
    }

    public function update(Request $request, User $user)
    {
        $actor = $request->user();

        if (!$this->canManageAlumniProfile($actor, $user)) {
            return $this->failed($request, 'edit', 'Hanya super admin dan admin yang dapat mengedit data alumni atau koorda.');
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
            $user = AccountIntegrity::lockUser($user);
            if (!$this->canManageAlumniProfile($actor, $user)) {
                throw ValidationException::withMessages(['edit' => 'Hanya super admin dan admin yang dapat mengedit data alumni atau koorda.']);
            }
            $before = $user->only($fields);
            $user->fill($validated);
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
            $criticalChanged = $user->isDirty('wilayah_id') || $user->isDirty('angkatan');
            if ($criticalChanged && $user->status !== User::STATUS_SUSPENDED) {
                if ($reason = AccountIntegrity::deactivationError($user)) {
                    throw ValidationException::withMessages(['status' => $reason]);
                }
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

            $needsVerification = $criticalChanged && $user->status === User::STATUS_PENDING;
            if ($needsVerification) {
                $user->notify(new \App\Notifications\AccountStatusNotification('pending', ['by' => $actor->name]));
            }

            return $needsVerification;
        });

        $message = $criticalChanged
            ? "Data alumni {$user->name} diperbarui. Perubahan wilayah/angkatan menunggu verifikasi."
            : "Data alumni {$user->name} berhasil diperbarui.";
        return $request->expectsJson() ? response()->json(compact('message'))->header('Cache-Control', 'private, no-store')
            : redirect()->route('alumni.show', $user)->with('success', $message);
    }

    public function updateLevel(Request $request, User $user)
    {
        $actor = $request->user();

        if (!$actor->hasAnyRole(['super_admin', 'admin'])) {
            return $this->failed($request, 'level', 'Hanya super admin dan admin yang dapat mengubah level materi.');
        }

        if ($user->hasAnyRole(['super_admin', 'admin']) || !$user->hasAnyRole(['alumni', 'koorda'])) {
            return $this->failed($request, 'level', 'Level materi hanya dapat diubah untuk akun alumni dan koorda.');
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

        $message = "Level materi {$user->name} berhasil diperbarui.";
        return $request->expectsJson() ? response()->json(compact('message'))->header('Cache-Control', 'private, no-store')
            : redirect()->route('alumni.show', $user)->with('success', $message);
    }

    public function destroy(Request $request, User $user)
    {
        $actor = $request->user();

        if (!$actor->hasAnyRole(['super_admin', 'admin'])) {
            return $this->failed($request, 'delete', 'Hanya super admin dan admin yang dapat menghapus akun alumni atau koorda.');
        }

        if ($user->is($request->user())) {
            return $this->failed($request, 'delete', 'Tidak boleh menghapus akun yang sedang digunakan.');
        }

        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return $this->failed($request, 'delete', 'Akun super admin dan admin tidak dapat dihapus dari daftar alumni.');
        }

        if (!$user->hasAnyRole(['alumni', 'koorda'])) {
            return $this->failed($request, 'delete', 'Hanya akun alumni dan koorda yang dapat dihapus dari halaman ini.');
        }

        [$name, $avatarUrl] = DB::transaction(function () use ($user, $actor) {
            $user = AccountIntegrity::lockUser($user);
            if (!$this->canManageAlumniProfile($actor, $user)) {
                throw ValidationException::withMessages(['delete' => 'Hanya akun alumni dan koorda yang dapat dihapus dari halaman ini.']);
            }
            if ($reason = AccountIntegrity::deletionError($user)) {
                throw ValidationException::withMessages(['delete' => $reason]);
            }

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
            return [$user->name, $user->photo_url];
        });

        AccountIntegrity::removeOwnedAvatar($avatarUrl);

        $message = "Data alumni {$name} berhasil dihapus.";
        return $request->expectsJson() ? response()->json(compact('message'))->header('Cache-Control', 'private, no-store')
            : redirect()->route('alumni.index')->with('success', $message);
    }

    private function failed(Request $request, string $key, string $message)
    {
        return $request->expectsJson() ? response()->json(compact('message'), 403)->header('Cache-Control', 'private, no-store')
            : back()->withErrors([$key => $message]);
    }

    private function canManageAlumniProfile(User $actor, User $target): bool
    {
        return $actor->hasAnyRole(['super_admin', 'admin'])
            && $target->hasAnyRole(['alumni', 'koorda'])
            && !$target->hasAnyRole(['super_admin', 'admin']);
    }
}
