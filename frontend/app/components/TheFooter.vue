<script setup lang="ts">
const links = [
    { name: 'Github', href: 'https://github.com/AngelinoVerhaeghe', icon: 'uil:github', color: 'bg-lavender' },
    { name: 'Twitter', href: 'https://twitter.com/AngelinoSonic', icon: 'uil:twitter', color: 'bg-sky' },
    { name: 'LinkedIn', href: 'https://www.linkedin.com/in/angelino-verhaeghe/', icon: 'uil:linkedin', color: 'bg-mint' },
]

const pages = [
    { name: 'Home', href: '/' },
    { name: 'Blog', href: '/blog' },
    { name: 'Projects', href: '/projects' },
    { name: 'Cases', href: '/cases' },
]

const year = new Date().getFullYear()

const { openSettings } = useCookieConsent()
</script>

<template>
    <footer class="mt-24 px-4 pb-6 sm:px-6 lg:mt-32 lg:px-10">
        <div v-reveal class="comic-panel relative mx-auto max-w-6xl overflow-visible bg-lavender px-6 pb-8 pt-14 sm:px-10 lg:pt-16">
            <!-- Caption box sitting on the panel edge -->
            <p class="absolute -top-5 left-6 -rotate-2 rounded-lg border-3 border-line bg-lemon px-4 py-1 font-comic text-xl tracking-wider text-ink shadow-comic-sm sm:left-10">
                To be continued…
            </p>

            <ComicBurst text="Zap!" color="var(--rose)" class="absolute -right-3 -top-10 w-24 rotate-12 text-base sm:-right-6 sm:w-28 lg:-top-14 lg:w-32 lg:text-lg"/>

            <div class="grid gap-10 md:grid-cols-[1.4fr_1fr_1fr]">
                <div v-reveal="'left'">
                    <TheLogo/>
                    <p class="mt-4 max-w-sm text-ink-soft">Exploring life's stories and wisdom, one issue at a time. Heroes and villains welcome.</p>
                </div>

                <div v-reveal="{ from: 'up', delay: 100 }">
                    <h2 class="font-comic text-2xl tracking-wider">Pages</h2>
                    <ul class="mt-3 grid grid-cols-2 gap-x-4 gap-y-2 md:grid-cols-1">
                        <li v-for="page in pages" :key="page.name">
                            <NuxtLink :to="page.href" class="group inline-flex items-center gap-1 font-semibold text-ink-soft transition hover:text-ink">
                                <Icon name="uil:arrow-right" class="transition-transform group-hover:translate-x-1"/>
                                {{ page.name }}
                            </NuxtLink>
                        </li>
                    </ul>
                </div>

                <div v-reveal="{ from: 'right', delay: 200 }">
                    <h2 class="font-comic text-2xl tracking-wider">Assemble</h2>
                    <ul class="mt-3 flex gap-3">
                        <li v-for="link in links" :key="link.name">
                            <a
                                :href="link.href"
                                target="_blank"
                                rel="nofollow noopener"
                                :aria-label="link.name"
                                :class="link.color"
                                class="flex h-12 w-12 items-center justify-center rounded-xl border-3 border-line text-ink shadow-comic-sm transition hover:-translate-y-1 hover:-rotate-6 active:translate-y-0 active:shadow-none"
                            >
                                <Icon :name="link.icon" size="1.5rem"/>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-10 flex flex-col items-center justify-between gap-2 border-t-3 border-dashed border-line/40 pt-6 text-sm text-ink-soft sm:flex-row">
                <span class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1">
                    <span>© {{ year }} Codewave. All rights reserved.</span>
                    <NuxtLink to="/cookies" class="font-semibold underline decoration-2 underline-offset-2 transition hover:text-ink">Cookie policy</NuxtLink>
                    <button type="button" class="font-semibold underline decoration-2 underline-offset-2 transition hover:text-ink" @click="openSettings">Cookie settings</button>
                </span>
                <span class="font-comic text-lg tracking-wider">
                    <span class="dark:hidden">Drawn in hero mode</span>
                    <span class="hidden dark:inline">Plotted in villain mode</span>
                </span>
            </div>
        </div>
    </footer>
</template>
