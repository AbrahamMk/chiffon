<script setup>
defineProps({
    title: { type: String, default: 'Nothing here yet' },
    message: { type: String, default: '' },
});
</script>

<template>
    <div class="rounded-xl border border-dashed border-stone-300 bg-white px-6 py-10 text-center">
        <h3 class="text-base font-medium text-stone-800">{{ title }}</h3>
        <p v-if="message" class="mt-2 text-sm text-stone-500">{{ message }}</p>
        <div v-if="$slots.default" class="mt-4">
            <slot />
        </div>
    </div>
</template>
