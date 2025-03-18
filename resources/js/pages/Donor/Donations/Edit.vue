<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea/index';
import { Switch } from '@/components/ui/switch/index';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover/index';
import { format } from 'date-fns';
import { CalendarIcon } from 'lucide-vue-next';
import { ref } from 'vue';
import { parseDate, today, type DateValue } from '@internationalized/date';

interface Donation {
    id: number;
    title: string;
    description: string;
    category: string;
    quantity: number | null;
    location: string;
    is_urgent: boolean;
    monetary_value: number | null;
    expiry_date: string | null;
    status: string;
}


const props = defineProps<{
    donation: Donation;
}>();

const form = useForm({
    title: props.donation.title,
    description: props.donation.description,
    category: props.donation.category,
    quantity: props.donation.quantity,
    location: props.donation.location,
    is_urgent: props.donation.is_urgent,
    monetary_value: props.donation.monetary_value,
    expiry_date: props.donation.expiry_date,
    status: props.donation.status,
});

const selectedDate = ref<DateValue | undefined>(
    props.donation.expiry_date ? parseDate(props.donation.expiry_date.split('T')[0]) : undefined
);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('donor.dashboard'),
    },
    {
        title: 'My Donations',
        href: route('donations.my'),
    },
    {
        title: 'Edit Donation',
        href: route('donations.edit', { donation: props.donation.id }),
    }
];

const handleSubmit = () => {
    form.expiry_date = selectedDate.value ? new Date(selectedDate.value.toString()).toISOString() : null;
    form.put(route('donations.update', { donation: props.donation.id }), {
        onSuccess: () => {
            // Handle success
        },
    });
};
</script>

<template>
    <Head title="Edit Donation" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card>
                <CardHeader>
                    <CardTitle>Edit Donation</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="handleSubmit" class="space-y-6">
                        <!-- Title -->
                        <div class="space-y-2">
                            <Label for="title">Title</Label>
                            <Input
                                id="title"
                                v-model="form.title"
                                type="text"
                                required
                            />
                            <div v-if="form.errors.title" class="text-sm text-destructive">
                                {{ form.errors.title }}
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <Label for="description">Description</Label>
                            <Textarea
                                id="description"
                                v-model="form.description"
                                required
                            />
                            <div v-if="form.errors.description" class="text-sm text-destructive">
                                {{ form.errors.description }}
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="space-y-2">
                            <Label for="location">Location</Label>
                            <Input
                                id="location"
                                v-model="form.location"
                                type="text"
                                required
                            />
                            <div v-if="form.errors.location" class="text-sm text-destructive">
                                {{ form.errors.location }}
                            </div>
                        </div>

                        <!-- Is Urgent -->
                        <div class="flex items-center space-x-2">
                            <Switch
                                id="is_urgent"
                                v-model="form.is_urgent"
                            />
                            <Label for="is_urgent">Mark as Urgent</Label>
                        </div>

                        <!-- Expiry Date -->
                        <div class="space-y-2">
                            <Label>Expiry Date</Label>
                            <Popover>
                                <PopoverTrigger as-child>
                                    <Button
                                        variant="outline"
                                        class="w-full justify-start text-left font-normal"
                                    >
                                        <CalendarIcon class="mr-2 h-4 w-4" />
                                        <span v-if="selectedDate">
                                            {{ format(new Date(selectedDate.toString()), 'PPP') }}
                                        </span>
                                        <span v-else>Pick a date</span>
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent class="w-auto p-0" align="start">
                                    <Calendar
                                        v-model="selectedDate"
                                        class="rounded-md border"
                                        :min-date="today()"
                                        :disabled="false"
                                    />
                                </PopoverContent>
                            </Popover>
                        </div>

                        <!-- Non-editable fields (read-only) -->
                        <div class="space-y-2">
                            <Label>Category</Label>
                            <Input
                                v-model="form.category"
                                disabled
                                class="bg-muted"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label>Quantity</Label>
                            <Input
                                :model-value="form.quantity?.toString() || 'N/A'"
                                disabled
                                class="bg-muted"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label>Monetary Value</Label>
                            <Input
                                :model-value="form.monetary_value ? `$${form.monetary_value.toFixed(2)}` : 'N/A'"
                                disabled
                                class="bg-muted"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label>Status</Label>
                            <Input
                                v-model="form.status"
                                disabled
                                class="bg-muted"
                            />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end gap-4">
                            <Button
                                type="button"
                                variant="outline"
                                :href="route('donations.my')"
                            >
                                Cancel
                            </Button>
                            <Button
                                type="submit"
                                :disabled="form.processing"
                            >
                                Update Donation
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>