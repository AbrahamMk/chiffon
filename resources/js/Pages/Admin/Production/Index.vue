<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ jobs: Object, filters: Object });
</script>

<template>
    <Head title="Production" />
    <AdminLayout>
        <h1 class="text-2xl font-semibold">Production jobs</h1>
        <ul class="mt-6 text-sm divide-y">
            <li v-for="job in jobs.data" :key="job.id" class="py-2">
                <Link :href="route('admin.production.show', job.id)" class="underline">{{ job.job_number }}</Link> — {{ job.title }} ({{ job.status }})
            </li>
        </ul>
    </AdminLayout>
</template>
