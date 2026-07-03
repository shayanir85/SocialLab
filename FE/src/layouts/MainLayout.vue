<template>
  <q-layout class="shadow-2 rounded-borders" view="hHh lpR fFf">
    <q-header
      elevated
      :class="$q.dark.isActive ? 'bg-secondary' : 'bg-black'"
      reveal
      bordered
      class="bg-primary text-white"
    >
      <q-toolbar>
        <q-btn flat @click="drawer = !drawer" round dense icon="menu" />
        <q-toolbar-title>
          <q-drawer
            v-model="drawer"
            show-if-above
            overlay
            :width="250"
            :breakpoint="500"
            bordered
            :class="$q.dark.isActive ? 'bg-grey-3' : 'bg-grey-9'"
          >
            <q-scroll-area class="fit">
              <q-list>
                <template v-for="(menuItem, index) in menuList" :key="index">
                  <q-item clickable @click="navigateTo(menuItem.route)" v-ripple>
                    <q-item-section avatar>
                      <q-icon :name="menuItem.icon" />
                    </q-item-section>
                    <q-item-section>
                      {{ menuItem.label }}
                    </q-item-section>
                  </q-item>
                  <q-separator :key="'sep' + index" v-if="menuItem.separator" />
                </template>
              </q-list>
            </q-scroll-area>
          </q-drawer>
          <q-avatar>
            <img src="../assets/logo-dark.svg" />
          </q-avatar>
          {{ user?.name || "Shayan-Iranpour" }}
        </q-toolbar-title>
      </q-toolbar>
    </q-header>

    <q-page-container>
      <router-view />
    </q-page-container>

    <q-footer reveal bordered class="bg-grey-8 text-white">
      <q-toolbar>
        <q-toolbar-title>
          <q-avatar>
            <img src="../assets/logo-dark.svg" />
          </q-avatar>
          Shayan-Iranpour
        </q-toolbar-title>
      </q-toolbar>
    </q-footer>
  </q-layout>
</template>

<script setup>
import { api } from "src/boot/axios.js";
import { useRouter, useRoute } from "vue-router";
import { ref, onMounted } from "vue";

const drawer = ref(true);
const router = useRouter();
const route = useRoute();
const user = ref(null);

const menuList = ref([
  {
    label: "Discover",
    icon: "explore",
    route: "/discover",
    separator: true,
  },
  {
    label: "Create Post",
    icon: "post_add",
    route: "/createPost",
    separator: true,
  },
  {
    label: "Create Reel",
    icon: "play_circle",
    route: "/createReel",
    separator: true,
  },
  {
    label: "Messages",
    icon: "message",
    route: "/messages",
    separator: true,
  },
  {
    label: "Profile",
    icon: "person",
    route: "/profile",
    separator: true,
  },
  {
    label: "Logout",
    icon: "logout",
    route: "/logout",
    separator: true,
  },
]);

const navigateTo = (routePath) => {
  if (routePath === '/logout') {
    localStorage.removeItem('token');
    router.push('/login');
    return;
  }
  router.push(routePath);
};

onMounted(() => {
  const token = localStorage.getItem('token');
  
  // If no token and not on login/register, redirect to login
  if (!token && route.path !== '/login' && route.path !== '/register') {
    router.push('/login');
    return;
  }

  // If token exists and on login/register, let the router guard handle it
  // No need to manually redirect here
  if (token) {
    api
      .get("user")
      .then((r) => {
        if (r.data.id && r.data.name) {
          user.value = r.data;
        }
      })
      .catch((e) => {
        console.log('Error fetching user:', e);
        localStorage.removeItem('token');
        router.push('/login');
      });
  }
});
</script>