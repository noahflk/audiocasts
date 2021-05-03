<template>
    <h3 class="text-2xl font-medium">Select library locations</h3>
    <ul class="space-y-3">
        <li
            v-for="(directory, index) in directories"
            :key="directory.path"
            class="bg-theme-light-blue py-2 px-3 rounded-md flex justify-between"
        >
            <span class="truncate">{{ directory.path }}</span>
            <button
                @click="deleteDirectory(directory, index)"
                class="w-6 h-6 flex-shrink-0"
            >
                <svg
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    ></path>
                </svg>
            </button>
        </li>

        <li class="relative rounded-md shadow-sm">
            <form @submit.prevent="addDirectory">
                <input
                    type="text"
                    v-model="newDirectory"
                    v-bind:class="{
                        'border-red-500 border-2': errorMessage,
                        'border-theme-light-blue': !errorMessage,
                    }"
                    placeholder="Add absolute path..."
                    class="bg-theme-light-blue focus:ring-indigo-500 focus:border-theme-blue block w-full pl-3 pr-12 rounded-md"
                />
                <div class="absolute inset-y-0 right-0 flex items-center">
                    <button class="w-6 h-6 mr-3" type="submit">
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"
                            ></path>
                        </svg>
                    </button>
                </div>
            </form>
        </li>

        <span class="text-red-600 text-sm" role="alert">
            {{ errorMessage }}
        </span>
    </ul>
    <div class="flex items-center justify-between pt-10">
        <div class="space-x-3">
            <span
                class="w-9 h-1 bg-theme-dark-blue rounded-md inline-block"
            ></span>
            <span
                class="w-9 h-1 bg-theme-medium-blue rounded-md inline-block"
            ></span>
            <span
                class="w-9 h-1 bg-theme-medium-blue rounded-md inline-block"
            ></span>
        </div>
        <button
            @click="next"
            class="px-5 py-2 border border-transparent text-base leading-6 font-medium rounded-md text-white bg-theme-blue hover:bg-indigo-500 focus:outline-none focus:shadow-outline transition duration-150 ease-in-out"
        >
            <span class="font-semibold">Next</span>
        </button>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "setup-media-location",
    emits: ["next"],
    data() {
        return {
            directories: [],
            newDirectory: null,
            errorMessage: null,
        };
    },
    mounted() {
        axios.get("/api/directories").then((response) => {
            this.directories = response?.data?.directories;
        });
    },
    methods: {
        async addDirectory() {
            if (!this.newDirectory || this.newDirectory === "") {
                this.errorMessage = "Please enter a valid path";
                return;
            }

            // Perform validation before adding
            // call POST /api/directories with JSON
            try {
                const { data } = await axios.post("/api/directories", {
                    directories: [this.newDirectory],
                });

                this.directories = this.directories.concat(data);
                this.newDirectory = null;
                this.errorMessage = null;
            } catch (error) {
                if (error.response?.data?.invalid) {
                    this.errorMessage = "Invalid directory";
                } else {
                    this.errorMessage = "Unknown error";
                }

                return error;
            }

            return;
        },
        deleteDirectory(directory, index) {
            axios.delete(`/api/directories/${directory.id}`).then(() => {
                this.directories.splice(index, 1);
            });
        },
        async next() {
            if (this.newDirectory !== "") {
                const error = await this.addDirectory();
                if (error) return;
            }

            this.$emit("next");
        },
    },
};
</script>
