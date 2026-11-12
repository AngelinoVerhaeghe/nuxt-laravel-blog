// The facts the privacy policy (and cookie policy) need that the code can't know.
// Anything still in [square brackets] is a placeholder: the privacy page shows a
// "draft" notice until every one of them is filled in.
export const legalDetails = {
    // Who decides what happens with the data (the GDPR "controller")
    controller: '[Your name or company name]',
    address: '[Street and number, postcode, city, country]',
    email: '[privacy@example.com]',

    // Hosting company of the site and the API, and where its servers are
    hosting: '[Hosting provider, country]',
    // How long the hosting provider keeps server logs
    serverLogRetention: '[number of] days',
    // The company that sends the invitation and password reset emails
    emailProvider: '[Email provider, country]',

    // The data protection authority you answer to
    authority: {
        name: '[Data protection authority]',
        url: '[https://authority.example]',
    },
}

function placeholders(value: unknown): boolean {
    if (typeof value === 'string') return /\[[^\]]+\]/.test(value)
    return typeof value === 'object' && value !== null && Object.values(value).some(placeholders)
}

export function legalDetailsMissing() {
    return placeholders(legalDetails)
}
