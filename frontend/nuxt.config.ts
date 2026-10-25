// https://nuxt.com/docs/api/configuration/nuxt-config
import tailwindcss from '@tailwindcss/vite'

export default defineNuxtConfig( {
    compatibilityDate: '2026-10-25',
    devtools: { enabled: true },
    modules: [
        '@nuxt/icon',
        '@nuxt/content',
        '@nuxt/image',
    ],
    runtimeConfig: {
        public: {
            // Override with NUXT_PUBLIC_API_BASE
            apiBase: 'http://localhost:8000/api/v1',
        },
    },
    content: {
        build: {
            markdown: {
                highlight: {
                    theme: 'nord',
                    langs: [ 'ts', 'js', 'css', 'sql', 'json', 'bash', 'vue' ]
                }
            }
        },
        experimental: {
            // Use Node's built-in SQLite instead of the better-sqlite3 package
            sqliteConnector: 'native'
        }
    },
    css: [ '~/assets/css/main.css' ],
    vite: {
        plugins: [
            tailwindcss(),
        ],
    },
    image: {
        // The screen sizes predefined by `@nuxt/image`:
        screens: {
            xs: 320,
            sm: 640,
            md: 768,
            lg: 1024,
            xl: 1280,
            xxl: 1536,
            '2xl': 1536
        },
        format: ['webp'],
    }
} );
