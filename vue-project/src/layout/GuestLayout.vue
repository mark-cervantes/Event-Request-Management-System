<script setup>
import GuestSideBar from '@/components/guest/GuestSideBar.vue';
import GuestTopBar from '@/components/guest/GuestTopBar.vue';
</script>

<template>
  <v-layout>
    <GuestSideBar />
    <v-main>
      <GuestTopBar />
      <router-view /> <!-- Page content -->
    </v-main>
  </v-layout>
</template>
