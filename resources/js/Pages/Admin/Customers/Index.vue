<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ customers: Object, filters: Object });
</script>

<template>
    <Head title="Customers" />
    <AdminLayout>
        <h1 class="text-2xl font-semibold">Customers</h1>
        <ul class="mt-6 divide-y text-sm">
            <li v-for="c in customers.data" :key="c.id" class="py-2">
                <Link :href="route('admin.customers.show', c.id)" class="underline">{{ c.full_name }}</Link> — {{ c.phone }}
            </li>
        </ul>
    </AdminLayout>
</template>
