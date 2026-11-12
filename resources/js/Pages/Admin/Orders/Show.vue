<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ order: Object });
const form = useForm({ status: props.order.status, notes: '' });

function updateStatus() {
    form.patch(route('admin.orders.update-status', props.order.id));
}
</script>

<template>
    <Head :title="order.order_number" />
    <AdminLayout>
        <h1 class="text-2xl font-semibold">{{ order.order_number }}</h1>
        <form class="mt-6 flex gap-2" @submit.prevent="updateStatus">
            <input v-model="form.status" class="rounded border px-2 py-1" />
            <input v-model="form.notes" class="rounded border px-2 py-1" placeholder="Notes" />
            <button class="rounded bg-stone-900 px-3 py-1 text-white text-sm">Update status</button>
        </form>
        <ul class="mt-8 space-y-1 text-sm">
            <li v-for="item in order.items" :key="item.id">{{ item.product_name }} × {{ item.quantity }}</li>
        </ul>
    </AdminLayout>
</template>
