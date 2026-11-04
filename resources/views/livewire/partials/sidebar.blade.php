@php
    $mobile = $mobile ?? false;
    $keyPrefix = $mobile ? 'mobile' : 'desktop';
    $closeDrawer = $mobile ? 'x-on:click="navOpen = false"' : '';
    $navItems = [
        ['section' => 'home', 'label' => 'Início', 'icon' => 'home'],
        ['section' => 'explore', 'label' => 'Explorar', 'icon' => 'compass'],
        ['section' => 'library', 'label' => 'Biblioteca', 'icon' => 'library'],
        ['section' => 'playlists', 'label' => 'Playlists', 'icon' => 'list'],
    ];
@endphp

<div class="flex h-full flex-col overflow-y-auto px-4 pb-[118px] pt-6">
    <div class="flex items-center justify-between px-2">
        <button wire:click="navigateTo('home')" {!! $closeDrawer !!} class="group flex items-center gap-3" aria-label="Ir para o início">
            <span class="relative flex h-10 w-10 items-center justify-center overflow-hidden rounded-2xl bg-sonic-400 text-ink-950 shadow-glow">
                <span class="absolute inset-0 bg-gradient-to-br from-white/40 via-transparent to-transparent"></span>
                <span class="relative flex items-end gap-[2px]">
                    <span class="h-2 w-[3px] rounded-full bg-ink-950"></span>
                    <span class="h-4 w-[3px] rounded-full bg-ink-950"></span>
                    <span class="h-3 w-[3px] rounded-full bg-ink-950"></span>
                    <span class="h-5 w-[3px] rounded-full bg-ink-950"></span>
                </span>
            </span>
            <span>
                <span class="block font-display text-[21px] font-bold leading-none tracking-[0.16em] text-white">SONIC</span>
                <span class="mt-1 block font-mono text-[8px] uppercase tracking-[0.3em] text-zinc-500">music, refined</span>
            </span>
        </button>

        @if ($mobile)
            <button x-on:click="navOpen = false" class="flex h-9 w-9 items-center justify-center rounded-full text-zinc-400 hover:bg-white/5 hover:text-white" aria-label="Fechar menu">
                <x-icon name="x" :size="18" />
            </button>
        @endif
    </div>

    <nav class="mt-8 space-y-1" aria-label="Navegação principal">
        @foreach ($navItems as $item)
            @php
                $isActive = match ($item['section']) {
                    'playlists' => in_array($section, ['playlists', 'playlist'], true),
                    default => $section === $item['section'],
                };
            @endphp

            <button
                wire:click="navigateTo('{{ $item['section'] }}')"
                {!! $closeDrawer !!}
                class="group flex w-full items-center gap-4 rounded-md px-3 py-2.5 text-left text-sm font-semibold transition {{ $isActive ? 'text-white' : 'text-zinc-400 hover:text-white' }}"
                @if ($isActive) aria-current="page" @endif
            >
                <x-icon :name="$item['icon']" :size="20" class="{{ $isActive ? 'text-sonic-400' : 'text-zinc-500 group-hover:text-white' }}" />
                <span>{{ $item['label'] }}</span>
                @if ($isActive)
                    <span class="ml-auto h-1.5 w-1.5 rounded-full bg-sonic-400"></span>
                @endif
            </button>
        @endforeach
    </nav>

    <div class="mt-7 flex min-h-0 flex-1 flex-col border-t border-white/[0.06] pt-5">
        <div class="flex items-center justify-between px-2">
            <span class="text-sm font-semibold text-zinc-400">Sua biblioteca</span>
            <span class="rounded-full bg-white/5 px-2 py-0.5 font-mono text-[10px] text-zinc-500">{{ count($favoriteTracks) }}</span>
        </div>

        <button
            wire:click="openFavorites"
            {!! $closeDrawer !!}
            class="group mt-3 flex w-full items-center gap-3 rounded-md px-2 py-2 text-left transition hover:bg-white/[0.06]"
        >
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded bg-gradient-to-br from-sonic-500 to-ink-700 text-ink-950 shadow-lg shadow-sonic-500/10">
                <x-icon name="heart" :size="17" filled />
            </span>
            <span class="min-w-0">
                <span class="block truncate text-sm font-medium text-zinc-100">Músicas curtidas</span>
                <span class="block truncate text-xs text-zinc-500">Playlist · {{ count($favoriteTracks) }} {{ count($favoriteTracks) === 1 ? 'faixa' : 'faixas' }}</span>
            </span>
        </button>

        <div class="mt-5 flex items-center justify-between px-3">
            <span class="text-sm font-semibold text-zinc-400">Playlists</span>
            <button wire:click="navigateTo('playlists')" {!! $closeDrawer !!} class="text-[11px] text-zinc-500 transition hover:text-sonic-400">Ver todas</button>
        </div>

        <div class="mt-2 space-y-0.5">
            @foreach ($playlists as $playlist)
                <button
                    wire:key="{{ $keyPrefix }}-sidebar-playlist-{{ $playlist['id'] }}"
                    wire:click="openPlaylist('{{ $playlist['id'] }}')"
                    {!! $closeDrawer !!}
                    class="group flex w-full items-center gap-3 rounded-md px-2 py-1.5 text-left transition hover:bg-white/[0.06]"
                >
                    <x-sonic.cover :cover="$playlist['cover']" class="h-9 w-9 shrink-0 rounded-md" />
                    <span class="min-w-0">
                        <span class="block truncate text-[13px] font-medium text-zinc-300 group-hover:text-white">{{ $playlist['title'] }}</span>
                        <span class="block truncate text-[11px] text-zinc-600">Playlist · {{ $playlist['trackCount'] }} {{ $playlist['trackCount'] === 1 ? 'faixa' : 'faixas' }}</span>
                    </span>
                </button>
            @endforeach
        </div>

        <div class="mt-auto pt-6">
            <div class="relative overflow-hidden rounded-2xl border border-white/[0.06] bg-ink-850/80 p-4">
                <div class="absolute -right-6 -top-8 h-24 w-24 rounded-full bg-sonic-500/15 blur-2xl"></div>
                <div class="relative flex items-center gap-2 text-sonic-300">
                    <x-icon name="sparkles" :size="15" />
                    <span class="font-mono text-[9px] uppercase tracking-[0.2em]">Som original</span>
                </div>
                <p class="relative mt-2 text-[12px] leading-5 text-zinc-400">
                    As faixas deste demo são sintetizadas no navegador. Nenhum áudio protegido por direitos autorais.
                </p>
            </div>
        </div>
    </div>
</div>
