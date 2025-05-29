<template>
  <div class="d-flex">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main content -->
    <div class="container-fluid p-4" style="max-width: 900px;">
      <div class="card shadow-sm">
        <div class="card-body">
          <h2 class="card-title mb-4">
            {{ isEdit ? '✏️ Edit Post' : '📝 Create a New Post' }}
          </h2>

          <form @submit.prevent="submitPost" novalidate>
            <!-- Title -->
            <div class="mb-3">
              <label for="title" class="form-label fw-semibold">Title</label>
              <input
                id="title"
                v-model="form.title"
                type="text"
                class="form-control"
                placeholder="Enter post title"
                required
              />
            </div>

            <!-- Content -->
            <div class="mb-3">
              <label for="content" class="form-label fw-semibold">Content</label>
              <textarea
                id="content"
                v-model="form.content"
                @input="updateCharCount"
                rows="5"
                class="form-control"
                placeholder="Write something..."
                required
              ></textarea>
              <div class="form-text">Characters: {{ charCount }}</div>
            </div>

            <!-- Image Upload -->
            <div class="mb-3">
              <label for="image" class="form-label fw-semibold">Image</label>
              <input
                id="image"
                type="file"
                accept="image/*"
                class="form-control"
                @change="handleImageUpload"
              />
              <div v-if="previewImage" class="mt-3">
                <img
                  :src="previewImage"
                  class="img-fluid rounded shadow-sm border"
                  style="max-height: 256px; width: 100%; object-fit: cover;"
                  alt="Preview"
                />
              </div>
            </div>

            <!-- Platform Selection -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Select Platforms</label>
              <div class="d-flex flex-wrap gap-3 mt-2">
                <div
                  v-for="platform in platforms"
                  :key="platform.id"
                  class="form-check"
                >
                  <input
                    class="form-check-input"
                    type="checkbox"
                    :id="'platform-' + platform.id"
                    :value="platform.id"
                    v-model="form.platforms"
                  />
                  <label
                    class="form-check-label"
                    :for="'platform-' + platform.id"
                  >
                    {{ platform.name }}
                  </label>
                </div>
              </div>
            </div>

            <!-- Status -->
            <div class="mb-3">
              <label for="status" class="form-label fw-semibold">Status</label>
              <select
                id="status"
                v-model="form.status"
                class="form-select"
                required
              >
                <option value="draft">Draft</option>
                <option value="scheduled">Scheduled</option>
                <option value="published">Published</option>
              </select>
            </div>

            <!-- Schedule Time (only if scheduled) -->
            <div class="mb-3" v-if="form.status === 'scheduled'">
              <label for="scheduled_at" class="form-label fw-semibold">
                Schedule Time
              </label>
              <input
                id="scheduled_at"
                v-model="form.scheduled_at"
                type="datetime-local"
                class="form-control"
                required
              />
            </div>

            <!-- Error Message -->
            <div v-if="error" class="alert alert-danger">
              {{ error }}
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
              {{ isEdit ? '💾 Update Post' : '📤 Submit Post' }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from '@/api';

import Sidebar from '@/components/AppSidebar.vue';

const route = useRoute();
const router = useRouter();

const isEdit = !!route.params.id;

const form = ref({
  title: '',
  content: '',
  image: null,
  platforms: [],
  scheduled_at: '',
  status: 'draft',
});

const previewImage = ref(null);
const charCount = ref(0);
const error = ref('');
const platforms = ref([]);

const updateCharCount = () => {
  charCount.value = form.value.content.length;
};

const handleImageUpload = (event) => {
  const file = event.target.files[0];
  form.value.image = file;

  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      previewImage.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

onMounted(async () => {
  try {
    const response = await axios.get('/platforms');
    platforms.value = response.data;

    if (isEdit) {
      const post = await axios.get(`/posts/${route.params.id}`);
      form.value.title = post.data.title;
      form.value.content = post.data.content;
      form.value.status = post.data.status;
      form.value.scheduled_at = post.data.scheduled_at || '';
      form.value.platforms = post.data.platforms?.map((p) => p.id) || [];
      if (post.data.image_url) {
        previewImage.value = post.data.image_url;
      }
      updateCharCount();
    }
  } catch {
    platforms.value = [];
  }
});

const submitPost = async () => {
  error.value = '';

  if (form.value.status === 'scheduled' && !form.value.scheduled_at) {
    error.value = 'Please provide a schedule time';
    return;
  }

  try {
    const formData = new FormData();
    formData.append('title', form.value.title);
    formData.append('content', form.value.content);
    formData.append('status', form.value.status);
    if (form.value.status === 'scheduled') {
      formData.append('scheduled_at', form.value.scheduled_at);
    }

    form.value.platforms.forEach((id) => formData.append('platforms[]', id));
    if (form.value.image instanceof File) {
      formData.append('image', form.value.image);
    }

    const url = isEdit ? `/posts/${route.params.id}` : '/posts';
    if (isEdit) {
      formData.append('_method', 'PUT');
    }

    await axios.post(url, formData);
    alert(`✅ Post ${isEdit ? 'updated' : 'created'} successfully!`);
    router.push('/posts');
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to submit post.';
  }
};
</script>

