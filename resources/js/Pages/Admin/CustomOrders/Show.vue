<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ customOrder: Object, imageUrls: Object });
const statusForm = useForm({ status: props.customOrder.status });
const quoteForm = useForm({ amount: '', deposit_required: '', notes: '' });
</script>

<template>
    <Head :title="customOrder.reference" />
    <AdminLayout>
        <h1 class="text-2xl font-semibold">{{ customOrder.reference }}</h1>
        <form class="mt-4 flex gap-2" @submit.prevent="statusForm.patch(route('admin.custom-orders.update-status', customOrder.id))">
            <input v-model="statusForm.status" class="rounded border px-2 py-1" />
            <button class="text-sm underline">Save status</button>
        </form>
        <form class="mt-8 grid max-w-md gap-2" @submit.prevent="quoteForm.post(route('admin.custom-orders.quotations.store', customOrder.id))">
            <input v-model="quoteForm.amount" type="number" step="0.01" placeholder="Quote amount" class="rounded border px-2 py-1" />
            <button class="rounded bg-stone-900 px-3 py-2 text-sm text-white">Send quotation</button>
        </form>
    </AdminLayout>
</template>
