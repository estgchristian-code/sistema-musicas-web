import { registerSonicPlayer } from './player';

const start = () => {
    if (window.Alpine) {
        registerSonicPlayer(window.Alpine);
    }
};

if (window.Alpine) {
    start();
} else {
    document.addEventListener('alpine:init', start, { once: true });
}
