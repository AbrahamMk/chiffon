<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';

const props = defineProps({
    cart: Object,
    deliveryZones: Array,
    paymentMethods: Array,
    guestCheckoutEnabled: Boolean,
});

const user = usePage().props.auth?.user;

const form = useForm({
    customer_name: user?.name ?? '',
    customer_phone: '',
    customer_email: user?.email ?? '',
    fulfillment_type: 'delivery',
    delivery_zone_id: props.deliveryZones[0]?.id ?? null,
    city: '',
    subcity: '',
    address_line: '',
    delivery_instructions: '',
    coupon_code: '',
    payment_method_id: props.paymentMethods[0]?.id ?? null,
});
</script>

<template>
    <Head title="Checkout" />
    <StorefrontLayout>
        <div class="mx-auto max-w-2xl px-4 py-12">
            <h1 class="font-display text-4xl">Checkout</h1>
            <p v-if="!guestCheckoutEnabled && !user" class="mt-4 text-red-700">Sign in to complete checkout.</p>
            <form class="mt-8 space-y-4" @submit.prevent="form.post(route('checkout.store'))">
                <input v-model="form.customer_name" required class="w-full rounded border px-3 py-2" placeholder="Full name" />
                <input v-model="form.customer_phone" required class="w-full rounded border px-3 py-2" placeholder="Phone" />
                <input v-model="form.customer_email" type="email" class="w-full rounded border px-3 py-2" placeholder="Email" />
                <select v-model="form.delivery_zone_id" class="w-full rounded border px-3 py-2">
                    <option v-for="z in deliveryZones" :key="z.id" :value="z.id">{{ z.name }} ({{ z.fee }})</option>
                </select>
                <textarea v-model="form.address_line" class="w-full rounded border px-3 py-2" placeholder="Address" />
                <button class="rounded-full bg-stone-900 px-6 py-3 text-sm text-white" :disabled="form.processing">Place order — {{ cart.subtotal }}</button>
            </form>
        </div>
    </StorefrontLayout>
</template>
