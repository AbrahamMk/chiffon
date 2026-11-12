<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ suppliers: Object });

function destroySupplier(id) {
    if (confirm('Delete this supplier?')) {
        router.delete(route('admin.inventory.suppliers.destroy', id));
    }
}
</script>

<template>
    <Head title="Suppliers" />
    <AdminLayout>
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-2xl font-semibold">Suppliers</h1>
            <Link :href="route('admin.inventory.suppliers.create')" class="text-sm underline">New supplier</Link>
        </div>

        <table class="mt-6 w-full text-sm">
            <thead>
                <tr class="text-left text-stone-500">
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Active</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="s in suppliers.data" :key="s.id" class="border-t">
                    <td class="py-2">
                        <Link :href="route('admin.inventory.suppliers.edit', s.id)" class="underline">{{ s.name }}</Link>
                    </td>
                    <td>{{ s.contact_name ?? '—' }}</td>
                    <td>{{ s.phone ?? '—' }}</td>
                    <td>{{ s.email ?? '—' }}</td>
                    <td>{{ s.is_active ? 'Yes' : 'No' }}</td>
                    <td class="py-2 text-right">
                        <button type="button" class="text-rose-600 hover:underline" @click="destroySupplier(s.id)">
                            Delete
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div v-if="suppliers.links?.length > 3" class="mt-4 flex flex-wrap gap-2 text-sm">
            <template v-for="link in suppliers.links" :key="link.label">
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
