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
                  <q-item clickable :active="menuItem.label === 'Outbox'" v-ripple>
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
          {{ user?.name || "Loading..." }}
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
import { useRouter } from "vue-router";
import { ref } from "vue";

const drawer = ref(false);
const menuList = ref([
  {
    label: 'Discover',
    icon: 'explore',
    route: '/discover',
    separator: false
  },
  {
    label: 'Create Post',
    icon: 'post_add',
    route: '/createPost',
    separator: false
  },
  {
    label: 'Create Reel',
    icon: 'play_circle',
    route: '/createReel',
    separator: false
  },
  {
    label: 'Messages',
    icon: 'message',
    route: '/messages',
    separator: true  // Separator after this item
  },
  {
    label: 'Profile',
    icon: 'person',
    route: '/profile',
    separator: false
  },
  {
    label: 'Logout',
    icon: 'logout',
    route: '/logout',
    separator: false
  }
]);

const router = useRouter();
const user = ref({ name: "" });
api
  .get("user")
  .then((r) => {
    if (r.data.id && r.data.name) {
      user.value = r.data;
      router.push("/discover");
    }
  })
  .catch((e) => {
    console.log(e);
  });
</script>
