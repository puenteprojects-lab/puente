<script setup lang="ts">
withDefaults(
    defineProps<{
        arch?: string;
        dot?: string;
        /**
         * Crops the viewBox to the drawing itself. The square viewBox leaves
         * the mark floating in the middle third of its box, which reads as
         * weak at icon sizes, so inline uses want this on.
         */
        compact?: boolean;
    }>(),
    {
        arch: 'currentColor',
        dot: 'var(--brand-terracotta)',
        compact: false,
    },
);
</script>

<template>
    <svg
        :viewBox="compact ? '9.25 30.75 83.375 38.375' : '0 0 100 100'"
        fill="none"
        aria-hidden="true"
    >
        <g :stroke="arch" stroke-width="6.5" stroke-linecap="round" fill="none">
            <path d="M23.125 65.875a26.875 26.875 0 0 1 53.75 0" />
            <path d="M12.5 65.875h10.625" />
            <path d="M76.875 65.875h12.5" />
        </g>
        <circle cx="50" cy="35.75" r="5" :fill="dot" />
    </svg>
</template>
