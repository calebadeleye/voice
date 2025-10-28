import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        // Output compiled assets into /build folder (outside /core)
        outDir: path.resolve(__dirname, '../build'),
        emptyOutDir: true, // clears old build files before building new ones
    },
    publicDir: false, // disables copying /public content
});
