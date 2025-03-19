<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Package, MapPin, Clock, User, Calendar } from 'lucide-vue-next';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { computed, ref } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { toast } from '@/components/ui/use-toast';

interface Donation {
    id: number;
    title: string;
    description: string;
    category: string;
    location: string;
    is_urgent: boolean;
    created_at: string;
    status: string;
    quantity: number | null;
    monetary_value: number | null;
    expiry_date: string | null;
    donor: {
        id: number;
        name: string;
    };
    claims: Array<{
        id: number;
        charity: {
            id: number;
            name: string;
        };
        status: string;
        created_at: string;
    }>;
}

const props = defineProps<{
    donation: Donation;
    userRole: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('donor.dashboard'),
    },
    {
        title: 'Available Donations',
        href: route('donations.index'),
    },
    {
        title: props.donation.title,
        href: route('donations.show', props.donation.id),
    }
];

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString();
};

const formatCurrency = (value: number | null): string => {
    if (!value) return 'N/A';
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(value);
};

const canClaim = computed(() => {
    return props.userRole === 'charity' && props.donation.status === 'available';
});

const isDialogOpen = ref(false);

const handleClaim = () => {
    router.post(route('claims.store', props.donation.id), {}, {
        onSuccess: () => {
            // Close the dialog after successful submission
            isDialogOpen.value = false;
        },
        onError: (errors) => {
            // Handle any errors if needed
            console.error('Failed to claim donation:', errors);
        }
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="donation.title" />
        
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card>
                <CardHeader>
                    <div class="flex items-start justify-between">
                        <div>
                            <CardTitle class="text-2xl">{{ donation.title }}</CardTitle>
                            <div class="mt-2 flex items-center gap-2">
                                <Badge :variant="donation.is_urgent ? 'destructive' : 'default'">
                                    {{ donation.is_urgent ? 'Urgent' : 'Normal' }}
                                </Badge>
                                <Badge variant="outline">{{ donation.category }}</Badge>
                                <Badge variant="secondary">{{ donation.status }}</Badge>
                            </div>
                        </div>
                        <Dialog v-if="canClaim" v-model:open="isDialogOpen">
                            <DialogTrigger as-child>
                                <Button>Claim Donation</Button>
                            </DialogTrigger>
                            <DialogContent>
                                <DialogHeader>
                                    <DialogTitle>Claim Donation</DialogTitle>
                                    <DialogDescription>
                                        Are you sure you want to claim this donation? This action cannot be undone.
                                    </DialogDescription>
                                </DialogHeader>
                                <DialogFooter>
                                    <Button variant="outline" @click="isDialogOpen = false">Cancel</Button>
                                    <Button @click="handleClaim">Confirm Claim</Button>
                                </DialogFooter>
                            </DialogContent>
                        </Dialog>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-6 md:grid-cols-2">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold">Description</h3>
                                <p class="mt-2 text-muted-foreground">{{ donation.description }}</p>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold">Details</h3>
                                <div class="mt-2 space-y-2">
                                    <div class="flex items-center text-muted-foreground">
                                        <Package class="mr-2 h-4 w-4" />
                                        Category: {{ donation.category }}
                                    </div>
                                    <div class="flex items-center text-muted-foreground">
                                        <MapPin class="mr-2 h-4 w-4" />
                                        Location: {{ donation.location }}
                                    </div>
                                    <div class="flex items-center text-muted-foreground">
                                        <Clock class="mr-2 h-4 w-4" />
                                        Posted: {{ formatDate(donation.created_at) }}
                                    </div>
                                    <div v-if="donation.quantity" class="flex items-center text-muted-foreground">
                                        <Package class="mr-2 h-4 w-4" />
                                        Quantity: {{ donation.quantity }}
                                    </div>
                                    <div v-if="donation.monetary_value" class="flex items-center text-muted-foreground">
                                        <Package class="mr-2 h-4 w-4" />
                                        Value: {{ formatCurrency(donation.monetary_value) }}
                                    </div>
                                    <div v-if="donation.expiry_date" class="flex items-center text-muted-foreground">
                                        <Calendar class="mr-2 h-4 w-4" />
                                        Expires: {{ formatDate(donation.expiry_date) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold">Donor Information</h3>
                                <div class="mt-2 flex items-center text-muted-foreground">
                                    <User class="mr-2 h-4 w-4" />
                                    {{ donation.donor.name }}
                                </div>
                            </div>

                            <div v-if="donation.claims.length > 0">
                                <h3 class="text-lg font-semibold">Claim History</h3>
                                <div class="mt-2 space-y-2">
                                    <div v-for="claim in donation.claims" :key="claim.id" class="flex items-center justify-between rounded-lg border p-2">
                                        <div>
                                            <div class="font-medium">{{ claim.charity.name }}</div>
                                            <div class="text-sm text-muted-foreground">
                                                Claimed on {{ formatDate(claim.created_at) }}
                                            </div>
                                        </div>
                                        <Badge :variant="claim.status === 'pending' ? 'default' : 'secondary'">
                                            {{ claim.status }}
                                        </Badge>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>