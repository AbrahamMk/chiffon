<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    settings: { type: Object, required: true },
});

const form = useForm({ ...props.settings });

const submit = () => {
    form.put(route('admin.settings.brand.update'));
};
</script>

<template>
    <Head title="Brand Settings" />
    <AdminLayout>
        <div class="mx-auto max-w-3xl space-y-6">
            <div>
                <h1 class="text-2xl font-semibold">Brand settings</h1>
                <p class="mt-1 text-sm text-stone-500">
                    Configure the working brand identity. Leave contact fields empty until real business details are confirmed.
                </p>
            </div>

            <form class="space-y-5 rounded-xl border border-stone-200 bg-white p-6" @submit.prevent="submit">
                <div>
                    <InputLabel for="brand_name" value="Business name" />
                    <TextInput id="brand_name" v-model="form.brand_name" class="mt-1 block w-full" required />
                    <InputError class="mt-1" :message="form.errors.brand_name" />
                </div>

                <div>
                    <InputLabel for="brand_tagline" value="Tagline" />
                    <TextInput id="brand_tagline" v-model="form.brand_tagline" class="mt-1 block w-full" />
                    <InputError class="mt-1" :message="form.errors.brand_tagline" />
                </div>

                <div>
                    <InputLabel for="brand_description" value="Description" />
                    <textarea
                        id="brand_description"
                        v-model="form.brand_description"
                        class="mt-1 w-full rounded-md border-stone-300 shadow-sm focus:border-stone-500 focus:ring-stone-500"
                        rows="4"
                    />
                    <InputError class="mt-1" :message="form.errors.brand_description" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <InputLabel for="brand_primary_color" value="Primary color" />
                        <TextInput id="brand_primary_color" v-model="form.brand_primary_color" class="mt-1 block w-full" />
                        <InputError class="mt-1" :message="form.errors.brand_primary_color" />
                    </div>
                    <div>
                        <InputLabel for="brand_accent_color" value="Accent color" />
                        <TextInput id="brand_accent_color" v-model="form.brand_accent_color" class="mt-1 block w-full" />
                        <InputError class="mt-1" :message="form.errors.brand_accent_color" />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <InputLabel for="currency" value="Currency" />
                        <TextInput id="currency" v-model="form.currency" class="mt-1 block w-full" maxlength="3" />
                        <InputError class="mt-1" :message="form.errors.currency" />
                    </div>
                    <div>
                        <InputLabel for="timezone" value="Timezone" />
                        <TextInput id="timezone" v-model="form.timezone" class="mt-1 block w-full" />
                        <InputError class="mt-1" :message="form.errors.timezone" />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <InputLabel for="contact_email" value="Contact email (optional)" />
                        <TextInput id="contact_email" v-model="form.contact_email" type="email" class="mt-1 block w-full" />
                        <InputError class="mt-1" :message="form.errors.contact_email" />
                    </div>
                    <div>
                        <InputLabel for="contact_phone" value="Contact phone (optional)" />
                        <TextInput id="contact_phone" v-model="form.contact_phone" class="mt-1 block w-full" />
                        <InputError class="mt-1" :message="form.errors.contact_phone" />
                    </div>
                </div>

                <div>
                    <InputLabel for="business_address" value="Business address (optional)" />
                    <TextInput id="business_address" v-model="form.business_address" class="mt-1 block w-full" />
                    <InputError class="mt-1" :message="form.errors.business_address" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <InputLabel for="social_instagram" value="Instagram URL" />
                        <TextInput id="social_instagram" v-model="form.social_instagram" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel for="social_tiktok" value="TikTok URL" />
                        <TextInput id="social_tiktok" v-model="form.social_tiktok" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel for="social_facebook" value="Facebook URL" />
                        <TextInput id="social_facebook" v-model="form.social_facebook" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel for="social_youtube" value="YouTube URL" />
                        <TextInput id="social_youtube" v-model="form.social_youtube" class="mt-1 block w-full" />
                    </div>
                </div>

                <PrimaryButton :disabled="form.processing">Save brand settings</PrimaryButton>
            </form>
        </div>
    </AdminLayout>
</template>
