<script setup>
const props = defineProps({
    icon: {
        type: String,
        required: false
    },
    text: {
        type: String,
        required: true
    },
    route: {
        type: String,
        required: true
    }
})
</script>

<template>
    <NuxtLink
        :to="props.route"
        class="group inline-flex items-center gap-2 rounded-2xl border-3 border-line bg-ink px-7 py-3 font-comic text-2xl tracking-wider text-paper shadow-comic transition duration-200 hover:-translate-y-1 hover:-rotate-1 hover:bg-lavender-deep active:translate-x-1 active:translate-y-1 active:rotate-0 active:shadow-none dark:bg-lavender-deep dark:text-paper dark:hover:bg-rose-deep"
    >
        {{ props.text }}
        <Icon :name="props.icon ?? 'uil:arrow-right'" class="transition-transform group-hover:translate-x-1"/>
    </NuxtLink>
</template>
