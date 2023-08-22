<script setup>
const { data: posts } = await useAsyncData('posts', () => {
    return queryContent('/blog').find()
});
</script>

<template>
    <h1 class="text-3xl">Blog</h1>

    <p> Navigeer door de Code: Ontdekkingen en Inzichten in de Developmentwereld. Of je nu een doorgewinterde programmeur bent of net
        begint te leren coderen, hier vind je een schat aan informatie, inzichten en inspiratie om je reis in de developmentwereld te
        verrijken.</p>

   <section class="grid md:grid-cols-3 gap-10 mt-8">
       <Post :posts="posts" />
   </section>
</template>