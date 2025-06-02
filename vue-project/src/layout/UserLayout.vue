<script setup>
import UserSideBar from '@/components/user/UserSideBar.vue';
import UserTopBar from '@/components/user/UserTopBar.vue';
</script>

<template>
  <v-layout>
    <UserSideBar />
    <v-main>
      <UserTopBar/>
      <router-view /> <!-- Page content -->
    </v-main>
  </v-layout>
</template>
