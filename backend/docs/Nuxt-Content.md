## Nuxt Content

This in the [...slug] file to show the content in the markdown file

```javascript
const { path } = useRoute();

const { data } = await useAsyncData(`content-${ path }`, () => {
 return queryContent()
     .where({ _path: path })
     .findOne()
})
```

This to render the template

```vue
<article class="mx-auto max-w-7xl text-gray-600 bg-amber-50 grow items-start w-full prose p-2 sm:p-6 lg:p-8">
    <ContentRenderer :value="data"/>
</article>
```


Make a component to render the blogpost

```vue
<PostCardMarkdown :posts="posts" />
```
and pass the post object, in pages directory /blog

```javascript
const { data: posts } = await useAsyncData('posts', () => {
     return queryContent('/blog').find()
});
```

In the Component for example `PostCardMarkdown` u place this:

```vue
<script setup>
const props = defineProps([ 'posts' ]);
</script>

<template>
    <div v-for="post in props.posts" :key="post.id"
         class="bg-white rounded-md shadow-md overflow-hidden">
        <div class="relative">
            <nuxt-link :to="post._path">
                <img class="w-full h-48 object-cover" :src="`/images/blog/${post.originalUrl}`" alt="{{ post.title }}"/>
            </nuxt-link>
            <small class="absolute left-4 bottom-4 text-xs text-amber-900 font-bold uppercase px-3 py-1 rounded-full bg-amber-300">{{ post.category }}</small>
        </div>

        <div class="p-6">
            <small class="flex justify-end text-sm text-gray-500">{{ post.createAt }}</small>
            <h5 class="font-semibold text-xl underline decoration-amber-400">{{ post.title }}</h5>
            <p class="text-xs md:text-base py-4">{{ post.description }}</p>
            <nuxt-link :to="post._path" class="inline-block rounded bg-blue-500 px-6 pb-2 pt-2.5 text-xs font-medium uppercase leading-normal text-white">
                Read More
            </nuxt-link>
        </div>
    </div>
</template>
```
Let say u want to show the tree latest blogposts on the homepage, put this on the page to fetch them:

```javascript
const { data: posts } = await useAsyncData('latest-posts', () => {
     return queryContent('/blog')
         .sort({ data: 1 })
         .limit(3)
         .find()
 })
```

