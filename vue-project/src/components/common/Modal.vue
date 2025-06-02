<template>
  <v-dialog
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    :max-width="maxWidth"
    :persistent="persistent"
    :scrollable="scrollable"
  >
    <v-card>
      <v-card-title class="d-flex justify-space-between align-center">
        <span class="text-h5">{{ title }}</span>
        <v-btn
          v-if="!persistent"
          icon="mdi-close"
          variant="text"
          @click="$emit('update:modelValue', false)"
        />
      </v-card-title>
      
      <v-divider />
      
      <v-card-text class="pa-6">
        <slot />
      </v-card-text>
      
      <v-divider v-if="$slots.actions" />
      
      <v-card-actions v-if="$slots.actions" class="pa-6">
        <v-spacer />
        <slot name="actions" />
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: 'Modal',
  emits: ['update:modelValue'],
  props: {
    modelValue: {
      type: Boolean,
      default: false
    },
    title: {
      type: String,
      required: true
    },
    maxWidth: {
      type: [String, Number],
      default: 600
    },
    persistent: {
      type: Boolean,
      default: false
    },
    scrollable: {
      type: Boolean,
      default: true
    }
  }
}
</script>