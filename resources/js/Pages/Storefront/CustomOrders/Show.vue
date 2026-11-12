<script setup>
import { Head } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';

defineProps({ customOrder: Object });
</script>

<template>
    <Head :title="customOrder.reference" />
    <StorefrontLayout>
        <div class="mx-auto max-w-xl px-4 py-12">
            <h1 class="font-display text-3xl">Reference {{ customOrder.reference }}</h1>
            <p class="mt-2 capitalize text-stone-600">Status: {{ customOrder.status }}</p>
            <p v-if="customOrder.quoted_amount" class="mt-4">Quote: {{ customOrder.quoted_amount }}</p>
        </div>
    </StorefrontLayout>
</template>
