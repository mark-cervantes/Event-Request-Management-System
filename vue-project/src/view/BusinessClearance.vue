<script setup>
import { buildApiUrl } from '@/utils/api';
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';

const apiUrl = import.meta.env.VITE_API_URL; // Add this line

const tab = ref('users');
const allData = ref([]);
const filteredData = ref([]);

const dialog = ref(false);
const receiptDialog = ref(false);
const currentReceipt = ref('');
const cardTitle = ref('Add Business Clearance');
const isEditMode = ref(false);
const userRole = ref('');
const search = ref('');

// Form fields
const Business_Certificate_ID = ref(0);
const Business_Name = ref('');
const Business_Type = ref('');
const Business_Address = ref('');
const Business_Owner = ref('');
const Date_Registered = ref('');
const Issued_Date = ref('');
const Expiry_Date = ref('');
const Status = ref('Approved');
const Certificate_Path = ref(null);

const formRef = ref(null);


function setBusinessOwnerFromSession() {
  const sessionUser = JSON.parse(sessionStorage.getItem('user') || '{}');
  Business_Owner.value = sessionUser.user_id || 1; // Default to 1 if no user_id
}

const headers = [
  { text: 'Business Name', value: 'business_name' },
  { text: 'Business Type', value: 'business_type' },
  { text: 'Business Address', value: 'business_address' },
  { text: 'Date Registered', value: 'date_registered' },
  { text: 'Issued Date', value: 'issued_date' },
  { text: 'Expiry Date', value: 'expiry_date' },
  { text: 'Status', value: 'status' },
  { text: 'Actions', value: 'actions', sortable: false },
];

function clearFields() {
  Business_Certificate_ID.value = 0;
  Business_Name.value = '';
  Business_Type.value = '';
  Business_Address.value = '';
  Business_Owner.value = '';
  Date_Registered.value = '';
  Issued_Date.value = '';
  Expiry_Date.value = '';
  Status.value = 'Approved';
  Certificate_Path.value = null;
}

function addClicked() {
  dialog.value = true;
  cardTitle.value = 'Add Business Clearance';
  isEditMode.value = false;
  clearFields();
  setBusinessOwnerFromSession();
}

async function addCertificate() {
  const valid = await formRef.value?.validate();
  if (!valid) return;

  try {
    const url = buildApiUrl('/users/business_certificate_controller.php');
    const payload = {
      action: isEditMode.value ? 'edit' : 'add',
      id: Business_Certificate_ID.value,
      business_name: Business_Name.value,
      business_type: Business_Type.value,
      business_address: Business_Address.value,
      business_owner: 1, // Simplified: just use 1 as default owner
      date_registered: Date_Registered.value,
      issued_date: Issued_Date.value,
      expiry_date: Expiry_Date.value,
      status: Status.value === 'Approved' ? 1 : 0
    };

    // Convert file to base64 if present
    if (Certificate_Path.value) {
      const file = Array.isArray(Certificate_Path.value) ? Certificate_Path.value[0] : Certificate_Path.value;
      if (file) {
        try {
          const base64 = await convertFileToBase64(file);
          payload.image_data = base64;
        } catch (error) {
          console.error('Error converting file to base64:', error);
          alert('Error processing image file. Please try again.');
          return;
        }
      }
    }

    console.log('Submitting payload:', payload); // Debug log

    const response = await axios.post(url, payload, {
      headers: { 'Content-Type': 'application/json' }
    });

    if (response.data && response.data.message) {
      alert(response.data.message);
      dialog.value = false;
      await fetchbusinesslist(); // Refresh the data instead of reloading page
    }
  } catch (error) {
    console.error('Error submitting form:', error);
    alert('Error submitting form. Please try again.');
  }
}

// Helper function to convert file to base64
function convertFileToBase64(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => resolve(reader.result);
    reader.onerror = error => reject(error);
    reader.readAsDataURL(file);
  });
}

async function editClicked(id) {
  dialog.value = true;
  cardTitle.value = 'Edit Business Clearance';
  isEditMode.value = true;

  try {
    const response = await axios.get(buildApiUrl('/users/business_certificate_controller.php'), {
      params: { id }
    });

    const data = response.data.data?.[0] || response.data;
    Business_Certificate_ID.value = id;
    Business_Name.value = data.business_name;
    Business_Type.value = data.business_type;
    Business_Address.value = data.business_address;
    Business_Owner.value = data.business_owner;
    Date_Registered.value = data.date_registered;
    Issued_Date.value = data.issued_date;
    Expiry_Date.value = data.expiry_date;
    Status.value = data.status == 1 ? 'Approved' : 'Pending';
    Certificate_Path.value = null; // PDF not auto-loaded
  } catch (error) {
    console.error(error);
  }
}

async function deleteClicked(id) {
  if (confirm("Do you want to delete this Business Clearance?")) {
    try {
      const url = buildApiUrl('/users/business_certificate_controller.php');
      const payload = { action: 'delete', id };
      const response = await axios.post(url, payload);
      alert(response.data.message);
      await fetchbusinesslist(); // Refresh the data instead of reloading page
    } catch (error) {
      console.error(error);
    }
  }
}

async function fetchbusinesslist() {
  try {
    const sessionUser = JSON.parse(sessionStorage.getItem('user') || '{}');
    const params = sessionUser.role !== 'admin' ? { id: sessionUser.user_id } : {};

    const response = await axios.get(buildApiUrl('/users/business_certificate_controller.php'), {
      params
    });

    allData.value = response.data.data || [];
    filterTable();
  } catch (error) {
    console.error('Error fetching Business Clearances:', error);
  }
}

// Filter function for search
function filterTable() {
  const q = search.value ? search.value.toLowerCase() : '';
  if (!q) {
    filteredData.value = allData.value;
    return;
  }
  filteredData.value = allData.value.filter(row =>
    (row.business_name && row.business_name.toLowerCase().includes(q)) ||
    (row.business_type && row.business_type.toLowerCase().includes(q)) ||
    (row.business_address && row.business_address.toLowerCase().includes(q)) ||
    (row.business_owner && String(row.business_owner).toLowerCase().includes(q)) ||
    (row.date_registered && row.date_registered.toLowerCase().includes(q)) ||
    (row.issued_date && row.issued_date.toLowerCase().includes(q)) ||
    (row.expiry_date && row.expiry_date.toLowerCase().includes(q)) ||
    (row.status !== undefined && (row.status == 1 ? 'approved' : 'pending').includes(q))
  );
}

watch([allData, search], filterTable);

function viewReceipt(imageData) {
  if (imageData && imageData.trim() !== '') {
    // If it's base64 data, use it directly; otherwise try to construct URL
    const isBase64 = imageData.startsWith('data:');
    if (isBase64) {
      currentReceipt.value = imageData;
    } else {
      // Fallback for old file paths
      const fullPath = imageData.startsWith('http') ? imageData : apiUrl + 'api/endpoints/' + imageData;
      currentReceipt.value = fullPath;
    }
    receiptDialog.value = true;
  }
}

onMounted(() => {
  fetchbusinesslist();
  clearFields();
  const sessionUser = JSON.parse(sessionStorage.getItem('user') || '{}');
  userRole.value = sessionUser?.role || '';
});
</script>


<template>
  <v-container fluid class="pa-4">
  <v-tabs-items v-model="tab">
    <v-tab-item value="users">
    <v-row class="my-4" justify="end">
      <v-btn @click="addClicked" color="primary" rounded>
      <v-icon>mdi-plus</v-icon> {{ userRole === 'admin' ? 'Add Business Clearance' : 'Request Business Clearance' }}
      </v-btn>
    </v-row>

      <v-dialog v-model="dialog" width="600" scrollable>  
      <v-card prepend-icon="mdi-file-document" :title="cardTitle">
      <v-card-text>
      <v-divider class="mb-3"></v-divider>
      <v-sheet>
      <v-form ref="formRef">
        <v-text-field 
        v-model="Business_Name" 
        label="Business Name" 
       
        />
        <v-text-field 
        v-model="Business_Type" 
        label="Business Type" 
       
        />
        <v-text-field 
        v-model="Business_Address" 
        label="Business Address" 
       
        />
        <v-text-field 
        v-model="Date_Registered" 
        label="Date Registered" 
        type="date" 
       
        />
        <v-text-field 
        v-model="Issued_Date" 
        label="Issued Date" 
        type="date" 
       
        />
        <v-text-field 
        v-model="Expiry_Date" 
        label="Expiry Date" 
        type="date" 
       
        />
        <template v-if="userRole === 'admin'">
        <v-select 
          v-model="Status" 
          label="Status" 
          :items="['Approved', 'Pending']" 
          :disabled="false" 
        />
        <v-file-input
          v-model="Certificate_Path"
          label="Upload Payment"
          prepend-icon="mdi-upload"
          show-size
          accept="image/*"
          :disabled="false"
        />
        </template>
  <template v-else>
  <v-file-input
    v-model="Certificate_Path"
    label="Upload Payment"
    prepend-icon="mdi-upload"
    show-size
    accept="image/*"
  />
  <!-- Show payment instruction below the file input -->
  <div class="text-caption mt-1">
    Please upload a screenshot of your ₱160 payment made via 
    <strong>GCash (09636755401)</strong> 
  </div>

  <v-select 
    v-model="Status" 
    label="Status" 
    :items="['Approved', 'Pending']" 
    :disabled="true" 
  />
</template>


        <v-card-actions class="justify-end">
        <v-btn @click="dialog = false" color="grey darken-1">Close</v-btn>
        <v-btn @click="addCertificate" color="success">Save</v-btn>
        </v-card-actions>
      </v-form>
      </v-sheet>
      </v-card-text>
      </v-card>
      </v-dialog>
<v-data-table
  :headers="headers"
  :items="filteredData"
  item-value="business_certificate_id"
  class="elevation-1 mt-4"
  density="comfortable"
  fixed-header
  height="600"
>
  <template #top>
    <v-text-field
      v-model="search"
      label="Search"
      class="mx-4 mt-2"
      prepend-inner-icon="mdi-magnify"
      hide-details
      clearable
    />
  </template>

      <thead>
      <tr>
        <th>Business Name</th>
        <th>Business Type</th>
        <th>Business Address</th>
        <!-- <th v-if="userRole == 'admin'">User</th> -->
        <th>Date Registered</th>
        <th>Issued Date</th>
        <th>Expiry Date</th>
        <th>Status</th>
        <th>Payment</th>
        <th>Actions</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="row in filteredData" :key="row.business_certificate_id">
        <td>{{ row.business_name }}</td>
        <td>{{ row.business_type }}</td>
        <td>{{ row.business_address }}</td>
        <!-- <td v-if="userRole == 'admin'">{{ row.business_owner }}</td> -->
        <td>{{ row.date_registered }}</td>
        <td>{{ row.issued_date }}</td>
        <td>{{ row.expiry_date }}</td>
        <td>
        <v-chip
          v-if="new Date(row.expiry_date) < new Date()"
          color="red"
          dark
        >
          Expired
        </v-chip>
        <v-chip
          v-else
          :color="row.status == 1 ? 'green' : 'orange'"
          dark
        >
          {{ row.status == 1 ? 'Pending' : 'Approved' }}
        </v-chip>
        </td>
        <td>
            <v-btn
              v-if="row.image_data && row.image_data.trim() !== ''"
              text
              color="primary"
              @click="viewReceipt(row.image_data)"
            >
              View
            </v-btn>
            <span v-else>-</span>
        </td>
        <td>
        <v-btn
          icon
          color="primary"
          @click="editClicked(row.business_certificate_id)"
          v-if="userRole === 'admin' || row.status == 1 && new Date(row.expiry_date) >= new Date()"
        >
          <v-icon>mdi-pencil</v-icon>
        </v-btn>
        <v-btn icon color="red" @click="deleteClicked(row.business_certificate_id)">
          <v-icon>mdi-delete</v-icon>
        </v-btn>
        </td>
      </tr>
      </tbody>
</v-data-table>
    </v-tab-item>
  </v-tabs-items>
  </v-container>

  <!-- Add Receipt Viewer Dialog -->
  <v-dialog v-model="receiptDialog" max-width="800">
    <v-card>
      <v-card-title class="text-h5">
        Payment Receipt
        <v-spacer></v-spacer>
        <v-btn icon @click="receiptDialog = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>
      <v-card-text>
        <v-img
          v-if="currentReceipt"
          :src="currentReceipt"
          max-height="600"
          contain
          class="grey lighten-2"
        >
          <template v-slot:placeholder>
            <v-row class="fill-height ma-0" align="center" justify="center">
              <v-progress-circular indeterminate color="grey lighten-1"></v-progress-circular>
            </v-row>
          </template>
        </v-img>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
