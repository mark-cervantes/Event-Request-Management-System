<script setup>
import AdminSidebar from '@/components/admin/AdminSidebar.vue';
import AdminTopbar from '@/components/admin/AdminTopbar.vue';
</script>

<template>
  <v-layout>
    <AdminSidebar />
    <v-main>
      <AdminTopbar />
      <router-view /> <!-- Page content -->
    </v-main>
  </v-layout>
</template>
