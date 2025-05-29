<template>
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">

      <!-- اسم النظام بشكل جذاب -->
      <div class="text-center mb-4">
        <h1 class="display-5 fw-bold text-primary">
          <i class="bi bi-speedometer2 me-2"></i> 
          PostScheduler
        </h1>
        <p class="text-muted fst-italic">Join us and start scheduling!</p>
      </div>

      <h2 class="card-title text-center mb-4 fw-bold">Register</h2>

      <form @submit.prevent="submit" novalidate>
        <div class="mb-3">
          <label for="name" class="form-label">Name</label>
          <input
            v-model="name"
            type="text"
            class="form-control"
            id="name"
            placeholder="Enter your name"
            required
          />
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input
            v-model="email"
            type="email"
            class="form-control"
            id="email"
            placeholder="Enter your email"
            required
          />
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input
            v-model="password"
            type="password"
            class="form-control"
            id="password"
            placeholder="Enter your password"
            required
          />
        </div>
        <div class="mb-3">
          <label for="password_confirmation" class="form-label">Confirm Password</label>
          <input
            v-model="password_confirmation"
            type="password"
            class="form-control"
            id="password_confirmation"
            placeholder="Confirm your password"
            required
          />
        </div>
        <button type="submit" class="btn btn-primary w-100">Register</button>

        <div v-if="error" class="alert alert-danger mt-3" role="alert">
          {{ error }}
        </div>
        <router-link
          to="/login"
          role="button"
          >Login</router-link
        >
      </form>
    </div>
  </div>
</template>

  
  <script setup>
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  import api from '../axios';
  
  const name = ref('');
  const email = ref('');
  const password = ref('');
  const password_confirmation = ref('');
  const error = ref('');
  const router = useRouter();
  
  const submit = async () => {
    error.value = '';
    try {
      const res = await api.post('/auth/register', {
        name: name.value,
        email: email.value,
        password: password.value,
        password_confirmation: password_confirmation.value,
      });
      router.push('/login');
    } catch (err) {
      error.value = err.response?.data?.message || 'Registration failed';
    }
  };
  </script>
  
  <style scoped>

  .btn {
    background-color: #22c55e;
    color: white;
  }
  </style>
  