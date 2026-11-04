<?php

namespace App\Support;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Str;

/**
 * Read-only catalog for Sonic's fictional demo content.
 *
 * The catalog decorates the raw configuration data with the relationships
 * needed by the interface, keeping Blade views free of lookup logic.
 */
final class SonicCatalog
{
    /** @var array<string, array<string, mixed>> */
    private array $artists;

    /** @var array<string, array<string, mixed>> */
    private array $albums;

    /** @var array<string, array<string, mixed>> */
    private array $tracks;

    /** @var array<string, array<string, mixed>> */
    private array $playlists;

    /** @var array<int, string> */
    private array $genres;

    public function __construct(Repository $config)
    {
        $this->artists = $config->get('sonic.artists', []);
        $this->albums = $config->get('sonic.albums', []);
        $this->tracks = $config->get('sonic.tracks', []);
        $this->playlists = $config->get('sonic.playlists', []);
        $this->genres = $config->get('sonic.genres', []);

        $this->decorateAlbums();
        $this->decorateTracks();
        $this->decoratePlaylists();
        $this->decorateArtists();
    }

    /** @return array<string, array<string, mixed>> */
    public function artists(): array
    {
        return $this->artists;
    }

    /** @return array<string, array<string, mixed>> */
    public function albums(): array
    {
        return $this->albums;
    }

    /** @return array<string, array<string, mixed>> */
    public function tracks(): array
    {
        return $this->tracks;
    }

    /** @return array<string, array<string, mixed>> */
    public function playlists(): array
    {
        return $this->playlists;
    }

    /** @return array<int, string> */
    public function genres(): array
    {
        return $this->genres;
    }

    /** @return array<string, mixed>|null */
    public function artist(string $id): ?array
    {
        return $this->artists[$id] ?? null;
    }

    /** @return array<string, mixed>|null */
    public function album(string $id): ?array
    {
        return $this->albums[$id] ?? null;
    }

    /** @return array<string, mixed>|null */
    public function track(string $id): ?array
    {
        return $this->tracks[$id] ?? null;
    }

    /** @return array<string, mixed>|null */
    public function playlist(string $id): ?array
    {
        return $this->playlists[$id] ?? null;
    }

    /** @return array<string, array<string, mixed>> */
    public function tracksForAlbum(string $albumId): array
    {
        return array_filter($this->tracks, fn (array $track): bool => $track['albumId'] === $albumId);
    }

    /** @return array<string, array<string, mixed>> */
    public function tracksForArtist(string $artistId): array
    {
        return array_filter($this->tracks, fn (array $track): bool => $track['artistId'] === $artistId);
    }

    /** @return array<string, array<string, mixed>> */
    public function albumsForArtist(string $artistId): array
    {
        return array_filter($this->albums, fn (array $album): bool => $album['artistId'] === $artistId);
    }

    /**
     * @param  array<int, string>  $trackIds
     * @return array<string, array<string, mixed>>
     */
    public function tracksForIds(array $trackIds): array
    {
        $tracks = [];

        foreach ($trackIds as $trackId) {
            if (isset($this->tracks[$trackId])) {
                $tracks[$trackId] = $this->tracks[$trackId];
            }
        }

        return $tracks;
    }

    /**
     * @return array{
     *     tracks: array<string, array<string, mixed>>,
     *     albums: array<string, array<string, mixed>>,
     *     artists: array<string, array<string, mixed>>,
     *     playlists: array<string, array<string, mixed>>
     * }
     */
    public function search(string $query): array
    {
        $needle = $this->normalize($query);

        if ($needle === '') {
            return ['tracks' => [], 'albums' => [], 'artists' => [], 'playlists' => []];
        }

        $tracks = array_filter($this->tracks, fn (array $track): bool => $this->matches(
            $needle,
            $track['title'],
            $track['artist'],
            $track['album'],
            $track['genre'],
        ));

        $albums = array_filter($this->albums, fn (array $album): bool => $this->matches(
            $needle,
            $album['title'],
            $album['artistName'],
            $album['genre'],
        ));

        $artists = array_filter($this->artists, fn (array $artist): bool => $this->matches(
            $needle,
            $artist['name'],
            $artist['genre'],
        ));

        $playlists = array_filter($this->playlists, fn (array $playlist): bool => $this->matches(
            $needle,
            $playlist['title'],
            $playlist['description'],
            $playlist['tag'],
            implode(' ', $playlist['tags']),
        ));

        return [
            'tracks' => array_slice($tracks, 0, 8, true),
            'albums' => array_slice($albums, 0, 4, true),
            'artists' => array_slice($artists, 0, 4, true),
            'playlists' => array_slice($playlists, 0, 4, true),
        ];
    }

    /**
     * Minimal track payload consumed by the browser audio player.
     *
     * @return array<int, array<string, mixed>>
     */
    public function playerTracks(): array
    {
        return array_values(array_map(fn (array $track): array => [
            'id' => $track['id'],
            'title' => $track['title'],
            'artist' => $track['artist'],
            'artistId' => $track['artistId'],
            'album' => $track['album'],
            'albumId' => $track['albumId'],
            'duration' => $track['duration'],
            'cover' => $track['cover'],
            'coverImage' => isset($track['coverImage']) ? asset($track['coverImage']) : null,
            'coverAttribution' => $this->albums[$track['albumId']]['coverAttribution'] ?? null,
            'coverSourceUrl' => $this->albums[$track['albumId']]['coverSourceUrl'] ?? null,
            'coverLicense' => $this->albums[$track['albumId']]['coverLicense'] ?? null,
            'coverLicenseUrl' => $this->albums[$track['albumId']]['coverLicenseUrl'] ?? null,
            'audioSource' => isset($track['audio']['file']) ? asset('audio/'.$track['audio']['file']) : null,
            'sourceTitle' => $track['audio']['sourceTitle'] ?? null,
            'attribution' => $track['audio']['attribution'] ?? null,
            'sourceUrl' => $track['audio']['sourceUrl'] ?? null,
            'license' => $track['audio']['license'] ?? null,
            'licenseUrl' => $track['audio']['licenseUrl'] ?? null,
        ], $this->tracks));
    }

    public function formatDuration(int $seconds): string
    {
        return sprintf('%d:%02d', intdiv($seconds, 60), $seconds % 60);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function filteredAlbums(?string $genre): array
    {
        if ($genre === null || $genre === '') {
            return $this->albums;
        }

        return array_filter($this->albums, fn (array $album): bool => $album['genre'] === $genre);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function filteredArtists(?string $genre): array
    {
        if ($genre === null || $genre === '') {
            return $this->artists;
        }

        return array_filter($this->artists, fn (array $artist): bool => $artist['genre'] === $genre);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function filteredPlaylists(?string $genre): array
    {
        if ($genre === null || $genre === '') {
            return $this->playlists;
        }

        return array_filter($this->playlists, fn (array $playlist): bool => in_array($genre, $playlist['tags'], true));
    }

    private function decorateAlbums(): void
    {
        foreach ($this->albums as $id => $album) {
            $tracks = $this->tracksForAlbum($id);
            $artist = $this->artists[$album['artistId']] ?? null;

            $this->albums[$id] = array_merge($album, [
                'artistName' => $artist['name'] ?? 'Sonic',
                'trackCount' => count($tracks),
                'duration' => array_sum(array_column($tracks, 'duration')),
                'durationLabel' => $this->formatDuration(array_sum(array_column($tracks, 'duration'))),
            ]);
        }
    }

    private function decorateTracks(): void
    {
        foreach ($this->tracks as $id => $track) {
            $album = $this->albums[$track['albumId']] ?? null;
            $artist = $this->artists[$track['artistId']] ?? null;

            $this->tracks[$id] = array_merge($track, [
                'artist' => $artist['name'] ?? 'Sonic',
                'album' => $album['title'] ?? 'Single',
                'albumYear' => $album['year'] ?? null,
                'genre' => $artist['genre'] ?? 'Sonic',
                'cover' => $album['cover'] ?? ['from' => '#22D97F', 'to' => '#0C0F0D', 'accent' => '#C9FF6B', 'pattern' => 'pulse'],
                'coverImage' => $album['coverImage'] ?? null,
                'durationLabel' => $this->formatDuration($track['duration']),
            ]);
        }
    }

    private function decoratePlaylists(): void
    {
        foreach ($this->playlists as $id => $playlist) {
            $tracks = $this->tracksForIds($playlist['trackIds']);

            $this->playlists[$id] = array_merge($playlist, [
                'tracks' => $tracks,
                'trackCount' => count($tracks),
                'duration' => array_sum(array_column($tracks, 'duration')),
                'durationLabel' => $this->formatDuration(array_sum(array_column($tracks, 'duration'))),
            ]);
        }
    }

    private function decorateArtists(): void
    {
        foreach ($this->artists as $id => $artist) {
            $tracks = $this->tracksForArtist($id);
            $albums = $this->albumsForArtist($id);

            $this->artists[$id] = array_merge($artist, [
                'tracks' => $tracks,
                'albums' => $albums,
                'trackCount' => count($tracks),
            ]);
        }
    }

    private function normalize(string $value): string
    {
        return Str::lower(Str::ascii(trim($value)));
    }

    private function matches(string $needle, string ...$haystacks): bool
    {
        foreach ($haystacks as $haystack) {
            if (Str::contains($this->normalize($haystack), $needle)) {
                return true;
            }
        }

        return false;
    }
}
