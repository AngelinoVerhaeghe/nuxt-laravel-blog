<script setup>
const links = [
    { name: 'Github', href: 'https://github.com/AngelinoVerhaeghe', icon: 'uil:github'},
    { name: 'Twitter', href: 'https://twitter.com/AngelinoSonic', icon: 'uil:twitter'},
    { name: 'LinkedIn', href: 'https://www.linkedin.com/in/angelino-verhaeghe/', icon: 'uil:linkedin'}
]
</script>

<template>
    <footer class="bg-sky-900">
        <section class="mx-auto max-w-7xl text-white flex flex-row items-center justify-between p-2 sm:p-6 lg:p-8">
            <p>&copy; CodeWave 2023. All Rights Reserved.</p>
            <div class="flex flex-row justify-center space-x-4">
                <a v-for="link in links" :key="link.name" :href="link.href" target="_blank" rel="nofollow">
                    <Icon :name="link.icon" size="1.5rem" class="text-white" />
                </a>
            </div>
        </section>
    </footer>
</template>