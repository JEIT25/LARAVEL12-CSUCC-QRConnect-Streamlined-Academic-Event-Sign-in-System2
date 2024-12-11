import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import fs from 'fs';
import path from 'path';

export default defineConfig({
    // server: { //this config is for running locally in the same wifi
    //     host: '0.0.0.0', //configure ip address evrytime network is change
    //     port: 5173,
    //     https: {
    //         key: fs.readFileSync(path.resolve(__dirname, 'ssl/private-key.key')),
    //         cert: fs.readFileSync(path.resolve(__dirname, 'ssl/certificate.crt')),
    //     },
    // },
    // host: {
    //     host: '0.0.0.0',  // Allow connections from any IP
    //     port: 5173,       // You can change this to any port you prefer
    //     strictPort: true, // Ensure that Vite will fail if the port is already taken
    // },
    plugins: [
        laravel(['resources/js/app.js']),
        vue({
            template: {
                transformAssetUrls: {
                    // The Vue plugin will re-write asset URLs, when referenced
                    // in Single File Components, to point to the Laravel web
                    // server. Setting this to `null` allows the Laravel plugin
                    // to instead re-write asset URLs to point to the Vite
                    // server instead.
                    base: null,

                    // The Vue plugin will parse absolute URLs and treat them
                    // as absolute paths to files on disk. Setting this to
                    // `false` will leave absolute URLs un-touched so they can
                    // reference assets in the public directory as expected.
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
