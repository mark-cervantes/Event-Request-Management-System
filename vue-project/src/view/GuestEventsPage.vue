<template>
  <v-container fluid>
    <v-row justify="center">
      <v-col cols="12" md="10" lg="8">
        <v-card elevation="12" rounded="xl" class="guest-form-card">
          <v-card-title class="text-center pa-8">
            <div class="text-h3 text-primary mb-3">
              <v-icon size="48" class="mr-3">mdi-calendar-plus</v-icon>
              Request Event Space
            </div>
            <div class="text-h6 text-grey-darken-1 font-weight-regular">
              Submit your event reservation request below. Our team will review and contact you within 24 hours.
            </div>
          </v-card-title>

          <v-card-text class="pa-8">
            <v-alert
              color="info"
              variant="tonal"
              icon="mdi-information"
              class="mb-6"
            >
              <div class="text-body-1">
                <strong>Guest Reservation System</strong><br>
                Complete the form below to request facility reservation. No account required.
              </div>
            </v-alert>

            <v-form ref="guestForm" v-model="isFormValid" @submit.prevent="submitGuestRequest">
              <v-row>
                <!-- Event Details Section -->
                <v-col cols="12">
                  <div class="text-h6 text-primary mb-4">
                    <v-icon class="mr-2">mdi-calendar-star</v-icon>
                    Event Details
                  </div>
                </v-col>
                
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="guestRequest.eventName"
                    label="Event Name"
                    :rules="validationRules.required"
                    variant="outlined"
                    prepend-inner-icon="mdi-format-title"
                    placeholder="e.g., Birthday Party, Community Meeting"
                  />
                </v-col>
                
                <v-col cols="12" md="6">
                  <v-select
                    v-model="guestRequest.facility"
                    :items="availableFacilities"
                    label="Requested Facility"
                    :rules="validationRules.required"
                    variant="outlined"
                    prepend-inner-icon="mdi-home-city"
                  />
                </v-col>
                
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="guestRequest.startDateTime"
                    label="Event Start"
                    type="datetime-local"
                    :rules="[...validationRules.required, validationRules.futureDate]"
                    variant="outlined"
                    prepend-inner-icon="mdi-calendar-start"
                  />
                </v-col>
                
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="guestRequest.endDateTime"
                    label="Event End"
                    type="datetime-local"
                    :rules="[...validationRules.required, validationRules.endAfterStart]"
                    variant="outlined"
                    prepend-inner-icon="mdi-calendar-end"
                  />
                </v-col>
                
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="guestRequest.expectedAttendees"
                    label="Expected Attendees"
                    type="number"
                    min="1"
                    :rules="[...validationRules.required, validationRules.positiveNumber]"
                    variant="outlined"
                    prepend-inner-icon="mdi-account-group"
                  />
                </v-col>
                
                <v-col cols="12" md="6">
                  <v-select
                    v-model="guestRequest.eventType"
                    :items="eventTypes"
                    label="Event Type"
                    :rules="validationRules.required"
                    variant="outlined"
                    prepend-inner-icon="mdi-tag"
                  />
                </v-col>
                
                <v-col cols="12">
                  <v-textarea
                    v-model="guestRequest.description"
                    label="Event Description/Purpose"
                    :rules="validationRules.required"
                    variant="outlined"
                    rows="3"
                    prepend-inner-icon="mdi-text-box"
                    placeholder="Describe your event, its purpose, and any special requirements..."
                  />
                </v-col>

                <!-- Contact Information Section -->
                <v-col cols="12">
                  <div class="text-h6 text-primary mb-4 mt-4">
                    <v-icon class="mr-2">mdi-account-circle</v-icon>
                    Contact Information
                  </div>
                </v-col>
                
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="guestRequest.contactName"
                    label="Your Full Name"
                    :rules="validationRules.required"
                    variant="outlined"
                    prepend-inner-icon="mdi-account"
                  />
                </v-col>
                
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="guestRequest.contactPhone"
                    label="Contact Phone Number"
                    :rules="[...validationRules.required, validationRules.phone]"
                    variant="outlined"
                    prepend-inner-icon="mdi-phone"
                    placeholder="e.g., +63 912 345 6789"
                  />
                </v-col>
                
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="guestRequest.contactEmail"
                    label="Email Address (Optional)"
                    :rules="validationRules.email"
                    variant="outlined"
                    prepend-inner-icon="mdi-email"
                    placeholder="your.email@example.com"
                  />
                </v-col>
                
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="guestRequest.organization"
                    label="Organization/Group (Optional)"
                    variant="outlined"
                    prepend-inner-icon="mdi-domain"
                    placeholder="Company, Club, or Organization name"
                  />
                </v-col>

                <!-- Submit Section -->
                <v-col cols="12" class="text-center mt-6">
                  <v-btn
                    type="submit"
                    color="primary"
                    size="x-large"
                    :loading="isSubmitting"
                    :disabled="!isFormValid"
                    rounded="xl"
                    class="px-12 py-4"
                    elevation="4"
                  >
                    <v-icon start size="large">mdi-send</v-icon>
                    Submit Reservation Request
                  </v-btn>
                  
                  <div class="text-caption text-grey-darken-1 mt-3">
                    By submitting, you agree to our facility use policies
                  </div>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Success Dialog -->
    <v-dialog v-model="showSuccessDialog" max-width="600" persistent>
      <v-card rounded="xl">
        <v-card-title class="text-center pa-6 bg-success">
          <v-icon size="64" color="white" class="mb-3">mdi-check-circle-outline</v-icon>
          <div class="text-h4 text-white">Request Submitted!</div>
        </v-card-title>
        <v-card-text class="pa-6 text-center">
          <div class="text-h6 mb-4">Thank you for your reservation request</div>
          <p class="text-body-1 mb-4">
            Your event reservation request has been successfully submitted and assigned reference number:
          </p>
          <v-chip
            color="primary"
            size="large"
            variant="elevated"
            class="mb-4"
          >
            <v-icon start>mdi-tag</v-icon>
            REF-{{ submissionReference }}
          </v-chip>
          <p class="text-body-2 text-grey-darken-1">
            Our team will review your request and contact you at 
            <strong>{{ guestRequest.contactPhone }}</strong> within 24-48 hours.
          </p>
          <v-alert
            color="info"
            variant="tonal"
            icon="mdi-information"
            class="mt-4 text-left"
          >
            Please keep this reference number for your records. You may need it when following up on your request.
          </v-alert>
        </v-card-text>
        <v-card-actions class="justify-center pb-6">
          <v-btn
            color="primary"
            variant="elevated"
            size="large"
            @click="submitAnotherRequest"
            class="mr-3"
          >
            <v-icon start>mdi-plus</v-icon>
            Submit Another Request
          </v-btn>
          <v-btn
            color="grey-darken-1"
            variant="outlined"
            size="large"
            @click="returnToHomepage"
          >
            <v-icon start>mdi-home</v-icon>
            Return to Homepage
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Loading Overlay -->
    <v-overlay
      :model-value="isSubmitting"
      class="align-center justify-center"
      contained
    >
      <div class="text-center">
        <v-progress-circular
          color="primary"
          indeterminate
          size="80"
          width="6"
          class="mb-4"
        />
        <div class="text-h6 text-white">Submitting your request...</div>
        <div class="text-body-2 text-grey-lighten-1">Please wait</div>
      </div>
    </v-overlay>
  </v-container>
</template>

<script>
import axios from 'axios'
import { apiConfig } from '@/config/api.js'

export default {
  name: 'GuestEventsPage',
  data() {
    return {
      isFormValid: false,
      isSubmitting: false,
      showSuccessDialog: false,
      submissionReference: '',
      apiBaseUrl: apiConfig.baseURL,
      
      guestRequest: {
        eventName: '',
        facility: '',
        startDateTime: '',
        endDateTime: '',
        expectedAttendees: 1,
        eventType: '',
        description: '',
        contactName: '',
        contactPhone: '',
        contactEmail: '',
        organization: ''
      },

      availableFacilities: [
        'Barangay Hall',
        'Basketball Court',
        'Covered Court',
        'Multi-purpose Area',
        'Community Center',
        'Auditorium',
        'Meeting Room A',
        'Meeting Room B', 
        'Training Room',
        'Executive Conference Room',
        'Outdoor Pavilion',
        'Recreation Area'
      ],

      eventTypes: [
        'Community Meeting',
        'Cultural Event',
        'Sports Activity',
        'Educational Workshop',
        'Religious Gathering',
        'Birthday Party',
        'Wedding Reception',
        'Corporate Event',
        'Fundraising Activity',
        'Health Program',
        'Government Function',
        'Other'
      ],

      validationRules: {
        required: [
          value => !!value || 'This field is required'
        ],
        email: [
          value => !value || /.+@.+\..+/.test(value) || 'Please enter a valid email address'
        ],
        phone: [
          value => {
            if (!value) return 'Phone number is required'
            const pattern = /^[+]?[0-9\s\-()]{10,}$/
            return pattern.test(value) || 'Please enter a valid phone number'
          }
        ],
        positiveNumber: [
          value => {
            if (!value) return 'This field is required'
            const num = parseInt(value)
            return (num > 0 && num <= 1000) || 'Must be between 1 and 1000'
          }
        ],
        futureDate: value => {
          if (!value) return 'Date and time is required'
          const selectedDate = new Date(value)
          const now = new Date()
          return selectedDate > now || 'Event must be scheduled for a future date'
        },
        endAfterStart: value => {
          if (!value || !this.guestRequest.startDateTime) return 'End time is required'
          const startDate = new Date(this.guestRequest.startDateTime)
          const endDate = new Date(value)
          return endDate > startDate || 'Event end time must be after start time'
        }
      }
    }
  },
  methods: {
    async submitGuestRequest() {
      const { valid } = await this.$refs.guestForm.validate()
      if (!valid) return

      this.isSubmitting = true
      try {
        // Generate a simple reference number
        this.submissionReference = 'GR' + Date.now().toString().slice(-6)
        
        const requestData = {
          event_name: this.guestRequest.eventName,
          facility: this.guestRequest.facility,
          start_datetime: this.guestRequest.startDateTime,
          end_datetime: this.guestRequest.endDateTime,
          expected_attendees: parseInt(this.guestRequest.expectedAttendees),
          event_type: this.guestRequest.eventType,
          purpose: this.guestRequest.description,
          requested_by: this.guestRequest.contactName,
          contact_number: this.guestRequest.contactPhone,
          contact_email: this.guestRequest.contactEmail || '',
          organization: this.guestRequest.organization || '',
          reference_number: this.submissionReference
        }

        await axios.post(`${this.apiBaseUrl}/reservations/create_guest_reservation.php`, requestData, {
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        })

        this.showSuccessDialog = true
      } catch (error) {
        console.error('Error submitting guest request:', error)
        const errorMessage = error.response?.data?.message || 'Unable to submit request at this time. Please try again later.'
        
        // Show error notification (if you have a notification system)
        if (this.$toast?.error) {
          this.$toast.error(errorMessage)
        } else {
          alert('Error: ' + errorMessage)
        }
      } finally {
        this.isSubmitting = false
      }
    },

    submitAnotherRequest() {
      this.resetGuestForm()
      this.showSuccessDialog = false
    },

    returnToHomepage() {
      this.$router.push({ name: 'GuestHomepage' })
    },

    resetGuestForm() {
      this.guestRequest = {
        eventName: '',
        facility: '',
        startDateTime: '',
        endDateTime: '',
        expectedAttendees: 1,
        eventType: '',
        description: '',
        contactName: '',
        contactPhone: '',
        contactEmail: '',
        organization: ''
      }
      this.submissionReference = ''
      this.$refs.guestForm?.resetValidation()
    }
  }
}
</script>

<style scoped>
.v-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
}

.guest-form-card {
  background: rgba(255, 255, 255, 0.98);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.text-primary {
  background: linear-gradient(45deg, #1976d2, #42a5f5);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.v-form {
  width: 100%;
}

.v-text-field, .v-select, .v-textarea {
  margin-bottom: 8px;
}

.v-btn {
  text-transform: none;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.v-alert {
  border-radius: 12px;
}

.v-chip {
  font-size: 1.1rem;
  font-weight: bold;
  padding: 16px 24px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .v-container {
    padding: 10px;
  }
  
  .guest-form-card {
    margin: 0;
  }
  
  .text-h3 {
    font-size: 1.8rem !important;
  }
  
  .text-h6 {
    font-size: 1.1rem !important;
  }
}

/* Animation for form sections */
.v-row {
  animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
