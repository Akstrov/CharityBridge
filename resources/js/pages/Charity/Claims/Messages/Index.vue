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
        href: route('charity.dashboard'),
    },
    {
        title: 'My Claims',
        href: route('charity.claims.index'),
    },
    {
        title: props.claim.donation.title,
        href: route('donations.show', props.claim.donation.id),
    },
    {
        title: 'Messages',
        href: route('messages.index', props.claim.id),
    }
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Messages
            :claim="claim"
            :messages="messages"
            back-route="charity.claims.show"
            :back-params="{ claim: claim.id }"
        />
    </AppLayout>
</template>
