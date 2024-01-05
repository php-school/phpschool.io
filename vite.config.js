import {defineConfig, splitVendorChunkPlugin} from 'vite'
import vue from '@vitejs/plugin-vue'
import liveReload from 'vite-plugin-live-reload'
import path from 'path'

export default defineConfig({
    plugins: [
        vue(),
        liveReload([
            __dirname + '/templates/**/*.phtml',
        ]),
        splitVendorChunkPlugin(),
    ],

    // config
    root: 'assets',

    build: {
        // output dir for production build
        outDir: '../public/dist',
        emptyOutDir: true,

        manifest: true,

        // our entry
        rollupOptions: {
            input: {
                online: path.resolve(__dirname, 'assets/online.js'),
            }
        }
    },

    server: {
        strictPort: true,
        port: 5133,
        proxy: {
            '/api': {
                target: 'http://www.phpschool.local',
                changeOrigin: true,
            },
        },
    },

    define: {
        'process.env.ES_BUILD': process.env.ES_BUILD,
    },
})