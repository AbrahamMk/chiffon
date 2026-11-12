<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ variants: Object, lowStockThreshold: Number });
</script>

<template>
    <Head title="Inventory report" />
    <AdminLayout>
        <h1 class="text-2xl font-semibold">Inventory</h1>
        <p class="text-sm text-stone-500">Low stock threshold: {{ lowStockThreshold }}</p>
        <table class="mt-6 w-full text-sm">
            <tr v-for="v in variants.data" :key="v.id" class="border-t">
                <td class="py-1">{{ v.sku }}</td>
                <td>{{ v.product?.name }}</td>
                <td>{{ v.stock_quantity }}</td>
            </tr>
        </table>
    </AdminLayout>
</template>
