<script setup>
const { posts, status } = await useBlogPosts();

const latestPosts = computed(() => posts.value.slice(0, 3));
</script>

<template>
    <div>
        <section class="relative overflow-hidden rounded-[2.5rem] bg-linear-to-br from-lavender via-rose to-peach px-6 py-20 text-center sm:py-28">
            <div class="absolute -left-16 -top-20 h-72 w-72 rounded-full bg-mint/80 blur-3xl" aria-hidden="true"></div>
            <div class="absolute -bottom-24 -right-10 h-80 w-80 rounded-full bg-white/60 blur-3xl" aria-hidden="true"></div>

            <div class="relative">
                <p class="inline-block rounded-full bg-white/60 px-4 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-lavender-deep">The Codewave journal</p>
                <h1 class="mt-6 font-fraunces text-4xl font-medium tracking-tight sm:text-6xl">
                    Welcome to <em class="text-lavender-deep">Codewave</em>
                </h1>
                <p class="mx-auto mt-5 max-w-xl text-lg text-ink-soft">Your Gateway to the Digital World!</p>
                <div class="mt-10">
                    <Button text="Read the blog" route="/blog"/>
                </div>
            </div>
        </section>

        <section class="mt-20">
            <h2 class="font-fraunces text-3xl font-medium">Recent article</h2>
            <div class="mt-8 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-ink/5 lg:p-12">
                <article class="prose lg:prose-lg mx-auto">
                    <span class="not-prose inline-block rounded-full bg-mint px-3 py-1 text-xs font-semibold uppercase tracking-wider text-mint-deep">Laravel 10.23</span>
                    <p>This week, the Laravel team released v10.23 with a new make:view Artisan command, support for PHPRedis 6.0, creating controller
                        test files while creating a model, and more.</p>

                    <h3 class="font-fraunces font-medium">Add make:view Artisan command</h3>
                    <p>Nuno Maduro contributed a new make:view Artisan command to create a Laravel blade view at the given path. The path follows dot
                        notation similar to how view rendering works in the framework:</p>

                    <p><code class="rounded-md bg-lavender px-2 py-1 text-lavender-deep before:content-none after:content-none">php artisan make:view users.index</code></p>
                </article>
            </div>
        </section>

        <section class="mt-20">
            <div class="flex items-baseline justify-between">
                <h2 class="font-fraunces text-3xl font-medium">Latest posts</h2>
                <nuxt-link to="/blog" class="text-sm font-semibold text-lavender-deep hover:text-ink duration-200">View all &rarr;</nuxt-link>
            </div>

            <p v-if="status === 'idle' || status === 'pending'" class="mt-8 text-ink-soft">
                Loading ...
            </p>
            <div class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <PostCard v-for="post in latestPosts" :key="post.id" :post="post"/>
            </div>
        </section>
    </div>
</template>
