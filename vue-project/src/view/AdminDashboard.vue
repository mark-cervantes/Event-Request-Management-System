<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import LineChart from '@/components/LineChart.vue' // Adjust path as needed
import { buildApiUrl } from '@/utils/api.js'

const residentCount = ref(0)
const userCount = ref(0)
const activeEvents = ref(18)
const pendingCertificates = ref(24)
const totalComplaints = ref(87)

const selectedRange = ref('Last 6 Months')

const chartData = ref({
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
  datasets: [
    {
      label: 'Residents',
      data: [120, 150, 180, 210, 240, 300],
      borderColor: '#3b82f6',
      backgroundColor: 'rgba(59, 130, 246, 0.2)',
      fill: true,
      tension: 0.4
    }
  ]
})

const businessClearanceRequests = ref([
  { name: 'Juan Dela Cruz', date_requested: '2025-05-30' },
  { name: 'Maria Santos', date_requested: '2025-05-28' },
  { name: 'Carlos Reyes', date_requested: '2025-05-27' },
  { name: 'Ana Mendoza', date_requested: '2025-05-25' },
  { name: 'Ana Mendoza', date_requested: '2025-05-25' },
  { name: 'Ana Mendoza', date_requested: '2025-05-25' },
  { name: 'Luis Gomez', date_requested: '2025-05-23' }
])


const chartOptions = {
  responsive: true,
  plugins: {
    legend: { position: 'top' },
    title: { display: false }
  },
  scales: {
    y: {
      beginAtZero: true
    }
  }
}

async function fetchResidentCount() {
  try {
    const response = await axios.get(buildApiUrl('resident/read_count_resident.php'))
    residentCount.value = response.data.total_residents || 0
  } catch (error) {
    console.error('Failed to fetch resident count:', error)
  }
}

async function fetchUserCount() {
  try {
    const response = await axios.get(buildApiUrl('users/read_count_users.php'))
    userCount.value = response.data.total_users || 0
  } catch (error) {
    console.error('Failed to fetch user count:', error)
  }
}

onMounted(() => {
  fetchResidentCount()
  fetchUserCount()
})
</script>

<template>
  <v-app>
    <v-main class="bg-grey-lighten-2">
      <v-container fluid>
        <v-row class="mb-2" align="stretch">
          <v-col cols="12" md="6" lg="3">
            <v-card class="pa-6" elevation="2">
              <div class="d-flex justify-space-between align-start mb-4">
                <div>
                  <div class="text-grey text-caption">Total Residents</div>
                  <div class="text-h4 font-weight-bold mt-1">{{ residentCount }}</div>
                </div>
                <div class="icon-bg bg-blue-lighten-4">
                  <v-icon color="blue-darken-2" size="32">mdi-account-outline</v-icon>
                </div>
              </div>
              <div class="d-flex align-center">
                <div class="d-flex align-center text-success text-caption mr-2">
                  <v-icon size="18" class="mr-1">mdi-arrow-up</v-icon>
                  12.5%
                </div>
                <span class="text-grey text-caption">vs last month</span>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" md="6" lg="3">
            <v-card class="pa-6" elevation="2">
              <div class="d-flex justify-space-between align-start mb-4">
                <div>
                  <div class="text-grey text-caption">Users</div>
                  <div class="text-h4 font-weight-bold mt-1">{{ userCount }}</div>
                </div>
                <div class="icon-bg bg-green-lighten-4">
                  <v-icon color="green-darken-2" size="32">mdi-account</v-icon>
                </div>
              </div>
              <div class="d-flex align-center">
                <div class="d-flex align-center text-success text-caption mr-2">
                  <v-icon size="18" class="mr-1">mdi-arrow-up</v-icon>
                  7.3%
                </div>
                <span class="text-grey text-caption">vs last month</span>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" md="6" lg="3">
            <v-card class="pa-6" elevation="2">
              <div class="d-flex justify-space-between align-start mb-4">
                <div>
                  <div class="text-grey text-caption">Pending Certificates</div>
                  <div class="text-h4 font-weight-bold mt-1">{{ pendingCertificates }}</div>
                </div>
                <div class="icon-bg bg-amber-lighten-4">
                  <v-icon color="amber-darken-2" size="32">mdi-file-document-outline</v-icon>
                </div>
              </div>
              <div class="d-flex align-center">
                <div class="d-flex align-center text-error text-caption mr-2">
                  <v-icon size="18" class="mr-1">mdi-arrow-down</v-icon>
                  3.1%
                </div>
                <span class="text-grey text-caption">vs last month</span>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" md="6" lg="3">
            <v-card class="pa-6" elevation="2">
              <div class="d-flex justify-space-between align-start mb-4">
                <div>
                  <div class="text-grey text-caption">Total Complaints</div>
                  <div class="text-h4 font-weight-bold mt-1">{{ totalComplaints }}</div>
                </div>
                <div class="icon-bg bg-red-lighten-4">
                  <v-icon color="red-darken-2" size="32">mdi-alert-outline</v-icon>
                </div>
              </div>
              <div class="d-flex align-center">
                <div class="d-flex align-center text-error text-caption mr-2">
                  <v-icon size="18" class="mr-1">mdi-arrow-up</v-icon>
                  5.8%
                </div>
                <span class="text-grey text-caption">vs last month</span>
              </div>
            </v-card>
          </v-col>
        </v-row>

        <v-row class="mt-1" style="align-items: stretch;">
          <v-col cols="12" md="6" lg="6" class="d-flex">
            <v-card class="pa-6 d-flex flex-column" elevation="2" style="flex: 1;">
              <div class="d-flex justify-space-between align-center mb-4">
                <div>
                  <div class="text-grey text-caption">Chart</div>
                  <div class="text-h6 font-weight-bold">Resident Growth</div>
                </div>
                <v-select :items="['Last 6 Months']" v-model="selectedRange" dense hide-details
                  style="max-width: 150px;"></v-select>
              </div>
              <div style="flex-grow: 1;">
                <LineChart :chart-data="chartData" :chart-options="chartOptions" />
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" md="6" lg="6" class="d-flex">
            <v-card class="pa-6 d-flex flex-column" elevation="2" style="flex: 1;">
              <div class="d-flex justify-space-between align-center mb-4">
                <div>
                  <div class="text-grey text-caption">Business Clearance Requests</div>
                  <div class="text-h6 font-weight-bold mt-1">Recent Requesters</div>
                </div>
                <div class="icon-bg bg-indigo-lighten-4">
                  <v-icon color="indigo-darken-2" size="32">mdi-briefcase-outline</v-icon>
                </div>
              </div>
              <v-divider></v-divider>
              <v-list density="compact" class="mt-2" style="flex-grow: 1; overflow-y: auto;">
                <v-list-item v-for="(request, index) in businessClearanceRequests" :key="index">
                  <v-list-item-content>
                    <v-list-item-title class="font-weight-medium">{{ request.name }}</v-list-item-title>
                    <v-list-item-subtitle>
                      Requested on {{ new Date(request.date_requested).toLocaleDateString() }}
                    </v-list-item-subtitle>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-card>
          </v-col>
        </v-row>





      </v-container>
    </v-main>
  </v-app>
</template>

<style scoped>
.icon-bg {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
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
