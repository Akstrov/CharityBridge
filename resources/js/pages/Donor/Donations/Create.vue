<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { format } from 'date-fns';
import { CalendarIcon } from 'lucide-vue-next';
import { ref } from 'vue';
import { today, type DateValue } from '@internationalized/date';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    from?: 'my-donations' | 'available-donations';
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('donor.dashboard'),
    },
    {
        title: props.from === 'my-donations' ? 'My Donations' : 'Available Donations',
        href: props.from === 'my-donations' ? route('donations.my') : route('donations.index'),
    },
    {
        title: 'Create Donation',
        href: route('donations.create'),
    }
];

const selectedDate = ref<DateValue | undefined>();

const form = useForm({
    title: '',
    description: '',
    category: '',
    quantity: null as number | null,
    location: '',
    is_urgent: false,
    monetary_value: null as number | null,
    expiry_date: null as string | null,
});

const handleSubmit = () => {
    form.expiry_date = selectedDate.value ? new Date(selectedDate.value.toString()).toISOString() : null;
    form.post(route('donations.store', { from: props.from }), {
        onSuccess: () => {
            // Handle success
        },
    });
};
</script>

<template>
    <Head title="Create Donation" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card>
                <CardHeader>
                    <CardTitle>Create New Donation</CardTitle>
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

                        <!-- Category -->
                        <div class="space-y-2">
                            <Label for="category">Category</Label>
                            <Select v-model="form.category" required>
                                <SelectTrigger>
                                    <SelectValue placeholder="Select a category" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="food">Food</SelectItem>
                                    <SelectItem value="clothes">Clothes</SelectItem>
                                    <SelectItem value="money">Money</SelectItem>
                                    <SelectItem value="other">Other</SelectItem>
                                </SelectContent>
                            </Select>
                            <div v-if="form.errors.category" class="text-sm text-destructive">
                                {{ form.errors.category }}
                            </div>
                        </div>

                        <!-- Quantity -->
                        <div class="space-y-2">
                            <Label for="quantity">Quantity</Label>
                            <Input
                                id="quantity"
                                v-model="form.quantity"
                                type="number"
                                min="1"
                                placeholder="Optional"
                            />
                            <div v-if="form.errors.quantity" class="text-sm text-destructive">
                                {{ form.errors.quantity }}
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

                        <!-- Monetary Value -->
                        <div class="space-y-2">
                            <Label for="monetary_value">Monetary Value</Label>
                            <Input
                                id="monetary_value"
                                v-model="form.monetary_value"
                                type="number"
                                min="0"
                                step="0.01"
                                placeholder="Optional"
                            />
                            <div v-if="form.errors.monetary_value" class="text-sm text-destructive">
                                {{ form.errors.monetary_value }}
                            </div>
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
                                    />
                                </PopoverContent>
                            </Popover>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end gap-4">
                            <Link :href="props.from === 'my-donations' ? route('donations.my') : route('donations.index')">
                                <Button type="button" variant="outline">
                                    Cancel
                                </Button>
                            </Link>
                            <Button
                                type="submit"
                                :disabled="form.processing"
                            >
                                Create Donation
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>