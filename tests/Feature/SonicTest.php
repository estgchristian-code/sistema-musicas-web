<?php

namespace Tests\Feature;

use App\Livewire\Sonic;
use App\Support\SonicCatalog;
use Livewire\Livewire;
use Tests\TestCase;

class SonicTest extends TestCase
{
    public function test_home_page_renders_the_fictional_catalog(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Sua trilha sonora para hoje')
            ->assertSee('Foco Absoluto')
            ->assertSee('Luna Vale')
            ->assertSeeLivewire(Sonic::class);
    }

    public function test_primary_navigation_updates_the_section_and_history(): void
    {
        Livewire::test(Sonic::class)
            ->assertSet('section', 'home')
            ->call('navigateTo', 'explore')
            ->assertSet('section', 'explore')
            ->assertSee('Descubra um novo clima')
            ->assertDispatched('sonic-navigated')
            ->call('navigateTo', 'library')
            ->assertSet('section', 'library')
            ->call('goBack')
            ->assertSet('section', 'explore')
            ->call('goForward')
            ->assertSet('section', 'library');
    }

    public function test_invalid_navigation_requests_are_ignored(): void
    {
        Livewire::test(Sonic::class)
            ->call('navigateTo', 'invalid')
            ->assertSet('section', 'home')
            ->call('openPlaylist', 'missing-playlist')
            ->assertSet('section', 'home');
    }

    public function test_search_switches_to_search_section_and_matches_accents(): void
    {
        Livewire::test(Sonic::class)
            ->set('search', 'mare alta')
            ->assertSet('section', 'search')
            ->assertSee('Resultados para')
            ->assertSee('Maré Alta')
            ->call('setSearchTab', 'albums')
            ->assertSet('searchTab', 'albums')
            ->assertSee('Maré Alta')
            ->call('clearSearch')
            ->assertSet('section', 'home')
            ->assertSet('search', '');
    }

    public function test_favorites_are_toggled_and_persisted_in_the_session(): void
    {
        Livewire::test(Sonic::class)
            ->call('toggleFavorite', 'vidro-fume')
            ->assertSet('favoriteTrackIds', ['vidro-fume'])
            ->assertDispatched('sonic-favorites-changed');

        $this->assertSame(['vidro-fume'], session('sonic.favorites'));

        $this->withSession(['sonic.favorites' => ['vidro-fume']])
            ->get('/')
            ->assertOk()
            ->assertSee('Músicas curtidas');
    }

    public function test_playing_a_track_dispatches_the_player_event_with_its_queue(): void
    {
        Livewire::test(Sonic::class)
            ->call('playTrack', 'vidro-fume', 'playlist', 'foco-absoluto')
            ->assertSet('currentTrackId', 'vidro-fume')
            ->assertDispatched('sonic-play', trackId: 'vidro-fume');
    }

    public function test_playing_a_playlist_starts_with_its_first_track(): void
    {
        Livewire::test(Sonic::class)
            ->call('playPlaylist', 'foco-absoluto')
            ->assertSet('currentTrackId', 'quarto-404')
            ->assertDispatched('sonic-play', trackId: 'quarto-404');
    }

    public function test_licensed_album_tracks_have_local_audio_and_complete_attribution(): void
    {
        $catalog = app(SonicCatalog::class);
        $tracks = $catalog->tracksForAlbum('piano-demo');
        $playerTracks = array_column($catalog->playerTracks(), null, 'id');
        $album = $catalog->album('piano-demo');

        $this->assertCount(4, $tracks);

        foreach ($tracks as $track) {
            $this->assertFileExists(public_path('audio/'.$track['audio']['file']));
            $this->assertSame('CC BY-SA 3.0', $track['audio']['license']);
            $this->assertSame(
                asset('audio/'.$track['audio']['file']),
                $playerTracks[$track['id']]['audioSource'],
            );
        }

        $this->assertSame('Marcelo Armengot', $album['coverAttribution']);
        $this->assertSame('CC BY-SA 3.0', $album['coverLicense']);
        $this->assertFileExists(public_path($album['coverImage']));
    }

    public function test_tracks_without_audio_files_are_not_given_a_playable_source(): void
    {
        $tracks = app(SonicCatalog::class)->playerTracks();
        $tracksById = array_column($tracks, null, 'id');

        $this->assertNull($tracksById['vidro-fume']['audioSource']);
        $this->assertNull($tracksById['vidro-fume']['coverImage']);
    }

    public function test_album_and_artist_pages_render_their_content(): void
    {
        Livewire::test(Sonic::class)
            ->call('openAlbum', 'mare-alta')
            ->assertSet('section', 'album')
            ->assertSee('Maré Alta')
            ->assertSee('Tocar álbum')
            ->call('openArtist', 'corvo-azul')
            ->assertSet('section', 'artist')
            ->assertSee('Corvo Azul')
            ->assertSee('Faixas mais tocadas');
    }

    public function test_licensed_album_page_displays_the_artwork_and_credit(): void
    {
        Livewire::test(Sonic::class)
            ->call('openAlbum', 'piano-demo')
            ->assertSee('Piano demo')
            ->assertSee('Marcelo Armengot')
            ->assertSee('CC BY-SA 3.0')
            ->assertSee('archive.org/details/pandacd-2133-piano-demo');
    }

    public function test_search_results_can_be_played_as_a_queue(): void
    {
        Livewire::test(Sonic::class)
            ->set('search', 'luna')
            ->call('playSearch')
            ->assertSet('currentTrackId', 'vidro-fume')
            ->assertDispatched('sonic-play', trackId: 'vidro-fume');
    }

    public function test_explore_filters_the_catalog_by_genre(): void
    {
        Livewire::test(Sonic::class)
            ->call('navigateTo', 'explore')
            ->assertSee('Descubra um novo clima')
            ->call('setGenre', 'MPB')
            ->assertSet('genre', 'MPB')
            ->assertSee('Playlists com esse clima')
            ->assertSee('Corvo Azul')
            ->call('setGenre', null)
            ->assertSet('genre', null);
    }

    public function test_library_tabs_render_each_collection(): void
    {
        Livewire::test(Sonic::class)
            ->call('navigateTo', 'library')
            ->assertSee('Tudo no seu ritmo')
            ->assertSee('Sua coleção começa aqui')
            ->call('setLibraryTab', 'playlists')
            ->assertSee('Playlists salvas')
            ->call('setLibraryTab', 'albums')
            ->assertSee('Álbuns salvos')
            ->call('setLibraryTab', 'artists')
            ->assertSee('Artistas seguidos');
    }

    public function test_playlists_page_and_playlist_detail_render(): void
    {
        Livewire::test(Sonic::class)
            ->call('navigateTo', 'playlists')
            ->assertSee('Curadoria Sonic')
            ->call('openPlaylist', 'foco-absoluto')
            ->assertSet('section', 'playlist')
            ->assertSee('Foco Absoluto')
            ->assertSee('Tocar tudo')
            ->assertSee('Playlists parecidas');
    }
}
