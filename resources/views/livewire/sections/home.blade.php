@php
    $hour = (int) now()->format('G');
    $greeting = match (true) {
        $hour < 12 => 'Bom dia',
        $hour < 18 => 'Boa tarde',
        default => 'Boa noite',
    };

    $continueTracks = array_slice($recentTracks, 0, 3, true);
    $featuredSongs = array_slice(array_diff_key($tracks, $continueTracks), 0, 6, true);
    $homePlaylists = array_slice($playlists, 0, 4, true);
    $homeAlbums = array_slice($albums, 0, 4, true);
    $homeArtists = array_slice($artists, 0, 4, true);
@endphp

<div class="space-y-7 sm:space-y-9">
    <section class="pt-1">
        <h1 class="font-display text-2xl font-bold tracking-[-0.035em] text-white sm:text-[30px]">
            {{ $greeting }}
        </h1>
        <p class="mt-1 text-sm text-zinc-400">Sua trilha sonora para hoje</p>
    </section>

    <section aria-label="Acesso rápido">
        <div class="grid grid-cols-2 gap-2 xl:grid-cols-3 2xl:grid-cols-4">
            @foreach ($quickPicks as $pick)
                @php
                    $openAction = match ($pick['type']) {
                        'favorites' => 'openFavorites',
                        'playlist' => "openPlaylist('{$pick['id']}')",
                        'album' => "openAlbum('{$pick['id']}')",
                        'artist' => "openArtist('{$pick['id']}')",
                    };

                    $playAction = match ($pick['type']) {
                        'favorites' => 'playFavorites',
                        'playlist' => "playPlaylist('{$pick['id']}')",
                        'album' => "playAlbum('{$pick['id']}')",
                        'artist' => "playArtist('{$pick['id']}')",
                    };
                @endphp

                <div class="group flex min-w-0 items-center gap-3 overflow-hidden rounded-md bg-white/[0.07] transition hover:bg-white/[0.14]">
                    <button wire:click="{{ $openAction }}" class="shrink-0" aria-label="Abrir {{ $pick['title'] }}">
                        <x-sonic.cover :cover="$pick['cover']" class="h-14 w-14 rounded-l-md shadow-lg shadow-black/20" />
                    </button>

                    <button wire:click="{{ $openAction }}" class="min-w-0 flex-1 text-left">
                        <span class="block truncate text-[13px] font-semibold text-white">{{ $pick['title'] }}</span>
                        <span class="mt-0.5 block truncate text-[11px] text-zinc-400">{{ $pick['subtitle'] }}</span>
                    </button>

                    <button
                        wire:click="{{ $playAction }}"
                        class="mr-3 flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-sonic-400 text-ink-950 opacity-100 shadow-lg shadow-black/30 transition hover:scale-105 hover:bg-sonic-300 sm:translate-y-1 sm:opacity-0 sm:group-hover:translate-y-0 sm:group-hover:opacity-100 sm:focus-visible:translate-y-0 sm:focus-visible:opacity-100"
                        aria-label="Tocar {{ $pick['title'] }}"
                    >
                        <x-icon name="play" :size="17" />
                    </button>
                </div>
            @endforeach
        </div>
    </section>

    @if ($continueTracks !== [])
        <section class="space-y-3">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-medium text-zinc-400">Seu histórico</p>
                    <h2 class="mt-1 font-display text-xl font-bold tracking-tight text-white sm:text-2xl">Continue ouvindo</h2>
                </div>
            </div>

            <div class="no-scrollbar -mx-2 flex snap-x gap-2 overflow-x-auto px-2 pb-2">
                @foreach ($continueTracks as $track)
                    <article wire:key="continue-track-{{ $track['id'] }}" class="group w-[165px] shrink-0 snap-start rounded-lg p-3 transition hover:bg-white/[0.06] sm:w-[190px]">
                        <div class="relative">
                            <button wire:click="playTrack('{{ $track['id'] }}', 'all')" class="block w-full overflow-hidden rounded-md" aria-label="Tocar {{ $track['title'] }}">
                                <x-sonic.cover :cover="$track['cover']" :image="$track['coverImage'] ?? null" class="aspect-square w-full shadow-lg shadow-black/20 transition duration-300 group-hover:scale-[1.03]" />
                            </button>
                            <button
                                wire:click="playTrack('{{ $track['id'] }}', 'all')"
                                class="absolute bottom-2 right-2 flex h-11 w-11 translate-y-1 items-center justify-center rounded-full bg-sonic-400 text-ink-950 opacity-0 shadow-xl transition hover:scale-105 hover:bg-sonic-300 group-hover:translate-y-0 group-hover:opacity-100 group-focus-within:translate-y-0 group-focus-within:opacity-100"
                                aria-label="Tocar {{ $track['title'] }}"
                            >
                                <x-icon name="play" :size="17" />
                            </button>
                        </div>

                        <div class="mt-3 flex items-start gap-2">
                            <button wire:click="playTrack('{{ $track['id'] }}', 'all')" class="min-w-0 flex-1 text-left">
                                <span class="block truncate text-sm font-semibold text-white">{{ $track['title'] }}</span>
                                <span class="mt-1 block truncate text-xs text-zinc-400">{{ $track['artist'] }}</span>
                            </button>
                            <span class="pt-0.5 text-[11px] text-zinc-500">{{ $track['durationLabel'] }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section class="space-y-3">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-medium text-zinc-400">Curadoria Sonic</p>
                <h2 class="mt-1 font-display text-xl font-bold tracking-tight text-white sm:text-2xl">Playlists em destaque</h2>
            </div>
            <button wire:click="navigateTo('playlists')" class="text-xs font-semibold text-zinc-400 transition hover:text-white">Ver todas</button>
        </div>

        <div class="no-scrollbar -mx-2 flex snap-x gap-2 overflow-x-auto px-2 pb-2">
            @foreach ($homePlaylists as $playlist)
                <div class="w-[165px] shrink-0 snap-start sm:w-[190px]">
                    @include('livewire.partials.media-card', ['type' => 'playlist', 'item' => $playlist])
                </div>
            @endforeach
        </div>
    </section>

    <section class="space-y-3">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-medium text-zinc-400">Sua próxima descoberta</p>
                <h2 class="mt-1 font-display text-xl font-bold tracking-tight text-white sm:text-2xl">Álbuns recomendados</h2>
            </div>
            <button wire:click="navigateTo('explore')" class="text-xs font-semibold text-zinc-400 transition hover:text-white">Ver todos</button>
        </div>

        <div class="no-scrollbar -mx-2 flex snap-x gap-2 overflow-x-auto px-2 pb-2">
            @foreach ($homeAlbums as $album)
                <div class="w-[165px] shrink-0 snap-start sm:w-[190px]">
                    @include('livewire.partials.media-card', ['type' => 'album', 'item' => $album])
                </div>
            @endforeach
        </div>
    </section>

    <section class="space-y-3">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-medium text-zinc-400">Conheça novas vozes</p>
                <h2 class="mt-1 font-display text-xl font-bold tracking-tight text-white sm:text-2xl">Artistas recomendados</h2>
            </div>
            <button wire:click="navigateTo('explore')" class="text-xs font-semibold text-zinc-400 transition hover:text-white">Ver todos</button>
        </div>

        <div class="no-scrollbar -mx-2 flex snap-x gap-2 overflow-x-auto px-2 pb-2">
            @foreach ($homeArtists as $artist)
                <article class="group w-[165px] shrink-0 snap-start rounded-lg p-3 transition hover:bg-white/[0.06] sm:w-[190px]">
                    <div class="relative">
                        <button wire:click="openArtist('{{ $artist['id'] }}')" class="block w-full" aria-label="Abrir {{ $artist['name'] }}">
                        <x-sonic.cover :cover="$artist['cover']" class="aspect-square w-full rounded-full shadow-lg shadow-black/20 transition duration-300 group-hover:scale-[1.03]" />
                        </button>
                        <button
                            wire:click="playArtist('{{ $artist['id'] }}')"
                            class="absolute bottom-2 right-2 flex h-11 w-11 translate-y-1 items-center justify-center rounded-full bg-sonic-400 text-ink-950 opacity-0 shadow-xl transition hover:scale-105 hover:bg-sonic-300 group-hover:translate-y-0 group-hover:opacity-100 group-focus-within:translate-y-0 group-focus-within:opacity-100"
                            aria-label="Tocar {{ $artist['name'] }}"
                        >
                            <x-icon name="play" :size="17" />
                        </button>
                    </div>

                    <button wire:click="openArtist('{{ $artist['id'] }}')" class="mt-3 block w-full text-left">
                        <span class="block truncate text-sm font-semibold text-white">{{ $artist['name'] }}</span>
                        <span class="mt-1 block truncate text-xs text-zinc-400">{{ $artist['genre'] }} · {{ $artist['listeners'] }} ouvintes</span>
                    </button>
                </article>
            @endforeach
        </div>
    </section>

    <section class="space-y-3">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-medium text-zinc-400">Seleção Sonic</p>
                <h2 class="mt-1 font-display text-xl font-bold tracking-tight text-white sm:text-2xl">Músicas recomendadas</h2>
            </div>
            <span class="text-xs text-zinc-400">{{ count($featuredSongs) }} faixas</span>
        </div>

        <div class="no-scrollbar -mx-2 flex snap-x gap-2 overflow-x-auto px-2 pb-2">
            @foreach ($featuredSongs as $track)
                @php
                    $isFavorite = in_array($track['id'], $favoriteTrackIds, true);
                @endphp

                <article wire:key="recommended-track-{{ $track['id'] }}" class="group w-[165px] shrink-0 snap-start rounded-lg p-3 transition hover:bg-white/[0.06] sm:w-[190px]">
                    <div class="relative">
                        <button wire:click="playTrack('{{ $track['id'] }}', 'all')" class="block w-full overflow-hidden rounded-md" aria-label="Tocar {{ $track['title'] }}">
                            <x-sonic.cover :cover="$track['cover']" :image="$track['coverImage'] ?? null" class="aspect-square w-full shadow-lg shadow-black/20 transition duration-300 group-hover:scale-[1.03]" />
                        </button>
                        <button
                            wire:click="playTrack('{{ $track['id'] }}', 'all')"
                            class="absolute bottom-2 right-2 flex h-11 w-11 translate-y-1 items-center justify-center rounded-full bg-sonic-400 text-ink-950 opacity-0 shadow-xl transition hover:scale-105 hover:bg-sonic-300 group-hover:translate-y-0 group-hover:opacity-100 group-focus-within:translate-y-0 group-focus-within:opacity-100"
                            aria-label="Tocar {{ $track['title'] }}"
                        >
                            <x-icon name="play" :size="17" />
                        </button>
                    </div>

                    <div class="mt-3 flex items-start gap-2">
                        <button wire:click="playTrack('{{ $track['id'] }}', 'all')" class="min-w-0 flex-1 text-left">
                            <span class="block truncate text-sm font-semibold text-white">{{ $track['title'] }}</span>
                            <span class="mt-1 block truncate text-xs text-zinc-400">{{ $track['artist'] }}</span>
                        </button>
                        <button
                            wire:click="toggleFavorite('{{ $track['id'] }}')"
                            class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full transition {{ $isFavorite ? 'text-sonic-400' : 'text-zinc-500 opacity-100 hover:text-white sm:opacity-0 sm:group-hover:opacity-100' }}"
                            aria-label="{{ $isFavorite ? 'Remover' : 'Adicionar' }} {{ $track['title'] }} {{ $isFavorite ? 'das' : 'às' }} músicas curtidas"
                            aria-pressed="{{ $isFavorite ? 'true' : 'false' }}"
                        >
                            <x-icon name="heart" :size="15" :filled="$isFavorite" />
                        </button>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
</div>
