<script setup lang="ts">
// A comic "POW!" starburst: a spiky polygon with lettering on top
const props = withDefaults(defineProps<{
    text: string
    color?: string
    spikes?: number
}>(), {
    color: 'var(--lemon)',
    spikes: 14,
})

const points = computed(() => {
    const result: string[] = []
    for (let i = 0; i < props.spikes * 2; i++) {
        const radius = i % 2 === 0 ? 48 : 34
        const angle = (Math.PI * i) / props.spikes - Math.PI / 2
        result.push(`${ (50 + radius * Math.cos(angle)).toFixed(2) },${ (50 + radius * Math.sin(angle)).toFixed(2) }`)
    }
    return result.join(' ')
})
</script>

<template>
    <!-- The root has no position class, so callers can position it (absolute, etc.) -->
    <div aria-hidden="true">
        <div class="relative flex aspect-square items-center justify-center">
            <svg viewBox="0 0 100 100" class="absolute inset-0 h-full w-full overflow-visible drop-shadow-[4px_4px_0_var(--pop)]">
                <polygon :points="points" :fill="props.color" stroke="var(--line)" stroke-width="2.5" stroke-linejoin="round"/>
            </svg>
            <span class="relative font-comic text-[1.6em] leading-none tracking-wider text-ink">{{ props.text }}</span>
        </div>
    </div>
</template>
