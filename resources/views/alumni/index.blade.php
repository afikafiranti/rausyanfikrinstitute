@extends('layouts.app')

@section('page-content')
    @php
        $showCreateModal = old('_form') === 'alumni-create' && $errors->any();
        $statusLabels = ['pending' => 'Menunggu', 'active' => 'Aktif', 'suspended' => 'Ditangguhkan'];
    @endphp
    <section data-alumni-workspace class="rf-card min-w-0 sm:p-6" aria-labelledby="alumni-heading">
        <header class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-200 pb-5">
            <div><h1 id="alumni-heading" class="text-2xl font-semibold text-ink">Alumni</h1><p class="mt-2 text-sm text-muted">Kelola data akun dan tinjau pendaftaran alumni.</p></div>
            <div class="flex flex-wrap gap-2">
                <button type="button" id="open-alumni-modal" aria-controls="modal-register-alumni" aria-expanded="{{ $showCreateModal ? 'true' : 'false' }}" class="rf-btn">Buat</button>
                <a href="{{ route('alumni.csv.index', $filters) }}" class="rf-btn-secondary">Impor</a>
                <a href="{{ route('alumni.excel.export', $filters) }}" class="rf-btn-secondary" title="Unduh Excel sesuai daftar terpilih">Ekspor</a>
            </div>
        </header>
        @if (session('success'))<p role="status" class="mt-4 rounded-lg bg-green-50 p-3 text-sm text-green-900">{{ session('success') }}</p>@endif
        @if ($errors->any())<div role="alert" class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-800"><p>Permintaan belum berhasil.</p><ul class="mt-2 list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <form action="{{ route('alumni.index') }}" method="GET" class="mt-5 flex flex-wrap items-end gap-2">
            <input type="hidden" name="view" value="{{ $view }}"><input type="hidden" name="sort" value="{{ $sort }}"><input type="hidden" name="dir" value="{{ $dir }}">
            <div class="min-w-0 flex-1"><label for="alumni-search" class="block text-sm font-medium">Cari nama</label><input id="alumni-search" name="q" type="search" value="{{ $q }}" maxlength="100" placeholder="Nama alumni" class="mt-1 w-full"></div>
            <button type="submit" class="rf-btn">Cari</button>
            @if ($q !== '')<a href="{{ route('alumni.index', array_diff_key($filters, ['q' => true])) }}" class="rf-btn-secondary">Reset</a>@endif
        </form>
        <div data-alumni-results>
            <nav aria-label="Tampilan alumni" class="mt-5 flex flex-wrap gap-2 border-b border-slate-200 pb-4">
                <a href="{{ route('alumni.index', array_merge($filters, ['view' => 'all'])) }}" @if ($view === 'all') aria-current="page" @endif class="rf-btn-secondary {{ $view === 'all' ? 'border-brand bg-blue-50 text-brand' : '' }}">Semua <span class="text-xs">{{ $totalAlumni }}</span></a>
                @if ($canReview)<a href="{{ route('alumni.index', array_merge($filters, ['view' => 'verification'])) }}" @if ($view === 'verification') aria-current="page" @endif class="rf-btn-secondary {{ $view === 'verification' ? 'border-brand bg-blue-50 text-brand' : '' }}">Verifikasi <span class="text-xs">{{ $pendingCount }}</span></a>@endif
            </nav>
            <div data-alumni-workspace-status role="status" aria-live="polite" tabindex="-1" hidden class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm"></div>
            <div role="region" aria-label="Daftar alumni, geser untuk melihat semua kolom" tabindex="0" class="mt-4 overflow-x-auto rounded-lg border border-slate-200">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <caption class="sr-only">Daftar akun alumni. Klik header untuk mengurutkan data.</caption>
                    <thead class="bg-slate-50 text-muted"><tr>
                        @foreach (['name' => 'Nama', 'angkatan' => 'Angkatan', 'wilayah' => 'Wilayah', 'role' => 'Peran', 'level' => 'Level', 'status' => 'Status'] as $key => $label)
                            <th scope="col" aria-sort="{{ $sort === $key ? ($dir === 'asc' ? 'ascending' : 'descending') : 'none' }}" class="px-3 py-2">
                                <a href="{{ route('alumni.index', array_merge($filters, ['sort' => $key, 'dir' => $sort === $key && $dir === 'asc' ? 'desc' : 'asc'])) }}" class="inline-flex min-h-11 items-center gap-2 rounded font-semibold text-ink hover:text-brand" aria-label="Urutkan {{ $label }} {{ $sort === $key && $dir === 'asc' ? 'menurun' : 'menaik' }}">{{ $label }}<span aria-hidden="true">@if ($sort === $key) @if ($dir === 'asc') &#8593; @else &#8595; @endif @else &#8597; @endif</span></a>
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
                                        <a href="{{ route('alumni.show', ['id' => $item->id] + $filters + ['page' => $alumni->currentPage()]) }}#verifikasi" data-verification-user data-name="{{ $item->name }}" data-email="{{ $item->email }}" data-approve-url="{{ route('verification.approve', $item) }}" data-reject-url="{{ route('verification.reject', $item) }}" class="rf-btn-secondary px-3">Verifikasi</a>
                                    @endif
                                    <a href="{{ route('alumni.show', $item) }}" class="rf-btn-secondary p-2" title="Detail {{ $item->name }}" aria-label="Detail {{ $item->name }}"><i class="fas fa-eye" aria-hidden="true"></i></a>
                                    @if ($canViewAll && $canManageTarget)
                                        <a href="{{ route('alumni.edit', $item) }}" class="rf-btn-secondary p-2" title="Edit {{ $item->name }}" aria-label="Edit {{ $item->name }}"><i class="fas fa-pen" aria-hidden="true"></i></a>
                                    @endif
                                    @if ($canViewAll && ! $item->is(auth()->user()) && $canManageTarget)
                                        <form method="POST" action="{{ route('alumni.destroy', $item) }}" onsubmit="return confirm('Hapus akun ini? Tindakan ini tidak dapat dibatalkan.');">@csrf @method('DELETE')<button type="submit" class="rf-btn-danger p-2" title="Hapus {{ $item->name }}" aria-label="Hapus {{ $item->name }}"><i class="fas fa-trash" aria-hidden="true"></i></button></form>
                                    @endif
                                </div></td>
                            </tr>
                        @empty<tr><td colspan="7" class="px-3 py-8 text-muted">{{ $view === 'verification' ? 'Tidak ada akun yang perlu diverifikasi sesuai pencarian.' : ($q !== '' ? 'Tidak ada alumni dengan nama tersebut.' : 'Belum ada data alumni.') }}</td></tr>@endforelse
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
        {{-- Modal --}}
        <div id="modal-register-alumni" role="dialog" aria-modal="true" aria-labelledby="alumni-modal-heading" aria-hidden="{{ $showCreateModal ? 'false' : 'true' }}" tabindex="-1"
            class="{{ $showCreateModal ? '' : 'hidden' }} fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50 p-4">

            <div class="bg-white rounded-xl w-full max-w-md max-h-full overflow-y-auto p-6 shadow-lg relative">
                <button type="button" id="close-alumni-modal" aria-label="Tutup formulir tambah alumni"
                    class="absolute right-3 top-3 text-slate-500 hover:text-slate-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-brand">
                    <i class="fas fa-times text-xl" aria-hidden="true"></i>
                </button>

                <h2 id="alumni-modal-heading" class="text-xl font-semibold mb-4">Tambah Alumni Baru</h2>

                <form method="POST" action="{{ route('alumni.store') }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="_form" value="alumni-create">

                    <div>
                        <label for="new-alumni-name" class="block text-sm mb-1">Nama</label>
                        <input id="new-alumni-name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" required @error('name') aria-invalid="true" @enderror class="w-full rounded-lg border-slate-300">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="new-alumni-email" class="block text-sm mb-1">Email</label>
                        <input id="new-alumni-email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required @error('email') aria-invalid="true" @enderror class="w-full rounded-lg border-slate-300">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="new-alumni-wilayah" class="block text-sm mb-1">Wilayah</label>
                        <select id="new-alumni-wilayah" name="wilayah_id" required @error('wilayah_id') aria-invalid="true" @enderror class="w-full rounded-lg border-slate-300">
                            <option value="">Pilih wilayah</option>
                            @foreach ($wilayah as $region)
                                <option value="{{ $region->id }}" @selected(old('wilayah_id', auth()->user()->wilayah_id) == $region->id)>{{ $region->name }}</option>
                            @endforeach
                        </select>
                        @error('wilayah_id')
                            <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="new-alumni-password" class="block text-sm mb-1">Password</label>
                            <input id="new-alumni-password" type="password" name="password" autocomplete="new-password" required @error('password') aria-invalid="true" @enderror class="w-full rounded-lg border-slate-300">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="new-alumni-password-confirmation" class="block text-sm mb-1">Ulangi Password</label>
                            <input id="new-alumni-password-confirmation" type="password" name="password_confirmation" autocomplete="new-password" required
                                class="w-full rounded-lg border-slate-300">
                        </div>
                    </div>

                    <button class="rf-btn w-full bg-brand hover:bg-brand-dark text-white py-2 rounded-lg">
                        Buat Akun
                    </button>
                </form>
            </div>
        </div>
        @if ($canReview) @include('verification.dialog') @endif
@endsection
