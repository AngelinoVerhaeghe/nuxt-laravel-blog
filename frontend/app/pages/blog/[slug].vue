<script setup>
const { apiBase } = useRuntimeConfig().public;
const route = useRoute();
const slug = route.params.slug;

// A markdown post in content/blog wins; any other slug comes from the API
const { data: markdownPost } = await useAsyncData(`blog-markdown-${ slug }`, () => queryCollection('blog').path(`/blog/${ slug }`).first());

const { data: post, status } = await useFetch(() => `${ apiBase }/posts/${ slug }`, {
    transform: (_post) => _post.data,
    server: false,
    immediate: !markdownPost.value
})
</script>

<template>
    <article class="prose lg:prose-lg prose-headings:font-fraunces prose-headings:font-medium prose-a:text-lavender-deep">
        <ContentRenderer v-if="markdownPost" :value="markdownPost"/>
        <div v-else-if="status === 'idle' || status === 'pending'">Loading...</div>
        <div v-else-if="post">
            <h2 class="text-2xl font-semibold mb-6">{{ post.title }}</h2>
            <nuxt-img v-if="post.imageUrl" class="w-full rounded-xl shadow-md h-48 object-cover mb-10" :src="post.imageUrl" :alt="post.title"/>
            <p class="">{{ post.description }}</p>
        </div>
    </article>
</template>
