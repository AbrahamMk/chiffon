<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ coupons: Object });
</script>

<template>
    <Head title="Coupons" />
    <AdminLayout>
        <h1 class="text-2xl font-semibold">Coupons</h1>
        <Link :href="route('admin.marketing.coupons.create')" class="mt-4 inline-block text-sm underline">New coupon</Link>
        <ul class="mt-4 text-sm divide-y">
            <li v-for="c in coupons.data" :key="c.id" class="py-2">
                <Link :href="route('admin.marketing.coupons.edit', c.id)" class="underline">{{ c.code }}</Link> — {{ c.name }}
            </li>
        </ul>
    </AdminLayout>
</template>
