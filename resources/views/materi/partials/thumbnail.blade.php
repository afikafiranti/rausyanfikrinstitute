@php
    $validVideoId = is_string($videoId) && preg_match('/\A[A-Za-z0-9_-]{11}\z/D', $videoId);
    $placeholder = $emptyLabel ?? 'Thumbnail tidak tersedia';
@endphp
<span @unless ($validVideoId) role="img" aria-label="{{ $placeholder }}" @endunless
    class="relative flex {{ ($size ?? 'playlist') === 'video' ? 'h-[54px] w-24' : 'h-[45px] w-20 sm:h-[63px] sm:w-28' }} shrink-0 items-center justify-center overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
    @if ($validVideoId)
        <img src="https://i.ytimg.com/vi/{{ $videoId }}/mqdefault.jpg" alt="" width="{{ ($size ?? 'playlist') === 'video' ? 96 : 112 }}" height="{{ ($size ?? 'playlist') === 'video' ? 54 : 63 }}"
            loading="lazy" decoding="async" referrerpolicy="no-referrer" class="absolute inset-0 h-full w-full object-cover"
            onerror="this.hidden = true; this.nextElementSibling.hidden = false; this.parentElement.setAttribute('role', 'img'); this.parentElement.setAttribute('aria-label', 'Thumbnail tidak tersedia')">
    @endif
    <span @if ($validVideoId) hidden @endif class="px-1 text-center text-[10px] leading-3 text-muted" aria-hidden="true">{{ $validVideoId ? 'Thumbnail tidak tersedia' : $placeholder }}</span>
</span>
