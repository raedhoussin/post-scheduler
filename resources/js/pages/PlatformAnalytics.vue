<template>
    <div class="d-flex min-vh-100">
      <AppSidebar />
      <div class="container mt-4">
        <h1 class="mb-4">Platform Analytics</h1>
  
        <div v-if="loading" class="text-center text-muted">Loading analytics...</div>
  
        <table v-else class="table table-bordered table-hover">
          <thead class="table-light">
            <tr>
              <th>Platform</th>
              <th>Type</th>
              <th>Total Posts</th>
              <th>Published</th>
              <th>Scheduled</th>
              <th>Publish Rate</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in analytics" :key="item.platform_id">
              <td>{{ item.platform_name }}</td>
              <td>{{ item.platform_type }}</td>
              <td>{{ item.total_posts }}</td>
              <td>{{ item.published_posts }}</td>
              <td>{{ item.scheduled_posts }}</td>
              <td>{{ item.publish_success_rate }}%</td>
            </tr>
            <tr v-if="analytics.length === 0">
              <td colspan="6" class="text-center text-muted">No analytics data available.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import axios from '@/api';
  import AppSidebar from '@/components/AppSidebar.vue';
  
  const analytics = ref([]);
  const loading = ref(true);
  
  const fetchAnalytics = async () => {
    try {
      const res = await axios.get('/analytics/posts'); //
      analytics.value = res.data;
    } catch (error) {
      console.error('Failed to fetch analytics:', error);
    } finally {
      loading.value = false;
    }
  };
  
  onMounted(() => {
    fetchAnalytics();
  });
  </script>
  