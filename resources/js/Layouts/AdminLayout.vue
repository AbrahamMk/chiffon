<script setup>
import { computed, ref } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import {
    BarChart3,
    CreditCard,
    Factory,
    FolderTree,
    LayoutDashboard,
    LogOut,
    Package,
    PackageCheck,
    PenLine,
    Settings2,
    ShoppingBag,
    SlidersHorizontal,
    Ticket,
    Truck,
    Users,
    Warehouse,
    Menu,
    X,
} from '@lucide/vue';

const page = usePage();
const brand = computed(() => page.props.brand);
const auth = computed(() => page.props.auth);
const flash = computed(() => page.props.flash);
const sidebarOpen = ref(false);

const logout = useForm({});

function permissions() {
    return auth.value.user?.permissions ?? [];
}

function can(permission) {
    return permissions().includes(permission);
}

function canAny(list) {
    return list.some((p) => can(p));
}

const nav = computed(() => {
    const items = [
        {
            label: 'Dashboard',
            href: route('admin.dashboard'),
            routeName: 'admin.dashboard',
            icon: LayoutDashboard,
            show: true,
        },
        {
            label: 'Products',
            href: route('admin.catalog.products.index'),
            routeName: 'admin.catalog.products.*',
            icon: Package,
            show: can('products.manage'),
        },
        {
            label: 'Categories',
            href: route('admin.catalog.categories.index'),
            routeName: 'admin.catalog.categories.*',
            icon: FolderTree,
            show: can('products.manage'),
        },
        {
            label: 'Stock',
            href: route('admin.inventory.stock.index'),
            routeName: 'admin.inventory.stock.*',
            icon: Warehouse,
            show: can('inventory.manage'),
        },
        {
            label: 'Suppliers',
            href: route('admin.inventory.suppliers.index'),
            routeName: 'admin.inventory.suppliers.*',
            icon: Truck,
            show: can('inventory.manage'),
        },
        {
            label: 'Orders',
            href: route('admin.orders.index'),
            routeName: 'admin.orders.*',
            icon: ShoppingBag,
            show: can('orders.view'),
        },
        {
            label: 'Payments',
            href: route('admin.payments.index'),
            routeName: 'admin.payments.*',
            icon: CreditCard,
            show: can('payments.manage'),
        },
        {
            label: 'Custom Orders',
            href: route('admin.custom-orders.index'),
            routeName: 'admin.custom-orders.*',
            icon: PenLine,
            show: can('custom_orders.manage'),
        },
        {
            label: 'Production',
            href: route('admin.production.index'),
            routeName: 'admin.production.*',
            icon: Factory,
            show: canAny(['production.manage', 'production.update']),
        },
        {
            label: 'Deliveries',
            href: route('admin.deliveries.index'),
            routeName: 'admin.deliveries.*',
            icon: PackageCheck,
            show: canAny(['deliveries.manage', 'deliveries.update']),
        },
        {
            label: 'Customers',
            href: route('admin.customers.index'),
            routeName: 'admin.customers.*',
            icon: Users,
            show: can('customers.manage'),
        },
        {
            label: 'Coupons',
            href: route('admin.marketing.coupons.index'),
            routeName: 'admin.marketing.coupons.*',
            icon: Ticket,
            show: can('marketing.manage'),
        },
        {
            label: 'Reports Sales',
            href: route('admin.reports.sales'),
            routeName: 'admin.reports.sales',
            icon: BarChart3,
            show: can('reports.view'),
        },
        {
            label: 'Brand Settings',
            href: route('admin.settings.brand.edit'),
            routeName: 'admin.settings.brand.*',
            icon: Settings2,
            show: can('settings.manage'),
        },
        {
            label: 'Operations',
            href: route('admin.settings.operational.edit'),
            routeName: 'admin.settings.operational.*',
            icon: SlidersHorizontal,
            show: can('settings.manage'),
        },
    ];

    return items.filter((item) => item.show);
});

const submitLogout = () => {
    logout.post(route('logout'));
};
</script>

<template>
    <div class="min-h-screen bg-stone-100 font-body text-stone-900">
        <div class="flex min-h-screen">
            <aside
                class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full border-r border-stone-200 bg-white transition lg:static lg:translate-x-0"
                :class="{ 'translate-x-0': sidebarOpen }"
            >
                <div class="flex h-16 items-center justify-between border-b border-stone-200 px-5">
                    <Link :href="route('admin.dashboard')" class="font-display text-2xl tracking-wide">
                        {{ brand.name }}
                    </Link>
                    <button type="button" class="lg:hidden" @click="sidebarOpen = false">
                        <X class="h-5 w-5" />
                    </button>
                </div>
                <nav class="space-y-1 p-3">
                    <Link
                        v-for="item in nav"
                        :key="item.href"
                        :href="item.href"
                        class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-stone-700 hover:bg-stone-100"
                        :class="{ 'bg-stone-100 font-medium': route().current(item.routeName) }"
                    >
                        <component :is="item.icon" class="h-4 w-4" />
                        {{ item.label }}
                    </Link>
                </nav>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                <header class="flex h-16 items-center justify-between border-b border-stone-200 bg-white px-4 sm:px-6">
                    <button type="button" class="rounded p-2 hover:bg-stone-100 lg:hidden" @click="sidebarOpen = true">
                        <Menu class="h-5 w-5" />
                    </button>
                    <div class="ml-auto flex items-center gap-4 text-sm">
                        <Link :href="route('home')" class="text-stone-500 hover:text-stone-800">View site</Link>
                        <span class="text-stone-700">{{ auth.user?.name }}</span>
                        <button
                            type="button"
                            class="inline-flex items-center gap-1 text-stone-500 hover:text-stone-800"
                            @click="submitLogout"
                        >
                            <LogOut class="h-4 w-4" />
                            Logout
                        </button>
                    </div>
                </header>

                <div
                    v-if="flash.success"
                    class="mx-4 mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 sm:mx-6"
                >
                    {{ flash.success }}
                </div>
                <div
                    v-if="flash.error"
                    class="mx-4 mt-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 sm:mx-6"
                >
                    {{ flash.error }}
                </div>

                <main class="flex-1 p-4 sm:p-6">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
