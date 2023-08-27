<script setup>
const route = useRoute();
const slug = route.params.slug;

const { data: post, pending } = await useFetch(() => `${ import.meta.env.VITE_API_BASE_URL }/posts/${ slug }`, {
    transform: (_post) => _post.data,
    server: false
})
</script>

<template>
    <article class="prose lg:prose-lg">
        <div v-if="pending">Loading...</div>
        <div>
            <h2 class="text-2xl font-semibold mb-6">{{ post.title }}</h2>
            <nuxt-img class="w-full rounded-xl shadow-md h-48 object-cover mb-10" :src="post.imageUrl" alt="{{ post.title }}"/>
            <p class="">{{ post.description }}</p>
        </div>
    </article>
</template>
