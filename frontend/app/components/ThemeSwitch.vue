<script setup lang="ts">
// Light mode is "hero", dark mode is "villain". The visuals come from the `dark`
// class alone (no v-if on the mode), so the server render always matches.
const colorMode = useColorMode()

function toggle() {
    colorMode.preference = colorMode.value === 'dark' ? 'light' : 'dark'
}
</script>

<template>
    <button
        type="button"
        class="group inline-flex items-center gap-2 rounded-full border-3 border-line bg-lemon p-1 shadow-comic-sm transition active:translate-x-0.5 active:translate-y-0.5 active:shadow-none sm:pr-3 dark:bg-lavender"
        title="Switch between hero (light) and villain (dark) mode"
        aria-label="Switch between hero (light) and villain (dark) mode"
        @click="toggle"
    >
        <!-- Track: both emblems sit faded at the ends, the knob slides over the active one -->
        <span class="relative flex h-9 w-17 items-center justify-between rounded-full border-3 border-line bg-sky px-1.5 dark:bg-paper">
            <svg viewBox="0 0 24 24" class="h-4 w-4 opacity-35" aria-hidden="true">
                <path d="M12 2.5 19.5 5.2v5.9c0 4.9-3.2 8.6-7.5 10.4-4.3-1.8-7.5-5.5-7.5-10.4V5.2Z" fill="none" stroke="var(--ink)" stroke-width="2.4" stroke-linejoin="round"/>
            </svg>
            <svg viewBox="0 0 24 24" class="h-4 w-4 opacity-35" aria-hidden="true">
                <path d="M1.8 10.2c0-2.6 2.4-3.9 5.4-3.9 2.3 0 3.6 1.4 4.8 1.4s2.5-1.4 4.8-1.4c3 0 5.4 1.3 5.4 3.9 0 3.9-2.4 6.5-5.4 6.5-2.4 0-3.3-2-4.8-2s-2.4 2-4.8 2c-3 0-5.4-2.6-5.4-6.5Z" fill="none" stroke="var(--ink)" stroke-width="2.2" stroke-linejoin="round"/>
            </svg>

            <span class="absolute -left-0.5 flex h-8 w-8 items-center justify-center rounded-full border-3 border-line bg-lemon transition-transform duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)] group-hover:-rotate-12 dark:translate-x-8 dark:rotate-[360deg] dark:bg-rose-deep dark:group-hover:rotate-[372deg]">
                <!-- Hero: shield with a star -->
                <svg viewBox="0 0 24 24" class="h-5 w-5 dark:hidden" aria-hidden="true">
                    <path d="M12 2.5 19.5 5.2v5.9c0 4.9-3.2 8.6-7.5 10.4-4.3-1.8-7.5-5.5-7.5-10.4V5.2Z" fill="#c7b4f7" stroke="#2f2a3b" stroke-width="2" stroke-linejoin="round"/>
                    <path d="m12 7.4 1.35 2.75 3.03.44-2.19 2.13.52 3.02L12 14.32l-2.71 1.42.52-3.02-2.19-2.13 3.03-.44Z" fill="#fdf1c4" stroke="#2f2a3b" stroke-width="1.3" stroke-linejoin="round"/>
                </svg>
                <!-- Villain: domino eye mask with angry eyes -->
                <svg viewBox="0 0 24 24" class="hidden h-5 w-5 dark:block" aria-hidden="true">
                    <path d="M1.8 10.2c0-2.6 2.4-3.9 5.4-3.9 2.3 0 3.6 1.4 4.8 1.4s2.5-1.4 4.8-1.4c3 0 5.4 1.3 5.4 3.9 0 3.9-2.4 6.5-5.4 6.5-2.4 0-3.3-2-4.8-2s-2.4 2-4.8 2c-3 0-5.4-2.6-5.4-6.5Z" fill="#2f2a3b" stroke="#2f2a3b" stroke-width="1.6" stroke-linejoin="round"/>
                    <path d="M4.6 10.4c1.2-.9 3.1-.9 4.6.4-.9 1.4-3.3 1.6-4.6-.4Z" fill="#fdf1c4"/>
                    <path d="M19.4 10.4c-1.2-.9-3.1-.9-4.6.4.9 1.4 3.3 1.6 4.6-.4Z" fill="#fdf1c4"/>
                </svg>
            </span>
        </span>

        <span class="hidden font-comic text-lg leading-none tracking-wider sm:inline">
            <span class="dark:hidden">Hero</span>
            <span class="hidden dark:inline">Villain</span>
        </span>
    </button>
</template>
