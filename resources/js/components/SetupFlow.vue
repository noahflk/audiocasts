<template>
    <h2 class="font-bold text-4xl mb-4 sm:mb-8">Server Setup</h2>

    <div v-show="page === 1" class="bg-white shadow max-w-xl w-full rounded-md p-4 sm:p-8 space-y-4">
        <setup-media-location @next="page++"></setup-media-location>
    </div>

    <div v-show="page === 2" class="bg-white shadow max-w-xl w-full rounded-md p-4 sm:p-8 space-y-4">
        <setup-security @next="page++" @back="page--"></setup-security>
    </div>

    <div v-show="page === 3" class="bg-white shadow max-w-xl w-full rounded-md p-4 sm:p-8 space-y-4">
        <setup-feed @back="page--"></setup-feed>
    </div>
</template>

<script>
import SetupMediaLocation from "./SetupMediaLocation";
import SetupSecurity from "./SetupSecurity";
import SetupFeed from "./SetupFeed";

export default {
    components: { SetupSecurity, SetupMediaLocation, SetupFeed },
    data() {
        return {
            page: 1,
        }
    }
}
</script>
