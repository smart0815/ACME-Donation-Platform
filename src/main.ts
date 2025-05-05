import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import './style.css';

// Create the app instance
const app = createApp(App);

// Initialize Pinia store
const pinia = createPinia();

// Add plugins
app.use(pinia);
app.use(router);

// Mount the app to the container
app.mount('#app');

// Debug mounting
if (import.meta.env.DEV) {
  console.log('Vue app mounted successfully');
}