<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ promotion: Object });
const form = useForm({
    title: props.promotion?.title ?? '',
    description: props.promotion?.description ?? '',
    link_url: props.promotion?.link_url ?? '',
    is_active: props.promotion?.is_active ?? true,
    starts_at: props.promotion?.starts_at ?? '',
    ends_at: props.promotion?.ends_at ?? '',
    banner: null,
});

function submit() {
    const opts = { forceFormData: true };
    if (props.promotion) {
        form.post(route('admin.marketing.promotions.update', props.promotion.id), { ...opts, _method: 'put' });
    } else {
        form.post(route('admin.marketing.promotions.store'), opts);
    }
}
</script>

<template>
    <Head title="Promotion" />
    <AdminLayout>
        <form class="max-w-lg space-y-3" @submit.prevent="submit">
            <input v-model="form.title" required class="w-full rounded border px-2 py-1" />
            <textarea v-model="form.description" class="w-full rounded border px-2 py-1" />
            <input type="file" @change="form.banner = $event.target.files[0]" />
            <button class="rounded bg-stone-900 px-4 py-2 text-sm text-white">Save</button>
        </form>
    </AdminLayout>
</template>
