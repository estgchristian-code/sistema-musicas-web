@props(['name' => 'music', 'size' => 20, 'filled' => false])

<svg
    {{ $attributes->merge([
        'width' => $size,
        'height' => $size,
        'viewBox' => '0 0 24 24',
        'fill' => 'none',
        'stroke' => 'currentColor',
        'stroke-width' => '1.8',
        'stroke-linecap' => 'round',
        'stroke-linejoin' => 'round',
        'aria-hidden' => 'true',
        'focusable' => 'false',
    ]) }}
>
    @switch($name)
        @case('home')
            <path d="M4 10.4 12 4l8 6.4V20a1 1 0 0 1-1 1h-4.5v-6.2h-5V21H5a1 1 0 0 1-1-1z" />
            @break

        @case('compass')
            <circle cx="12" cy="12" r="9" />
            <path d="m15.4 8.6-2.1 4.7-4.7 2.1 2.1-4.7z" />
            @break

        @case('library')
            <path d="M4 4.5h3v15H4zM9.5 4.5h3v15h-3zM15 5.2l2.9-.8 4 14.5-2.9.8z" />
            @break

        @case('heart')
            <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 1 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8z" fill="{{ $filled ? 'currentColor' : 'none' }}" />
            @break

        @case('play')
            <path d="M8 5.2v13.6L19 12z" fill="currentColor" stroke="none" />
            @break

        @case('pause')
            <path d="M7.5 5h3v14h-3zM13.5 5h3v14h-3z" fill="currentColor" stroke="none" />
            @break

        @case('skip-back')
            <path d="M6 5v14" />
            <path d="M18.5 5.2 8.8 12l9.7 6.8z" fill="currentColor" stroke="none" />
            @break

        @case('skip-forward')
            <path d="M18 5v14" />
            <path d="M5.5 5.2 15.2 12l-9.7 6.8z" fill="currentColor" stroke="none" />
            @break

        @case('search')
            <circle cx="10.8" cy="10.8" r="6.8" />
            <path d="m16 16 4.8 4.8" />
            @break

        @case('x')
            <path d="m6 6 12 12M18 6 6 18" />
            @break

        @case('menu')
            <path d="M4 7h16M4 12h16M4 17h16" />
            @break

        @case('chevron-left')
            <path d="m15 5-7 7 7 7" />
            @break

        @case('chevron-right')
            <path d="m9 5 7 7-7 7" />
            @break

        @case('chevron-down')
            <path d="m5 9 7 7 7-7" />
            @break

        @case('chevron-up')
            <path d="m5 15 7-7 7 7" />
            @break

        @case('volume')
            <path d="M4 10v4h4l5 4V6l-5 4z" />
            <path d="M16 9a4 4 0 0 1 0 6M18.5 6.5a8 8 0 0 1 0 11" />
            @break

        @case('volume-x')
            <path d="M4 10v4h4l5 4V6l-5 4z" />
            <path d="m16 10 4 4M20 10l-4 4" />
            @break

        @case('music')
            <path d="M9 18V5l10-2v13" />
            <circle cx="6" cy="18" r="3" />
            <circle cx="16" cy="16" r="3" />
            @break

        @case('clock')
            <circle cx="12" cy="12" r="8.5" />
            <path d="M12 7.5V12l3 2" />
            @break

        @case('sparkles')
            <path d="m12 3 1.4 4.1L17.5 8.5l-4.1 1.4L12 14l-1.4-4.1-4.1-1.4 4.1-1.4z" />
            <path d="m18.5 14 .9 2.6 2.6.9-2.6.9-.9 2.6-.9-2.6-2.6-.9 2.6-.9z" />
            @break

        @case('expand')
            <path d="M8 3H3v5M16 3h5v5M21 16v5h-5M3 16v5h5" />
            @break

        @case('list')
            <path d="M9 6h11M9 12h11M9 18h11" />
            <path d="M4.5 6h.01M4.5 12h.01M4.5 18h.01" stroke-width="2.5" />
            @break

        @case('arrow-right')
            <path d="M4 12h16M14 6l6 6-6 6" />
            @break

        @case('disc')
            <circle cx="12" cy="12" r="9" />
            <circle cx="12" cy="12" r="2.6" />
            @break

        @case('mic')
            <rect x="9" y="3" width="6" height="11" rx="3" />
            <path d="M5.5 11a6.5 6.5 0 0 0 13 0M12 17.5V21M9 21h6" />
            @break

        @case('check')
            <path d="m5 12 4.2 4.2L19 6.5" />
            @break

        @case('shuffle')
            <path d="M16 3h5v5M21 3l-6.5 6.5M4 4l5 5M16 21h5v-5M21 21l-6.5-6.5M4 20l5-5" />
            @break

        @case('repeat')
            <path d="m17 2 4 4-4 4M3 11V9a3 3 0 0 1 3-3h15M7 22l-4-4 4-4M21 13v2a3 3 0 0 1-3 3H3" />
            @break

        @case('repeat-1')
            <path d="m17 2 4 4-4 4M3 11V9a3 3 0 0 1 3-3h15M7 22l-4-4 4-4M21 13v2a3 3 0 0 1-3 3H3" />
            <path d="M11.5 10.5 12.8 10v4.5" />
            @break

        @case('headphones')
            <path d="M4 14v-2a8 8 0 0 1 16 0v2" />
            <path d="M4 14h2.5v4H5a1 1 0 0 1-1-1zM20 14h-2.5v4H19a1 1 0 0 0 1-1z" />
            @break

        @default
            <path d="M9 18V5l10-2v13" />
            <circle cx="6" cy="18" r="3" />
            <circle cx="16" cy="16" r="3" />
    @endswitch
</svg>
