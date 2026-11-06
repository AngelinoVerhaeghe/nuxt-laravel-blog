<script setup>
useHead({ title: 'Blog' });

const { posts, status } = await useBlogPosts();

const directions = [ 'left', 'up', 'right' ];
</script>

<template>
    <div>
        <section class="comic-panel comic-rays relative overflow-hidden bg-mint px-6 py-12 [--ray:var(--sky)] sm:px-10 sm:py-16">
            <div class="halftone pointer-events-none absolute inset-0" aria-hidden="true"></div>
            <ComicBurst v-reveal="{ from: 'pop', delay: 300 }" text="Kaboom!" color="var(--peach)" :spikes="16" class="absolute -right-4 -top-4 hidden w-36 text-base sm:block lg:right-6 lg:top-4 lg:w-44 lg:text-lg"/>

            <div class="relative max-w-2xl">
                <ComicHeading v-reveal="'down'" as="h1" kicker="The complete archive">The Blog</ComicHeading>
                <p v-reveal="{ from: 'left', delay: 150 }" class="mt-5 text-lg text-ink-soft">
                    Every issue, newest first.
                    <span class="dark:hidden">Grab your cape and start reading.</span>
                    <span class="hidden dark:inline">Pull up a chair in the lair and start reading.</span>
                </p>
            </div>
        </section>

        <section class="mt-12">
            <LoadingPanel v-if="status === 'idle' || status === 'pending'"/>
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3 lg:gap-10">
                <div v-for="(post, index) in posts" :key="post.id" v-reveal="{ from: directions[index % 3], delay: (index % 3) * 120 }">
                    <PostCard :post="post" :index="index"/>
                </div>
            </div>
        </section>
    </div>
</template>
