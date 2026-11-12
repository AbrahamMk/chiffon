<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, ShoppingBag, User, X } from '@lucide/vue';
import { ref } from 'vue';

const page = usePage();
const brand = computed(() => page.props.brand);
const auth = computed(() => page.props.auth);
const mobileOpen = ref(false);

const nav = [
    { label: 'Home', href: route('home') },
    { label: 'Shop', href: route('shop.index') },
    { label: 'Collections', href: route('collections.index') },
    { label: 'Custom Orders', href: route('custom-orders.create') },
    { label: 'About', href: route('about') },
    { label: 'Contact', href: route('contact') },
];
</script>

<template>
    <div class="min-h-screen font-body text-[var(--aster-charcoal)]">
        <div
            class="pointer-events-none fixed inset-0 -z-10"
            :style="{
                background: `radial-gradient(circle at top right, ${brand.accent_color}22, transparent 40%), linear-gradient(180deg, var(--aster-ivory), var(--aster-offwhite) 45%, #efe7dc)`,
            }"
        />

        <header class="border-b border-black/5 bg-[var(--aster-offwhite)]/80 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <Link :href="route('home')" class="font-display text-3xl tracking-wide" :style="{ color: brand.primary_color }">
                    {{ brand.name }}
                </Link>

                <nav class="hidden items-center gap-6 lg:flex">
                    <Link
                        v-for="item in nav"
                        :key="item.href"
                        :href="item.href"
                        class="text-sm uppercase tracking-[0.18em] text-[var(--aster-muted)] transition hover:text-[var(--aster-charcoal)]"
                    >
                        {{ item.label }}
                    </Link>
                </nav>

                <div class="flex items-center gap-3">
                    <Link :href="route('shop.index')" class="rounded-full p-2 text-[var(--aster-charcoal)] hover:bg-black/5">
                        <ShoppingBag class="h-5 w-5" />
                    </Link>
                    <Link
                        v-if="auth.user"
                        :href="auth.user.is_staff ? route('admin.dashboard') : route('profile.edit')"
                        class="rounded-full p-2 hover:bg-black/5"
                    >
                        <User class="h-5 w-5" />
                    </Link>
                    <Link
                        v-else
                        :href="route('login')"
                        class="hidden text-sm uppercase tracking-[0.16em] sm:inline"
                    >
                        Account
                    </Link>
                    <button type="button" class="rounded-full p-2 lg:hidden" @click="mobileOpen = !mobileOpen">
                        <X v-if="mobileOpen" class="h-5 w-5" />
                        <Menu v-else class="h-5 w-5" />
                    </button>
                </div>
            </div>

            <div v-if="mobileOpen" class="border-t border-black/5 px-4 py-4 lg:hidden">
                <div class="flex flex-col gap-3">
                    <Link
                        v-for="item in nav"
                        :key="`m-${item.href}`"
                        :href="item.href"
                        class="text-sm uppercase tracking-[0.16em]"
                        @click="mobileOpen = false"
                    >
                        {{ item.label }}
                    </Link>
                </div>
            </div>
        </header>

        <main>
            <slot />
        </main>

        <footer class="mt-20 border-t border-black/10 bg-[var(--aster-charcoal)] text-[var(--aster-ivory)]">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:px-6 md:grid-cols-3 lg:px-8">
                <div>
                    <p class="font-display text-3xl">{{ brand.name }}</p>
                    <p class="mt-3 max-w-sm text-sm text-white/70">{{ brand.tagline || brand.description }}</p>
                </div>
                <div class="text-sm text-white/70">
                    <p class="mb-2 uppercase tracking-[0.18em] text-white/90">Contact</p>
                    <p>{{ brand.contact_email || 'Add contact email in Settings' }}</p>
                    <p>{{ brand.contact_phone || 'Add phone in Settings' }}</p>
                </div>
                <div class="text-sm text-white/70">
                    <p class="mb-2 uppercase tracking-[0.18em] text-white/90">Follow</p>
                    <div class="flex flex-col gap-1">
                        <a v-if="brand.social.instagram" :href="brand.social.instagram" target="_blank" rel="noopener">Instagram</a>
                        <a v-if="brand.social.tiktok" :href="brand.social.tiktok" target="_blank" rel="noopener">TikTok</a>
                        <a v-if="brand.social.facebook" :href="brand.social.facebook" target="_blank" rel="noopener">Facebook</a>
                        <a v-if="brand.social.youtube" :href="brand.social.youtube" target="_blank" rel="noopener">YouTube</a>
                        <p v-if="!brand.social.instagram && !brand.social.tiktok && !brand.social.facebook && !brand.social.youtube">
                            Social links configurable in Settings
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
