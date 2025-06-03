<template>
  <v-app>
  
    <!-- Main content-->
    <v-main >
      <v-container fluid>
        <!-- Success/Error Messages -->
        <v-alert
          v-if="successMessage"
          class="mb-4"
          dismissible
          type="success"
          @click:close="successMessage = ''"
        >
          {{ successMessage }}
        </v-alert>

        <v-alert
          v-if="errorMessage"
          class="mb-4"
          dismissible
          type="error"
          @click:close="errorMessage = ''"
        >
          {{ errorMessage }}
        </v-alert>

        <v-form ref="form" v-model="valid">
          <v-row>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="caseNo"
                dense
                label="Brgy. Case No."
                outlined
                readonly
                :rules="[rules.required]"
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-select
                v-model="forType"
                dense
                :items="forOptions"
                label="Type of case"
                outlined
                required
                :rules="[rules.required]"
              />
            </v-col>
          </v-row>

          <v-divider class="my-4" />
          <!-- Start of Complainant-->
          <v-row>
            <v-col cols="12">
              <strong>(Complainant)</strong>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" md="4">
              <v-text-field
                v-model="complainant.fullName"
                dense
                label="Full Name"
                outlined
                required
                :rules="[rules.required]"
              />
            </v-col>
            <v-col cols="12" md="4">
              <v-text-field
                v-model="complainant.address"
                dense
                label="Complete Address"
                outlined
                required
                :rules="[rules.required]"
              />
            </v-col>
            <v-col cols="12" md="4">
              <v-text-field
                v-model="complainant.cellphone"
                dense
                label="Cellphone Numbers"
                outlined
                required
                :rules="[rules.required, rules.phone]"
              />
            </v-col>
          </v-row>

          <v-row align="center" class="my-4">
            <v-col cols="5"><v-divider /></v-col>
            <v-col class="text-center" cols="2">- Vs -</v-col>
            <v-col cols="5"><v-divider /></v-col>
          </v-row>
          <!-- START OF RESPONDENT -->
          <v-row>
            <v-col cols="12">
              <strong>(Respondent)</strong>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" md="4">
              <v-text-field
                v-model="respondent.fullName"
                dense
                label="Full Name"
                outlined
                required
                :rules="[rules.required]"
              />
            </v-col>
            <v-col cols="12" md="4">
              <v-text-field
                v-model="respondent.address"
                dense
                label="Complete Address"
                outlined
                required
                :rules="[rules.required]"
              />
            </v-col>
            <v-col cols="12" md="4">
              <v-text-field
                v-model="respondent.cellphone"
                dense
                label="Cellphone Numbers"
                outlined
                required
                :rules="[rules.required, rules.phone]"
              />
            </v-col>
          </v-row>
          <!-- Complaint Description -->
          <v-row justify="center">
            <v-col class="text-center" cols="12">
              <h5 class="font-weight-bold">COMPLAINT</h5>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12">
              <div>
                I/We hereby complain against above-named respondent/s for violating my/our rights and interest in the following manner:
              </div>
              <v-textarea
                v-model="complaintDetails"
                class="mt-2"
                outlined
                required
                rows="3"
                :rules="[rules.required]"
              />
            </v-col>
          </v-row>

          <!-- Attachment File -->
          <v-row class="mt-4">
            <v-col cols="12">
              <v-file-input
                v-model="evidenceFiles"
                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                :counter="evidenceFiles ? evidenceFiles.length : 0"
                label="Attach Evidence or Files"
                multiple
                prepend-icon="mdi-paperclip"
                show-size
              />
              <div v-if="evidenceFiles && evidenceFiles.length" class="mt-2">
                <div class="text-caption font-italic">Selected files:</div>
                <v-chip
                  v-for="(file, i) in evidenceFiles"
                  :key="i"
                  class="ma-1"
                  color="blue-grey-darken-2"
                  outlined
                  small
                  text-color="white"
                >
                  {{ file.name }}
                </v-chip>
              </div>
            </v-col>
          </v-row>
          <!-- Date of Incident -->
          <v-row>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="dateOfIncident"
                clearable
                label="Date of Incident"
                outlined
                :rules="[dateRequired]"
                type="date"
              />
            </v-col>

            <!-- Date Submitted -->
            <v-col cols="12" md="6">
              <v-text-field
                v-model="dateSubmitted"
                clearable
                label="Date Submitted"
                outlined
                :rules="[dateRequired]"
                type="date"
              />
            </v-col>
          </v-row>
          <!-- BUTTONS RESET SUBMIT PRINT-->
          <v-row class="mt-4" justify="center">
            <v-btn
              class="mr-2"
              color="grey-darken-2"
              :disabled="loading"
              variant="flat"
              @click="resetForm"
            >
              <v-icon start>mdi-refresh</v-icon> Reset
            </v-btn>
            <v-btn
              class="mr-2"
              color="blue-darken-2"
              :loading="loading"
              variant="flat"
              @click="submit"
            >
              <v-icon start>mdi-send</v-icon> Submit
            </v-btn>
            <v-btn
              color="green-darken-2"
              :disabled="loading"
              variant="flat"
              @click="printForm"
            >
              <v-icon start>mdi-printer</v-icon> Print
            </v-btn>
          </v-row>
        </v-form>

        <v-dialog v-model="showSuccessDialog" max-width="400">
          <v-card>
            <v-card-title class="headline">Complaint Submitted</v-card-title>
            <v-card-text>
              Would you like to submit another complaint or go back to the home page?
            </v-card-text>
            <v-card-actions>
              <v-spacer />
              <v-btn color="primary" text @click="handleSubmitAnother">
                Submit Another
              </v-btn>
              <v-btn color="secondary" text @click="() => { showSuccessDialog = false; router.push('/user/user-homepage') }">
                Go to Home
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-container />
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
  import { onMounted, ref } from 'vue'
  import axios from 'axios'
  import { useRouter } from 'vue-router'

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
  const forOptions = ['Neighbor Dispute', 'Verbal Abuse', 'Battery/Assault', 'Theft' , 'Non-payment of Debt', 'Harassment / Threats', 'Tresspassing', 'Property Damage', 'Boundary Dispute','Disturbance of Peace','Improper Waste Disposal','Unauthorized Cutting of Trees/Plants','Parking Dispute','Illegal Eviction Attempt','Cyber Defamation','Vandalism']

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


  // Validation rules
  const rules = {
    required: value => !!value || 'This field is required',
    phone: value => {
      const pattern = /^[0-9+\-\s()]+$/
      return pattern.test(value) || 'Invalid phone number format'
    },
  }


  // Reset form function
  function resetForm () {
    const currentCaseNo = caseNo.value;

    if (form.value) {
      form.value.reset(); // resets all inputs and validation state
    }

    // Restore excluded field
    caseNo.value = currentCaseNo;

    // Clear messages
    successMessage.value = '';
    errorMessage.value = '';
  }


  // Print form function
  function printForm () {
    window.print()
  }

  // Date fields
  const dateSubmitted = ref(null)
  const dateOfIncident = ref(null)

  // Validation rule
  const dateRequired = value => !!value || 'Date is required'
  const showSuccessDialog = ref(false)

  //SUBMIT
  async function submit () {
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
        // For attachment_file, you need to handle file upload differently (see note below)
        attachment_file: null,
      }

      // Send POST request to your PHP API endpoint
      const response = await axios.post('http://localhost/sta_cruz_prj/api/endpoints/complaints/create_complaint.php', payload)

      if (response.status === 201) {
        successMessage.value = response.data.message || 'Complaint Added!'
        resetForm()
        showSuccessDialog.value = true
      } else {
        errorMessage.value = response.data.message || 'Failed to add complaint.'
      }
    } catch (error) {
      errorMessage.value = error.response?.data?.message || 'An error occurred while submitting.'
    } finally {
      loading.value = false
    }
  }


  async function fetchNextCaseNo () {
    try {
      const response = await axios.get('http://localhost/sta_cruz_prj/api/endpoints/complaints/get_next_case_no.php')
      caseNo.value = response.data.next_case_no || ''
    } catch (error) {
      console.error('Failed to fetch next case number:', error)
    }
  }

  onMounted(() => {
    fetchNextCaseNo()
  })


  async function handleSubmitAnother () {
    await fetchNextCaseNo() // Refresh the case number from backend
    resetForm() // Reset all other form fields
    showSuccessDialog.value = false // Close the dialog
  }


</script>

<style scoped>
.custom-center-btn{
  display: flex;
  justify-content: center;
  align-items: center
}
</style>
