<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Messages from '@/components/Claims/Messages.vue';
import { type BreadcrumbItem } from '@/types';

interface Message {
    id: number;
    content: string;
    created_at: string;
    sender: {
        id: number;
        name: string;
        role: string;
    };
}

interface Claim {
    id: number;
    donation: {
        id: number;
        title: string;
        donor: {
            name: string;
        };
    };
    charity: {
        name: string;
    };
}

const props = defineProps<{
    claim: Claim;
    messages: Message[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('donor.dashboard'),
    },
    {
        title: 'My Donations',
        href: route('donor.my-donations'),
    },
    {
        title: props.claim.donation.title,
        href: route('donations.show', props.claim.donation.id),
    },
    {
        title: 'Messages',
        href: route('donor.messages.index', props.claim.id),
    }
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Messages
            :claim="claim"
            :messages="messages"
            back-route="donations.show"
            :back-params="{ donation: claim.donation.id }"
        />
    </AppLayout>
</template>
