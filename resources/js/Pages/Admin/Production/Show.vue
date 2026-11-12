<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ job: Object });

function stageForm(stageId) {
    return useForm({ status: 'completed', notes: '' });
}
</script>

<template>
    <Head :title="job.job_number" />
    <AdminLayout>
        <h1 class="text-2xl font-semibold">{{ job.job_number }}</h1>
        <ul class="mt-6 space-y-2 text-sm">
            <li v-for="stage in job.job_stages" :key="stage.id">
                {{ stage.production_stage?.name }} — {{ stage.status }}
            </li>
        </ul>
    </AdminLayout>
</template>
