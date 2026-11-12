<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ promotions: Object });
</script>

<template>
    <Head title="Promotions" />
    <AdminLayout>
        <h1 class="text-2xl font-semibold">Promotions</h1>
        <Link :href="route('admin.marketing.promotions.create')" class="mt-4 inline-block text-sm underline">New promotion</Link>
        <ul class="mt-4 divide-y text-sm">
            <li v-for="p in promotions.data" :key="p.id" class="py-2">
                <Link :href="route('admin.marketing.promotions.edit', p.id)" class="underline">{{ p.title }}</Link>
            </li>
        </ul>
    </AdminLayout>
</template>
