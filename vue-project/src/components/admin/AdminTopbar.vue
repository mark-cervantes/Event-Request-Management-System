<script setup>
import { ref } from 'vue'
const userFullName = ref('Admin')
</script>

<template>
    <div class="topbar d-flex justify-space-between align-center px-8 py-2">
        <div class="d-flex align-center justify-end w-100">
            <v-avatar size="22" color="gray" >
                <v-icon small>mdi-account</v-icon>
            </v-avatar>
            <span class="ml-2 mr-8 text-subtitle-1 font-weight-medium">{{ userFullName }}</span>
        </div>

    </div>
</template>

<style scoped>
.topbar {
    background-color: white;
    border-bottom: 1px solid #ccc;
    height: 56px;
}
</style>
