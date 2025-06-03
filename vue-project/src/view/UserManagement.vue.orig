    <script setup>
    import axios from 'axios';
    import { ref, onMounted, computed } from 'vue';
    import { buildApiUrl } from '@/utils/api.js';

    const dialog = ref(false);
    const isEditMode = ref(false);
    const formRef = ref(null);
    const cardTitle = ref('Add Account');
    const viewMode = ref('residents'); // Match toggle button value
    const allUsers = ref([]);
    const residents = ref([]);
    const search = ref('');
    const showPassword = ref(false);
    const tableViewRole = ref('admin'); // 'all', 'admin', 'official', 'user'


    const statusFilter = ref('');
    const roleFilter = ref('');


    const form = ref({
        user_id: 0,
        username: '',
        email: '',
        password: '',
        role: '',
        status: '',
        resident_id: '',
        start_term: '',
        end_term: '',
        position_title: ''
    });


    const rules = {
        required: value => !!value || 'Field is required'
    };

 function openDialog(edit = false, user = null) {
    isEditMode.value = edit;
    cardTitle.value = edit ? 'Edit User' : 'Add Account';
    dialog.value = true;

    if (edit && user) {
        const userClone = JSON.parse(JSON.stringify(user));
        Object.assign(form.value, userClone);
    } else {
        resetForm();
    }
}



    function resetForm() {
        Object.assign(form.value, {
            user_id: 0,
            username: '',
            email: '',
            password: '',
            role: '',
            status: '',
            resident_id: '',
            start_term: '',
            end_term: '',
            position_title: ''  
        });
    }


async function saveUser() {
    const isValid = await formRef.value.validate();

    if (!isValid) {
        console.log("Form validation failed.");
        return;
    }

    // Check if the resident is already linked to an account
    const isDuplicateResident = allUsers.value.some(user => 
        user.resident_id === form.value.resident_id &&
        user.user_id !== form.value.user_id // exclude current user if editing
    );

    if (isDuplicateResident) {
        alert("Selected resident already has an account.");
        return; // Don't close the dialog
    }

    // Ensure the role is set correctly
    if (!form.value.role) {
        alert("Role is required.");
        return;
    }    // Set term fields to empty if role is not 'official'
    if (form.value.role !== 'official') {
        form.value.start_term = '';
        form.value.end_term = '';
        form.value.position_title = '';
    }

    const url = isEditMode.value
        ? buildApiUrl('users/update_users.php')
        : buildApiUrl('users/create_users.php');

    try {
        const { data } = await axios.post(url, form.value);
        alert(data.message);
        fetchUsers(); // Refresh the user list
        dialog.value = false; // Only close the dialog on successful save
    } catch (err) {
        console.error('Save error:', err.response ? err.response.data : err.message);
        alert('Error saving user: ' + (err.response ? err.response.data.message : err.message));
    }
}


    async function archiveUser(user_id) {
        console.log("Archiving user with ID:", user_id); // ← Add this line
        if (!confirm('Do you want to archive this user?')) return;        try {
            const { data } = await axios.post(buildApiUrl('users/archive_users.php'), {
                user_id
            });
            alert(data.message);
        } catch (err) {
            console.error('Archive error:', err);
        } finally {
            fetchUsers();
            fetchResidents();
        }
    }
    async function fetchUsers() {
        try {
            const { data } = await axios.post(buildApiUrl('users/read_users.php'));
            allUsers.value = data.data;
        } catch (err) {
            console.error('User fetch error:', err);
        }
    }

    async function fetchResidents() {
        try {
            const { data } = await axios.get(buildApiUrl('resident/read_resident.php'));
            residents.value = data.data.map(r => ({
                ...r,
                full_name: `${r.first_name} ${r.middle_name} ${r.last_name}`
            }));
        } catch (err) {
            console.error('Resident fetch error:', err);
        }
    }

    const filteredData = computed(() => {
        if (!allUsers.value) return [];

        return allUsers.value
            .filter(user => {
                const stat = (user.status || '').toLowerCase();

                if (viewMode.value === 'residents' && !['active', 'inactive'].includes(stat)) return false;
                if (viewMode.value === 'archived' && stat !== 'archived') return false;
                if (statusFilter.value && stat !== statusFilter.value.toLowerCase()) return false;
                if (roleFilter.value && user.role !== roleFilter.value) return false;
                if (tableViewRole.value !== 'all' && user.role !== tableViewRole.value) return false;

                return true;
            })
            .filter(user => {
                const term = search.value.toLowerCase();
                if (!term) return true;

                const resident = residents.value.find(r => r.resident_id === user.resident_id);
                const fullName = resident
                    ? `${resident.first_name} ${resident.middle_name} ${resident.last_name}`.toLowerCase()
                    : '';

                return (
                    fullName.includes(term) ||
                    (user.email || '').toLowerCase().includes(term) ||
                    (user.username || '').toLowerCase().includes(term)
                );
            });
    });


    onMounted(() => {
        fetchUsers();
        fetchResidents();
    });

    </script>


    <template>
        <v-container fluid class="pa-4 mt-1">
            <v-btn-toggle v-model="tableViewRole" mandatory>
                <v-btn value="admin">Admin</v-btn>
                <v-btn value="official">Official</v-btn>
                <v-btn value="user">User</v-btn>
            </v-btn-toggle>




            <v-sheet class="pa-4 mb-4 " elevation="1" style="background-color: white;">
                <v-row class=" align-center mb-2  " outline>
                    <v-col>
                        <h1 class="text-h4 font-weight-bold">USER MANAGEMENT</h1>
                    </v-col>

                    <v-col cols="auto">
                        <v-btn class=" text-subtitle-3" color="#0A24C2" height="45" min-width="150" justify="end"
                            @click="openDialog()">
                            <v-icon>mdi-plus</v-icon> Add Account
                        </v-btn>
                    </v-col>

                </v-row>
        

                <v-text-field v-model="search" label="Search" prepend-inner-icon="mdi-magnify" clearable class="bg-white" />
            <v-row>
                <v-col>
                    <v-btn-toggle v-model="viewMode" mandatory>
                        <v-btn value="residents" >Active/Inactive</v-btn>
                        <v-btn value="archived" >Archived</v-btn>
                    </v-btn-toggle>
                </v-col>
                </v-row>

                <!-- your <v-row> of selects here -->
            </v-sheet>


            <!-- User Dialog -->
            <v-dialog v-model="dialog" width="900">
                <v-card :title="cardTitle" prepend-icon="mdi-account-plus">
                    <v-card-text>
                        <v-form ref="formRef">
                            <v-select v-model="form.role" :items="['admin', 'official', 'user']" label="Role"
                                :rules="[rules.required]" />

                            <!-- Show only if role is 'official' -->
                            <template v-if="form.role === 'official'">
                                <v-text-field v-model="form.start_term" label="Start of Term" type="date"
                                    :rules="[rules.required]" />
                                <v-text-field v-model="form.end_term" label="End of Term" type="date"
                                    :rules="[rules.required]" />
                                <v-select v-model="form.position_title" :items="['Barangay Captain', 'Secretary', 'Staff']"
                                    label="Position Title" :rules="[rules.required]" item-text="" item-value="" outlined />

                            </template>

                            <v-text-field v-model="form.username" label="Username" :rules="[rules.required]" />
                            <v-text-field v-model="form.email" label="Email" :rules="[rules.required]" />
                            <v-text-field v-model="form.password" :type="showPassword ? 'text' : 'password'"
                                label="Password" :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                                @click:append-inner="showPassword = !showPassword"
                                :rules="[!isEditMode ? rules.required : () => true]" />



                            <v-select v-model="form.status" :items="['active', 'inactive']" label="Status"
                                :rules="[rules.required]" />
                            <v-autocomplete v-model="form.resident_id"  :items="residents" item-value="resident_id"
                                :item-title="item => `${item.resident_id || 'Resident Number'} - ${item.full_name || 'Full Name'}`"
                                label="Select Resident" :rules="[rules.required]" dense clearable />


                        </v-form>
                    </v-card-text>
                    <v-card-actions class="justify-end">
                        <v-btn @click="dialog = false" color="grey">Close</v-btn>
                        <v-btn @click="saveUser" color="#0A24C2">Save</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>


            <v-table v-if="tableViewRole === 'admin'">

                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created at</th>
                        <th>Updated at</th>
                        <th>Edit</th>
                        <th>Archive</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="user in filteredData" :key="user.user_id">
                        <td>{{ user.username }}</td>
                        <td>{{ user.email }}</td>
                        <td>{{ user.role }}</td>
                        <td>{{ user.status }}</td>
                        <td>{{ user.created_at }}</td>
                        <td>{{ user.updated_at }}</td>
                        <td>
                            <v-btn color="#0A24C2" @click="openDialog(true, user)">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                        </td>
                        <td>
                            <v-btn color="#FDC804" @click="archiveUser(user.user_id)">
                                <v-icon>mdi-archive</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                </tbody>
            </v-table>

            <v-table v-else-if="tableViewRole === 'official'">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Position Title</th>
                        <th>Full Name</th>
                        <th>Status</th>
                        <th>Start Term</th>
                        <th>End Term</th>
                        <th>Created at</th>
                        <th>Updated at</th>
                        <th>Edit</th>
                        <th>Archive</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="user in filteredData" :key="user.user_id">
                        <td>{{ user.username }}</td>
                        <td>{{ user.email }}</td>
                        <td>{{ user.role }}</td>
                        <td>{{ user.position_title }}</td>
                        <td>{{ user.resident_id }}</td>
                        <td>
                            {{
                                residents.find(r => r.resident_id === user.resident_id)?.full_name || 'N/A'
                            }}
                        </td>
                        <td>{{ user.status }}</td>
                        <td>{{ user.start_term }}</td>
                        <td>{{ user.end_term }}</td>
                        <td>{{ user.created_at }}</td>
                        <td>{{ user.updated_at }}</td>
                        <td>
                            <v-btn color="#0A24C2" @click="openDialog(true, user)">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                        </td>
                        <td>
                            <v-btn color="#FDC804" @click="archiveUser(user.user_id)">
                                <v-icon>mdi-archive</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                </tbody>


            </v-table>
            <v-table v-else>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Resident Number</th>
                        <th>Full Name</th>
                        <th>Status</th>
                        <th>Created at</th>
                        <th>Updated at</th>
                        <th>Edit</th>
                        <th>Archive</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in filteredData" :key="user.user_id">
                        <td>{{ user.username }}</td>
                        <td>{{ user.email }}</td>
                        <td>{{ user.role }}</td>
                        <td>{{ user.resident_id }}</td>
                        <td>
                            {{
                                residents.find(r => r.resident_id === user.resident_id)?.full_name || 'N/A'
                            }}
                        </td>
                        <td>{{ user.status }}</td>
                    
                        <td>{{ user.created_at }}</td>
                        <td>{{ user.updated_at }}</td>
                        <td>
                            <v-btn color="#0A24C2" @click="openDialog(true, user)">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                        </td>
                        <td>
                            <v-btn color="#FDC804" @click="archiveUser(user.user_id)">
                                <v-icon>mdi-archive</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                </tbody>

            </v-table>

        </v-container>
    </template>
