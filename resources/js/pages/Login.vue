<template>
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
      
      <div class="text-center mb-4">
        <h1 class="display-5 fw-bold text-primary">
          <i class="bi bi-speedometer2 me-2"></i> 
          PostScheduler
        </h1>
        <p class="text-muted fst-italic">Manage your posts effortlessly</p>
      </div>

      <h2 class="card-title text-center mb-4 fw-bold">Login</h2>
      
      <form @submit.prevent="submit" novalidate>
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
        <router-link
          to="/register"
          role="button"
          >Register</router-link
        >
        <button type="submit" class="btn btn-primary w-100">Login</button>
        <div v-if="error" class="alert alert-danger mt-3" role="alert">
          {{ error }}
        </div>
      </form>
    </div>
  </div>
</template>


  <script setup>
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  import axios from 'axios';
  import { useAuth } from '../composables/useAuth';
  
  const email = ref('');
  const password = ref('');
  const error = ref('');
  const router = useRouter();
  
  const submit = async () => {
    error.value = '';
  
    try {
      const response = await axios.post('/api/auth/login', {
        email: email.value,
        password: password.value,
      });
  
      const { setUser } = useAuth();
      setUser(response.data.user, response.data.token);  
  
      router.push('/dashboard');
    } catch (err) {
      error.value = err.response?.data?.message || 'Login failed';
    }
  };
  </script>
  