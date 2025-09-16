@props([
  'name',                   // nama ikon: home | list | lines | user | doc (opsional)
  'class' => 'w-5 h-5',     // ukuran default
  'strokeWidth' => 1.5,      // tebal garis
  'ariaLabel' => null,       // untuk aksesibilitas; isi string untuk non-dekoratif
])

@php
  $role = $ariaLabel ? 'img' : 'presentation';
@endphp

<svg xmlns="http://www.w3.org/2000/svg"
     {{ $attributes->merge(['class' => $class]) }}
     viewBox="0 0 24 24" fill="none" stroke="currentColor"
     stroke-linecap="round" stroke-linejoin="round"
     role="{{ $role }}" @unless($ariaLabel) aria-hidden="true" @else aria-label="{{ $ariaLabel }}" @endunless>
  @switch($name)
    @case('home')
      <path stroke-width="{{ $strokeWidth }}" d="M3 10.5 12 3l9 7.5V21H3z"/>
      @break

    @case('list')   {{-- tiga garis: cocok untuk "Materi" --}}
      <path stroke-width="{{ $strokeWidth }}" d="M4 6h16M4 12h16M4 18h10"/>
      @break

    @case('lines')  {{-- tiga paragraf: cocok untuk "Laporan" --}}
      <path stroke-width="{{ $strokeWidth }}" d="M7 7h10M7 12h10M7 17h6"/>
      @break

    @case('user')
      <path stroke-width="{{ $strokeWidth }}" d="M16 7a4 4 0 1 1-8 0M4 20a8 8 0 1 1 16 0"/>
      @break

    @case('doc')    {{-- alternatif ikon dokumen --}}
      <path stroke-width="{{ $strokeWidth }}" d="M7 3h6l5 5v11a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
      <path stroke-width="{{ $strokeWidth }}" d="M13 3v6h6"/>
      @break

    @default        {{-- fallback: tanda plus --}}
      <path stroke-width="{{ $strokeWidth }}" d="M12 5v14M5 12h14"/>
  @endswitch
</svg>
