<?php

namespace App\Livewire;

use App\Support\SonicCatalog;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Sonic extends Component
{
    public string $section = 'home';

    public ?string $playlistId = null;

    public ?string $albumId = null;

    public ?string $artistId = null;

    public string $search = '';

    public string $searchTab = 'all';

    public ?string $genre = null;

    public string $libraryTab = 'liked';

    /** @var array<int, string> */
    public array $favoriteTrackIds = [];

    public ?string $currentTrackId = null;

    /** @var array<int, array<string, mixed>> */
    public array $history = [];

    public int $historyIndex = 0;

    private SonicCatalog $catalog;

    public function boot(SonicCatalog $catalog): void
    {
        $this->catalog = $catalog;
    }

    public function mount(): void
    {
        $trackIds = array_keys($this->catalog->tracks());

        $this->favoriteTrackIds = array_values(array_intersect(
            session('sonic.favorites', []),
            $trackIds,
        ));

        $this->currentTrackId = $trackIds[0] ?? null;

        foreach ($this->catalog->tracks() as $track) {
            if (isset($track['audio']['file'])) {
                $this->currentTrackId = $track['id'];
                break;
            }
        }

        $this->resetHistory();
    }

    public function navigateTo(string $section): void
    {
        if (! in_array($section, ['home', 'explore', 'library', 'playlists'], true)) {
            return;
        }

        $this->section = $section;
        $this->search = '';
        $this->clearContext();
        $this->pushHistory();
    }

    public function openPlaylist(string $playlistId): void
    {
        if ($this->catalog->playlist($playlistId) === null) {
            return;
        }

        $this->section = 'playlist';
        $this->playlistId = $playlistId;
        $this->search = '';
        $this->clearContext(except: 'playlistId');
        $this->pushHistory();
    }

    public function openAlbum(string $albumId): void
    {
        if ($this->catalog->album($albumId) === null) {
            return;
        }

        $this->section = 'album';
        $this->albumId = $albumId;
        $this->search = '';
        $this->clearContext(except: 'albumId');
        $this->pushHistory();
    }

    public function openArtist(string $artistId): void
    {
        if ($this->catalog->artist($artistId) === null) {
            return;
        }

        $this->section = 'artist';
        $this->artistId = $artistId;
        $this->search = '';
        $this->clearContext(except: 'artistId');
        $this->pushHistory();
    }

    public function openFavorites(): void
    {
        $this->section = 'library';
        $this->libraryTab = 'liked';
        $this->search = '';
        $this->clearContext();
        $this->pushHistory();
    }

    public function updatedSearch(): void
    {
        $term = trim($this->search);

        if ($term === '') {
            if ($this->section === 'search') {
                $this->section = 'home';
                $this->clearContext();
            }

            return;
        }

        $this->searchTab = 'all';

        if ($this->section !== 'search') {
            $this->section = 'search';
            $this->clearContext();
            $this->dispatch('sonic-navigated');
        }
    }

    public function clearSearch(): void
    {
        $this->search = '';

        if ($this->section === 'search') {
            $this->section = 'home';
            $this->clearContext();
        }
    }

    public function setSearchTab(string $tab): void
    {
        if (in_array($tab, ['all', 'tracks', 'albums', 'artists', 'playlists'], true)) {
            $this->searchTab = $tab;
        }
    }

    public function setLibraryTab(string $tab): void
    {
        if (in_array($tab, ['liked', 'playlists', 'albums', 'artists'], true)) {
            $this->libraryTab = $tab;
        }
    }

    public function setGenre(?string $genre): void
    {
        if ($genre !== null && ! in_array($genre, $this->catalog->genres(), true)) {
            return;
        }

        $this->section = 'explore';
        $this->genre = $genre;
        $this->clearContext();
        $this->pushHistory();
    }

    public function playTrack(string $trackId, string $queueType = 'all', ?string $queueId = null): void
    {
        $track = $this->catalog->track($trackId);

        if ($track === null) {
            return;
        }

        $this->currentTrackId = $trackId;

        $this->dispatch(
            'sonic-play',
            trackId: $trackId,
            queue: $this->queueFor($queueType, $queueId),
        );
    }

    public function playPlaylist(string $playlistId): void
    {
        $playlist = $this->catalog->playlist($playlistId);

        if ($playlist === null || $playlist['tracks'] === []) {
            return;
        }

        $this->startQueue($playlist['tracks']);
    }

    public function playAlbum(string $albumId): void
    {
        $tracks = $this->catalog->tracksForAlbum($albumId);

        if ($tracks === []) {
            return;
        }

        $this->startQueue($tracks);
    }

    public function playArtist(string $artistId): void
    {
        $tracks = $this->catalog->tracksForArtist($artistId);

        if ($tracks === []) {
            return;
        }

        $this->startQueue($tracks);
    }

    public function playFavorites(): void
    {
        $tracks = $this->catalog->tracksForIds($this->favoriteTrackIds);

        if ($tracks === []) {
            return;
        }

        $this->startQueue($tracks);
    }

    public function playSearch(): void
    {
        $tracks = $this->catalog->search($this->search)['tracks'];

        if ($tracks === []) {
            return;
        }

        $this->startQueue($tracks);
    }

    public function toggleFavorite(string $trackId): void
    {
        if ($this->catalog->track($trackId) === null) {
            return;
        }

        if (in_array($trackId, $this->favoriteTrackIds, true)) {
            $this->favoriteTrackIds = array_values(array_diff($this->favoriteTrackIds, [$trackId]));
        } else {
            $this->favoriteTrackIds[] = $trackId;
        }

        session()->put('sonic.favorites', $this->favoriteTrackIds);

        $this->dispatch('sonic-favorites-changed', ids: $this->favoriteTrackIds);
    }

    public function goBack(): void
    {
        if ($this->historyIndex <= 0) {
            return;
        }

        $this->historyIndex--;
        $this->applyState($this->history[$this->historyIndex]);
    }

    public function goForward(): void
    {
        if ($this->historyIndex >= count($this->history) - 1) {
            return;
        }

        $this->historyIndex++;
        $this->applyState($this->history[$this->historyIndex]);
    }

    public function render(): View
    {
        $catalog = $this->catalog;
        $tracks = $catalog->tracks();

        $favoriteTracks = $catalog->tracksForIds($this->favoriteTrackIds);
        $searchResults = $catalog->search($this->search);

        $selectedPlaylist = $this->playlistId !== null ? $catalog->playlist($this->playlistId) : null;
        $selectedAlbum = $this->albumId !== null ? $catalog->album($this->albumId) : null;
        $selectedArtist = $this->artistId !== null ? $catalog->artist($this->artistId) : null;

        $recentTracks = $catalog->tracksForIds([
            'vidro-fume',
            'mare-alta',
            'depois-da-chuva',
            'sol-de-dentro',
            'ponto-cego',
            'circuito-verde',
        ]);

        $quickPicks = [
            [
                'type' => 'favorites',
                'title' => 'Músicas curtidas',
                'subtitle' => count($favoriteTracks) === 1 ? '1 faixa' : count($favoriteTracks).' faixas',
                'cover' => ['from' => '#263B2F', 'to' => '#0B1711', 'accent' => '#4DF29B', 'pattern' => 'pulse'],
            ],
            $this->quickPickFromPlaylist('foco-absoluto'),
            $this->quickPickFromPlaylist('madrugada-suave'),
            $this->quickPickFromAlbum('horizonte-de-vidro'),
            $this->quickPickFromArtist('luna-vale'),
            $this->quickPickFromPlaylist('descobrir-brasil'),
            $this->quickPickFromPlaylist('piano-demo-licenciado'),
        ];

        $explorePlaylists = $catalog->filteredPlaylists($this->genre);
        $exploreAlbums = $catalog->filteredAlbums($this->genre);
        $exploreArtists = $catalog->filteredArtists($this->genre);

        $playlistArtists = [];

        if ($selectedPlaylist !== null) {
            foreach ($selectedPlaylist['tracks'] as $track) {
                $playlistArtists[$track['artistId']] = $catalog->artist($track['artistId']);
            }
        }

        $relatedPlaylists = $this->playlistId !== null
            ? array_slice(array_diff_key($catalog->playlists(), [$this->playlistId => true]), 0, 3, true)
            : [];

        $artistPlaylists = [];

        if ($selectedArtist !== null) {
            foreach ($catalog->playlists() as $playlist) {
                foreach ($playlist['tracks'] as $track) {
                    if ($track['artistId'] === $selectedArtist['id']) {
                        $artistPlaylists[$playlist['id']] = $playlist;
                        break;
                    }
                }
            }
        }

        $moreFromArtist = $selectedAlbum !== null
            ? array_diff_key($catalog->albumsForArtist($selectedAlbum['artistId']), [$selectedAlbum['id'] => true])
            : [];

        return view('livewire.sonic', [
            'catalog' => $catalog,
            'tracks' => $tracks,
            'playlists' => $catalog->playlists(),
            'albums' => $catalog->albums(),
            'artists' => $catalog->artists(),
            'genres' => $catalog->genres(),
            'featuredPlaylist' => $catalog->playlist('foco-absoluto'),
            'quickPicks' => $quickPicks,
            'recentTracks' => $recentTracks,
            'favoriteTracks' => $favoriteTracks,
            'searchResults' => $searchResults,
            'selectedPlaylist' => $selectedPlaylist,
            'selectedAlbum' => $selectedAlbum,
            'selectedArtist' => $selectedArtist,
            'explorePlaylists' => $explorePlaylists,
            'exploreAlbums' => $exploreAlbums,
            'exploreArtists' => $exploreArtists,
            'playlistArtists' => $playlistArtists,
            'relatedPlaylists' => $relatedPlaylists,
            'artistPlaylists' => $artistPlaylists,
            'moreFromArtist' => $moreFromArtist,
            'player' => [
                'tracks' => $catalog->playerTracks(),
                'initialTrackId' => $this->currentTrackId ?? array_key_first($tracks),
                'initialQueue' => array_keys($tracks),
                'favorites' => $this->favoriteTrackIds,
                'volume' => 0.72,
            ],
        ]);
    }

    private function startQueue(array $tracks): void
    {
        $queue = array_keys($tracks);
        $firstTrackId = $queue[0] ?? null;

        if ($firstTrackId === null) {
            return;
        }

        $this->currentTrackId = $firstTrackId;

        $this->dispatch('sonic-play', trackId: $firstTrackId, queue: $queue);
    }

    /**
     * @return array<int, string>
     */
    private function queueFor(string $type, ?string $id): array
    {
        $tracks = match ($type) {
            'playlist' => $id !== null ? ($this->catalog->playlist($id)['tracks'] ?? []) : [],
            'album' => $id !== null ? $this->catalog->tracksForAlbum($id) : [],
            'artist' => $id !== null ? $this->catalog->tracksForArtist($id) : [],
            'favorites' => $this->catalog->tracksForIds($this->favoriteTrackIds),
            'search' => $this->catalog->search($this->search)['tracks'],
            default => $this->catalog->tracks(),
        };

        return array_keys($tracks);
    }

    /**
     * @return array<string, mixed>
     */
    private function quickPickFromPlaylist(string $playlistId): array
    {
        $playlist = $this->catalog->playlist($playlistId);

        return [
            'type' => 'playlist',
            'id' => $playlistId,
            'title' => $playlist['title'] ?? 'Playlist',
            'subtitle' => $playlist['tag'] ?? 'Playlist',
            'cover' => $playlist['cover'] ?? ['from' => '#22D97F', 'to' => '#0C0F0D', 'accent' => '#C9FF6B', 'pattern' => 'pulse'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function quickPickFromAlbum(string $albumId): array
    {
        $album = $this->catalog->album($albumId);

        return [
            'type' => 'album',
            'id' => $albumId,
            'title' => $album['title'] ?? 'Álbum',
            'subtitle' => $album['artistName'] ?? 'Sonic',
            'cover' => $album['cover'] ?? ['from' => '#22D97F', 'to' => '#0C0F0D', 'accent' => '#C9FF6B', 'pattern' => 'pulse'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function quickPickFromArtist(string $artistId): array
    {
        $artist = $this->catalog->artist($artistId);

        return [
            'type' => 'artist',
            'id' => $artistId,
            'title' => $artist['name'] ?? 'Artista',
            'subtitle' => $artist['genre'] ?? 'Artista',
            'cover' => $artist['cover'] ?? ['from' => '#22D97F', 'to' => '#0C0F0D', 'accent' => '#C9FF6B', 'pattern' => 'pulse'],
        ];
    }

    private function clearContext(?string $except = null): void
    {
        if ($except !== 'playlistId') {
            $this->playlistId = null;
        }

        if ($except !== 'albumId') {
            $this->albumId = null;
        }

        if ($except !== 'artistId') {
            $this->artistId = null;
        }
    }

    private function resetHistory(): void
    {
        $this->history = [$this->currentState()];
        $this->historyIndex = 0;
    }

    private function pushHistory(): void
    {
        $this->history = array_slice($this->history, 0, $this->historyIndex + 1);
        $this->history[] = $this->currentState();
        $this->historyIndex = count($this->history) - 1;
        $this->dispatch('sonic-navigated');
    }

    /**
     * @return array<string, mixed>
     */
    private function currentState(): array
    {
        return [
            'section' => $this->section,
            'playlistId' => $this->playlistId,
            'albumId' => $this->albumId,
            'artistId' => $this->artistId,
            'search' => $this->search,
            'searchTab' => $this->searchTab,
            'genre' => $this->genre,
            'libraryTab' => $this->libraryTab,
        ];
    }

    /**
     * @param  array<string, mixed>  $state
     */
    private function applyState(array $state): void
    {
        $this->section = $state['section'] ?? 'home';
        $this->playlistId = $state['playlistId'] ?? null;
        $this->albumId = $state['albumId'] ?? null;
        $this->artistId = $state['artistId'] ?? null;
        $this->search = $state['search'] ?? '';
        $this->searchTab = $state['searchTab'] ?? 'all';
        $this->genre = $state['genre'] ?? null;
        $this->libraryTab = $state['libraryTab'] ?? 'liked';
        $this->dispatch('sonic-navigated');
    }
}
