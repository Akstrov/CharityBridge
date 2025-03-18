<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Plus, Search, Edit, Trash2, Package } from 'lucide-vue-next';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface Donation {
    id: number;
    title: string;
    description: string;
    status: 'available' | 'claimed' | 'completed';
    created_at: string;
    claims: number;
}

const props = defineProps<{
    donations: Donation[];
}>();

const search = ref('');
const status = ref('all');

// Filter donations based on search and status
const filteredDonations = computed(() => {
    return props.donations.filter(donation => {
        const matchesSearch = search.value === '' || 
            donation.title.toLowerCase().includes(search.value.toLowerCase()) ||
            donation.description.toLowerCase().includes(search.value.toLowerCase());
        
        const matchesStatus = status.value === 'all' || donation.status === status.value;
        
        return matchesSearch && matchesStatus;
    });
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('donor.dashboard'),
    },
    {
        title: 'My Donations',
        href: route('donations.my'),
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

const donationToDelete = ref<Donation | null>(null);

const handleDelete = (donation: Donation) => {
    if (donation.status !== 'available') {
        return;
    }
    donationToDelete.value = donation;
};

const confirmDelete = () => {
    if (!donationToDelete.value) return;
    
    router.delete(route('donations.destroy', { donation: donationToDelete.value.id }), {
        onSuccess: () => {
            donationToDelete.value = null;
        },
    });
};
</script>

<template>
    <Head title="My Donations" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle>My Donations</CardTitle>
                    <Link :href="route('donations.create', { from: 'my-donations' })">
                        <Button>
                            <Plus class="mr-2 h-4 w-4" />
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
                            <Select v-model="status">
                                <SelectTrigger class="w-[180px]">
                                    <SelectValue placeholder="Status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Status</SelectItem>
                                    <SelectItem value="available">Available</SelectItem>
                                    <SelectItem value="claimed">Claimed</SelectItem>
                                    <SelectItem value="completed">Completed</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>

                    <!-- Donations List -->
                    <div v-if="filteredDonations.length === 0" class="text-center py-8 text-muted-foreground">
                        No donations found.
                    </div>
                    <div v-else class="space-y-4">
                        <div v-for="donation in filteredDonations" :key="donation.id" class="flex items-center justify-between rounded-lg border p-4">
                            <div class="flex items-center gap-4">
                                <div class="rounded-full bg-primary/10 p-2">
                                    <Package class="h-5 w-5 text-primary" />
                                </div>
                                <div>
                                    <h3 class="font-medium">{{ donation.title }}</h3>
                                    <p class="text-sm text-muted-foreground line-clamp-1">{{ donation.description }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <span :class="getStatusColor(donation.status)" class="text-sm font-medium">
                                        {{ donation.status }}
                                    </span>
                                    <p class="text-sm text-muted-foreground">{{ donation.claims }} claims</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Link :href="route('donations.edit', { donation: donation.id })">
                                        <Button variant="ghost" size="icon">
                                            <Edit class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                    <Button 
                                        variant="ghost" 
                                        size="icon" 
                                        class="text-destructive"
                                        :disabled="donation.status !== 'available'"
                                        @click="handleDelete(donation)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>

    <Dialog :open="!!donationToDelete" @update:open="(value: boolean) => !value && (donationToDelete = null)">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Delete Donation</DialogTitle>
                <DialogDescription>
                    Are you sure you want to delete this donation? This action cannot be undone.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" @click="donationToDelete = null">Cancel</Button>
                <Button variant="destructive" @click="confirmDelete">Delete</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template> 