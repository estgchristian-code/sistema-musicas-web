@php
    $queueId = $queueId ?? null;
    $showAlbum = $showAlbum ?? true;
    $isFavorite = in_array($track['id'], $favoriteTrackIds, true);
    $playAction = "playTrack('{$track['id']}', '{$queueType}', ".Illuminate\Support\Js::from($queueId).')';
    $favoriteClasses = $isFavorite
        ? 'text-sonic-400'
        : 'text-zinc-600 opacity-100 hover:text-zinc-200 sm:opacity-0 sm:group-hover:opacity-100 sm:focus-visible:opacity-100';
@endphp

<div
    wire:key="track-{{ $queueType }}-{{ $queueId ?? 'all' }}-{{ $track['id'] }}"
    class="group grid grid-cols-[auto_minmax(0,1fr)_auto] items-center gap-3 rounded-xl px-2 py-2 transition hover:bg-white/[0.055]"
>
    <div class="relative h-11 w-11 shrink-0 overflow-hidden rounded-lg">
        <x-sonic.cover :cover="$track['cover']" :image="$track['coverImage'] ?? null" class="h-full w-full" />
        <button
            wire:click="{{ $playAction }}"
            class="absolute inset-0 flex items-center justify-center bg-black/55 text-white opacity-0 backdrop-blur-[1px] transition group-hover:opacity-100 focus-visible:opacity-100"
            aria-label="Tocar {{ $track['title'] }}"
        >
            <x-icon name="play" :size="16" />
        </button>
    </div>

    <div class="min-w-0">
        <button wire:click="{{ $playAction }}" class="block max-w-full truncate text-left text-[13px] font-medium text-zinc-100 transition hover:text-white hover:underline">
            {{ $track['title'] }}
        </button>
        <p class="mt-0.5 flex min-w-0 items-center truncate text-[11px] text-zinc-500">
            <button wire:click="openArtist('{{ $track['artistId'] }}')" class="truncate transition hover:text-zinc-200 hover:underline">{{ $track['artist'] }}</button>
            @if ($showAlbum)
                <span class="mx-1.5 text-zinc-700">•</span>
                <button wire:click="openAlbum('{{ $track['albumId'] }}')" class="truncate transition hover:text-zinc-200 hover:underline">{{ $track['album'] }}</button>
            @endif
        </p>
    </div>

    <div class="flex items-center gap-2 sm:gap-3">
        <button
            wire:click="toggleFavorite('{{ $track['id'] }}')"
            class="flex h-8 w-8 items-center justify-center rounded-full transition {{ $favoriteClasses }}"
            aria-label="{{ $isFavorite ? 'Remover' : 'Adicionar' }} {{ $track['title'] }} {{ $isFavorite ? 'das' : 'às' }} músicas curtidas"
            aria-pressed="{{ $isFavorite ? 'true' : 'false' }}"
        >
            <x-icon name="heart" :size="16" :filled="$isFavorite" />
        </button>

        <span class="hidden w-10 text-right font-mono text-[11px] text-zinc-600 sm:block">{{ $track['durationLabel'] }}</span>
    </div>
</div>
