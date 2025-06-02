<template>
  <Modal
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    title="Event Details"
    max-width="600"
  >
    <v-container>
      <v-row>
        <v-col cols="12">
          <DetailItem label="Event Name" :value="event.event_name" />
        </v-col>
        
        <v-col cols="12" md="6">
          <DetailItem label="Facility" :value="event.facility" />
        </v-col>
        
        <v-col cols="12" md="6">
          <DetailItem label="Status">
            <v-chip
              :color="getStatusColor(event.status)"
              variant="flat"
              size="small"
            >
              {{ event.status }}
            </v-chip>
          </DetailItem>
        </v-col>
        
        <v-col cols="12" md="6">
          <DetailItem label="Start Date/Time" :value="formatDateTime(event.start_datetime)" />
        </v-col>
        
        <v-col cols="12" md="6">
          <DetailItem label="End Date/Time" :value="formatDateTime(event.end_datetime)" />
        </v-col>
        
        <v-col cols="12" md="6">
          <DetailItem label="Requested By" :value="event.requested_by" />
        </v-col>
        
        <v-col cols="12" md="6">
          <DetailItem label="Contact Number" :value="event.contact_number || 'N/A'" />
        </v-col>
        
        <v-col cols="12" md="6">
          <DetailItem label="Expected Attendees" :value="event.expected_attendees" />
        </v-col>
        
        <v-col cols="12" md="6">
          <DetailItem label="Created At" :value="formatDateTime(event.created_at)" />
        </v-col>
        
        <v-col cols="12">
          <DetailItem label="Purpose" :value="event.purpose || 'N/A'" />
        </v-col>
      </v-row>
    </v-container>

    <template #actions>
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

// Detail Item Component (inline)
const DetailItem = {
  name: 'DetailItem',
  props: {
    label: String,
    value: [String, Number]
  },
  template: `
    <div class="mb-3">
      <v-chip variant="outlined" size="small" class="mb-2">
        {{ label }}
      </v-chip>
      <div class="text-body-1">
        <slot>{{ value }}</slot>
      </div>
    </div>
  `
}

export { DetailItem }
</script>