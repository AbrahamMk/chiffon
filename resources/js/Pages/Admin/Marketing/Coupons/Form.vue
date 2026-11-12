<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ coupon: Object });
const form = useForm({
    code: props.coupon?.code ?? '',
    name: props.coupon?.name ?? '',
    type: props.coupon?.type ?? 'percent',
    value: props.coupon?.value ?? 0,
    min_order_amount: props.coupon?.min_order_amount ?? '',
    usage_limit: props.coupon?.usage_limit ?? '',
    is_active: props.coupon?.is_active ?? true,
    starts_at: props.coupon?.starts_at ?? '',
    ends_at: props.coupon?.ends_at ?? '',
});

function submit() {
    if (props.coupon) {
        form.put(route('admin.marketing.coupons.update', props.coupon.id));
    } else {
        form.post(route('admin.marketing.coupons.store'));
    }
}
</script>

<template>
    <Head :title="coupon ? 'Edit coupon' : 'New coupon'" />
    <AdminLayout>
        <form class="max-w-md space-y-3" @submit.prevent="submit">
            <input v-model="form.code" required class="w-full rounded border px-2 py-1" placeholder="Code" />
            <input v-model="form.name" required class="w-full rounded border px-2 py-1" placeholder="Name" />
            <input v-model="form.value" type="number" step="0.01" class="w-full rounded border px-2 py-1" />
            <button class="rounded bg-stone-900 px-4 py-2 text-sm text-white">Save</button>
        </form>
    </AdminLayout>
</template>
