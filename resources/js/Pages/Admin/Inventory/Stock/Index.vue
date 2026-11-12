<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    lowStockThreshold: Number,
    lowStockVariants: Object,
    recentTransactions: Object,
});

const adjustmentForm = useForm({
    product_variant_id: '',
    quantity_change: '',
    reason: '',
    inventory_location_id: '',
});

function submitAdjustment() {
    adjustmentForm.inventory_location_id = adjustmentForm.inventory_location_id || null;
    adjustmentForm.post(route('admin.inventory.stock.store'), {
        onSuccess: () => {
            adjustmentForm.reset('product_variant_id', 'quantity_change', 'reason', 'inventory_location_id');
        },
    });
}

function fillVariant(id) {
    adjustmentForm.product_variant_id = id;
}
</script>

<template>
    <Head title="Stock" />
    <AdminLayout>
        <h1 class="text-2xl font-semibold">Stock</h1>
        <p class="mt-1 text-sm text-stone-500">Low stock threshold: {{ lowStockThreshold }}</p>

        <h2 class="mt-8 font-medium">Adjust stock</h2>
        <form class="mt-3 max-w-md space-y-3 text-sm" @submit.prevent="submitAdjustment">
            <input
                v-model="adjustmentForm.product_variant_id"
                type="number"
                required
                class="w-full rounded border px-2 py-1"
                placeholder="Variant ID"
            />
            <input
                v-model="adjustmentForm.quantity_change"
                type="number"
                required
                class="w-full rounded border px-2 py-1"
                placeholder="Quantity change (+/−)"
            />
            <input
                v-model="adjustmentForm.reason"
                class="w-full rounded border px-2 py-1"
                placeholder="Reason (optional)"
            />
            <input
                v-model="adjustmentForm.inventory_location_id"
                type="number"
                class="w-full rounded border px-2 py-1"
                placeholder="Location ID (optional)"
            />
            <p v-if="adjustmentForm.errors.quantity_change" class="text-xs text-rose-600">
                {{ adjustmentForm.errors.quantity_change }}
            </p>
            <button
                type="submit"
                class="rounded bg-stone-900 px-4 py-2 text-sm text-white"
                :disabled="adjustmentForm.processing"
            >
                Apply adjustment
            </button>
        </form>

        <h2 class="mt-10 font-medium">Low stock variants</h2>
        <table class="mt-3 w-full text-sm">
            <thead>
                <tr class="text-left text-stone-500">
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Qty</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="v in lowStockVariants.data" :key="v.id" class="border-t">
                    <td class="py-2">{{ v.product?.name ?? '—' }}</td>
                    <td>{{ v.sku }}</td>
                    <td>{{ v.stock_quantity }}</td>
                    <td class="py-2 text-right">
                        <button type="button" class="text-sm underline" @click="fillVariant(v.id)">Adjust</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div v-if="lowStockVariants.links?.length > 3" class="mt-4 flex flex-wrap gap-2 text-sm">
            <template v-for="link in lowStockVariants.links" :key="'low-' + link.label">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    class="rounded border px-2 py-1"
                    :class="{ 'bg-stone-900 text-white': link.active }"
                    v-html="link.label"
                />
                <span v-else class="rounded border px-2 py-1 text-stone-400" v-html="link.label" />
            </template>
        </div>

        <h2 class="mt-10 font-medium">Recent transactions</h2>
        <table class="mt-3 w-full text-sm">
            <thead>
                <tr class="text-left text-stone-500">
                    <th>When</th>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Type</th>
                    <th>Change</th>
                    <th>After</th>
                    <th>By</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="t in recentTransactions.data" :key="t.id" class="border-t">
                    <td class="py-2 whitespace-nowrap">{{ t.created_at }}</td>
                    <td>{{ t.product_variant?.product?.name ?? '—' }}</td>
                    <td>{{ t.product_variant?.sku ?? '—' }}</td>
                    <td>{{ t.type }}</td>
                    <td>{{ t.quantity_change > 0 ? '+' : '' }}{{ t.quantity_change }}</td>
                    <td>{{ t.quantity_after }}</td>
                    <td>{{ t.user?.name ?? '—' }}</td>
                </tr>
            </tbody>
        </table>

        <div v-if="recentTransactions.links?.length > 3" class="mt-4 flex flex-wrap gap-2 text-sm">
            <template v-for="link in recentTransactions.links" :key="'tx-' + link.label">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    class="rounded border px-2 py-1"
                    :class="{ 'bg-stone-900 text-white': link.active }"
                    v-html="link.label"
                />
                <span v-else class="rounded border px-2 py-1 text-stone-400" v-html="link.label" />
            </template>
        </div>
    </AdminLayout>
</template>
