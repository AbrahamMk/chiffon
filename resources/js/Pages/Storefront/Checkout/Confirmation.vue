<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';

defineProps({ order: Object });
</script>

<template>
    <Head title="Order confirmed" />
    <StorefrontLayout>
        <div class="mx-auto max-w-xl px-4 py-16 text-center">
            <h1 class="font-display text-4xl">Thank you</h1>
            <p class="mt-4">Order <strong>{{ order.order_number }}</strong> · {{ order.payment_status }}</p>
            <p class="mt-2 text-lg">Total: {{ order.grand_total }}</p>
            <Link :href="route('home')" class="mt-8 inline-block text-sm underline">Continue shopping</Link>
        </div>
    </StorefrontLayout>
</template>
