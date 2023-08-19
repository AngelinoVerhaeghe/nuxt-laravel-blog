<template>
    <div class="flex flex-col font-quicksand antialiased scroll-smooth min-h-screen">
        <TheNavigation />
        <main class="mx-auto max-w-7xl text-gray-600 bg-amber-50 grow items-start w-full p-2 sm:p-6 lg:p-8">
            <NuxtPage/>
        </main>
        <TheFooter />
    </div>
</template>
