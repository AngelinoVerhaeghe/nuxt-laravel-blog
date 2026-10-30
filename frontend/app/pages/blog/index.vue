<script setup>
const { posts, status } = await useBlogPosts();
</script>

<template>
    <h1 class="font-fraunces text-4xl font-medium">Blogs</h1>

    <section class="grid sm:grid-cols-2 gap-10 mt-8">
        <div v-if="status === 'idle' || status === 'pending'">
            <p class="text-ink-soft">Loading...</p>
        </div>
        <PostCard v-for="post in posts" :key="post.id" :post="post"/>
    </section>
</template>
