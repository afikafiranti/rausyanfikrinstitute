<section data-alumni-modal-content class="p-5 sm:p-6">
<h2 id="alumni-modal-heading" tabindex="-1" class="pr-20 text-xl font-semibold">Buat alumni</h2>
<p class="mb-5 mt-2 text-sm text-muted">Akun baru menunggu verifikasi sebelum dapat digunakan.</p>
                <form data-alumni-form method="POST" action="{{ route('alumni.store') }}" class="space-y-3">
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
                        Simpan
                    </button>
                </form>
</section>
