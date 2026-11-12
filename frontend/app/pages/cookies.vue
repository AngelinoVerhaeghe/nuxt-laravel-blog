<script setup lang="ts">
import { CONSENT_COOKIE, cookieCategories } from '~/composables/useCookieConsent'

useHead({ title: 'Cookie policy' })

const { openSettings } = useCookieConsent()

// Update this date, and the tables below, whenever what the site stores changes
const updatedOn = '12 November 2026'

// Everything the site stores in your browser. Keep this in sync with the
// categories and services in useCookieConsent.ts.
const necessary = [
    { name: CONSENT_COOKIE, type: 'Cookie', purpose: 'Remembers your cookie choices and when you made them, so we don\'t ask on every page.', duration: '6 months' },
    { name: 'codewave-color-mode', type: 'Local storage', purpose: 'Remembers hero (light) or villain (dark) mode, or that you follow your device\'s setting.', duration: 'Until you clear it' },
    { name: 'content_*', type: 'Local storage', purpose: 'May be stored while you browse: a copy of the blog posts, so pages open quickly.', duration: 'Until you clear it' },
]

const optional = cookieCategories.filter(category => !category.required)
</script>

<template>
    <div class="mx-auto max-w-4xl">
        <section class="comic-panel comic-rays relative overflow-hidden bg-peach px-6 py-12 [--ray:var(--lemon)] sm:px-10 sm:py-16">
            <div class="halftone pointer-events-none absolute inset-0" aria-hidden="true"></div>
            <ComicCookie v-reveal="{ from: 'pop', delay: 300 }" class="absolute -right-2 top-6 hidden w-28 rotate-12 sm:block lg:right-10 lg:w-36"/>

            <div class="relative max-w-2xl">
                <ComicHeading v-reveal="'down'" as="h1" kicker="The fine print">Cookie policy</ComicHeading>
                <p v-reveal="{ from: 'left', delay: 150 }" class="mt-5 text-lg text-ink-soft">
                    What Codewave stores in your browser, why, and how to change your mind. No secret identities here.
                </p>
                <button v-reveal="{ from: 'up', delay: 300 }" type="button" class="group mt-8 inline-flex items-center gap-2 rounded-2xl border-3 border-line bg-ink px-6 py-2.5 font-comic text-2xl tracking-wider text-paper shadow-comic transition hover:-translate-y-1 hover:bg-lavender-deep active:translate-x-1 active:translate-y-1 active:shadow-none dark:bg-lavender-deep dark:hover:bg-rose-deep" @click="openSettings">
                    <Icon name="uil:setting" class="transition-transform group-hover:rotate-90"/>
                    Cookie settings
                </button>
            </div>
        </section>

        <article v-reveal="{ from: 'up', delay: 100 }" class="comic-panel relative mt-12 px-5 pb-10 pt-12 sm:px-10">
            <span class="absolute -top-5 left-5 -rotate-2 rounded-lg border-3 border-line bg-lemon px-3 py-0.5 font-comic text-xl tracking-wider text-ink shadow-comic-sm sm:left-10">
                Last updated {{ updatedOn }}
            </span>

            <div class="prose prose-comic max-w-none">
                <h2>What cookies are</h2>
                <p>
                    Cookies and local storage are small bits of information a website keeps in your browser. Some are needed to make the site work.
                    Others are used to measure visits or show ads, and those need your permission first. That's the law in the EU
                    (the GDPR and the ePrivacy rules), and it's also just good manners.
                </p>

                <h2>What we store without asking</h2>
                <p>
                    These are strictly necessary: the site needs them to do what you ask it to, so the law doesn't require your permission.
                    None of them track you or are shared with anyone.
                </p>
                <div class="not-prose overflow-x-auto rounded-xl border-3 border-line">
                    <table class="w-full min-w-xl text-left text-sm">
                        <thead class="bg-mint font-comic text-lg tracking-wider">
                            <tr>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Type</th>
                                <th class="px-4 py-2">What it does</th>
                                <th class="px-4 py-2">Kept for</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in necessary" :key="item.name" class="border-t-3 border-line align-top">
                                <td class="px-4 py-3 font-mono text-xs font-semibold">{{ item.name }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ item.type }}</td>
                                <td class="px-4 py-3 text-ink-soft">{{ item.purpose }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ item.duration }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h2>What we only use with your permission</h2>
                <p>
                    Right now, nothing. Codewave runs no statistics, advertising or tracking tools. The settings exist so that if we ever add one,
                    it stays switched off until you say yes. Before that happens we'll list it here and ask you again.
                </p>
                <ul>
                    <li v-for="category in optional" :key="category.id">
                        <strong>{{ category.name }}:</strong> {{ category.description }}
                        <em>{{ category.services.length ? `Used by ${ category.services.map(service => service.name).join(', ') }.` : 'Not in use.' }}</em>
                    </li>
                </ul>

                <h2>Other websites</h2>
                <p>
                    Our fonts come from <a href="https://bunny.net/fonts/" target="_blank" rel="noopener">Bunny Fonts</a>, a European service that says it sets no cookies
                    and keeps no logs of visitors. Your browser does connect to their servers to download the fonts.
                    The GitHub, Twitter and LinkedIn buttons in the footer are plain links: those sites only see you once you click through,
                    and from then on their own cookie policies apply.
                </p>

                <h2>Changing your mind</h2>
                <p>
                    You can change or withdraw your choice at any time with <strong>Cookie settings</strong> at the bottom of every page, or with the
                    button at the top of this page. Saying no is as easy as saying yes, and the site works the same either way.
                    If you withdraw permission, we remove the cookies that category set and reload the page.
                    Your choice is kept for 6 months; after that, or when this policy changes, we ask again.
                </p>
                <p>
                    You can also delete cookies and local storage yourself in your browser settings. If you do, we'll simply ask again.
                </p>
                <p>
                    For everything else about your personal data, who we are and how to reach us, read the
                    <NuxtLink to="/privacy">privacy policy</NuxtLink>.
                </p>
            </div>
        </article>
    </div>
</template>
