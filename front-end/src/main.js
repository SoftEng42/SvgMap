import Vue from 'vue'
import App from './App.vue'
import BootstrapVue from 'bootstrap-vue'
import axios from 'axios';
import VueAxios from 'vue-axios';
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

Vue.use(BootstrapVue);
Vue.use(VueAxios, axios);
Vue.config.productionTip = false
if (Vue.config.productionTip) {
  // Vue.axios.defaults.baseURL = 'https://test.memlist.se/api/v3';
  Vue.axios.defaults.baseURL = 'http://46.17.248.20/:8000/api/';
} else {
  Vue.axios.defaults.baseURL = 'http://46.17.248.20/:8000/api/';
}
new Vue({
  render: h => h(App)
}).$mount('#app')
