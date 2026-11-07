<section data-alumni-modal-content class="p-5 sm:p-6">
    <h2 id="alumni-modal-heading" tabindex="-1" class="pr-20 text-xl font-semibold">Detail alumni</h2>
    @if ($alumni->avatar_url)<img src="{{ $alumni->avatar_url }}" alt="Foto {{ $alumni->name }}" width="64" height="64" class="mt-4 h-16 w-16 rounded-full object-cover">@endif
    <p class="mt-3 break-words text-lg font-semibold">{{ $alumni->name }}</p>
    <p class="mt-1 break-words text-sm text-muted">{{ $alumni->email }}</p>
    <dl class="mt-5 grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
        @foreach ([
            'Status akun' => ['pending' => 'Menunggu', 'active' => 'Aktif', 'suspended' => 'Ditangguhkan'][$alumni->status] ?? $alumni->status,
            'Peran' => $alumni->roles->pluck('name')->map(fn ($name) => ['super_admin' => 'Super Admin', 'admin' => 'Admin', 'koorda' => 'Koorda', 'alumni' => 'Alumni'][$name] ?? $name)->implode(', '),
            'No. WA' => $alumni->phone,
            'Angkatan' => $alumni->angkatan,
            'Wilayah' => $alumni->wilayah?->name,
            'Level materi' => $alumni->level?->description ?: $alumni->level?->name,
            'Pekerjaan' => $alumni->pekerjaan,
            'Pendidikan terakhir' => $alumni->pendidikan_terakhir,
            'Kampus' => $alumni->kampus,
            'Tempat lahir' => $alumni->tempat_lahir,
            'Tanggal lahir' => $alumni->tanggal_lahir,
            'Status pernikahan' => $alumni->status_pernikahan,
            'Status AB' => $alumni->ab,
        ] as $label => $value)
            <div class="min-w-0"><dt class="text-muted">{{ $label }}</dt><dd class="mt-1 break-words font-medium">{{ filled($value) ? $value : '-' }}</dd></div>
        @endforeach
    </dl>
    @if ($canUpdateLevel)
        <form data-alumni-form method="POST" action="{{ route('alumni.level.update', $alumni) }}" class="mt-6 flex flex-wrap items-end gap-2 border-t border-slate-200 pt-5">
            @csrf @method('PATCH')
            <div class="min-w-0 flex-1"><label for="alumni-detail-level" class="block text-sm font-medium">Ubah level materi</label><select id="alumni-detail-level" name="level_id" required class="mt-1 w-full"><option value="">Pilih level</option>@foreach ($levels as $level)<option value="{{ $level->id }}" @selected((int) $alumni->level_id === $level->id)>{{ $level->description ?: $level->name }}</option>@endforeach</select></div>
            <button type="submit" class="rf-btn">Simpan</button>
        </form>
    @endif
    <div class="mt-6 flex flex-wrap gap-2 border-t border-slate-200 pt-5">
        @if ($canEditProfile)<a data-alumni-modal="edit" href="{{ route('alumni.edit', $alumni) }}" class="rf-btn-secondary">Edit</a>@endif
        @if ($canVerify)<a data-alumni-modal="verification" href="{{ route('alumni.show', $alumni) }}#verifikasi" class="rf-btn">Verifikasi</a>@endif
    </div>
</section>
