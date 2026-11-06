<script setup>
const { posts, status } = await useBlogPosts();

const latestPosts = computed(() => posts.value.slice(0, 3));

// Cards slide in from alternating sides
const directions = [ 'left', 'up', 'right' ];
</script>

<template>
    <div>
        <!-- Splash panel -->
        <section class="comic-panel comic-rays relative overflow-hidden bg-lavender px-6 pb-16 pt-24 text-center [--ray:var(--rose)] sm:px-10 sm:py-24 lg:py-28 lg:shadow-comic-lg">
            <div class="halftone pointer-events-none absolute inset-0" aria-hidden="true"></div>

            <ComicBurst v-reveal="{ from: 'pop', delay: 500 }" text="Pow!" class="absolute left-3 top-4 w-20 text-sm sm:left-8 sm:top-8 sm:w-28 sm:text-base lg:w-32 lg:text-lg"/>
            <ComicBurst v-reveal="{ from: 'pop', delay: 700 }" text="Bam!" color="var(--mint)" :spikes="12" class="absolute -bottom-2 right-2 w-20 text-sm sm:bottom-6 sm:right-8 sm:w-28 sm:text-base lg:w-32 lg:text-lg"/>

            <div class="relative">
                <p v-reveal="'down'" class="inline-block -rotate-2 rounded-lg border-3 border-line bg-lemon px-4 py-1 font-comic text-lg tracking-widest text-ink shadow-comic-sm sm:text-xl">
                    Issue #01 · The Codewave journal
                </p>

                <h1 v-reveal="{ from: 'down', delay: 150 }" class="mt-8">
                    <span class="block font-comic text-3xl tracking-widest text-ink-soft sm:text-4xl">Welcome to</span>
                    <span class="comic-title block text-7xl text-ink sm:text-8xl lg:text-9xl">Codewave</span>
                </h1>

                <!-- Speech bubble -->
                <div v-reveal="{ from: 'left', delay: 300 }" class="relative mx-auto mt-10 max-w-md rounded-[2rem] border-3 border-line bg-panel px-6 py-4 shadow-comic-sm">
                    <p class="text-lg font-semibold sm:text-xl">Your Gateway to the Digital World!</p>
                    <p class="mt-1 text-sm text-ink-soft">
                        <span class="dark:hidden">Heroes ship code. These are their stories.</span>
                        <span class="hidden dark:inline">Villains read the docs too. These are their secrets.</span>
                    </p>
                    <span class="absolute -bottom-4 left-1/2 h-7 w-7 -translate-x-1/2 rotate-45 border-b-3 border-r-3 border-line bg-panel" aria-hidden="true"></span>
                </div>

                <div v-reveal="{ from: 'up', delay: 450 }" class="mt-12">
                    <Button text="Read the blog" route="/blog"/>
                </div>
            </div>
        </section>

        <!-- Recent article -->
        <section class="mt-20 lg:mt-28">
            <ComicHeading v-reveal="'left'" kicker="Breaking news">Recent article</ComicHeading>

            <div v-reveal="{ from: 'right', delay: 100 }" class="comic-panel relative mt-8 p-6 sm:p-8 lg:p-12">
                <span class="absolute -right-3 -top-4 rotate-6 rounded-lg border-3 border-line bg-rose px-3 py-0.5 font-comic text-lg tracking-wider shadow-comic-sm">Hot off the press!</span>

                <article class="prose prose-comic lg:prose-lg mx-auto">
                    <span class="not-prose inline-block rounded-lg border-3 border-line bg-mint px-3 py-0.5 font-comic text-lg tracking-wider text-ink">Laravel 10.23</span>
                    <p>This week, the Laravel team released v10.23 with a new make:view Artisan command, support for PHPRedis 6.0, creating controller
                        test files while creating a model, and more.</p>

                    <h3>Add make:view Artisan command</h3>
                    <p>Nuno Maduro contributed a new make:view Artisan command to create a Laravel blade view at the given path. The path follows dot
                        notation similar to how view rendering works in the framework:</p>

                    <p><code class="rounded-md border-2 border-line bg-lavender px-2 py-1 text-ink before:content-none after:content-none">php artisan make:view users.index</code></p>
                </article>
            </div>
        </section>

        <!-- Latest posts -->
        <section class="mt-20 lg:mt-28">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <ComicHeading v-reveal="'left'" kicker="Fresh issues">Latest posts</ComicHeading>
                <NuxtLink v-reveal="'right'" to="/blog" class="group inline-flex items-center gap-1 rounded-full border-3 border-line bg-peach px-4 py-1 font-comic text-xl tracking-wider shadow-comic-sm transition hover:-translate-y-0.5 hover:bg-lemon">
                    View all
                    <Icon name="uil:arrow-right" class="transition-transform group-hover:translate-x-1"/>
                </NuxtLink>
            </div>

            <LoadingPanel v-if="status === 'idle' || status === 'pending'" class="mt-10"/>
            <div class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="(post, index) in latestPosts" :key="post.id" v-reveal="{ from: directions[index % 3], delay: index * 120 }">
                    <PostCard :post="post" :index="index"/>
                </div>
            </div>
        </section>
    </div>
</template>
