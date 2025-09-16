@props([
  'src' => null,
  'name' => '',
  'class' => 'h-8 w-8',
])

@if($src)
  <img src="{{ $src }}" alt="avatar" class="rounded-full object-cover {{ $class }}">
@else
  @php $initial = mb_strtoupper(mb_substr($name ?: 'U', 0, 1)); @endphp
  <div class="rounded-full bg-white/25 text-white font-semibold flex items-center justify-center {{ $class }}">
    {{ $initial }}
  </div>
@endif
