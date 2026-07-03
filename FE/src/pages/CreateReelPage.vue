<template>
  <q-page class="flex flex-center">
    <div class="q-pa-md" style="max-width: 800px">
      <q-form @submit="onSubmit" @reset="onReset" class="q-gutter-md">
        <q-input
          outlined
          type="file"
          v-model="video"
          label="video"
          style="max-width: 400px; width: 100%"
          lazy-rules
          :rules="[(val) => (val && val.length > 0) || 'Title is required']"
        />
        <q-input
          outlined
          v-model="title"
          label="Title"
          style="max-width: 400px; width: 100%"
          lazy-rules
          :rules="[(val) => (val && val.length > 0) || 'Title is required']"
        />

        <q-input
          outlined
          type="textarea"
          v-model="content"
          label="Content"
          style="max-width: 400px; width: 100%"
          lazy-rules
          :rules="[(val) => (val && val.length > 0) || 'Content is required']"
        />

        <div>
          <q-btn label="Submit" type="submit" outline color="primary" />
          <q-btn label="Reset" type="reset" outline color="secondary" class="q-ml-sm" />
        </div>
      </q-form>
    </div>
  </q-page>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { api } from 'src/boot/axios.js'

const router = useRouter()
const title = ref()
const video = ref((File));
const content = ref()

function onReset() {
  video.value = null
  title.value = null
  content.value = null
}

function onSubmit() {
  api
    .post('/reel/create', {
      title: title.value,
      content: content.value,
    })
    .then((response) => {
      console.log(response.data)
      onReset()
      router.push('/discover')
    })
    .catch((error) => {
      console.error(error)
    })
}
</script>
