<template>
  <!-- Bootstrap Modal Backdrop -->
  <div class="modal d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <!-- Close Button -->
        <div class="modal-header">
          <h5 class="modal-title">Edit Post</h5>
          <button type="button" class="btn-close" @click="$emit('close')"></button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
          <form @submit.prevent="submit">
            <div class="mb-3">
              <label class="form-label">Title</label>
              <input v-model="localPost.title" type="text" class="form-control" required />
            </div>

            <div class="mb-3">
              <label class="form-label">Content</label>
              <textarea v-model="localPost.content" class="form-control" rows="4" required></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">Status</label>
              <select v-model="localPost.status" class="form-select">
                <option value="draft">Draft</option>
                <option value="scheduled">Scheduled</option>
                <option value="published">Published</option>
              </select>
            </div>

            <div class="mb-3" v-if="localPost.status === 'scheduled'">

              <label class="form-label">Scheduled At</label>
              <input v-model="localPost.scheduled_at" type="datetime-local" class="form-control" />
            </div>

            <div class="mb-3">
              <label class="form-label">Platforms</label>
              <select v-model="selectedPlatformIds" multiple class="form-select">
                  <option
                    v-for="platform in platforms"
                    :key="platform.id"
                    :value="platform.id"
                  >
                    {{ platform.name }}
                  </option>
                </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Image</label>
              <input type="file" class="form-control" @change="onImageChange" accept="image/*" />
            </div>

           <div v-if="!previewImage && post.image_url" class="mb-3">
          <img :src="post.image_url" class="img-thumbnail" style="max-height: 200px;" />
        </div>

        <div v-if="previewImage" class="mb-3">
          <img :src="previewImage" class="img-thumbnail" style="max-height: 200px;" />
        </div>

            <div class="d-flex justify-content-end">
              <button type="submit" class="btn btn-success">
                💾 Save Changes
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import axios from '@/api';

const props = defineProps({
  post: Object,
  platforms: Array,
});

const emit = defineEmits(['close', 'updated']);

const localPost = ref({});
const selectedPlatformIds = ref([]);
const previewImage = ref(null);
const imageFile = ref(null);

// Initialize local data when prop changes
watch(
  () => props.post,
  (newPost) => {
    if (newPost) {
      localPost.value = { ...newPost };
      selectedPlatformIds.value = newPost.platforms?.map(p => p.id) || [];
      previewImage.value = null;
      imageFile.value = null;
    }
  },
  { immediate: true }
);

// Handle image preview and store the file
function onImageChange(e) {
  const file = e.target.files[0];
  if (file) {
    imageFile.value = file;
    previewImage.value = URL.createObjectURL(file);
  }
}

// Handle form submit
async function submit() {
  const formData = new FormData();
  formData.append('title', localPost.value.title || '');
  formData.append('content', localPost.value.content || '');
  formData.append('status', localPost.value.status || '');
  if (localPost.value.status === 'scheduled') {
    formData.append('scheduled_at', localPost.value.scheduled_at || '');
  }
  selectedPlatformIds.value.forEach((id, index) => {
  formData.append(`platforms[${index}]`, id);
});

  if (imageFile.value) {
    formData.append('image', imageFile.value);
  }
  for (let pair of formData.entries()) {
  console.log(pair[0]+ ': ' + pair[1]);
}

  try {
    await axios.put(`/posts/${localPost.value.id}`, {
  title: localPost.value.title,
  content: localPost.value.content,
  status: localPost.value.status,
  scheduled_at: localPost.value.status === 'scheduled' ? localPost.value.scheduled_at : null,
  platforms: selectedPlatformIds.value,
  image : imageFile.value,
});

    emit('updated');
    emit('close');
  } catch (err) {
    alert('Failed to update post.');
    console.error(err);
  }
}
</script>
