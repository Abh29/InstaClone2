/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');

const $ = require('jquery');
require('bootstrap');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});


globalThis.__VUE_OPTIONS_API__ = true;
globalThis.__VUE_PROD_DEVTOOLS__ = true;


import { createApp } from 'vue'
import LikeButton from "./components/LikeButton.vue";
import SubscribeButton from "./components/SubscribeButton.vue";
import ImagePost from "./components/ImagePost.vue";
import ProfileInfo from "./components/ProfileInfo.vue";


import axios from 'axios'
import VueAxios from 'vue-axios'

import VLazyImageComponent from "v-lazy-image";

const app = createApp({
    el: '#app'
})
    .component('LikeButton', LikeButton)
    .component('SubscribeButton', SubscribeButton)
    .component('ImagePost', ImagePost)
    .component('VLazyImage', VLazyImageComponent)
    .component('ProfileInfo', ProfileInfo)
    .use(VueAxios, axios)
    .mount("#app")

import './controllers/profile_controller';
