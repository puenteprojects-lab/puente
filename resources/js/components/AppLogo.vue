<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import PuenteMark from '@/components/PuenteMark.vue';

const name = usePage().props.name;
</script>

<template>
    <div
        class="flex aspect-square size-8 items-center justify-center rounded-md bg-brand-teal"
    >
        <PuenteMark compact class="h-3.5 w-auto text-white" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-tight font-semibold">{{
            name
        }}</span>
    </div>
</template>
