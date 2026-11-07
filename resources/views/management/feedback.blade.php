@if ($errors->any())
    <div role="alert" class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-900">
        <p class="font-semibold">Periksa kembali isian formulir.</p>
        <ul class="mt-2 list-inside list-disc">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
