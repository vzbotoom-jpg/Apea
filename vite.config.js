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
                // ✅ Samakan dengan design system (app.css memakai 'Inter')
                bunny('Inter', {
                    weights: [400, 500, 600, 700, 800],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        // ✅ PERBAIKAN: WebSocket HMR error 400 di WSL/Windows
        host: '0.0.0.0',        // dev server bisa diakses dari Windows
        port: 5173,
        allowedHosts: true,     // hilangkan blokir origin (penyebab handshake 400)
        hmr: {
            host: 'localhost',  // paksa client HMR connect via localhost
        },
        watch: {
            ignored: ['/storage/framework/views/'],
        },
    },
});