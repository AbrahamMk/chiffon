<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ filters: Object, summary: Object, rows: Array });
</script>

<template>
    <Head title="Sales report" />
    <AdminLayout>
        <h1 class="text-2xl font-semibold">Sales</h1>
        <p class="mt-2 text-sm">Verified: {{ summary.verified_payments }} · Paid orders: {{ summary.paid_orders }}</p>
        <Link :href="route('admin.reports.sales.export', filters)" class="mt-4 inline-block text-sm underline">Export Excel</Link>
        <table class="mt-6 w-full text-sm">
            <tr v-for="(row, i) in rows" :key="i" class="border-t">
                <td v-for="(cell, j) in row" :key="j" class="py-1">{{ cell }}</td>
            </tr>
        </table>
    </AdminLayout>
</template>
