<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { ScrollArea } from '@/components/ui/scroll-area';
import { useToast } from '@/components/ui/toast/use-toast';
import { ChevronLeft } from 'lucide-vue-next';

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

interface PageProps {
    auth: {
        user: {
            id: number;
        };
    };
    [key: string]: any;
}

const props = defineProps<{
    claim: Claim;
    messages: Message[];
    backRoute: string;
    backParams?: Record<string, any>;
}>();

const page = usePage<PageProps>();
const { toast } = useToast();
const messagesContainer = ref<HTMLElement | null>(null);
const newMessage = ref('');
const isSending = ref(false);

const scrollToBottom = async () => {
    await nextTick();
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
};

const sendMessage = () => {
    if (!newMessage.value.trim()) return;

    isSending.value = true;
    router.post(route('messages.store', props.claim.id), {
        content: newMessage.value
    }, {
        onSuccess: () => {
            newMessage.value = '';
            scrollToBottom();
            toast({
                title: 'Message sent',
                description: 'Your message has been sent successfully.',
            });
        },
        onFinish: () => {
            isSending.value = false;
        }
    });
};

const goBack = () => {
    router.visit(route(props.backRoute, props.backParams));
};

onMounted(() => {
    scrollToBottom();
});
</script>

<template>
    <div class="h-full flex flex-col">
        <!-- Header -->
        <div class="border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
            <div class="container flex h-14 items-center gap-4">
                <Button variant="ghost" size="icon" @click="goBack" class="shrink-0">
                    <ChevronLeft class="h-4 w-4" />
                </Button>
                <div class="flex-1">
                    <h1 class="text-lg font-semibold">Messages</h1>
                    <p class="text-sm text-muted-foreground">
                        {{ claim.donation.title }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Chat Container -->
        <div class="flex-1 container py-4">
            <div class="bg-card rounded-lg shadow-lg overflow-hidden max-w-3xl mx-auto h-full flex flex-col">
                <!-- Chat Header -->
                <div class="border-b p-3 bg-muted/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                <span class="text-primary font-semibold">
                                    {{ page.props.auth.user.role === 'donor'
                                        ? claim.charity.name.charAt(0)
                                        : claim.donation.donor.name.charAt(0) }}
                                </span>
                            </div>
                            <div>
                                <h2 class="font-semibold text-sm">
                                    {{ page.props.auth.user.role === 'donor'
                                        ? claim.charity.name
                                        : claim.donation.donor.name }}
                                </h2>
                                <p class="text-xs text-muted-foreground">{{ claim.donation.title }}</p>
                            </div>
                        </div>
                        <div class="text-xs text-muted-foreground">
                            {{ new Date().toLocaleDateString() }}
                        </div>
                    </div>
                </div>

                <!-- Messages Area -->
                <ScrollArea ref="messagesContainer" class="flex-1">
                    <div class="p-3 space-y-2">
                        <div v-for="message in messages" :key="message.id"
                             class="flex flex-col"
                             :class="message.sender.id === page.props.auth.user.id ? 'items-end' : 'items-start'">
                            <div class="max-w-[85%] rounded-lg px-3 py-2"
                                 :class="message.sender.id === page.props.auth.user.id
                                        ? 'bg-primary text-primary-foreground rounded-br-none'
                                        : 'bg-muted rounded-bl-none'">
                                <p class="text-sm leading-relaxed">{{ message.content }}</p>
                                <div class="flex items-center gap-1 text-[10px] mt-1 opacity-70">
                                    <span>{{ message.sender.name }}</span>
                                    <span>•</span>
                                    <span>{{ new Date(message.created_at).toLocaleString() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </ScrollArea>

                <!-- Message Input -->
                <div class="border-t p-3 bg-muted/50">
                    <form @submit.prevent="sendMessage" class="flex gap-2">
                        <Textarea
                            v-model="newMessage"
                            placeholder="Type your message..."
                            class="flex-1 min-h-[60px] max-h-[120px] resize-none text-sm"
                            :disabled="isSending"
                        />
                        <Button
                            type="submit"
                            :disabled="isSending"
                            class="self-end"
                            size="sm"
                        >
                            <span v-if="!isSending">Send</span>
                            <span v-else class="flex items-center gap-2">
                                <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Sending...
                            </span>
                        </Button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
