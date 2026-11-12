<script setup lang="ts">
import { cookieCategories, type OptionalCategory } from '~/composables/useCookieConsent'

// The first-visit banner and the settings dialog. GDPR/ePrivacy rules it follows:
// nothing optional runs before a yes, "Reject all" is as easy and as visible as
// "Accept all", no box is pre-ticked, the site stays usable without answering, and
// the choice can be changed at any time (footer and cookie policy page).
const { consent, answered, settingsOpen, acceptAll, rejectAll, save, openSettings, closeSettings } = useCookieConsent()

const dialog = ref<HTMLDialogElement>()
const choices = reactive<Record<OptionalCategory, boolean>>({ analytics: false, marketing: false })

watch(settingsOpen, (open) => {
    if (!dialog.value) return
    if (open) {
        // Start from what was saved before, or everything off
        for (const key of Object.keys(choices) as OptionalCategory[]) {
            choices[key] = consent.value?.categories[key] === true
        }
        if (!dialog.value.open) dialog.value.showModal()
    } else if (dialog.value.open) {
        dialog.value.close()
    }
// After the render, so the content exists when showModal() picks what to focus
}, { flush: 'post' })

const savedOn = computed(() => consent.value
    ? new Date(consent.value.date).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' })
    : null)

// Close when clicking the backdrop (outside the panel)
function closeOnBackdrop(event: MouseEvent) {
    if (event.target === dialog.value) closeSettings()
}

</script>

<template>
    <div data-cookie-banner>
        <Transition
            enter-active-class="transition duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)] motion-reduce:transition-none"
            enter-from-class="translate-y-full opacity-0"
            leave-active-class="transition duration-300 ease-in motion-reduce:transition-none"
            leave-to-class="translate-y-full opacity-0"
        >
            <section
                v-if="!answered"
                aria-labelledby="cookie-banner-title"
                aria-describedby="cookie-banner-text"
                class="fixed inset-x-0 bottom-0 z-50 px-4 pb-[max(1rem,env(safe-area-inset-bottom))] sm:px-6 lg:px-10"
            >
                <div class="comic-panel relative mx-auto max-w-4xl bg-panel px-5 pb-5 pt-9 sm:px-8 sm:pb-6">
                    <h2 id="cookie-banner-title" class="absolute -top-5 left-5 -rotate-2 rounded-lg border-3 border-line bg-lemon px-4 py-1 font-comic text-xl tracking-wider text-ink shadow-comic-sm sm:left-8">
                        Cookie check!
                    </h2>

                    <ComicCookie class="absolute -right-3 -top-8 w-16 rotate-12 animate-wiggle [--tilt:10deg] sm:-right-5 sm:w-20"/>

                    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:gap-8">
                        <p id="cookie-banner-text" class="flex-1 text-ink-soft sm:pr-10 lg:pr-0">
                            We only store what keeps this site running, like your hero or villain mode.
                            Statistics or marketing cookies only ever run if you say yes, and you can change your mind any time
                            under <strong class="text-ink">Cookie settings</strong> at the bottom of the page.
                            <NuxtLink to="/cookies" class="font-semibold text-lavender-deep underline decoration-2 underline-offset-2 hover:text-rose-deep">Read the cookie policy</NuxtLink>.
                        </p>

                        <!-- Reject and accept look exactly the same: neither is the "easy" button -->
                        <div class="grid shrink-0 grid-cols-2 gap-3 sm:flex sm:flex-wrap sm:items-center">
                            <button type="button" class="cookie-choice" @click="rejectAll">Reject all</button>
                            <button type="button" class="cookie-choice" @click="acceptAll">Accept all</button>
                            <button type="button" class="cookie-secondary col-span-2" @click="openSettings">
                                <Icon name="uil:setting"/>
                                Let me choose
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </Transition>

        <!-- Once answered, a small cookie in the corner reopens the settings from any page -->
        <Transition
            enter-active-class="transition delay-300 duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)] motion-reduce:transition-none"
            enter-from-class="scale-0 -rotate-90 opacity-0"
            leave-active-class="transition duration-200 ease-in motion-reduce:transition-none"
            leave-to-class="scale-0 opacity-0"
        >
            <button
                v-if="answered"
                type="button"
                class="group fixed bottom-[max(1rem,env(safe-area-inset-bottom))] left-4 z-40 inline-flex items-center rounded-full border-3 border-line bg-lemon p-1 text-ink shadow-comic-sm transition hover:-translate-y-0.5 hover:bg-peach active:translate-x-0.5 active:translate-y-0.5 active:shadow-none sm:left-6 lg:left-10 dark:bg-lavender"
                aria-haspopup="dialog"
                @click="openSettings"
            >
                <ComicCookie class="w-9 transition-transform duration-500 group-hover:rotate-[20deg] motion-reduce:transition-none"/>
                <!-- The label slides out on hover and keyboard focus; screen readers always hear it -->
                <span class="max-w-0 overflow-hidden whitespace-nowrap font-comic text-lg tracking-wider transition-[max-width,padding] duration-300 group-hover:max-w-40 group-hover:px-2 group-focus-visible:max-w-40 group-focus-visible:px-2">
                    Cookie settings
                </span>
            </button>
        </Transition>

        <dialog
            ref="dialog"
            aria-labelledby="cookie-settings-title"
            class="comic-panel m-auto max-h-[calc(100dvh-2rem)] w-[calc(100%-2rem)] max-w-xl overflow-visible bg-panel p-0 text-ink backdrop:bg-[rgb(21_17_30/0.6)]"
            @close="closeSettings"
            @click="closeOnBackdrop"
        >
            <div v-if="settingsOpen" class="halftone max-h-[calc(100dvh-2rem)] overflow-y-auto rounded-[1rem] px-5 pb-6 pt-8 sm:px-8">
                <button
                    type="button"
                    class="absolute -right-3 -top-3 flex h-10 w-10 items-center justify-center rounded-full border-3 border-line bg-rose shadow-comic-sm transition hover:rotate-90 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none"
                    @click="closeSettings"
                >
                    <span class="sr-only">Close without saving</span>
                    <Icon name="uil:times" size="1.4rem"/>
                </button>

                <div class="flex items-center gap-4">
                    <ComicCookie class="w-12 shrink-0 -rotate-12"/>
                    <h2 id="cookie-settings-title" class="comic-title text-4xl sm:text-5xl">Your cookie choices</h2>
                </div>

                <p class="mt-4 text-ink-soft">
                    Strictly necessary storage is always on. Everything else stays off until you switch it on, and you can switch it off again here whenever you like.
                    <NuxtLink to="/cookies" class="font-semibold text-lavender-deep underline decoration-2 underline-offset-2 hover:text-rose-deep" @click="closeSettings">Cookie policy</NuxtLink>
                </p>

                <ul class="mt-6 space-y-4">
                    <li
                        v-for="category in cookieCategories"
                        :key="category.id"
                        class="rounded-xl border-3 border-line p-4 shadow-comic-sm"
                        :class="category.required ? 'bg-mint' : 'bg-lavender'"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 :id="`cookie-category-${ category.id }`" class="font-comic text-2xl tracking-wider">{{ category.name }}</h3>
                                <p :id="`cookie-category-${ category.id }-text`" class="mt-1 text-sm text-ink-soft">{{ category.description }}</p>
                                <p v-if="!category.required" class="mt-2 text-xs font-semibold uppercase tracking-widest text-ink-soft">
                                    {{ category.services.length ? `Used by: ${ category.services.map(service => service.name).join(', ') }` : 'Not in use on this site yet' }}
                                </p>
                            </div>

                            <span v-if="category.required" class="shrink-0 rounded-full border-3 border-line bg-panel px-3 py-0.5 font-comic text-lg tracking-wider">Always on</span>
                            <button
                                v-else
                                type="button"
                                role="switch"
                                :aria-checked="choices[category.id as OptionalCategory]"
                                :aria-labelledby="`cookie-category-${ category.id }`"
                                :aria-describedby="`cookie-category-${ category.id }-text`"
                                class="relative mt-1 h-9 w-17 shrink-0 rounded-full border-3 border-line transition-colors"
                                :class="choices[category.id as OptionalCategory] ? 'bg-mint-deep' : 'bg-panel'"
                                @click="choices[category.id as OptionalCategory] = !choices[category.id as OptionalCategory]"
                            >
                                <span
                                    class="absolute left-0.5 top-0.5 flex h-6 w-6 items-center justify-center rounded-full border-3 border-line bg-lemon transition-transform duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)] motion-reduce:transition-none"
                                    :class="choices[category.id as OptionalCategory] && 'translate-x-8'"
                                >
                                    <Icon :name="choices[category.id as OptionalCategory] ? 'uil:check' : 'uil:times'" size="0.85rem" class="text-ink"/>
                                </span>
                            </button>
                        </div>
                    </li>
                </ul>

                <p v-if="savedOn" class="mt-5 text-sm text-ink-soft">Your current choice was saved on {{ savedOn }}.</p>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <button type="button" class="cookie-choice" @click="rejectAll">Reject all</button>
                    <button type="button" class="cookie-choice" @click="acceptAll">Accept all</button>
                    <button type="button" class="cookie-secondary col-span-2" @click="save({ ...choices })">
                        <Icon name="uil:check"/>
                        Save my choices
                    </button>
                </div>
            </div>
        </dialog>
    </div>
</template>

<style scoped>
@reference '~/assets/css/main.css';

.cookie-choice {
    @apply inline-flex items-center justify-center gap-2 rounded-xl border-3 border-line bg-ink px-5 py-2 font-comic text-xl tracking-wider text-paper shadow-comic-sm transition hover:-translate-y-0.5 hover:bg-lavender-deep active:translate-x-0.5 active:translate-y-0.5 active:shadow-none dark:bg-lavender-deep dark:hover:bg-rose-deep;
}

.cookie-secondary {
    @apply inline-flex items-center justify-center gap-2 rounded-xl border-3 border-line bg-panel px-5 py-2 font-comic text-xl tracking-wider text-ink shadow-comic-sm transition hover:-translate-y-0.5 hover:bg-peach active:translate-x-0.5 active:translate-y-0.5 active:shadow-none;
}
</style>
