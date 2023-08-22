// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig( {
    devtools: { enabled: true },
    modules: [
        'nuxt-icon',
        '@nuxt/content'
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
    }
} );
