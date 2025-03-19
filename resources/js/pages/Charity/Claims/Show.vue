<template>
    <Head :title="claim.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Claim Details</h1>
                <div class="flex gap-2">
                    <Button
                        v-if="claim.status === 'pending'"
                        variant="destructive"
                        @click="openCancelDialog"
                        :disabled="isCancelling"
                    >
                        Cancel Claim
                    </Button>
                    <Button @click="router.get(route('messages.index', claim.id))">
                        View Messages
                    </Button>
                </div>
            </div>
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle>{{ claim.title }}</CardTitle>
                    <Badge :variant="statusVariant">{{ claim.status.charAt(0).toUpperCase() + claim.status.slice(1) }}</Badge>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-6">
                        <!-- Donation Details -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Donation Details</h3>
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <p class="text-sm text-muted-foreground">Category</p>
                                    <p class="font-medium">{{ claim.category }}</p>
                                </div>
                                <div class="space-y-2">
                                    <p class="text-sm text-muted-foreground">Amount</p>
                                    <p class="font-medium">{{ claim.amount }} {{ claim.currency }}</p>
                                </div>
                                <div class="space-y-2">
                                    <p class="text-sm text-muted-foreground">Donation Date</p>
                                    <p class="font-medium">{{ claim.donation_date }}</p>
                                </div>
                                <div class="space-y-2">
                                    <p class="text-sm text-muted-foreground">Claim Date</p>
                                    <p class="font-medium">{{ claim.claim_date }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <h3 class="text-lg font-semibold">Description</h3>
                            <p class="text-muted-foreground">{{ claim.description }}</p>
                        </div>

                        <!-- Donor Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold">Donor Information</h3>
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <p class="text-sm text-muted-foreground">Name</p>
                                    <p class="font-medium">{{ claim.donor }}</p>
                                </div>
                                <div class="space-y-2">
                                    <p class="text-sm text-muted-foreground">Email</p>
                                    <p class="font-medium">{{ claim.donor_email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Claim Notes -->
                        <div v-if="claim.claim_notes" class="space-y-2">
                            <h3 class="text-lg font-semibold">Claim Notes</h3>
                            <p class="text-muted-foreground">{{ claim.claim_notes }}</p>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end gap-4">
                            <Button variant="outline" @click="router.visit(route('charity.claims.index'))">
                                Back to Claims
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>

    <!-- Cancel Confirmation Dialog -->
    <Dialog :open="showCancelDialog" @update:open="showCancelDialog = $event">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Cancel Claim</DialogTitle>
                <DialogDescription>
                    Are you sure you want to cancel this claim? This action cannot be undone.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" @click="showCancelDialog = false">Cancel</Button>
                <Button
                    variant="destructive"
                    @click="cancelClaim"
                    :disabled="isCancelling"
                >
                    <LoaderCircle v-if="isCancelling" class="mr-2 h-4 w-4 animate-spin" />
                    Confirm Cancel
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { LoaderCircle } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface Claim {
    id: number;
    title: string;
    status: 'pending' | 'approved' | 'rejected';
    date: string;
    donor: string;
    donor_email: string;
    category: string;
    description: string;
    amount: number;
    currency: string;
    donation_date: string;
    claim_date: string;
    claim_notes?: string;
}

const props = defineProps<{
    claim: Claim;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('charity.dashboard'),
    },
    {
        title: 'My Claims',
        href: route('charity.claims.index'),
    },
    {
        title: props.claim.title,
        href: route('charity.claims.show', props.claim.id),
    },
];

const showCancelDialog = ref(false);
const isCancelling = ref(false);

const statusVariant = computed(() => {
    switch (props.claim.status) {
        case 'pending':
            return 'warning';
        case 'approved':
            return 'success';
        case 'rejected':
            return 'destructive';
        default:
            return 'default';
    }
});

const openCancelDialog = () => {
    showCancelDialog.value = true;
};

const cancelClaim = () => {
    isCancelling.value = true;
    router.delete(route('charity.claims.destroy', props.claim.id), {
        preserveScroll: true,
        onSuccess: () => {
            showCancelDialog.value = false;
            router.get(route('charity.claims.index'));
        },
        onFinish: () => {
            isCancelling.value = false;
        },
    });
};
</script>
