<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { computed } from 'vue';
import { buildApiUrl } from '@/utils/api.js';


const formRef = ref(null);

const viewMode = ref('residents');
const search = ref('');

const allData = ref(null);
const dialog = ref(false);
const cardTitle = ref('Add Resident');
const Resident_ID = ref(0);
const First_Name = ref('');
const Middle_Name = ref('');
const Last_Name = ref('');
const Contact_Number = ref('');
const Address = ref('');
const Birth_Date = ref(null);
const Gender = ref('');
const Civil_Status = ref('');
const Household_No = ref('');
const Religion = ref('');
const Status = ref('');

const genderFilter = ref('')  // '', 'M', 'F'
const civilStatusFilter = ref('')  // '', 'S', 'M'
const statusFilter = ref('')  // '', 'active', 'inactive', 'archived'


const rules = {
    required: value => !!value || 'Field is required'
}

const filteredData = computed(() => {
    if (!allData.value) return [];

    return allData.value
        .filter(resident => {
            const stat = resident.status.toLowerCase();

            // ● Show active + inactive when viewMode is "residents"
            if (viewMode.value === 'residents' && !['active', 'inactive'].includes(stat)) {
                return false;
            }

            // ● Show only archived when viewMode is "archived"
            if (viewMode.value === 'archived' && stat !== 'archived') {
                return false;
            }

            // ● Gender filter
            if (genderFilter.value && resident.gender !== genderFilter.value) {
                return false;
            }

            // ● Civil status filter
            if (civilStatusFilter.value && resident.civil_status !== civilStatusFilter.value) {
                return false;
            }

            // ● Status filter (if you want to override)
            if (statusFilter.value && stat !== statusFilter.value) {
                return false;
            }

            return true;
        })
        .filter(row => {
            // ● Text search
            const term = search.value.toLowerCase();
            if (!term) return true;
            const fullName = `${row.first_name} ${row.middle_name} ${row.last_name}`.toLowerCase();
            return fullName.includes(term) ||
                row.contact_number.toLowerCase().includes(term) ||
                row.address.toLowerCase().includes(term) ||
                row.religion.toLowerCase().includes(term);
        });
});





/*const filteredData = computed(() => {
  if (!allData.value) return [];
  return allData.value.filter(resident =>
    resident.status === (viewMode.value === 'archived' ? 'archived' : 'active')
  );
});*/

function addClicked() {
    dialog.value = true;
    cardTitle.value = 'Add Resident';
    clearFields();
}

function clearFields() {
    First_Name.value = '';
    Middle_Name.value = '';
    Last_Name.value = '';
    Contact_Number.value = '';
    Address.value = '';
    Birth_Date.value = null;
    Gender.value = '';
    Civil_Status.value = '';
    Household_No.value = '';
    Religion.value = '';
    Status.value = '';
}

async function addResident() {
    try {
        if (cardTitle.value == 'Add Resident') {
            console.log({
                resident_id: '',
                first_name: First_Name.value,
                middle_name: Middle_Name.value,
                last_name: Last_Name.value,
                contact_number: Contact_Number.value,
                address: Address.value,
                birth_date: Birth_Date.value,
                gender: Gender.value,
                civil_status: Civil_Status.value,
                household_no: Household_No.value,
                religion: Religion.value,
                status: Status.value            });
            const response = await axios.post(buildApiUrl('resident/create_resident.php'), {
                resident_id: '',
                first_name: First_Name.value,
                middle_name: Middle_Name.value,
                last_name: Last_Name.value,
                contact_number: Contact_Number.value,
                address: Address.value,
                birth_date: Birth_Date.value,
                gender: Gender.value,
                civil_status: Civil_Status.value,
                household_no: Household_No.value,
                religion: Religion.value,
                status: Status.value
            });
            alert(response.data.message);
        } else { // update
            const response = await axios.post(buildApiUrl('resident/update_resident.php'), {
                resident_id: Resident_ID.value,
                first_name: First_Name.value,
                middle_name: Middle_Name.value,
                last_name: Last_Name.value,
                contact_number: Contact_Number.value,
                address: Address.value,
                birth_date: Birth_Date.value,
                gender: Gender.value,
                civil_status: Civil_Status.value,
                household_no: Household_No.value,
                religion: Religion.value,
                status: Status.value
            });
            alert(response.data.message);
        }
    } catch (error) {
        console.error(error);
    } finally {
        fetchResidentList();
        dialog.value = false;
    }
}

async function editClicked(id) {
    dialog.value = true;
    cardTitle.value = 'Edit Resident';

    try {
        const response = await axios.post(buildApiUrl('resident/read_single_resident.php'), { resident_id: id });
        Resident_ID.value = id;
        First_Name.value = response.data.first_name;
        Middle_Name.value = response.data.middle_name;
        Last_Name.value = response.data.last_name;
        Contact_Number.value = response.data.contact_number;
        Address.value = response.data.address;
        Birth_Date.value = response.data.birth_date;
        Gender.value = response.data.gender;
        Civil_Status.value = response.data.civil_status;
        Household_No.value = response.data.household_no;
        Religion.value = response.data.religion;
        Status.value = response.data.status;
    } catch (error) {
        console.error(error);
    }
}

/*async function deleteClicked(id) {
    if (confirm("Do you want to delete this resident?")) {
        try {
            const response = await axios.post(buildApiUrl('resident/delete_resident.php'), { resident_id: id });
            alert(response.data.message);
        } catch (error) {
            console.error(error);
        } finally {
            fetchResidentList();
        }
    }
}*/

async function fetchResidentList() {
    const response = await axios.post(buildApiUrl('resident/read_resident.php'));
    console.log(response.data.data);
    allData.value = response.data.data;
}

async function archiveClicked(id) {
    if (confirm("Do you want to archive this resident?")) {
        try {
            const response = await axios.post(buildApiUrl('resident/archive_resident.php'), {
                resident_id: id
            });
            alert(response.data.message);
        } catch (error) {
            console.error(error);
        } finally {
            fetchResidentList();
        }
    }
}


onMounted(() => {
    fetchResidentList();
    clearFields();
});
</script>

<template>
    <v-container fluid class="pa-4 mt-1">




        <v-row>
            <v-col>
                <v-btn-toggle v-model="viewMode" mandatory>
                    <v-btn value="residents" color="#0A24C2">Residents</v-btn>
                    <v-btn value="archived" color="#FDC804">Archived</v-btn>
                </v-btn-toggle>
            </v-col>



        </v-row>

        <v-sheet class="pa-4 " elevation="1" style="background-color: white;">
            <v-row class=" align-center mb-2 ">
                <v-col>
                    <h1 class="text-h4 font-weight-bold">RESIDENT MANAGEMENT</h1>
                </v-col>
                <v-spacer></v-spacer>
                <v-col cols="auto">

                </v-col>

                <v-col cols="auto">
                    <v-btn class=" text-subtitle-3" color="#0A24C2" height="45" min-width="150" justify="end"
                        @click="addClicked">
                        <v-icon>mdi-plus</v-icon> Add Resident
                    </v-btn>
                </v-col>
            </v-row>
            <v-row class="mb-1" dense>
                <v-col cols="12" sm="4">
                    <v-select v-model="genderFilter" :items="[
                        { title: 'All Genders', value: '' },
                        { title: 'Male', value: 'M' },
                        { title: 'Female', value: 'F' }
                    ]" item-title="title" item-value="value" label="Filter by Gender" dense clearable />
                </v-col>

                <v-col cols="12" sm="4">
                    <v-select v-model="civilStatusFilter" :items="[
                        { title: 'All Statuses', value: '' },
                        { title: 'Single', value: 'S' },
                        { title: 'Married', value: 'M' }
                    ]" item-title="title" item-value="value" label="Filter by Civil Status" dense clearable />
                </v-col>

                <v-col cols="12" sm="4">
                    <v-select v-model="statusFilter" :items="[
                        { title: 'All Records', value: '' },
                        { title: 'Active', value: 'active' },
                        { title: 'Inactive', value: 'inactive' },
                    ]" item-title="title" item-value="value" label="Filter by Record Status" dense clearable />
                </v-col>
            </v-row>
            <v-text-field v-model="search" label="Search" prepend-inner-icon="mdi-magnify" clearable class="bg-white" />

            <!-- your <v-row> of selects here -->
        </v-sheet>

        <v-dialog v-model="dialog" width="900" scrollable>

            <v-card prepend-icon="mdi-account-plus" :title="cardTitle">
                <v-card-text>
                    <v-divider class="mb-3"></v-divider>
                    <v-sheet>
                        <v-form ref="formRef">
                            <v-input hidden v-model="Resident_ID"></v-input>

                            <v-text-field v-model="First_Name" :counter="15" :rules="[rules.required]"
                                label="First Name" required></v-text-field>
                            <v-text-field v-model="Middle_Name" :rules="[rules.required]" label="Middle Name"
                                required></v-text-field>
                            <v-text-field v-model="Last_Name" :rules="[rules.required]" label="Last Name"
                                required></v-text-field>
                            <v-text-field v-model="Contact_Number" :rules="[rules.required]" label="Contact Number"
                                required></v-text-field>

                            <v-text-field type="date" v-model="Birth_Date" label="Birth Date" :rules="[rules.required]"
                                required></v-text-field>


                            <v-radio-group v-model="Gender" label="Gender">
                                <v-radio label="Male" value="M"></v-radio>
                                <v-radio label="Female" value="F"></v-radio>
                            </v-radio-group>

                            <v-select v-model="Civil_Status" label="Civil Status" :items="[
                                { title: 'Single', value: 'S' },
                                { title: 'Married', value: 'M' }
                            ]"></v-select>

                            <v-text-field v-model="Household_No" label="Household Number"></v-text-field>
                            <v-text-field v-model="Religion" label="Religion"></v-text-field>

                            <v-textarea v-model="Address" :rules="[rules.required]" label="Address"></v-textarea>

                            <v-select v-model="Status" label="Status" :items="[
                                { title: 'Active', value: 'active' },
                                { title: 'Inactive', value: 'inactive' },
                            ]"></v-select>
                            <v-card-actions class="justify-end">
                                <v-btn @click="dialog = false" color="black">Close</v-btn>
                                <v-btn @click="addResident()" color="#0A24C2">Save</v-btn>
                            </v-card-actions>
                        </v-form>
                    </v-sheet>
                </v-card-text>
            </v-card>
        </v-dialog>

        <v-table>

            <thead class="text-weight-xl">
                <tr>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Contact Number</th>
                    <th>Birth Date</th>
                    <th>Gender</th>
                    <th>Civil Status</th>
                    <th>Household No</th>
                    <th>Religion</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Edit</th>
                    <th>Archive</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="row in filteredData" :key="row.resident_id">
                    <td>{{ row.first_name }}</td>
                    <td>{{ row.middle_name }}</td>
                    <td>{{ row.last_name }}</td>
                    <td>{{ row.contact_number }}</td>
                    <td>{{ row.birth_date }}</td>
                    <td>{{ row.gender == 'M' ? 'Male' : 'Female' }}</td>
                    <td>{{ row.civil_status == 'S' ? 'Single' : 'Married' }}</td>
                    <td>{{ row.household_no }}</td>
                    <td>{{ row.religion }}</td>
                    <td>{{ row.address }}</td>
                    <td>{{ row.status }}</td>
                    <td><v-btn @click="editClicked(row.resident_id)" color="#0A24C2"><v-icon
                                icon="mdi-pencil"></v-icon></v-btn></td>
                    <td><v-btn @click="archiveClicked(row.resident_id)" color="#FDC804">
                            <v-icon icon="mdi-archive"></v-icon>
                        </v-btn></td>

                </tr>
            </tbody>
        </v-table>
    </v-container>

</template>
