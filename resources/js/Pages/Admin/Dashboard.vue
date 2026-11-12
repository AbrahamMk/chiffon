<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    stats: { type: Object, default: () => ({}) },
    recentOrders: { type: Array, default: () => [] },
    recentPayments: { type: Array, default: () => [] },
});

const brand = computed(() => usePage().props.brand);
</script>

<template>
    <Head title="Admin Dashboard" />
    <AdminLayout>
        <div class="space-y-6">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Dashboard</h1>
                <p class="mt-1 text-sm text-stone-500">Operations overview for {{ brand.name }}.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-stone-200 bg-white p-4">
                    <p class="text-xs uppercase tracking-wide text-stone-500">Today sales</p>
                    <p class="mt-2 text-2xl font-semibold">{{ stats.today_sales ?? '—' }}</p>
                </div>
                <div class="rounded-xl border border-stone-200 bg-white p-4">
                    <p class="text-xs uppercase tracking-wide text-stone-500">Month sales</p>
                    <p class="mt-2 text-2xl font-semibold">{{ stats.month_sales ?? '—' }}</p>
                </div>
                <div class="rounded-xl border border-stone-200 bg-white p-4">
                    <p class="text-xs uppercase tracking-wide text-stone-500">Orders</p>
                    <p class="mt-2 text-2xl font-semibold">{{ stats.orders_count ?? '—' }}</p>
                </div>
                <div class="rounded-xl border border-stone-200 bg-white p-4">
                    <p class="text-xs uppercase tracking-wide text-stone-500">Customers</p>
                    <p class="mt-2 text-2xl font-semibold">{{ stats.customers_count ?? '—' }}</p>
                </div>
                <div class="rounded-xl border border-stone-200 bg-white p-4">
                    <p class="text-xs uppercase tracking-wide text-stone-500">Low stock SKUs</p>
                    <p class="mt-2 text-2xl font-semibold">{{ stats.low_stock_count ?? '—' }}</p>
                </div>
                <div class="rounded-xl border border-stone-200 bg-white p-4">
                    <p class="text-xs uppercase tracking-wide text-stone-500">Custom in pipeline</p>
                    <p class="mt-2 text-2xl font-semibold">{{ stats.custom_orders_in_production ?? '—' }}</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-xl border bg-white p-4 text-sm">
                    <h2 class="font-medium">Recent orders</h2>
                    <ul class="mt-3 space-y-1">
                        <li v-for="o in recentOrders" :key="o.id">{{ o.order_number }} — {{ o.grand_total }} ({{ o.status }})</li>
                    </ul>
                </div>
                <div class="rounded-xl border bg-white p-4 text-sm">
                    <h2 class="font-medium">Recent payments</h2>
                    <ul class="mt-3 space-y-1">
                        <li v-for="p in recentPayments" :key="p.id">{{ p.payment_reference }} — {{ p.amount }} ({{ p.status }})</li>
                    </ul>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
