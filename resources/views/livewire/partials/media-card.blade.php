@php
    $isArtist = $type === 'artist';
    $itemId = $item['id'];
    $title = $isArtist ? $item['name'] : $item['title'];

    $openAction = match ($type) {
        'playlist' => "openPlaylist('{$itemId}')",
        'album' => "openAlbum('{$itemId}')",
        'artist' => "openArtist('{$itemId}')",
    };

    $playAction = match ($type) {
        'playlist' => "playPlaylist('{$itemId}')",
        'album' => "playAlbum('{$itemId}')",
        'artist' => "playArtist('{$itemId}')",
    };

    $subtitle = match ($type) {
        'playlist' => $item['tag'].' · '.$item['trackCount'].($item['trackCount'] === 1 ? ' faixa' : ' faixas'),
        'album' => $item['artistName'].' · '.$item['year'],
        'artist' => $item['genre'],
    };
@endphp

<article class="group relative flex min-w-0 flex-col rounded-lg p-2.5 transition duration-300 hover:bg-white/[0.06] sm:p-3">
    <div class="relative mb-3 aspect-square overflow-hidden {{ $isArtist ? 'rounded-full' : 'rounded-md' }} shadow-lg shadow-black/20">
        <x-sonic.cover
            :cover="$item['cover']"
            :image="$item['coverImage'] ?? null"
            class="h-full w-full transition duration-500 group-hover:scale-[1.045] {{ $isArtist ? 'rounded-full' : 'rounded-md' }}"
        />

        <button
            wire:click="{{ $playAction }}"
            class="absolute bottom-2 right-2 flex h-11 w-11 items-center justify-center rounded-full bg-sonic-400 text-ink-950 opacity-100 shadow-xl shadow-black/40 transition duration-200 sm:translate-y-2 sm:opacity-0 sm:group-hover:translate-y-0 sm:group-hover:opacity-100 sm:focus-visible:translate-y-0 sm:focus-visible:opacity-100"
            aria-label="Tocar {{ $title }}"
            title="Tocar {{ $title }}"
        >
            <x-icon name="play" :size="18" />
        </button>
    </div>

    <button wire:click="{{ $openAction }}" class="block w-full text-left">
        <h3 class="truncate text-sm font-semibold text-white">{{ $title }}</h3>
        <p class="mt-1 line-clamp-2 text-xs leading-5 text-zinc-400">{{ $subtitle }}</p>
    </button>
</article>
