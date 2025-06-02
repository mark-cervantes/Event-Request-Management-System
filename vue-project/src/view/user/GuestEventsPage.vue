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
              @input="searchMyReservations"
              clearable
            />
          </v-col>
          
          <v-col cols="12" md="6">
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
        </v-row>

        <!-- Events Table -->
        <v-data-table
          :headers="tableHeaders"
          :items="filteredEvents"
          :items-per-page="10"
          :loading="loading"
          class="elevation-1"
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
          
          <template #[`item.actions`]="{ item }">
            <v-btn
              icon="mdi-eye"
              variant="text"
              size="small"
              color="info"
              @click="viewEvent(item)"
              title="View Details"
            />
            <v-btn
              v-if="item.status === 'Pending'"
              icon="mdi-cancel"
              variant="text"
              size="small"
              color="error"
              @click="cancelEvent(item.id)"
              title="Cancel Request"
            />
          </template>
        </v-data-table>
      </v-card-text>
    </v-card>

    <!-- Create Modal -->
    <v-dialog v-model="showCreateModal" max-width="800" persistent>
      <v-card>
        <v-card-title class="text-h5">
          Request Event Reservation
        </v-card-title>
        
        <v-card-text>
          <v-form ref="createForm" v-model="formValid">
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="newEvent.event_name"
                  label="Event Name"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                />
              </v-col>
              
              <v-col cols="12" md="6">
                <v-select
                  v-model="newEvent.facility"
                  :items="facilityOptions"
                  label="Facility"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                />
              </v-col>
              
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="newEvent.start_datetime"
                  label="Start Date & Time"
                  type="datetime-local"
                  variant="outlined"
                  :rules="[rules.required, rules.futureDate]"
                  required
                />
              </v-col>
              
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="newEvent.end_datetime"
                  label="End Date & Time"
                  type="datetime-local"
                  variant="outlined"
                  :rules="[rules.required, rules.endAfterStart]"
                  required
                />
              </v-col>
              
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="newEvent.requested_by"
                  label="Requested By"
                  variant="outlined"
                  :rules="[rules.required]"
                  required
                />
              </v-col>
              
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="newEvent.contact_number"
                  label="Contact Number"
                  variant="outlined"
                  :rules="[rules.required, rules.phone]"
                  required
                />
              </v-col>
              
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="newEvent.expected_attendees"
                  label="Expected Attendees"
                  type="number"
                  variant="outlined"
                  :rules="[rules.required, rules.positiveNumber]"
                  required
                />
              </v-col>
              
              <v-col cols="12">
                <v-textarea
                  v-model="newEvent.purpose"
                  label="Purpose/Description"
                  variant="outlined"
                  rows="3"
                  :rules="[rules.required]"
                  required
                />
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        
        <v-card-actions>
          <v-spacer />
          <v-btn @click="closeCreateModal">Cancel</v-btn>
          <v-btn 
            color="primary" 
            @click="submitReservation"
            :loading="loading"
            :disabled="!formValid"
          >
            Submit Request
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- View Modal -->
    <v-dialog v-model="showViewModal" max-width="600">
      <v-card v-if="viewingEvent">
        <v-card-title class="text-h5">
          Event Details
        </v-card-title>
        
        <v-card-text>
          <v-row>
            <v-col cols="12" md="6">
              <div class="detail-group">
                <label>Event Name:</label>
                <p>{{ viewingEvent.event_name }}</p>
              </div>
            </v-col>
            
            <v-col cols="12" md="6">
              <div class="detail-group">
                <label>Facility:</label>
                <p>{{ viewingEvent.facility }}</p>
              </div>
            </v-col>
            
            <v-col cols="12" md="6">
              <div class="detail-group">
                <label>Start Date & Time:</label>
                <p>{{ formatDateTime(viewingEvent.start_datetime) }}</p>
              </div>
            </v-col>
            
            <v-col cols="12" md="6">
              <div class="detail-group">
                <label>End Date & Time:</label>
                <p>{{ formatDateTime(viewingEvent.end_datetime) }}</p>
              </div>
            </v-col>
            
            <v-col cols="12" md="6">
              <div class="detail-group">
                <label>Status:</label>
                <v-chip
                  :color="getStatusColor(viewingEvent.status)"
                  variant="flat"
                  size="small"
                >
                  {{ viewingEvent.status }}
                </v-chip>
              </div>
            </v-col>
            
            <v-col cols="12" md="6">
              <div class="detail-group">
                <label>Expected Attendees:</label>
                <p>{{ viewingEvent.expected_attendees }}</p>
              </div>
            </v-col>
            
            <v-col cols="12" md="6">
              <div class="detail-group">
                <label>Requested By:</label>
                <p>{{ viewingEvent.requested_by }}</p>
              </div>
            </v-col>
            
            <v-col cols="12" md="6">
              <div class="detail-group">
                <label>Contact Number:</label>
                <p>{{ viewingEvent.contact_number }}</p>
              </div>
            </v-col>
            
            <v-col cols="12">
              <div class="detail-group">
                <label>Purpose:</label>
                <p>{{ viewingEvent.purpose || 'N/A' }}</p>
              </div>
            </v-col>
          </v-row>
        </v-card-text>
        
        <v-card-actions>
          <v-spacer />
          <v-btn @click="showViewModal = false">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

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

    <!-- Cancel Confirmation Dialog -->
    <v-dialog v-model="showCancelDialog" max-width="400">
      <v-card>
        <v-card-title>Cancel Reservation</v-card-title>
        <v-card-text>
          Are you sure you want to cancel this reservation request?
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn @click="showCancelDialog = false">No</v-btn>
          <v-btn color="error" @click="confirmCancel">Yes, Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import axios from 'axios'
import { apiConfig } from '@/config/api.js'

export default {
  name: 'GuestEventsPage',
  data() {
    return {
      events: [],
      loading: false,
      showCreateModal: false,
      showViewModal: false,
      showCancelDialog: false,
      formValid: false,
      searchKeywords: '',
      statusFilter: '',
      viewingEvent: null,
      cancelEventId: null,
      apiBaseUrl: apiConfig.baseURL,
      
      newEvent: {
        event_name: '',
        facility: '',
        start_datetime: '',
        end_datetime: '',
        requested_by: '',
        contact_number: '',
        purpose: '',
        expected_attendees: 1
      },
      
      tableHeaders: [
        { title: 'Event Name', key: 'event_name', sortable: true },
        { title: 'Facility', key: 'facility', sortable: true },
        { title: 'Start Date/Time', key: 'start_datetime', sortable: true },
        { title: 'End Date/Time', key: 'end_datetime', sortable: true },
        { title: 'Status', key: 'status', sortable: true },
        { title: 'Attendees', key: 'expected_attendees', sortable: true },
        { title: 'Actions', key: 'actions', sortable: false, width: '100px' }
      ],
      
      statusFilterOptions: [
        { title: 'All Status', value: '' },
        { title: 'Pending', value: 'Pending' },
        { title: 'Approved', value: 'Approved' },
        { title: 'Rejected', value: 'Rejected' },
        { title: 'Completed', value: 'Completed' },
        { title: 'Cancelled', value: 'Cancelled' }
      ],
      
      facilityOptions: [
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
      ],
      
      rules: {
        required: value => !!value || 'This field is required',
        phone: value => {
          const pattern = /^[\d\-\+\(\)\s]+$/
          return pattern.test(value) || 'Invalid phone number format'
        },
        positiveNumber: value => value > 0 || 'Must be greater than 0',
        futureDate: value => {
          if (!value) return true
          return new Date(value) > new Date() || 'Date must be in the future'
        },
        endAfterStart: value => {
          if (!value || !this.newEvent.start_datetime) return true
          return new Date(value) > new Date(this.newEvent.start_datetime) || 'End time must be after start time'
        }
      }
    }
  },
  
  computed: {
    filteredEvents() {
      let filtered = this.events
      
      if (this.searchKeywords) {
        const search = this.searchKeywords.toLowerCase()
        filtered = filtered.filter(event => 
          event.event_name.toLowerCase().includes(search) ||
          event.facility.toLowerCase().includes(search) ||
          event.requested_by.toLowerCase().includes(search)
        )
      }
      
      if (this.statusFilter) {
        filtered = filtered.filter(event => event.status === this.statusFilter)
      }
      
      return filtered
    },
    
    currentUserId() {
      // Get user ID from localStorage or session
      const user = JSON.parse(localStorage.getItem('user') || '{}')
      return user.id || 1 // Fallback for demo purposes
    }
  },
  
  mounted() {
    this.loadMyEvents()
  },
  
  methods: {
    async loadMyEvents() {
      this.loading = true
      try {
        const response = await axios.get(`${this.apiBaseUrl}/reservations/read_guest_reservations.php?guest_id=${this.currentUserId}`)
        this.events = response.data.data || []
      } catch (error) {
        console.error('Error loading events:', error)
        this.events = []
        this.$toast?.error?.('Error loading your events')
      } finally {
        this.loading = false
      }
    },

    searchMyReservations() {
      // Filtering is handled by computed property
    },

    filterByStatus() {
      // Filtering is handled by computed property
    },

    openCreateModal() {
      this.resetNewEvent()
      this.showCreateModal = true
    },

    closeCreateModal() {
      this.showCreateModal = false
      this.resetNewEvent()
    },

    resetNewEvent() {
      this.newEvent = {
        event_name: '',
        facility: '',
        start_datetime: '',
        end_datetime: '',
        requested_by: '',
        contact_number: '',
        purpose: '',
        expected_attendees: 1
      }
    },

    viewEvent(event) {
      this.viewingEvent = { ...event }
      this.showViewModal = true
    },

    async submitReservation() {
      if (!this.formValid) return

      this.loading = true
      try {
        const eventData = {
          ...this.newEvent,
          user_id: this.currentUserId
        }

        await axios.post(`${this.apiBaseUrl}/reservations/create_guest_reservation.php`, eventData)
        this.$toast?.success?.('Reservation request submitted successfully!')
        this.closeCreateModal()
        this.loadMyEvents()
      } catch (error) {
        console.error('Error submitting reservation:', error)
        const errorMessage = error.response?.data?.message || 'Error submitting reservation request'
        this.$toast?.error?.(errorMessage)
      } finally {
        this.loading = false
      }
    },

    cancelEvent(eventId) {
      this.cancelEventId = eventId
      this.showCancelDialog = true
    },

    async confirmCancel() {
      this.showCancelDialog = false
      this.loading = true
      try {
        await axios.put(`${this.apiBaseUrl}/reservations/update_reservation.php`, {
          id: this.cancelEventId,
          status: 'Cancelled'
        })
        this.$toast?.success?.('Reservation cancelled successfully')
        this.loadMyEvents()
      } catch (error) {
        console.error('Error cancelling reservation:', error)
        this.$toast?.error?.('Error cancelling reservation')
      } finally {
        this.loading = false
        this.cancelEventId = null
      }
    },

    formatDateTime(dateTime) {
      if (!dateTime) return 'N/A'
      return new Date(dateTime).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
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
  padding: 20px;
}

.detail-group {
  margin-bottom: 15px;
}

.detail-group label {
  font-weight: 600;
  color: #555;
  margin-bottom: 5px;
  display: block;
  font-size: 14px;
}

.detail-group p {
  margin: 0;
  padding: 8px;
  background: #f8f9fa;
  border-radius: 4px;
  color: #333;
}

@media (max-width: 768px) {
  .v-container {
    padding: 10px;
  }
  
  .text-h4 {
    font-size: 1.5rem !important;
  }
}
</style>
