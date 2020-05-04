
window.Vue = require('vue');
require('./bootstrap');

import {BootstrapVue, IconsPlugin} from 'bootstrap-vue';

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('game-init-component', require('./components/game/GameInit.vue').default);
Vue.component('game-gather-players', require('./components/game/GatherPlayers.vue').default);

Vue.use(BootstrapVue);


const app = new Vue({
    el: '#app'
});
