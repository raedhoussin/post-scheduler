<template>
  <div class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog">
      <form @submit.prevent="savePlatform" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ isEditMode ? 'Edit Platform' : 'Create New Platform' }}</h5>
          <button type="button" class="btn-close" aria-label="Close" @click="$emit('close')"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="name" class="form-label">Platform Name</label>
            <input
              id="name"
              type="text"
              v-model="form.name"
              class="form-control"
              :class="{ 'is-invalid': errors.name }"
              required
            />
            <div class="invalid-feedback" v-if="errors.name">{{ errors.name }}</div>
          </div>
          <div class="mb-3">
          <label for="type" class="form-label">Platform Type</label>
          <select
            id="type"
            v-model="form.type"
            class="form-select"
            :class="{ 'is-invalid': errors.type }"
          >
            <option value="">Select type</option>
            <option value="twitter">Twitter</option>
            <option value="instagram">Instagram</option>
            <option value="linkedin">LinkedIn</option>
          </select>
          <div v-if="errors.type" class="invalid-feedback">
            {{ errors.type }}
          </div>
        </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="$emit('close')">Cancel</button>
          <button type="submit" class="btn btn-primary" :disabled="loading">
            {{ loading ? 'Saving...' : (isEditMode ? 'Update' : 'Create') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch, ref, computed } from 'vue';
import axios from '@/api';

// Define component props
const props = defineProps({
  platform: {
    type: Object,
    default: null,
  },
});

// Define component emits
const emit = defineEmits(['close', 'saved']);

// Reactive form data
const form = reactive({
  name: '',
  type: '',
});

// Reactive error handling
const errors = reactive({
  name: null,
  type: null,
});

// Loading state for form submission
const loading = ref(false);

// Computed property to check if in edit mode
const isEditMode = computed(() => !!props.platform);

// Helper function to clear validation errors
const clearErrors = () => {
  errors.name = null;
  errors.type = null;
};

// Watch for changes in platform prop to populate form in edit mode
watch(
  () => props.platform,
  (newVal) => {
    if (newVal) {
      form.name = newVal.name || '';
      form.type = newVal.type || '';
      clearErrors();
    } else {
      form.name = '';
      form.type = '';
      clearErrors();
    }
  },
  { immediate: true }
);

/******************************************************************************/

/**
 * Validates form fields
 * @returns {boolean} Returns true if form is valid, false otherwise
 */
const validate = () => {
  clearErrors();
  let valid = true;
  
  if (!form.name.trim()) {
    errors.name = 'Platform name is required';
    valid = false;
  }
  
  if (!form.type.trim()) {
    errors.type = 'Platform type is required';
    valid = false;
  }
  
  return valid;
};

/******************************************************************************/

/**
 * Handles form submission for both create and update operations
 * @async
 */
const savePlatform = async () => {
  if (!validate()) return;

  loading.value = true;

  try {
    if (isEditMode.value) {
      // Update existing platform
      await axios.put(`/platforms/${props.platform.id}`, {
        name: form.name,
        type: form.type,
      });
    } else {
      // Create new platform
      await axios.post('/platforms', {
        name: form.name,
        type: form.type,
      });
    }
    emit('saved');
  } catch (error) {
    // Handle server-side validation errors
    if (error.response && error.response.data && error.response.data.errors) {
      const serverErrors = error.response.data.errors;
      errors.name = serverErrors.name ? serverErrors.name[0] : null;
      errors.type = serverErrors.type ? serverErrors.type[0] : null;
    } else {
      alert('Failed to save platform.');
    }
  } finally {
    loading.value = false;
  }
};
</script>

/******************************************************************************/

<style scoped>
.modal {
  background: rgba(0, 0, 0, 0.5);
}
</style>