<template>
  <v-container fluid>
    <v-card>
      <v-card-title class="d-flex justify-space-between align-center">
        <span class="text-h4">Events Management</span>
        <v-btn
          color="primary"
          prepend-icon="mdi-plus"
          @click="openCreateModal"
        >
          Add New Event
        </v-btn>
      </v-card-title>
      
      <v-card-text>
        <v-row class="mb-4">
          <v-col cols="12" md="4">
            <v-text-field
              v-model="searchKeywords"
              label="Search events..."
              prepend-inner-icon="mdi-magnify"
              variant="outlined"
              density="compact"
              @input="searchReservations"
              clearable
            />
          </v-col>
          
          <v-col cols="12" md="4">
            <v-select
              v-model="statusFilter"
              :items="statusFilterOptions"
              label="Filter by Status"
              variant="outlined"
              density="compact"
              @update:model-value="filterByStatus"
              clearable
            />
          </v-col>
          
          <v-col cols="12" md="4">
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
            <v-btn
              icon="mdi-pencil"
              variant="text"
              size="small"
              color="warning"
              @click="editEvent(item)"
            />
            <v-btn
              icon="mdi-delete"
              variant="text"
              size="small"
              color="error"
              @click="deleteEvent(item.id)"
            />
          </template>
        </DataTable>
      </v-card-text>
    </v-card>

    <!-- Create/Edit Modal -->
    <EventFormModal
      v-model="showFormModal"
      :event="currentEvent"
      :is-editing="isEditing"
      :loading="loading"
      @save="saveEvent"
    />

    <!-- View Modal -->
    <EventViewModal
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

    <!-- Delete Confirmation Dialog -->
    <v-dialog v-model="showDeleteDialog" max-width="400">
      <v-card>
        <v-card-title>Confirm Delete</v-card-title>
        <v-card-text>
          Are you sure you want to delete this event?
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn @click="showDeleteDialog = false">Cancel</v-btn>
          <v-btn color="error" @click="confirmDelete">Delete</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import axios from 'axios'
import DataTable from '@/components/common/Table.vue'
import EventFormModal from '@/components/event/EventFormModal.vue'
import EventViewModal from '@/components/event/EventViewModal.vue'
import { apiConfig } from '@/config/api.js'

export default {
  name: 'EventsTable',
  components: {
    DataTable,
    EventFormModal,
    EventViewModal
  },
  data() {
    return {
      events: [],
      loading: false,
      showFormModal: false,
      showViewModal: false,
      showDeleteDialog: false,
      isEditing: false,
      searchKeywords: '',
      statusFilter: '',
      facilityFilter: '',
      currentEvent: {},
      viewingEvent: {},
      deleteEventId: null,
      apiBaseUrl: apiConfig.baseURL,
      
      tableHeaders: [
        { title: 'ID', key: 'id', sortable: true, width: '80px' },
        { title: 'Event Name', key: 'event_name', sortable: true },
        { title: 'Facility', key: 'facility', sortable: true },
        { title: 'Start Date/Time', key: 'start_datetime', sortable: true },
        { title: 'End Date/Time', key: 'end_datetime', sortable: true },
        { title: 'Status', key: 'status', sortable: true },
        { title: 'Requested By', key: 'requested_by', sortable: true },
        { title: 'Contact', key: 'contact_number', sortable: false },
        { title: 'Attendees', key: 'expected_attendees', sortable: true },
        { title: 'Actions', key: 'actions', sortable: false, width: '120px' }
      ],
      
      statusFilterOptions: [
        { title: 'All Status', value: '' },
        { title: 'Pending', value: 'Pending' },
        { title: 'Approved', value: 'Approved' },
        { title: 'Rejected', value: 'Rejected' },
        { title: 'Completed', value: 'Completed' },
        { title: 'Cancelled', value: 'Cancelled' }
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
        const response = await axios.get(`${this.apiBaseUrl}/reservations/readReservation.php`)
        this.events = response.data.data || []
      } catch (error) {
        console.error('Error loading events:', error)
        this.events = []
      } finally {
        this.loading = false
      }
    },

    async searchReservations() {
      if (!this.searchKeywords?.trim()) {
        this.loadEvents()
        return
      }

      this.loading = true
      try {
        const response = await axios.get(`${this.apiBaseUrl}/reservations/search_reservations.php?keywords=${encodeURIComponent(this.searchKeywords)}`)
        this.events = response.data.data || []
      } catch (error) {
        console.error('Error searching events:', error)
        this.events = []
      } finally {
        this.loading = false
      }
    },

    async filterByStatus() {
      if (!this.statusFilter) {
        this.loadEvents()
        return
      }

      this.loading = true
      try {
        const response = await axios.get(`${this.apiBaseUrl}/reservations/read_reservations_by_status.php?status=${this.statusFilter}`)
        this.events = response.data.data || []
      } catch (error) {
        console.error('Error filtering by status:', error)
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
        const response = await axios.get(`${this.apiBaseUrl}/reservations/read_reservations_by_facility.php?facility=${encodeURIComponent(this.facilityFilter)}`)
        this.events = response.data.data || []
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
        user_id: null,
        official_id: null,
        facility: '',
        event_name: '',
        start_datetime: '',
        end_datetime: '',
        status: 'Pending',
        requested_by: '',
        contact_number: '',
        purpose: '',
        expected_attendees: 0
      }
      this.showFormModal = true
    },

    editEvent(event) {
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
          await axios.put(`${this.apiBaseUrl}/reservations/update_reservation.php`, eventData)
          this.$toast?.success?.('Event updated successfully')
        } else {
          await axios.post(`${this.apiBaseUrl}/reservations/create_singleReservation.php`, eventData)
          this.$toast?.success?.('Event created successfully')
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

    deleteEvent(eventId) {
      this.deleteEventId = eventId
      this.showDeleteDialog = true
    },

    async confirmDelete() {
      this.showDeleteDialog = false
      this.loading = true
      try {
        await axios.delete(`${this.apiBaseUrl}/reservations/delete_reservation.php`, {
          data: { id: this.deleteEventId }
        })
        this.$toast?.success?.('Event deleted successfully')
        this.loadEvents()
      } catch (error) {
        console.error('Error deleting event:', error)
        this.$toast?.error?.('Error deleting event')
      } finally {
        this.loading = false
        this.deleteEventId = null
      }
    },

    formatDateTime(dateTime) {
      if (!dateTime) return 'N/A'
      return new Date(dateTime).toLocaleString()
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
    }
  }
}
</script>

<style scoped>
.v-container {
  padding: 0;
}

.events-table-container {
  padding: 20px;
  background: #f8f9fa;
  min-height: 100vh;
}

.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.table-header h2 {
  color: #333;
  margin: 0;
}

.controls {
  display: flex;
  gap: 10px;
  align-items: center;
}

.search-input,
.filter-select {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.search-input {
  width: 200px;
}

.filter-select {
  width: 150px;
}

.btn {
  padding: 6px 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 12px;
  text-decoration: none;
  display: inline-block;
  transition: background-color 0.2s;
}

.btn-primary {
  background: #007bff;
  color: white;
}

.btn-primary:hover {
  background: #0056b3;
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background: #545b62;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 8px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #eee;
}

.modal-header h3 {
  margin: 0;
  color: #333;
}

.close-btn {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #999;
}

.close-btn:hover {
  color: #333;
}

.modal-body {
  padding: 20px;
}

.form-group {
  margin-bottom: 15px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: 500;
  color: #333;
}

.form-control {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.form-control:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}

.detail-group {
  margin-bottom: 15px;
}

.detail-group label {
  font-weight: 600;
  color: #555;
  margin-bottom: 5px;
  display: block;
}

.detail-group p {
  margin: 0;
  padding: 8px;
  background: #f8f9fa;
  border-radius: 4px;
}

.status-badge {
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
  text-transform: uppercase;
}

.status-badge.pending {
  background: #fff3cd;
  color: #856404;
}

.status-badge.approved {
  background: #d4edda;
  color: #155724;
}

.status-badge.rejected {
  background: #f8d7da;
  color: #721c24;
}

.status-badge.completed {
  background: #d1ecf1;
  color: #0c5460;
}

.status-badge.cancelled {
  background: #f5f5f5;
  color: #6c757d;
}

@media (max-width: 768px) {
  .table-header {
    flex-direction: column;
    gap: 15px;
    align-items: stretch;
  }

  .controls {
    flex-wrap: wrap;
  }

  .search-input,
  .filter-select {
    width: 100%;
  }

  .modal-content {
    width: 95%;
    margin: 10px;
  }

  .form-row {
    grid-template-columns: 1fr;
  }
}
</style>
