<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';
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

interface MessageNotificationData {
    message: {
        sender_id: number;
        content: string;
        created_at: string;
    };
    claim_id: number;
    claim_title: string;
}

interface LaravelNotification {
    id: string;
    type: string;
    notifiable_type: string;
    notifiable_id: number;
    data: MessageNotificationData | any; // Allow for other notification types
    read_at: string | null;
    created_at: string;
    updated_at: string;
}

interface PaginatedResponse<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
    prev_page_url: string | null;
    next_page_url: string | null;
}

const props = defineProps<{
    notifications: PaginatedResponse<LaravelNotification>;
    unreadCount: number;
    userRole: string;
    filters: {
        type: string;
        status: string;
    };
}>();

// Reactive state
const activeTab = ref('all');
const notificationType = ref(props.filters.type);
const statusFilter = ref(props.filters.status);

// Computed properties
const dashboardRoute = computed(() => {
    switch (props.userRole) {
        case 'donor': return route('donor.dashboard');
        case 'charity': return route('charity.dashboard');
        case 'admin': return route('admin.dashboard');
        default: return route('donor.dashboard');
    }
});

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    { title: 'Dashboard', href: dashboardRoute.value },
    { title: 'Notifications', href: route('notifications.index') }
]);

// Transform Laravel notifications to display format
const displayNotifications = computed(() => {
    return props.notifications.data.map(notification => {
        const isMessageNotification = notification.type === 'App\\Notifications\\NewMessageNotification';

        return {
            id: notification.id,
            type: isMessageNotification ? 'message' : notification.data.type || 'info',
            isRead: notification.read_at !== null,
            createdAt: notification.created_at,
            data: isMessageNotification ? {
                senderId: notification.data.message.sender_id,
                content: notification.data.message.content,
                claimId: notification.data.claim_id,
                claimTitle: notification.data.claim_title,
                link: notification.data.link
            } : notification.data
        };
    });
});

// Methods
const formatDate = (dateString: string): string => {
    const date = new Date(dateString);
    const options: Intl.DateTimeFormatOptions = {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return date.toLocaleDateString('en-US', options);
};
const viewConversation = (notificationId: string, claimId: number) => {
    router.post(route('notifications.read', notificationId), {}, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            router.visit(route('messages.index', { claim: claimId }));
        }
    });
};

const markAsRead = (id: string) => {
    router.post(route('notifications.read', { id }), {}, {
        preserveScroll: true,
        onSuccess: () => {
            const notification = props.notifications.data.find(n => n.id === id);
            if (notification) notification.read_at = new Date().toISOString();
        }
    });
};

const markAllAsRead = () => {
    router.post(route('notifications.read-all'), {}, {
        preserveScroll: true,
        onSuccess: () => {
            props.notifications.data.forEach(n => n.read_at = new Date().toISOString());
        }
    });
};

const deleteNotification = (id: string) => {
    if (confirm('Are you sure you want to delete this notification?')) {
        router.delete(route('notifications.destroy', { id }), { preserveScroll: true });
    }
};

// Filtering
const filteredNotifications = computed(() => {
    return displayNotifications.value.filter(notification => {
        const typeMatch = activeTab.value === 'all' ||
            (activeTab.value === 'unread' ? !notification.isRead :
                notification.type === activeTab.value);
        return typeMatch;
    });
});

// Pagination
const pageNumbers = computed(() => {
    const { current_page, last_page } = props.notifications;
    const range = [];
    const delta = 2;

    for (let i = 1; i <= last_page; i++) {
        if (i === 1 || i === last_page || (i >= current_page - delta && i <= current_page + delta)) {
            range.push(i);
        }
    }
    return range;
});
</script>

<template>

    <Head title="Notifications" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle class="flex items-center gap-2">
                        <Bell class="h-5 w-5" />
                        Notifications
                        <span v-if="unreadCount > 0" class="text-primary text-sm">
                            ({{ unreadCount }} unread)
                        </span>
                    </CardTitle>
                    <Button v-if="unreadCount > 0" variant="outline" @click="markAllAsRead">
                        <CheckCheck class="mr-2 h-4 w-4" />
                        Mark all read
                    </Button>
                </CardHeader>

                <CardContent>
                    <Tabs v-model="activeTab" class="mb-6">
                        <TabsList class="grid w-full grid-cols-5">
                            <TabsTrigger value="all">All</TabsTrigger>
                            <TabsTrigger value="unread">Unread</TabsTrigger>
                            <TabsTrigger value="info">Info</TabsTrigger>
                            <TabsTrigger value="success">Success</TabsTrigger>
                            <TabsTrigger value="warning">Alerts</TabsTrigger>
                        </TabsList>
                    </Tabs>

                    <div v-if="filteredNotifications.length" class="space-y-4">
                        <div v-for="notification in filteredNotifications" :key="notification.id"
                            class="group relative rounded-lg border p-4 transition-all hover:shadow-md"
                            :class="{ 'bg-muted/50': !notification.isRead }">

                            <!-- Message Notification Template -->
                            <div v-if="notification.type === 'message'" class="flex items-start gap-4">
                                <Mail class="h-5 w-5 text-blue-500 mt-1" />
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <h3 :class="{ 'font-semibold': !notification.isRead }">
                                            New Message in {{ notification.data.claimTitle }}
                                        </h3>
                                        <div class="flex items-center text-sm text-muted-foreground">
                                            <Clock class="mr-1 h-3 w-3" />
                                            {{ formatDate(notification.createdAt) }}
                                        </div>
                                    </div>
                                    <p class="text-muted-foreground text-sm">
                                        {{ notification.data.content }}
                                    </p>
                                    <Link :href="route('messages.index', notification.data.claimId)"
                                        @click="viewConversation(notification.id, notification.data.claimId)"
                                        class="text-primary hover:underline text-sm mt-2 inline-block">
                                    View Conversation
                                    </Link>
                                </div>
                            </div>

                            <!-- Default Notification Template -->
                            <div v-else class="flex items-start gap-4">
                                <component :is="getNotificationIcon(notification.type)"
                                    :class="getNotificationClass(notification.type)" class="h-5 w-5 mt-1" />
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <h3 :class="{ 'font-semibold': !notification.isRead }">
                                            {{ notification.data.title }}
                                        </h3>
                                        <div class="flex items-center text-sm text-muted-foreground">
                                            <Clock class="mr-1 h-3 w-3" />
                                            {{ formatDate(notification.createdAt) }}
                                        </div>
                                    </div>
                                    <p class="text-muted-foreground text-sm">{{ notification.data.message }}</p>
                                    <Link v-if="notification.data.related_to"
                                        :href="route(`${notification.data.related_to.type}s.show`, notification.data.related_to.id)"
                                        class="text-primary hover:underline text-sm mt-2 inline-block">
                                    View {{ notification.data.related_to.type }}
                                    </Link>
                                </div>
                            </div>
                            <div class="absolute right-4 top-4 flex gap-2 opacity-0 group-hover:opacity-100">
                                <Button v-if="!notification.isRead" variant="outline" size="sm"
                                    @click="markAsRead(notification.id)">
                                    <CheckCheck class="h-4 w-4" />
                                </Button>
                                <Button variant="outline" size="sm" class="text-destructive"
                                    @click="deleteNotification(notification.id)">
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                    </div>

                    <div v-else class="flex h-32 items-center justify-center text-muted-foreground">
                        No notifications found
                    </div>
                </CardContent>
            </Card>

            <!-- Pagination -->
            <div v-if="notifications.last_page > 1" class="flex items-center justify-center gap-2">
                <Link v-for="page in pageNumbers" :key="page"
                    :href="route('notifications.index', { page, type: notificationType, status: statusFilter })">
                <Button variant="outline"
                    :class="{ 'bg-primary text-primary-foreground': notifications.current_page === page }">
                    {{ page }}
                </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>

<!-- Add these helper functions at the end of the script -->
<script lang="ts">
function getNotificationIcon(type: string) {
    switch (type) {
        case 'message': return Mail;
        case 'info': return Mail;
        case 'success': return CheckCheck;
        case 'warning': return AlertCircle;
        case 'error': return AlertCircle;
        default: return Bell;
    }
}

function getNotificationClass(type: string) {
    switch (type) {
        case 'message': return 'text-blue-500';
        case 'info': return 'text-blue-500';
        case 'success': return 'text-green-500';
        case 'warning': return 'text-amber-500';
        case 'error': return 'text-red-500';
        default: return 'text-foreground';
    }
}
</script>
