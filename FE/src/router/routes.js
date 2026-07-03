const routes = [
  {
    path: '/',
    redirect: '/login',
  },
  {
    path: '/login',
    component: () => import('layouts/MainLayout.vue'),
    children: [{ 
      path: '', 
      component: () => import('pages/LoginPage.vue'),
      name: 'LoginPage'
    }],
    meta: { requiresGuest: true } // Changed to true for guest routes
  },
  {
    path: '/register',
    component: () => import('layouts/MainLayout.vue'),
    children: [{ 
      path: '', 
      component: () => import('pages/RegisterPage.vue'),
      name: 'RegisterPage'
    }],
    meta: { requiresGuest: true } // Changed to true
  },
  {
    path: '/discover',
    component: () => import('layouts/MainLayout.vue'),
    children: [{ 
      path: '', 
      component: () => import('pages/DiscoverPage.vue'),
      name: 'DiscoverPage'
    }],
    meta: { requiresAuth: true }
  },
  {
    path: '/createReel',
    component: () => import('layouts/MainLayout.vue'),
    children: [{ 
      path: '', 
      component: () => import('pages/CreateReelPage.vue'),
      name: 'CreateReelPage'
    }],
    meta: { requiresAuth: true }
  },
  {
    path: '/messages',
    component: () => import('layouts/MainLayout.vue'),
    children: [{ 
      path: '', 
      component: () => import('pages/MessagePage.vue'),
      name: 'MessagePage'
    }],
    meta: { requiresAuth: true }
  },
  {
    path: '/profile',
    component: () => import('layouts/MainLayout.vue'),
    children: [{ 
      path: '', 
      component: () => import('pages/ProfilePage.vue'),
      name: 'ProfilePage'
    }],
    meta: { requiresAuth: true }
  },
  {
    path: '/createPost',
    component: () => import('layouts/MainLayout.vue'),
    children: [{ 
      path: '', 
      component: () => import('pages/CreatePostPage.vue'),
      name: 'CreatePostPage'
    }],
    meta: { requiresAuth: true }
  },
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
    name: 'NotFound'
  }
]

export default routes