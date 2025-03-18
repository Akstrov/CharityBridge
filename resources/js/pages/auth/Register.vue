<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, ChevronLeft } from 'lucide-vue-next';
import { ref, computed } from 'vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
    phone: '',
    address: '',
    // Charity specific fields
    organization_name: '',
    organization_logo: null,
    mission_statement: '',
    description: '',
    website: '',
    registration_number: '',
    category: '',
    tax_id: '',
    areas_of_focus: '',
    year_established: '',
    social_media_links: '',
});

const selectedRole = ref('');
const isCharity = computed(() => selectedRole.value === 'charity');
const currentStep = ref(1);

const nextStep = () => {
    if (currentStep.value === 1 && validateStep1()) {
        currentStep.value = 2;
    }
};

const prevStep = () => {
    if (currentStep.value === 2) {
        currentStep.value = 1;
    }
};

const handleRoleChange = (value: string) => {
    selectedRole.value = value;
    form.role = value;
};

const validateStep1 = () => {
    return form.name && 
           form.email && 
           form.role && 
           form.phone && 
           form.address && 
           form.password && 
           form.password_confirmation;
};

const submit = () => {
    if (isCharity.value) {
        if (currentStep.value === 1) {
            if (validateStep1()) {
                nextStep();
            }
        } else {
            form.post(route('register'), {
                onFinish: () => form.reset('password', 'password_confirmation'),
                onSuccess: () => {
                    console.log('Registration successful');
                },
                onError: (errors) => {
                    console.error('Registration failed:', errors);
                }
            });
        }
    } else {
        if (validateStep1()) {
            console.log('Submitting donor registration:', form);
            form.post(route('register'), {
                onFinish: () => form.reset('password', 'password_confirmation'),
                onSuccess: () => {
                    console.log('Registration successful');
                },
                onError: (errors) => {
                    console.error('Registration failed:', errors);
                }
            });
        } else {
            console.log('Validation failed:', {
                name: form.name,
                email: form.email,
                role: form.role,
                phone: form.phone,
                address: form.address,
                password: form.password,
                password_confirmation: form.password_confirmation
            });
        }
    }
};
</script>

<template>
    <AuthBase title="Create an account" description="Enter your details below to create your account">
        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <!-- Step 1: Basic Information -->
                <Transition name="slide" mode="out-in">
                    <div v-if="currentStep === 1" class="space-y-6">
                        <div class="grid gap-2">
                            <Label for="name">Name</Label>
                            <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" v-model="form.name" placeholder="Full name" />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">Email address</Label>
                            <Input id="email" type="email" required :tabindex="2" autocomplete="email" v-model="form.email" placeholder="email@example.com" />
                            <InputError :message="form.errors.email" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="role">Role</Label>
                            <Select v-model="selectedRole" required @update:modelValue="handleRoleChange">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select your role" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="donor">Donor</SelectItem>
                                    <SelectItem value="charity">Charity</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.role" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="phone">Phone Number</Label>
                            <Input id="phone" type="tel" required :tabindex="3" autocomplete="tel" v-model="form.phone" placeholder="+1234567890" />
                            <InputError :message="form.errors.phone" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="address">Address</Label>
                            <Input id="address" type="text" required :tabindex="4" autocomplete="street-address" v-model="form.address" placeholder="Your address" />
                            <InputError :message="form.errors.address" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password">Password</Label>
                            <Input
                                id="password"
                                type="password"
                                required
                                :tabindex="5"
                                autocomplete="new-password"
                                v-model="form.password"
                                placeholder="Password"
                            />
                            <InputError :message="form.errors.password" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password_confirmation">Confirm password</Label>
                            <Input
                                id="password_confirmation"
                                type="password"
                                required
                                :tabindex="6"
                                autocomplete="new-password"
                                v-model="form.password_confirmation"
                                placeholder="Confirm password"
                            />
                            <InputError :message="form.errors.password_confirmation" />
                        </div>

                        <div v-if="currentStep === 1">
                            <Button 
                                type="button" 
                                class="mt-2 w-full" 
                                :disabled="form.processing"
                                @click="nextStep"
                                v-if="isCharity"
                            >
                                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                                Next
                            </Button>
                            <Button 
                                type="submit" 
                                class="mt-2 w-full" 
                                :disabled="form.processing"
                                v-else
                            >
                                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                                Create account
                            </Button>
                        </div>
                    </div>
                </Transition>

                <!-- Step 2: Charity Information -->
                <Transition name="slide" mode="out-in">
                    <div v-if="currentStep === 2" class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium">Charity Information</h3>
                            <Button type="button" variant="ghost" size="sm" @click="prevStep">
                                <ChevronLeft class="h-4 w-4 mr-2" />
                                Back
                            </Button>
                        </div>

                        <div class="grid gap-2">
                            <Label for="organization_name">Organization Name</Label>
                            <Input id="organization_name" type="text" required v-model="form.organization_name" placeholder="Your organization name" />
                            <InputError :message="form.errors.organization_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="description">Description</Label>
                            <Input id="description" type="text" required v-model="form.description" placeholder="Brief description of your organization" />
                            <InputError :message="form.errors.description" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="category">Category</Label>
                            <Input id="category" type="text" required v-model="form.category" placeholder="e.g., Education, Healthcare, Environment" />
                            <InputError :message="form.errors.category" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="website">Website</Label>
                            <Input id="website" type="url" v-model="form.website" placeholder="https://your-organization.com" />
                            <InputError :message="form.errors.website" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="registration_number">Registration Number</Label>
                            <Input id="registration_number" type="text" v-model="form.registration_number" placeholder="Your registration number" />
                            <InputError :message="form.errors.registration_number" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="tax_id">Tax ID</Label>
                            <Input id="tax_id" type="text" v-model="form.tax_id" placeholder="Your tax ID" />
                            <InputError :message="form.errors.tax_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="mission_statement">Mission Statement</Label>
                            <Input id="mission_statement" type="text" v-model="form.mission_statement" placeholder="Your organization's mission statement" />
                            <InputError :message="form.errors.mission_statement" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="areas_of_focus">Areas of Focus</Label>
                            <Input id="areas_of_focus" type="text" v-model="form.areas_of_focus" placeholder="e.g., Education, Poverty Relief, Environmental Protection" />
                            <InputError :message="form.errors.areas_of_focus" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="year_established">Year Established</Label>
                            <Input id="year_established" type="number" v-model="form.year_established" placeholder="YYYY" />
                            <InputError :message="form.errors.year_established" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="social_media_links">Social Media Links</Label>
                            <Input id="social_media_links" type="text" v-model="form.social_media_links" placeholder="Comma-separated social media links" />
                            <InputError :message="form.errors.social_media_links" />
                        </div>

                        <Button type="submit" class="mt-2 w-full" :disabled="form.processing">
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                            Create account
                        </Button>
                    </div>
                </Transition>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink :href="route('login')" class="underline underline-offset-4">Log in</TextLink>
            </div>
        </form>
    </AuthBase>
</template>

<style scoped>
.slide-enter-active,
.slide-leave-active {
    transition: all 0.3s ease;
}

.slide-enter-from {
    opacity: 0;
    transform: translateX(30px);
}

.slide-leave-to {
    opacity: 0;
    transform: translateX(-30px);
}
</style>
