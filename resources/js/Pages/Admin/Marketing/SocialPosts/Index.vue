<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ posts: Object });
</script>

<template>
    <Head title="Social posts" />
    <AdminLayout>
        <h1 class="text-2xl font-semibold">Social posts</h1>
        <Link :href="route('admin.marketing.social-posts.create')" class="mt-4 text-sm underline">New post</Link>
        <ul class="mt-4 divide-y text-sm">
            <li v-for="p in posts.data" :key="p.id" class="py-2">
                <Link :href="route('admin.marketing.social-posts.edit', p.id)" class="underline">{{ p.platform }} — {{ p.title }}</Link>
            </li>
        </ul>
    </AdminLayout>
</template>
