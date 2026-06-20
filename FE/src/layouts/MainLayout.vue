<template>
  <q-layout view="hHh lpR fFf">
    <q-header reveal bordered class="bg-primary text-white">
      <q-toolbar>
        <q-toolbar-title>
          <q-avatar>
            <img src="../assets/logo-dark.svg" />
          </q-avatar>
          Instagram-Clone
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
