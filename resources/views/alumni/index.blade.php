@extends('layouts.app')

@section('page-content')
    @php
        $statusLabels = ['pending' => 'Menunggu', 'active' => 'Aktif', 'suspended' => 'Ditangguhkan'];
    @endphp
    <section data-alumni-workspace class="rf-card min-w-0 sm:p-6" aria-labelledby="alumni-heading">
        <header class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-200 pb-5">
            <div><h1 id="alumni-heading" class="text-2xl font-semibold text-ink">Alumni</h1><p class="mt-2 text-sm text-muted">Kelola data akun dan tinjau pendaftaran alumni.</p></div>
            <div class="flex flex-wrap gap-2">
                <a href="#alumni-create-fallback" id="open-alumni-modal" data-alumni-modal="create" aria-controls="alumni-dialog" aria-expanded="false" class="rf-btn">Buat</a>
                <a href="{{ route('alumni.csv.index', $filters) }}" class="rf-btn-secondary">Impor</a>
                <a href="{{ route('alumni.excel.export', $filters) }}" class="rf-btn-secondary" title="Unduh Excel sesuai daftar terpilih">Ekspor</a>
            </div>
        </header>
        <noscript>
            @if (session('success'))<p role="status" class="mt-4 rounded-lg bg-green-50 p-3 text-sm text-green-900">{{ session('success') }}</p>@endif
            @if ($errors->any())<div role="alert" class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-800"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        </noscript>
        <form action="{{ route('alumni.index') }}" method="GET" class="mt-5 flex flex-wrap items-end gap-2">
            <input type="hidden" name="sort" value="{{ $sort }}"><input type="hidden" name="dir" value="{{ $dir }}">
            <div class="min-w-0 flex-1"><label for="alumni-search" class="block text-sm font-medium">Cari nama</label><input id="alumni-search" name="q" type="search" value="{{ $q }}" maxlength="100" placeholder="Nama alumni" class="mt-1 w-full"></div>
            <button type="submit" class="rf-btn">Cari</button>
            @if ($q !== '')<a href="{{ route('alumni.index', array_diff_key($filters, ['q' => true])) }}" class="rf-btn-secondary">Reset</a>@endif
        </form>
        <div data-alumni-results>
            <div data-alumni-workspace-status role="status" aria-live="polite" tabindex="-1" hidden class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm"></div>
            <div role="region" aria-label="Daftar alumni, geser untuk melihat semua kolom" tabindex="0" class="mt-5 overflow-x-auto rounded-lg border border-slate-200">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <caption class="sr-only">Daftar akun alumni. Klik header untuk mengurutkan data.</caption>
                    <thead class="bg-slate-50 text-muted"><tr>
                        @foreach (['name' => 'Nama', 'angkatan' => 'Angkatan', 'wilayah' => 'Wilayah', 'role' => 'Peran', 'level' => 'Level', 'status' => 'Status'] as $key => $label)
                            <th scope="col" aria-sort="{{ $sort === $key ? ($dir === 'asc' ? 'ascending' : 'descending') : 'none' }}" class="px-3 py-2">
                                <a href="{{ route('alumni.index', array_merge($filters, ['sort' => $key, 'dir' => $sort === $key && $dir === 'asc' ? 'desc' : 'asc'])) }}" class="inline-flex min-h-11 items-center gap-2 rounded font-semibold text-ink hover:text-brand" @if ($key === 'status') title="Menunggu, Aktif, Ditangguhkan" @endif aria-label="Urutkan {{ $label }} {{ $sort === $key && $dir === 'asc' ? 'menurun' : 'menaik' }}">{{ $label }}<span aria-hidden="true">@if ($sort === $key) @if ($dir === 'asc') &#8593; @else &#8595; @endif @else &#8597; @endif</span></a>
                            </th>
                        @endforeach
                        <th scope="col" class="px-3 py-2">Aksi</th>
                    </tr></thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($alumni as $item)
                            @php
                                $roleNames = $item->roles->pluck('name');
                                $canManageTarget = ($roleNames->contains('alumni') || $roleNames->contains('koorda')) && ! $roleNames->contains('super_admin') && ! $roleNames->contains('admin');
                            @endphp
                            <tr class="hover:bg-blue-50 focus-within:bg-blue-50">
                                <td class="px-3 py-3 font-medium text-ink">{{ $item->name }}</td>
                                <td class="px-3 py-3">{{ $item->angkatan ?? '-' }}</td>
                                <td class="px-3 py-3">{{ $item->wilayah?->name ?? '-' }}</td>
                                <td class="px-3 py-3"><div class="flex flex-wrap gap-1">@forelse ($item->roles as $role)
                                    @php($roleClass = ['super_admin' => 'bg-rose-200 text-rose-800', 'admin' => 'bg-amber-200 text-amber-800', 'koorda' => 'bg-violet-200 text-violet-800', 'alumni' => 'bg-sky-200 text-sky-800'][$role->name] ?? 'bg-slate-100 text-slate-800')
                                    <span class="rf-badge {{ $roleClass }}">{{ ['super_admin' => 'Super Admin', 'admin' => 'Admin', 'koorda' => 'Koorda', 'alumni' => 'Alumni'][$role->name] ?? $role->name }}</span>
                                @empty<span>-</span>@endforelse</div></td>
                                <td class="px-3 py-3">{{ $item->level?->description ?: ($item->level?->name ?? '-') }}</td>
                                <td class="px-3 py-3"><span class="rf-badge {{ $item->status === 'active' ? 'bg-green-100 text-green-800' : ($item->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-slate-100 text-slate-700') }}">{{ $statusLabels[$item->status] ?? $item->status }}</span></td>
                                <td class="px-3 py-3"><div class="flex items-center gap-2">
                                    @if (in_array($item->id, $reviewableIds, true))
                                        <a href="{{ route('alumni.show', ['id' => $item->id] + $filters + ['page' => $alumni->currentPage()]) }}#verifikasi" data-alumni-modal="verification" class="rf-btn-secondary px-3">Verifikasi</a>
                                    @endif
                                    <a href="{{ route('alumni.show', ['id' => $item->id] + $filters + ['page' => $alumni->currentPage()]) }}" data-alumni-modal="detail" class="rf-btn-secondary p-2" data-action-tooltip="Detail {{ $item->name }}" aria-label="Detail {{ $item->name }}"><i class="fas fa-magnifying-glass" aria-hidden="true"></i></a>
                                    @if ($canViewAll && $canManageTarget)
                                        <a href="{{ route('alumni.edit', $item) }}" data-alumni-modal="edit" class="rf-btn-secondary p-2" data-action-tooltip="Edit {{ $item->name }}" aria-label="Edit {{ $item->name }}"><i class="fas fa-pen" aria-hidden="true"></i></a>
                                    @endif
                                    @if ($canViewAll && ! $item->is(auth()->user()) && $canManageTarget)
                                        <a href="{{ route('alumni.show', ['id' => $item->id, 'action' => 'delete']) }}" data-alumni-modal="delete" class="rf-btn-danger p-2" data-action-tooltip="Hapus {{ $item->name }}" aria-label="Hapus {{ $item->name }}"><i class="fas fa-trash" aria-hidden="true"></i></a>
                                    @endif
                                </div></td>
                            </tr>
                        @empty<tr><td colspan="7" class="px-3 py-8 text-muted">{{ $q !== '' ? 'Tidak ada alumni dengan nama tersebut.' : 'Belum ada data alumni.' }}</td></tr>@endforelse
                    </tbody>
                </table>
            </div>
            <footer class="mt-4 flex flex-wrap items-center justify-between gap-3 text-sm text-muted">
                <p>Menampilkan {{ $alumni->firstItem() ?? 0 }} - {{ $alumni->lastItem() ?? 0 }} dari {{ $alumni->total() }} akun | Halaman {{ $alumni->currentPage() }} dari {{ $alumni->lastPage() }}</p>
                @if ($alumni->hasPages())<nav aria-label="Halaman alumni" class="flex flex-wrap gap-2">
                    @if (! $alumni->onFirstPage())<a href="{{ $alumni->previousPageUrl() }}" rel="prev" class="rf-btn-secondary px-3">Sebelumnya</a>@endif
                    @if ($alumni->hasMorePages())<a href="{{ $alumni->nextPageUrl() }}" rel="next" class="rf-btn-secondary px-3">Berikutnya</a>@endif
                </nav>@endif
            </footer>
        </div>
    </section>
    <section id="alumni-create-fallback" class="rf-card mt-6">@include('alumni.partials.create')</section>
    @include('alumni.dialog')
@endsection
