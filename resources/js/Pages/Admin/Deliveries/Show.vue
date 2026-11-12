<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ delivery: Object });
const form = useForm({ status: props.delivery.status, notes: '' });
</script>

<template>
    <Head :title="delivery.delivery_number" />
    <AdminLayout>
        <h1 class="text-2xl font-semibold">{{ delivery.delivery_number }}</h1>
        <form class="mt-4 flex gap-2" @submit.prevent="form.patch(route('admin.deliveries.update-status', delivery.id))">
            <input v-model="form.status" class="rounded border px-2 py-1" />
            <button class="text-sm underline">Update</button>
        </form>
    </AdminLayout>
</template>
