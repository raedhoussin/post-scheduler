<template>
  <div class="d-flex">
    <!-- Sidebar Component -->
    <Sidebar />

    <!-- Main Content Area -->
    <div class="container-fluid mt-4" style="max-width: 900px;">
      <h1 class="mb-4">Posts</h1>

      <!-- Search and Filter Form -->
      <form @submit.prevent="onSearch" class="row g-3 align-items-end mb-4">
        <div class="col-auto">
          <label class="form-label">Status</label>
          <select v-model="filters.status" class="form-select" style="width: 160px;">
            <option value="">All</option>
            <option value="draft">Draft</option>
            <option value="scheduled">Scheduled</option>
            <option value="published">Published</option>
          </select>
        </div>

        <div class="col-auto">
          <label class="form-label">From Date</label>
          <input type="date" v-model="filters.from_date" class="form-control" />
        </div>

        <div class="col-auto">
          <label class="form-label">To Date</label>
          <input type="date" v-model="filters.to_date" class="form-control" />
        </div>

        <div class="col-auto">
          <button type="submit" class="btn btn-primary px-4">Search</button>
        </div>
      </form>

      <!-- Empty State -->
      <div v-if="posts.length === 0" class="text-center text-muted py-5">
        No posts found.
      </div>

      <!-- Posts List -->
      <div v-for="post in posts" :key="post.id" class="card mb-3 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">{{ post.title }}</h5>
          <p class="card-text">{{ post.content }}</p>

          <!-- Post Status -->
          <p>
            <strong>Status:</strong>
            <span
              :class="{
                'text-warning': post.status === 'draft',
                'text-primary': post.status === 'scheduled',
                'text-success': post.status === 'published'
              }"
            >
              {{ post.status }}
            </span>
          </p>

          <!-- Scheduled Date -->
          <p v-if="post.scheduled_at">
            <strong>Scheduled At:</strong> {{ formatDate(post.scheduled_at) }}
          </p>

          <!-- Platforms -->
          <div class="mb-2">
            <strong>Platforms:</strong>
            <span v-if="post.platforms && post.platforms.length">
              <span
                v-for="platform in post.platforms"
                :key="platform.id"
                class="badge bg-info text-dark me-1"
              >
                {{ platform.name }}
              </span>
            </span>
            <span v-else>None</span>
          </div>

          <!-- Post Image -->
          <div v-if="post.image_url" class="mb-3">
            <img :src="post.image_url" alt="Post Image" class="img-fluid rounded" style="max-height: 200px; object-fit: cover;" />
          </div>

          <!-- Action Buttons -->
          <div class="d-flex gap-2">
            <button @click="openEditModal(post)" class="btn btn-warning btn-sm">
              ✏️ Edit
            </button>
            <button @click="deletePost(post.id)" class="btn btn-danger btn-sm">
              🗑️ Delete
            </button>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <nav v-if="lastPage > 1" aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
          <li :class="['page-item', { disabled: currentPage === 1 }]">
            <button class="page-link" @click="goToPage(currentPage - 1)" :disabled="currentPage === 1">Previous</button>
          </li>

          <li class="page-item disabled">
            <span class="page-link">{{ currentPage }} / {{ lastPage }}</span>
          </li>

          <li :class="['page-item', { disabled: currentPage === lastPage }]">
            <button class="page-link" @click="goToPage(currentPage + 1)" :disabled="currentPage === lastPage">Next</button>
          </li>
        </ul>
      </nav>
    </div>

    <!-- Edit Post Modal -->
    <EditPostModal
      v-if="showModal"
      :post="editPost"
      :platforms="platforms"
      @close="closeModal"
      @updated="handleUpdate"
    />
  </div>
</template>

/******************************************************************************/

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from '@/api';

// Components
import Sidebar from '@/components/AppSidebar.vue';
import EditPostModal from '@/components/EditPostModal.vue';

// Reactive state
const currentPage = ref(1);
const lastPage = ref(1);
const posts = ref([]);
const platforms = ref([]);
const showModal = ref(false);
const editPost = ref(null);

// Filter options
const filters = reactive({
  status: '',
  from_date: '',
  to_date: '',
});

/******************************************************************************/

/**
 * Fetches posts from API with optional pagination
 * @param {number} page - Page number to fetch
 */
const fetchPosts = async (page = 1) => {
  try {
    const params = {
      ...filters,
      page,
    };
    const response = await axios.get('/posts', { params });
    posts.value = response.data.data;
    currentPage.value = response.data.current_page;
    lastPage.value = response.data.last_page;
  } catch (error) {
    console.error('Failed to fetch posts:', error);
  }
};

/******************************************************************************/

/**
 * Navigates to specific page in pagination
 * @param {number} page - Page number to navigate to
 */
const goToPage = (page) => {
  if (page >= 1 && page <= lastPage.value) {
    fetchPosts(page);
  }
};

/**
 * Handles search form submission
 */
const onSearch = () => {
  currentPage.value = 1;
  fetchPosts(1);
};

/******************************************************************************/

/**
 * Fetches available platforms from API
 */
const fetchPlatforms = async () => {
  try {
    const res = await axios.get('/platforms');
    platforms.value = res.data;
  } catch {
    platforms.value = [];
  }
};

/******************************************************************************/

/**
 * Opens edit modal with selected post data
 * @param {Object} post - Post object to edit
 */
const openEditModal = (post) => {
  editPost.value = post;
  showModal.value = true;
};

/**
 * Closes the edit modal
 */
const closeModal = () => {
  showModal.value = false;
  editPost.value = null;
};

/**
 * Handles post update event from modal
 */
const handleUpdate = () => {
  fetchPosts(); // Refresh posts list
};

/******************************************************************************/

/**
 * Deletes a post after confirmation
 * @param {number} id - ID of post to delete
 */
const deletePost = async (id) => {
  if (!confirm('Are you sure you want to delete this post?')) return;
  try {
    await axios.delete(`/posts/${id}`);
    fetchPosts(); // Refresh posts list
  } catch (error) {
    alert('Failed to delete post.');
  }
};

/******************************************************************************/

/**
 * Formats datetime string to localized format
 * @param {string} datetime - ISO datetime string
 * @returns {string} Formatted date string
 */
const formatDate = (datetime) => {
  return new Date(datetime).toLocaleString();
};

// Lifecycle hook - fetch initial data when component mounts
onMounted(() => {
  fetchPlatforms();
  fetchPosts();
});
</script>