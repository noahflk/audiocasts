<x-layout>
    <div class="mb-4">
        <a href="/" class="font-medium text-theme-blue hover:text-indigo-500">
            ← See all Audiobooks
        </a>
    </div>

    <div class="grid grid-cols-6 gap-4">
        <div class="col-span-6 lg:col-span-4 order-last lg:order-first">
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        @if($audiobook->files())
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">

                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            File
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Size
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Duration
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">

                                    @foreach($audiobook->files as $file)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $loop->index + 1 }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $file->name }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                  {{ $file->type }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $file->sizeInMB() }} MB
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $file->formattedDuration() }}
                                            </td>
                                        </tr>
                                    @endforeach

                                    <!-- More people... -->
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p>No files found.</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
        <div class="col-span-6 lg:col-span-2 lg:ml-6">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-48 w-48">
                        <img class="h-full w-full shadow rounded-lg object-cover"
                             src="{{ $audiobook->coverPath() }}"
                             alt="Cover of {{ $audiobook->title  }}">
                    </div>
                    <div class="ml-6 space-y-2">
                        <div class="text-lg font-bold text-gray-900">
                            {{ $audiobook->title  }}
                        </div>
                        <div class="text-lg font text-gray-500">
                            {{ $audiobook->author  }}
                        </div>
                        <div class="text text-gray-500">
                            Lenth: {{ $audiobook->formattedDuration()  }}
                        </div>
                        <div class="text text-gray-500">
                            {{ $audiobook->files()->count() }} files
                        </div>
                        <div class="text text-gray-500">
                            Total size: {{ $audiobook->sizeInMB() }} MB
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
