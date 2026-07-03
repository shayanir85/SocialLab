<template>
  <q-page class="flex flex-center">
    <div class="q-pa-md" style="max-width: 800px">
      <q-form @submit="onSubmit" @reset="onReset" class="q-gutter-md">
        <q-input
          outlined
          v-model="email"
          label="Email"
          style="max-width: 400px; width: 100%"
          lazy-rules
          :rules="[
            (val) =>
              (val && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) ||
              'Please enter a valid email address',
          ]"
        />

        <q-input
          outlined
          type="password"
          v-model="password"
          label="Password"
          style="max-width: 400px; width: 100%"
          lazy-rules
          :rules="[
            (val) =>
              val && val.length > 8 ? true : 'Password must be more than 8 characters',
          ]"
        />

        <q-toggle v-model="Remember" label="Remember Me" />

        <div>
          <q-btn label="Submit" type="submit" outline color="primary" />
          <q-btn label="Reset" type="reset" outline color="secondary" class="q-ml-sm" />
        </div>
        <div>
          <q-btn flat to="/register" color="red">Don't have an account? Register</q-btn>
        </div>
      </q-form>
    </div>
  </q-page>
</template>
<script setup>
// import { useRouter } from 'vue-router'
import { useRouter } from 'vue-router'
import { api } from "src/boot/axios.js";
import { ref } from "vue";

const email = ref();
const router = useRouter();
const password = ref();
const Remember = ref();

function onReset() {
  email.value = null;
  password.value = null;
  Remember.value = null;
}

function onSubmit() {
  api
    .post("/user/login", {
      email: email.value,
      password: password.value,
    })
    .then((response) => {
      console.log(response.data);
      if(response.data.success) {
        router.push("/discover");
      }
      localStorage.setItem("token", response.data.data.token);
    })
    .catch((error) => {
      console.error(error);
    });
}
</script>
