<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    products: Object,
    filters: Object,
    categories: Array,
});

function applyFilters(overrides = {}) {
    router.get(
        route('admin.catalog.products.index'),
        { ...props.filters, ...overrides },
        { preserveState: true, replace: true },
    );
}
</script>

<template>
    <Head title="Products" />
    <AdminLayout>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-2xl font-semibold">Products</h1>
            <Link :href="route('admin.catalog.products.create')" class="text-sm underline">New product</Link>
        </div>

        <div class="mt-4 flex flex-wrap gap-3 text-sm">
            <input
                type="search"
                class="rounded border px-2 py-1"
                placeholder="Search name or SKU"
                :value="filters.search"
                @change="applyFilters({ search: $event.target.value })"
            />
            <select
                class="rounded border px-2 py-1"
                :value="filters.status ?? ''"
                @change="applyFilters({ status: $event.target.value || null })"
            >
                <option value="">All statuses</option>
                <option value="draft">Draft</option>
                <option value="active">Active</option>
                <option value="archived">Archived</option>
            </select>
            <select
                class="rounded border px-2 py-1"
                :value="filters.category ?? ''"
                @change="applyFilters({ category: $event.target.value || null })"
            >
                <option value="">All categories</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
        </div>

        <table class="mt-6 w-full text-sm">
            <thead>
                <tr class="text-left text-stone-500">
                    <th class="w-16"></th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Base price</th>
                    <th>Variants</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="p in products.data" :key="p.id" class="border-t">
                    <td class="py-2 pr-3">
                        <div class="h-12 w-12 overflow-hidden rounded bg-stone-100">
                            <img v-if="p.image_url" :src="p.image_url" :alt="p.name" class="h-full w-full object-cover" />
                        </div>
                    </td>
                    <td class="py-2">
                        <Link :href="route('admin.catalog.products.show', p.id)" class="underline">{{ p.name }}</Link>
                    </td>
                    <td>{{ p.category?.name ?? '—' }}</td>
                    <td>{{ p.status }}</td>
                    <td>{{ p.base_price }}</td>
                    <td>{{ p.variants_count }}</td>
                </tr>
            </tbody>
        </table>

        <div v-if="products.links?.length > 3" class="mt-4 flex flex-wrap gap-2 text-sm">
            <template v-for="link in products.links" :key="link.label">
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
