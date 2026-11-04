@php
    $genreCovers = [
        'Eletrônico' => ['from' => '#17B978', 'to' => '#07352A', 'accent' => '#C9FF6B', 'pattern' => 'prism'],
        'Indie' => ['from' => '#F08B6A', 'to' => '#3A1C2C', 'accent' => '#FFD3A8', 'pattern' => 'orbit'],
        'Dream Pop' => ['from' => '#7565D8', 'to' => '#1B1740', 'accent' => '#C7B8FF', 'pattern' => 'aurora'],
        'MPB' => ['from' => '#2F6F8F', 'to' => '#0C2433', 'accent' => '#7FE0D2', 'pattern' => 'waves'],
        'Neo Soul' => ['from' => '#EC9A3C', 'to' => '#4B1F3B', 'accent' => '#FFE29A', 'pattern' => 'sun'],
        'Lo-fi' => ['from' => '#3E4E6E', 'to' => '#141625', 'accent' => '#F6B6C8', 'pattern' => 'dusk'],
        'Folktronica' => ['from' => '#4E6E52', 'to' => '#171D16', 'accent' => '#D7E8A8', 'pattern' => 'forest'],
        'Synthwave' => ['from' => '#1C77C3', 'to' => '#101433', 'accent' => '#FF4FD8', 'pattern' => 'grid'],
    ];

    $genreTiles = [];

    foreach ($genres as $genreName) {
        $genreTiles[$genreName] = [
            'cover' => $genreCovers[$genreName] ?? ['from' => '#22D97F', 'to' => '#0C0F0D', 'accent' => '#C9FF6B', 'pattern' => 'pulse'],
            'artists' => count(array_filter($artists, fn (array $artist): bool => $artist['genre'] === $genreName)),
            'playlists' => count($catalog->filteredPlaylists($genreName)),
        ];
    }

    $curatedPlaylists = array_slice($explorePlaylists, 0, 8, true);
    $curatedAlbums = array_slice($exploreAlbums, 0, 4, true);
    $curatedArtists = array_slice($exploreArtists, 0, 4, true);
@endphp

<div class="space-y-10">
    <section class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="font-mono text-[10px] uppercase tracking-[0.3em] text-sonic-400/80">Explorar</p>
            <h1 class="mt-2 font-display text-3xl font-bold tracking-[-0.035em] text-white sm:text-4xl lg:text-[42px]">
                Descubra um novo clima
            </h1>
            <p class="mt-3 max-w-xl text-sm leading-6 text-zinc-400">
                Navegue por gêneros, álbuns e artistas fictícios. Cada faixa é sintetizada ao vivo pelo navegador.
            </p>
        </div>

        <span class="inline-flex w-fit items-center gap-2 rounded-full border border-white/[0.07] bg-white/[0.03] px-3.5 py-2 font-mono text-[10px] uppercase tracking-[0.18em] text-zinc-400">
            <span class="h-1.5 w-1.5 rounded-full bg-sonic-400"></span>
            {{ count($genres) }} gêneros
        </span>
    </section>

    <section class="space-y-4">
        <div class="flex flex-wrap gap-2">
            <button
                wire:click="setGenre(null)"
                class="rounded-full border px-4 py-2 text-[12px] font-medium transition {{ $genre === null ? 'border-sonic-400/40 bg-sonic-400/[0.12] text-sonic-200' : 'border-white/[0.07] bg-white/[0.03] text-zinc-400 hover:border-white/15 hover:text-white' }}"
                @if ($genre === null) aria-pressed="true" @endif
            >
                Todos
            </button>

            @foreach ($genres as $genreName)
                <button
                    wire:click="setGenre('{{ $genreName }}')"
                    class="rounded-full border px-4 py-2 text-[12px] font-medium transition {{ $genre === $genreName ? 'border-sonic-400/40 bg-sonic-400/[0.12] text-sonic-200' : 'border-white/[0.07] bg-white/[0.03] text-zinc-400 hover:border-white/15 hover:text-white' }}"
                    @if ($genre === $genreName) aria-pressed="true" @endif
                >
                    {{ $genreName }}
                </button>
            @endforeach
        </div>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4">
            @foreach ($genres as $genreName)
                @php
                    $tile = $genreTiles[$genreName];
                    $isActive = $genre === $genreName;
                @endphp

                <button
                    wire:click="setGenre('{{ $genreName }}')"
                    class="group relative aspect-[4/3] overflow-hidden rounded-2xl border text-left transition duration-300 hover:-translate-y-1 hover:shadow-card {{ $isActive ? 'border-sonic-400/40 ring-1 ring-sonic-400/25' : 'border-white/[0.06]' }}"
                    aria-label="Explorar {{ $genreName }}"
                >
                    <x-sonic.cover :cover="$tile['cover']" class="absolute inset-0 h-full w-full transition duration-500 group-hover:scale-105" />
                    <span class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/15 to-transparent"></span>

                    <span class="absolute inset-x-0 bottom-0 p-3.5 sm:p-4">
                        <span class="block font-display text-base font-semibold tracking-[-0.01em] text-white sm:text-lg">{{ $genreName }}</span>
                        <span class="mt-1 flex items-center gap-2 text-[10px] text-white/65">
                            <span>{{ $tile['artists'] }} {{ $tile['artists'] === 1 ? 'artista' : 'artistas' }}</span>
                            <span class="text-white/25">•</span>
                            <span>{{ $tile['playlists'] }} {{ $tile['playlists'] === 1 ? 'playlist' : 'playlists' }}</span>
                        </span>
                    </span>

                    <span class="absolute right-3 top-3 flex h-8 w-8 items-center justify-center rounded-full bg-black/25 text-white opacity-0 backdrop-blur transition group-hover:opacity-100">
                        <x-icon name="arrow-right" :size="15" />
                    </span>
                </button>
            @endforeach
        </div>
    </section>

    @if ($genre !== null)
        <div class="flex flex-col gap-2 border-t border-white/[0.06] pt-8 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="font-mono text-[10px] uppercase tracking-[0.28em] text-sonic-400/80">Seleção por gênero</p>
                <h2 class="mt-1.5 font-display text-2xl font-semibold tracking-[-0.025em] text-white">{{ $genre }}</h2>
            </div>
            <button wire:click="setGenre(null)" class="inline-flex w-fit items-center gap-1.5 text-[11px] font-medium text-zinc-500 transition hover:text-sonic-300">
                Limpar filtro
                <x-icon name="x" :size="13" />
            </button>
        </div>
    @endif

    <section class="space-y-4">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="font-mono text-[10px] uppercase tracking-[0.28em] text-zinc-500">{{ $genre !== null ? 'Playlists' : 'Curadoria' }}</p>
                <h2 class="mt-1.5 font-display text-xl font-semibold tracking-[-0.02em] text-white sm:text-2xl">
                    {{ $genre !== null ? 'Playlists com esse clima' : 'Para cada momento' }}
                </h2>
            </div>
            <span class="text-[11px] text-zinc-500">{{ count($curatedPlaylists) }} playlists</span>
        </div>

        @if ($curatedPlaylists === [])
            <p class="rounded-2xl border border-white/[0.06] bg-ink-850/40 px-5 py-8 text-center text-sm text-zinc-400">
                Nenhuma playlist encontrada para este gênero.
            </p>
        @else
            <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                @foreach ($curatedPlaylists as $playlist)
                    @include('livewire.partials.media-card', ['type' => 'playlist', 'item' => $playlist])
                @endforeach
            </div>
        @endif
    </section>

    <section class="grid gap-8 xl:grid-cols-2">
        <div class="space-y-4">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="font-mono text-[10px] uppercase tracking-[0.28em] text-zinc-500">Álbuns</p>
                    <h2 class="mt-1.5 font-display text-xl font-semibold tracking-[-0.02em] text-white sm:text-2xl">
                        {{ $genre !== null ? 'Álbuns do gênero' : 'Discos para ouvir inteiro' }}
                    </h2>
                </div>
                <span class="text-[11px] text-zinc-500">{{ count($curatedAlbums) }}</span>
            </div>

            @if ($curatedAlbums === [])
                <p class="rounded-2xl border border-white/[0.06] bg-ink-850/40 px-5 py-8 text-center text-sm text-zinc-400">
                    Nenhum álbum encontrado.
                </p>
            @else
                <div class="grid grid-cols-2 gap-3 sm:gap-4">
                    @foreach ($curatedAlbums as $album)
                        @include('livewire.partials.media-card', ['type' => 'album', 'item' => $album])
                    @endforeach
                </div>
            @endif
        </div>

        <div class="space-y-4">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="font-mono text-[10px] uppercase tracking-[0.28em] text-zinc-500">Artistas</p>
                    <h2 class="mt-1.5 font-display text-xl font-semibold tracking-[-0.02em] text-white sm:text-2xl">
                        {{ $genre !== null ? 'Vozes do gênero' : 'Quem está no radar' }}
                    </h2>
                </div>
                <span class="text-[11px] text-zinc-500">{{ count($curatedArtists) }}</span>
            </div>

            @if ($curatedArtists === [])
                <p class="rounded-2xl border border-white/[0.06] bg-ink-850/40 px-5 py-8 text-center text-sm text-zinc-400">
                    Nenhum artista encontrado.
                </p>
            @else
                <div class="space-y-2">
                    @foreach ($curatedArtists as $artist)
                        <div class="group flex items-center gap-3 rounded-2xl border border-white/[0.05] bg-ink-850/45 p-3 transition hover:border-white/10 hover:bg-ink-800/80">
                            <button wire:click="openArtist('{{ $artist['id'] }}')" class="shrink-0" aria-label="Abrir {{ $artist['name'] }}">
                                <x-sonic.cover :cover="$artist['cover']" class="h-12 w-12 rounded-full" />
                            </button>

                            <button wire:click="openArtist('{{ $artist['id'] }}')" class="min-w-0 flex-1 text-left">
                                <span class="block truncate text-[13px] font-semibold text-zinc-100 transition group-hover:text-white">{{ $artist['name'] }}</span>
                                <span class="mt-0.5 block truncate text-[11px] text-zinc-500">{{ $artist['genre'] }} · {{ $artist['listeners'] }} ouvintes</span>
                            </button>

                            <button
                                wire:click="playArtist('{{ $artist['id'] }}')"
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-white/[0.08] bg-white/[0.03] text-zinc-400 transition hover:border-sonic-400/40 hover:bg-sonic-400 hover:text-ink-950"
                                aria-label="Tocar {{ $artist['name'] }}"
                            >
                                <x-icon name="play" :size="15" />
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</div>
