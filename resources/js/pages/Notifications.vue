<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import {
    Bell,
    CheckCheck,
    Mail,
    AlertCircle,
    Clock,
    Trash2,
    ChevronLeft,
    ChevronRight,
    ChevronsLeft,
    ChevronsRight
} from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';

// Add this to debug
import { onMounted } from 'vue';

interface Notification {
    id: number;
    title: string;
    message: string;
    type: 'info' | 'success' | 'warning' | 'error';
    is_read: boolean;
    created_at: string;
    related_to?: {
        type: string;
        id: number;
        title: string;
    };
}

interface PaginatedResponse {
    data: Notification[];
    current_page: number;
    last_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
    per_page?: number;
    total?: number;
}

// Define props with default values that don't reference local variables
const props = defineProps({
    notifications: {
        type: Object as () => PaginatedResponse,
        default: () => ({
            data: [
                {
                    id: 1,
                    title: 'New Donation Received',
                    message: 'You have received a new donation of $100 for the "Children Education Fund".',
                    type: 'success',
                    is_read: false,
                    created_at: new Date(Date.now() - 1000 * 60 * 30).toISOString(), // 30 minutes ago
                    related_to: {
                        type: 'donation',
                        id: 123,
                        title: 'Donation #123'
                    }
                },
                {
                    id: 2,
                    title: 'Donation Claimed',
                    message: 'Your donation of $50 has been claimed by "Save the Animals Foundation".',
                    type: 'info',
                    is_read: false,
                    created_at: new Date(Date.now() - 1000 * 60 * 60 * 2).toISOString(), // 2 hours ago
                    related_to: {
                        type: 'donation',
                        id: 124,
                        title: 'Donation #124'
                    }
                },
                {
                    id: 3,
                    title: 'New Message',
                    message: 'You have a new message regarding your donation from "Homeless Shelter".',
                    type: 'info',
                    is_read: true,
                    created_at: new Date(Date.now() - 1000 * 60 * 60 * 5).toISOString(), // 5 hours ago
                    related_to: {
                        type: 'claim',
                        id: 45,
                        title: 'Claim #45'
                    }
                },
                {
                    id: 4,
                    title: 'Donation Expiring Soon',
                    message: 'Your donation #125 will expire in 2 days. Consider extending it if it hasn\'t been claimed.',
                    type: 'warning',
                    is_read: false,
                    created_at: new Date(Date.now() - 1000 * 60 * 60 * 24).toISOString(), // 1 day ago
                    related_to: {
                        type: 'donation',
                        id: 125,
                        title: 'Donation #125'
                    }
                },
                {
                    id: 5,
                    title: 'Payment Failed',
                    message: 'We couldn\'t process your payment for donation #126. Please update your payment method.',
                    type: 'error',
                    is_read: false,
                    created_at: new Date(Date.now() - 1000 * 60 * 60 * 36).toISOString(), // 1.5 days ago
                    related_to: {
                        type: 'donation',
                        id: 126,
                        title: 'Donation #126'
                    }
                },
                {
                    id: 6,
                    title: 'System Maintenance',
                    message: 'CharityBridge will be undergoing maintenance on Saturday, June 15th from 2AM to 4AM UTC.',
                    type: 'info',
                    is_read: true,
                    created_at: new Date(Date.now() - 1000 * 60 * 60 * 48).toISOString(), // 2 days ago
                },
                {
                    id: 7,
                    title: 'Donation Impact Report',
                    message: 'Your donation to "Children Education Fund" has helped 5 children access educational resources this month.',
                    type: 'success',
                    is_read: true,
                    created_at: new Date(Date.now() - 1000 * 60 * 60 * 72).toISOString(), // 3 days ago
                    related_to: {
                        type: 'donation',
                        id: 120,
                        title: 'Donation #120'
                    }
                },
                {
                    id: 8,
                    title: 'Account Security Alert',
                    message: 'We noticed a login to your account from a new device. If this wasn\'t you, please secure your account immediately.',
                    type: 'warning',
                    is_read: true,
                    created_at: new Date(Date.now() - 1000 * 60 * 60 * 96).toISOString(), // 4 days ago
                }
            ],
            current_page: 1,
            last_page: 2,
            prev_page_url: null,
            next_page_url: '/notifications?page=2',
            per_page: 8,
            total: 15
        })
    },
    unreadCount: {
        type: Number,
        default: 4 // Match the number of unread notifications in our mock data
    },
    userRole: {
        type: String,
        default: 'donor'
    },
    filters: {
        type: Object,
        default: () => ({
            type: 'all',
            status: 'all'
        })
    }
});

// Safely access filter values with defaults
const notificationType = ref(props.filters?.type || 'all');
const status = ref(props.filters?.status || 'all');
const activeTab = ref('all');

const userRole = computed(() => props.userRole);

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

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboardRoute.value,
    },
    {
        title: 'Notifications',
        href: route('notifications.index'),
    }
];

// Add this computed property to ensure we always have data to display
const displayNotifications = computed(() => {
    // If props.notifications has valid data, use it
    if (props.notifications?.data?.length > 0) {
        return props.notifications;
    }

    // Otherwise, return our hardcoded mock data
    return {
        data: [
            {
                id: 1,
                title: 'New Donation Received',
                message: 'You have received a new donation of $100 for the "Children Education Fund".',
                type: 'success',
                is_read: false,
                created_at: new Date(Date.now() - 1000 * 60 * 30).toISOString(),
                related_to: {
                    type: 'donation',
                    id: 123,
                    title: 'Donation #123'
                }
            },
            // Add a few more items for testing
            {
                id: 2,
                title: 'System Maintenance',
                message: 'CharityBridge will be undergoing maintenance on Saturday.',
                type: 'info',
                is_read: true,
                created_at: new Date(Date.now() - 1000 * 60 * 60 * 24).toISOString()
            }
        ],
        current_page: 1,
        last_page: 1,
        prev_page_url: null,
        next_page_url: null
    };
});

// Add this for debugging
onMounted(() => {
    console.log('Component mounted');
    console.log('Props notifications:', props.notifications);
    console.log('Display notifications:', displayNotifications.value);
});

const formatDate = (dateString: string): string => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInHours = Math.floor((now.getTime() - date.getTime()) / (1000 * 60 * 60));

    if (diffInHours < 24) {
        if (diffInHours === 0) {
            const diffInMinutes = Math.floor((now.getTime() - date.getTime()) / (1000 * 60));
            return `${diffInMinutes} minute${diffInMinutes !== 1 ? 's' : ''} ago`;
        }
        return `${diffInHours} hour${diffInHours !== 1 ? 's' : ''} ago`;
    } else if (diffInHours < 48) {
        return 'Yesterday';
    } else {
        return date.toLocaleDateString();
    }
};

// Mark notification as read
const markAsRead = (id: number) => {
    router.post(
        route('notifications.read', { id }),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                // Update the notification in the list
                const notification = props.notifications.data.find(n => n.id === id);
                if (notification) {
                    notification.is_read = true;
                }
            }
        }
    );
};

// Mark all notifications as read
const markAllAsRead = () => {
    router.post(
        route('notifications.read-all'),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                // Update all notifications in the list
                props.notifications.data.forEach(notification => {
                    notification.is_read = true;
                });
            }
        }
    );
};

// Delete notification
const deleteNotification = (id: number) => {
    if (confirm('Are you sure you want to delete this notification?')) {
        router.delete(
            route('notifications.destroy', { id }),
            {
                preserveScroll: true
            }
        );
    }
};

// Get notification icon based on type
const getNotificationIcon = (type: string) => {
    switch (type) {
        case 'info':
            return Mail;
        case 'success':
            return CheckCheck;
        case 'warning':
        case 'error':
            return AlertCircle;
        default:
            return Bell;
    }
};

// Get notification class based on type
const getNotificationClass = (type: string) => {
    switch (type) {
        case 'info':
            return 'text-blue-500';
        case 'success':
            return 'text-green-500';
        case 'warning':
            return 'text-amber-500';
        case 'error':
            return 'text-red-500';
        default:
            return 'text-foreground';
    }
};

// Pagination helpers
const getPageNumbers = computed(() => {
    const current = props.notifications.current_page;
    const last = props.notifications.last_page;
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
    return page === props.notifications.current_page;
};

// Handle filter changes
const handleFilterChange = () => {
    const appliedType = activeTab.value === 'all' ? notificationType.value : activeTab.value;

    router.get(
        route('notifications.index'),
        {
            type: appliedType,
            status: status.value,
            page: 1, // Reset to first page when filters change
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

// Setup watchers for tab and status changes
const onTabChange = (newTab: string) => {
    activeTab.value = newTab;
    handleFilterChange();
};

const onStatusChange = (newStatus: string) => {
    status.value = newStatus;
    handleFilterChange();
};
</script>

<template>

    <Head title="Notifications" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle>
                        <div class="flex items-center">
                            <Bell class="mr-2 h-5 w-5" />
                            Notifications
                            <span v-if="unreadCount > 0" class="ml-2 text-sm text-primary">
                                ({{ unreadCount }} new)
                            </span>
                        </div>
                    </CardTitle>
                    <Button v-if="unreadCount > 0" variant="outline" @click="markAllAsRead">
                        <CheckCheck class="mr-2 h-4 w-4" />
                        Mark all as read
                    </Button>
                </CardHeader>
                <CardContent>
                    <!-- Tabs and Filters -->
                    <div class="mb-6">
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div class="flex items-start gap-4">
                                <div class="mt-1">
                                    <component class="h-5 w-5" />
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <h3 class="font-medium">
                                        </h3>
                                        <div class="flex items-center gap-2">
                                            <div class="flex items-center text-muted-foreground text-sm">
                                                <Clock class="h-3 w-3 mr-1" />
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
                <!-- Action buttons -->
                <div class="absolute right-2 top-2 flex gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                    <Button variant="outline" size="icon" class="h-7 w-7" @click="markAsRead(notification.id)"
                        title="Mark as read">
                        <CheckCheck class="h-4 w-4" />
                    </Button>
                    <Button variant="outline" size="icon" class="h-7 w-7 text-destructive hover:bg-destructive/10"
                        @click="deleteNotification(notification.id)" title="Delete notification">
                        <Trash2 class="h-4 w-4" />
                    </Button>
                </div>
            </Card>
        </div>

        <!-- Pagination -->
        <div v-if="notifications.last_page > 1" class="mt-6 flex items-center justify-center gap-1">
            <!-- First Page -->
            <Link v-if="notifications.current_page > 1" href="#">
            <Button variant="outline" size="icon">
                <ChevronsLeft class="h-4 w-4" />
            </Button>
            </Link>

            <!-- Previous Page -->
            <Link v-if="notifications.current_page > 1" href="#">
            <Button variant="outline" size="icon">
                <ChevronLeft class="h-4 w-4" />
            </Button>
            </Link>

            <!-- Page Numbers -->
            <div class="flex items-center gap-1">
                <template v-for="page in getPageNumbers" :key="page">
                    <Link v-if="typeof page === 'number'" href="#">
                    <Button variant="outline" size="sm" :class="{
                        'bg-primary text-primary-foreground hover:bg-primary/90': isCurrentPage(page),
                        'cursor-pointer': !isCurrentPage(page),
                        'cursor-default': isCurrentPage(page)
                    }">
                        {{ page }}
                    </Button>
                    </Link>
                    <span v-else class="px-2">{{ page }}</span>
                </template>
            </div>

            <!-- Next Page -->
            <Link v-if="notifications.current_page < notifications.last_page" href="#">
            <Button variant="outline" size="icon">
                <ChevronRight class="h-4 w-4" />
            </Button>
            </Link>

            <!-- Last Page -->
            <Link v-if="notifications.current_page < notifications.last_page" href="#">
            <Button variant="outline" size="icon">
                <ChevronsRight class="h-4 w-4" />
            </Button>
            </Link>
        </div>
    </AppLayout>
</template>
