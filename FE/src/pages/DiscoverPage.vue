<template>
  <q-page padding class="bg-grey-1">
    <!-- Loading State -->
    <div v-if="isLoading" class="flex flex-center" style="min-height: 400px;">
      <q-spinner-dots color="primary" size="3rem" />
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="flex flex-center column q-gutter-md" style="min-height: 400px;">
      <q-icon name="error_outline" color="negative" size="4rem" />
      <div class="text-h6 text-grey-7">{{ error }}</div>
      <q-btn color="primary" label="Retry" @click="fetchData" />
    </div>

    <!-- Content -->
    <template v-else>
      <!-- Tab Headers -->
      <div class="tabs-container q-mb-md">
        <q-tabs
          v-model="activeTab"
          dense
          class="bg-white rounded-borders shadow-2"
          align="justify"
          narrow-indicator
          indicator-color="primary"
          active-color="primary"
          active-bg-color="transparent"
          no-caps
        >
          <q-tab name="posts" icon="grid_on" label="Posts" />
          <q-tab name="reels" icon="play_circle" label="Reels" />
        </q-tabs>
      </div>

      <!-- Tab Panels with Smooth Transition -->
      <div class="tab-content">
        <transition
          enter-active-class="animated fadeIn"
          leave-active-class="animated fadeOut"
          mode="out-in"
          appear
        >
          <div :key="activeTab" class="tab-panel">
            <!-- Posts Tab - Text Only -->
            <div v-if="activeTab === 'posts'" class="posts-grid">
              <div
                v-for="item in posts"
                :key="item.id"
                class="post-item"
                @click="openPost(item)"
              >
                <div style="max-width:100%;" class="text-post-card">
                  <!-- Avatar and Username -->
                  <div class="flex items-center q-mb-sm">
                    <q-avatar size="32px" class="q-mr-sm">
                      <img :src="item.avatar || 'https://ui-avatars.com/api/?name=' + item. username + '&background=random'" />
                    </q-avatar>
                    <div>
                      <div class="text-subtitle2 text-weight-bold">{{ item.username }}</div>
                      <div class="text-caption text-grey-6">{{ formatTime(item.createdAt) }}</div>
                    </div>
                  </div>

                  <!-- Text Content -->
                  <div class="text-content q-mb-sm">
                    <h6 style="margin: 0; padding: 0;" >{{ item.title }}</h6>
                    <p class="text-body2" style="word-wrap: break-word; white-space: pre-wrap;">
                      {{ item.content }}
                    </p>
                  </div>

                  <!-- Tags/Hashtags -->
                  <div v-if="item.tags && item.tags.length" class="tags-container q-mb-sm">
                    <span
                      v-for="tag in item.tags"
                      :key="tag"
                      class="text-caption text-primary q-mr-xs"
                      style="cursor: pointer;"
                      @click.stop="searchTag(tag)"
                    >
                      #{{ tag }}
                    </span>
                  </div>

                  <!-- Engagement Stats -->
                  <div class="flex items-center q-gutter-md q-mt-sm q-pt-sm border-top">
                    <div class="flex items-center q-gutter-sm" style="cursor: pointer;" @click.stop="toggleLike(item)">
                      <q-icon
                        :name="item.is_liked ? 'favorite' : 'favorite_border'"
                        :color="item.is_liked ? 'red' : 'grey-7'"
                        size="sm"
                      />
                      <span class="text-caption">{{ formatNumber(item.likes_count) }}</span>
                    </div>
                    <div class="flex items-center q-gutter-sm" style="cursor: pointer;" @click.stop="openComments(item)">
                      <q-icon name="chat_bubble_outline" size="sm" color="grey-7" />
                      <span class="text-caption">{{ formatNumber(item.comments) }}</span>
                    </div>
                    <div class="flex items-center q-gutter-sm" style="cursor: pointer;" @click.stop="sharePost(item)">
                      <q-icon name="share" size="sm" color="grey-7" />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Empty state for posts -->
              <div v-if="posts.length === 0" class="col-span-full flex flex-center q-py-xl">
                <div class="text-center text-grey-6">
                  <q-icon name="article" size="3rem" />
                  <div class="text-subtitle1 q-mt-sm">No posts yet</div>
                </div>
              </div>
            </div>

            <!-- Reels Tab -->
            <div v-else-if="activeTab === 'reels'" class="reels-grid">
              <div
                v-for="reel in reels"
                :key="reel.id"
                class="reel-item"
                @click="openReel(reel)"
              >
                <q-img
                  :src="reel.thumbnail"
                  :ratio="9/16"
                  fit="cover"
                  class="rounded-borders cursor-pointer"
                  loading="lazy"
                >
                  <!-- Reel overlay with play icon -->
                  <div class="absolute-full flex flex-center">
                    <q-icon
                      name="play_circle"
                      size="3.5rem"
                      color="white"
                      class="reel-play-icon"
                      style="text-shadow: 0 2px 8px rgba(0,0,0,0.6);"
                    />
                  </div>

                  <!-- Bottom info -->
                  <div class="absolute-bottom text-white q-pa-sm bg-gradient">
                    <div class="flex items-center justify-between">
                      <span class="text-caption text-weight-medium">{{ reel.title }}</span>
                      <div class="flex items-center q-gutter-sm">
                        <q-icon name="play_arrow" size="sm" />
                        <span class="text-caption">{{ formatNumber(reel.views) }}</span>
                      </div>
                    </div>
                  </div>

                  <template v-slot:error>
                    <div class="absolute-full flex flex-center bg-grey-3 text-grey-6">
                      <q-icon name="broken_image" size="2rem" />
                    </div>
                  </template>
                </q-img>
              </div>

              <!-- Empty state for reels -->
              <div v-if="reels.length === 0" class="col-span-full flex flex-center q-py-xl">
                <div class="text-center text-grey-6">
                  <q-icon name="play_circle_outline" size="3rem" />
                  <div class="text-subtitle1 q-mt-sm">No reels yet</div>
                </div>
              </div>
            </div>
          </div>
        </transition>
      </div>
    </template>
  </q-page>
</template>

<script setup>
import { api } from 'src/boot/axios.js'
import { ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'

const $q = useQuasar()
const activeTab = ref('posts')
const posts = ref([])
const reels = ref([])
const isLoading = ref(true)
const error = ref(null)

// Fetch data function
const fetchData = async () => {
  isLoading.value = true
  error.value = null
  
  try {
    // Fetch both posts and reels in parallel
    const [postsResponse, reelsResponse] = await Promise.all([
      api.get('posts'),
      api.get('reels')
    ])
    
    posts.value = postsResponse.data || []
    reels.value = reelsResponse.data || []
    
  } catch (err) {
    console.error('Failed to fetch data:', err)
    error.value = err.response?.data?.message || 'Failed to load content. Please try again.'
    
    // Set empty arrays on error
    posts.value = []
    reels.value = []
  } finally {
    isLoading.value = false
  }
}

// Lifecycle - fetch data when component mounts
onMounted(() => {
  fetchData()
})

// Helper functions
const formatNumber = (num) => {
  if (!num) return '0'
  if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M'
  if (num >= 1000) return (num / 1000).toFixed(1) + 'K'
  return num.toString()
}

const formatTime = (timestamp) => {
  if (!timestamp) return ''
  const date = new Date(timestamp)
  const now = new Date()
  const diff = Math.floor((now - date) / 1000)
  
  if (diff < 60) return 'Just now'
  if (diff < 3600) return Math.floor(diff / 60) + 'm'
  if (diff < 86400) return Math.floor(diff / 3600) + 'h'
  if (diff < 604800) return Math.floor(diff / 86400) + 'd'
  return date.toLocaleDateString()
}

const openPost = (post) => {
  console.log('Opening post:', post)
  // Handle post click - navigate to post details or open modal
}

const openReel = (reel) => {
  console.log('Opening reel:', reel)
  // Handle reel click - navigate to reel player or open modal
}
const toggleLike = (post) => {
  // API call to toggle like
  api.post(`/post/Like/${post.id}`)
  .then((response) => {
      console.log(response);      
      post.is_liked = !post.is_liked
      post.likes_count += post.is_liked ? 1 : -1
    })
    .catch((error) => {
      console.error('Failed to toggle like:', error)
      $q.notify({
        message: 'Failed to update like. Please try again.',
        color: 'negative'
      })
    })
}

const openComments = (post) => {
  console.log('Opening comments for post:', post)
  // Open comments dialog or navigate
}

const sharePost = (post) => {
  console.log('Sharing post:', post)
  // Share functionality
  $q.notify({
    message: 'Share functionality coming soon!',
    color: 'info'
  })
}

const searchTag = (tag) => {
  console.log('Searching tag:', tag)
  // Navigate to tag search results
  $q.notify({
    message: `Searching for #${tag}`,
    color: 'info'
  })
}

</script>

<style scoped>
.tabs-container {
  position: sticky;
  top: 0;
  z-index: 10;
}

.tab-content {
  position: relative;
  min-height: 400px;
}

.tab-panel {
  position: relative;
  width: 100%;
}

/* Posts Grid - 2 columns for text posts */
.posts-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

.post-item {
  position: relative;
  transition: transform 0.2s ease;
}

.post-item:hover {
  transform: translateY(-2px);
}

/* Text Post Card Design */
.text-post-card {
  background: white;
  border-radius: 12px;
  padding: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  height: 100%;
  display: flex;
  flex-direction: column;
  transition: box-shadow 0.3s ease;
  cursor: pointer;
}

.text-post-card:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
}

.text-content {
  flex: 1;
}

.text-content p {
  margin: 0;
  line-height: 1.6;
  color: #262626;
}

.tags-container {
  display: flex;
  flex-wrap: wrap;
  gap: 2px;
}

.border-top {
  border-top: 1px solid #efefef;
}

/* Reels Grid - 2 columns with vertical videos */
.reels-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 8px;
}

.reel-item {
  position: relative;
  aspect-ratio: 9 / 16;
  overflow: hidden;
  border-radius: 12px;
  transition: transform 0.2s ease;
}

.reel-item:hover {
  transform: scale(1.02);
  z-index: 2;
}

.reel-item .q-img {
  width: 100%;
  height: 100%;
}

.reel-play-icon {
  opacity: 0.9;
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.reel-item:hover .reel-play-icon {
  transform: scale(1.1);
  opacity: 1;
}

/* Gradient overlay for better text visibility */
.bg-gradient {
  background: linear-gradient(transparent, rgba(0, 0, 0, 0.6) 60%);
}

/* Smooth transitions */
.fadeIn {
  animation-duration: 0.3s;
}

.fadeOut {
  animation-duration: 0.2s;
}

/* Grid full width for empty states */
.col-span-full {
  grid-column: 1 / -1;
}

/* Responsive adjustments */
@media (max-width: 600px) {
  .posts-grid {
    grid-template-columns: 1fr;
    gap: 12px;
  }
  
  .reels-grid {
    gap: 6px;
  }
  
  .reel-play-icon {
    font-size: 2.5rem !important;
  }
}

@media (min-width: 601px) and (max-width: 1024px) {
  .reels-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (min-width: 1025px) {
  .reels-grid {
    grid-template-columns: repeat(4, 1fr);
  }
  
  .posts-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}
</style>