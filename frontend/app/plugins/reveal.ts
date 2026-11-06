import type { Directive } from 'vue'

type Direction = 'up' | 'down' | 'left' | 'right' | 'pop'
type RevealValue = Direction | { from?: Direction, delay?: number } | undefined

// v-reveal slides an element in once it scrolls into view.
//   v-reveal                          from below
//   v-reveal="'left'"                 from the left (also 'right', 'down', 'pop')
//   v-reveal="{ from: 'right', delay: 150 }"
// The hidden start state is in main.css, keyed on data-reveal.
function options(value: RevealValue) {
    if (typeof value === 'string') return { from: value, delay: 0 }
    return { from: value?.from ?? 'up', delay: value?.delay ?? 0 }
}

function attributes(value: RevealValue) {
    const { from, delay } = options(value)
    return {
        'data-reveal': from,
        style: delay ? `--reveal-delay: ${ delay }ms` : undefined,
    }
}

export default defineNuxtPlugin((nuxtApp) => {
    let observer: IntersectionObserver | undefined

    function observe(el: HTMLElement) {
        if (!('IntersectionObserver' in window)) {
            el.classList.add('is-revealed')
            return
        }

        observer ??= new IntersectionObserver((entries) => {
            for (const entry of entries) {
                if (!entry.isIntersecting) continue
                entry.target.classList.add('is-revealed')
                observer!.unobserve(entry.target)
            }
        }, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 })

        observer.observe(el)
    }

    const reveal: Directive<HTMLElement, RevealValue> = {
        // Server render: start hidden, so nothing flashes before the client takes over
        getSSRProps: (binding) => attributes(binding.value),

        mounted(el, binding) {
            const { from, delay } = options(binding.value)
            el.dataset.reveal = from
            if (delay) el.style.setProperty('--reveal-delay', `${ delay }ms`)
            observe(el)
        },

        unmounted(el) {
            observer?.unobserve(el)
        },
    }

    nuxtApp.vueApp.directive('reveal', reveal)
})
