<?php

namespace App\Http\Controllers;

use App\Models\MemberReflection;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberReflectionController extends Controller
{
    public function index(Request $request)
    {
        $user = $this->member($request);
        $reflections = MemberReflection::query()->where('user_id', $user->id)
            ->orderByDesc('created_at')->orderByDesc('id')->paginate(12);

        return view('reflections.index', compact('reflections'));
    }

    public function create(Request $request)
    {
        $this->member($request);

        return view('reflections.form', ['reflection' => null]);
    }

    public function store(Request $request)
    {
        $user = $this->member($request);
        $data = $request->validate($this->contentRules());
        $reflection = MemberReflection::create(['user_id' => $user->id] + $data);

        return redirect()->route('reflections.show', $reflection)->with('success', 'Draf refleksi tersimpan.');
    }

    public function show(Request $request, MemberReflection $reflection)
    {
        $reflection = $this->owned($request, $reflection->id)
            ->load(['wilayah:id,name', 'reviewer:id,name']);

        return view('reflections.show', ['reflection' => $reflection, 'reviewMode' => false]);
    }

    public function edit(Request $request, MemberReflection $reflection)
    {
        $reflection = $this->ownedDraft($request, $reflection->id);

        return view('reflections.form', compact('reflection'));
    }

    public function update(Request $request, MemberReflection $reflection)
    {
        DB::transaction(function () use ($request, $reflection) {
            $draft = $this->ownedDraft($request, $reflection->id, true);
            $draft->update($request->validate($this->contentRules()));
        });

        return redirect()->route('reflections.show', $reflection)->with('success', 'Draf refleksi diperbarui.');
    }

    public function destroy(Request $request, MemberReflection $reflection)
    {
        DB::transaction(function () use ($request, $reflection) {
            $this->ownedDraft($request, $reflection->id, true)->delete();
        });

        return redirect()->route('reflections.index')->with('success', 'Draf refleksi dihapus.');
    }

    public function submit(Request $request, MemberReflection $reflection)
    {
        DB::transaction(function () use ($request, $reflection) {
            $draft = $this->ownedDraft($request, $reflection->id, true);
            $user = User::query()->lockForUpdate()->findOrFail($request->user()->id);
            abort_unless($user->status === User::STATUS_ACTIVE && $user->wilayah_id, 403,
                'Akun aktif dan wilayah diperlukan untuk mengirim refleksi.');
            $draft->update([
                'status' => MemberReflection::STATUS_SUBMITTED,
                'wilayah_id' => $user->wilayah_id,
                'submitted_at' => now(),
            ]);
        });

        return redirect()->route('reflections.show', $reflection)->with('success', 'Refleksi dikirim untuk mendapat masukan.');
    }

    public function reviewIndex(Request $request)
    {
        $reflections = $this->reviewable($request)
            ->with(['user:id,name', 'wilayah:id,name'])
            ->orderByDesc('submitted_at')->orderByDesc('id')->paginate(12);

        return view('reflections.review', compact('reflections'));
    }

    public function reviewShow(Request $request, MemberReflection $reflection)
    {
        $reflection = $this->reviewable($request)
            ->with(['user:id,name', 'wilayah:id,name', 'reviewer:id,name'])
            ->findOrFail($reflection->id);

        return view('reflections.show', ['reflection' => $reflection, 'reviewMode' => true]);
    }

    public function reviewUpdate(Request $request, MemberReflection $reflection)
    {
        DB::transaction(function () use ($request, $reflection) {
            $record = $this->reviewable($request)->lockForUpdate()->findOrFail($reflection->id);
            abort_unless($record->status === MemberReflection::STATUS_SUBMITTED, 409,
                'Refleksi ini sudah ditinjau.');
            $data = $request->validate(['feedback' => ['required', 'string', 'max:5000']]);
            $record->update([
                'status' => MemberReflection::STATUS_REVIEWED,
                'feedback' => $data['feedback'],
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);
        });

        return redirect()->route('reflections.review.show', $reflection)->with('success', 'Masukan tersimpan.');
    }

    private function member(Request $request): User
    {
        $user = $request->user();
        abort_unless($user->hasAnyRole(['alumni', 'admin', 'super_admin', 'koorda']), 403);

        return $user;
    }

    private function owned(Request $request, int $id, bool $lock = false): MemberReflection
    {
        $user = $this->member($request);

        return MemberReflection::query()->where('user_id', $user->id)
            ->when($lock, fn ($query) => $query->lockForUpdate())->findOrFail($id);
    }

    private function ownedDraft(Request $request, int $id, bool $lock = false): MemberReflection
    {
        $reflection = $this->owned($request, $id, $lock);
        abort_unless($reflection->status === MemberReflection::STATUS_DRAFT, 409,
            'Refleksi yang sudah dikirim tidak dapat diubah.');

        return $reflection;
    }

    private function reviewable(Request $request): Builder
    {
        $user = $request->user();
        abort_unless($user->can('review-report'), 403);

        return MemberReflection::query()
            ->whereIn('status', [MemberReflection::STATUS_SUBMITTED, MemberReflection::STATUS_REVIEWED])
            ->where('user_id', '!=', $user->id)
            ->when(! $user->hasAnyRole(['admin', 'super_admin']), function ($query) use ($user) {
                abort_unless($user->wilayah_id, 403, 'Wilayah belum ditetapkan pada akun.');

                $query->where('wilayah_id', $user->wilayah_id);
            });
    }

    private function contentRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'context' => ['required', 'in:materi,kajian,lainnya'],
            'activity_name' => ['nullable', 'string', 'max:180'],
            'body' => ['required', 'string', 'max:20000'],
        ];
    }
}
