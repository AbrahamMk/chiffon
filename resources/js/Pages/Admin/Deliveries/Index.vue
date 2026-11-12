<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ deliveries: Object, filters: Object });
</script>

<template>
    <Head title="Deliveries" />
    <AdminLayout>
        <h1 class="text-2xl font-semibold">Deliveries</h1>
        <ul class="mt-6 divide-y text-sm">
            <li v-for="d in deliveries.data" :key="d.id" class="py-2">
                <Link :href="route('admin.deliveries.show', d.id)" class="underline">{{ d.delivery_number }}</Link> — {{ d.status }}
            </li>
        </ul>
    </AdminLayout>
</template>
