<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';

const form = useForm({
    customer_name: '',
    customer_phone: '',
    customer_email: '',
    clothing_type: '',
    design_description: '',
    quantity: 1,
    measurements: [{ name: 'Bust', value: '', unit: 'cm' }],
    images: [],
});

function submit() {
    form.post(route('custom-orders.store'), { forceFormData: true });
}
</script>

<template>
    <Head title="Custom order" />
    <StorefrontLayout>
        <div class="mx-auto max-w-2xl px-4 py-12">
            <h1 class="font-display text-4xl">Request a custom design</h1>
            <form class="mt-8 space-y-4" @submit.prevent="submit">
                <input v-model="form.customer_name" required class="w-full rounded border px-3 py-2" placeholder="Name" />
                <input v-model="form.customer_phone" required class="w-full rounded border px-3 py-2" placeholder="Phone" />
                <textarea v-model="form.design_description" class="w-full rounded border px-3 py-2" placeholder="Describe your design" />
                <input type="file" multiple accept="image/*" @change="form.images = $event.target.files" />
                <button class="rounded-full bg-stone-900 px-6 py-3 text-sm text-white">Submit request</button>
            </form>
        </div>
    </StorefrontLayout>
</template>
