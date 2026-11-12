<script setup lang="ts">
useHead({ title: 'Privacy policy' })

const { openSettings } = useCookieConsent()

// Update this date whenever the policy changes
const updatedOn = '12 November 2026'

const details = legalDetails
const draft = legalDetailsMissing()

// Everything the site does with personal data (GDPR article 13). Keep this in sync
// with the code: a new form, tool or provider needs a card here.
const purposes = [
    {
        title: 'Visiting the site',
        icon: 'uil:globe',
        color: 'bg-sky',
        what: 'Your IP address, browser and device type, the page you asked for and the time.',
        why: 'To send you the pages, keep the site secure and track down errors.',
        basis: 'Legitimate interest (article 6(1)(f)): running a safe, working website.',
        kept: `In the server logs of our hosting provider (${ details.hosting }) for ${ details.serverLogRetention }.`,
    },
    {
        title: 'Loading the posts',
        icon: 'uil:book-open',
        color: 'bg-mint',
        what: 'Your IP address, when your browser asks our API for the blog posts.',
        why: 'To answer the request, and to stop anyone flooding the API: we count requests per IP address, up to 60 a minute.',
        basis: 'Legitimate interest (article 6(1)(f)): keeping the site available for everyone.',
        kept: 'The counter holds a scrambled (hashed) version of your IP address for one minute.',
    },
    {
        title: 'Fonts',
        icon: 'uil:font',
        color: 'bg-lavender',
        what: 'Your IP address, when your browser downloads our fonts from Bunny Fonts (BunnyWay d.o.o., Slovenia).',
        why: 'To show the comic lettering.',
        basis: 'Legitimate interest (article 6(1)(f)): a consistent look, from a European provider.',
        kept: 'Bunny Fonts says it keeps no logs of these requests.',
    },
    {
        title: 'Cookies and local storage',
        icon: 'uil:setting',
        color: 'bg-lemon',
        what: 'Your cookie choice, hero or villain mode, and a cache of the posts, all kept in your own browser.',
        why: 'To make the site work the way you set it up.',
        basis: 'Strictly necessary storage needs no permission. Anything optional needs your consent (article 6(1)(a)); we use nothing optional right now.',
        kept: 'Listed per item in the cookie policy.',
        link: { to: '/cookies', text: 'Read the cookie policy' },
    },
    {
        title: 'Emailing us',
        icon: 'uil:envelope',
        color: 'bg-peach',
        what: 'Your email address, your name if you give it, and what you write.',
        why: 'To answer you, including requests about your privacy rights.',
        basis: 'Legitimate interest (article 6(1)(f)) in answering you; for privacy requests, our legal obligation (article 6(1)(c)).',
        kept: 'Until your question is handled. For privacy requests, as long as we need to show how we answered.',
    },
    {
        title: 'Writer and admin accounts',
        icon: 'uil:user',
        color: 'bg-rose',
        what: 'Name, email address, a hashed password (nobody can read it), role and login session. Only for people who write or manage the blog.',
        why: 'To let them log in to the admin panel and send them invitations and password reset emails. A writer\'s name is shown with their posts.',
        basis: 'Legitimate interest (article 6(1)(f)) in giving our writers access, or our agreement with them (article 6(1)(b)).',
        kept: 'As long as the account exists. Sessions end after 2 hours of inactivity; invitation links expire after 48 hours and password reset links after 60 minutes.',
    },
]

const recipients = [
    { name: 'Hosting', who: details.hosting, what: 'Runs the site and the API, so it handles every request.' },
    { name: 'Fonts', who: 'Bunny Fonts (BunnyWay d.o.o., Slovenia)', what: 'Serves the fonts to your browser.' },
    { name: 'Email', who: details.emailProvider, what: 'Delivers invitation and password reset emails to writers and admins.' },
]

const rights = [
    { name: 'Access', text: 'Ask what we have about you and get a copy.' },
    { name: 'Correction', text: 'Have wrong or incomplete details fixed.' },
    { name: 'Deletion', text: 'Have your data removed when we no longer need it or have no good reason to keep it.' },
    { name: 'Restriction', text: 'Have us pause using your data while a question about it is sorted out.' },
    { name: 'Portability', text: 'Get the data you gave us in a common format, or have it sent to someone else.' },
    { name: 'Objection', text: 'Object to what we do based on legitimate interest. We then stop, unless we have compelling reasons that outweigh yours.' },
    { name: 'Withdraw consent', text: 'Take back a permission at any time, as easily as you gave it. What we did before stays lawful.' },
]
</script>

<template>
    <div class="mx-auto max-w-4xl">
        <section class="comic-panel comic-rays relative overflow-hidden bg-sky px-6 py-12 [--ray:var(--mint)] sm:px-10 sm:py-16">
            <div class="halftone pointer-events-none absolute inset-0" aria-hidden="true"></div>
            <ComicBurst v-reveal="{ from: 'pop', delay: 300 }" text="Shh!" color="var(--lemon)" :spikes="16" class="absolute -right-4 -top-4 hidden w-36 text-base sm:block lg:right-6 lg:top-4 lg:w-44 lg:text-lg"/>

            <div class="relative max-w-2xl">
                <ComicHeading v-reveal="'down'" as="h1" kicker="The fine print">Privacy policy</ComicHeading>
                <p v-reveal="{ from: 'left', delay: 150 }" class="mt-5 text-lg text-ink-soft">
                    Which personal data Codewave handles, why, for how long, and what you can do about it. Short version: as little as possible,
                    no tracking, and we never sell anything.
                </p>
            </div>
        </section>

        <aside v-if="draft" v-reveal="'pop'" class="comic-panel mt-10 flex items-start gap-3 bg-lemon p-5" role="note">
            <Icon name="uil:exclamation-triangle" size="1.6rem" class="mt-0.5 shrink-0 text-rose-deep"/>
            <p>
                <strong class="font-comic text-xl tracking-wider">Draft.</strong>
                Details in [square brackets] haven't been filled in yet, so this policy isn't final.
            </p>
        </aside>

        <article v-reveal="{ from: 'up', delay: 100 }" class="comic-panel relative mt-12 px-5 pb-10 pt-12 sm:px-10">
            <span class="absolute -top-5 left-5 -rotate-2 rounded-lg border-3 border-line bg-lemon px-3 py-0.5 font-comic text-xl tracking-wider text-ink shadow-comic-sm sm:left-10">
                Last updated {{ updatedOn }}
            </span>

            <div class="prose prose-comic max-w-none">
                <h2>Who we are</h2>
                <p>
                    The person responsible for your data (the "controller" in the GDPR) is <strong>{{ details.controller }}</strong>,
                    {{ details.address }}. For anything about your privacy, email
                    <a :href="`mailto:${ details.email }`">{{ details.email }}</a>.
                </p>

                <h2>The short version</h2>
                <ul>
                    <li>No analytics, advertising or tracking tools, and no profiling.</li>
                    <li>We never sell or rent out personal data.</li>
                    <li>We only handle what's needed to run the site and the accounts of the people who write for it.</li>
                    <li>No decisions about you are made automatically.</li>
                </ul>

                <h2>What we handle and why</h2>
            </div>

            <ul class="mt-6 grid gap-6 md:grid-cols-2">
                <li v-for="purpose in purposes" :key="purpose.title" class="rounded-xl border-3 border-line p-5 shadow-comic-sm" :class="purpose.color">
                    <h3 class="flex items-center gap-2 font-comic text-2xl tracking-wider">
                        <Icon :name="purpose.icon" class="text-lavender-deep"/>
                        {{ purpose.title }}
                    </h3>
                    <dl class="mt-3 space-y-2 text-sm">
                        <div>
                            <dt class="font-bold">What</dt>
                            <dd class="text-ink-soft">{{ purpose.what }}</dd>
                        </div>
                        <div>
                            <dt class="font-bold">Why</dt>
                            <dd class="text-ink-soft">{{ purpose.why }}</dd>
                        </div>
                        <div>
                            <dt class="font-bold">Legal basis</dt>
                            <dd class="text-ink-soft">{{ purpose.basis }}</dd>
                        </div>
                        <div>
                            <dt class="font-bold">Kept for</dt>
                            <dd class="text-ink-soft">{{ purpose.kept }}</dd>
                        </div>
                    </dl>
                    <NuxtLink v-if="purpose.link" :to="purpose.link.to" class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-lavender-deep underline decoration-2 underline-offset-2 hover:text-rose-deep">
                        {{ purpose.link.text }}
                        <Icon name="uil:arrow-right"/>
                    </NuxtLink>
                </li>
            </ul>

            <div class="prose prose-comic mt-10 max-w-none">
                <h2>Who else sees it</h2>
                <p>
                    A few companies process data on our behalf, only for the job below and under a data processing agreement where the law requires one.
                    Nobody else gets your data, unless a court or the law requires it.
                </p>
                <div class="not-prose overflow-x-auto rounded-xl border-3 border-line">
                    <table class="w-full min-w-lg text-left text-sm">
                        <thead class="bg-mint font-comic text-lg tracking-wider">
                            <tr>
                                <th class="px-4 py-2">For</th>
                                <th class="px-4 py-2">Who</th>
                                <th class="px-4 py-2">What they do</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="recipient in recipients" :key="recipient.name" class="border-t-3 border-line align-top">
                                <td class="px-4 py-3 font-semibold">{{ recipient.name }}</td>
                                <td class="px-4 py-3">{{ recipient.who }}</td>
                                <td class="px-4 py-3 text-ink-soft">{{ recipient.what }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h2>Outside the EU</h2>
                <p>
                    We aim to keep your data inside the European Economic Area. If a provider above does process it elsewhere, we only use one
                    with proper safeguards (an EU adequacy decision, such as the EU-US Data Privacy Framework, or the EU's standard contractual clauses)
                    and name it here.
                </p>

                <h2>Your rights</h2>
                <p>Under the GDPR you have the right to:</p>
                <ul>
                    <li v-for="right in rights" :key="right.name"><strong>{{ right.name }}:</strong> {{ right.text }}</li>
                </ul>
                <p>
                    Email <a :href="`mailto:${ details.email }`">{{ details.email }}</a> to use any of them. It's free, and we answer within one month.
                    If a request is complex we may need two more months, and we'll tell you why within the first month.
                    We may ask you to prove who you are, so nobody else gets your data.
                </p>
                <p>
                    Cookie choices you can change yourself, any time:
                    <button type="button" class="font-semibold text-lavender-deep underline decoration-2 underline-offset-2 hover:text-rose-deep" @click="openSettings">open the cookie settings</button>.
                </p>

                <h2>Complaints</h2>
                <p>
                    We'd like to hear it first, so we can put things right. You can also complain to a data protection authority: ours is
                    <a :href="details.authority.url" target="_blank" rel="noopener">{{ details.authority.name }}</a>,
                    or you can go to the one in the country where you live or work, or where you think the problem happened.
                </p>

                <h2>Keeping it safe</h2>
                <p>
                    The site runs over HTTPS. Passwords are stored hashed, so nobody can read them. Only admins can manage accounts, and writers only get the
                    blog. The API limits how many requests each visitor can make.
                </p>

                <h2>Changes</h2>
                <p>
                    When what we do with data changes, we update this page and the date at the top. If a change needs your permission,
                    we'll ask for it first.
                </p>
            </div>
        </article>
    </div>
</template>
