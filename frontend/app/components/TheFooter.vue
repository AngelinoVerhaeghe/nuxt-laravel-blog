<script setup>
const links = [
    { name: 'Github', href: 'https://github.com/AngelinoVerhaeghe', icon: 'uil:github'},
    { name: 'Twitter', href: 'https://twitter.com/AngelinoSonic', icon: 'uil:twitter'},
    { name: 'LinkedIn', href: 'https://www.linkedin.com/in/angelino-verhaeghe/', icon: 'uil:linkedin'}
]
</script>

<template>
    <footer class="mt-24 border-t border-ink/10 pt-12 pb-6 md:pb-8">
        <div class="mx-auto max-w-(--breakpoint-xl) text-center">
            <a href="/" class="flex justify-center items-center font-fraunces text-2xl font-medium italic">
                Codewave
            </a>
            <p class="text-ink-soft my-6">Exploring life's stories and wisdom.</p>
            <ul class="flex flex-wrap justify-center items-center space-x-4 mb-6">
                <li v-for="link in links" :key="link.name">
                    <a :href="link.href" target="_blank" rel="nofollow" class="text-ink-soft hover:text-lavender-deep duration-200">
                        <Icon :name="link.icon" size="1.5rem" />
                    </a>
                </li>
            </ul>
            <span class="text-sm text-ink-soft sm:text-center">© 2023 <a href="#" class="hover:underline">Codewave</a>. All Rights Reserved.</span>
        </div>
    </footer>
</template>