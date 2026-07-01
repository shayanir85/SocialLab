<template>
  <q-page class="flex flex-center">
    <div class="q-pa-md" style="max-width: 800px">
      <q-form @submit="onSubmit" @reset="onReset" class="q-gutter-md">
        <q-input
          outlined
          v-model="Name"
          label="UserName"
          style="max-width: 400px; width: 100%"
          lazy-rules
        />
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
        <q-input
          outlined
          type="password"
          v-model="password_con"
          label="Confirm Password"
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
        </div>
        <div>
          <q-btn flat to="/login" color="red">Already have an account? Login</q-btn>
        </div>
      </q-form>
    </div>
  </q-page>
</template>
<script setup>
import { api } from "src/boot/axios.js";
import { ref } from "vue";
import {useRouter} from 'vue-router';

const router = useRouter()
const Name = ref();
const email = ref();
const password = ref();
const password_con = ref();
const Remember = ref();

function onReset() {
  Name.value = null;
  email.value = null;
  password.value = null;
  password_con.value = null;
  Remember.value = null;
}

function onSubmit() {
  if (password.value !== password_con.value) {
    console.error("Passwords do not match");
    return;
  }
  api
    .post("/user", {
      name: Name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_con.value,
    })
    .then((response) => {
      console.log(response.data);
      onReset();
      router.push('/login')
    })
    .catch((error) => {
      console.error(error);
    });
}
</script>
