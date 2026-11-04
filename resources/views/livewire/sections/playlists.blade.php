@php
    $totalDuration = $catalog->formatDuration(array_sum(array_column($playlists, 'duration')));
@endphp

<div class="space-y-8">
    <section class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="font-mono text-[10px] uppercase tracking-[0.3em] text-sonic-400/80">Playlists</p>
            <h1 class="mt-2 font-display text-3xl font-bold tracking-[-0.035em] text-white sm:text-4xl lg:text-[42px]">
                Curadoria Sonic
            </h1>
            <p class="mt-3 max-w-xl text-sm leading-6 text-zinc-400">
                Seleções fictícias para foco, noite, estrada e descobertas. Tudo pronto para tocar em um clique.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center gap-2 rounded-full border border-white/[0.07] bg-white/[0.03] px-3.5 py-2 font-mono text-[10px] uppercase tracking-[0.18em] text-zinc-400">
                <x-icon name="list" :size="13" class="text-sonic-300" />
                {{ count($playlists) }} {{ count($playlists) === 1 ? 'playlist' : 'playlists' }}
            </span>
            <span class="inline-flex items-center gap-2 rounded-full border border-white/[0.07] bg-white/[0.03] px-3.5 py-2 font-mono text-[10px] uppercase tracking-[0.18em] text-zinc-400">
                <x-icon name="clock" :size="13" class="text-sonic-300" />
                {{ $totalDuration }}
            </span>
        </div>
    </section>

    @if ($playlists === [])
        <p class="rounded-2xl border border-white/[0.06] bg-ink-850/40 px-5 py-10 text-center text-sm text-zinc-400">
            Nenhuma playlist disponível no catálogo.
        </p>
    @else
        <section class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($playlists as $playlist)
                @include('livewire.partials.media-card', ['type' => 'playlist', 'item' => $playlist])
            @endforeach
        </section>
    @endif

    <section class="relative overflow-hidden rounded-[28px] border border-white/[0.06] bg-ink-850/45 p-6 sm:p-8">
        <div class="absolute -right-20 -top-24 h-64 w-64 rounded-full bg-sonic-400/10 blur-3xl"></div>
        <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="max-w-lg">
                <p class="font-mono text-[9px] uppercase tracking-[0.22em] text-sonic-300/80">Áudio real</p>
                <h2 class="mt-2 font-display text-2xl font-semibold tracking-[-0.025em] text-white">Uma seleção com licença aberta</h2>
                <p class="mt-2 text-sm leading-6 text-zinc-400">
                    Reproduza as quatro gravações de Péndulo Diapasón disponíveis na playlist Piano demo, distribuídas sob licença CC BY-SA 3.0.
                </p>
            </div>

            <button
                wire:click="navigateTo('explore')"
                class="inline-flex h-11 w-fit items-center gap-2 rounded-full border border-white/10 bg-white/[0.04] px-5 text-[13px] font-medium text-zinc-200 transition hover:border-sonic-400/30 hover:bg-sonic-400/[0.07] hover:text-white"
            >
                Descobrir mais
                <x-icon name="arrow-right" :size="15" />
            </button>
        </div>
    </section>
</div>
