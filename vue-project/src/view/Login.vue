<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { buildApiUrl } from '@/utils/api.js'

import BrgyLogo from '@/assets/brgy_logo.png'

const username = ref('')
const password = ref('')
const visible = ref(false)
const router = useRouter()

const selectedRole = ref('user')

const login = async () => {
  try {
    const response = await axios.get(buildApiUrl('users/read_users.php'))
    const users = response.data.data || []

    const foundUser = users.find(user =>
      user.username === username.value &&
      user.password === password.value &&
      user.role === selectedRole.value
    )

    if (foundUser) {
      if (foundUser.status === 'archived') {
        alert('This account has been archived and cannot log in.')
        return
      }

      if (selectedRole.value === 'admin' || selectedRole.value === 'official') {
        router.push('/AdminDashboard')
      } else if (selectedRole.value === 'user') {
        router.push('/UserHomepage')
      }
    } else if (selectedRole.value === 'guest') {
      router.push('/GuestHomepage')
    } else {
      alert('Invalid credentials')
    }
  } catch (error) {
    console.error('Login failed:', error)
    alert('Login failed. Check console for details.')
  }
}

</script>


<template>
  <v-app>

    <!-- Main Section with Container -->
    <v-main>
      <v-container fluid class="fill-height d-flex align-center justify-center" style="background-color: #0238AE">
        <!-- Fixed Width Inner Container -->
        <v-container class="pa-10 elevation-15 mx-auto"
          style="max-width: 900px; min-height: 500px; border-radius: 16px; background: linear-gradient(90deg, #001282 0%, #0A24C2 100%, #475EEB 80%);">
          <v-row no-gutters>
            <!-- Left Side (Welcome) -->
            <v-col cols="12" md="6" class="d-flex flex-column justify-center px-2">
              <div>
                <!-- Logo and Barangay Text -->
                <div class="d-flex align-center mb-10">
                  <v-img :src="BrgyLogo" max-width="60" class="mr-4"></v-img>
                  <div style="line-height: 1;">
                    <div style="color: #FDC804; font-size: 20px;">BARANGAY</div>
                    <div style="color: #FFD700; font-size: 24px; font-weight: bold;">SANTA CRUZ</div>
                  </div>
                </div>

                <h1 style="color: white; font-size: 48px;">Welcome!</h1>
                <br>
                <p class="mt-6 mb-3" style="color: #c0c0c0; max-width: 400px;">
                  eBarangay Sta. Cruz: Your Gateway to Local Services Access complaint forms, event scheduling, and
                  business clearance services—all in one place.
                </p>

                <br>
                <div class="d-flex align-center mt-8" style="max-width: 400px;">
                  <v-icon color="#FDC804" class="mr-1">mdi-map-marker</v-icon>
                  <p class="mb-0" style="color: #FDC804;">
                    Sta. Cruz Naga City, Camarines Sur
                  </p>
                </div>


              </div>
            </v-col>
            <!-- Right Side (Login Form) -->
            <!-- Login Form -->
            <v-col cols="12" md="6" class="d-flex justify-center align-center">
              <v-card flat class="pa-8" width="100%" max-width="400"
                style="background-color: rgba(255, 255, 255, 0.08); border-radius: 16px; color: white;">
                <div class="text-center mb-6">
                  <h2 style="color: white; font-weight: bold;">Login</h2>
                </div>

                <v-form>

                  <v-select v-model="selectedRole" :items="['admin', 'official', 'user']" label="Login as"
                    variant="outlined" class="mt-4" prepend-inner-icon="mdi-account-key" density="comfortable"
                    block></v-select>

                  <v-text-field v-model="username" label="Username" placeholder="Your username"
                    prepend-inner-icon="mdi-account" variant="outlined" density="comfortable" class="mt-4" hide-details
                    block></v-text-field>

                  <v-text-field v-model="password" :type="visible ? 'text' : 'password'"
                    :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'" label="Password"
                    placeholder="Enter your password" prepend-inner-icon="mdi-lock-outline" variant="outlined"
                    density="comfortable" @click:append-inner="visible = !visible" class="mt-4" block></v-text-field>





                  <v-btn class="mt-6" block
                    style="background: linear-gradient(90deg, #FDC804 50%, #CA8B02 100%); color: black;" @click="login">
                    Login
                  </v-btn>

                  <v-btn class="mt-2" block variant="outlined" color="yellow"
                    style="color: #FFD700; border-color: #FFD700;" @click="() => { selectedRole = 'guest'; login() }">
                    Login as Guest
                  </v-btn>

                  <p style="color: #FFD700; font-style: italic; margin-top: 12px; font-size: 14px; text-align: center;">
                    *If you are not from Barangay Sta. Cruz, please login as Guest.*
                  </p>
                </v-form>
              </v-card>
            </v-col>
          </v-row>
        </v-container>
      </v-container>
    </v-main>
  </v-app>
</template>
