<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ filters: Object, byStatus: Array });
</script>

<template>
    <Head title="Payments report" />
    <AdminLayout>
        <h1 class="text-2xl font-semibold">Payments summary</h1>
        <ul class="mt-6 text-sm">
            <li v-for="row in byStatus" :key="row.status">{{ row.status }}: {{ row.count }} ({{ row.total }})</li>
        </ul>
    </AdminLayout>
</template>
