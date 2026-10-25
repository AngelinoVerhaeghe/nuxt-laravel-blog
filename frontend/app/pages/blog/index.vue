<script setup>
const { apiBase } = useRuntimeConfig().public;
const { data: posts, status } = await useFetch(() => `${ apiBase }/posts`, {
    transform: (_posts) => _posts.data,
    server: false
})
</script>

<template>
    <h1 class="text-3xl text-white">Blogs</h1>

    <section class="grid sm:grid-cols-2 gap-10 mt-8">
        <div v-if="status === 'idle' || status === 'pending'">
            <p>Loading...</p>
        </div>
        <PostCardApi v-for="post in posts" :key="post.id" :post="post"/>
    </section>
</template>
