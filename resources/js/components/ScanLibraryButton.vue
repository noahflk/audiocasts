<template>
    <div class="inline-flex rounded-md shadow">
        <button @click="scan"
                class="inline-flex items-center px-5 py-2 border border-transparent text-base leading-6 font-medium rounded-md text-white bg-theme-blue hover:bg-indigo-500 focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
            <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                 fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="font-semibold">Scan media</span>
        </button>
    </div>
</template>

<script>
import axios from "axios"
import {useToast} from "vue-toastification";

export default {
    setup() {
        const toast = useToast()

        return {toast}
    },
    data() {
        return {
            loading: false
        }
    },
    methods: {
        async scan() {
            this.loading = true

            try {
                await axios.post("/api/scan")

                // Not necessary because we need to do a reload anyway because the page is rendered on the server
                // this.toast.success("Library scan finished")
                window.location.reload();
            } catch (error) {
                console.log(error)
                this.toast.error("Unable to scan library")
            } finally {
                this.loading = false
            }

        }
    },
}
</script>
