<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { CalendarCheck, Images } from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Citas',
        href: '/admin/citas',
        icon: CalendarCheck,
    },
    {
        title: 'Resultados',
        href: '/admin/resultados',
        icon: Images,
    },
];

const page = usePage();
const headerLogo = computed(() => page.props.branding?.header_logo ?? '');
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader class="p-0">
            <Link
                v-if="headerLogo"
                href="/admin/citas"
                class="flex w-full items-center justify-center py-1 transition hover:bg-sidebar-accent/50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sidebar-ring dark:rounded-xl dark:bg-white/90 dark:p-2"
                aria-label="Ir a la agenda de citas"
            >
                <img
                    :src="headerLogo"
                    :alt="page.props.branding?.name ?? 'LuDa Dental'"
                    class="h-auto max-h-24 w-full object-contain group-data-[collapsible=icon]:hidden"
                />
                <AppLogoIcon
                    class="hidden size-7 fill-current text-luda-navy group-data-[collapsible=icon]:block"
                />
            </Link>

            <SidebarMenu v-else>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/admin/citas">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
