<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import ProductCard from '@/Components/Storefront/ProductCard.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';

const props = defineProps({ product: Object, related: Array });

const form = useForm({
    variant_id: props.product.variants[0]?.id,
    quantity: 1,
});

const activeImage = computed(() => {
    const images = props.product.images || [];
    return images.find((img) => img.is_primary) || images[0] || null;
});

function addToCart() {
    form.post(route('cart.store'), { preserveScroll: true });
}
</script>

<template>
    <Head :title="product.name" />
    <StorefrontLayout>
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 lg:grid-cols-2">
            <div>
                <div class="aspect-[4/5] overflow-hidden rounded-2xl bg-[linear-gradient(145deg,#efe7dc,#d9cfc3)]">
                    <img
                        v-if="activeImage"
                        :src="activeImage.url"
                        :alt="activeImage.alt_text || product.name"
                        class="h-full w-full object-cover"
                    />
                    <div v-else class="flex h-full items-center justify-center px-6 text-center">
                        <p class="font-display text-3xl text-[var(--aster-charcoal)]/60">Image coming soon</p>
                    </div>
                </div>
                <div v-if="product.images?.length > 1" class="mt-3 grid grid-cols-4 gap-2">
                    <img
                        v-for="img in product.images"
                        :key="img.id"
                        :src="img.url"
                        :alt="img.alt_text || product.name"
                        class="aspect-square rounded-lg object-cover"
                    />
                </div>
            </div>

            <div>
                <h1 class="font-display text-4xl">{{ product.name }}</h1>
                <p class="mt-4 text-stone-600">{{ product.description }}</p>
                <p v-if="product.fabric_info" class="mt-4 text-sm text-stone-500">{{ product.fabric_info }}</p>

                <label class="mt-6 block text-sm">Variant</label>
                <select v-model="form.variant_id" class="mt-1 w-full rounded-lg border px-3 py-2">
                    <option v-for="v in product.variants" :key="v.id" :value="v.id">
                        {{ v.name || v.sku }} — {{ v.price }} ETB
                        <template v-if="v.stock_quantity < 1"> (out of stock)</template>
                    </option>
                </select>

                <label class="mt-4 block text-sm">Quantity</label>
                <input v-model.number="form.quantity" type="number" min="1" max="99" class="mt-1 w-28 rounded-lg border px-3 py-2" />

                <button class="mt-6 rounded-full bg-stone-900 px-6 py-3 text-sm text-white" @click="addToCart">
                    Add to cart
                </button>
            </div>
        </div>

        <section v-if="related?.length" class="mx-auto max-w-7xl px-4 pb-16">
            <h2 class="font-display text-2xl">Related</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <ProductCard v-for="p in related" :key="p.id" :product="p" />
            </div>
        </section>
    </StorefrontLayout>
</template>
