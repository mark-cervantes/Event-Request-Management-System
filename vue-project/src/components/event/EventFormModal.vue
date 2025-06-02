<template>
  <Modal
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    :title="isEditing ? 'Edit Event' : 'Create New Event'"
    max-width="800"
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
              :rules="[rules.required]"
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
              label="Requested By"
              :rules="[rules.required]"
              required
              variant="outlined"
            />
          </v-col>
          
          <v-col cols="12" md="6">
            <v-text-field
              v-model="localEvent.contact_number"
              label="Contact Number"
              variant="outlined"
            />
          </v-col>
          
          <v-col cols="12" md="6">
            <v-select
              v-model="localEvent.status"
              :items="statusOptions"
              label="Status"
              variant="outlined"
            />
          </v-col>
          
          <v-col cols="12">
            <v-textarea
              v-model="localEvent.purpose"
              label="Purpose"
              variant="outlined"
              rows="3"
            />
          </v-col>
          
          <v-col cols="12" md="6">
            <v-text-field
              v-model.number="localEvent.expected_attendees"
              label="Expected Attendees"
              type="number"
              min="0"
              variant="outlined"
            />
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
        color="primary"
        :loading="loading"
        :disabled="!valid"
        @click="handleSubmit"
      >
        {{ isEditing ? 'Update' : 'Create' }}
      </v-btn>
    </template>
  </Modal>
</template>

<script>
import Modal from '@/components/common/Modal.vue'

export default {
  name: 'EventFormModal',
  components: {
    Modal
  },
  emits: ['update:modelValue', 'save'],
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
      statusOptions: [
        { title: 'Pending', value: 'Pending' },
        { title: 'Approved', value: 'Approved' },
        { title: 'Rejected', value: 'Rejected' },
        { title: 'Completed', value: 'Completed' },
        { title: 'Cancelled', value: 'Cancelled' }
      ],
      rules: {
        required: value => !!value || 'Required.',
        endAfterStart: () => {
          if (this.localEvent.start_datetime && this.localEvent.end_datetime) {
            return new Date(this.localEvent.end_datetime) > new Date(this.localEvent.start_datetime) || 'End time must be after start time.'
          }
          return true
        }
      }
    }
  },
  watch: {
    modelValue(newVal) {
      if (newVal) {
        this.resetForm()
      }
    },
    event: {
      handler(newEvent) {
        this.localEvent = { ...newEvent }
        if (this.isEditing && newEvent.start_datetime) {
          this.localEvent.start_datetime = this.formatDateTimeForInput(newEvent.start_datetime)
          this.localEvent.end_datetime = this.formatDateTimeForInput(newEvent.end_datetime)
        }
      },
      deep: true,
      immediate: true
    }
  },
  methods: {
    resetForm() {
      if (!this.isEditing) {
        this.localEvent = {
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
      }
      this.$refs.form?.resetValidation()
    },
    
    async handleSubmit() {
      const { valid } = await this.$refs.form.validate()
      if (valid) {
        this.$emit('save', { ...this.localEvent })
      }
    },
    
    formatDateTimeForInput(dateTime) {
      if (!dateTime) return ''
      const date = new Date(dateTime)
      return date.toISOString().slice(0, 16)
    }
  }
}
</script>