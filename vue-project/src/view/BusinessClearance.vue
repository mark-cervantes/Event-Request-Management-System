<script setup>
import { buildApiUrl } from '@/utils/api';
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';

const apiUrl = import.meta.env.VITE_API_URL; // Add this line

const tab = ref('users');
const allData = ref([]);
const filteredData = ref([]);

const dialog = ref(false);
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
  if (sessionUser.role !== 'admin') {
    Business_Owner.value = sessionUser.user_id  ;
  }
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

function handleFileUpload(file) {
  // v-file-input emits an array if multiple, or a File if single
  if (Array.isArray(file)) {
    Certificate_Path.value = file[0] || null;
  } else if (file && file.target && file.target.files) {
    Certificate_Path.value = file.target.files[0] || null;
  } else {
    Certificate_Path.value = file;
  }
}

// Helper to upload file and get path
async function uploadCertificateFile(file, action, payload) {
  if (!file) return '';
  try {
    const formData = new FormData();
    formData.append('path', file);
    formData.append('action', action);
    // Append all payload fields for add/edit
    Object.keys(payload).forEach(key => {
      if (payload[key] !== undefined && key !== 'path') {
        formData.append(key, payload[key]);
      }
    });

    const baseURL = import.meta.env.VITE_API_URL;
    const response = await axios.post(
      baseURL + '/users/business_certificate_controller.php',
      formData,
      { headers: { 'Content-Type': 'multipart/form-data' } }
    );
    if (response.data && response.data.path) {
      return response.data.path;
    }
    // If backend returns message only, treat as error
    if (response.data && response.data.message) {
      alert(response.data.message);
    }
    return '';
  } catch (error) {
    console.error('File upload failed:', error);
    return '';
  }
}

async function addCertificate() {
  const valid = await formRef.value?.validate();
  if (!valid) return;

  setBusinessOwnerFromSession();

  try {
    const baseURL = import.meta.env.VITE_API_URL;
    const url = baseURL + '/users/business_certificate_controller.php';
    // Prepare payload for add/edit
    const payload = {
      action: isEditMode.value ? 'edit' : 'add',
      id: Business_Certificate_ID.value,
      business_name: Business_Name.value,
      business_type: Business_Type.value,
      business_address: Business_Address.value,
      business_owner: Business_Owner.value,
      date_registered: Date_Registered.value,
      issued_date: Issued_Date.value,
      expiry_date: Expiry_Date.value,
      status: Status.value
    };

    let filePath = '';
    if (Certificate_Path.value) {
      // Upload file and all fields together (backend expects action in POST)
      filePath = await uploadCertificateFile(Certificate_Path.value, payload.action, payload);
      if (!filePath) {
        // If backend returns error, stop
        return;
      }
      payload.path = filePath;
    } else if (isEditMode.value) {
      // If editing and no new file, don't send path
      // (backend will not update path)
    } else {
      // If adding and no file, error
      alert('Certificate file is required.');
      return;
    }

    // If file was uploaded, backend already handled add/edit, so skip extra POST
    if (Certificate_Path.value) {
      dialog.value = false;
      window.location.reload();
      return;
    }

    // If no file, send JSON payload for add/edit
    const response = await axios.post(url, payload);
    alert(response.data.message);
    dialog.value = false;
    window.location.reload();
  } catch (error) {
    console.error(error);
  }
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
      const baseURL = import.meta.env.VITE_API_URL;
      const url = baseURL + '/users/business_certificate_controller.php';
      const payload = { action: 'delete', id };
      const response = await axios.post(url, payload);
      alert(response.data.message);
    } catch (error) {
      console.error(error);
    } finally {
      window.location.reload();
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
    (row.status !== undefined && (row.status == 1 ? 'approved' : 'pending').includes(q)) ||
    (row.path && row.path.toLowerCase().includes(q))
  );
}

watch([allData, search], filterTable);

onMounted(() => {
  fetchbusinesslist();
  clearFields();
  setBusinessOwnerFromSession();

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
          label="Upload Payment "
          
          prepend-icon="mdi-upload"
          show-size
          @change="handleFileUpload"
          :disabled="false"
        />
        </template>
  <template v-else>
  <v-file-input
    v-model="Certificate_Path"
    label="Upload Payment"
    prepend-icon="mdi-upload"
    show-size
    @change="handleFileUpload"
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
            <a
          v-if="row.path && row.path.trim() !== ''"
          :href="apiUrl + 'api/endpoints/'+row.path"
          target="_blank"
          style="text-decoration: none;"
        >View</a>
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
</template>
