<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ categories: Object });

function destroyCategory(id) {
    if (confirm('Delete this category?')) {
        router.delete(route('admin.catalog.categories.destroy', id));
    }
}
</script>

<template>
    <Head title="Categories" />
    <AdminLayout>
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-2xl font-semibold">Categories</h1>
            <Link :href="route('admin.catalog.categories.create')" class="text-sm underline">New category</Link>
        </div>

        <table class="mt-6 w-full text-sm">
            <thead>
                <tr class="text-left text-stone-500">
                    <th>Name</th>
                    <th>Parent</th>
                    <th>Products</th>
                    <th>Active</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="c in categories.data" :key="c.id" class="border-t">
                    <td class="py-2">
                        <Link :href="route('admin.catalog.categories.edit', c.id)" class="underline">{{ c.name }}</Link>
                    </td>
                    <td>{{ c.parent?.name ?? '—' }}</td>
                    <td>{{ c.products_count }}</td>
                    <td>{{ c.is_active ? 'Yes' : 'No' }}</td>
                    <td class="py-2 text-right">
                        <button type="button" class="text-rose-600 hover:underline" @click="destroyCategory(c.id)">
                            Delete
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div v-if="categories.links?.length > 3" class="mt-4 flex flex-wrap gap-2 text-sm">
            <template v-for="link in categories.links" :key="link.label">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    class="rounded border px-2 py-1"
                    :class="{ 'bg-stone-900 text-white': link.active }"
                    v-html="link.label"
                />
                <span v-else class="rounded border px-2 py-1 text-stone-400" v-html="link.label" />
            </template>
        </div>
    </AdminLayout>
</template>
