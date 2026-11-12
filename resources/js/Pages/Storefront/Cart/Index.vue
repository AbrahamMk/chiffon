<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';

defineProps({ cart: Object });

function updateQty(id, quantity) {
    router.patch(route('cart.update', id), { quantity });
}

function removeItem(id) {
    router.delete(route('cart.destroy', id));
}
</script>

<template>
    <Head title="Cart" />
    <StorefrontLayout>
        <div class="mx-auto max-w-3xl px-4 py-12">
            <h1 class="font-display text-4xl">Your cart</h1>
            <p v-if="!cart.items?.length" class="mt-6 text-stone-500">Your cart is empty.</p>
            <ul v-else class="mt-8 divide-y">
                <li v-for="item in cart.items" :key="item.id" class="flex items-center gap-4 py-4">
                    <div class="h-20 w-20 shrink-0 overflow-hidden rounded-lg bg-stone-100">
                        <img v-if="item.image" :src="item.image" :alt="item.product_name" class="h-full w-full object-cover" />
                        <div v-else class="flex h-full items-center justify-center text-[10px] uppercase tracking-wide text-stone-400">
                            No image
                        </div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-medium">{{ item.product_name }}</p>
                        <p class="text-sm text-stone-500">{{ item.variant_label }}</p>
                    </div>
                    <input
                        type="number"
                        min="0"
                        class="w-20 rounded border px-2 py-1"
                        :value="item.quantity"
                        @change="updateQty(item.id, Number($event.target.value))"
                    />
                    <p class="w-24 text-right text-sm">{{ item.line_total }}</p>
                    <button class="text-sm text-red-700" @click="removeItem(item.id)">Remove</button>
                </li>
            </ul>
            <p v-if="cart.items?.length" class="mt-6 text-right text-lg font-semibold">Subtotal: {{ cart.subtotal }} ETB</p>
            <Link
                v-if="cart.items?.length"
                :href="route('checkout.create')"
                class="mt-6 inline-block rounded-full bg-stone-900 px-6 py-3 text-sm text-white"
            >
                Checkout
            </Link>
        </div>
    </StorefrontLayout>
</template>
