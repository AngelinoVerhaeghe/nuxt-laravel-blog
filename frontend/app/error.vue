<script setup lang="ts">
import type { NuxtError } from '#app'

const props = defineProps<{ error: NuxtError }>()

const notFound = computed(() => props.error.statusCode === 404)

useHead({ title: () => notFound.value ? 'Page not found' : 'Something went wrong' })
</script>

<template>
    <div class="flex min-h-screen flex-col overflow-x-clip font-quicksand text-ink antialiased">
        <CookieBanner/>
        <TheNavigation/>

        <main class="w-full flex-1 px-4 pt-12 sm:px-6 lg:px-10 lg:pt-20">
            <div class="mx-auto max-w-3xl">
                <section class="comic-panel comic-rays relative overflow-hidden bg-rose px-6 py-16 text-center [--ray:var(--peach)] sm:px-12">
                    <div class="halftone pointer-events-none absolute inset-0" aria-hidden="true"></div>

                    <div class="relative">
                        <ComicBurst v-reveal="'pop'" :text="String(error.statusCode)" :spikes="16" class="mx-auto w-40 text-2xl sm:w-48 sm:text-3xl"/>
                        <h1 v-reveal="{ from: 'down', delay: 150 }" class="comic-title mt-8 text-5xl sm:text-6xl">
                            {{ notFound ? 'This page got zapped!' : 'Our hero tripped!' }}
                        </h1>
                        <p v-reveal="{ from: 'up', delay: 300 }" class="mx-auto mt-5 max-w-md text-lg text-ink-soft">
                            {{ notFound ? 'A villain made off with this page, or it was never drawn in the first place.' : 'Something went wrong on our side. Try again in a moment.' }}
                        </p>
                        <div v-reveal="{ from: 'up', delay: 450 }" class="mt-10">
                            <button
                                type="button"
                                class="group inline-flex items-center gap-2 rounded-2xl border-3 border-line bg-ink px-7 py-3 font-comic text-2xl tracking-wider text-paper shadow-comic transition hover:-translate-y-1 hover:bg-lavender-deep active:translate-x-1 active:translate-y-1 active:shadow-none dark:bg-lavender-deep"
                                @click="clearError({ redirect: '/' })"
                            >
                                <Icon name="uil:rocket"/>
                                Fly back home
                            </button>
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <TheFooter/>
    </div>
</template>
