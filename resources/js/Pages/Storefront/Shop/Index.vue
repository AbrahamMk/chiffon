<script setup>
import { Head, router } from '@inertiajs/vue3';
import ProductCard from '@/Components/Storefront/ProductCard.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
});

function applyFilters(overrides = {}) {
    router.get(route('shop.index'), { ...props.filters, ...overrides }, { preserveState: true });
}
</script>

<template>
    <Head title="Shop" />
    <StorefrontLayout>
        <div class="mx-auto max-w-7xl px-4 py-12">
            <h1 class="font-display text-4xl">Shop</h1>
            <div class="mt-8 flex flex-wrap gap-4">
                <input
                    type="search"
                    class="rounded-lg border px-3 py-2"
                    placeholder="Search"
                    :value="filters.q"
                    @change="applyFilters({ q: $event.target.value })"
                />
                <select
                    class="rounded-lg border px-3 py-2"
                    :value="filters.category"
                    @change="applyFilters({ category: $event.target.value || null })"
                >
                    <option value="">All categories</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.slug">{{ cat.name }}</option>
                </select>
                <select
                    class="rounded-lg border px-3 py-2"
                    :value="filters.sort"
                    @change="applyFilters({ sort: $event.target.value })"
                >
                    <option value="newest">Newest</option>
                    <option value="price_asc">Price ↑</option>
                    <option value="price_desc">Price ↓</option>
                    <option value="name">Name</option>
                </select>
            </div>

            <div v-if="products.data?.length" class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
            </div>
            <p v-else class="mt-10 text-[var(--aster-muted)]">No products match these filters yet.</p>
        </div>
    </StorefrontLayout>
</template>
