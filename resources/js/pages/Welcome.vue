<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>

    <Head title="Charity Bridge">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <!-- Navigation Bar -->
        <header
            class="border-b border-[#19140035] bg-white px-6 py-4 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
            <div class="mx-auto flex max-w-7xl items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="text-[#1b1b18] dark:text-[#EDEDEC]">
                        <path d="M2 12h2"></path>
                        <path d="M20 12h2"></path>
                        <path d="M12 2v2"></path>
                        <path d="M12 20v2"></path>
                        <path d="M20 16a8 8 0 1 0-16 0"></path>
                        <path d="M12 12L6 6"></path>
                    </svg>
                    <span class="text-xl font-bold">Charity Bridge</span>
                </div>
                <nav class="flex items-center gap-4">
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                        class="rounded-sm border border-[#19140035] px-5 py-1.5 text-sm font-medium text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]">
                    Dashboard
                    </Link>
                    <template v-else>
                        <Link :href="route('login')"
                            class="rounded-sm border border-transparent px-5 py-1.5 text-sm font-medium text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]">
                        Log in
                        </Link>
                        <Link :href="route('register')"
                            class="rounded-sm border border-[#19140035] px-5 py-1.5 text-sm font-medium text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]">
                        Register
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <div class="mx-auto grid max-w-7xl gap-8 px-6 py-12 lg:grid-cols-2 lg:items-center lg:py-16">
            <div class="space-y-6">
                <h1 class="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
                    Bridging <span class="text-[#1b1b18] underline dark:text-[#EDEDEC]">Donors</span> with <span
                        class="text-[#1b1b18] underline dark:text-[#EDEDEC]">Charities</span>
                </h1>
                <p class="text-lg text-[#706f6c] dark:text-[#A1A09A]">
                    Charity Bridge connects generous donors with trusted charitable organizations.
                    Upload your donations and we'll match them with the perfect charity to make a real impact.
                </p>
                <div class="flex flex-wrap gap-4">
                    <Link :href="route('register')"
                        class="rounded-sm border border-[#19140035] px-5 py-2 text-sm font-medium text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]">
                    Start Donating
                    </Link>
                    <a href="#how-it-works"
                        class="rounded-sm border border-transparent px-5 py-2 text-sm font-medium text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]">
                        Learn More
                    </a>
                </div>
            </div>
            <div class="relative h-64 overflow-hidden rounded-lg shadow-xl lg:h-96">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-[#1b1b18] to-[#464540] opacity-90 dark:from-[#161615] dark:to-[#3E3E3A]">
                </div>
                <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                        class="mb-4">
                        <path
                            d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z">
                        </path>
                    </svg>
                    <h3 class="text-xl font-bold">Make a Difference Today</h3>
                    <p class="mt-2">Every donation, no matter how small, can change someone's life.</p>
                </div>
            </div>
        </div>

        <!-- How It Works Section -->
        <div id="how-it-works" class="bg-white py-12 dark:bg-[#161615]">
            <div class="mx-auto max-w-7xl px-6">
                <div class="mb-10 text-center">
                    <h2 class="text-3xl font-bold">How It Works</h2>
                    <p class="mt-2 text-[#706f6c] dark:text-[#A1A09A]">Three simple steps to connect your donations with
                        the
                        right charity</p>
                </div>

                <div class="grid gap-8 md:grid-cols-3">
                    <!-- Step 1 -->
                    <div
                        class="rounded-lg border border-[#e3e3e0] bg-[#FDFDFC] p-6 shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:border-[#3E3E3A] dark:bg-[#161615]">
                        <div
                            class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-[#FDFDFC] border border-[#e3e3e0] dark:border-[#3E3E3A] dark:bg-[#161615]">
                            <span class="text-xl font-bold">1</span>
                        </div>
                        <h3 class="mb-2 text-xl font-semibold">Upload Your Donation</h3>
                        <p class="text-[#706f6c] dark:text-[#A1A09A]">
                            Register and provide details about what you wish to donate - goods, services, or funds.
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div
                        class="rounded-lg border border-[#e3e3e0] bg-[#FDFDFC] p-6 shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:border-[#3E3E3A] dark:bg-[#161615]">
                        <div
                            class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-[#FDFDFC] border border-[#e3e3e0] dark:border-[#3E3E3A] dark:bg-[#161615]">
                            <span class="text-xl font-bold">2</span>
                        </div>
                        <h3 class="mb-2 text-xl font-semibold">We Find the Right Match</h3>
                        <p class="text-[#706f6c] dark:text-[#A1A09A]">
                            Our administrators review and match your donation with the charity that can use it most
                            effectively.
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div
                        class="rounded-lg border border-[#e3e3e0] bg-[#FDFDFC] p-6 shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:border-[#3E3E3A] dark:bg-[#161615]">
                        <div
                            class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-[#FDFDFC] border border-[#e3e3e0] dark:border-[#3E3E3A] dark:bg-[#161615]">
                            <span class="text-xl font-bold">3</span>
                        </div>
                        <h3 class="mb-2 text-xl font-semibold">Make an Impact</h3>
                        <p class="text-[#706f6c] dark:text-[#A1A09A]">
                            Track your donation's journey and see the real difference your generosity makes in the
                            community.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- For Charities Section -->
        <div class="bg-[#FDFDFC] py-12 dark:bg-[#0a0a0a]">
            <div class="mx-auto max-w-7xl px-6">
                <div
                    class="overflow-hidden rounded-lg shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] md:flex">
                    <div class="bg-[#1b1b18] p-10 text-white md:w-1/2 dark:bg-[#161615]">
                        <h2 class="mb-4 text-3xl font-bold">For Charities</h2>
                        <p class="mb-6">
                            Are you a charitable organization looking for resources? Join our platform to connect with
                            donors and access the donations you need to further your mission.
                        </p>
                        <Link :href="route('register')"
                            class="inline-block rounded-sm border border-[#EDEDEC] px-5 py-2 text-sm font-medium text-[#EDEDEC] hover:bg-[#ffffff10]">
                        Register as a Charity
                        </Link>
                    </div>
                    <div class="bg-white p-10 dark:bg-[#161615] md:w-1/2">
                        <h2 class="mb-4 text-3xl font-bold">For Donors</h2>
                        <p class="mb-6 text-[#706f6c] dark:text-[#A1A09A]">
                            Have something to donate? Creating an account takes just a minute. Your generous
                            contributions will be matched with verified charities that need them most.
                        </p>
                        <Link :href="route('register')"
                            class="inline-block rounded-sm border border-[#19140035] px-5 py-2 text-sm font-medium text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]">
                        Become a Donor
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="border-t border-[#19140035] bg-white py-8 dark:border-[#3E3E3A] dark:bg-[#161615]">
            <div class="mx-auto max-w-7xl px-6">
                <div class="md:flex md:items-center md:justify-between">
                    <div class="mb-6 flex items-center space-x-2 md:mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="text-[#1b1b18] dark:text-[#EDEDEC]">
                            <path d="M2 12h2"></path>
                            <path d="M20 12h2"></path>
                            <path d="M12 2v2"></path>
                            <path d="M12 20v2"></path>
                            <path d="M20 16a8 8 0 1 0-16 0"></path>
                            <path d="M12 12L6 6"></path>
                        </svg>
                        <span class="text-lg font-bold">Charity Bridge</span>
                    </div>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                        © 2025 Charity Bridge. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
