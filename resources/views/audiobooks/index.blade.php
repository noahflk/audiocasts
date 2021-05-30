<x-layout>
    @if(!$audiobooks->isEmpty())
        <div class="grid gap-4 sm:gap-8 grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
            @foreach($audiobooks as $audiobook)
                <div>
                    <div class="relative aspect-h-1 aspect-w-1 rounded overflow-hidden shadow">
                        <img class="w-full h-full  absolute inset-0 object-cover" alt="Audiobook cover"
                             src="{{ $audiobook->coverPath() }}">
                    </div>
                    <div class="text-theme-dark-blue mt-1 sm:mt-3">
                        <a>
                            <div class="text-lg sm:text-xl font-bold truncate"
                                 title="{{ $audiobook->title }}">{{ $audiobook->title }}</div>
                        </a>
                        <div class="text-lg sm:text-xl font-light truncate">{{ $audiobook->author }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="flex items-center justify-center h-full">
            <div class="text-center space-y-6 text-theme-dark-blue">
                <img class="w-48 mx-auto" alt="Empty illustration" src="/images/illustration-empty.svg">
                <div class="space-y-2">
                    <p class="text-4xl font-bold">Your library is empty!</p>
                    <p class="text-xl">Add an audiobook and then press the scan button</p>
                </div>
                <scan-library-button></scan-library-button>
            </div>
        </div>
    @endif
</x-layout>
