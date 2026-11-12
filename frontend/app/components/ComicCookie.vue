<script setup lang="ts">
// A comic cookie with a bite taken out, in the site's outline style
const mask = useId()
</script>

<template>
    <svg viewBox="0 0 48 48" class="overflow-visible drop-shadow-[3px_3px_0_var(--pop)]" aria-hidden="true">
        <defs>
            <mask :id="mask">
                <rect width="48" height="48" fill="white"/>
                <circle cx="41" cy="9" r="9" fill="black"/>
            </mask>
        </defs>
        <g :mask="`url(#${ mask })`">
            <circle cx="24" cy="24" r="20" fill="#f3c98b" stroke="#2f2a3b" stroke-width="3"/>
            <circle cx="16" cy="18" r="3" fill="#6b4226"/>
            <circle cx="27" cy="28" r="3.4" fill="#6b4226"/>
            <circle cx="15" cy="31" r="2.4" fill="#6b4226"/>
            <circle cx="31" cy="17" r="2" fill="#6b4226"/>
            <circle cx="22" cy="38" r="1.8" fill="#6b4226"/>
        </g>
        <!-- Outline of the bite, so it reads as a bite and not a gap in the stroke -->
        <path d="M32.55 5.92A9 9 0 0 0 43.01 17.77" fill="none" stroke="#2f2a3b" stroke-width="3" stroke-linecap="round"/>
    </svg>
</template>
