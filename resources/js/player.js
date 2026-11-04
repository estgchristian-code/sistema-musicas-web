const STORAGE_KEY = 'sonic.player';
const HAVE_METADATA = 1;

function clamp(value, min, max) {
    return Math.min(max, Math.max(min, value));
}

function createPlayer(config) {
    return {
        tracks: config.tracks ?? [],
        trackMap: {},
        current: null,
        queue: [],
        position: 0,
        duration: 0,
        playing: false,
        loading: false,
        expanded: false,
        seeking: false,
        volume: clamp(Number(config.volume) || 0.72, 0, 1),
        previousVolume: 0.72,
        muted: false,
        shuffle: false,
        repeat: 'off',
        favorites: new Set(config.favorites ?? []),
        audio: null,
        ticker: null,
        playbackVersion: 0,
        error: '',

        init() {
            this.trackMap = Object.fromEntries(this.tracks.map((track) => [track.id, track]));
            this.queue = (config.initialQueue ?? []).filter((id) => this.trackMap[id]);

            if (this.queue.length === 0) {
                this.queue = this.tracks.map((track) => track.id);
            }

            const saved = this.readSavedState();
            const restoredId = saved?.trackId && this.trackMap[saved.trackId] ? saved.trackId : config.initialTrackId;
            const initialTrack = this.trackMap[restoredId] ?? this.tracks[0] ?? null;

            if (saved) {
                this.volume = clamp(Number(saved.volume) || this.volume, 0, 1);
                this.previousVolume = this.volume || 0.72;
                this.shuffle = Boolean(saved.shuffle);
                this.repeat = ['off', 'all', 'one'].includes(saved.repeat) ? saved.repeat : 'off';
            }

            this.audio = new Audio();
            this.audio.preload = 'metadata';
            this.audio.volume = this.volume;
            this.audio.addEventListener('loadedmetadata', () => {
                if (this.isCurrentAudioSource() && Number.isFinite(this.audio.duration)) {
                    this.duration = this.audio.duration;
                }
            });
            this.audio.addEventListener('timeupdate', () => {
                if (!this.seeking && Number.isFinite(this.audio.currentTime)) {
                    this.position = this.audio.currentTime;
                }
            });
            this.audio.addEventListener('play', () => {
                if (this.isCurrentAudioSource()) {
                    this.playing = true;
                    this.updateDocumentTitle();
                }
            });
            this.audio.addEventListener('pause', () => {
                this.playing = !this.audio.paused && this.isCurrentAudioSource();
                this.updateDocumentTitle();
            });
            this.audio.addEventListener('ended', () => {
                if (this.isCurrentAudioSource()) {
                    this.handleEnded();
                }
            });
            this.audio.addEventListener('error', () => {
                if (this.isCurrentAudioSource()) {
                    this.playbackVersion += 1;
                    this.error = 'Não foi possível carregar o arquivo de áudio desta faixa.';
                    this.playing = false;
                    this.loading = false;
                    this.updateDocumentTitle();
                }
            });

            if (initialTrack) {
                this.setTrack(initialTrack.id);
                this.updateDocumentTitle();
            }
        },

        destroy() {
            this.stopTicker();
            this.audio?.pause();
            document.body.classList.remove('overflow-hidden');
        },

        get progressPercent() {
            return this.duration > 0 ? clamp((this.position / this.duration) * 100, 0, 100) : 0;
        },

        get volumePercent() {
            return this.muted ? 0 : Math.round(this.volume * 100);
        },

        get progressStyle() {
            return `background: linear-gradient(90deg, #4DF29B 0%, #4DF29B ${this.progressPercent}%, rgba(255,255,255,.14) ${this.progressPercent}%, rgba(255,255,255,.14) 100%)`;
        },

        get volumeStyle() {
            return `background: linear-gradient(90deg, #4DF29B 0%, #4DF29B ${this.volumePercent}%, rgba(255,255,255,.14) ${this.volumePercent}%, rgba(255,255,255,.14) 100%)`;
        },

        get isFavorite() {
            return this.current ? this.favorites.has(this.current.id) : false;
        },

        async playFromEvent(detail = {}) {
            const trackId = detail.trackId;
            const nextQueue = Array.isArray(detail.queue) ? detail.queue.filter((id) => this.trackMap[id]) : [];

            if (!this.trackMap[trackId]) {
                return;
            }

            if (nextQueue.length > 0) {
                this.queue = nextQueue;
            }

            if (this.current?.id === trackId && this.playing) {
                this.seek(0);
                return;
            }

            this.setTrack(trackId);
            await this.play();
        },

        setTrack(trackId) {
            const track = this.trackMap[trackId];

            if (!track) {
                return;
            }

            this.playbackVersion += 1;
            this.audio.pause();
            this.stopTicker();
            this.current = track;
            this.position = 0;
            this.duration = 0;
            this.error = '';
            this.loading = false;

            if (track.audioSource) {
                this.audio.src = track.audioSource;
                this.audio.load();
            } else {
                this.audio.removeAttribute('src');
                this.audio.load();
                this.error = 'Esta faixa não possui um arquivo de áudio disponível.';
            }

            this.saveState();
            this.updateDocumentTitle();
        },

        async play() {
            if (!this.current) {
                return;
            }

            if (!this.current.audioSource) {
                this.error = 'Esta faixa não possui um arquivo de áudio disponível.';
                this.playing = false;
                return;
            }

            this.error = '';

            if (this.duration > 0 && this.position >= this.duration) {
                this.position = 0;
                this.audio.currentTime = 0;
            }

            const playbackVersion = ++this.playbackVersion;
            const trackId = this.current.id;
            this.loading = true;

            try {
                await this.audio.play();

                if (playbackVersion !== this.playbackVersion || this.current?.id !== trackId) {
                    return;
                }

                this.playing = true;
                this.startTicker();
                this.saveState();
            } catch (error) {
                if (playbackVersion !== this.playbackVersion || this.current?.id !== trackId) {
                    return;
                }

                if (error.name === 'NotAllowedError') {
                    this.error = 'O navegador bloqueou a reprodução. Toque em reproduzir para tentar novamente.';
                } else {
                    this.error = 'Não foi possível reproduzir o arquivo de áudio desta faixa.';
                }

                this.playing = false;
            } finally {
                if (playbackVersion === this.playbackVersion) {
                    this.loading = false;
                }
            }
        },

        pause() {
            if (!this.playing && !this.loading) {
                return;
            }

            this.playbackVersion += 1;
            this.position = this.currentPosition();
            this.audio.pause();
            this.stopTicker();
            this.playing = false;
            this.loading = false;
            this.saveState();
            this.updateDocumentTitle();
        },

        toggle() {
            if (this.playing) {
                this.pause();
                return;
            }

            this.play();
        },

        playableQueue() {
            return this.queue.filter((id) => this.trackMap[id]?.audioSource);
        },

        findQueueTrack(direction, wrap = true) {
            const playable = this.playableQueue();

            if (playable.length === 0) {
                return null;
            }

            if (this.shuffle && playable.length > 1) {
                const alternatives = playable.filter((id) => id !== this.current?.id);
                return alternatives[Math.floor(Math.random() * alternatives.length)];
            }

            const currentIndex = playable.indexOf(this.current?.id);

            if (currentIndex === -1) {
                return direction > 0 ? playable[0] : playable[playable.length - 1];
            }

            const nextIndex = currentIndex + direction;

            if (!wrap && (nextIndex < 0 || nextIndex >= playable.length)) {
                return null;
            }

            return playable[(nextIndex + playable.length) % playable.length];
        },

        async next() {
            const nextTrackId = this.findQueueTrack(1);

            if (!nextTrackId) {
                this.error = 'Não há arquivos de áudio disponíveis nesta fila.';
                return;
            }

            const wasPlaying = this.playing;
            this.setTrack(nextTrackId);

            if (wasPlaying) {
                await this.play();
            }
        },

        async previous() {
            if (this.position > 3) {
                this.seek(0);
                return;
            }

            const previousTrackId = this.findQueueTrack(-1);

            if (!previousTrackId) {
                this.error = 'Não há arquivos de áudio disponíveis nesta fila.';
                return;
            }

            const wasPlaying = this.playing;
            this.setTrack(previousTrackId);

            if (wasPlaying) {
                await this.play();
            }
        },

        handleEnded() {
            if (this.repeat === 'one') {
                this.seek(0);
                this.play();
                return;
            }

            const nextTrackId = this.findQueueTrack(1, this.repeat === 'all');

            if (!nextTrackId) {
                this.position = this.duration;
                this.playing = false;
                this.stopTicker();
                this.updateDocumentTitle();
                return;
            }

            this.setTrack(nextTrackId);
            this.play();
        },

        cycleRepeat() {
            this.repeat = this.repeat === 'off' ? 'all' : this.repeat === 'all' ? 'one' : 'off';
            this.saveState();
        },

        toggleShuffle() {
            this.shuffle = !this.shuffle;
            this.saveState();
        },

        currentPosition() {
            return this.audio && Number.isFinite(this.audio.currentTime)
                ? this.audio.currentTime
                : this.position;
        },

        startTicker() {
            this.stopTicker();

            this.ticker = window.setInterval(() => {
                if (!this.seeking && this.audio && Number.isFinite(this.audio.currentTime)) {
                    this.position = this.audio.currentTime;
                }
            }, 250);
        },

        stopTicker() {
            if (this.ticker !== null) {
                window.clearInterval(this.ticker);
                this.ticker = null;
            }
        },

        beginSeek() {
            this.seeking = true;
        },

        previewSeek(value) {
            this.seeking = true;
            this.position = clamp(Number(value) || 0, 0, this.duration);
        },

        commitSeek(value) {
            this.seek(value);
            this.seeking = false;
        },

        seek(value) {
            const target = clamp(Number(value) || 0, 0, this.duration);
            this.position = target;

            if (!this.current?.audioSource) {
                return;
            }

            if (this.audio.readyState >= HAVE_METADATA) {
                this.audio.currentTime = target;
            } else {
                this.audio.addEventListener('loadedmetadata', () => {
                    if (this.current?.audioSource) {
                        this.audio.currentTime = target;
                    }
                }, { once: true });
            }
        },

        setVolume(value) {
            this.volume = clamp(Number(value) || 0, 0, 1);
            this.muted = this.volume === 0;

            if (this.volume > 0) {
                this.previousVolume = this.volume;
            }

            if (this.audio) {
                this.audio.volume = this.muted ? 0 : this.volume;
            }

            this.saveState();
        },

        toggleMute() {
            if (this.muted || this.volume === 0) {
                this.muted = false;
                this.volume = this.previousVolume || 0.72;
            } else {
                this.previousVolume = this.volume;
                this.muted = true;
            }

            if (this.audio) {
                this.audio.volume = this.muted ? 0 : this.volume;
            }

            this.saveState();
        },

        toggleFavorite() {
            if (!this.current) {
                return;
            }

            if (this.favorites.has(this.current.id)) {
                this.favorites.delete(this.current.id);
            } else {
                this.favorites.add(this.current.id);
            }
        },

        setFavorites(ids) {
            this.favorites = new Set(ids ?? []);
        },

        toggleExpanded() {
            this.expanded = !this.expanded;
            document.body.classList.toggle('overflow-hidden', this.expanded);
        },

        closeExpanded() {
            this.expanded = false;
            document.body.classList.remove('overflow-hidden');
        },

        handleSpace(event) {
            const tag = document.activeElement?.tagName?.toLowerCase();
            const isTyping = tag === 'input' || tag === 'textarea' || tag === 'select' || document.activeElement?.isContentEditable;

            if (isTyping || tag === 'button') {
                return;
            }

            event.preventDefault();
            this.toggle();
        },

        formatTime(seconds) {
            const total = Math.max(0, Math.round(Number(seconds) || 0));

            return `${Math.floor(total / 60)}:${String(total % 60).padStart(2, '0')}`;
        },

        isCurrentAudioSource() {
            return Boolean(
                this.current?.audioSource
                && this.audio?.currentSrc
                && this.audio.currentSrc === new URL(this.current.audioSource, document.baseURI).href
            );
        },

        updateDocumentTitle() {
            document.title = this.playing && this.current
                ? `${this.current.title} · ${this.current.artist} — Sonic`
                : 'Sonic — música sem limites';
        },

        readSavedState() {
            try {
                return JSON.parse(window.localStorage.getItem(STORAGE_KEY));
            } catch (error) {
                return null;
            }
        },

        saveState() {
            try {
                window.localStorage.setItem(STORAGE_KEY, JSON.stringify({
                    trackId: this.current?.id ?? null,
                    volume: this.volume,
                    shuffle: this.shuffle,
                    repeat: this.repeat,
                }));
            } catch (error) {
                // Private browsing can block local storage.
            }
        },
    };
}

export function registerSonicPlayer(Alpine) {
    if (!Alpine || Alpine.__sonicPlayerRegistered) {
        return;
    }

    Alpine.data('sonicPlayer', (config) => createPlayer(config));
    Alpine.__sonicPlayerRegistered = true;
}
