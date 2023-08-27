<script setup>
const { data: posts, pending } = await useFetch(() => `${ import.meta.env.VITE_API_BASE_URL }/posts`, {
    transform: (_posts) => _posts.data,
    server: false,
    headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json'
    }
})
</script>

<template>
    <section>
        <h2 class="text-2xl font-semibold">Latest Blog Posts</h2>

        <div v-if="pending">
            Loading ...
        </div>
        <section class="grid sm:grid-cols-2 gap-10 mt-8">
            <PostCardApi v-for="post in posts" :key="post.id" :post="post"/>
        </section>
    </section>
</template>
