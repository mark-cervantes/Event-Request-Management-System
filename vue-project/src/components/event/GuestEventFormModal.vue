<template>
  <Modal
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    :title="isEditing ? 'Edit Event Request' : 'Submit Event Request'"
    max-width="600"
    persistent
  >
    <v-form
      ref="form"
      v-model="valid"
      @submit.prevent="handleSubmit"
    >
      <v-container>
        <v-row>
          <v-col cols="12">
            <v-text-field
              v-model="localEvent.event_name"
              label="Event Name"
              :rules="[rules.required]"
              required
              variant="outlined"
            />
          </v-col>
          
          <v-col cols="12">
            <v-select
              v-model="localEvent.facility"
              :items="facilityOptions"
              label="Facility"
              :rules="[rules.required]"
              required
              variant="outlined"
            />
          </v-col>
          
          <v-col cols="12" md="6">
            <v-text-field
              v-model="localEvent.start_datetime"
              label="Start Date/Time"
              type="datetime-local"
              :rules="[rules.required, rules.futureDate]"
              required
              variant="outlined"
            />
          </v-col>
          
          <v-col cols="12" md="6">
            <v-text-field
              v-model="localEvent.end_datetime"
              label="End Date/Time"
              type="datetime-local"
              :rules="[rules.required, rules.endAfterStart]"
              required
              variant="outlined"
            />
          </v-col>
          
          <v-col cols="12">
            <v-text-field
              v-model="localEvent.requested_by"
              label="Requested By (Your Name)"
              :rules="[rules.required]"
              required
              variant="outlined"
            />
          </v-col>
          
          <v-col cols="12" md="6">
            <v-text-field
              v-model="localEvent.contact_number"
              label="Contact Number"
              :rules="[rules.required, rules.phone]"
              required
              variant="outlined"
            />
          </v-col>
          
          <v-col cols="12" md="6">
            <v-text-field
              v-model="localEvent.expected_attendees"
              label="Expected Attendees"
              type="number"
              min="1"
              :rules="[rules.required, rules.positiveNumber]"
              required
              variant="outlined"
            />
          </v-col>
          
          <v-col cols="12">
            <v-textarea
              v-model="localEvent.purpose"
              label="Purpose/Description"
              rows="3"
              variant="outlined"
              :rules="[rules.required]"
              required
            />
          </v-col>

          <!-- Read-only status indicator for editing -->
          <v-col v-if="isEditing" cols="12">
            <v-chip
              :color="getStatusColor(localEvent.status)"
              variant="flat"
              size="large"
              class="mb-2"
            >
              Status: {{ localEvent.status }}
            </v-chip>
            <v-alert
              v-if="localEvent.status !== 'Pending'"
              type="info"
              variant="tonal"
              class="mt-2"
            >
              Note: Only pending events can be modified.
            </v-alert>
          </v-col>
        </v-row>
      </v-container>
    </v-form>

    <template #actions>
      <v-btn
        variant="text"
        @click="$emit('update:modelValue', false)"
      >
        Cancel
      </v-btn>
      <v-btn
        type="submit"
        color="primary"
        :loading="loading"
        :disabled="!valid || (isEditing && localEvent.status !== 'Pending')"
        @click="handleSubmit"
      >
        {{ isEditing ? 'Update Request' : 'Submit Request' }}
      </v-btn>
    </template>
  </Modal>
</template>

<script>
import Modal from '@/components/common/Modal.vue'

export default {
  name: 'GuestEventFormModal',
  components: {
    Modal
  },
  props: {
    modelValue: {
      type: Boolean,
      default: false
    },
    event: {
      type: Object,
      default: () => ({})
    },
    isEditing: {
      type: Boolean,
      default: false
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  emits: ['update:modelValue', 'save'],
  data() {
    return {
      valid: false,
      localEvent: {},
      
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
          const pattern = /^[0-9+\-\s()]+$/
          return pattern.test(value) || 'Please enter a valid phone number'
        },
        positiveNumber: value => {
          return (value > 0) || 'Must be greater than 0'
        },
        futureDate: value => {
          if (!value) return true
          const selectedDate = new Date(value)
          const now = new Date()
          return selectedDate > now || 'Date must be in the future'
        },
        endAfterStart: value => {
          if (!value || !this.localEvent.start_datetime) return true
          const startDate = new Date(this.localEvent.start_datetime)
          const endDate = new Date(value)
          return endDate > startDate || 'End time must be after start time'
        }
      }
    }
  },
  watch: {
    event: {
      handler(newEvent) {
        this.localEvent = { ...newEvent }
      },
      immediate: true,
      deep: true
    },
    modelValue(newValue) {
      if (newValue && this.$refs.form) {
        this.$refs.form.resetValidation()
      }
    }
  },
  methods: {
    async handleSubmit() {
      const { valid } = await this.$refs.form.validate()
      if (!valid) return

      // For guests, status is always set to 'Pending' on the backend
      const eventData = {
        ...this.localEvent,
        expected_attendees: parseInt(this.localEvent.expected_attendees) || 0
      }

      this.$emit('save', eventData)
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
.v-form {
  max-height: 70vh;
  overflow-y: auto;
}
</style>
