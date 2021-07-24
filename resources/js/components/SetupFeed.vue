<template>
    <h3 class="text-2xl font-medium">RSS Feed Settings</h3>

    <form class="mt-8 space-y-3">
        <div>
            <label for="title">Title <span class="text-red-600">*</span></label>
            <input
                v-model="title.value"
                aria-label="Title"
                id="title"
                name="title"
                placeholder="Dan's audiobooks"
                required
                class="appearance-none text-theme-dark-blue border-transparent rounded-md relative block w-full px-3 py-2 bg-theme-light-blue text-gray-900 rounded-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
            />
            <span
                v-if="title.status !== ''"
                class="text-red-600 text-sm truncate"
                role="alert"
            >
                {{ this.title.status }}
            </span>
        </div>

        <div>
            <label for="description">Description</label>
            <input
                v-model="description.value"
                aria-label="Description"
                id="description"
                name="description"
                class="appearance-none text-theme-dark-blue border-transparent rounded-md relative block w-full px-3 py-2 bg-theme-light-blue text-gray-900 rounded-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
            />
            <span
                v-if="description.status !== ''"
                class="text-red-600 text-sm truncate"
                role="alert"
            >
                {{ this.description.status }}
            </span>
        </div>

        <div>
            <label for="author"
                >Feed Author <span class="text-red-600">*</span></label
            >
            <input
                v-model="author.value"
                aria-label="Feed Author"
                id="author"
                name="author"
                placeholder="Dan Smith"
                required
                class="appearance-none text-theme-dark-blue border-transparent rounded-md relative block w-full px-3 py-2 bg-theme-light-blue text-gray-900 rounded-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
            />
            <span
                v-if="author.status !== ''"
                class="text-red-600 text-sm truncate"
                role="alert"
            >
                {{ this.author.status }}
            </span>
        </div>

        <div class="flex w-full justify-between">
            <div>
                <div class="flex flex-col space-y-2">
                    <label>Cover</label>
                    <div class="space-x-2 flex">
                        <label
                            for="cover"
                            class="cursor-pointer px-5 py-2 border border-transparent text-base text-theme-blue border border-theme-blue leading-6 font-medium rounded-md text-white bg-theme-medium-blue hover:bg-theme-light-blue focus:outline-none focus:shadow-outline transition duration-150 ease-in-out"
                        >
                            {{ this.file?.name ?? "Select..." }}
                        </label>
                        <input
                            hidden
                            type="file"
                            accept="image/*"
                            aria-label="Select a cover image"
                            id="cover"
                            name="cover"
                            @change="filesChange"
                        />
                        <button
                            @click="removeImage"
                            class="text-theme-dark-blue"
                            v-show="this.file"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
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

                    <span
                        v-if="cover.status !== ''"
                        class="text-red-600 text-sm truncate"
                        role="alert"
                    >
                        {{ this.cover.status }}
                    </span>
                </div>
            </div>

            <div>
                <label for="language"
                    >Language <span class="text-red-600">*</span></label
                >
                <input
                    v-model="language.value"
                    aria-label="Language"
                    id="language"
                    name="language"
                    placeholder="en-us"
                    required
                    class="appearance-none text-theme-dark-blue border-transparent rounded-md relative block w-full px-3 py-2 bg-theme-light-blue text-gray-900 rounded-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
                />
                <span
                    v-if="language.status !== ''"
                    class="text-red-600 text-sm"
                    role="alert"
                >
                    {{ this.language.status }}
                </span>
            </div>
        </div>
    </form>

    <div class="flex items-center justify-between pt-10">
        <div class="space-x-3">
            <span
                class="w-9 h-1 bg-theme-dark-blue rounded-md inline-block"
            ></span>
            <span
                class="w-9 h-1 bg-theme-dark-blue rounded-md inline-block"
            ></span>
            <span
                class="w-9 h-1 bg-theme-dark-blue rounded-md inline-block"
            ></span>
        </div>
        <div class="space-x-3">
            <button
                @click="$emit('back')"
                class="px-5 py-2 border border-transparent text-base text-theme-blue border border-theme-blue leading-6 font-medium rounded-md text-white bg-theme-medium-blue hover:bg-theme-light-blue focus:outline-none focus:shadow-outline transition duration-150 ease-in-out"
            >
                <span class="font-semibold">Back</span>
            </button>
            <button
                @click="save"
                class="inline-flex items-center px-5 py-2 border border-transparent text-base leading-6 font-medium rounded-md text-white bg-theme-blue hover:bg-indigo-500 focus:outline-none focus:shadow-outline transition duration-150 ease-in-out"
            >
                <svg
                    v-if="loading"
                    class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    ></circle>
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    ></path>
                </svg>
                <span class="font-semibold">Finish</span>
            </button>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "setup-feed",
    emits: ["back", "next"],
    data() {
        return {
            title: { value: "", status: "" },
            description: { value: "", status: "" },
            author: { value: "", status: "" },
            cover: { value: "", status: "" },
            // Code like "en" (ISO 639-1) or "eng" (ISO 639-2).
            language: { value: "", status: "" },
            file: null,
            loading: false,
        };
    },
    methods: {
        filesChange(event) {
            if (!event.target.files.length) return;
            this.file = event.target.files[0];
        },
        removeImage(event) {
            event.preventDefault();
            this.file = null;
        },
        async save() {
            // build form data for cover store
            // send regular request for rest
            // send request to finish setup
            // redirect to dashboard

            this.loading = true;

            try {
                await axios.post("/api/feed", {
                    title: this.title.value,
                    description: this.description.value,
                    author: this.author.value,
                    language: this.language.value,
                });

                if (this.file) {
                    const formData = new FormData();
                    formData.append("cover", this.file);
                    await axios.post("/api/feed/cover", formData);
                }

                await axios.post("/api/settings", {
                    key: "SETUP_DONE",
                    value: true,
                });

                window.location = "/";
            } catch (error) {
                const data = error?.response?.data;
                const title = data?.errors?.title;
                const description = data?.errors?.description;
                const author = data?.errors?.author;
                const language = data?.errors?.language;

                this.title.status = title ? title[title.length - 1] : "";
                this.description.status = description
                    ? description[description.length - 1]
                    : "";
                this.author.status = author ? author[author.length - 1] : "";
                this.language.status = language
                    ? language[language.length - 1]
                    : "";
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>
