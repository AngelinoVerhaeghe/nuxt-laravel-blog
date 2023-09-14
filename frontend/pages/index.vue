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
        <div class="grid grid-cols-5 gap-10">
            <div class="col-span-3">
                <h3 class="text-2xl font-merriweather underline decoration-lime-400 font-semibold text-white">Recent Article</h3>
                <article
                    class="text-white bg-white border-l-[2px] border-lime-400 prose lg:prose-lg bg-opacity-10 backdrop-blur-xl rounded-tr-lg rounded-br-lg mt-10 p-4 lg:p-8">
                    <h4 class="text-xl font-semibold text-lime-400 uppercase">Laravel 10.23</h4>
                    <p>This week, the Laravel team released v10.23 with a new make:view Artisan command, support for PHPRedis 6.0, creating controller
                        test files while creating a model, and more.</p>

                    <p class="text-xl font-semibold underline decoration-lime-400"> Add make:view Artisan command</p>
                    <p>Nuno Maduro contributed a new make:view Artisan command to create a Laravel blade view at the given path. The path follows dot
                        notation similar to how view rendering works in the framework:</p>

                    <p>php artisan make:view users.index</p>
                </article>
            </div>
            <aside class="col-span-2">
                <h2 class="font-merriweather text-lime-400 text-2xl font-semibold">Latest Blog Posts</h2>

                <div v-if="pending">
                    Loading ...
                </div>
                <section class="grid sm:grid-cols-2 gap-10 mt-8">
                    <PostCardApi v-for="post in posts" :key="post.id" :post="post"/>
                </section>
            </aside>
        </div>

    </section>
</template>
