<template>
    <Head title="Donor Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Stats Grid -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Donations</CardTitle>
                        <Gift class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalDonations }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Active Donations</CardTitle>
                        <Package class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.activeDonations }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Claimed Donations</CardTitle>
                        <Clock class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.claimedDonations }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Completed Donations</CardTitle>
                        <CheckCircle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.completedDonations }}</div>
                    </CardContent>
                </Card>
            </div>

            <!-- Recent Donations -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle>Recent Donations</CardTitle>
                    <Link :href="route('donations.my')">
                        <Button variant="outline">View All Donations</Button>
                    </Link>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div v-for="donation in recentDonations" :key="donation.id" class="flex items-center justify-between border-b pb-4 last:border-0">
                            <div>
                                <h3 class="font-medium">{{ donation.title }}</h3>
                                <p class="text-sm text-muted-foreground">Claims: {{ donation.claims }}</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-sm text-muted-foreground">{{ donation.date }}</span>
                                <span :class="getStatusColor(donation.status)" class="text-sm font-medium">
                                    {{ donation.status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Quick Actions -->
            <div class="grid gap-4 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Donation Management</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <Link :href="route('donations.create')">
                                <Button class="w-full">Create New Donation</Button>
                            </Link>
                            <Link :href="route('donations.my')">
                                <Button variant="outline" class="w-full">Manage Donations</Button>
                            </Link>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Account Management</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <Link :href="route('profile.edit')">
                                <Button class="w-full">Update Profile</Button>
                            </Link>
                            <p class="text-sm text-muted-foreground text-center">
                                Manage your profile and account settings
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Gift, CheckCircle, Clock, Package } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

interface Donation {
    id: number;
    title: string;
    status: 'available' | 'claimed' | 'completed';
    date: string;
    claims: number;
}

interface Stats {
    totalDonations: number;
    activeDonations: number;
    claimedDonations: number;
    completedDonations: number;
}

defineProps<{
    stats: Stats;
    recentDonations: Donation[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('donor.dashboard'),
    }
];

const getStatusColor = (status: Donation['status']): string => {
    switch (status) {
        case 'available':
            return 'text-emerald-500';
        case 'claimed':
            return 'text-amber-500';
        case 'completed':
            return 'text-sky-500';
        default:
            return 'text-gray-500';
    }
};
</script>
