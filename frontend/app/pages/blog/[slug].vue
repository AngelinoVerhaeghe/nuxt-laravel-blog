<script setup>
const { apiBase } = useRuntimeConfig().public;
const route = useRoute();
const slug = route.params.slug;

const { data: post, status } = await useFetch(() => `${ apiBase }/posts/${ slug }`, {
    transform: (_post) => _post.data,
    server: false
})
</script>

<template>
    <article class="prose prose-invert lg:prose-lg">
        <div v-if="status === 'idle' || status === 'pending'">Loading...</div>
        <div v-else-if="post">
            <h2 class="text-2xl font-semibold mb-6">{{ post.title }}</h2>
            <nuxt-img v-if="post.imageUrl" class="w-full rounded-xl shadow-md h-48 object-cover mb-10" :src="post.imageUrl" :alt="post.title"/>
            <p class="">{{ post.description }}</p>
        </div>
    </article>
</template>
