<x-layout>
    <div class="flex justify-between flex-col md:flex-row h-full">
        <div class="flex-grow-0 md:w-48 text-theme-dark-blue mb-6 sm:mr-8">
            <h3 class="px-3 mb-3 font-semibold text-3xl">Settings</h3>
            <ul class="space-y-2">
                <li>
                    <a href="/settings/1" class="flex items-center px-3 py-2 transition-colors duration-200 relative block hover:text-gray-600">
                        <span class="rounded-md absolute inset-0 bg-gray-300 opacity-0"></span>
                        <svg class="w-8 h-8 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">--}}
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span class="relative font-medium text-xl">Media</span>
                    </a>
                </li>
                <li>
                    <a href="/settings/2" class="flex items-center px-3 py-2 transition-colors duration-200 relative block hover:text-gray-600">
                        <span class="rounded-md absolute inset-0 bg-gray-300 opacity-50"></span>
                        <span class="relative">
                            <svg class="w-8 h-8 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">--}}
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </span>
                        <span class="relative font-medium text-xl">Security</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="flex-grow">
            <x-page-center>
                <div class="bg-white shadow max-w-xl w-full h-full sm:h-auto rounded-md p-4 sm:p-8">
                    <div class="flex h-full flex-col justify-between">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between space-x-2">
                                <h3 class="text-xl">Protect Web Interface</h3>
                                <toggle-button></toggle-button>
                            </div>
                            <div class="flex items-center justify-between space-x-2">
                                <h3 class="text-xl">Protect RSS Interface</h3>
                                <toggle-button value="true"></toggle-button>
                            </div>
                            <form class="mt-8 space-y-4">
                                <div>
                                    <label for="feed-username">Username</label>
                                    <input
                                        aria-label="Username" id="feed-username" name="username" required
                                        class="appearance-none text-theme-dark-blue border-2 rounded-md relative block w-full px-3 py-2 bg-theme-light-blue text-gray-900 rounded-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
                                    >
                                </div>
                                <div>
                                    <label for="feed-password">Username</label>
                                    <input
                                        aria-label="Password" id="feed-password" name="password" type="password" required
                                        class="appearance-none border-red-500 text-theme-dark-blue border-2 rounded-md relative block w-full px-3 py-2 bg-theme-light-blue text-gray-900 rounded-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
                                    >
                                    <span class="text-red-600 text-sm" role="alert">
                                        Password must be at least 8 characters long
                                    </span>
                                </div>
                            </form>
                        </div>
                        <div>
                            <div class="text-right ">
                                <button class="mt-10 px-5 py-2 border border-transparent text-base leading-6 font-medium rounded-md text-white bg-theme-blue hover:bg-indigo-500 focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                                    <span class="font-semibold">Save changes</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </x-page-center>
        </div>
    </div>
</x-layout>
