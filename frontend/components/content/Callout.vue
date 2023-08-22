<script setup>
const props = defineProps({
    title: {
        type: String,
        default: 'Callout'
    }
})
</script>

<template>
    <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 shadow-md px-4 py-3">
        <div class="flex">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mt-1 mr-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
            </svg>
            <div>
                <p class="font-bold m-0">{{ props.title }}</p>
                <p class="text-sm m-0">
                    <slot/>
                </p>
            </div>
        </div>
    </div>
</template>