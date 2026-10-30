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
    <a :href="props.route" class="inline-block rounded-full bg-ink px-7 py-3 text-sm font-semibold tracking-wide text-cream shadow-md shadow-lavender-deep/20 hover:bg-lavender-deep duration-200">{{ props.text }}</a>
</template>
