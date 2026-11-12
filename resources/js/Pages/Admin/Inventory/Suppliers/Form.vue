<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    supplier: { type: Object, default: null },
});

const form = useForm({
    name: props.supplier?.name ?? '',
    contact_name: props.supplier?.contact_name ?? '',
    phone: props.supplier?.phone ?? '',
    email: props.supplier?.email ?? '',
    address: props.supplier?.address ?? '',
    notes: props.supplier?.notes ?? '',
    is_active: props.supplier?.is_active ?? true,
});

function submit() {
    if (props.supplier) {
        form.put(route('admin.inventory.suppliers.update', props.supplier.id));
    } else {
        form.post(route('admin.inventory.suppliers.store'));
    }
}
</script>

<template>
    <Head :title="supplier ? 'Edit supplier' : 'New supplier'" />
    <AdminLayout>
        <Link :href="route('admin.inventory.suppliers.index')" class="text-sm text-stone-500 underline">← Suppliers</Link>
        <h1 class="mt-2 text-2xl font-semibold">{{ supplier ? 'Edit supplier' : 'New supplier' }}</h1>

        <form class="mt-6 max-w-lg space-y-3" @submit.prevent="submit">
            <input v-model="form.name" required class="w-full rounded border px-2 py-1" placeholder="Name" />
            <input v-model="form.contact_name" class="w-full rounded border px-2 py-1" placeholder="Contact name" />
            <input v-model="form.phone" class="w-full rounded border px-2 py-1" placeholder="Phone" />
            <input v-model="form.email" type="email" class="w-full rounded border px-2 py-1" placeholder="Email" />
            <textarea v-model="form.address" rows="2" class="w-full rounded border px-2 py-1" placeholder="Address" />
            <textarea v-model="form.notes" rows="3" class="w-full rounded border px-2 py-1" placeholder="Notes" />

            <label class="flex items-center gap-2 text-sm">
                <input v-model="form.is_active" type="checkbox" />
                Active
            </label>

            <button
                type="submit"
                class="rounded bg-stone-900 px-4 py-2 text-sm text-white"
                :disabled="form.processing"
            >
                Save
            </button>
        </form>
    </AdminLayout>
</template>
