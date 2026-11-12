<template>
    <div class="flex min-h-screen flex-col overflow-x-clip font-quicksand text-ink antialiased">
        <!-- First in the page, so keyboard and screen reader users reach it first -->
        <CookieBanner/>
        <TheNavigation/>

        <main class="w-full flex-1 px-4 pt-8 sm:px-6 lg:px-10 lg:pt-12">
            <div class="mx-auto max-w-6xl">
                <NuxtPage/>
            </div>
        </main>

        <TheFooter/>
    </div>
</template>

<script setup lang="ts">
useHead({
    htmlAttrs: { lang: 'en' },
    titleTemplate: (title) => title ? `${ title } · Codewave` : 'Codewave',
    // Without JavaScript the scroll reveal never runs, so show everything straight away. The cookie
    // banner's buttons need JavaScript too, and without it no optional script can run anyway.
    noscript: [ { innerHTML: '<style>[data-reveal]{opacity:1!important;transform:none!important}[data-cookie-banner]{display:none!important}</style>' } ],
})
</script>
