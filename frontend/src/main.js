import './assets/main.css'

import {createApp} from 'vue'
import {createPinia} from 'pinia'

import App from './App.vue'
import router from './router'
import {useToast} from './composables/useToast';
import Toast from './components/Toast.vue';
import {API_URL} from "@/config/index.js";
const app = createApp(App)

// Регистрация глобальных компонентов
app.component('Toast', Toast);

// Инициализация тост-уведомлений
const {toasts} = useToast();

app.config.globalProperties.$toasts = toasts;

// Установка HTTP-заголовков
import axios from 'axios';

axios.defaults.withCredentials = true;
axios.defaults.baseURL = API_URL;
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['Content-Type'] = 'application/json';

app.use(createPinia())
app.use(router)

app.mount('#app')
