<script setup>
const props = defineProps({
    title: {
        type: String,
        default: 'Callout'
    }
})
</script>

<template>
    <aside class="not-prose relative my-10 rounded-2xl border-3 border-line bg-mint px-5 pb-5 pt-7 text-ink shadow-comic">
        <p class="absolute -top-5 left-4 flex -rotate-2 items-center gap-2 rounded-lg border-3 border-line bg-lemon px-3 py-0.5 font-comic text-xl tracking-wider shadow-comic-sm">
            <Icon name="uil:bolt"/>
            {{ props.title }}
        </p>
        <div class="text-base leading-relaxed">
            <slot/>
        </div>
    </aside>
</template>
