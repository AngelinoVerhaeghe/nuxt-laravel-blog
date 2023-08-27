<script setup>
defineProps({
    post: {
        type: Object,
        required: true
    }
})
</script>

<template>
    <div class="bg-white rounded-md shadow-md overflow-hidden">
        <div class="relative">
            <nuxt-link :to="`/blog/${post.slug}`">
                <nuxt-img class="w-full h-48 object-cover" :src="post.imageUrl" alt="{{ post.title }}"/>
            </nuxt-link>
            <small class="absolute left-4 bottom-4 text-xs text-amber-900 font-bold uppercase px-3 py-1 rounded-full bg-amber-300">{{ post.category.name }}</small>
        </div>

        <div class="p-6">
            <small class="flex justify-end text-sm text-gray-500">{{ post.createAt }}</small>
            <h5 class="font-semibold text-xl underline decoration-amber-400">{{ post.title }}</h5>
            <p class="text-xs md:text-base py-4">{{ post.description.substring(0,100) + '...' }}</p>
            <small class="flex justify-end">{{ post.author.name }}</small>
            <nuxt-link :to="`/blog/${post.slug}`" class="inline-block rounded bg-amber-500 px-4 pb-1 pt-1.5 text-xs font-medium uppercase leading-normal shadow-sm text-amber-100 hover:bg-amber-700 duration-200">
                Read More
            </nuxt-link>
        </div>
    </div>
</template>
