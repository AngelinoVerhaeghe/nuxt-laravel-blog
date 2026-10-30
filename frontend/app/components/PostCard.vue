<script setup>
defineProps({
    post: {
        type: Object,
        required: true
    }
})
</script>

<template>
    <div class="group flex flex-col rounded-3xl bg-white shadow-sm ring-1 ring-ink/5 overflow-hidden hover:-translate-y-1 hover:shadow-xl hover:shadow-lavender-deep/10 duration-300">
        <div class="relative">
            <nuxt-link :to="`/blog/${post.slug}`">
                <nuxt-img v-if="post.imageUrl" class="w-full h-48 object-cover" :src="post.imageUrl" :alt="post.title"/>
            </nuxt-link>
            <small :class="post.imageUrl ? 'absolute left-4 bottom-4' : 'inline-block ml-6 mt-6'" class="text-xs text-lavender-deep font-semibold uppercase tracking-wider px-3 py-1 rounded-full bg-lavender">{{ post.category.name }}</small>
        </div>

        <div class="flex flex-1 flex-col p-6">
            <small class="text-xs text-ink-soft">{{ post.createAt }} &middot; {{ post.author.name }}</small>
            <h5 class="mt-2 font-fraunces text-xl font-medium leading-snug">{{ post.title }}</h5>
            <p class="text-sm text-ink-soft leading-relaxed py-4">{{ post.description.substring(0,100) + '...' }}</p>
            <nuxt-link :to="`/blog/${post.slug}`" class="mt-auto text-sm font-semibold text-lavender-deep hover:text-ink duration-200">
                Read more &rarr;
            </nuxt-link>
        </div>
    </div>
</template>
