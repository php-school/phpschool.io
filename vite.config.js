import {defineConfig, splitVendorChunkPlugin} from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
    plugins: [
        vue(),
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
                app: path.resolve(__dirname, 'assets/app.js'),
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