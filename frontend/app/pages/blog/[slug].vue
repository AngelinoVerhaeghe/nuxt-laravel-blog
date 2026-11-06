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

// Category, date and author in the same shape for both kinds of post
const meta = computed(() => markdownPost.value ? fromMarkdown(markdownPost.value) : post.value);

useHead({ title: () => meta.value?.title });
</script>

<template>
    <div class="mx-auto max-w-3xl">
        <NuxtLink v-reveal="'left'" to="/blog" class="group inline-flex items-center gap-1 rounded-full border-3 border-line bg-panel px-4 py-1 font-comic text-xl tracking-wider shadow-comic-sm transition hover:-translate-y-0.5 hover:bg-peach">
            <Icon name="uil:arrow-left" class="transition-transform group-hover:-translate-x-1"/>
            Back to the blog
        </NuxtLink>

        <LoadingPanel v-if="!markdownPost && (status === 'idle' || status === 'pending')" class="mt-10"/>

        <article v-else-if="meta" v-reveal="{ from: 'up', delay: 100 }" class="comic-panel relative mt-10 px-5 pb-10 pt-12 sm:px-10 lg:px-14">
            <span class="absolute -top-5 left-5 -rotate-2 rounded-lg border-3 border-line bg-lemon px-3 py-0.5 font-comic text-xl tracking-wider text-ink shadow-comic-sm sm:left-10">
                {{ meta.category.name }}
            </span>

            <p class="text-xs font-semibold uppercase tracking-widest text-ink-soft">{{ meta.createAt }} &middot; By {{ meta.author.name }}</p>

            <div class="prose prose-comic lg:prose-lg mt-4 max-w-none">
                <ContentRenderer v-if="markdownPost" :value="markdownPost"/>
                <template v-else>
                    <h1>{{ post.title }}</h1>
                    <nuxt-img v-if="post.imageUrl" class="h-56 w-full object-cover sm:h-72" :src="post.imageUrl" :alt="post.title"/>
                    <p>{{ post.description }}</p>
                </template>
            </div>
        </article>

        <div v-else v-reveal="'pop'" class="comic-panel mt-10 p-10 text-center">
            <p class="comic-title text-5xl">Post not found</p>
            <p class="mt-3 text-ink-soft">This issue vanished from the archive.</p>
        </div>
    </div>
</template>
