<template>
  <Modal
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    title="Event Details"
    max-width="600"
  >
    <v-container v-if="event">
      <v-row>
        <v-col cols="12">
          <h3 class="text-h5 mb-4">{{ event.event_name }}</h3>
        </v-col>
        
        <v-col cols="12" md="6">
          <v-list-item class="px-0">
            <template #prepend>
              <v-icon icon="mdi-map-marker" color="primary" />
            </template>
            <v-list-item-title>Facility</v-list-item-title>
            <v-list-item-subtitle>{{ event.facility }}</v-list-item-subtitle>
          </v-list-item>
        </v-col>
        
        <v-col cols="12" md="6">
          <v-list-item class="px-0">
            <template #prepend>
              <v-icon icon="mdi-clock" color="primary" />
            </template>
            <v-list-item-title>Status</v-list-item-title>
            <v-list-item-subtitle>
              <v-chip
                :color="getStatusColor(event.status)"
                variant="flat"
                size="small"
              >
                {{ event.status }}
              </v-chip>
            </v-list-item-subtitle>
          </v-list-item>
        </v-col>
        
        <v-col cols="12" md="6">
          <v-list-item class="px-0">
            <template #prepend>
              <v-icon icon="mdi-calendar-start" color="primary" />
            </template>
            <v-list-item-title>Start Date & Time</v-list-item-title>
            <v-list-item-subtitle>{{ formatDateTime(event.start_datetime) }}</v-list-item-subtitle>
          </v-list-item>
        </v-col>
        
        <v-col cols="12" md="6">
          <v-list-item class="px-0">
            <template #prepend>
              <v-icon icon="mdi-calendar-end" color="primary" />
            </template>
            <v-list-item-title>End Date & Time</v-list-item-title>
            <v-list-item-subtitle>{{ formatDateTime(event.end_datetime) }}</v-list-item-subtitle>
          </v-list-item>
        </v-col>
        
        <v-col cols="12" md="6">
          <v-list-item class="px-0">
            <template #prepend>
              <v-icon icon="mdi-account" color="primary" />
            </template>
            <v-list-item-title>Requested By</v-list-item-title>
            <v-list-item-subtitle>{{ event.requested_by }}</v-list-item-subtitle>
          </v-list-item>
        </v-col>
        
        <v-col cols="12" md="6">
          <v-list-item class="px-0">
            <template #prepend>
              <v-icon icon="mdi-phone" color="primary" />
            </template>
            <v-list-item-title>Contact Number</v-list-item-title>
            <v-list-item-subtitle>{{ event.contact_number || 'Not provided' }}</v-list-item-subtitle>
          </v-list-item>
        </v-col>
        
        <v-col cols="12" md="6">
          <v-list-item class="px-0">
            <template #prepend>
              <v-icon icon="mdi-account-group" color="primary" />
            </template>
            <v-list-item-title>Expected Attendees</v-list-item-title>
            <v-list-item-subtitle>{{ event.expected_attendees || 'Not specified' }}</v-list-item-subtitle>
          </v-list-item>
        </v-col>
        
        <v-col cols="12" md="6">
          <v-list-item class="px-0">
            <template #prepend>
              <v-icon icon="mdi-calendar-plus" color="primary" />
            </template>
            <v-list-item-title>Date Requested</v-list-item-title>
            <v-list-item-subtitle>{{ formatDateTime(event.created_at) }}</v-list-item-subtitle>
          </v-list-item>
        </v-col>
        
        <v-col v-if="event.purpose" cols="12">
          <v-list-item class="px-0">
            <template #prepend>
              <v-icon icon="mdi-text" color="primary" />
            </template>
            <v-list-item-title>Purpose/Description</v-list-item-title>
            <v-list-item-subtitle class="text-wrap">{{ event.purpose }}</v-list-item-subtitle>
          </v-list-item>
        </v-col>

        <!-- Show approval/rejection details if available -->
        <v-col v-if="event.official_id && (event.status === 'Approved' || event.status === 'Rejected')" cols="12">
          <v-divider class="my-4" />
          <h4 class="text-h6 mb-3">Review Information</h4>
          <v-list-item class="px-0">
            <template #prepend>
              <v-icon icon="mdi-account-tie" color="secondary" />
            </template>
            <v-list-item-title>Reviewed By</v-list-item-title>
            <v-list-item-subtitle>Official ID: {{ event.official_id }}</v-list-item-subtitle>
          </v-list-item>
          <v-list-item v-if="event.updated_at" class="px-0">
            <template #prepend>
              <v-icon icon="mdi-update" color="secondary" />
            </template>
            <v-list-item-title>Review Date</v-list-item-title>
            <v-list-item-subtitle>{{ formatDateTime(event.updated_at) }}</v-list-item-subtitle>
          </v-list-item>
        </v-col>

        <!-- Status-specific messages -->
        <v-col cols="12">
          <v-alert
            v-if="event.status === 'Pending'"
            type="info"
            variant="tonal"
            class="mt-4"
          >
            <v-icon icon="mdi-clock" />
            This event request is pending review.
          </v-alert>
          <v-alert
            v-else-if="event.status === 'Approved'"
            type="success"
            variant="tonal"
            class="mt-4"
          >
            <v-icon icon="mdi-check-circle" />
            This event has been approved.
          </v-alert>
          <v-alert
            v-else-if="event.status === 'Rejected'"
            type="error"
            variant="tonal"
            class="mt-4"
          >
            <v-icon icon="mdi-close-circle" />
            This event request has been rejected.
          </v-alert>
          <v-alert
            v-else-if="event.status === 'Completed'"
            type="info"
            variant="tonal"
            class="mt-4"
          >
            <v-icon icon="mdi-check-all" />
            This event has been completed.
          </v-alert>
          <v-alert
            v-else-if="event.status === 'Cancelled'"
            type="warning"
            variant="tonal"
            class="mt-4"
          >
            <v-icon icon="mdi-cancel" />
            This event has been cancelled.
          </v-alert>
        </v-col>
      </v-row>
    </v-container>
    <template #actions>
      <v-spacer />
      <v-btn
        variant="text"
        @click="$emit('update:modelValue', false)"
      >
        Close
      </v-btn>
    </template>
  </Modal>
</template>

<script>
import Modal from '@/components/common/Modal.vue'

export default {
  name: 'EventViewModal',
  components: {
    Modal
  },
  emits: ['update:modelValue'],
  props: {
    modelValue: {
      type: Boolean,
      default: false
    },
    event: {
      type: Object,
      default: () => ({})
    }
  },
  methods: {
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
      if (!datetime) return 'Not available'
      return new Date(datetime).toLocaleString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }
  }
}
</script>

<style scoped>
.v-list-item {
  min-height: 48px;
}

.v-list-item-subtitle {
  white-space: normal;
  word-wrap: break-word;
}
</style>