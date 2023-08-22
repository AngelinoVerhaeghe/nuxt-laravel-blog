<script setup>
const props = defineProps([ 'posts' ]);
</script>

<template>
    <div v-for="post in posts" :key="post.id"
         class="bg-white rounded-md shadow-md overflow-hidden">
        <div class="relative">
            <nuxt-link :to="post._path">
                <img class="w-full h-48 object-cover" :src="`/images/blog/${post.originalUrl}`" alt="{{ post.title }}"/>
            </nuxt-link>
            <small class="absolute left-4 bottom-4 text-xs text-amber-900 font-bold uppercase px-3 py-1 rounded-full bg-amber-300">{{ post.category }}</small>
        </div>

        <div class="p-6">
            <small class="flex justify-end text-sm text-gray-500">{{ post.createAt }}</small>
            <h5 class="font-semibold text-xl underline decoration-amber-400">{{ post.title }}</h5>
            <p class="text-xs md:text-base py-4">{{ post.description }}</p>
            <nuxt-link :to="post._path" class="inline-block rounded bg-blue-500 px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white shadow-[0_4px_9px_-4px_#3b71ca] transition duration-150 ease-in-out hover:bg-blue-600 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-blue-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-blue-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)]">
                Read More
            </nuxt-link>
        </div>
    </div>
</template>