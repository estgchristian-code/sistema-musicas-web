import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Outfit', {
                    weights: [400, 500, 600, 700, 800],
                    variable: '--font-outfit',
                    display: 'swap',
                }),
                bunny('DM Sans', {
                    weights: [400, 500, 600, 700],
                    variable: '--font-dm-sans',
                    display: 'swap',
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
