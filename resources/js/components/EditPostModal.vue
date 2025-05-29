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
          <form @submit.prevent="$emit('update')">
            <div class="mb-3">
              <label class="form-label">Title</label>
              <input v-model="post.title" type="text" class="form-control" required />
            </div>

            <div class="mb-3">
              <label class="form-label">Content</label>
              <textarea v-model="post.content" class="form-control" rows="4" required></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">Status</label>
              <select v-model="post.status" class="form-select">
                <option value="draft">Draft</option>
                <option value="scheduled">Scheduled</option>
                <option value="published">Published</option>
              </select>
            </div>

            <div class="mb-3" v-if="post.status === 'scheduled'">
              <label class="form-label">Scheduled At</label>
              <input v-model="post.scheduled_at" type="datetime-local" class="form-control" />
            </div>

            <div class="mb-3">
              <label class="form-label">Platforms</label>
              <select v-model="post.platforms" multiple class="form-select">
                <option v-for="platform in platforms" :key="platform.id" :value="platform.id">
                  {{ platform.name }}
                </option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Image</label>
              <input type="file" class="form-control" @change="onImageChange" accept="image/*" />
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
const props = defineProps({
  post: Object,
  platforms: Array,
  previewImage: String,
});

const emit = defineEmits(['close', 'update', 'update-image']);

function onImageChange(e) {
  const file = e.target.files[0];
  if (file) emit('update-image', file);
}
</script>
