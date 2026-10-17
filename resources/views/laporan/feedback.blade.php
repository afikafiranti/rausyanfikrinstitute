@if (session('success'))
    <p role="status" class="mt-5 rounded-lg bg-green-50 p-4 text-green-900">{{ session('success') }}</p>
@endif
@if ($errors->any())
    <div role="alert" class="mt-5 rounded-lg bg-red-50 p-4 text-red-900">
        <p class="font-semibold">Periksa kembali isian laporan.</p>
        <ul class="mt-2 list-inside list-disc">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
