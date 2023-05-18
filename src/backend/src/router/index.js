import { createRouter, createWebHistory } from "vue-router";

import AppLayout from "../components/AppLayout.vue"
import Dashboard from "../views/Dashboard.vue"
import Products from "../views/Products.vue"
import Login from "../views/Login.vue"
import RequestPassword from "../views/RequestPassword.vue"
import ResetPassword from "../views/ResetPassword.vue"
import NotFound from "../views/NotFound.vue"

const routes = [

  {
    path: '/app',
    name: 'app',
    component: AppLayout,
    children: [
      {
        path: 'dashboard',
        name: 'app.dashboard',
        component: Dashboard
      },
      {
        path: 'products',
        name: 'app.products',
        component: Products
      },
    ]
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
  },
  {
    path: '/request-password',
    name: 'requestPassword',
    component: RequestPassword,
  },
  {
    path: '/reset-password/:token',
    name: 'resetPassword',
    component: ResetPassword,
  },
  {
    path: '/:pathMatch(.*)',
    name: 'notfound',
    component: NotFound
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router