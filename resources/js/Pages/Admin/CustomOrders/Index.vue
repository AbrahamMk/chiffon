<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ customOrders: Object, filters: Object });
</script>

<template>
    <Head title="Custom orders" />
    <AdminLayout>
        <h1 class="text-2xl font-semibold">Custom orders</h1>
        <ul class="mt-6 divide-y text-sm">
            <li v-for="o in customOrders.data" :key="o.id" class="py-2">
                <Link :href="route('admin.custom-orders.show', o.id)" class="underline">{{ o.reference }}</Link>
                — {{ o.customer_name }} ({{ o.status }})
            </li>
        </ul>
    </AdminLayout>
</template>
