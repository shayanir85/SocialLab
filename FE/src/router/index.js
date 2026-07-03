import { defineRouter } from '#q-app/wrappers'
import { createRouter, createMemoryHistory, createWebHistory, createWebHashHistory } from 'vue-router'
import routes from './routes'

export default defineRouter((/* { store, ssrContext } */) => {
  const createHistory = process.env.SERVER
    ? createMemoryHistory
    : (process.env.VUE_ROUTER_MODE === 'history' ? createWebHistory : createWebHashHistory)

  const Router = createRouter({
    scrollBehavior: () => ({ left: 0, top: 0 }),
    routes,
    history: createHistory(process.env.VUE_ROUTER_BASE)
  })

  // Add navigation guards
  Router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token')
    const isAuthenticated = !!token

    // Check if route requires authentication
    if (to.meta.requiresAuth && !isAuthenticated) {
      next('/login')
      return
    }

    // Check if route is for guests only (login/register)
    if (to.meta.requiresGuest && isAuthenticated) {
      next('/discover')
      return
    }

    // Allow navigation
    next()
  })

  return Router
})