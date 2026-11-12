<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ orders: Object, filters: Object });
</script>

<template>
    <Head title="Orders" />
    <AdminLayout>
        <h1 class="text-2xl font-semibold">Orders</h1>
        <table class="mt-6 w-full text-sm">
            <thead><tr class="text-left text-stone-500"><th>Number</th><th>Customer</th><th>Status</th><th>Total</th></tr></thead>
            <tbody>
                <tr v-for="o in orders.data" :key="o.id" class="border-t">
                    <td class="py-2"><Link :href="route('admin.orders.show', o.id)" class="underline">{{ o.order_number }}</Link></td>
                    <td>{{ o.customer_name }}</td>
                    <td>{{ o.status }}</td>
                    <td>{{ o.grand_total }}</td>
                </tr>
            </tbody>
        </table>
    </AdminLayout>
</template>
