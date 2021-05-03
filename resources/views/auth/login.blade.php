<x-layout>
    <x-page-center>
        <img class="h-16 mb-4 -mt-28 hidden sm-h:block" src="/images/audiocasts-icon.svg" alt="Audiocasts Icon" >
        <h2 class="font-bold text-4xl mb-8 text-center">Sign in to your account</h2>
        <div class="bg-white shadow max-w-lg w-full rounded-md p-4 sm:p-8 space-y-4">
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="feed-username">Username</label>
                    <input
                        aria-label="Username" id="feed-username" name="username" type="text" required
                        class="appearance-none text-theme-dark-blue relative rounded-md relative block w-full border border-transparent px-3 py-2 bg-theme-light-blue text-gray-900 focus:outline-none focus:ring-theme-blue focus:border-theme-blue focus:z-10 sm:text-sm sm:leading-5"
                    >
                </div>

                <div>
                    <label for="feed-password">Password</label>
                    <input
                        aria-label="Password" id="feed-password" name="password" type="password" required
                        class="appearance-none text-theme-dark-blue relative rounded-md relative block w-full border border-transparent px-3 py-2 bg-theme-light-blue text-gray-900 focus:outline-none focus:ring-theme-blue focus:border-theme-blue focus:z-10 sm:text-sm sm:leading-5"
                    >
                </div>

                @error('error')
                    <span class="text-red-600 text-center" role="alert">
                        <p>{{ $message }}</p>
                    </span>
                @enderror

                <button class="w-full mt-4 px-5 py-2 border border-transparent text-base leading-6 font-medium rounded-md text-white bg-theme-blue hover:bg-indigo-500 focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                    <span class="font-semibold">Sign in</span>
                </button>
            </form>
        </div>
    </x-page-center>
</x-layout>
