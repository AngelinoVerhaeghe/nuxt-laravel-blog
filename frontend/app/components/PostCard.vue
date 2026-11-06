<script setup>
const props = defineProps({
    post: {
        type: Object,
        required: true
    },
    // Rotates through pastel badge colors so a grid of cards isn't one note
    index: {
        type: Number,
        default: 0
    }
})

const badgeColors = [ 'bg-lemon', 'bg-mint', 'bg-rose', 'bg-sky', 'bg-peach' ]
const badgeColor = computed(() => badgeColors[props.index % badgeColors.length])
</script>

<template>
    <article class="group comic-panel relative flex h-full flex-col overflow-hidden transition duration-300 hover:-translate-y-1.5 hover:-rotate-1 hover:shadow-comic-lg">
        <NuxtLink :to="`/blog/${ post.slug }`" class="relative block border-b-3 border-line" tabindex="-1" aria-hidden="true">
            <nuxt-img v-if="post.imageUrl" class="h-48 w-full object-cover transition duration-500 group-hover:scale-105" :src="post.imageUrl" :alt="post.title"/>
            <div v-else class="comic-rays h-32 w-full bg-lavender [--ray:var(--panel)]"></div>
        </NuxtLink>

        <span :class="badgeColor" class="absolute left-4 top-4 -rotate-3 rounded-lg border-3 border-line px-3 py-0.5 font-comic text-lg tracking-wider text-ink shadow-comic-sm">
            {{ post.category.name }}
        </span>

        <div class="flex flex-1 flex-col p-6">
            <p class="text-xs font-semibold uppercase tracking-widest text-ink-soft">{{ post.createAt }} &middot; {{ post.author.name }}</p>
            <h3 class="mt-2 font-comic text-3xl leading-none tracking-wide">
                <NuxtLink :to="`/blog/${ post.slug }`" class="transition hover:text-lavender-deep">{{ post.title }}</NuxtLink>
            </h3>
            <p class="py-4 text-sm leading-relaxed text-ink-soft">{{ post.description.length > 100 ? post.description.substring(0, 100) + '…' : post.description }}</p>
            <NuxtLink :to="`/blog/${ post.slug }`" class="mt-auto inline-flex items-center gap-1 self-start rounded-full border-3 border-line bg-lavender px-4 py-1 font-comic text-lg tracking-wider text-ink transition group-hover:bg-lemon">
                Read more
                <Icon name="uil:arrow-right" class="transition-transform group-hover:translate-x-1"/>
            </NuxtLink>
        </div>
    </article>
</template>
