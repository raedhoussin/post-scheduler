<template>
  <!-- Sidebar Header -->
  <div class="bg-dark text-white p-4 vh-100 position-sticky top-0" style="width: 220px;">

    <h4 class="mb-4">📊 Dashboard</h4>
    <nav class="nav flex-column">
      <router-link to="/profile" class="nav-link text-white">👤 Profile</router-link>
      <router-link to="/posts/create" class="nav-link text-white">➕ Create Post</router-link>
      <router-link to="/posts" class="nav-link text-white">📄 View Posts</router-link>
      <router-link to="/platforms" class="nav-link text-white">🧩 Manage Platforms</router-link>
      <router-link to="/activity-log" class="nav-link text-white">📝 Activity Logs</router-link>
      <button @click="logout" class="btn btn-danger mt-4">Logout</button>
    </nav>
  </div>
</template>

<script setup>
import { useAuth } from '../composables/useAuth';

const { user, logout } = useAuth();
</script>
