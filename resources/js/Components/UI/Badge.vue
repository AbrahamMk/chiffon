<script setup>
defineProps({
    tone: {
        type: String,
        default: 'neutral',
    },
});

const tones = {
    neutral: 'bg-stone-100 text-stone-700',
    success: 'bg-emerald-50 text-emerald-700',
    warning: 'bg-amber-50 text-amber-800',
    danger: 'bg-rose-50 text-rose-700',
};
</script>

<template>
    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" :class="tones[tone] || tones.neutral">
        <slot />
    </span>
</template>
