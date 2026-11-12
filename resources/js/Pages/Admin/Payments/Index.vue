<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ payments: Object, filters: Object });
</script>

<template>
    <Head title="Payments" />
    <AdminLayout>
        <h1 class="text-2xl font-semibold">Payments</h1>
        <table class="mt-6 w-full text-sm">
            <tr v-for="p in payments.data" :key="p.id" class="border-t">
                <td class="py-2">{{ p.payment_reference }}</td>
                <td>{{ p.order?.order_number }}</td>
                <td>{{ p.amount }}</td>
                <td>{{ p.status }}</td>
            </tr>
        </table>
    </AdminLayout>
</template>
