<template>
  <div 
    class="d-flex flex-column justify-content-center align-items-center min-vh-100 text-white px-3"
    style="background: linear-gradient(135deg, #4a90e2, #9013fe);"
  >
    <div class="text-center mb-5" style="max-width: 700px;">
      <h1 class="display-3 fw-bold text-shadow-lg mb-3">
        PostScheduler
      </h1>
      <p class="lead text-shadow-sm fs-5 mx-auto" style="max-width: 600px;">
        Schedule and manage your social media posts effortlessly across platforms like Twitter, Instagram, and LinkedIn.
      </p>
    </div>

    <div class="mb-5">
      <template v-if="!isLoggedIn">
        <router-link
          to="/login"
          class="btn btn-light btn-lg me-3 shadow"
          role="button"
          >Login</router-link
        >
        <router-link
          to="/register"
          class="btn btn-outline-light btn-lg shadow"
          role="button"
          >Register</router-link
        >
      </template>
      <template v-else>
        <router-link
          to="/dashboard"
          class="btn btn-light btn-lg me-3 shadow"
          role="button"
          >Go to Dashboard</router-link
        >
        <router-link
          to="/profile"
          class="btn btn-light btn-lg shadow"
          role="button"
          >Go to Profile</router-link
        >
      </template>
    </div>

    <div 
      class="card bg-white text-dark shadow p-4 rounded" 
      style="max-width: 350px; width: 100%;"
    >
      <h5 class="card-title mb-3">Laravel Developer </h5>
      <p class="mb-2"><strong>Name</strong> Raed Houssin </p>
      <p class="mb-2"><strong>Mob</strong> +20 101 161 3429</p>
      <p class="mb-0">
        <strong>LlinkedIn</strong>
        <a 
          href="https://www.linkedin.com/in/raed-houssin-392564119/" 
          target="_blank" 
          rel="noopener noreferrer"
          class="text-primary text-decoration-none"
        >
        ReedHoussin-LinkedIn Account 
        </a>
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const isLoggedIn = computed(() => {
  return !!localStorage.getItem("token");
});
</script>
<style scoped>
.text-shadow-lg {
  text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.6);
}
.text-shadow-sm {
  text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.5);
}

.btn:hover {
  transform: scale(1.05);
  transition: transform 0.3s ease;
}
</style>



