import inertia from '@inertiajs/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import { defineConfig } from 'vite';
import fs from 'fs';
import os from 'os';

// Determinar el comando PHP de forma dinámica para EnvKit en Windows
let phpCommand = 'php artisan wayfinder:generate';
if (os.platform() === 'win32') {
    const envkitPhp = 'C:\\ProgramData\\envkit\\services\\php\\8.4.22\\php.exe';
    if (fs.existsSync(envkitPhp)) {
        phpCommand = `"${envkitPhp}" artisan wayfinder:generate`;
    }
}

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        inertia(),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        wayfinder({
            formVariants: true,
            command: phpCommand,
        }),
    ],
});
