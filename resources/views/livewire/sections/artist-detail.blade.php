@php
    $artist = $selectedArtist;
    $artistTracks = array_slice($artist['tracks'], 0, 5, true);
    $artistAlbums = $artist['albums'];
@endphp

<div class="space-y-9">
    <section class="relative overflow-hidden rounded-[28px] border border-white/[0.06] bg-ink-850/50 p-5 sm:p-7">
        <div
            class="pointer-events-none absolute inset-0 opacity-60"
            style="background-image: radial-gradient(circle at 86% 10%, {{ $artist['cover']['accent'] }}2e, transparent 42%), linear-gradient(150deg, rgba(20,26,21,.7), rgba(9,13,10,.97));"
        ></div>

        <div class="relative flex flex-col gap-6 sm:flex-row sm:items-end">
            <div class="relative w-fit shrink-0 self-start sm:self-end">
                <div class="absolute -inset-4 rounded-full bg-sonic-400/10 blur-2xl"></div>
                <x-sonic.cover
                    :cover="$artist['cover']"
                    class="relative h-36 w-36 rounded-full shadow-[0_24px_56px_-18px_rgba(0,0,0,.9)] ring-1 ring-white/10 sm:h-44 sm:w-44"
                />
            </div>

            <div class="min-w-0 flex-1">
                <p class="font-mono text-[10px] uppercase tracking-[0.28em] text-sonic-300/80">Artista</p>
                <h1 class="mt-2 font-display text-3xl font-bold leading-[1.06] tracking-[-0.04em] text-white sm:text-4xl lg:text-[46px]">
                    {{ $artist['name'] }}
                </h1>

                <p class="mt-3 flex flex-wrap items-center gap-x-2 gap-y-1 text-[11px] text-zinc-500">
                    <span class="rounded-full border border-white/[0.08] bg-white/[0.04] px-2 py-0.5 text-[10px] text-zinc-300">{{ $artist['genre'] }}</span>
                    <span class="text-zinc-700">•</span>
                    <span>{{ $artist['listeners'] }} ouvintes mensais</span>
                    <span class="text-zinc-700">•</span>
                    <span>{{ $artist['trackCount'] }} {{ $artist['trackCount'] === 1 ? 'faixa' : 'faixas' }}</span>
                    <span class="text-zinc-700">•</span>
                    <span>{{ count($artistAlbums) }} {{ count($artistAlbums) === 1 ? 'álbum' : 'álbuns' }}</span>
                </p>

                <p class="mt-3 max-w-2xl text-sm leading-6 text-zinc-400">{{ $artist['bio'] }}</p>

                <div class="mt-5 flex flex-wrap items-center gap-3">
                    <button
                        wire:click="playArtist('{{ $artist['id'] }}')"
                        class="inline-flex h-11 items-center gap-2 rounded-full bg-sonic-400 px-5 text-[13px] font-semibold text-ink-950 shadow-glow transition hover:scale-[1.03] hover:bg-sonic-300 active:scale-95"
                    >
                        <x-icon name="play" :size="16" />
                        Tocar {{ $artist['name'] }}
                    </button>

                    @if ($artistAlbums !== [])
                        <button
                            wire:click="openAlbum('{{ array_key_first($artistAlbums) }}')"
                            class="inline-flex h-11 items-center gap-2 rounded-full border border-white/10 bg-white/[0.04] px-5 text-[13px] font-medium text-zinc-200 transition hover:border-white/20 hover:bg-white/[0.07] hover:text-white"
                        >
                            <x-icon name="disc" :size="15" />
                            Ver discografia
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="grid gap-8 xl:grid-cols-[1.35fr_1fr]">
        <div class="space-y-3">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="font-mono text-[10px] uppercase tracking-[0.28em] text-zinc-500">Populares</p>
                    <h2 class="mt-1.5 font-display text-xl font-semibold tracking-[-0.02em] text-white sm:text-2xl">Faixas mais tocadas</h2>
                </div>
                <span class="text-[11px] text-zinc-500">{{ count($artistTracks) }} faixas</span>
            </div>

            <div class="rounded-2xl border border-white/[0.055] bg-ink-850/40 p-2 sm:p-3">
                @foreach ($artistTracks as $track)
                    @include('livewire.partials.track-row', [
                        'track' => $track,
                        'queueType' => 'artist',
                        'queueId' => $artist['id'],
                        'showAlbum' => true,
                        'favoriteTrackIds' => $favoriteTrackIds,
                    ])
                @endforeach
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="font-mono text-[10px] uppercase tracking-[0.28em] text-zinc-500">Discografia</p>
                    <h2 class="mt-1.5 font-display text-xl font-semibold tracking-[-0.02em] text-white sm:text-2xl">Álbuns</h2>
                </div>
                <span class="text-[11px] text-zinc-500">{{ count($artistAlbums) }}</span>
            </div>

            @if ($artistAlbums === [])
                <p class="rounded-2xl border border-white/[0.06] bg-ink-850/40 px-5 py-8 text-center text-sm text-zinc-400">
                    Nenhum álbum disponível.
                </p>
            @else
                <div class="grid grid-cols-2 gap-3 sm:gap-4">
                    @foreach ($artistAlbums as $album)
                        @include('livewire.partials.media-card', ['type' => 'album', 'item' => $album])
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @if ($artistPlaylists !== [])
        <section class="space-y-4 border-t border-white/[0.06] pt-8">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="font-mono text-[10px] uppercase tracking-[0.28em] text-zinc-500">Curadoria</p>
                    <h2 class="mt-1.5 font-display text-xl font-semibold tracking-[-0.02em] text-white sm:text-2xl">Aparece nestas playlists</h2>
                </div>
                <button wire:click="navigateTo('playlists')" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-zinc-500 transition hover:text-sonic-300">
                    Ver todas
                    <x-icon name="arrow-right" :size="13" />
                </button>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                @foreach ($artistPlaylists as $playlist)
                    @include('livewire.partials.media-card', ['type' => 'playlist', 'item' => $playlist])
                @endforeach
            </div>
        </section>
    @endif
</div>
