<template>
    <div class="flex items-center justify-between space-x-2">
        <h3 class="text-2xl">Protect Web Interface</h3>
        <toggle-button v-model="web.enabled"></toggle-button>
    </div>

    <transition name="fade">
        <form v-if="web.enabled" class="mt-8 space-y-3">
            <div>
                <label for="web-username">Username</label>
                <input
                    v-model="web.username"
                    aria-label="Username"
                    id="web-username"
                    name="username"
                    required
                    class="appearance-none text-theme-dark-blue border-2 rounded-md relative block w-full px-3 py-2 bg-theme-light-blue text-gray-900 rounded-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
                    :class="webUsernameError"
                />
                <p class="text-red-500 text-sm italic"></p>
                <span
                    v-if="webStatus.username !== ''"
                    class="text-red-600 text-sm truncate"
                    role="alert"
                >
                    {{ this.webStatus.username }}
                </span>
            </div>

            <div>
                <label for="web-password">Password</label>
                <input
                    v-model="web.password"
                    aria-label="Password"
                    id="web-password"
                    name="password"
                    type="password"
                    required
                    class="appearance-none text-theme-dark-blue border-2 rounded-md relative block w-full px-3 py-2 bg-theme-light-blue text-gray-900 rounded-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
                    :class="webPasswordError"
                />
                <span
                    v-if="webStatus.password !== ''"
                    class="text-red-600 text-sm truncate"
                    role="alert"
                >
                    {{ this.webStatus.password }}
                </span>
            </div>
        </form>
    </transition>

    <div class="flex items-center justify-between space-x-2">
        <h3 class="text-2xl">Protect RSS feed</h3>
        <toggle-button v-model="feed.enabled"></toggle-button>
    </div>

    <transition name="fade">
        <form v-if="feed.enabled" class="mt-8 space-y-3">
            <div>
                <label for="feed-username">Username</label>
                <input
                    v-model="feed.username"
                    aria-label="Username"
                    id="feed-username"
                    name="username"
                    required
                    class="appearance-none text-theme-dark-blue border-2 rounded-md relative block w-full px-3 py-2 bg-theme-light-blue text-gray-900 rounded-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
                    :class="feedUsernameError"
                />
                <span
                    v-if="feedStatus.username !== ''"
                    class="text-red-600 text-sm truncate"
                    role="alert"
                >
                    {{ this.feedStatus.username }}
                </span>
            </div>

            <div>
                <label for="feed-password">Password</label>
                <input
                    v-model="feed.password"
                    aria-label="Username"
                    id="feed-password"
                    name="password"
                    type="password"
                    required
                    class="appearance-none text-theme-dark-blue border-2 rounded-md relative block w-full px-3 py-2 bg-theme-light-blue text-gray-900 rounded-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:text-sm sm:leading-5"
                    :class="feedPasswordError"
                />
                <span
                    v-if="feedStatus.password !== ''"
                    class="text-red-600 text-sm truncate"
                    role="alert"
                >
                    {{ this.feedStatus.password }}
                </span>
            </div>
        </form>
    </transition>

    <div class="flex items-center justify-between pt-10">
        <div class="space-x-3">
            <span
                class="w-9 h-1 bg-theme-dark-blue rounded-md inline-block"
            ></span>
            <span
                class="w-9 h-1 bg-theme-dark-blue rounded-md inline-block"
            ></span>
            <span
                class="w-9 h-1 bg-theme-medium-blue rounded-md inline-block"
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
                @click="next"
                class="px-5 py-2 border border-transparent text-base leading-6 font-medium rounded-md text-white bg-theme-blue hover:bg-indigo-500 focus:outline-none focus:shadow-outline transition duration-150 ease-in-out"
            >
                <span class="font-semibold">Next</span>
            </button>
        </div>
    </div>
</template>

<script>
import ToggleButton from "./ToggleButton";

const saveChanges = async (type, { enabled, username, password }) => {
    const { data: users } = await axios.get("/api/users");
    const user = users[type];
    const userIsEmpty = user && Object.keys(user).length === 0;

    // Enabled and exists on server, now newly disabled
    if (!!user.enabled && !userIsEmpty && enabled === false) {
        return axios.patch(`/api/users/${type}`, {
            enabled: false,
        });
    }

    // Does not exist on server, now newly enabled
    if (userIsEmpty && enabled) {
        return axios.post("/api/users", {
            username,
            password,
            type,
        });
    }

    // Does exist on server and is enabled and any value has changed
    if (!!user.enabled && !userIsEmpty && (username || password || enabled)) {
        return axios.patch(`/api/users/${type}`, {
            username,
            password,
            enabled,
        });
    }
};

export default {
    name: "setup-security",
    emits: ["back", "next"],
    components: {
        ToggleButton,
    },
    async mounted() {
        try {
            const { data: users } = await axios.get("/api/users");
            this.web.enabled = users.web.enabled == true;
            this.feed.enabled = users.feed.enabled == true;

            this.web.username = users.web.username ?? "";
            this.feed.username = users.feed.username ?? "";

            this.initialWeb = {
                enabled: users.web.enabled == true,
                username: users.web.username ?? "",
            };
            this.initialFeed = {
                enabled: users.feed.enabled == true,
                username: users.feed.username ?? "",
            };

            // TODO: Make this reactive, computed value
            this.webStatus.password =
                this.web.enabled || users.web.username
                    ? "Entering a new password will the override existing one."
                    : "";
            this.feedStatus.password =
                this.feed.enabled || users.feed.username
                    ? "Entering a new password will the override existing one."
                    : "";
        } catch (error) {
            // TODO: Toast error
            console.log(error);
        }
    },
    data() {
        return {
            web: {
                username: "",
                password: "",
                enabled: false,
                passwordSet: false,
            },
            feed: {
                username: "",
                password: "",
                enabled: false,
                passwordSet: false,
            },
            initialWeb: { username: "", enabled: false },
            initialFeed: { username: "", enabled: false },
            webStatus: { username: "", password: "" },
            feedStatus: { username: "", password: "" },
            loading: false,
        };
    },
    computed: {
        webUsernameError() {
            return {
                "border-red-500": this.webStatus.username !== "",
                "border-theme-light-blue": this.webStatus.username === "",
            };
        },
        webPasswordError() {
            return {
                "border-red-500": this.webStatus.password !== "",
                "border-theme-light-blue": this.webStatus.password === "",
            };
        },
        feedUsernameError() {
            return {
                "border-red-500": this.feedStatus.username !== "",
                "border-theme-light-blue": this.webStatus.username === "",
            };
        },
        feedPasswordError() {
            return {
                "border-red-500": this.feedStatus.password !== "",
                "border-theme-light-blue": this.webStatus.password === "",
            };
        },
    },
    methods: {
        async next() {
            this.loading = true;
            let error = false;

            const webChanges = {};

            if (this.web.enabled !== this.initialWeb.enabled) {
                webChanges.enabled = this.web.enabled;
            }

            if (
                this.web.enabled &&
                this.web.username !== this.initialWeb.username
            ) {
                webChanges.username = this.web.username;
            }

            if (this.web.enabled && this.web.password !== "") {
                webChanges.password = this.web.password;
            }

            try {
                await saveChanges("web", webChanges);
            } catch (err) {
                error = true;
                this.handleSaveError("web", err);
            }

            const feedChanges = {};

            // Feed has been newly disabled
            if (this.feed.enabled !== this.initialFeed.enabled) {
                feedChanges.enabled = this.feed.enabled;
            }

            if (
                this.feed.enabled &&
                this.feed.username !== this.initialFeed.username
            ) {
                feedChanges.username = this.feed.username;
            }

            if (this.feed.enabled && this.feed.password !== "") {
                feedChanges.password = this.feed.password;
            }

            try {
                await saveChanges("feed", feedChanges);
            } catch (err) {
                error = true;
                this.handleSaveError("feed", err);
            }

            if (error) {
                // TODO: Show error toast
            } else {
                this.$emit("next");
            }

            // TODO: Loading animation on finish button

            this.loading = false;
        },
        handleSaveError(type, error) {
            const data = error?.response?.data;
            const usernameError = data?.errors?.username;
            const passwordError = data?.errors?.password;

            if (!data) {
                // TODO: Toast
                console.log(error);
                return;
            }

            if (type == "web") {
                if (usernameError) {
                    this.webStatus.username =
                        usernameError[usernameError.length - 1];
                } else {
                    this.webStatus.username = "";
                }

                if (passwordError) {
                    this.webStatus.password =
                        passwordError[passwordError.length - 1];
                } else {
                    this.webStatus.password = "";
                }

                if (typeof data.errors === "string") {
                    this.webStatus.username = data.errors;
                }
            }

            if (type == "feed") {
                if (usernameError) {
                    this.feedStatus.username =
                        usernameError[usernameError.length - 1];
                } else {
                    this.feedStatus.username = "";
                }

                if (passwordError) {
                    this.feedStatus.password =
                        passwordError[passwordError.length - 1];
                } else {
                    this.feedStatus.password = "";
                }

                if (typeof data.errors === "string") {
                    this.feedStatus.username = data.errors;
                }
            }
        },
    },
};
</script>
