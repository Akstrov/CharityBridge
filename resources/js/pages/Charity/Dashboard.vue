<template>
    <Head title="Charity Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Stats Grid -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Submitted Claims</CardTitle>
                        <HandCoins class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.submittedClaims }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Approved Claims</CardTitle>
                        <CheckCircle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.approvedClaims }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Pending Approval</CardTitle>
                        <Clock class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.pendingApproval }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Available Donations</CardTitle>
                        <AlertCircle class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.availableDonations }}</div>
                    </CardContent>
                </Card>
            </div>

            <!-- Recent Claims -->
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle>Recent Claims</CardTitle>
                    <Link :href="route('charity.claims.index')">
                        <Button variant="outline">View All Claims</Button>
                    </Link>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div v-for="claim in recentClaims" :key="claim.id" class="flex items-center justify-between border-b pb-4 last:border-0">
                            <div>
                                <h3 class="font-medium">{{ claim.title }}</h3>
                                <p class="text-sm text-muted-foreground">Donor: {{ claim.donor }}</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-sm text-muted-foreground">{{ claim.date }}</span>
                                <span :class="{
                                    'text-yellow-500': claim.status === 'pending',
                                    'text-green-500': claim.status === 'approved',
                                    'text-red-500': claim.status === 'rejected'
                                }" class="text-sm font-medium">
                                    {{ claim.status.charAt(0).toUpperCase() + claim.status.slice(1) }}
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
                        <CardTitle>Profile Management</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <Link :href="route('profile.edit')">
                                <Button class="w-full">Update Profile</Button>
                            </Link>
                            <p class="text-sm text-muted-foreground text-center">
                                Manage your charity profile and account settings
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Available Donations</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <Link :href="route('donations.index')">
                                <Button class="w-full">Browse Available Donations</Button>
                            </Link>
                            <p class="text-sm text-muted-foreground text-center">
                                View available donations and submit claims directly from their details page
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
import { HandCoins, CheckCircle, Clock, AlertCircle } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

interface Claim {
    id: number;
    title: string;
    status: 'pending' | 'approved' | 'rejected';
    date: string;
    donor: string;
}

interface Stats {
    submittedClaims: number;
    approvedClaims: number;
    pendingApproval: number;
    availableDonations: number;
}

defineProps<{
    stats: Stats;
    recentClaims: Claim[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Charity Dashboard',
        href: '/charity/dashboard',
    },
];
</script>
