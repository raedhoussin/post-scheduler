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
      activityLogs: [],       // Stores all activity log entries
      flashIndex: null,      // Index of the item to highlight (for new activities)
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
    async fetchActivityLogs() {
      try {
        const res = await axios.get('/activity-logs');
        this.activityLogs = res.data;
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