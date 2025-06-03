<template>
  <v-container fluid>
    <v-card>
      <v-card-title class="d-flex justify-space-between align-center">
        <span class="text-h4">My Event Reservations</span>
        <v-btn
          color="primary"
          prepend-icon="mdi-plus"
          @click="openCreateModal"
        >
          Request New Event
        </v-btn>
      </v-card-title>
      
      <v-card-text>
        <v-row class="mb-4">
          <v-col cols="12" md="6">
            <v-text-field
              v-model="searchKeywords"
              label="Search my events..."
              prepend-inner-icon="mdi-magnify"
              variant="outlined"
              density="compact"
              @input="searchReservations"
              clearable
            />
          </v-col>
          
          <v-col cols="12" md="6">
            <v-select
              v-model="facilityFilter"
              :items="facilityFilterOptions"
              label="Filter by Facility"
              variant="outlined"
              density="compact"
              @update:model-value="filterByFacility"
              clearable
            />
          </v-col>
        </v-row>

        <!-- Reusable Vuetify Table -->
        <DataTable
          :headers="tableHeaders"
          :items="events"
          :items-per-page="15"
          :external-search="searchKeywords"
          hide-search
        >
          <template #[`item.status`]="{ item }">
            <v-chip
              :color="getStatusColor(item.status)"
              variant="flat"
              size="small"
            >
              {{ item.status }}
            </v-chip>
          </template>
          
          <template #[`item.start_datetime`]="{ item }">
            {{ formatDateTime(item.start_datetime) }}
          </template>
          
          <template #[`item.end_datetime`]="{ item }">
            {{ formatDateTime(item.end_datetime) }}
          </template>
          
          <template #[`item.contact_number`]="{ item }">
            {{ item.contact_number || 'N/A' }}
          </template>
          
          <template #[`item.actions`]="{ item }">
            <v-btn
              icon="mdi-eye"
              variant="text"
              size="small"
              color="info"
              @click="viewEvent(item)"
            />
            <!-- Only show edit button for pending events -->
            <v-btn
              v-if="item.status === 'Pending'"
              icon="mdi-pencil"
              variant="text"
              size="small"
              color="warning"
              @click="editEvent(item)"
            />
          </template>
        </DataTable>
      </v-card-text>
    </v-card>

    <!-- Create/Edit Modal for Guests -->
    <GuestEventFormModal
      v-model="showFormModal"
      :event="currentEvent"
      :is-editing="isEditing"
      :loading="loading"
      @save="saveEvent"
    />

    <!-- View Modal -->
    <GuestEventViewModal
      v-model="showViewModal"
      :event="viewingEvent"
    />

    <!-- Loading Overlay -->
    <v-overlay
      :model-value="loading"
      class="align-center justify-center"
    >
      <v-progress-circular
        color="primary"
        indeterminate
        size="64"
      />
    </v-overlay>
  </v-container>
</template>

<script>
import axios from 'axios'
import DataTable from '@/components/common/Table.vue'
import GuestEventFormModal from '@/components/event/GuestEventFormModal.vue'
import GuestEventViewModal from '@/components/event/GuestEventViewModal.vue'
import { apiConfig } from '@/config/api.js'
import { buildApiUrl } from '@/utils/api'

export default {
  name: 'GuestEventsPage',
  components: {
    DataTable,
    GuestEventFormModal,
    GuestEventViewModal
  },
  data() {
    return {
      events: [],
      loading: false,
      showFormModal: false,
      showViewModal: false,
      isEditing: false,
      searchKeywords: '',
      facilityFilter: '',
      currentEvent: {},
      viewingEvent: {},
      apiBaseUrl: apiConfig.baseURL,
      currentUserId: 1, // TODO: Get from auth store/session
      
      tableHeaders: [
        { title: 'ID', key: 'id', sortable: true, width: '80px' },
        { title: 'Event Name', key: 'event_name', sortable: true },
        { title: 'Facility', key: 'facility', sortable: true },
        { title: 'Start Date/Time', key: 'start_datetime', sortable: true },
        { title: 'End Date/Time', key: 'end_datetime', sortable: true },
        { title: 'Status', key: 'status', sortable: true },
        { title: 'Contact', key: 'contact_number', sortable: false },
        { title: 'Attendees', key: 'expected_attendees', sortable: true },
        { title: 'Actions', key: 'actions', sortable: false, width: '120px' }
      ],
      
      facilityFilterOptions: [
        'Barangay Hall',
        'Basketball Court',
        'Covered Court',
        'Multi-purpose Area',
        'Auditorium',
        'Meeting Room 1',
        'Meeting Room 2',
        'Training Room A',
        'Executive Boardroom',
        'Conference Room A',
        'Conference Room B',
        'Conference Room C'
      ]
    }
  },
  mounted() {
    this.loadEvents()
  },
  methods: {
    async loadEvents() {
      this.loading = true
      try {
        const response = await axios.get(buildApiUrl(`/reservations/read_guest_reservations.php?user_id=${this.currentUserId}`))
        this.events = response.data.data || []
      } catch (error) {
        console.error('Error loading events:', error)
        this.events = []
      } finally {
        this.loading = false
      }
    },

    async searchReservations() {
      // Filter locally since we're only dealing with user's own events
      if (!this.searchKeywords?.trim()) {
        this.loadEvents()
        return
      }

      this.loading = true
      try {
        const response = await axios.get(buildApiUrl(`/reservations/read_guest_reservations.php?user_id=${this.currentUserId}`))
        const allEvents = response.data.data || []
        
        // Filter events locally by search keywords
        const searchTerm = this.searchKeywords.toLowerCase()
        this.events = allEvents.filter(event => 
          event.event_name?.toLowerCase().includes(searchTerm) ||
          event.facility?.toLowerCase().includes(searchTerm) ||
          event.requested_by?.toLowerCase().includes(searchTerm)
        )
      } catch (error) {
        console.error('Error searching events:', error)
        this.events = []
      } finally {
        this.loading = false
      }
    },

    async filterByFacility() {
      if (!this.facilityFilter) {
        this.loadEvents()
        return
      }

      this.loading = true
      try {
        const response = await axios.get(buildApiUrl(`/reservations/read_guest_reservations.php?user_id=${this.currentUserId}`))
        const allEvents = response.data.data || []
        
        // Filter events locally by facility
        this.events = allEvents.filter(event => 
          event.facility === this.facilityFilter
        )
      } catch (error) {
        console.error('Error filtering by facility:', error)
        this.events = []
      } finally {
        this.loading = false
      }
    },

    openCreateModal() {
      this.isEditing = false
      this.currentEvent = {
        id: null,
        user_id: this.currentUserId,
        facility: '',
        event_name: '',
        start_datetime: '',
        end_datetime: '',
        requested_by: '',
        contact_number: '',
        purpose: '',
        expected_attendees: 0
      }
      this.showFormModal = true
    },

    editEvent(event) {
      // Only allow editing of pending events
      if (event.status !== 'Pending') {
        this.$toast?.warning?.('Only pending events can be edited')
        return
      }
      
      this.isEditing = true
      this.currentEvent = { ...event }
      this.showFormModal = true
    },

    viewEvent(event) {
      this.viewingEvent = { ...event }
      this.showViewModal = true
    },

    async saveEvent(eventData) {
      this.loading = true
      try {
        if (this.isEditing) {
          // Use the guest-specific update endpoint that only allows certain fields
          await axios.put(buildApiUrl(`/reservations/update_guest_reservation.php`, eventData))
          this.$toast?.success?.('Event updated successfully')
        } else {
          // Use the guest-specific creation endpoint
          await axios.post(buildApiUrl(`/reservations/create_guest_reservation.php`, eventData))
          this.$toast?.success?.('Event request submitted successfully')
        }
        this.showFormModal = false
        this.loadEvents()
      } catch (error) {
        console.error('Error saving event:', error)
        const errorMessage = error.response?.data?.message || 'Error saving event'
        this.$toast?.error?.(errorMessage)
      } finally {
        this.loading = false
      }
    },

    getStatusColor(status) {
      const colors = {
        'Pending': 'warning',
        'Approved': 'success',
        'Rejected': 'error',
        'Completed': 'info',
        'Cancelled': 'grey'
      }
      return colors[status] || 'grey'
    },

    formatDateTime(datetime) {
      if (!datetime) return 'N/A'
      return new Date(datetime).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }
  }
}
</script>

<style scoped>
.v-card-title {
  padding: 16px 24px;
  background-color: var(--v-theme-surface);
}

.v-card-text {
  padding: 0 24px 24px;
}
</style>