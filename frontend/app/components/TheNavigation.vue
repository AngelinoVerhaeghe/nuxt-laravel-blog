<script setup lang="ts">
const route = useRoute()
const open = ref(false)

const navigation = [
    { name: 'Home', href: '/', icon: 'uil:rocket' },
    { name: 'Blog', href: '/blog', icon: 'uil:book-open' },
    { name: 'Projects', href: '/projects', icon: 'uil:bolt' },
    { name: 'Cases', href: '/cases', icon: 'uil:shield' },
]

function isCurrent(href: string) {
    return href === '/' ? route.path === '/' : route.path.startsWith(href)
}

// Close the mobile menu after navigating, and on Escape
watch(() => route.fullPath, () => open.value = false)

function closeOnEscape(event: KeyboardEvent) {
    if (event.key === 'Escape') open.value = false
}

onMounted(() => window.addEventListener('keydown', closeOnEscape))
onBeforeUnmount(() => window.removeEventListener('keydown', closeOnEscape))
</script>

<template>
    <header class="sticky top-0 z-40 px-4 pt-3 sm:px-6 lg:px-10">
        <nav class="relative mx-auto max-w-6xl" aria-label="Main">
            <div class="comic-panel flex items-center justify-between gap-3 rounded-2xl px-3 py-2 sm:px-5 lg:py-3">
                <TheLogo/>

                <!-- Tablet and desktop -->
                <ul class="hidden items-center gap-1 md:flex lg:gap-3">
                    <li v-for="item in navigation" :key="item.name">
                        <NuxtLink
                            :to="item.href"
                            :aria-current="isCurrent(item.href) ? 'page' : undefined"
                            :class="isCurrent(item.href)
                                ? 'border-line bg-lavender text-ink shadow-comic-sm -rotate-2'
                                : 'border-transparent text-ink-soft hover:border-line hover:bg-peach hover:text-ink hover:rotate-2'"
                            class="inline-block rounded-full border-3 px-3 py-1 font-comic text-xl tracking-wider transition duration-200 lg:px-4"
                        >
                            {{ item.name }}
                        </NuxtLink>
                    </li>
                </ul>

                <div class="flex items-center gap-2">
                    <ThemeSwitch/>

                    <!-- Mobile -->
                    <button
                        type="button"
                        class="flex h-11 w-11 items-center justify-center rounded-xl border-3 border-line bg-rose text-ink shadow-comic-sm transition active:translate-x-0.5 active:translate-y-0.5 active:shadow-none md:hidden"
                        :aria-expanded="open"
                        aria-controls="mobile-menu"
                        @click="open = !open"
                    >
                        <span class="sr-only">{{ open ? 'Close menu' : 'Open menu' }}</span>
                        <Icon :name="open ? 'uil:times' : 'uil:bars'" size="1.5rem" class="transition-transform duration-300" :class="open && 'rotate-90'"/>
                    </button>
                </div>
            </div>

            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 -translate-y-4 scale-95"
                leave-active-class="transition duration-200 ease-in"
                leave-to-class="opacity-0 -translate-y-2 scale-95"
            >
                <div v-if="open" id="mobile-menu" class="comic-panel halftone absolute inset-x-0 top-full mt-3 origin-top rounded-2xl bg-panel p-3 md:hidden">
                    <ul class="space-y-2">
                        <li v-for="(item, index) in navigation" :key="item.name" class="animate-slide-left" :style="{ animationDelay: `${ 60 + index * 70 }ms` }">
                            <NuxtLink
                                :to="item.href"
                                :aria-current="isCurrent(item.href) ? 'page' : undefined"
                                :class="isCurrent(item.href) ? 'bg-lavender shadow-comic-sm' : 'bg-panel hover:bg-peach'"
                                class="flex items-center gap-3 rounded-xl border-3 border-line px-4 py-2 font-comic text-2xl tracking-wider text-ink transition"
                            >
                                <Icon :name="item.icon" size="1.4rem" class="text-lavender-deep"/>
                                {{ item.name }}
                            </NuxtLink>
                        </li>
                    </ul>
                </div>
            </Transition>
        </nav>
    </header>
</template>
