@extends('layouts.app')

@section('page-content')
    <div class="mx-auto mt-6 max-w-5xl rounded-xl bg-white p-6 shadow">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-2xl font-semibold text-slate-800">Edit Data Alumni</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $alumni->name }} · {{ $alumni->email }}</p>
            </div>
            <a href="{{ route('alumni.show', $alumni) }}"
                class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <div class="font-semibold">Data alumni belum tersimpan.</div>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('alumni.update', $alumni) }}" class="grid grid-cols-1 gap-4 md:grid-cols-2">
            @csrf
            @method('PATCH')

            <div>
                <label for="name" class="block text-sm text-slate-600">Nama Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name', $alumni->name) }}" required
                    class="mt-1 w-full rounded-lg border-slate-300">
            </div>

            <div>
                <label for="email" class="block text-sm text-slate-600">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $alumni->email) }}" required
                    class="mt-1 w-full rounded-lg border-slate-300">
            </div>

            <div>
                <label for="phone" class="block text-sm text-slate-600">No. WA</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone', $alumni->phone) }}"
                    class="mt-1 w-full rounded-lg border-slate-300">
            </div>

            <div>
                <label for="angkatan" class="block text-sm text-slate-600">Angkatan</label>
                <input id="angkatan" type="text" name="angkatan" value="{{ old('angkatan', $alumni->angkatan) }}"
                    class="mt-1 w-full rounded-lg border-slate-300">
            </div>

            <div>
                <label for="wilayah_id" class="block text-sm text-slate-600">Wilayah</label>
                <select id="wilayah_id" name="wilayah_id" class="mt-1 w-full rounded-lg border-slate-300">
                    <option value="">Pilih wilayah</option>
                    @foreach ($wilayah as $item)
                        <option value="{{ $item->id }}" @selected((int) old('wilayah_id', $alumni->wilayah_id) === $item->id)>
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="pekerjaan" class="block text-sm text-slate-600">Pekerjaan</label>
                <input id="pekerjaan" type="text" name="pekerjaan" value="{{ old('pekerjaan', $alumni->pekerjaan) }}"
                    class="mt-1 w-full rounded-lg border-slate-300">
            </div>

            <div>
                <label for="tempat_lahir" class="block text-sm text-slate-600">Tempat Lahir</label>
                <input id="tempat_lahir" type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $alumni->tempat_lahir) }}"
                    class="mt-1 w-full rounded-lg border-slate-300">
            </div>

            <div>
                <label for="tanggal_lahir" class="block text-sm text-slate-600">Tanggal Lahir</label>
                <input id="tanggal_lahir" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $alumni->tanggal_lahir) }}"
                    class="mt-1 w-full rounded-lg border-slate-300">
            </div>

            <div>
                <label for="pendidikan_terakhir" class="block text-sm text-slate-600">Pendidikan Terakhir</label>
                <input id="pendidikan_terakhir" type="text" name="pendidikan_terakhir"
                    value="{{ old('pendidikan_terakhir', $alumni->pendidikan_terakhir) }}"
                    class="mt-1 w-full rounded-lg border-slate-300">
            </div>

            <div>
                <label for="kampus" class="block text-sm text-slate-600">Kampus</label>
                <input id="kampus" type="text" name="kampus" value="{{ old('kampus', $alumni->kampus) }}"
                    class="mt-1 w-full rounded-lg border-slate-300">
            </div>

            <div>
                <label for="status_pernikahan" class="block text-sm text-slate-600">Status Pernikahan</label>
                <select id="status_pernikahan" name="status_pernikahan" class="mt-1 w-full rounded-lg border-slate-300">
                    <option value="">Pilih status</option>
                    <option value="lajang" @selected(old('status_pernikahan', $alumni->status_pernikahan) === 'lajang')>Lajang</option>
                    <option value="menikah" @selected(old('status_pernikahan', $alumni->status_pernikahan) === 'menikah')>Menikah</option>
                    <option value="duda/janda" @selected(old('status_pernikahan', $alumni->status_pernikahan) === 'duda/janda')>Duda / Janda</option>
                </select>
            </div>

            <div>
                <label for="ab" class="block text-sm text-slate-600">Status AB</label>
                <select id="ab" name="ab" class="mt-1 w-full rounded-lg border-slate-300">
                    <option value="">Pilih status AB</option>
                    <option value="iya" @selected(old('ab', $alumni->ab) === 'iya')>Iya</option>
                    <option value="tidak" @selected(old('ab', $alumni->ab) === 'tidak')>Tidak</option>
                </select>
            </div>

            <div class="flex flex-wrap justify-end gap-2 pt-4 md:col-span-2">
                <a href="{{ route('alumni.show', $alumni) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection
