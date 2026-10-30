<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline'

const route = useRoute();

const navigation = [
    { name: 'Home', href: '/', current: route.name === 'index' },
    { name: 'Blog', href: '/blog', current: route.name.includes('blog') },
    { name: 'Projects', href: '/projects', current: route.name === 'projects' },
    { name: 'Cases', href: 'cases', current: route.name === 'cases' },
]
</script>

<template>
    <Disclosure as="nav" v-slot="{ open }">
        <div class="mx-auto max-w-6xl">
            <div class="relative flex h-16 items-center justify-between">
                <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                    <!-- Mobile menu button-->
                    <DisclosureButton class="relative inline-flex items-center justify-center rounded-md p-2 text-ink-soft hover:bg-lavender hover:text-ink focus:outline-hidden focus:ring-2 focus:ring-inset focus:ring-lavender-deep">
                        <span class="absolute -inset-0.5" />
                        <span class="sr-only">Open main menu</span>
                        <Bars3Icon v-if="!open" class="block h-6 w-6" aria-hidden="true" />
                        <XMarkIcon v-else class="block h-6 w-6" aria-hidden="true" />
                    </DisclosureButton>
                </div>
                <div class="flex flex-1 items-center justify-center sm:justify-between">
                    <a href="/" class="font-fraunces text-2xl font-medium italic text-ink">Codewave</a>
                    <div class="hidden sm:ml-6 sm:block">
                        <div class="flex space-x-10">
                            <a v-for="item in navigation" :key="item.name" :href="item.href" :class="[item.current ? 'text-ink' : 'text-ink-soft hover:text-ink', 'group text-sm font-semibold tracking-wide duration-200']" :aria-current="item.current ? 'page' : undefined">
                                {{ item.name }}
                                <span :class="[item.current ? 'max-w-full' : 'max-w-0 group-hover:max-w-full', 'block transition-all duration-500 h-0.5 rounded-full bg-lavender-deep']"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <DisclosurePanel class="sm:hidden">
            <div class="space-y-1 px-2 pb-3 pt-2">
                <DisclosureButton v-for="item in navigation" :key="item.name" as="a" :href="item.href" :class="[item.current ? 'bg-lavender text-ink' : 'text-ink-soft hover:bg-lavender/60 hover:text-ink', 'block rounded-xl px-3 py-2 text-base font-medium']" :aria-current="item.current ? 'page' : undefined">{{ item.name }}</DisclosureButton>
            </div>
        </DisclosurePanel>
    </Disclosure>
</template>
