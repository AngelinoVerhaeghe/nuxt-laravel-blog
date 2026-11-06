<script setup lang="ts">
// A section title in comic lettering, with a small caption box above it
withDefaults(defineProps<{
    kicker?: string
    as?: 'h1' | 'h2'
}>(), {
    as: 'h2',
})
</script>

<template>
    <div>
        <p v-if="kicker" class="inline-block -rotate-2 rounded-md border-3 border-line bg-lemon px-3 py-0.5 font-comic text-lg tracking-wider text-ink shadow-comic-sm">
            {{ kicker }}
        </p>
        <component :is="as" :class="as === 'h1' ? 'text-6xl sm:text-7xl' : 'text-5xl sm:text-6xl'" class="comic-title mt-3">
            <slot/>
        </component>
    </div>
</template>
