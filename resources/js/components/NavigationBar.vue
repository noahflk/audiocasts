<template>
    <nav class="bg-white shadow text-theme-dark-blue">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center space-x-6">
                    <div class="flex-shrink-0">
                        <a href="/">
                            <img
                                class="h-14"
                                src="/images/audiocasts-logo-full.svg"
                                alt="Audiocasts"
                            />
                        </a>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-4 flex items-center md:ml-6">
                        <a
                            href="/feed"
                            target="_blank"
                            class="p-1 rounded-full text-theme-dark-blue hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white"
                        >
                            <span class="sr-only">View RSS Feed</span>
                            <svg
                                class="w-8 h-8"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 5c7.18 0 13 5.82 13 13M6 11a7 7 0 017 7m-6 0a1 1 0 11-2 0 1 1 0 012 0z"
                                ></path>
                            </svg>
                        </a>
                        <profile-dropdown></profile-dropdown>
                    </div>
                </div>
                <div class="-mr-2 flex md:hidden">
                    <button
                        @click="mobileNavOpen = !mobileNavOpen"
                        class="inline-flex text-theme-dark-blue items-center justify-center p-2 rounded-md text-gray-400 hover:bg-theme-light-blue focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white"
                    >
                        <span class="sr-only">Open main menu</span>
                        <svg
                            v-show="!mobileNavOpen"
                            class="h-6 w-6"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>

                        <svg
                            v-show="mobileNavOpen"
                            class="h-6 w-6"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div v-show="mobileNavOpen" class="md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
                <a
                    href="#"
                    class="bg-theme-dark-blue text-theme-light-blue block px-3 py-2 rounded-md text-base font-medium"
                >Feed</a
                >
                <a
                    href="#"
                    class="hover:bg-theme-light-blue block px-3 py-2 rounded-md text-base font-medium"
                >Scan Media</a
                >
            </div>
            <div class="pt-4 pb-3 border-t border-theme-dark-blue">
                <div class="flex items-center px-5">
                    <div class="flex-shrink-0">
                        <svg
                            class="w-8 h-8"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                            ></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="font-medium leading-none">username</div>
                    </div>
                </div>
                <div class="mt-3 px-2 space-y-1">
                    <a
                        href="#"
                        class="block px-3 py-2 rounded-md text-base font-medium hover:bg-theme-light-blue"
                    >Settings</a
                    >
                    <a
                        href="#"
                        class="block px-3 py-2 rounded-md text-base font-medium hover:bg-theme-light-blue"
                    >Sign out</a
                    >
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
import ProfileDropdown from "./ProfileDropdown";

export default {
    components: {
        ProfileDropdown,
    },
    data() {
        return {
            mobileNavOpen: false,
        };
    },
};
</script>
