<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    settings: { type: Object, required: true },
});

const form = useForm({ ...props.settings });

const submit = () => {
    form.put(route('admin.settings.operational.update'));
};
</script>

<template>
    <Head title="Operational Settings" />
    <AdminLayout>
        <div class="mx-auto max-w-2xl space-y-6">
            <div>
                <h1 class="text-2xl font-semibold">Operational settings</h1>
                <p class="mt-1 text-sm text-stone-500">Feature flags for day-to-day commerce behaviour.</p>
            </div>

            <form class="space-y-5 rounded-xl border border-stone-200 bg-white p-6" @submit.prevent="submit">
                <label class="flex items-center gap-3 text-sm">
                    <Checkbox v-model:checked="form.custom_orders_enabled" />
                    Enable custom orders
                </label>
                <label class="flex items-center gap-3 text-sm">
                    <Checkbox v-model:checked="form.customer_registration_enabled" />
                    Enable customer registration
                </label>
                <label class="flex items-center gap-3 text-sm">
                    <Checkbox v-model:checked="form.guest_checkout_enabled" />
                    Enable guest checkout
                </label>
                <label class="flex items-center gap-3 text-sm">
                    <Checkbox v-model:checked="form.allow_negative_stock" />
                    Allow negative stock
                </label>

                <div>
                    <InputLabel for="low_stock_threshold" value="Low stock threshold" />
                    <TextInput
                        id="low_stock_threshold"
                        v-model="form.low_stock_threshold"
                        type="number"
                        min="0"
                        class="mt-1 block w-full"
                        required
                    />
                    <InputError class="mt-1" :message="form.errors.low_stock_threshold" />
                </div>

                <PrimaryButton :disabled="form.processing">Save operational settings</PrimaryButton>
            </form>
        </div>
    </AdminLayout>
</template>
