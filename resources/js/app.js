

require('./bootstrap');

window.Vue = require('vue');


//vuex
import Vuex from 'vuex'

Vue.use(Vuex)
import storevx from './store/index'
import filter from './filter'
const store = new Vuex.Store(storevx)


// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));


//chat scrool
import VueChatScroll from 'vue-chat-scroll'
Vue.use(VueChatScroll)


Vue.component('main-app', require('./components/MainApp').default);



const app = new Vue({
    el: '#app',
    store,
    filter,
});
