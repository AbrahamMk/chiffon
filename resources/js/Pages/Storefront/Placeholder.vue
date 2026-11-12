<script setup>
import { Head } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';

defineProps({
    title: { type: String, required: true },
    message: { type: String, required: true },
});
</script>

<template>
    <Head :title="title" />
    <StorefrontLayout>
        <section class="mx-auto max-w-3xl px-4 py-24 text-center sm:px-6">
            <h1 class="font-display text-5xl">{{ title }}</h1>
            <p class="mt-4 text-[var(--aster-muted)]">{{ message }}</p>
        </section>
    </StorefrontLayout>
</template>
