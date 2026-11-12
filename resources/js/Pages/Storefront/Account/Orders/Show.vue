<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';

defineProps({ order: Object });
</script>

<template>
    <Head :title="order.order_number" />
    <StorefrontLayout>
        <div class="mx-auto max-w-3xl px-4 py-12">
            <Link :href="route('account.orders.index')" class="text-sm text-stone-500">← Back</Link>
            <h1 class="mt-4 font-display text-3xl">{{ order.order_number }}</h1>
            <ul class="mt-6 space-y-2">
                <li v-for="item in order.items" :key="item.id">{{ item.product_name }} × {{ item.quantity }}</li>
            </ul>
        </div>
    </StorefrontLayout>
</template>
