<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ product: Object });

const archiveForm = useForm({});

function archiveProduct() {
    if (confirm('Archive this product?')) {
        archiveForm.post(route('admin.catalog.products.archive', props.product.id));
    }
}

function destroyProduct() {
    if (confirm('Delete this product permanently?')) {
        router.delete(route('admin.catalog.products.destroy', props.product.id));
    }
}
</script>

<template>
    <Head :title="product.name" />
    <AdminLayout>
        <Link :href="route('admin.catalog.products.index')" class="text-sm text-stone-500 underline">← Products</Link>

        <div class="mt-2 flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold">{{ product.name }}</h1>
                <p class="text-sm text-stone-500">
                    {{ product.category?.name ?? 'Uncategorized' }} · {{ product.status }}
                    <span v-if="product.sku"> · SKU {{ product.sku }}</span>
                </p>
            </div>
            <div class="flex flex-wrap gap-3 text-sm">
                <Link :href="route('admin.catalog.products.edit', product.id)" class="underline">Edit</Link>
                <button
                    v-if="product.status !== 'archived'"
                    type="button"
                    class="text-stone-600 underline"
                    @click="archiveProduct"
                >
                    Archive
                </button>
                <button type="button" class="text-rose-600 underline" @click="destroyProduct">Delete</button>
            </div>
        </div>

        <div v-if="product.images?.length" class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div v-for="img in product.images" :key="img.id" class="overflow-hidden rounded-lg border bg-stone-50">
                <img :src="img.url" :alt="img.alt_text || product.name" class="aspect-square w-full object-cover" />
                <p v-if="img.is_primary" class="px-2 py-1 text-xs text-stone-500">Primary</p>
            </div>
        </div>
        <p v-else class="mt-6 text-sm text-stone-500">No images yet — upload them on the edit page.</p>

        <dl class="mt-6 grid max-w-2xl gap-2 text-sm sm:grid-cols-2">
            <p><span class="text-stone-500">Base price:</span> {{ product.base_price }}</p>
            <p v-if="product.compare_at_price">
                <span class="text-stone-500">Compare at:</span> {{ product.compare_at_price }}
            </p>
            <p><span class="text-stone-500">Featured:</span> {{ product.is_featured ? 'Yes' : 'No' }}</p>
        </dl>

        <p v-if="product.description" class="mt-4 max-w-2xl text-sm text-stone-700">{{ product.description }}</p>

        <h2 class="mt-8 font-medium">Variants</h2>
        <table class="mt-2 w-full max-w-2xl text-sm">
            <thead>
                <tr class="text-left text-stone-500">
                    <th>SKU</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Active</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="v in product.variants" :key="v.id" class="border-t">
                    <td class="py-2">{{ v.sku }}</td>
                    <td>{{ v.name ?? '—' }}</td>
                    <td>{{ v.price }}</td>
                    <td>{{ v.stock_quantity }}</td>
                    <td>{{ v.is_active ? 'Yes' : 'No' }}</td>
                </tr>
            </tbody>
        </table>
    </AdminLayout>
</template>
