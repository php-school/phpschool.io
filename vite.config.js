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
    base: process.env.APP_ENV === 'development' ? '/' : '/dist/',

    build: {
        // output dir for production build
        outDir: '../public/dist',
        emptyOutDir: true,

        manifest: true,

        // our entry
        rollupOptions: {
            input: {
                cloud: path.resolve(__dirname, 'assets/cloud.js'),
                main: path.resolve(__dirname, 'assets/main.js'),
                login: path.resolve(__dirname, 'assets/login.js'),
            }
        }
    },

    server: {
        strictPort: true,
        port: 5133
    },

    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js'
        }
    }
})