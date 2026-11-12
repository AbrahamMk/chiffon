<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ post: Object });
const form = useForm({
    platform: props.post?.platform ?? 'instagram',
    title: props.post?.title ?? '',
    caption: props.post?.caption ?? '',
    url: props.post?.url ?? '',
    is_featured: props.post?.is_featured ?? false,
    is_active: props.post?.is_active ?? true,
    published_at: props.post?.published_at ?? '',
    media: null,
});

function submit() {
    const opts = { forceFormData: true };
    if (props.post) {
        form.post(route('admin.marketing.social-posts.update', props.post.id), { ...opts, _method: 'put' });
    } else {
        form.post(route('admin.marketing.social-posts.store'), opts);
    }
}
</script>

<template>
    <Head title="Social post" />
    <AdminLayout>
        <form class="max-w-lg space-y-3" @submit.prevent="submit">
            <input v-model="form.platform" class="w-full rounded border px-2 py-1" />
            <input v-model="form.title" class="w-full rounded border px-2 py-1" />
            <textarea v-model="form.caption" class="w-full rounded border px-2 py-1" />
            <button class="rounded bg-stone-900 px-4 py-2 text-sm text-white">Save</button>
        </form>
    </AdminLayout>
</template>
