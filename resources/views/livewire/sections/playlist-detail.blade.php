@php
    $playlist = $selectedPlaylist;
    $playlistTracks = $playlist['tracks'];
    $playlistArtistList = array_filter($playlistArtists);
@endphp

<div class="space-y-9">
    <section class="flex flex-col gap-6 sm:flex-row sm:items-end">
        <div class="relative shrink-0 self-start sm:self-end">
            <div class="absolute -inset-4 rounded-[30px] bg-sonic-400/10 blur-2xl"></div>
            <x-sonic.cover
                :cover="$playlist['cover']"
                label="Playlist"
                class="relative h-40 w-40 rounded-2xl shadow-[0_28px_60px_-20px_rgba(0,0,0,.9)] sm:h-48 sm:w-48 lg:h-52 lg:w-52"
            />
        </div>

        <div class="min-w-0 flex-1">
            <p class="font-mono text-[10px] uppercase tracking-[0.28em] text-zinc-500">Playlist</p>
            <h1 class="mt-2 max-w-3xl font-display text-3xl font-bold leading-[1.08] tracking-[-0.04em] text-white sm:text-4xl lg:text-[46px]">
                {{ $playlist['title'] }}
            </h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-zinc-400">{{ $playlist['description'] }}</p>

            <div class="mt-4 flex flex-wrap items-center gap-x-2 gap-y-1 text-[11px] text-zinc-500">
                <span class="flex items-center gap-1.5 text-zinc-300">
                    <x-icon name="sparkles" :size="13" class="text-sonic-300" />
                    {{ $playlist['curator'] }}
                </span>
                <span class="text-zinc-700">•</span>
                <span>{{ $playlist['trackCount'] }} {{ $playlist['trackCount'] === 1 ? 'faixa' : 'faixas' }}</span>
                <span class="text-zinc-700">•</span>
                <span>{{ $playlist['durationLabel'] }}</span>
                <span class="text-zinc-700">•</span>
                <span>{{ $playlist['tag'] }}</span>
            </div>

            <div class="mt-6 flex flex-wrap items-center gap-3">
                <button
                    wire:click="playPlaylist('{{ $playlist['id'] }}')"
                    class="inline-flex h-11 items-center gap-2 rounded-full bg-sonic-400 px-5 text-[13px] font-semibold text-ink-950 shadow-glow transition hover:scale-[1.03] hover:bg-sonic-300 active:scale-95"
                >
                    <x-icon name="play" :size="16" />
                    Tocar tudo
                </button>
                <button
                    wire:click="navigateTo('playlists')"
                    class="inline-flex h-11 items-center gap-2 rounded-full border border-white/10 bg-white/[0.04] px-5 text-[13px] font-medium text-zinc-200 transition hover:border-white/20 hover:bg-white/[0.07] hover:text-white"
                >
                    Outras playlists
                    <x-icon name="arrow-right" :size="15" />
                </button>
            </div>
        </div>
    </section>

    @if ($playlistArtistList !== [])
        <section class="flex flex-wrap items-center gap-2">
            <span class="mr-1 font-mono text-[9px] uppercase tracking-[0.2em] text-zinc-500">Artistas</span>
            @foreach ($playlistArtistList as $artist)
                <button
                    wire:click="openArtist('{{ $artist['id'] }}')"
                    class="inline-flex items-center gap-2 rounded-full border border-white/[0.07] bg-white/[0.03] py-1 pl-1 pr-3 text-[11px] font-medium text-zinc-300 transition hover:border-white/15 hover:bg-white/[0.06] hover:text-white"
                >
                    <x-sonic.cover :cover="$artist['cover']" class="h-6 w-6 rounded-full" />
                    {{ $artist['name'] }}
                </button>
            @endforeach
        </section>
    @endif

    <section class="space-y-3">
        <div class="hidden grid-cols-[44px_minmax(0,1fr)_86px] gap-3 px-2 pb-1 font-mono text-[9px] uppercase tracking-[0.2em] text-zinc-600 sm:grid">
            <span></span>
            <span>Título</span>
            <span class="text-right">Duração</span>
        </div>

        <div class="rounded-2xl border border-white/[0.055] bg-ink-850/40 p-2 sm:p-3">
            @foreach ($playlistTracks as $track)
                @include('livewire.partials.track-row', [
                    'track' => $track,
                    'queueType' => 'playlist',
                    'queueId' => $playlist['id'],
                    'showAlbum' => true,
                    'favoriteTrackIds' => $favoriteTrackIds,
                ])
            @endforeach
        </div>
    </section>

    @if ($relatedPlaylists !== [])
        <section class="space-y-4 border-t border-white/[0.06] pt-8">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="font-mono text-[10px] uppercase tracking-[0.28em] text-zinc-500">Continue explorando</p>
                    <h2 class="mt-1.5 font-display text-xl font-semibold tracking-[-0.02em] text-white sm:text-2xl">Playlists parecidas</h2>
                </div>
                <button wire:click="navigateTo('playlists')" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-zinc-500 transition hover:text-sonic-300">
                    Ver todas
                    <x-icon name="arrow-right" :size="13" />
                </button>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-3">
                @foreach ($relatedPlaylists as $related)
                    @include('livewire.partials.media-card', ['type' => 'playlist', 'item' => $related])
                @endforeach
            </div>
        </section>
    @endif
</div>
