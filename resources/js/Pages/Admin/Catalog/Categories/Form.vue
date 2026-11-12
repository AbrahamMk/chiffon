<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    category: { type: Object, default: null },
    parents: Array,
});

const form = useForm({
    name: props.category?.name ?? '',
    parent_id: props.category?.parent_id ?? '',
    description: props.category?.description ?? '',
    is_featured: props.category?.is_featured ?? false,
    is_active: props.category?.is_active ?? true,
    sort_order: props.category?.sort_order ?? 0,
});

function submit() {
    form.parent_id = form.parent_id || null;

    if (props.category) {
        form.put(route('admin.catalog.categories.update', props.category.id));
    } else {
        form.post(route('admin.catalog.categories.store'));
    }
}
</script>

<template>
    <Head :title="category ? 'Edit category' : 'New category'" />
    <AdminLayout>
        <Link :href="route('admin.catalog.categories.index')" class="text-sm text-stone-500 underline">← Categories</Link>
        <h1 class="mt-2 text-2xl font-semibold">{{ category ? 'Edit category' : 'New category' }}</h1>

        <form class="mt-6 max-w-lg space-y-3" @submit.prevent="submit">
            <div>
                <input v-model="form.name" required class="w-full rounded border px-2 py-1" placeholder="Name" />
                <p v-if="form.errors.name" class="text-xs text-rose-600">{{ form.errors.name }}</p>
            </div>

            <select v-model="form.parent_id" class="w-full rounded border px-2 py-1">
                <option value="">No parent</option>
                <option v-for="p in parents" :key="p.id" :value="p.id">{{ p.name }}</option>
            </select>

            <textarea
                v-model="form.description"
                rows="3"
                class="w-full rounded border px-2 py-1"
                placeholder="Description"
            />

            <input
                v-model.number="form.sort_order"
                type="number"
                min="0"
                class="w-full rounded border px-2 py-1"
                placeholder="Sort order"
            />

            <label class="flex items-center gap-2 text-sm">
                <input v-model="form.is_featured" type="checkbox" />
                Featured
            </label>

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
