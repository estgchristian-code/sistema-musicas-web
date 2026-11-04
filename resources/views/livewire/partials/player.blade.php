<div
    wire:ignore
    x-data="sonicPlayer({{ Illuminate\Support\Js::from($player) }})"
    x-on:sonic-play.window="playFromEvent($event.detail)"
    x-on:sonic-favorites-changed.window="setFavorites($event.detail.ids)"
    x-on:keydown.space.window="handleSpace($event)"
    class="fixed inset-x-0 bottom-0 z-40 border-t border-white/[0.08] bg-[#090909] shadow-[0_-12px_30px_-20px_rgba(0,0,0,.85)]"
    aria-label="Player de música"
>
    <div
        x-show="error"
        x-transition.opacity
        x-cloak
        class="absolute -top-12 left-1/2 z-20 -translate-x-1/2 rounded-full border border-red-400/20 bg-red-950/90 px-4 py-2 text-xs text-red-200 shadow-lg"
    >
        <span x-text="error"></span>
    </div>

    {{-- Desktop / tablet player --}}
    <div class="mx-auto hidden h-[90px] max-w-[1700px] grid-cols-[minmax(0,1fr)_minmax(320px,1.3fr)_minmax(0,1fr)] items-center gap-4 px-4 lg:grid xl:gap-6 xl:px-6">
        <div class="flex min-w-0 items-center gap-3">
            <div
                class="relative h-14 w-14 shrink-0 overflow-hidden rounded-lg shadow-lg shadow-black/30"
                :style="`background: linear-gradient(145deg, ${current?.cover?.from ?? '#22D97F'}, ${current?.cover?.to ?? '#0C0F0D'})`"
            >
                <div class="absolute -left-3 -top-3 h-12 w-12 rounded-full opacity-70 blur-xl" :style="`background: ${current?.cover?.accent ?? '#C9FF6B'}`"></div>
                <div class="cover-grain absolute inset-0"></div>
                <img x-show="current?.coverImage" :src="current?.coverImage" alt="" class="absolute inset-0 h-full w-full object-cover">
                <span x-show="!current?.coverImage" class="relative flex h-full items-center justify-center font-display text-xl font-bold text-white/90" x-text="current?.title?.charAt(0) ?? 'S'"></span>
                <div x-show="playing" class="absolute inset-x-1.5 bottom-1.5 flex h-2.5 items-end justify-center gap-[2px]">
                    <span class="equalizer-bar h-full w-[2px] rounded-full bg-white/80"></span>
                    <span class="equalizer-bar h-2/3 w-[2px] rounded-full bg-white/80"></span>
                    <span class="equalizer-bar h-4/5 w-[2px] rounded-full bg-white/80"></span>
                    <span class="equalizer-bar h-1/2 w-[2px] rounded-full bg-white/80"></span>
                </div>
            </div>

            <div class="min-w-0 flex-1">
                <p class="truncate text-[13px] font-semibold tracking-[-0.01em] text-white" x-text="current?.title ?? 'Selecione uma faixa'"></p>
                <p class="mt-0.5 truncate text-[11px] text-zinc-500">
                    <span x-text="current?.artist ?? 'Sonic'"></span>
                    <span class="mx-1 text-zinc-700">·</span>
                    <span x-text="current?.album ?? 'Demo'"></span>
                </p>
            </div>

            <button
                x-on:click="toggleFavorite(); $wire.toggleFavorite(current.id)"
                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full transition hover:bg-white/5"
                :class="isFavorite ? 'text-sonic-400' : 'text-zinc-600 hover:text-white'"
                :aria-pressed="isFavorite"
                :aria-label="isFavorite ? 'Remover das músicas curtidas' : 'Adicionar às músicas curtidas'"
            >
                <x-icon x-show="isFavorite" name="heart" :size="17" filled />
                <x-icon x-show="!isFavorite" name="heart" :size="17" />
            </button>
        </div>

        <div class="flex min-w-0 flex-col items-center">
            <div class="flex items-center gap-3">
                <button
                    x-on:click="toggleShuffle()"
                    class="hidden h-7 w-7 items-center justify-center rounded-full transition hover:text-white sm:flex"
                    :class="shuffle ? 'text-sonic-400' : 'text-zinc-600'"
                    :aria-pressed="shuffle"
                    aria-label="Misturar faixas"
                    title="Misturar faixas"
                >
                    <x-icon name="shuffle" :size="15" />
                </button>

                <button x-on:click="previous()" class="flex h-8 w-8 items-center justify-center rounded-full text-zinc-400 transition hover:bg-white/5 hover:text-white" aria-label="Faixa anterior" title="Faixa anterior">
                    <x-icon name="skip-back" :size="18" />
                </button>

                <button
                    x-on:click="toggle()"
                    :disabled="loading"
                    class="relative flex h-11 w-11 items-center justify-center rounded-full bg-sonic-400 text-ink-950 shadow-glow transition hover:scale-105 hover:bg-sonic-300 disabled:opacity-70"
                    :aria-label="playing ? 'Pausar' : 'Reproduzir'"
                >
                    <svg x-show="loading" x-cloak class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" stroke-dasharray="30 16" stroke-linecap="round" />
                    </svg>
                    <x-icon x-show="!loading && !playing" name="play" :size="20" />
                    <x-icon x-show="!loading && playing" name="pause" :size="20" />
                </button>

                <button x-on:click="next()" class="flex h-8 w-8 items-center justify-center rounded-full text-zinc-400 transition hover:bg-white/5 hover:text-white" aria-label="Próxima faixa" title="Próxima faixa">
                    <x-icon name="skip-forward" :size="18" />
                </button>

                <button
                    x-on:click="cycleRepeat()"
                    class="hidden h-7 w-7 items-center justify-center rounded-full transition hover:text-white sm:flex"
                    :class="repeat !== 'off' ? 'text-sonic-400' : 'text-zinc-600'"
                    :aria-label="repeat === 'one' ? 'Repetir uma faixa' : repeat === 'all' ? 'Repetir fila' : 'Ativar repetição'"
                    :title="repeat === 'one' ? 'Repetindo uma faixa' : repeat === 'all' ? 'Repetindo fila' : 'Ativar repetição'"
                >
                    <x-icon x-show="repeat !== 'one'" name="repeat" :size="15" />
                    <x-icon x-show="repeat === 'one'" name="repeat-1" :size="15" />
                </button>
            </div>

            <div class="mt-2.5 flex w-full items-center gap-2.5">
                <span class="w-8 text-right font-mono text-[10px] tabular-nums text-zinc-600" x-text="formatTime(position)">0:00</span>
                <input
                    type="range"
                    min="0"
                    step="1"
                    class="sonic-range min-w-0 flex-1"
                    :max="duration"
                    :value="position"
                    :style="progressStyle"
                    :disabled="!current?.audioSource"
                    :aria-valuetext="`${formatTime(position)} de ${formatTime(duration)}`"
                    aria-label="Progresso da faixa"
                    x-on:pointerdown="beginSeek()"
                    x-on:input="previewSeek($event.target.value)"
                    x-on:change="commitSeek($event.target.value)"
                >
                <span class="w-8 font-mono text-[10px] tabular-nums text-zinc-600" x-text="current?.audioSource ? formatTime(duration) : '—:—'">0:00</span>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <span x-show="current?.sourceUrl" class="hidden items-center gap-1.5 text-[10px] text-zinc-500 2xl:flex">
                <a x-bind:href="current?.sourceUrl" target="_blank" rel="noopener noreferrer" class="max-w-36 truncate transition hover:text-white" x-text="current?.attribution"></a>
                <span>·</span>
                <a x-bind:href="current?.licenseUrl" target="_blank" rel="noopener noreferrer" class="transition hover:text-white" x-text="current?.license"></a>
                <span x-show="current?.coverAttribution">·</span>
                <a x-show="current?.coverAttribution" x-bind:href="current?.coverSourceUrl" target="_blank" rel="noopener noreferrer" class="max-w-28 truncate transition hover:text-white" x-text="`Capa: ${current?.coverAttribution}`"></a>
            </span>

            <button
                x-on:click="toggleMute()"
                class="flex h-8 w-8 items-center justify-center rounded-full text-zinc-500 transition hover:text-white"
                :aria-label="muted ? 'Ativar som' : 'Silenciar'"
                :title="muted ? 'Ativar som' : 'Silenciar'"
            >
                <x-icon x-show="!muted && volume > 0" name="volume" :size="17" />
                <x-icon x-show="muted || volume === 0" name="volume-x" :size="17" />
            </button>

            <input
                type="range"
                min="0"
                max="100"
                step="1"
                class="sonic-range w-20 xl:w-24"
                :value="volumePercent"
                :style="volumeStyle"
                aria-label="Volume"
                x-on:input="setVolume($event.target.value / 100)"
            >
        </div>
    </div>

    {{-- Mobile mini player --}}
    <div class="lg:hidden">
        <div class="relative h-[76px] px-4">
            <div class="absolute inset-x-0 top-0 h-px bg-white/10">
                <div class="h-full bg-sonic-400 transition-[width] duration-300" :style="`width: ${progressPercent}%`"></div>
            </div>

            <div class="flex h-full items-center gap-3">
                <button x-on:click="toggleExpanded()" class="flex min-w-0 flex-1 items-center gap-3 text-left" aria-label="Abrir player em tela cheia">
                    <div
                        class="relative h-11 w-11 shrink-0 overflow-hidden rounded-lg"
                        :style="`background: linear-gradient(145deg, ${current?.cover?.from ?? '#22D97F'}, ${current?.cover?.to ?? '#0C0F0D'})`"
                    >
                        <div class="absolute -left-2 -top-2 h-8 w-8 rounded-full opacity-70 blur-lg" :style="`background: ${current?.cover?.accent ?? '#C9FF6B'}`"></div>
                        <img x-show="current?.coverImage" :src="current?.coverImage" alt="" class="absolute inset-0 h-full w-full object-cover">
                        <span x-show="!current?.coverImage" class="relative flex h-full items-center justify-center font-display text-base font-bold text-white/90" x-text="current?.title?.charAt(0) ?? 'S'"></span>
                    </div>
                    <span class="min-w-0">
                        <span class="block truncate text-[13px] font-semibold text-white" x-text="current?.title ?? 'Selecione uma faixa'"></span>
                        <span class="mt-0.5 block truncate text-[11px] text-zinc-500" x-text="current?.artist ?? 'Sonic'"></span>
                    </span>
                </button>

                <button
                    x-on:click="toggle()"
                    :disabled="loading"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-sonic-400 text-ink-950 shadow-glow disabled:opacity-70"
                    :aria-label="playing ? 'Pausar' : 'Reproduzir'"
                >
                    <svg x-show="loading" x-cloak class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" stroke-dasharray="30 16" stroke-linecap="round" />
                    </svg>
                    <x-icon x-show="!loading && !playing" name="play" :size="18" />
                    <x-icon x-show="!loading && playing" name="pause" :size="18" />
                </button>

                <button x-on:click="toggleExpanded()" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-zinc-500" aria-label="Expandir player">
                    <x-icon name="chevron-up" :size="18" />
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile full-screen player --}}
    <div
        x-show="expanded"
        x-cloak
        x-transition.opacity.duration.200ms
        class="fixed inset-0 z-50 flex flex-col overflow-y-auto bg-ink-950 lg:hidden"
        :style="`background-image: radial-gradient(circle at 25% 0%, ${current?.cover?.accent ?? '#4DF29B'}30, transparent 42%), linear-gradient(180deg, ${current?.cover?.from ?? '#0C100D'}26, #070a08 58%)`"
        role="dialog"
        aria-modal="true"
        aria-label="Player em tela cheia"
    >
        <div class="flex items-center justify-between px-5 pb-2 pt-[max(1.25rem,env(safe-area-inset-top))]">
            <button x-on:click="closeExpanded()" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/5 text-zinc-300 backdrop-blur" aria-label="Fechar player">
                <x-icon name="chevron-down" :size="20" />
            </button>
            <div class="text-center">
                <p class="font-mono text-[9px] uppercase tracking-[0.24em] text-zinc-500">Tocando agora</p>
                <p class="mt-0.5 text-[11px] text-zinc-400" x-text="current?.album ?? 'Sonic demo'"></p>
            </div>
            <button
                x-on:click="toggleFavorite(); $wire.toggleFavorite(current.id)"
                class="flex h-10 w-10 items-center justify-center rounded-full bg-white/5 backdrop-blur transition"
                :class="isFavorite ? 'text-sonic-400' : 'text-zinc-400'"
                :aria-pressed="isFavorite"
                aria-label="Favoritar faixa"
            >
                <x-icon x-show="isFavorite" name="heart" :size="18" filled />
                <x-icon x-show="!isFavorite" name="heart" :size="18" />
            </button>
        </div>

        <div class="flex flex-1 flex-col justify-center px-7 py-6">
            <div class="relative mx-auto aspect-square w-full max-w-[340px] overflow-hidden rounded-[2rem] shadow-[0_30px_80px_-20px_rgba(0,0,0,.9)]" :style="`background: linear-gradient(145deg, ${current?.cover?.from ?? '#22D97F'}, ${current?.cover?.to ?? '#0C0F0D'})`">
                <img x-show="current?.coverImage" :src="current?.coverImage" alt="" class="absolute inset-0 h-full w-full object-cover">
                <div class="absolute -left-10 -top-10 h-40 w-40 rounded-full opacity-60 blur-3xl" :style="`background: ${current?.cover?.accent ?? '#C9FF6B'}`"></div>
                <div class="absolute -bottom-12 -right-8 h-44 w-44 rounded-full opacity-40 blur-3xl" :style="`background: ${current?.cover?.from ?? '#22D97F'}`"></div>
                <div class="cover-grain absolute inset-0"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span x-show="!current?.coverImage" class="font-display text-[92px] font-bold leading-none text-white/90" x-text="current?.title?.charAt(0) ?? 'S'"></span>
                    <span x-show="!current?.coverImage" class="mt-3 max-w-[80%] text-center font-mono text-[10px] uppercase tracking-[0.28em] text-white/55" x-text="current?.artist ?? 'Sonic'"></span>
                </div>
                <div x-show="playing" class="absolute inset-x-10 bottom-8 flex h-8 items-end justify-center gap-1.5">
                    <span class="equalizer-bar h-full w-1 rounded-full bg-white/75"></span>
                    <span class="equalizer-bar h-3/4 w-1 rounded-full bg-white/75"></span>
                    <span class="equalizer-bar h-5/6 w-1 rounded-full bg-white/75"></span>
                    <span class="equalizer-bar h-2/3 w-1 rounded-full bg-white/75"></span>
                    <span class="equalizer-bar h-full w-1 rounded-full bg-white/75"></span>
                </div>
            </div>

            <div class="mt-8 flex items-start gap-3">
                <div class="min-w-0 flex-1">
                    <h2 class="truncate font-display text-2xl font-bold tracking-[-0.02em] text-white" x-text="current?.title ?? 'Selecione uma faixa'"></h2>
                    <p class="mt-1 truncate text-sm text-zinc-400">
                        <span x-text="current?.artist ?? 'Sonic'"></span>
                        <span class="mx-1 text-zinc-600">·</span>
                        <span x-text="current?.album ?? 'Demo'"></span>
                    </p>
                </div>
                <span x-show="current?.sourceUrl" class="mt-1 flex flex-col items-end gap-1 text-right text-[9px] text-zinc-500">
                    <a x-bind:href="current?.sourceUrl" target="_blank" rel="noopener noreferrer" class="transition hover:text-white" x-text="current?.attribution"></a>
                    <a x-bind:href="current?.licenseUrl" target="_blank" rel="noopener noreferrer" class="transition hover:text-white" x-text="current?.license"></a>
                    <a x-show="current?.coverAttribution" x-bind:href="current?.coverSourceUrl" target="_blank" rel="noopener noreferrer" class="transition hover:text-white" x-text="`Capa: ${current?.coverAttribution}`"></a>
                </span>
                <span x-show="!current?.audioSource" class="mt-1 text-[9px] text-zinc-500">Sem arquivo de áudio</span>
            </div>

            <div class="mt-7">
                <input
                    type="range"
                    min="0"
                    step="1"
                    class="sonic-range w-full"
                    :max="duration"
                    :value="position"
                    :style="progressStyle"
                    :disabled="!current?.audioSource"
                    :aria-valuetext="`${formatTime(position)} de ${formatTime(duration)}`"
                    aria-label="Progresso da faixa"
                    x-on:pointerdown="beginSeek()"
                    x-on:input="previewSeek($event.target.value)"
                    x-on:change="commitSeek($event.target.value)"
                >
                <div class="mt-2 flex justify-between font-mono text-[10px] tabular-nums text-zinc-500">
                    <span x-text="formatTime(position)">0:00</span>
                    <span x-text="formatTime(duration)">0:00</span>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between">
                <button
                    x-on:click="toggleShuffle()"
                    class="flex h-11 w-11 items-center justify-center rounded-full transition"
                    :class="shuffle ? 'bg-sonic-400/10 text-sonic-400' : 'text-zinc-500'"
                    :aria-pressed="shuffle"
                    aria-label="Misturar faixas"
                >
                    <x-icon name="shuffle" :size="19" />
                </button>

                <button x-on:click="previous()" class="flex h-12 w-12 items-center justify-center rounded-full text-zinc-200 transition hover:bg-white/5" aria-label="Faixa anterior">
                    <x-icon name="skip-back" :size="26" />
                </button>

                <button
                    x-on:click="toggle()"
                    :disabled="loading"
                    class="flex h-16 w-16 items-center justify-center rounded-full bg-sonic-400 text-ink-950 shadow-glow transition active:scale-95 disabled:opacity-70"
                    :aria-label="playing ? 'Pausar' : 'Reproduzir'"
                >
                    <svg x-show="loading" x-cloak class="h-6 w-6 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" stroke-dasharray="30 16" stroke-linecap="round" />
                    </svg>
                    <x-icon x-show="!loading && !playing" name="play" :size="28" />
                    <x-icon x-show="!loading && playing" name="pause" :size="28" />
                </button>

                <button x-on:click="next()" class="flex h-12 w-12 items-center justify-center rounded-full text-zinc-200 transition hover:bg-white/5" aria-label="Próxima faixa">
                    <x-icon name="skip-forward" :size="26" />
                </button>

                <button
                    x-on:click="cycleRepeat()"
                    class="flex h-11 w-11 items-center justify-center rounded-full transition"
                    :class="repeat !== 'off' ? 'bg-sonic-400/10 text-sonic-400' : 'text-zinc-500'"
                    :aria-label="repeat === 'one' ? 'Repetir uma faixa' : repeat === 'all' ? 'Repetir fila' : 'Ativar repetição'"
                >
                    <x-icon x-show="repeat !== 'one'" name="repeat" :size="19" />
                    <x-icon x-show="repeat === 'one'" name="repeat-1" :size="19" />
                </button>
            </div>

            <div class="mt-7 flex items-center gap-3 rounded-2xl border border-white/[0.06] bg-white/[0.03] px-4 py-3">
                <button x-on:click="toggleMute()" class="text-zinc-500" :aria-label="muted ? 'Ativar som' : 'Silenciar'">
                    <x-icon x-show="!muted && volume > 0" name="volume" :size="17" />
                    <x-icon x-show="muted || volume === 0" name="volume-x" :size="17" />
                </button>
                <input
                    type="range"
                    min="0"
                    max="100"
                    step="1"
                    class="sonic-range flex-1"
                    :value="volumePercent"
                    :style="volumeStyle"
                    aria-label="Volume"
                    x-on:input="setVolume($event.target.value / 100)"
                >
                <span class="w-8 text-right font-mono text-[10px] tabular-nums text-zinc-500" x-text="`${volumePercent}%`"></span>
            </div>

            <p class="mt-6 text-center font-mono text-[9px] uppercase tracking-[0.2em] text-zinc-600">
                Áudio gerado no navegador · sem direitos autorais
            </p>
        </div>
    </div>
</div>
