// Cookie consent (GDPR + ePrivacy). Strictly necessary storage is always on; every
// other category stays off until the visitor opts in, and is easy to switch off again.
//
// Gate anything optional on `allowed()`, for example:
//   const { allowed } = useCookieConsent()
//   watchEffect(() => { if (allowed('analytics')) loadAnalytics() })
//
// Adding a tool? List it under its category below (with the cookies it sets, so they
// can be removed when consent is withdrawn), add it to the cookie policy page, and
// bump CONSENT_VERSION so every visitor is asked again.

export type OptionalCategory = 'analytics' | 'marketing'

export interface CookieService {
    name: string
    // Cookie names to delete when consent is withdrawn; a trailing * matches a prefix (`_ga*`)
    cookies: string[]
}

export interface CookieCategory {
    id: 'necessary' | OptionalCategory
    name: string
    description: string
    required: boolean
    services: CookieService[]
}

export const CONSENT_VERSION = 1
export const CONSENT_COOKIE = 'codewave-consent'
// Ask again after six months, so a choice never silently lasts forever
export const CONSENT_MAX_AGE = 60 * 60 * 24 * 182

export const cookieCategories: CookieCategory[] = [
    {
        id: 'necessary',
        name: 'Strictly necessary',
        description: 'Keeps the site working: remembers this cookie choice, your hero or villain mode, and a cache of the posts. Always on, because the site needs it.',
        required: true,
        services: [],
    },
    {
        id: 'analytics',
        name: 'Statistics',
        description: 'Counts visits and which posts get read, so we know what to draw next.',
        required: false,
        services: [],
    },
    {
        id: 'marketing',
        name: 'Marketing & embeds',
        description: 'Videos, social posts and ads from other sites, which can follow you around the web.',
        required: false,
        services: [],
    },
]

interface StoredConsent {
    version: number
    // When the choice was made (ISO 8601), so it can be shown and proven
    date: string
    categories: Record<OptionalCategory, boolean>
}

const optionalIds = cookieCategories.filter(category => !category.required).map(category => category.id as OptionalCategory)

function everything(value: boolean) {
    return Object.fromEntries(optionalIds.map(id => [ id, value ])) as Record<OptionalCategory, boolean>
}

function isCurrent(value: unknown): value is StoredConsent {
    return typeof value === 'object' && value !== null
        && (value as StoredConsent).version === CONSENT_VERSION
        && typeof (value as StoredConsent).categories === 'object'
}

// Delete a withdrawn category's cookies on this host and its parent domains
function deleteCookies(category: OptionalCategory) {
    const patterns = cookieCategories.find(item => item.id === category)?.services.flatMap(service => service.cookies) ?? []
    if (!patterns.length) return

    const names = document.cookie.split(';').map(part => part.split('=')[0]!.trim()).filter(Boolean)
    const parts = location.hostname.split('.')
    const domains = [ '', ...parts.slice(0, -1).map((_, index) => `; domain=.${ parts.slice(index).join('.') }`) ]

    for (const name of names) {
        const matches = patterns.some(pattern => pattern.endsWith('*') ? name.startsWith(pattern.slice(0, -1)) : name === pattern)
        if (!matches) continue
        for (const domain of domains) {
            document.cookie = `${ name }=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/${ domain }`
        }
    }
}

export function useCookieConsent() {
    // The cookie (not localStorage) holds the choice, so the server already knows
    // whether to render the banner and it never flashes on load
    const cookie = useCookie<StoredConsent | null>(CONSENT_COOKIE, {
        maxAge: CONSENT_MAX_AGE,
        sameSite: 'lax',
        path: '/',
        default: () => null,
    })

    const consent = useState<StoredConsent | null>('cookie-consent', () => isCurrent(cookie.value) ? cookie.value : null)
    const settingsOpen = useState('cookie-settings-open', () => false)

    const answered = computed(() => consent.value !== null)

    function allowed(category: OptionalCategory) {
        return consent.value?.categories[category] === true
    }

    function save(categories: Record<OptionalCategory, boolean>) {
        const withdrawn = optionalIds.filter(id => allowed(id) && !categories[id])

        const value: StoredConsent = {
            version: CONSENT_VERSION,
            date: new Date().toISOString(),
            categories: { ...everything(false), ...categories },
        }
        cookie.value = value
        consent.value = value
        settingsOpen.value = false

        // Scripts that already ran keep running until the page reloads, so clear
        // their cookies and start fresh: withdrawing is as thorough as giving
        if (import.meta.client && withdrawn.length) {
            withdrawn.forEach(deleteCookies)
            // After the tick, so useCookie has written the new choice first
            nextTick(() => reloadNuxtApp({ force: true }))
        }
    }

    return {
        consent: readonly(consent),
        answered,
        allowed,
        settingsOpen,
        acceptAll: () => save(everything(true)),
        rejectAll: () => save(everything(false)),
        save,
        openSettings: () => settingsOpen.value = true,
        closeSettings: () => settingsOpen.value = false,
    }
}
