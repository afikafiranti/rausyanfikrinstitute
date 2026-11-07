@if ($errors->any())
    <div role="alert" class="mt-4 rounded-lg border border-red-300 bg-red-50 p-3 text-sm text-red-800">
        <p>Periksa isian berikut:</p>
        <ul class="mt-2 list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
