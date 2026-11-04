<div
    x-data="{ navOpen: false }"
    x-on:keydown.escape.window="navOpen = false"
    x-on:sonic-navigated.window="window.scrollTo({ top: 0, behavior: 'smooth' })"
    class="relative min-h-screen bg-black pb-[76px] lg:pb-[100px]"
>
    <div aria-hidden="true" class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute inset-0 bg-black"></div>
    </div>

    <aside class="fixed inset-y-2 left-2 z-30 hidden w-[280px] overflow-hidden rounded-lg bg-[#121212] lg:block">
        @include('livewire.partials.sidebar', [
            'playlists' => $playlists,
            'favoriteTracks' => $favoriteTracks,
            'section' => $section,
        ])
    </aside>

    {{-- Mobile navigation drawer --}}
    <div x-show="navOpen" x-cloak class="fixed inset-0 z-50 lg:hidden">
        <div
            x-show="navOpen"
            x-transition.opacity.duration.200ms
            x-on:click="navOpen = false"
            class="absolute inset-0 bg-black/70 backdrop-blur-sm"
        ></div>

        <div
            x-show="navOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="absolute inset-y-0 left-0 w-[86%] max-w-[324px] rounded-r-lg bg-[#121212] shadow-[30px_0_80px_-20px_rgba(0,0,0,.9)]"
        >
            @include('livewire.partials.sidebar', [
                'playlists' => $playlists,
                'favoriteTracks' => $favoriteTracks,
                'section' => $section,
                'mobile' => true,
            ])
        </div>
    </div>

    <div class="lg:pl-[296px] lg:pr-2 lg:pt-2">
        {{-- Sticky header --}}
        <header class="sticky top-0 z-30 rounded-t-lg border-b border-white/[0.04] bg-[#121212]/95 backdrop-blur-2xl">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-2.5 px-4 py-3 sm:px-6 lg:h-[68px] lg:flex-nowrap lg:px-8">
                <button
                    x-on:click="navOpen = true"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-zinc-300 transition hover:bg-white/[0.06] hover:text-white lg:hidden"
                    aria-label="Abrir menu de navegação"
                >
                    <x-icon name="menu" :size="20" />
                </button>

                <button wire:click="navigateTo('home')" class="flex items-center gap-2 lg:hidden" aria-label="Ir para o início">
                    <span class="relative flex h-8 w-8 items-center justify-center overflow-hidden rounded-xl bg-sonic-400 text-ink-950 shadow-glow">
                        <span class="flex items-end gap-[2px]">
                            <span class="h-1.5 w-[2px] rounded-full bg-ink-950"></span>
                            <span class="h-3 w-[2px] rounded-full bg-ink-950"></span>
                            <span class="h-2 w-[2px] rounded-full bg-ink-950"></span>
                            <span class="h-3.5 w-[2px] rounded-full bg-ink-950"></span>
                        </span>
                    </span>
                    <span class="font-display text-sm font-bold tracking-[0.18em] text-white">SONIC</span>
                </button>

                <div class="hidden items-center gap-1 lg:flex">
                    <button
                        wire:click="goBack"
                        @if ($historyIndex <= 0) disabled @endif
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-black/50 text-zinc-300 transition hover:text-white disabled:opacity-30 disabled:hover:text-zinc-300"
                        aria-label="Voltar"
                        title="Voltar"
                    >
                        <x-icon name="chevron-left" :size="18" />
                    </button>
                    <button
                        wire:click="goForward"
                        @if ($historyIndex >= count($history) - 1) disabled @endif
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-black/50 text-zinc-300 transition hover:text-white disabled:opacity-30 disabled:hover:text-zinc-300"
                        aria-label="Avançar"
                        title="Avançar"
                    >
                        <x-icon name="chevron-right" :size="18" />
                    </button>
                </div>

                <div class="order-last w-full lg:order-none lg:max-w-2xl lg:flex-1">
                    <div class="group relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-zinc-500 transition group-focus-within:text-sonic-300">
                            <x-icon name="search" :size="17" />
                        </span>
                        <input
                            type="search"
                            wire:model.live.debounce.350ms="search"
                            wire:keydown.escape="clearSearch"
                            placeholder="Buscar músicas, álbuns, artistas..."
                            autocomplete="off"
                            class="h-10 w-full rounded-full border border-transparent bg-[#242424] pl-10 pr-10 text-sm text-zinc-100 placeholder:text-zinc-400 transition hover:bg-[#2a2a2a] focus:border-white/30 focus:bg-[#242424] focus:outline-none focus:ring-2 focus:ring-white/10 [&::-webkit-search-cancel-button]:hidden"
                            aria-label="Buscar no catálogo Sonic"
                        >
                        @if ($search !== '')
                            <button
                                wire:click="clearSearch"
                                class="absolute inset-y-0 right-0 flex w-10 items-center justify-center text-zinc-500 transition hover:text-white"
                                aria-label="Limpar busca"
                            >
                                <x-icon name="x" :size="15" />
                            </button>
                        @endif
                    </div>
                </div>

                <div class="ml-auto flex items-center gap-2.5 lg:ml-0">
                    <span class="hidden items-center gap-1.5 rounded-full border border-white/[0.07] px-3 py-1.5 font-mono text-[9px] uppercase tracking-[0.18em] text-zinc-500 xl:flex">
                        <span class="h-1.5 w-1.5 rounded-full bg-sonic-400"></span>
                        Demo generativa
                    </span>

                    <span
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-sonic-300 via-sonic-400 to-sonic-700 font-display text-[13px] font-bold text-ink-950 shadow-glow"
                        title="Ouvinte Sonic"
                        aria-label="Perfil de demonstração"
                    >
                        S
                    </span>
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-[1560px] rounded-b-lg bg-[#121212] px-4 pb-32 pt-6 sm:px-6 sm:pt-8 lg:px-8 lg:pb-[148px] lg:pt-8">
            <div wire:key="section-{{ $section }}-{{ $playlistId }}-{{ $albumId }}-{{ $artistId }}">
                @switch($section)
                    @case('explore')
                        @include('livewire.sections.explore')
                        @break

                    @case('library')
                        @include('livewire.sections.library')
                        @break

                    @case('playlists')
                        @include('livewire.sections.playlists')
                        @break

                    @case('playlist')
                        @if ($selectedPlaylist !== null)
                            @include('livewire.sections.playlist-detail')
                        @else
                            @include('livewire.sections.home')
                        @endif
                        @break

                    @case('album')
                        @if ($selectedAlbum !== null)
                            @include('livewire.sections.album-detail')
                        @else
                            @include('livewire.sections.home')
                        @endif
                        @break

                    @case('artist')
                        @if ($selectedArtist !== null)
                            @include('livewire.sections.artist-detail')
                        @else
                            @include('livewire.sections.home')
                        @endif
                        @break

                    @case('search')
                        @include('livewire.sections.search')
                        @break

                    @default
                        @include('livewire.sections.home')
                @endswitch
            </div>
        </main>
    </div>

    @include('livewire.partials.player', ['player' => $player])
</div>
