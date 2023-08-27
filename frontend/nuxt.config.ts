// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig( {
    devtools: { enabled: true },
    modules: [
        'nuxt-icon',
        '@nuxt/content',
        '@nuxt/image',
    ],
    content: {
        highlight: {
            theme: 'nord',
            preload: [ 'ts', 'js', 'css', 'sql', 'json', 'bash', "vue" ]
        }
    },
    css: [ '~/assets/css/main.css' ],
    postcss: {
        plugins: {
            tailwindcss: {},
            autoprefixer: {},
        },
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
