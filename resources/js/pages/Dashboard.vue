<template>
  <div class="d-flex min-vh-100">
    <!-- Sidebar -->
    <AppSidebar />

    <!-- Main Dashboard Content -->
    <div class="flex-grow-1 p-5">
      <div class="container">
        <div class="card shadow-sm">
          <div class="card-body">
            <h2 class="card-title mb-4">👋 Welcome, {{ user?.name }}</h2>
            <p class="card-text text-muted">
              This is your dashboard. Here you'll see your scheduled posts and activity.
            </p>

            <div class="alert alert-info mt-4">
              Use the sidebar to navigate between different features.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuth } from '../composables/useAuth';
import AppSidebar from '../components/AppSidebar.vue'; //    

const { user, logout } = useAuth();
</script>
