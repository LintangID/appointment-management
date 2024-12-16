import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '../views/HomeView.vue';
import LoginView from '../views/LoginView.vue';
import CreateView from '../views/CreateView.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
    },
    {
      path: '/create',
      name: 'create',
      component: CreateView,
    },
  ],
});

let sessionStartTime = null; // Waktu awal sesi
const sessionDuration = 3600000; // Durasi sesi dalam milidetik (1 jam)

router.beforeEach((to, from, next) => {
  // Jika menuju halaman login, abaikan timer
  if (to.path === '/login') {
    return next();
  }

  // Set waktu mulai jika belum diset
  if (!sessionStartTime) {
    sessionStartTime = Date.now();
  }

  // Hitung sisa waktu sesi
  const elapsedTime = Date.now() - sessionStartTime;
  const remainingTime = sessionDuration - elapsedTime;

  if (remainingTime <= 0) {
    // Jika waktu habis, alihkan ke halaman login
    alert('Sesi Anda telah habis! Anda akan diarahkan ke halaman login.');
    sessionStartTime = null; // Reset waktu sesi
    localStorage.removeItem('name');
    localStorage.removeItem('token');
    next('/login');
  } else {
    // Jika masih ada waktu, lanjutkan ke halaman berikutnya
    setTimeout(() => {
      sessionStartTime = null; // Reset waktu sesi
      localStorage.removeItem('name');
      localStorage.removeItem('token');
      router.push('/login'); // Arahkan ke login
    }, remainingTime); //set timeout dengan sisa waktu
    next();
  }
});

export default router;
