<!-- eslint-disable vue/valid-v-slot -->
<template>
  <v-app>
    <!-- Main content-->
    <v-main style="background-color: lightgray;">
      <v-container class="pa-4" fluid style=" min-height: 100vh;">
        <!-- Statistic Cards -->
        <v-row>
          <v-col cols="12" md="3">
            <v-card class="pa-4" style=" color: #020302;">
              <v-row align="center">
                <v-col cols="3">
                  <v-icon color="blue" size="36">mdi-file-document</v-icon>
                </v-col>
                <v-col>
                  <div class="text-caption">Total Complaints</div>
                  <div class="text-h5 font-weight-bold">{{ totalComplaintsCount }}</div>
                </v-col>
              </v-row>
            </v-card>
          </v-col>
          <v-col cols="12" md="3">
            <v-card class="pa-4" style=" color: #020302;">
              <v-row align="center">
                <v-col cols="3">
                  <v-icon color="green" size="36">mdi-check-circle</v-icon>
                </v-col>
                <v-col>
                  <div class="text-caption">Resolved Cases</div>
                  <div class="text-h5 font-weight-bold">{{ resolvedCases }}</div>
                </v-col>
              </v-row>
            </v-card>
          </v-col>
          <v-col cols="12" md="3">
            <v-card class="pa-4" style=" color: #020302;">
              <v-row align="center">
                <v-col cols="3">
                  <v-icon color="yellow" size="36">mdi-progress-clock</v-icon>
                </v-col>
                <v-col>
                  <div class="text-caption">In Progress Cases</div>
                  <div class="text-h5 font-weight-bold">{{ inProgressCases }}</div>
                </v-col>
              </v-row>
            </v-card>
          </v-col>
          <v-col cols="12" md="3">
            <v-card class="pa-4" style=" color: #020302;">
              <v-row align="center">
                <v-col cols="3">
                  <v-icon color="red" size="36">mdi-alert-circle</v-icon>
                </v-col>
                <v-col>
                  <div class="text-caption">Pending Cases</div>
                  <div class="text-h5 font-weight-bold">{{ pendingCases }}</div>
                </v-col>
              </v-row>
            </v-card>
          </v-col>
        </v-row>

        <!-- Complaint Statistics Chart -->
        <v-card class="pa-6 mt-4" style="background-color: #fff; color: #020302;">
          <div class="text-h6 mb-2">Complaint Statistics</div>
          <div class="d-flex flex-column align-center">
            <div class="mb-2">
              <v-chip class="mr-2" color="teal" label size="small">Complaints</v-chip>
              <v-chip class="mr-2" color="#29d657" label size="small">Resolved</v-chip>

              <v-chip
                class="mr-2"
                color="yellow"
                label
                size="small"
                style="color:#333;"
              >In Progress</v-chip>
              <v-chip color="red" label size="small">Pending</v-chip>

            </div>
            <div style="max-width: 340px;">
              <ComplaintDonutChart />
            </div>
          </div>
        </v-card>

        <!-- Complaint Records Section -->
        <v-card class="pa-4 mt-4" style=" color: #020302;">
          <h2 class="mb-4 text-white">Complaint Records</h2>
          <v-row align="center" class="mb-2">
            <v-col cols="12" md="4">
              <v-text-field
                v-model="search"
                color="primary"
                dense
                hide-details
                label="Search complaints..."
                prepend-inner-icon="mdi-magnify"
                variant="solo"
              />
            </v-col>
            <v-col cols="6" md="2">
              <v-select
                v-model="statusFilter"
                clearable
                dense
                hide-details
                :items="statusOptions"
                label="All Status"
                variant="solo"
              />
            </v-col>
          </v-row>

          <v-data-table
            v-model:page="page"
            class="elevation-1"
            :headers="headers"
            item-key="brngy_case_no"
            :items="filteredComplaints"
            :items-per-page="itemsPerPage"
          >
            <template #item.status="{ item }">
              <v-chip
                :color="statusColor(item.status)"
                
                
              >
                {{ item.status }}
              </v-chip>
            </template>

            <template #item.actions="{ item }">
              <div class="d-flex ga-2 justify-end">
                <v-btn
                  color="info"
                  icon
                  size="small"
                  :title="'View ' + item.id"
                  @click="viewItem(item)"
                >
                  <v-icon>mdi-eye</v-icon>
                </v-btn>
                <v-btn
                  color="primary"
                  icon
                  size="small"
                  :title="'Edit ' + item.id"
                  @click="editItem(item)"
                >
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
                <v-btn
                  color="success"
                  icon
                  size="small"
                  :title="'Resolve ' + item.id"
                  @click="resolveItem(item)"
                >
                  <v-icon>mdi-check</v-icon>
                </v-btn>
                <v-btn
                  color="error"
                  icon
                  size="small"
                  :title="'Delete ' + item.id"
                  @click="deleteItem(item)"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </div>
            </template>
          </v-data-table>

          <!-- VIEW BUTTON EFFECT -->
          <v-dialog v-model="viewDialog" max-width="800px">
            <v-card>
              <v-card-title>
                Complaint Details
                <v-spacer />
                <v-btn icon @click="viewDialog = false">
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </v-card-title>
              <v-divider />
              <v-card-text>
                <v-row>
                  <v-col cols="12" md="6">
                    <v-text-field
                      dense
                      label="Brgy. Case No."
                      :model-value="selectedComplaint.brngy_case_no"
                      outlined
                      readonly
                    />
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      dense
                      label="Type of case"
                      :model-value="selectedComplaint.case_type"
                      outlined
                      readonly
                    />
                  </v-col>
                </v-row>
                <v-divider class="my-2" />
                <v-row>
                  <v-col cols="12"><strong>(Complainant)</strong></v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" md="4">
                    <v-text-field
                      dense
                      label="Full Name"
                      :model-value="selectedComplaint.complainant_fullname"
                      outlined
                      readonly
                    />
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-text-field
                      dense
                      label="Complete Address"
                      :model-value="selectedComplaint.complainant_address"
                      outlined
                      readonly
                    />
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-text-field
                      dense
                      label="Cellphone Numbers"
                      :model-value="selectedComplaint.complainant_cellphone"
                      outlined
                      readonly
                    />
                  </v-col>
                </v-row>
                <v-row align="center" class="my-2">
                  <v-col cols="5"><v-divider /></v-col>
                  <v-col class="text-center" cols="2">- Vs -</v-col>
                  <v-col cols="5"><v-divider /></v-col>
                </v-row>
                <v-row>
                  <v-col cols="12"><strong>(Respondent)</strong></v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" md="4">
                    <v-text-field
                      dense
                      label="Full Name"
                      :model-value="selectedComplaint.respondent_fullname"
                      outlined
                      readonly
                    />
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-text-field
                      dense
                      label="Complete Address"
                      :model-value="selectedComplaint.respondent_address"
                      outlined
                      readonly
                    />
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-text-field
                      dense
                      label="Cellphone Numbers"
                      :model-value="selectedComplaint.respondent_cellphone"
                      outlined
                      readonly
                    />
                  </v-col>
                </v-row>
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
                      class="mt-2"
                      dense
                      :model-value="selectedComplaint.complaint_description"
                      outlined
                      readonly
                      rows="3"
                    />
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" md="6">
                    <v-text-field
                      dense
                      label="Date of Incident"
                      :model-value="selectedComplaint.date_of_incident"
                      outlined
                      readonly
                    />
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      dense
                      label="Date Submitted"
                      :model-value="selectedComplaint.submitted_date"
                      outlined
                      readonly
                    />
                  </v-col>
                </v-row>
                <!-- Add more fields if needed -->
              </v-card-text>
              <v-card-actions>
                <v-spacer />
                <v-btn color="primary" @click="viewDialog = false">Close</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>


          <!-- EDIT BUTTON DIALOG -->
          <v-dialog v-model="editDialog" max-width="800px" persistent>
            <v-card>
              <v-card-title>
                Edit Complaint
                <v-spacer />
                <v-btn icon @click="editDialog = false">
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </v-card-title>
              <v-divider />
              <v-card-text>
                <v-form ref="editForm" v-model="editValid" lazy-validation>
                  <v-row>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="editComplaint.brngy_case_no"
                        dense
                        label="Brgy. Case No."
                        outlined
                        readonly
                      />
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-select
                        v-model="editComplaint.case_type"
                        dense
                        :items="forOptions"
                        label="Type of Case"
                        outlined
                        :rules="[rules.required]"
                      />
                    </v-col>
                  </v-row>
                  <v-divider class="my-2" />
                  <v-row>
                    <v-col cols="12"><strong>(Complainant)</strong></v-col>
                  </v-row>
                  <v-row>
                    <v-col cols="12" md="4">
                      <v-text-field
                        v-model="editComplaint.complainant_fullname"
                        dense
                        label="Full Name"
                        outlined
                        :rules="[rules.required]"
                      />
                    </v-col>
                    <v-col cols="12" md="4">
                      <v-text-field
                        v-model="editComplaint.complainant_address"
                        dense
                        label="Complete Address"
                        outlined
                        :rules="[rules.required]"
                      />
                    </v-col>
                    <v-col cols="12" md="4">
                      <v-text-field
                        v-model="editComplaint.complainant_cellphone"
                        dense
                        label="Cellphone Numbers"
                        outlined
                        :rules="[rules.required, rules.phone]"
                      />
                    </v-col>
                  </v-row>
                  <v-row align="center" class="my-2">
                    <v-col cols="5"><v-divider /></v-col>
                    <v-col class="text-center" cols="2">- Vs -</v-col>
                    <v-col cols="5"><v-divider /></v-col>
                  </v-row>
                  <v-row>
                    <v-col cols="12"><strong>(Respondent)</strong></v-col>
                  </v-row>
                  <v-row>
                    <v-col cols="12" md="4">
                      <v-text-field
                        v-model="editComplaint.respondent_fullname"
                        dense
                        label="Full Name"
                        outlined
                        :rules="[rules.required]"
                      />
                    </v-col>
                    <v-col cols="12" md="4">
                      <v-text-field
                        v-model="editComplaint.respondent_address"
                        dense
                        label="Complete Address"
                        outlined
                        :rules="[rules.required]"
                      />
                    </v-col>
                    <v-col cols="12" md="4">
                      <v-text-field
                        v-model="editComplaint.respondent_cellphone"
                        dense
                        label="Cellphone Numbers"
                        outlined
                        :rules="[rules.required, rules.phone]"
                      />
                    </v-col>
                  </v-row>
                  <v-row justify="center">
                    <v-col class="text-center" cols="12">
                      <h5 class="font-weight-bold">COMPLAINT</h5>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col cols="12">
                      <v-textarea
                        v-model="editComplaint.complaint_description"
                        class="mt-2"
                        outlined
                        rows="3"
                        :rules="[rules.required]"
                      />
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="editComplaint.date_of_incident"
                        dense
                        label="Date of Incident"
                        outlined
                        :rules="[rules.required]"
                        type="date"
                      />
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        dense
                        label="Date Submitted"
                        :model-value="selectedComplaint.submitted_date"
                        outlined
                        readonly
                      />
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col cols="12" md="6">
                      <v-select
                        v-model="editComplaint.status"
                        dense
                        :items="statusOptions"
                        label="Status"
                        outlined
                        :rules="[rules.required]"
                      />
                    </v-col>
                  </v-row>
                </v-form>
              </v-card-text>
              <v-card-actions>
                <v-spacer />
                <v-btn text @click="editDialog = false">Cancel</v-btn>
                <v-btn color="primary" :disabled="!editValid" @click="submitEdit">Save</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <!-- RESOLVE BUTTON DIALOG -->
          <v-dialog v-model="resolveDialog" max-width="500">
            <v-card>
              <v-card-title class="text-h5">
                Confirm Resolve
              </v-card-title>
              <v-card-text>
                Are you sure you want to mark complaint #{{ resolvingItem?.brngy_case_no }} as resolved?
              </v-card-text>
              <v-card-actions>
                <v-spacer />
                <v-btn color="grey" text @click="resolveDialog = false">
                  Cancel
                </v-btn>
                <v-btn color="success" @click="confirmResolve">
                  Confirm
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>


          <!-- DELETE BUTTON DIALOG -->
          <!-- Delete Confirmation Dialog -->
          <v-dialog v-model="deleteDialog" max-width="500">
            <v-card>
              <v-card-title class="text-h5">Confirm Delete</v-card-title>
              <v-card-text>
                Are you sure you want to delete complaint #{{ deletingItem?.brngy_case_no }}?
              </v-card-text>
              <v-card-actions>
                <v-spacer />
                <v-btn color="grey" text @click="deleteDialog = false">Cancel</v-btn>
                <v-btn color="error" @click="confirmDelete">Delete</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <!-- Message Dialog -->
          <!-- <v-dialog v-model="messageDialog" max-width="400">
            <v-card :color="messageColor" dark>
              <v-card-text class="text-center">{{ messageText }}</v-card-text>
              <v-card-actions>
                <v-spacer />
                <v-btn dark text @click="messageDialog = false">OK</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog> -->
        </v-card>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
  import ComplaintDonutChart from '@/components/ComplaintDonutChart.vue'	
import { buildApiUrl } from '@/utils/api'
  import { computed, onMounted, ref } from 'vue'
  const forOptions = ['Neighbor Dispute', 'Verbal Abuse', 'Battery/Assault', 'Theft' , 'Non-payment of Debt', 'Harassment / Threats', 'Tresspassing', 'Property Damage', 'Boundary Dispute','Disturbance of Peace','Improper Waste Disposal','Unauthorized Cutting of Trees/Plants','Parking Dispute','Illegal Eviction Attempt','Cyber Defamation','Vandalism']


  const search = ref('')
  const statusFilter = ref(null)
  const page = ref(1)
  const itemsPerPage = 5

  const statusOptions = ['Pending', 'In Progress', 'Resolved']

  const headers = [
    { title: 'ID', key: 'brngy_case_no', align: 'start' },
    { title: 'Date Filed', key: 'submitted_date' },
    { title: 'Complainant', key: 'complainant_fullname' },
    { title: 'Subject', key: 'case_type' },
    { title: 'Phone Number', key: 'complainant_cellphone' },
    { title: 'Status', key: 'status' },
    { title: 'Actions', key: 'actions', align: 'end', sortable: false },
  ]


  // Fetch total complaints count from API
  const totalComplaintsCount = computed(() => complaints.value.length)
  async function fetchTotalComplaints () {
    try {
      const response = await fetch('http://localhost/sta_cruz_prj/api/endpoints/complaints/read_count_complaint.php')
      const data = await response.json()
      totalComplaintsCount.value = data.total_complaints || 0
    } catch (error) {
      console.error('Error fetching total complaints:', error)
    }
  }


  const complaints = ref([]) // start empty

  // Fetch complaints from API
  async function fetchComplaints () {
    try {
      const response = await fetch(buildApiUrl('/complaints/read_complaints.php'))
      const data = await response.json()
      complaints.value = data.data || []
    } catch (error) {
      console.error('Error fetching complaints:', error)
    }
  }


  onMounted(() => {
    fetchComplaints()
    fetchTotalComplaints()
  })
  // UPDATE
  async function updateComplaint (editedComplaint) {
    try {
      // Prepare the payload (adjust fields as per your backend API)
      const payload = {
        brngy_case_no: editedComplaint.brngy_case_no,
        case_type: editedComplaint.case_type,
        complainant_fullname: editedComplaint.complainant_fullname,
        complainant_address: editedComplaint.complainant_address,
        complainant_cellphone: editedComplaint.complainant_cellphone,
        respondent_fullname: editedComplaint.respondent_fullname,
        respondent_address: editedComplaint.respondent_address,
        respondent_cellphone: editedComplaint.respondent_cellphone,
        complaint_description: editedComplaint.complaint_description,
        date_of_incident: editedComplaint.date_of_incident,
        status: editedComplaint.status,
      // Do NOT include submitted_date or other readonly fields if your API forbids updating them
      }

      const response = await fetch(buildApiUrl('/complaints/update_complaints.php'), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload),
      })

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Failed to update complaint')
      }


      return true
    } catch (error) {
      alert('Error updating complaint: ' + error.message)
      return false
    }
  }


  // Computed filtered complaints with normalized status filter
  const filteredComplaints = computed(() => {
    const filterStatus = normalizeStatus(statusFilter.value)

    return complaints.value.filter(item => {
      const matchesSearch =
        search.value === '' ||
        Object.values(item)
          .join(' ')
          .toLowerCase()
          .includes(search.value.toLowerCase())

      const itemStatus = normalizeStatus(item.status)

      // If no status filter is selected, show all
      const matchesStatus = !filterStatus || itemStatus === filterStatus

      return matchesSearch && matchesStatus
    })
  })


  // Helper to normalize status strings
  function normalizeStatus (status) {
    return status?.trim().toLowerCase() || ''
  }
  const pendingCases = computed(() =>
    complaints.value.filter(c => normalizeStatus(c.status) === 'pending').length
  );
  const inProgressCases = computed(() =>
    complaints.value.filter(c => normalizeStatus(c.status) === 'in progress').length
  );
  const resolvedCases = computed(() =>
    complaints.value.filter(c => normalizeStatus(c.status) === 'resolved').length
  );

  function statusColor (status) {
    switch (normalizeStatus(status)) {
      case 'resolved': return 'green';
      case 'in progress': return 'amber darken-2';
      case 'pending': return 'red darken-2';
      default: return 'grey';
    }
  }

  // ACTION HANDLERS
  const viewDialog = ref(false)
  const selectedComplaint = ref({})
  function viewItem (item) {
    selectedComplaint.value = { ...item }
    viewDialog.value = true
  }


  // EDIT HANDLES
  const editDialog = ref(false)
  const editComplaint = ref({})
  const editForm = ref(null)
  const editValid = ref(false)


  const rules = {
    required: value => !!value || 'This field is required',
    phone: value => {
      const pattern = /^[0-9+\-\s()]+$/
      return pattern.test(value) || 'Invalid phone number format'
    },
  }

  // Call this when clicking the Edit button to open the dialog with data
  function editItem (item) {
    // Clone the item to avoid mutating the original directly
    editComplaint.value = { ...item }
    editDialog.value = true
  }

  async function submitEdit () {
    if (!editForm.value.validate()) {
      return
    }

    editComplaint.value.status = editComplaint.value.status.toUpperCase()
    const success = await updateComplaint(editComplaint.value)
    if (success) {
      // Update local complaints array reactively
      const index = complaints.value.findIndex(c => c.brngy_case_no === editComplaint.value.brngy_case_no)
      if (index !== -1) {
        // Replace the old complaint with the updated one
        complaints.value[index] = { ...editComplaint.value }
      }
      await fetchComplaints()

      editDialog.value = false
    }
  }

  const resolveDialog = ref(false)
  const resolvingItem = ref(null)

  function resolveItem (item) {
    resolvingItem.value = item
    resolveDialog.value = true
  }


  async function confirmResolve () {
    resolveDialog.value = false // Close the dialog

    if (!resolvingItem.value) return // Exit if no item to resolve

    // Clone the item and set the status to 'RESOLVED'
    const updatedComplaint = { ...resolvingItem.value, status: 'RESOLVED' }

    const success = await updateComplaint(updatedComplaint)
    if (success) {
      const index = complaints.value.findIndex(c => c.brngy_case_no === resolvingItem.value.brngy_case_no)
      if (index !== -1) {
        complaints.value[index] = { ...updatedComplaint }
      }
      await fetchComplaints()
    }

    resolvingItem.value = null // Clear resolving item
  }

  // DELETE

  const deleteDialog = ref(false)
  const deletingItem = ref(null)

  // Message dialog for feedback (optional but recommended)
  const messageDialog = ref(false)
  const messageText = ref('')
  const messageColor = ref('green')

  function showMessage (text, color = 'green') {
    messageText.value = text
    messageColor.value = color
    messageDialog.value = true
  }

  // Open delete confirmation dialog
  function deleteItem (item) {
    deletingItem.value = item
    deleteDialog.value = true
  }

  // Confirm delete and call API
  async function confirmDelete () {
    deleteDialog.value = false; // Close the delete confirmation dialog

    if (!deletingItem.value) return; // Exit if no item selected to delete

    try {
      const response = await fetch(buildApiUrl('/complaints/delete_complaint.php'), {
        method: 'POST', // or 'DELETE' if your API supports it
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ brngy_case_no: deletingItem.value.brngy_case_no }),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Failed to delete complaint');
      }

      // Remove deleted complaint from local reactive array
      const index = complaints.value.findIndex(c => c.brngy_case_no === deletingItem.value.brngy_case_no);
      if (index !== -1) {
        complaints.value.splice(index, 1);
      }

      await fetchComplaints()
      showMessage(data.message || 'Complaint deleted successfully!', 'green');
    } catch (error) {
      showMessage('Error deleting complaint: ' + error.message, 'red');
    } finally {
      deletingItem.value = null;
    }
  }


</script>


<style scoped>

.v-chip {
  font-weight: 500;
  letter-spacing: 0.5px;
}
</style>

<style>
html,
body {
  height: 100%;
  margin: 0;
  background-color: lightgray;
  font-family: 'Poppins', sans-serif;
}
</style>