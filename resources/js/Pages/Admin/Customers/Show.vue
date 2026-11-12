<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ customer: Object, recentOrders: Array });
</script>

<template>
    <Head :title="customer.full_name" />
    <AdminLayout>
        <h1 class="text-2xl font-semibold">{{ customer.full_name }}</h1>
        <p class="text-sm text-stone-500">{{ customer.phone }} · {{ customer.email }}</p>
        <h2 class="mt-8 font-medium">Recent orders</h2>
        <ul class="mt-2 text-sm">
            <li v-for="o in recentOrders" :key="o.id">{{ o.order_number }} — {{ o.grand_total }}</li>
        </ul>
    </AdminLayout>
</template>
