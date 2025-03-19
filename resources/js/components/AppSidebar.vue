<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    LayoutDashboard,
    Gift,
    Heart,
    Settings,
    BookOpen,
    Github
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

interface User {
    role: 'donor' | 'charity' | 'admin';
}

const page = usePage<{ auth: { user: User } }>();
const userRole = computed(() => page.props.auth.user?.role || 'donor');

// Get the dashboard route based on user role
const dashboardRoute = computed(() => {
    const role = userRole.value;
    switch (role) {
        case 'donor':
            return route('donor.dashboard');
        case 'charity':
            return route('charity.dashboard');
        case 'admin':
            return route('admin.dashboard');
        default:
            return route('donor.dashboard');
    }
});

// Donor-specific navigation items
const donorNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: route('donor.dashboard'),
        icon: LayoutDashboard,
    },
    {
        title: 'My Donations',
        href: route('donor.my-donations'),
        icon: Gift,
    },
    {
        title: 'Browse Donations',
        href: route('donations.index'),
        icon: Heart,
    },
    {
        title: 'Notifications',
        href: route('notifications.index'),
        icon: BookOpen,
    }
];

// Charity-specific navigation items
const charityNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: route('charity.dashboard'),
        icon: LayoutDashboard,
    },
    {
        title: 'My Claims',
        href: route('charity.claims.index'),
        icon: Gift,
    },
    {
        title: 'Browse Donations',
        href: route('donations.index'),
        icon: Heart,
    },
    {
        title: 'Notifications',
        href: route('notifications.index'),
        icon: BookOpen,
    }
];

// Admin-specific navigation items (placeholder for now)
const adminNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: route('admin.dashboard'),
        icon: LayoutDashboard,
    }
];

// Common footer items for all users
const footerNavItems: NavItem[] = [
    {
        title: 'Profile Settings',
        href: route('profile.edit'),
        icon: Settings,
    },
    {
        title: 'Documentation',
        href: 'https://charitybridge.com/docs',
        icon: BookOpen,
    },
    {
        title: 'GitHub',
        href: 'https://github.com/charitybridge',
        icon: Github,
    }
];

// Get the appropriate navigation items based on user role
const mainNavItems = computed(() => {
    switch (userRole.value) {
        case 'donor':
            return donorNavItems;
        case 'charity':
            return charityNavItems;
        case 'admin':
            return adminNavItems;
        default:
            return donorNavItems;
    }
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboardRoute">
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
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
