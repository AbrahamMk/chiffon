<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    product: { type: Object, required: true },
    showPrice: { type: Boolean, default: true },
});
</script>

<template>
    <Link
        :href="route('shop.show', product.slug)"
        class="group block overflow-hidden rounded-xl border border-black/5 bg-white/70 transition hover:-translate-y-0.5 hover:shadow-md"
    >
        <div class="aspect-[4/5] overflow-hidden bg-[linear-gradient(145deg,#efe7dc,#d9cfc3)]">
            <img
                v-if="product.image"
                :src="product.image"
                :alt="product.name"
                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                loading="lazy"
            />
            <div v-else class="flex h-full w-full flex-col items-center justify-center px-4 text-center">
                <p class="font-display text-2xl text-[var(--aster-charcoal)]/70">{{ product.name }}</p>
                <p class="mt-2 text-xs uppercase tracking-[0.18em] text-[var(--aster-muted)]">Image coming soon</p>
            </div>
        </div>
        <div class="p-4">
            <p class="font-medium text-[var(--aster-charcoal)]">{{ product.name }}</p>
            <p v-if="product.category?.name" class="mt-1 text-xs uppercase tracking-[0.14em] text-[var(--aster-muted)]">
                {{ product.category.name }}
            </p>
            <p v-if="showPrice && product.min_price != null" class="mt-2 text-sm text-[var(--aster-muted)]">
                From {{ product.min_price }} ETB
            </p>
        </div>
    </Link>
</template>
