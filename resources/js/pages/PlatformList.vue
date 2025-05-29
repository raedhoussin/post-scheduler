<template>
      <div class="d-flex min-vh-100">
    <!-- Sidebar -->
    <AppSidebar />
    <div class="container mt-4">
      <h1 class="mb-4">Platforms</h1>
  
      <button class="btn btn-primary mb-3" @click="openCreateModal">➕ Add New Platform</button>
  
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>Name</th>
            <td>Type</td>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="platform in platforms" :key="platform.id">
            <td>{{ platform.name }}</td>
            <td>{{ platform.type }}</td>
            <td>
              <span :class="platform.active ? 'text-success' : 'text-muted'">
                {{ platform.active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td>
              <button class="btn btn-sm btn-warning me-2" @click="openEditModal(platform)">✏️ Edit</button>
              <button class="btn btn-sm btn-danger me-2" @click="deletePlatform(platform.id)">🗑️ Delete</button>
              <button
                class="btn btn-sm"
                :class="platform.active ? 'btn-secondary' : 'btn-success'"
                @click="togglePlatform(platform)"
              >
                {{ platform.active ? 'Deactivate' : 'Activate' }}
              </button>
            </td>
          </tr>
          <tr v-if="platforms.length === 0">
            <td colspan="4" class="text-center text-muted">No platforms available.</td>
          </tr>
        </tbody>
      </table>
  
      <PlatformFormModal
        v-if="showModal"
        :platform="editPlatform"
        @close="closeModal"
        @saved="handleSave"
      />
    </div>
      </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import axios from '@/api';
  import PlatformFormModal from '@/components/PlatformFormModal.vue';
  import AppSidebar from '../components/AppSidebar.vue'; //   
  
  const platforms = ref([]);
  const showModal = ref(false);
  const editPlatform = ref(null);
  
  const fetchPlatforms = async () => {
    try {
      const res = await axios.get('/platforms');
      platforms.value = res.data;
    } catch (error) {
      console.error('Failed to fetch platforms:', error);
    }
  };
  
  const openCreateModal = () => {
    editPlatform.value = null;
    showModal.value = true;
  };
  
  const openEditModal = (platform) => {
    editPlatform.value = { ...platform };
    showModal.value = true;
  };
  
  const closeModal = () => {
    showModal.value = false;
    editPlatform.value = null;
  };
  
  const handleSave = () => {
    fetchPlatforms();
    closeModal();
  };
  
  const deletePlatform = async (id) => {
    if (!confirm('Are you sure you want to delete this platform?')) return;
    try {
      await axios.delete(`/platforms/${id}`);
      fetchPlatforms();
    } catch (error) {
      alert('Failed to delete platform.');
    }
  };
  
  const togglePlatform = async (platform) => {
    try {
      const newStatus = !platform.active;
      await axios.post('/platforms/toggle', {
        platform_id: platform.id,
        enabled: newStatus,
      });
      platform.active = newStatus;    
    } catch (error) {
      alert('Failed to update platform status.');
      console.error('Failed to toggle platform:', error);
    }
  };
  
  onMounted(() => {
    fetchPlatforms();
  });
  </script>
  