@props(['cover' => [], 'label' => null, 'image' => null])

@php
    $from = $cover['from'] ?? '#22D97F';
    $to = $cover['to'] ?? '#0C0F0D';
    $accent = $cover['accent'] ?? '#C9FF6B';
    $pattern = $cover['pattern'] ?? 'pulse';
@endphp

<div
    {{ $attributes->merge(['class' => 'relative isolate overflow-hidden']) }}
    style="background-color: {{ $from }}; background-image: radial-gradient(circle at 18% 12%, {{ $accent }}5c, transparent 42%), radial-gradient(circle at 82% 88%, {{ $from }}8c, transparent 58%), linear-gradient(145deg, {{ $from }}, {{ $to }});"
>
    @if ($image !== null)
        <img src="{{ asset($image) }}" alt="{{ $label ?? '' }}" class="absolute inset-0 h-full w-full object-cover">
    @else
        <svg class="absolute inset-0 h-full w-full" viewBox="0 0 200 200" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
            @switch($pattern)
            @case('aurora')
                <circle cx="34" cy="26" r="82" fill="{{ $accent }}" opacity=".26" />
                <circle cx="178" cy="168" r="92" fill="{{ $from }}" opacity=".5" />
                <ellipse cx="118" cy="44" rx="112" ry="28" fill="#ffffff" opacity=".08" transform="rotate(-18 118 44)" />
                <path d="M-20 152C38 98 72 172 122 142s70-38 108-12" fill="none" stroke="{{ $accent }}" stroke-width="1.6" opacity=".7" />
                <path d="M-20 168C38 114 72 188 122 158s70-38 108-12" fill="none" stroke="#ffffff" stroke-width="1" opacity=".2" />
                @break

            @case('orbit')
                <circle cx="100" cy="100" r="62" fill="none" stroke="{{ $accent }}" stroke-width="1.2" opacity=".55" />
                <circle cx="100" cy="100" r="86" fill="none" stroke="#ffffff" stroke-width="1" opacity=".2" />
                <ellipse cx="100" cy="100" rx="94" ry="32" fill="none" stroke="{{ $accent }}" stroke-width="1.4" opacity=".8" transform="rotate(-24 100 100)" />
                <ellipse cx="100" cy="100" rx="72" ry="22" fill="none" stroke="#ffffff" stroke-width="1" opacity=".22" transform="rotate(32 100 100)" />
                <circle cx="100" cy="100" r="22" fill="{{ $accent }}" opacity=".55" />
                <circle cx="176" cy="66" r="7" fill="{{ $accent }}" />
                <circle cx="29" cy="137" r="3.5" fill="#ffffff" opacity=".8" />
                @break

            @case('waves')
                <path d="M-20 118C30 66 70 170 122 118s74-48 118-4v106H-20Z" fill="{{ $accent }}" opacity=".16" />
                <path d="M-20 138C30 86 72 182 124 134s72-44 116-2" fill="none" stroke="{{ $accent }}" stroke-width="2" opacity=".85" />
                <path d="M-20 156C32 106 70 196 126 152s70-42 114 0" fill="none" stroke="#ffffff" stroke-width="1.2" opacity=".3" />
                <path d="M-20 174C30 130 76 208 128 172s68-38 112-2" fill="none" stroke="{{ $accent }}" stroke-width="1" opacity=".45" />
                <circle cx="148" cy="52" r="28" fill="{{ $accent }}" opacity=".22" />
                @break

            @case('grid')
                <g stroke="{{ $accent }}" stroke-width="1" opacity=".55">
                    <path d="M100 42 18 200M100 42l82 158M100 42 54 200M100 42l46 158M100 42 0 135M100 42l200 93" />
                    <path d="M-20 58h240M-20 86h240M-20 118h240M-20 154h240M-20 194h240" opacity=".5" />
                </g>
                <circle cx="100" cy="52" r="54" fill="{{ $accent }}" opacity=".12" />
                <circle cx="100" cy="52" r="27" fill="none" stroke="#ffffff" stroke-width="1.2" opacity=".35" />
                <rect x="82" y="34" width="36" height="36" fill="{{ $accent }}" opacity=".28" transform="rotate(45 100 52)" />
                @break

            @case('sun')
                <circle cx="100" cy="116" r="86" fill="{{ $accent }}" opacity=".12" />
                <circle cx="100" cy="116" r="58" fill="{{ $accent }}" opacity=".92" />
                <circle cx="100" cy="116" r="42" fill="#ffffff" opacity=".18" />
                <g stroke="{{ $to }}" stroke-width="5" opacity=".72">
                    <path d="M22 104h156M26 118h148M34 132h132M46 146h108M60 160h80" />
                </g>
                <path d="M-20 42C30 20 68 64 108 38s72-8 112 14" fill="none" stroke="#ffffff" stroke-width="1" opacity=".3" />
                @break

            @case('prism')
                <polygon points="30,172 100,26 170,172" fill="#ffffff" opacity=".07" />
                <polygon points="56,172 100,66 144,172" fill="{{ $accent }}" opacity=".26" />
                <path d="M62 40 110 172M100 26l34 146" stroke="{{ $accent }}" stroke-width="1.2" opacity=".75" />
                <path d="M24 148 170 92" stroke="#ffffff" stroke-width="1" opacity=".28" />
                <circle cx="146" cy="52" r="4" fill="#ffffff" opacity=".85" />
                <circle cx="56" cy="72" r="2.5" fill="{{ $accent }}" />
                @break

            @case('forest')
                <g fill="none" stroke="{{ $accent }}" stroke-width="1.4" opacity=".55">
                    <path d="M52 200c6-42-8-68 2-104 4-16 1-34 5-50M101 200c-4-52 8-74 1-116-3-18 2-36-2-54M149 200c7-40-6-66 3-98 4-14 0-30 4-46" />
                </g>
                <g fill="{{ $accent }}" opacity=".25">
                    <ellipse cx="54" cy="66" rx="18" ry="34" transform="rotate(-28 54 66)" />
                    <ellipse cx="109" cy="54" rx="16" ry="30" transform="rotate(18 109 54)" />
                    <ellipse cx="151" cy="82" rx="15" ry="28" transform="rotate(32 151 82)" />
                    <ellipse cx="78" cy="120" rx="22" ry="12" transform="rotate(-36 78 120)" />
                    <ellipse cx="132" cy="138" rx="20" ry="10" transform="rotate(24 132 138)" />
                </g>
                <circle cx="100" cy="156" r="32" fill="{{ $accent }}" opacity=".12" />
                @break

            @case('dusk')
                <circle cx="100" cy="126" r="88" fill="{{ $accent }}" opacity=".12" />
                <circle cx="100" cy="126" r="50" fill="{{ $accent }}" opacity=".72" />
                <g stroke="{{ $to }}" stroke-width="4" opacity=".5">
                    <path d="M30 124h140M36 138h128M48 152h104M62 166h76" />
                </g>
                <circle cx="152" cy="48" r="2" fill="#ffffff" opacity=".85" />
                <circle cx="44" cy="70" r="1.6" fill="#ffffff" opacity=".6" />
                <circle cx="64" cy="34" r="1.2" fill="#ffffff" opacity=".5" />
                @break

            @case('bloom')
                <g fill="{{ $accent }}" opacity=".3">
                    <ellipse cx="100" cy="56" rx="17" ry="44" />
                    <ellipse cx="100" cy="56" rx="17" ry="44" transform="rotate(45 100 100)" />
                    <ellipse cx="100" cy="56" rx="17" ry="44" transform="rotate(90 100 100)" />
                    <ellipse cx="100" cy="56" rx="17" ry="44" transform="rotate(135 100 100)" />
                </g>
                <circle cx="100" cy="100" r="62" fill="none" stroke="{{ $accent }}" stroke-width="1" opacity=".35" />
                <circle cx="100" cy="100" r="15" fill="{{ $accent }}" opacity=".9" />
                <circle cx="100" cy="100" r="28" fill="none" stroke="#ffffff" stroke-width="1" opacity=".25" />
                @break

            @case('bars')
                <g fill="{{ $accent }}" opacity=".82">
                    <rect x="28" y="116" width="10" height="56" rx="5" />
                    <rect x="46" y="88" width="10" height="84" rx="5" />
                    <rect x="64" y="128" width="10" height="44" rx="5" />
                    <rect x="82" y="62" width="10" height="110" rx="5" />
                    <rect x="100" y="96" width="10" height="76" rx="5" />
                    <rect x="118" y="46" width="10" height="126" rx="5" />
                    <rect x="136" y="82" width="10" height="90" rx="5" />
                    <rect x="154" y="110" width="10" height="62" rx="5" />
                </g>
                <path d="M20 182h160" stroke="#ffffff" stroke-width="1" opacity=".25" />
                <circle cx="162" cy="38" r="16" fill="{{ $accent }}" opacity=".18" />
                @break

            @default
                <g fill="{{ $accent }}" opacity=".82">
                    <rect x="24" y="118" width="12" height="54" rx="6" />
                    <rect x="44" y="92" width="12" height="80" rx="6" />
                    <rect x="64" y="128" width="12" height="44" rx="6" />
                    <rect x="84" y="70" width="12" height="102" rx="6" />
                    <rect x="104" y="104" width="12" height="68" rx="6" />
                    <rect x="124" y="48" width="12" height="124" rx="6" />
                    <rect x="144" y="86" width="12" height="86" rx="6" />
                    <rect x="164" y="112" width="12" height="60" rx="6" />
                </g>
                <path d="M18 42h164" stroke="#ffffff" stroke-width="1" opacity=".2" />
            @endswitch
        </svg>
    @endif

    <div class="cover-grain absolute inset-0"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-black/35 via-transparent to-white/10"></div>

    @if ($label)
        <span class="absolute left-3 top-3 rounded-full border border-white/15 bg-black/20 px-2 py-0.5 font-mono text-[9px] uppercase tracking-[0.24em] text-white/70 backdrop-blur">
            {{ $label }}
        </span>
    @endif
</div>
