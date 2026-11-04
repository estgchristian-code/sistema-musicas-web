@php
    $term = trim($search);
    $resultTracks = $searchResults['tracks'];
    $resultAlbums = $searchResults['albums'];
    $resultArtists = $searchResults['artists'];
    $resultPlaylists = $searchResults['playlists'];

    $totalResults = count($resultTracks) + count($resultAlbums) + count($resultArtists) + count($resultPlaylists);

    $tabs = [
        ['key' => 'all', 'label' => 'Tudo', 'count' => $totalResults],
        ['key' => 'tracks', 'label' => 'Faixas', 'count' => count($resultTracks)],
        ['key' => 'albums', 'label' => 'Álbuns', 'count' => count($resultAlbums)],
        ['key' => 'artists', 'label' => 'Artistas', 'count' => count($resultArtists)],
        ['key' => 'playlists', 'label' => 'Playlists', 'count' => count($resultPlaylists)],
    ];

    $topType = null;
    $topItem = null;

    if ($resultArtists !== []) {
        $topType = 'artist';
        $topItem = $resultArtists[array_key_first($resultArtists)];
    } elseif ($resultAlbums !== []) {
        $topType = 'album';
        $topItem = $resultAlbums[array_key_first($resultAlbums)];
    } elseif ($resultPlaylists !== []) {
        $topType = 'playlist';
        $topItem = $resultPlaylists[array_key_first($resultPlaylists)];
    }

    $topTitle = match ($topType) {
        'artist' => $topItem['name'] ?? '',
        'album', 'playlist' => $topItem['title'] ?? '',
        default => null,
    };

    $topSubtitle = match ($topType) {
        'artist' => ($topItem['genre'] ?? '').' · Artista',
        'album' => ($topItem['artistName'] ?? '').' · Álbum',
        'playlist' => ($topItem['tag'] ?? '').' · Playlist',
        default => null,
    };

    $topOpen = match ($topType) {
        'artist' => "openArtist('{$topItem['id']}')",
        'album' => "openAlbum('{$topItem['id']}')",
        'playlist' => "openPlaylist('{$topItem['id']}')",
        default => '',
    };

    $topPlay = match ($topType) {
        'artist' => "playArtist('{$topItem['id']}')",
        'album' => "playAlbum('{$topItem['id']}')",
        'playlist' => "playPlaylist('{$topItem['id']}')",
        default => '',
    };
@endphp

<div class="space-y-8">
    <section class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="font-mono text-[10px] uppercase tracking-[0.3em] text-sonic-400/80">Busca</p>
            <h1 class="mt-2 max-w-3xl font-display text-3xl font-bold leading-[1.1] tracking-[-0.035em] text-white sm:text-4xl">
                @if ($term !== '')
                    Resultados para <span class="text-sonic-300">“{{ $term }}”</span>
                @else
                    O que você quer ouvir?
                @endif
            </h1>
            <p class="mt-2 text-sm text-zinc-500">
                @if ($totalResults > 0)
                    {{ $totalResults }} {{ $totalResults === 1 ? 'resultado encontrado' : 'resultados encontrados' }} no catálogo fictício.
                @else
                    Busque por faixas, álbuns, artistas ou playlists.
                @endif
            </p>
        </div>

        @if ($resultTracks !== [])
            <button
                wire:click="playSearch"
                class="inline-flex h-11 w-fit items-center gap-2 rounded-full bg-sonic-400 px-5 text-[13px] font-semibold text-ink-950 shadow-glow transition hover:scale-[1.03] hover:bg-sonic-300 active:scale-95"
            >
                <x-icon name="play" :size="16" />
                Tocar resultados
            </button>
        @endif
    </section>

    @if ($term === '')
        <section class="relative overflow-hidden rounded-[28px] border border-white/[0.06] bg-ink-850/45 px-6 py-14 text-center">
            <div class="absolute left-1/2 top-0 h-48 w-48 -translate-x-1/2 rounded-full bg-sonic-400/10 blur-3xl"></div>
            <div class="relative mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-white/[0.04] text-sonic-300">
                <x-icon name="search" :size="26" />
            </div>
            <h2 class="relative mt-5 font-display text-2xl font-semibold tracking-[-0.02em] text-white">Comece digitando</h2>
            <p class="relative mx-auto mt-2 max-w-md text-sm leading-6 text-zinc-400">
                Experimente “Luna”, “Maré Alta”, “foco” ou “synthwave” para ver a busca em ação.
            </p>
        </section>
    @else
        <div class="flex flex-wrap gap-2 border-b border-white/[0.06] pb-3">
            @foreach ($tabs as $tab)
                <button
                    wire:click="setSearchTab('{{ $tab['key'] }}')"
                    class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-[12px] font-medium transition {{ $searchTab === $tab['key'] ? 'bg-white/[0.09] text-white' : 'text-zinc-500 hover:bg-white/[0.04] hover:text-zinc-200' }}"
                    @if ($searchTab === $tab['key']) aria-pressed="true" @endif
                >
                    {{ $tab['label'] }}
                    <span class="rounded-full bg-white/[0.06] px-1.5 py-0.5 font-mono text-[9px] text-zinc-500">{{ $tab['count'] }}</span>
                </button>
            @endforeach
        </div>

        @if ($totalResults === 0)
            <section class="relative overflow-hidden rounded-[28px] border border-white/[0.06] bg-ink-850/45 px-6 py-14 text-center">
                <div class="absolute left-1/2 top-0 h-48 w-48 -translate-x-1/2 rounded-full bg-flare/10 blur-3xl"></div>
                <div class="relative mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-white/[0.04] text-zinc-400">
                    <x-icon name="search" :size="26" />
                </div>
                <h2 class="relative mt-5 font-display text-2xl font-semibold tracking-[-0.02em] text-white">Nada por aqui</h2>
                <p class="relative mx-auto mt-2 max-w-md text-sm leading-6 text-zinc-400">
                    Não encontramos nada para “{{ $term }}”. Tente outro artista, álbum ou gênero.
                </p>
                <button
                    wire:click="navigateTo('explore')"
                    class="relative mt-6 inline-flex h-11 items-center gap-2 rounded-full border border-white/10 bg-white/[0.04] px-5 text-[13px] font-medium text-zinc-200 transition hover:border-sonic-400/30 hover:bg-sonic-400/[0.07] hover:text-white"
                >
                    <x-icon name="compass" :size="16" />
                    Explorar catálogo
                </button>
            </section>
        @elseif ($searchTab === 'all')
            <div class="{{ $topItem !== null ? 'grid gap-6 xl:grid-cols-[0.85fr_1.15fr]' : 'space-y-6' }}">
                @if ($topItem !== null)
                    <section class="space-y-3">
                        <h2 class="font-display text-lg font-semibold tracking-[-0.02em] text-white">Resultado principal</h2>

                        <article class="group relative overflow-hidden rounded-[24px] border border-white/[0.06] bg-ink-850/50 p-4 transition hover:border-white/10 hover:bg-ink-800/70">
                            <div class="flex items-center gap-4">
                                <button wire:click="{{ $topOpen }}" class="shrink-0" aria-label="Abrir {{ $topTitle }}">
                                    <x-sonic.cover :cover="$topItem['cover']" :image="$topItem['coverImage'] ?? null" class="h-24 w-24 rounded-xl shadow-lg shadow-black/30 sm:h-28 sm:w-28" />
                                </button>

                                <div class="min-w-0 flex-1">
                                    <p class="font-mono text-[9px] uppercase tracking-[0.2em] text-sonic-300/80">Destaque</p>
                                    <button wire:click="{{ $topOpen }}" class="mt-1 block max-w-full truncate text-left font-display text-2xl font-bold tracking-[-0.025em] text-white transition hover:text-sonic-200">
                                        {{ $topTitle }}
                                    </button>
                                    <p class="mt-1 truncate text-[12px] text-zinc-500">{{ $topSubtitle }}</p>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center gap-2">
                                <button
                                    wire:click="{{ $topPlay }}"
                                    class="inline-flex h-10 items-center gap-2 rounded-full bg-sonic-400 px-4 text-[12px] font-semibold text-ink-950 transition hover:scale-[1.03] hover:bg-sonic-300 active:scale-95"
                                >
                                    <x-icon name="play" :size="14" />
                                    Tocar
                                </button>
                                <button
                                    wire:click="{{ $topOpen }}"
                                    class="inline-flex h-10 items-center gap-2 rounded-full border border-white/10 bg-white/[0.04] px-4 text-[12px] font-medium text-zinc-200 transition hover:border-white/20 hover:bg-white/[0.07] hover:text-white"
                                >
                                    Abrir
                                    <x-icon name="arrow-right" :size="14" />
                                </button>
                            </div>
                        </article>
                    </section>
                @endif

                <section class="space-y-3">
                    <div class="flex items-end justify-between gap-4">
                        <h2 class="font-display text-lg font-semibold tracking-[-0.02em] text-white">Faixas</h2>
                        @if ($resultTracks !== [])
                            <button wire:click="setSearchTab('tracks')" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-zinc-500 transition hover:text-sonic-300">
                                Ver todas
                                <x-icon name="arrow-right" :size="13" />
                            </button>
                        @endif
                    </div>

                    @if ($resultTracks === [])
                        <p class="rounded-2xl border border-white/[0.06] bg-ink-850/40 px-5 py-8 text-center text-sm text-zinc-400">
                            Nenhuma faixa encontrada.
                        </p>
                    @else
                        <div class="rounded-2xl border border-white/[0.055] bg-ink-850/40 p-2 sm:p-3">
                            @foreach (array_slice($resultTracks, 0, 4, true) as $track)
                                @include('livewire.partials.track-row', [
                                    'track' => $track,
                                    'queueType' => 'search',
                                    'queueId' => null,
                                    'showAlbum' => true,
                                    'favoriteTrackIds' => $favoriteTrackIds,
                                ])
                            @endforeach
                        </div>
                    @endif
                </section>
            </div>

            @if ($resultAlbums !== [])
                <section class="space-y-4 border-t border-white/[0.06] pt-8">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="font-mono text-[10px] uppercase tracking-[0.28em] text-zinc-500">Álbuns</p>
                            <h2 class="mt-1.5 font-display text-xl font-semibold tracking-[-0.02em] text-white sm:text-2xl">Discos encontrados</h2>
                        </div>
                        <button wire:click="setSearchTab('albums')" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-zinc-500 transition hover:text-sonic-300">
                            Ver todos
                            <x-icon name="arrow-right" :size="13" />
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                        @foreach ($resultAlbums as $album)
                            @include('livewire.partials.media-card', ['type' => 'album', 'item' => $album])
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($resultArtists !== [])
                <section class="space-y-4 border-t border-white/[0.06] pt-8">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="font-mono text-[10px] uppercase tracking-[0.28em] text-zinc-500">Artistas</p>
                            <h2 class="mt-1.5 font-display text-xl font-semibold tracking-[-0.02em] text-white sm:text-2xl">Vozes encontradas</h2>
                        </div>
                        <button wire:click="setSearchTab('artists')" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-zinc-500 transition hover:text-sonic-300">
                            Ver todos
                            <x-icon name="arrow-right" :size="13" />
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                        @foreach ($resultArtists as $artist)
                            @include('livewire.partials.media-card', ['type' => 'artist', 'item' => $artist])
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($resultPlaylists !== [])
                <section class="space-y-4 border-t border-white/[0.06] pt-8">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="font-mono text-[10px] uppercase tracking-[0.28em] text-zinc-500">Playlists</p>
                            <h2 class="mt-1.5 font-display text-xl font-semibold tracking-[-0.02em] text-white sm:text-2xl">Seleções encontradas</h2>
                        </div>
                        <button wire:click="setSearchTab('playlists')" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-zinc-500 transition hover:text-sonic-300">
                            Ver todas
                            <x-icon name="arrow-right" :size="13" />
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                        @foreach ($resultPlaylists as $playlist)
                            @include('livewire.partials.media-card', ['type' => 'playlist', 'item' => $playlist])
                        @endforeach
                    </div>
                </section>
            @endif
        @elseif ($searchTab === 'tracks')
            @if ($resultTracks === [])
                <p class="rounded-2xl border border-white/[0.06] bg-ink-850/40 px-5 py-10 text-center text-sm text-zinc-400">
                    Nenhuma faixa encontrada para “{{ $term }}”.
                </p>
            @else
                <section class="space-y-3">
                    <div class="flex items-end justify-between gap-4">
                        <h2 class="font-display text-xl font-semibold tracking-[-0.02em] text-white">Faixas encontradas</h2>
                        <span class="text-[11px] text-zinc-500">{{ count($resultTracks) }} faixas</span>
                    </div>

                    <div class="rounded-2xl border border-white/[0.055] bg-ink-850/40 p-2 sm:p-3">
                        @foreach ($resultTracks as $track)
                            @include('livewire.partials.track-row', [
                                'track' => $track,
                                'queueType' => 'search',
                                'queueId' => null,
                                'showAlbum' => true,
                                'favoriteTrackIds' => $favoriteTrackIds,
                            ])
                        @endforeach
                    </div>
                </section>
            @endif
        @elseif ($searchTab === 'albums')
            @if ($resultAlbums === [])
                <p class="rounded-2xl border border-white/[0.06] bg-ink-850/40 px-5 py-10 text-center text-sm text-zinc-400">
                    Nenhum álbum encontrado para “{{ $term }}”.
                </p>
            @else
                <section class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                    @foreach ($resultAlbums as $album)
                        @include('livewire.partials.media-card', ['type' => 'album', 'item' => $album])
                    @endforeach
                </section>
            @endif
        @elseif ($searchTab === 'artists')
            @if ($resultArtists === [])
                <p class="rounded-2xl border border-white/[0.06] bg-ink-850/40 px-5 py-10 text-center text-sm text-zinc-400">
                    Nenhum artista encontrado para “{{ $term }}”.
                </p>
            @else
                <section class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                    @foreach ($resultArtists as $artist)
                        @include('livewire.partials.media-card', ['type' => 'artist', 'item' => $artist])
                    @endforeach
                </section>
            @endif
        @else
            @if ($resultPlaylists === [])
                <p class="rounded-2xl border border-white/[0.06] bg-ink-850/40 px-5 py-10 text-center text-sm text-zinc-400">
                    Nenhuma playlist encontrada para “{{ $term }}”.
                </p>
            @else
                <section class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                    @foreach ($resultPlaylists as $playlist)
                        @include('livewire.partials.media-card', ['type' => 'playlist', 'item' => $playlist])
                    @endforeach
                </section>
            @endif
        @endif
    @endif
</div>
