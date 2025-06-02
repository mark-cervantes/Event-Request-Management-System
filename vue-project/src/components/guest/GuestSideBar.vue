<script setup>
import BrgyLogo from '@/assets/brgy_logo.png'

import { ref } from 'vue'
import { useRouter } from 'vue-router'

const logoutDialog = ref(false)
const router = useRouter()

function confirmLogout() {
    logoutDialog.value = true
}

function logout() {
    // Clear session or token
    localStorage.removeItem('authToken')
    localStorage.removeItem('user')

    // Redirect to login page
    router.push({ name: 'Login' })
}
</script>


<template>
    <v-dialog v-model="logoutDialog" max-width="400">
        <v-card>
            <v-card-title class="text-h6">Confirm Logout</v-card-title>
            <v-card-text>Are you sure you want to log out?</v-card-text>
            <v-card-actions class="justify-end">
                <v-btn text @click="logoutDialog = false">Cancel</v-btn>
                <v-btn color="red" dark @click="logout">Logout</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-navigation-drawer permanent location="left" width="250" class="sidebar">
        <div class="logo-container" style="color: #FDC804;">
            <v-img :src="BrgyLogo" max-width="120" class="mx-auto mt-7" />
            <div class="text-center" style="line-height: 1;">
                <p class="font-weight-light" style="font-size: 20px;">BARANGAY</p>
                <p class="font-weight-bold" style="font-size: 24px;">SANTA CRUZ</p>
                <br><br>
            </div>
        </div>

        <v-divider class="my-3" />

        <v-list nav dense class="ml-2 custom-font">
            <v-list-item prepend-icon="mdi-home-outline" title="Home" :to="{ name: 'UserHomepage' }" />
            <v-list-item prepend-icon="mdi-calendar" title="Event Scheduling"
                :to="{ path: '/admin/event-scheduling' }" />
            <v-list-item prepend-icon="mdi-message-alert" title="Complaints" :to="{ path: '/admin/complaints' }" />
        </v-list>

        <v-divider></v-divider>
        <v-list nav dense class="ml-2 custom-font align-center">

            <v-list-item prepend-icon="mdi-logout" title="Logout" @click="confirmLogout" />
        </v-list>
    </v-navigation-drawer>
</template>

<style scoped>
.sidebar {
    background: linear-gradient(90deg, #001282 0%, #0A24C2 100%, #475EEB 80%);
    color: white;
}

.custom-font {
    font-size: 20px !important;
    padding: 12px 0;
    cursor: pointer;
}

.logo-container {
    text-align: center;
}

::v-deep(.v-list-item) {
    transition: border 0.3s ease, color 0.3s ease;
    border-left: 4px solid transparent;
    border-radius: 0;
    font-size: 24px;
}

::v-deep(.v-list-item:hover) {
    border-left: 4px solid #FDC804;
    font-weight: bold;
}

::v-deep(.v-list-item--active) {
    border-left: 4px solid #FDC804 !important;
    font-weight: bold;
    color: white !important;
}
</style>
