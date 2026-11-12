<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';

defineProps({ orders: Object });
</script>

<template>
    <Head title="My orders" />
    <StorefrontLayout>
        <div class="mx-auto max-w-3xl px-4 py-12">
            <h1 class="font-display text-4xl">My orders</h1>
            <ul class="mt-8 divide-y">
                <li v-for="order in orders.data" :key="order.id" class="py-4">
                    <Link :href="route('account.orders.show', order.order_number)" class="font-medium hover:underline">
                        {{ order.order_number }}
                    </Link>
                    <span class="ml-2 text-sm text-stone-500">{{ order.status }} · {{ order.grand_total }}</span>
                </li>
            </ul>
        </div>
    </StorefrontLayout>
</template>
