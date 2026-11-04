@php
    $tabs = [
        ['key' => 'liked', 'label' => 'Curtidas', 'count' => count($favoriteTracks)],
        ['key' => 'playlists', 'label' => 'Playlists', 'count' => count($playlists)],
        ['key' => 'albums', 'label' => 'Álbuns', 'count' => count($albums)],
        ['key' => 'artists', 'label' => 'Artistas', 'count' => count($artists)],
    ];

    $favoriteDuration = $catalog->formatDuration(array_sum(array_column($favoriteTracks, 'duration')));
@endphp

<div class="space-y-8">
    <section class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="font-mono text-[10px] uppercase tracking-[0.3em] text-sonic-400/80">Sua biblioteca</p>
            <h1 class="mt-2 font-display text-3xl font-bold tracking-[-0.035em] text-white sm:text-4xl lg:text-[42px]">
                Tudo no seu ritmo
            </h1>
            <p class="mt-3 max-w-xl text-sm leading-6 text-zinc-400">
                As músicas curtidas ficam salvas nesta sessão. Playlists, álbuns e artistas do catálogo também vivem aqui.
            </p>
        </div>

        <span class="inline-flex w-fit items-center gap-2 rounded-full border border-white/[0.07] bg-white/[0.03] px-3.5 py-2 font-mono text-[10px] uppercase tracking-[0.18em] text-zinc-400">
            <x-icon name="heart" :size="13" class="text-sonic-300" />
            {{ count($favoriteTracks) }} {{ count($favoriteTracks) === 1 ? 'curtida' : 'curtidas' }}
        </span>
    </section>

    <div class="flex flex-wrap gap-2 border-b border-white/[0.06] pb-3">
        @foreach ($tabs as $tab)
            <button
                wire:click="setLibraryTab('{{ $tab['key'] }}')"
                class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-[12px] font-medium transition {{ $libraryTab === $tab['key'] ? 'bg-white/[0.09] text-white' : 'text-zinc-500 hover:bg-white/[0.04] hover:text-zinc-200' }}"
                @if ($libraryTab === $tab['key']) aria-pressed="true" @endif
            >
                {{ $tab['label'] }}
                <span class="rounded-full bg-white/[0.06] px-1.5 py-0.5 font-mono text-[9px] text-zinc-500">{{ $tab['count'] }}</span>
            </button>
        @endforeach
    </div>

    @if ($libraryTab === 'liked')
        @if ($favoriteTracks === [])
            <section class="relative overflow-hidden rounded-[28px] border border-white/[0.06] bg-ink-850/45 px-6 py-14 text-center sm:px-10">
                <div class="absolute left-1/2 top-0 h-48 w-48 -translate-x-1/2 rounded-full bg-sonic-400/10 blur-3xl"></div>
                <div class="relative mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-sonic-500 to-ink-700 text-ink-950 shadow-glow">
                    <x-icon name="heart" :size="28" />
                </div>
                <h2 class="relative mt-5 font-display text-2xl font-semibold tracking-[-0.02em] text-white">Sua coleção começa aqui</h2>
                <p class="relative mx-auto mt-2 max-w-md text-sm leading-6 text-zinc-400">
                    Toque no coração de uma faixa para salvá-la. Suas escolhas aparecem imediatamente nesta lista.
                </p>
                <button
                    wire:click="navigateTo('explore')"
                    class="relative mt-6 inline-flex h-11 items-center gap-2 rounded-full bg-sonic-400 px-5 text-[13px] font-semibold text-ink-950 shadow-glow transition hover:scale-[1.03] hover:bg-sonic-300 active:scale-95"
                >
                    <x-icon name="compass" :size="16" />
                    Explorar catálogo
                </button>
            </section>
        @else
            <section class="space-y-5">
                <div class="flex flex-col gap-4 rounded-[24px] border border-white/[0.06] bg-ink-850/45 p-4 sm:flex-row sm:items-center sm:p-5">
                    <div class="relative shrink-0">
                        <div class="absolute -inset-2 rounded-2xl bg-sonic-400/15 blur-xl"></div>
                        <div class="relative flex h-24 w-24 items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-sonic-500 via-sonic-600 to-ink-800 text-ink-950 shadow-lg shadow-black/30">
                            <div class="cover-grain absolute inset-0"></div>
                            <x-icon name="heart" :size="38" filled />
                        </div>
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="font-mono text-[9px] uppercase tracking-[0.22em] text-sonic-300/80">Playlist pessoal</p>
                        <h2 class="mt-1 font-display text-2xl font-bold tracking-[-0.025em] text-white">Músicas curtidas</h2>
                        <p class="mt-1.5 text-[12px] text-zinc-500">{{ count($favoriteTracks) }} faixas · {{ $favoriteDuration }}</p>

                        <div class="mt-4 flex flex-wrap items-center gap-3">
                            <button
                                wire:click="playFavorites"
                                class="inline-flex h-10 items-center gap-2 rounded-full bg-sonic-400 px-4 text-[12px] font-semibold text-ink-950 shadow-glow transition hover:scale-[1.03] hover:bg-sonic-300 active:scale-95"
                            >
                                <x-icon name="play" :size="15" />
                                Tocar curtidas
                            </button>
                            <button
                                wire:click="navigateTo('explore')"
                                class="inline-flex h-10 items-center gap-2 rounded-full border border-white/10 bg-white/[0.04] px-4 text-[12px] font-medium text-zinc-200 transition hover:border-white/20 hover:bg-white/[0.07] hover:text-white"
                            >
                                Encontrar mais
                                <x-icon name="arrow-right" :size="14" />
                            </button>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-white/[0.055] bg-ink-850/40 p-2 sm:p-3">
                    @foreach ($favoriteTracks as $track)
                        @include('livewire.partials.track-row', [
                            'track' => $track,
                            'queueType' => 'favorites',
                            'queueId' => null,
                            'showAlbum' => true,
                            'favoriteTrackIds' => $favoriteTrackIds,
                        ])
                    @endforeach
                </div>
            </section>
        @endif
    @elseif ($libraryTab === 'playlists')
        <section class="space-y-4">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="font-mono text-[10px] uppercase tracking-[0.28em] text-zinc-500">Coleção</p>
                    <h2 class="mt-1.5 font-display text-xl font-semibold tracking-[-0.02em] text-white sm:text-2xl">Playlists salvas</h2>
                </div>
                <button wire:click="navigateTo('playlists')" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-zinc-500 transition hover:text-sonic-300">
                    Ver todas
                    <x-icon name="arrow-right" :size="13" />
                </button>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                @foreach ($playlists as $playlist)
                    @include('livewire.partials.media-card', ['type' => 'playlist', 'item' => $playlist])
                @endforeach
            </div>
        </section>
    @elseif ($libraryTab === 'albums')
        <section class="space-y-4">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="font-mono text-[10px] uppercase tracking-[0.28em] text-zinc-500">Coleção</p>
                    <h2 class="mt-1.5 font-display text-xl font-semibold tracking-[-0.02em] text-white sm:text-2xl">Álbuns salvos</h2>
                </div>
                <span class="text-[11px] text-zinc-500">{{ count($albums) }} álbuns</span>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                @foreach ($albums as $album)
                    @include('livewire.partials.media-card', ['type' => 'album', 'item' => $album])
                @endforeach
            </div>
        </section>
    @else
        <section class="space-y-4">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="font-mono text-[10px] uppercase tracking-[0.28em] text-zinc-500">Coleção</p>
                    <h2 class="mt-1.5 font-display text-xl font-semibold tracking-[-0.02em] text-white sm:text-2xl">Artistas seguidos</h2>
                </div>
                <span class="text-[11px] text-zinc-500">{{ count($artists) }} artistas</span>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                @foreach ($artists as $artist)
                    @include('livewire.partials.media-card', ['type' => 'artist', 'item' => $artist])
                @endforeach
            </div>
        </section>
    @endif
</div>
