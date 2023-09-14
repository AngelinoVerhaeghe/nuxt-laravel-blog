<script setup>
const links = [
    { name: 'Github', href: 'https://github.com/AngelinoVerhaeghe', icon: 'uil:github'},
    { name: 'Twitter', href: 'https://twitter.com/AngelinoSonic', icon: 'uil:twitter'},
    { name: 'LinkedIn', href: 'https://www.linkedin.com/in/angelino-verhaeghe/', icon: 'uil:linkedin'}
]
</script>

<template>
    <footer class="font-semibold text-white md:p-8 lg:p-10">
        <div class="mx-auto max-w-screen-xl text-center">
            <a href="/" class="flex justify-center items-center text-2xl font-semibold">
                Codewave
            </a>
            <p class="text-lime-400 my-6">Exploring life's stories and wisdom.</p>
            <ul class="flex flex-wrap justify-center items-center space-x-4 mb-6">
                <li v-for="link in links" :key="link.name">
                    <a :href="link.href" target="_blank" rel="nofollow">
                        <Icon :name="link.icon" size="1.5rem" />
                    </a>
                </li>
            </ul>
            <span class="text-sm sm:text-center">© 2023 <a href="#" class="hover:underline">Codewave</a>. All Rights Reserved.</span>
        </div>
    </footer>
</template>