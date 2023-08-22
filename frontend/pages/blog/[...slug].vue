<script setup>
const { path } = useRoute();

const { data } = await useAsyncData(`content-${ path }`, () => {
    return queryContent()
        .where({ _path: path })
        .findOne()
})
</script>

<template>
    <article class="mx-auto max-w-7xl text-gray-600 bg-amber-50 grow items-start w-full prose p-2 sm:p-6 lg:p-8">
        <ContentRenderer :value="data"/>
    </article>
</template>