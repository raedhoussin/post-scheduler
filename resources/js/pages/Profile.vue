<template>
  <div class="d-flex min-vh-100 bg-light">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main content -->
    <div class="container p-4" style="max-width: 600px;">
      <div class="bg-white rounded shadow p-4">
        <h1 class="h4 fw-bold mb-4">👤 My Profile</h1>

        <form @submit.prevent="updateProfile">
          <div class="mb-3">
            <label for="name" class="form-label text-secondary fw-semibold">Name:</label>
            <input
              v-model="form.name"
              type="text"
              id="name"
              class="form-control"
              required
              maxlength="255"
            />
          </div>

          <div class="mb-3">
            <label for="email" class="form-label text-secondary fw-semibold">Email:</label>
            <input
              v-model="form.email"
              type="email"
              id="email"
              class="form-control"
              required
              maxlength="255"
            />
          </div>

          <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>

        <p v-if="successMessage" class="text-success mt-3">{{ successMessage }}</p>
        <p v-if="errorMessage" class="text-danger mt-3">{{ errorMessage }}</p>

        <router-link to="/dashboard" class="text-primary small text-decoration-underline mt-4 d-block">
          ← Back to Dashboard
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, watch } from 'vue';
import Sidebar from '@/components/AppSidebar.vue';
import { useAuth } from '../composables/useAuth';
import axios from '@/api';  

const { user, fetchUser } = useAuth();

const form = reactive({
  name: '',
  email: '',
});

const successMessage = ref('');
const errorMessage = ref('');

watch(user, (newUser) => {
  if (newUser) {
    form.name = newUser.name || '';
    form.email = newUser.email || '';
  }
}, { immediate: true });

const updateProfile = async () => {
  successMessage.value = '';
  errorMessage.value = '';

  try {
    const response = await axios.post('/profile', {
      name: form.name,
      email: form.email,
    });
    successMessage.value = 'Profile updated successfully.';
    await fetchUser();
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'An error occurred.';
  }
};
</script>

