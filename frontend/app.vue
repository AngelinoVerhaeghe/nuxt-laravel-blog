<template>
    <div class="bg-black font-quicksand antialiased scroll-smooth min-h-screen">
        <div class="container mx-auto text-gray-900 p-6 lg:p-10">
            <TheNavigation/>

            <div class="absolute top-52">
                <h1 class="font-merriweather text-white text-3xl font-bold">Welcome to Codewave</h1>
                <p class="font-quicksand text-lg text-lime-200 mt-3">Your Gateway to the Digital World!</p>
            </div>

            <main class="mt-[250px] p-2 sm:p-6 lg:p-8">
                <NuxtPage/>
            </main>

            <TheFooter/>
        </div>
    </div>
</template>
<script setup lang="ts">
</script>