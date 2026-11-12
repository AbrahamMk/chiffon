<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import ProductCard from '@/Components/Storefront/ProductCard.vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';

const page = usePage();
const brand = computed(() => page.props.brand);

defineProps({
    featuredProducts: { type: Array, default: () => [] },
    newArrivals: { type: Array, default: () => [] },
    promotions: { type: Array, default: () => [] },
    socialPosts: { type: Array, default: () => [] },
});
</script>

<template>
    <Head :title="brand.name" />

    <StorefrontLayout>
        <section class="relative overflow-hidden">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:items-center lg:px-8 lg:py-24">
                <div>
                    <p class="text-xs uppercase tracking-[0.28em] text-[var(--aster-muted)]">Ethiopian fashion</p>
                    <h1
                        class="mt-4 font-display text-5xl leading-tight sm:text-6xl lg:text-7xl"
                        :style="{ color: brand.primary_color }"
                    >
                        {{ brand.name }}
                    </h1>
                    <p class="mt-5 max-w-xl text-lg text-[var(--aster-muted)]">
                        {{ brand.tagline || 'Premium Habesha fashion — collections, custom designs, and a complete business platform.' }}
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <Link
                            :href="route('shop.index')"
                            class="rounded-full px-6 py-3 text-sm uppercase tracking-[0.18em] text-white"
                            :style="{ backgroundColor: brand.primary_color }"
                        >
                            Explore Collection
                        </Link>
                        <Link
                            :href="route('about')"
                            class="rounded-full border border-black/15 px-6 py-3 text-sm uppercase tracking-[0.18em]"
                        >
                            Discover Our Story
                        </Link>
                        <Link
                            v-if="brand.features.custom_orders_enabled"
                            :href="route('custom-orders.create')"
                            class="rounded-full border px-6 py-3 text-sm uppercase tracking-[0.18em]"
                            :style="{ borderColor: brand.accent_color, color: brand.primary_color }"
                        >
                            Request a Custom Design
                        </Link>
                    </div>
                </div>

                <div class="relative min-h-[420px] overflow-hidden rounded-[2rem] shadow-2xl shadow-black/10">
                    <img
                        v-if="featuredProducts[0]?.image"
                        :src="featuredProducts[0].image"
                        :alt="featuredProducts[0].name"
                        class="absolute inset-0 h-full w-full object-cover"
                    />
                    <div
                        v-else
                        class="absolute inset-0"
                        :style="{
                            background: `linear-gradient(145deg, ${brand.primary_color}, ${brand.accent_color} 70%, #2b211c)`,
                        }"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/10 to-transparent" />
                    <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
                        <p class="font-display text-4xl">
                            {{ featuredProducts[0]?.name || 'Tibeb · Chiffon · Habesha' }}
                        </p>
                        <p class="mt-2 max-w-md text-sm text-white/80">
                            {{ featuredProducts[0] ? 'Featured from the collection' : 'Upload product photos to fill this hero.' }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl space-y-16 px-4 py-12 sm:px-6 lg:px-8">
            <div v-if="featuredProducts.length">
                <h2 class="font-display text-3xl">Featured</h2>
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <ProductCard v-for="p in featuredProducts" :key="p.id" :product="p" />
                </div>
            </div>

            <div v-if="newArrivals.length">
                <h2 class="font-display text-3xl">New arrivals</h2>
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <ProductCard v-for="p in newArrivals" :key="p.id" :product="p" />
                </div>
            </div>

            <div v-if="promotions.length">
                <h2 class="font-display text-3xl">Promotions</h2>
                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <a
                        v-for="promo in promotions"
                        :key="promo.id"
                        :href="promo.link_url || '#'"
                        class="overflow-hidden rounded-xl border"
                    >
                        <div class="aspect-[16/10] bg-stone-100">
                            <img
                                v-if="promo.banner_url || promo.banner_path"
                                :src="promo.banner_url || promo.banner_path"
                                :alt="promo.title"
                                class="h-full w-full object-cover"
                            />
                        </div>
                        <div class="p-4">
                            <p class="font-medium">{{ promo.title }}</p>
                        </div>
                    </a>
                </div>
            </div>

            <div v-if="socialPosts.length">
                <h2 class="font-display text-3xl">From social</h2>
                <div class="mt-6 grid gap-3 sm:grid-cols-3">
                    <a
                        v-for="post in socialPosts"
                        :key="post.id"
                        :href="post.url || '#'"
                        class="overflow-hidden rounded-lg border"
                    >
                        <div class="aspect-square bg-stone-100">
                            <img
                                v-if="post.media_url || post.media_path"
                                :src="post.media_url || post.media_path"
                                :alt="post.title"
                                class="h-full w-full object-cover"
                            />
                        </div>
                        <p class="p-3 text-sm">{{ post.platform }} · {{ post.title }}</p>
                    </a>
                </div>
            </div>
        </section>
    </StorefrontLayout>
</template>
