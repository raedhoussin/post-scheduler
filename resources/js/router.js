import { createRouter, createWebHistory } from 'vue-router';
import Home from './pages/Home.vue';
import Login from './pages/Login.vue';
import Register from './pages/Register.vue';
import Dashboard from './pages/Dashboard.vue';
import Profile from './pages/Profile.vue';
import PostList from './pages/PostList.vue';
import PlatformList from './pages/PlatformList.vue';

const routes = [
  { path: '/', component: Home },
  { path: '/login', component: Login, meta: { guest: true } },
  { path: '/register', component: Register, meta: { guest: true } },
  { path: '/dashboard', component: Dashboard, meta: { requiresAuth: true } },
  { path: '/profile', component: Profile, meta: { requiresAuth: true } },
  { path: '/posts', component: PostList },

  {
    path: '/posts/create',
    name: 'PostEditor',
    component: () => import('./pages/PostEditor.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/platforms',
    name: 'PlatformList',
    component: PlatformList,
    meta: { requiresAuth: true },
  },
  {
    path: '/activity-log',
    name: 'ActivityLog',
    component: () => import('./pages/ActivityLog.vue'),
    meta: { requiresAuth: true },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  const isAuthenticated = !!token;

  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login');
  } else if (to.meta.guest && isAuthenticated) {
    next('/dashboard');
  } else {
    next();
  }
});

export default router;
