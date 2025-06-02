// Generate a reusable vuetify data table component with sorting, filtering, and pagination features.
<template>
  <v-container>
    <v-data-table
      :headers="headers"
      :items="items"
      :search="externalSearch || search"
      :items-per-page="itemsPerPage"
      class="elevation-1"
    >
      <template v-slot:top v-if="!hideSearch">
        <v-text-field
          v-model="search"
          label="Search"
          class="mx-4"
          append-icon="mdi-magnify"
          single-line
          hide-details
        ></v-text-field>
      </template>
      
      <!-- Pass through all item slots -->
      <template v-for="(_, name) in $slots" :key="name" #[name]="slotData">
        <slot :name="name" v-bind="slotData" />
      </template>
    </v-data-table>
  </v-container>
</template>
<script>
// make the headers and items props for best reusability
export default {
  name: 'DataTable',
  props: {
    headers: {
      type: Array,
      required: true
    },
    items: {
      type: Array,
      required: true
    },
    itemsPerPage: {
      type: Number,
      default: 10
    },
    externalSearch: {
      type: String,
      default: ''
    },
    hideSearch: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      search: ''
    };
  }
};
</script>
// make the style fluid with the rest of the app
<style scoped>
.v-data-table {
  width: 100%;
  max-width: 100%;
}
.v-data-table .v-data-table__wrapper {
  overflow-x: auto;
}
.v-data-table .v-data-table__wrapper::-webkit-scrollbar {
  height: 8px;
}
.v-data-table .v-data-table__wrapper::-webkit-scrollbar-thumb {
  background-color: #ccc;
  border-radius: 4px;
}
.v-data-table .v-data-table__wrapper::-webkit-scrollbar-thumb:hover {
  background-color: #aaa;
}
.v-data-table .v-data-table__wrapper::-webkit-scrollbar-track {
  background-color: #f5f5f5;
}
.v-data-table .v-data-table__wrapper::-webkit-scrollbar-track:hover {
  background-color: #e0e0e0;
}
</style> 