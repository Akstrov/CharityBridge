<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed, watch } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Search, Package, MapPin, Clock, AlertCircle, ChevronsLeft, ChevronLeft, ChevronRight, ChevronsRight } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';

interface Donation {
    id: number;
    title: string;
    description: string;
    category: string;
    location: string;
    is_urgent: boolean;
    created_at: string;
    donor: {
        name: string;
    };
}

interface PaginatedResponse {
    data: Donation[];
    current_page: number;
    last_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
}

const props = defineProps<{
    donations: PaginatedResponse;
    userRole: string;
    filters: {
        search: string;
        category: string;
    };
}>();

const search = ref(props.filters.search);
const category = ref(props.filters.category);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('donor.dashboard'),
    },
    {
        title: 'Available Donations',
        href: route('donations.index'),
    }
];

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString();
};

// Watch for changes in search and category
watch([search, category], ([newSearch, newCategory]) => {
    router.get(
        route('donations.index'),
        {
            search: newSearch,
            category: newCategory,
            page: 1, // Reset to first page when filters change
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
});

// Pagination helpers
const getPageNumbers = computed(() => {
    const current = props.donations.current_page;
    const last = props.donations.last_page;
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
    return page === props.donations.current_page;
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Available Donations" />
        
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle>Available Donations</CardTitle>
                    <Link v-if="userRole === 'donor'" :href="route('donations.create', { from: 'available-donations' })">
                        <Button>
                            <Package class="mr-2 h-4 w-4" />
                            Create New Donation
                        </Button>
                    </Link>
                </CardHeader>
                <CardContent>
                    <!-- Filters -->
                    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div class="flex flex-1 items-center gap-4">
                            <div class="relative flex-1">
                                <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                                <Input
                                    v-model="search"
                                    placeholder="Search donations..."
                                    class="pl-8"
                                />
                            </div>
                            <Select v-model="category">
                                <SelectTrigger class="w-[180px]">
                                    <SelectValue placeholder="Category" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Categories</SelectItem>
                                    <SelectItem value="food">Food</SelectItem>
                                    <SelectItem value="clothes">Clothes</SelectItem>
                                    <SelectItem value="money">Money</SelectItem>
                                    <SelectItem value="other">Other</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>

                    <!-- Donations List -->
                    <div v-if="donations.data.length === 0" class="text-center py-8 text-muted-foreground">
                        No donations found.
                    </div>
                    <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <Card v-for="donation in donations.data" :key="donation.id" class="group hover:shadow-lg transition-shadow">
                            <CardHeader>
                                <div class="flex items-start justify-between">
                                    <CardTitle class="text-lg group-hover:text-primary transition-colors">
                                        <Link :href="route('donations.show', { donation: donation.id })">
                                            {{ donation.title }}
                                        </Link>
                                    </CardTitle>
                                    <div v-if="donation.is_urgent" class="flex items-center text-destructive">
                                        <AlertCircle class="h-4 w-4 mr-1" />
                                        <span class="text-sm">Urgent</span>
                                    </div>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <p class="text-muted-foreground line-clamp-2 mb-4">{{ donation.description }}</p>
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center text-muted-foreground">
                                        <Package class="h-4 w-4 mr-2" />
                                        {{ donation.category }}
                                    </div>
                                    <div class="flex items-center text-muted-foreground">
                                        <MapPin class="h-4 w-4 mr-2" />
                                        {{ donation.location }}
                                    </div>
                                    <div class="flex items-center text-muted-foreground">
                                        <Clock class="h-4 w-4 mr-2" />
                                        Posted {{ formatDate(donation.created_at) }}
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <!-- Pagination -->
                    <div v-if="donations.last_page > 1" class="mt-6 flex items-center justify-center gap-1">
                        <!-- First Page -->
                        <Link
                            v-if="donations.current_page > 1"
                            :href="route('donations.index', { page: 1, search: search, category: category })"
                        >
                            <Button variant="outline" size="icon">
                                <ChevronsLeft class="h-4 w-4" />
                            </Button>
                        </Link>

                        <!-- Previous Page -->
                        <Link
                            v-if="donations.current_page > 1"
                            :href="route('donations.index', { page: donations.current_page - 1, search: search, category: category })"
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
                                    :href="route('donations.index', { page, search: search, category: category })"
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
                            v-if="donations.current_page < donations.last_page"
                            :href="route('donations.index', { page: donations.current_page + 1, search: search, category: category })"
                        >
                            <Button variant="outline" size="icon">
                                <ChevronRight class="h-4 w-4" />
                            </Button>
                        </Link>

                        <!-- Last Page -->
                        <Link
                            v-if="donations.current_page < donations.last_page"
                            :href="route('donations.index', { page: donations.last_page, search: search, category: category })"
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
</template> 