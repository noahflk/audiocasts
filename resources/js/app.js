/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");

import { createApp } from "vue";
import Toast, { POSITION } from "vue-toastification";
import "vue-toastification/dist/index.css";

import ToggleButton from "./components/ToggleButton";
import NavigationBar from "./components/NavigationBar";
import SetupFlow from "./components/SetupFlow";
import ScanLibraryButton from "./components/ScanLibraryButton";

const app = createApp({});

app.use(Toast, {
    position: POSITION.BOTTOM_LEFT
})

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

app.component("toggle-button", ToggleButton);
app.component("scan-library-button", ScanLibraryButton);
app.component("navigation-bar", NavigationBar);
app.component("setup-flow", SetupFlow);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

app.mount("#app");
