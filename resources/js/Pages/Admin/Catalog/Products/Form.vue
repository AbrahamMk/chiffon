<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Plus, Trash2 } from '@lucide/vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    product: { type: Object, default: null },
    categories: Array,
});

const emptyVariant = () => ({
    sku: '',
    name: '',
    price: '',
    stock_quantity: 0,
    is_active: true,
});

const form = useForm({
    name: props.product?.name ?? '',
    category: props.product?.product_category_id ?? props.product?.category?.id ?? '',
    description: props.product?.description ?? '',
    fabric_info: props.product?.fabric_info ?? '',
    care_instructions: props.product?.care_instructions ?? '',
    base_price: props.product?.base_price ?? '',
    compare_at_price: props.product?.compare_at_price ?? '',
    status: props.product?.status ?? 'draft',
    is_featured: props.product?.is_featured ?? false,
    variants: props.product ? [] : [emptyVariant()],
});

const imageForm = useForm({
    images: [],
    alt_text: '',
});

function addVariant() {
    form.variants.push(emptyVariant());
}

function removeVariant(index) {
    form.variants.splice(index, 1);
}

function submit() {
    form.category = form.category || null;
    form.compare_at_price = form.compare_at_price === '' ? null : form.compare_at_price;

    if (props.product) {
        form.put(route('admin.catalog.products.update', props.product.id));
    } else {
        form.post(route('admin.catalog.products.store'));
    }
}

function onImageSelect(event) {
    imageForm.images = Array.from(event.target.files || []);
}

function uploadImages() {
    if (!props.product || !imageForm.images.length) {
        return;
    }

    imageForm.post(route('admin.catalog.products.images.store', props.product.id), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            imageForm.reset();
        },
    });
}

function makePrimary(imageId) {
    router.patch(route('admin.catalog.products.images.primary', [props.product.id, imageId]), {}, { preserveScroll: true });
}

function removeImage(imageId) {
    if (!confirm('Remove this image?')) {
        return;
    }

    router.delete(route('admin.catalog.products.images.destroy', [props.product.id, imageId]), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head :title="product ? 'Edit product' : 'New product'" />
    <AdminLayout>
        <Link
            :href="product ? route('admin.catalog.products.show', product.id) : route('admin.catalog.products.index')"
            class="text-sm text-stone-500 underline"
        >
            ← Back
        </Link>
        <h1 class="mt-2 text-2xl font-semibold">{{ product ? 'Edit product' : 'New product' }}</h1>

        <form class="mt-6 max-w-2xl space-y-3" @submit.prevent="submit">
            <input v-model="form.name" required class="w-full rounded border px-2 py-1" placeholder="Name" />

            <select v-model="form.category" class="w-full rounded border px-2 py-1">
                <option value="">Uncategorized</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>

            <textarea v-model="form.description" rows="3" class="w-full rounded border px-2 py-1" placeholder="Description" />
            <textarea v-model="form.fabric_info" rows="2" class="w-full rounded border px-2 py-1" placeholder="Fabric info" />
            <textarea
                v-model="form.care_instructions"
                rows="2"
                class="w-full rounded border px-2 py-1"
                placeholder="Care instructions"
            />

            <div class="flex flex-wrap gap-3">
                <input
                    v-model="form.base_price"
                    type="number"
                    step="0.01"
                    min="0"
                    required
                    class="w-full rounded border px-2 py-1 sm:flex-1"
                    placeholder="Base price"
                />
                <input
                    v-model="form.compare_at_price"
                    type="number"
                    step="0.01"
                    min="0"
                    class="w-full rounded border px-2 py-1 sm:flex-1"
                    placeholder="Compare at price"
                />
            </div>

            <select v-model="form.status" required class="w-full rounded border px-2 py-1">
                <option value="draft">Draft</option>
                <option value="active">Active</option>
                <option value="archived">Archived</option>
            </select>

            <label class="flex items-center gap-2 text-sm">
                <input v-model="form.is_featured" type="checkbox" />
                Featured
            </label>

            <div v-if="!product" class="border-t pt-4">
                <div class="flex items-center justify-between">
                    <h2 class="font-medium">Variants</h2>
                    <button type="button" class="inline-flex items-center gap-1 text-sm underline" @click="addVariant">
                        <Plus class="h-4 w-4" />
                        Add row
                    </button>
                </div>
                <div
                    v-for="(variant, index) in form.variants"
                    :key="index"
                    class="mt-3 grid gap-2 rounded border border-stone-200 p-3 sm:grid-cols-2"
                >
                    <input v-model="variant.sku" required class="rounded border px-2 py-1" placeholder="SKU" />
                    <input v-model="variant.name" class="rounded border px-2 py-1" placeholder="Variant name" />
                    <input
                        v-model="variant.price"
                        type="number"
                        step="0.01"
                        min="0"
                        required
                        class="rounded border px-2 py-1"
                        placeholder="Price"
                    />
                    <input
                        v-model.number="variant.stock_quantity"
                        type="number"
                        min="0"
                        class="rounded border px-2 py-1"
                        placeholder="Stock"
                    />
                    <label class="flex items-center gap-2 text-sm sm:col-span-2">
                        <input v-model="variant.is_active" type="checkbox" />
                        Active
                    </label>
                    <button
                        v-if="form.variants.length > 1"
                        type="button"
                        class="inline-flex items-center gap-1 text-sm text-rose-600 sm:col-span-2"
                        @click="removeVariant(index)"
                    >
                        <Trash2 class="h-4 w-4" />
                        Remove
                    </button>
                </div>
            </div>

            <div v-else-if="product.variants?.length" class="border-t pt-4 text-sm">
                <h2 class="font-medium">Variants</h2>
                <p class="mt-1 text-stone-500">Edit stock via Inventory → Stock.</p>
                <ul class="mt-2 divide-y">
                    <li v-for="v in product.variants" :key="v.id" class="py-2">
                        {{ v.sku }} — {{ v.name || '—' }} ({{ v.stock_quantity }} in stock)
                    </li>
                </ul>
            </div>

            <button
                type="submit"
                class="rounded bg-stone-900 px-4 py-2 text-sm text-white"
                :disabled="form.processing"
            >
                Save
            </button>
        </form>

        <div v-if="product" class="mt-10 max-w-3xl border-t pt-6">
            <h2 class="text-lg font-medium">Product images</h2>
            <p class="mt-1 text-sm text-stone-500">Upload after the product is created. First image becomes primary.</p>

            <div v-if="product.images?.length" class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                <div v-for="img in product.images" :key="img.id" class="overflow-hidden rounded-lg border">
                    <img :src="img.url" :alt="img.alt_text || product.name" class="aspect-square w-full object-cover" />
                    <div class="flex items-center justify-between gap-2 px-2 py-2 text-xs">
                        <button
                            v-if="!img.is_primary"
                            type="button"
                            class="underline"
                            @click="makePrimary(img.id)"
                        >
                            Make primary
                        </button>
                        <span v-else class="text-stone-500">Primary</span>
                        <button type="button" class="text-rose-600 underline" @click="removeImage(img.id)">Remove</button>
                    </div>
                </div>
            </div>

            <form class="mt-4 space-y-3" @submit.prevent="uploadImages">
                <input type="file" accept="image/*" multiple class="block w-full text-sm" @change="onImageSelect" />
                <input
                    v-model="imageForm.alt_text"
                    type="text"
                    class="w-full rounded border px-2 py-1 text-sm"
                    placeholder="Alt text (optional)"
                />
                <button
                    type="submit"
                    class="rounded border border-stone-300 px-3 py-2 text-sm"
                    :disabled="imageForm.processing || !imageForm.images.length"
                >
                    Upload images
                </button>
            </form>
        </div>
    </AdminLayout>
</template>
