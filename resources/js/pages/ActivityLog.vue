<template>
  <div class="d-flex min-vh-100">
    <AppSidebar />

    <div class="container mt-4 flex-grow-1">
      <h3 class="mb-4">Activity Log</h3>

      <div class="list-group">
        <div
          v-for="(log, index) in activityLogs"
          :key="log.id"
          :class="['list-group-item', 'list-group-item-action', 'flex-column', 'align-items-start', { 'flash-highlight': flashIndex === index }]"
        >
          <div class="d-flex w-100 justify-content-between">
            <h5 class="mb-1">{{ log.user?.name || 'Unknown User' }}</h5>
            <small class="text-muted">{{ new Date(log.created_at).toLocaleString() }}</small>
          </div>
          <p class="mb-1"><strong>Action:</strong> {{ log.action }}</p>
          <small class="text-muted">{{ log.description }}</small>
        </div>
      </div>
      <nav aria-label="Page navigation" class="mt-3">
  <ul class="pagination justify-content-center">
    <li :class="['page-item', { disabled: pagination.current_page === 1 }]">
      <button class="page-link" @click="fetchActivityLogs(pagination.current_page - 1)" :disabled="pagination.current_page === 1">
        Previous
      </button>
    </li>

    <li
      v-for="page in pagination.last_page"
      :key="page"
      :class="['page-item', { active: page === pagination.current_page }]"
    >
      <button class="page-link" @click="fetchActivityLogs(page)">{{ page }}</button>
    </li>

    <li :class="['page-item', { disabled: pagination.current_page === pagination.last_page }]">
      <button class="page-link" @click="fetchActivityLogs(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page">
        Next
      </button>
    </li>
  </ul>
</nav>

    </div>
  </div>
</template>

<script setup>
// Composables
import { useAuth } from '../composables/useAuth';

// Components
import AppSidebar from '../components/AppSidebar.vue';

// Initialize auth composable
const { user, logout } = useAuth();
</script>

/******************************************************************************/

<script>
import axios from '@/api';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Configure Pusher for real-time functionality
window.Pusher = Pusher;

/**
 * Initialize Laravel Echo for real-time event broadcasting
 */
window.Echo = new Echo({
  broadcaster: 'pusher',
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
  wsHost: import.meta.env.VITE_PUSHER_HOST || window.location.hostname,
  wsPort: import.meta.env.VITE_PUSHER_PORT || 6001,
  wssPort: import.meta.env.VITE_PUSHER_PORT || 6001,
  forceTLS: import.meta.env.VITE_PUSHER_SCHEME === 'https',
  encrypted: true,
  enabledTransports: ['ws', 'wss'],
});

export default {
  name: 'ActivityLog',
  components: { AppSidebar },
  
  data() {
  return {
    activityLogs: [],
    flashIndex: null,
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 50,
      total: 0,
    },
  };
},


  mounted() {
    // Fetch initial activity logs when component mounts
    this.fetchActivityLogs();

    /**
     * Listen for real-time activity events
     */
    window.Echo.channel('user-activities')
      .listen('UserActionOccurred', (e) => {
        // Add new activity to beginning of array
        this.activityLogs.unshift(e.activity);
        
        // Highlight the new activity (index 0 since we added to beginning)
        this.flashIndex = 0;

        // Remove highlight after 3 seconds
        setTimeout(() => {
          this.flashIndex = null;
        }, 3000);
      });
  },

  methods: {
    /**
     * Fetches activity logs from the API
     * @async
     */
     async fetchActivityLogs(page = 1) {
  try {
    const res = await axios.get('/activity-logs', { params: { page } });
    this.activityLogs = res.data.data;  // 
    this.pagination = {
      current_page: res.data.current_page,
      last_page: res.data.last_page,
      per_page: res.data.per_page,
      total: res.data.total,
    };
  } catch (error) {
    console.error('Failed to load activity logs:', error);
  }
},

  },
};
</script>

/******************************************************************************/

<style scoped>
/**
 * Animation for highlighting new activity log entries
 */
.flash-highlight {
  animation: flash-bg 2s ease;
}

@keyframes flash-bg {
  0% {
    background-color: #fff3cd;
  }
  50% {
    background-color: #ffeeba;
  }
  100% {
    background-color: transparent;
  }
}
</style>