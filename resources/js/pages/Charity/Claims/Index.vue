<template>
    <Head title="My Claims" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle>My Claims</CardTitle>
                </CardHeader>
                <CardContent>
                    <!-- Filters -->
                    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div class="flex items-center gap-4">
                            <Select v-model="status">
                                <SelectTrigger class="w-[180px]">
                                    <SelectValue placeholder="Status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Status</SelectItem>
                                    <SelectItem value="pending">Pending</SelectItem>
                                    <SelectItem value="approved">Approved</SelectItem>
                                    <SelectItem value="rejected">Rejected</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>

                    <div v-if="claims.data.length === 0" class="text-center py-8 text-muted-foreground">
                        No claims found.
                    </div>
                    <div v-else class="space-y-4">
                        <div v-for="claim in claims.data" :key="claim.id" class="flex items-center justify-between border-b pb-4 last:border-0">
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
                                <Button 
                                    variant="outline" 
                                    size="sm"
                                    @click="router.visit(route('charity.claims.show', claim.id))"
                                >
                                    View Details
                                </Button>
                                <Button 
                                    v-if="claim.status === 'pending'"
                                    variant="destructive" 
                                    size="sm"
                                    @click="openCancelDialog(claim)"
                                >
                                    Cancel
                                </Button>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="claims.last_page > 1" class="mt-6 flex items-center justify-center gap-1">
                        <!-- First Page -->
                        <Link
                            v-if="claims.current_page > 1"
                            :href="route('charity.claims.index', { page: 1 })"
                        >
                            <Button variant="outline" size="icon">
                                <ChevronsLeft class="h-4 w-4" />
                            </Button>
                        </Link>

                        <!-- Previous Page -->
                        <Link
                            v-if="claims.current_page > 1"
                            :href="route('charity.claims.index', { page: claims.current_page - 1 })"
                        >
                            <Button variant="outline" size="icon">
                                <ChevronLeft class="h-4 w-4" />
                            </Button>
                        </Link>

                        <!-- Page Numbers -->
                        <div class="flex items-center gap-1">
                            <template v-for="page in getPageNumbers" :key="page">
                                <Link
                                    v-if="typeof page === 'number'"
                                    :href="route('charity.claims.index', { page })"
                                >
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        :class="{
                                            'bg-primary text-primary-foreground hover:bg-primary/90': isCurrentPage(page),
                                            'cursor-pointer': !isCurrentPage(page),
                                            'cursor-default': isCurrentPage(page)
                                        }"
                                    >
                                        {{ page }}
                                    </Button>
                                </Link>
                                <span v-else class="px-2">{{ page }}</span>
                            </template>
                        </div>

                        <!-- Next Page -->
                        <Link
                            v-if="claims.current_page < claims.last_page"
                            :href="route('charity.claims.index', { page: claims.current_page + 1 })"
                        >
                            <Button variant="outline" size="icon">
                                <ChevronRight class="h-4 w-4" />
                            </Button>
                        </Link>

                        <!-- Last Page -->
                        <Link
                            v-if="claims.current_page < claims.last_page"
                            :href="route('charity.claims.index', { page: claims.last_page })"
                        >
                            <Button variant="outline" size="icon">
                                <ChevronsRight class="h-4 w-4" />
                            </Button>
                        </Link>
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
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { ChevronsLeft, ChevronLeft, ChevronRight, ChevronsRight, LoaderCircle } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

interface Claim {
    id: number;
    title: string;
    status: 'pending' | 'approved' | 'rejected';
    date: string;
    donor: string;
}

interface PaginatedClaims {
    data: Claim[];
    current_page: number;
    last_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
}

const props = defineProps<{
    claims: PaginatedClaims;
    filters: {
        status: string;
    };
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
];

const showCancelDialog = ref(false);
const selectedClaim = ref<Claim | null>(null);
const isCancelling = ref(false);
const status = ref(props.filters.status);

// Watch for changes in status
watch(status, (newStatus) => {
    router.get(route('charity.claims.index'), {
        status: newStatus,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
});

const openCancelDialog = (claim: Claim) => {
    selectedClaim.value = claim;
    showCancelDialog.value = true;
};

const cancelClaim = () => {
    if (!selectedClaim.value) return;

    isCancelling.value = true;
    router.delete(route('charity.claims.destroy', selectedClaim.value.id), {
        onSuccess: () => {
            showCancelDialog.value = false;
            selectedClaim.value = null;
        },
        onFinish: () => {
            isCancelling.value = false;
        },
    });
};

// Pagination helpers
const getPageNumbers = computed(() => {
    const current = props.claims.current_page;
    const last = props.claims.last_page;
    const delta = 2; // Number of pages to show before and after current page

    const range = [];
    const rangeWithDots = [];
    let l;

    for (let i = 1; i <= last; i++) {
        if (
            i === 1 || // First page
            i === last || // Last page
            (i >= current - delta && i <= current + delta) // Pages around current page
        ) {
            range.push(i);
        }
    }

    for (let i = 0; i < range.length; i++) {
        if (l) {
            if (range[i] - l === 2) {
                rangeWithDots.push(l + 1);
            } else if (range[i] - l !== 1) {
                rangeWithDots.push('...');
            }
        }
        rangeWithDots.push(range[i]);
        l = range[i];
    }

    return rangeWithDots;
});

const isCurrentPage = (page: number | string): boolean => {
    return page === props.claims.current_page;
};
</script> 