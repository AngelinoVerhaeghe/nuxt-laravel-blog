<script setup>
const { data: posts, pending } = await useFetch(() => `${ import.meta.env.VITE_API_BASE_URL }/posts`, {
    transform: (_posts) => _posts.data,
    server: false
})
</script>

<template>
    <h1 class="text-3xl">Blogs</h1>

    <section class="grid sm:grid-cols-2 gap-10 mt-8">
        <div v-if="pending">
            <p>Loading...</p>
        </div>
        <PostCardApi v-for="post in posts" :key="post.id" :post="post"/>
    </section>
</template>
