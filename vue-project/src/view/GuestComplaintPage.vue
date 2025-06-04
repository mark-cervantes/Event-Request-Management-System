<template>
  <v-app>
    <!-- Main content -->
    <v-main class="bg-grey-lighten-3">
      <v-container fluid class="px-6 py-8">
        <!-- Header Section -->
        <v-row justify="center" class="mb-6">
          <v-col cols="12" md="10" lg="8">
            <div class="text-center mb-8">
              <div class="d-flex align-center justify-center mb-4">
                <div class="d-flex align-center justify-center mb-2" style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-radius: 50%; width: 80px; height: 80px; box-shadow: 0 8px 32px rgba(59, 130, 246, 0.3);">
                  <v-icon color="white" size="40">mdi-file-document-outline</v-icon>
                </div>
              </div>
              <h1 class="text-h3 font-weight-bold mb-2" style="color: #1e40af; font-family: 'Playfair Display', serif;">
                Submit Complaint
              </h1>
              <div class="mx-auto mb-4" style="width: 100px; height: 4px; background: linear-gradient(90deg, #3b82f6, #60a5fa); border-radius: 8px;"></div>
              <p class="text-h6 text-grey-darken-1" style="font-family: 'Poppins', sans-serif;">
                Voice your concerns through our digital complaint system
              </p>
            </div>
          </v-col>
        </v-row>

        <!-- Form Section -->
        <v-row justify="center">
          <v-col cols="12" md="10" lg="8">
            <v-card class="elevation-8 rounded-xl pa-6" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border: 1px solid rgba(59, 130, 246, 0.1);">
              <!-- Success/Error Messages -->
              <v-alert
                v-if="successMessage"
                class="mb-6"
                dismissible
                type="success"
                prominent
                border="start"
                @click:close="successMessage = ''"
              >
                <template #prepend>
                  <v-icon>mdi-check-circle</v-icon>
                </template>
                {{ successMessage }}
              </v-alert>

              <v-alert
                v-if="errorMessage"
                class="mb-6"
                dismissible
                type="error"
                prominent
                border="start"
                @click:close="errorMessage = ''"
              >
                <template #prepend>
                  <v-icon>mdi-alert-circle</v-icon>
                </template>
                {{ errorMessage }}
              </v-alert>

              <v-form ref="form" v-model="valid">
                <!-- Case Information -->
                <div class="mb-6">
                  <h3 class="text-h5 font-weight-bold mb-4" style="color: #1e40af;">
                    <v-icon class="mr-2" color="blue-darken-3">mdi-information-outline</v-icon>
                    Case Information
                  </h3>
                  <v-divider class="mb-4" style="border-color: #3b82f6; border-width: 2px;"></v-divider>
                  
                  <v-row>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="caseNo"
                        label="Barangay Case No."
                        variant="outlined"
                        readonly
                        prepend-inner-icon="mdi-tag-outline"
                        :rules="[rules.required]"
                        style="background-color: #f1f5f9;"
                      />
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-select
                        v-model="forType"
                        :items="forOptions"
                        label="Type of Case"
                        variant="outlined"
                        prepend-inner-icon="mdi-format-list-bulleted"
                        required
                        :rules="[rules.required]"
                      />
                    </v-col>
                  </v-row>
                </div>

                <!-- Complainant Information -->
                <div class="mb-6">
                  <h3 class="text-h5 font-weight-bold mb-4" style="color: #1e40af;">
                    <v-icon class="mr-2" color="blue-darken-3">mdi-account-outline</v-icon>
                    Complainant Information
                  </h3>
                  <v-divider class="mb-4" style="border-color: #3b82f6; border-width: 2px;"></v-divider>
                  
                  <v-row>
                    <v-col cols="12" md="4">
                      <v-text-field
                        v-model="complainant.fullName"
                        label="Full Name"
                        variant="outlined"
                        prepend-inner-icon="mdi-account"
                        required
                        :rules="[rules.required]"
                      />
                    </v-col>
                    <v-col cols="12" md="4">
                      <v-text-field
                        v-model="complainant.address"
                        label="Complete Address"
                        variant="outlined"
                        prepend-inner-icon="mdi-map-marker"
                        required
                        :rules="[rules.required]"
                      />
                    </v-col>
                    <v-col cols="12" md="4">
                      <v-text-field
                        v-model="complainant.cellphone"
                        label="Cellphone Number"
                        variant="outlined"
                        prepend-inner-icon="mdi-phone"
                        required
                        :rules="[rules.required, rules.phone]"
                      />
                    </v-col>
                  </v-row>
                </div>

                <!-- VS Divider -->
                <v-row align="center" class="my-6">
                  <v-col cols="5">
                    <v-divider style="border-color: #3b82f6; border-width: 1px;"></v-divider>
                  </v-col>
                  <v-col class="text-center" cols="2">
                    <div class="text-h6 font-weight-bold" style="color: #1e40af;">VS</div>
                  </v-col>
                  <v-col cols="5">
                    <v-divider style="border-color: #3b82f6; border-width: 1px;"></v-divider>
                  </v-col>
                </v-row>

                <!-- Respondent Information -->
                <div class="mb-6">
                  <h3 class="text-h5 font-weight-bold mb-4" style="color: #1e40af;">
                    <v-icon class="mr-2" color="blue-darken-3">mdi-account-multiple-outline</v-icon>
                    Respondent Information
                  </h3>
                  <v-divider class="mb-4" style="border-color: #3b82f6; border-width: 2px;"></v-divider>
                  
                  <v-row>
                    <v-col cols="12" md="4">
                      <v-text-field
                        v-model="respondent.fullName"
                        label="Full Name"
                        variant="outlined"
                        prepend-inner-icon="mdi-account"
                        required
                        :rules="[rules.required]"
                      />
                    </v-col>
                    <v-col cols="12" md="4">
                      <v-text-field
                        v-model="respondent.address"
                        label="Complete Address"
                        variant="outlined"
                        prepend-inner-icon="mdi-map-marker"
                        required
                        :rules="[rules.required]"
                      />
                    </v-col>
                    <v-col cols="12" md="4">
                      <v-text-field
                        v-model="respondent.cellphone"
                        label="Cellphone Number"
                        variant="outlined"
                        prepend-inner-icon="mdi-phone"
                        required
                        :rules="[rules.required, rules.phone]"
                      />
                    </v-col>
                  </v-row>
                </div>

                <!-- Complaint Details -->
                <div class="mb-6">
                  <h3 class="text-h5 font-weight-bold mb-4" style="color: #1e40af;">
                    <v-icon class="mr-2" color="blue-darken-3">mdi-text-box-outline</v-icon>
                    Complaint Details
                  </h3>
                  <v-divider class="mb-4" style="border-color: #3b82f6; border-width: 2px;"></v-divider>
                  
                  <div class="mb-4 pa-4 rounded-lg" style="background-color: #f1f5f9; border-left: 4px solid #3b82f6;">
                    <p class="text-body-1 mb-0" style="color: #475569;">
                      I/We hereby complain against above-named respondent/s for violating my/our rights and interest in the following manner:
                    </p>
                  </div>
                  
                  <v-textarea
                    v-model="complaintDetails"
                    label="Describe your complaint in detail"
                    variant="outlined"
                    prepend-inner-icon="mdi-text"
                    rows="4"
                    required
                    :rules="[rules.required]"
                    hint="Please provide as much detail as possible about the incident"
                    persistent-hint
                  />
                </div>

                <!-- Evidence Upload -->
                <div class="mb-6">
                  <h3 class="text-h5 font-weight-bold mb-4" style="color: #1e40af;">
                    <v-icon class="mr-2" color="blue-darken-3">mdi-paperclip</v-icon>
                    Evidence & Supporting Documents
                  </h3>
                  <v-divider class="mb-4" style="border-color: #3b82f6; border-width: 2px;"></v-divider>
                  
                  <v-file-input
                    v-model="evidenceFiles"
                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                    counter
                    label="Attach Evidence or Files"
                    variant="outlined"
                    multiple
                    prepend-inner-icon="mdi-attachment"
                    show-size
                    hint="Accepted formats: JPG, PNG, PDF, DOC, DOCX (Max 10MB per file)"
                    persistent-hint
                  />
                  
                  <div v-if="evidenceFiles && evidenceFiles.length" class="mt-4">
                    <div class="text-caption font-weight-medium mb-2" style="color: #475569;">Selected files:</div>
                    <div class="d-flex flex-wrap gap-2">
                      <v-chip
                        v-for="(file, i) in evidenceFiles"
                        :key="i"
                        color="blue-darken-2"
                        variant="flat"
                        size="small"
                      >
                        <v-icon start>mdi-file</v-icon>
                        {{ file.name }}
                      </v-chip>
                    </div>
                  </div>
                </div>

                <!-- Date Information -->
                <div class="mb-6">
                  <h3 class="text-h5 font-weight-bold mb-4" style="color: #1e40af;">
                    <v-icon class="mr-2" color="blue-darken-3">mdi-calendar-outline</v-icon>
                    Date Information
                  </h3>
                  <v-divider class="mb-4" style="border-color: #3b82f6; border-width: 2px;"></v-divider>
                  
                  <v-row>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="dateOfIncident"
                        label="Date of Incident"
                        variant="outlined"
                        type="date"
                        prepend-inner-icon="mdi-calendar-alert"
                        :rules="[dateRequired]"
                        clearable
                      />
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="dateSubmitted"
                        label="Date Submitted"
                        variant="outlined"
                        type="date"
                        prepend-inner-icon="mdi-calendar-check"
                        :rules="[dateRequired]"
                        clearable
                      />
                    </v-col>
                  </v-row>
                </div>

                <!-- Action Buttons -->
                <v-row class="mt-8" justify="center">
                  <v-col cols="auto">
                    <v-btn
                      size="large"
                      color="grey-darken-1"
                      variant="outlined"
                      :disabled="loading"
                      @click="resetForm"
                      class="mr-4"
                    >
                      <v-icon start>mdi-refresh</v-icon>
                      Reset Form
                    </v-btn>
                  </v-col>
                  <v-col cols="auto">
                    <v-btn
                      size="large"
                      color="blue-darken-2"
                      variant="flat"
                      :loading="loading"
                      @click="submit"
                    >
                      <v-icon start>mdi-send</v-icon>
                      Submit Complaint
                    </v-btn>
                  </v-col>
                </v-row>
              </v-form>
            </v-card>
          </v-col>
        </v-row>

        <!-- Success Dialog -->
        <v-dialog v-model="showSuccessDialog" max-width="500" persistent>
          <v-card class="rounded-xl">
            <v-card-title class="text-h5 font-weight-bold pa-6" style="background: linear-gradient(135deg, #3b82f6, #1e40af); color: white;">
              <v-icon class="mr-2" color="white">mdi-check-circle</v-icon>
              Complaint Submitted Successfully
            </v-card-title>
            <v-card-text class="pa-6">
              <div class="text-center">
                <div class="mb-4">
                  <v-icon size="64" color="green" class="mb-2">mdi-check-circle-outline</v-icon>
                </div>
                <p class="text-h6 font-weight-medium mb-2">Your complaint has been recorded!</p>
                <div class="pa-3 rounded-lg mb-4" style="background-color: #f0f9ff; border: 1px solid #3b82f6;">
                  <p class="text-body-1 mb-1"><strong>Reference Number:</strong></p>
                  <p class="text-h6 font-weight-bold" style="color: #1e40af;">{{ caseNo }}</p>
                </div>
                <p class="text-body-2 text-grey-darken-1">
                  Please keep this reference number for your records. You will be contacted regarding the status of your complaint.
                </p>
              </div>
            </v-card-text>
            <v-card-actions class="pa-6 pt-0">
              <v-spacer />
              <v-btn
                color="blue-darken-2"
                variant="outlined"
                @click="handleSubmitAnother"
              >
                Submit Another
              </v-btn>
              <v-btn
                color="blue-darken-2"
                variant="flat"
                @click="() => { showSuccessDialog = false; router.push('/guest/guest-homepage') }"
              >
                Return Home
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { apiConfig } from '@/config/api.js'

const router = useRouter()

// Form validation
const form = ref(null)
const valid = ref(false)
const loading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

// Form fields
const caseNo = ref('')
const forType = ref('')
const forOptions = [
  'Neighbor Dispute', 
  'Verbal Abuse', 
  'Battery/Assault', 
  'Theft', 
  'Non-payment of Debt', 
  'Harassment / Threats', 
  'Trespassing', 
  'Property Damage', 
  'Boundary Dispute',
  'Disturbance of Peace',
  'Improper Waste Disposal',
  'Unauthorized Cutting of Trees/Plants',
  'Parking Dispute',
  'Illegal Eviction Attempt',
  'Cyber Defamation',
  'Vandalism'
]

const complainant = ref({
  fullName: '',
  address: '',
  cellphone: '',
})

const respondent = ref({
  fullName: '',
  address: '',
  cellphone: '',
})

const evidenceFiles = ref([])
const complaintDetails = ref('')
const dateSubmitted = ref(null)
const dateOfIncident = ref(null)
const showSuccessDialog = ref(false)

// Validation rules
const rules = {
  required: value => !!value || 'This field is required',
  phone: value => {
    const pattern = /^[0-9+\-\s()]+$/
    return pattern.test(value) || 'Invalid phone number format'
  },
}

const dateRequired = value => !!value || 'Date is required'

// Reset form function
function resetForm() {
  const currentCaseNo = caseNo.value

  if (form.value) {
    form.value.reset()
  }

  // Restore case number
  caseNo.value = currentCaseNo

  // Clear messages
  successMessage.value = ''
  errorMessage.value = ''
}

// Submit function
async function submit() {
  successMessage.value = ''
  errorMessage.value = ''

  // Validate the form before submitting
  if (!form.value.validate()) {
    errorMessage.value = 'Please fill in all required fields correctly.'
    return
  }

  loading.value = true

  try {
    // Prepare form data object matching your PHP expected fields
    const payload = {
      brngy_case_no: caseNo.value,
      case_type: forType.value,
      complainant_fullname: complainant.value.fullName,
      complainant_address: complainant.value.address,
      complainant_cellphone: complainant.value.cellphone,
      respondent_fullname: respondent.value.fullName,
      respondent_address: respondent.value.address,
      respondent_cellphone: respondent.value.cellphone,
      complaint_description: complaintDetails.value,
      date_of_incident: dateOfIncident.value,
      date_submitted: dateSubmitted.value,
      // For attachment_file, you need to handle file upload differently
      attachment_file: null,
    }

    // Send POST request to your PHP API endpoint
    const response = await axios.post(`${apiConfig.baseURL}/complaints/create_complaint.php`, payload)

    if (response.status === 201) {
      successMessage.value = response.data.message || 'Complaint submitted successfully!'
      showSuccessDialog.value = true
    } else {
      errorMessage.value = response.data.message || 'Failed to submit complaint.'
    }
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'An error occurred while submitting your complaint.'
  } finally {
    loading.value = false
  }
}

// Fetch next case number
async function fetchNextCaseNo() {
  try {
    const response = await axios.get(`${apiConfig.baseURL}/complaints/get_next_case_no.php`)
    caseNo.value = response.data.next_case_no || ''
  } catch (error) {
    console.error('Failed to fetch next case number:', error)
    caseNo.value = 'TEMP-' + Date.now() // Fallback case number
  }
}

// Handle submit another
async function handleSubmitAnother() {
  await fetchNextCaseNo()
  resetForm()
  showSuccessDialog.value = false
}

onMounted(() => {
  fetchNextCaseNo()
  // Set current date as default for date submitted
  const today = new Date().toISOString().split('T')[0]
  dateSubmitted.value = today
})
</script>

<style scoped>
.v-card {
  box-shadow: 0 10px 30px rgba(30, 64, 175, 0.1) !important;
}

.v-btn {
  text-transform: none !important;
  font-weight: 600 !important;
}

.v-text-field .v-field,
.v-select .v-field,
.v-textarea .v-field,
.v-file-input .v-field {
  border-radius: 12px !important;
}

.v-text-field--focused .v-field,
.v-select--focused .v-field,
.v-textarea--focused .v-field,
.v-file-input--focused .v-field {
  border-color: #3b82f6 !important;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2) !important;
}

.gap-2 {
  gap: 8px;
}
</style>
