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
    <a :href="props.route" class="bg-amber-600 text-amber-100 hover:bg-amber-800 duration-200 rounded-lg shadow-md px-4 py-2">{{ props.text }}</a>
</template>