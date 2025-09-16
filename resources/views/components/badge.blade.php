@props(['color' => 'blue'])
@php
  $map = [
    'green' => 'bg-green-100 text-green-800',
    'yellow'=> 'bg-yellow-100 text-yellow-800',
    'red'   => 'bg-red-100 text-red-800',
    'blue'  => 'bg-blue-100 text-blue-800',
  ];
@endphp
<span {{ $attributes->merge(['class' => 'rf-badge '.$map[$color]]) }}>
  {{ $slot }}
</span>
