<template>
  <span
    :class="badgeClass"
    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
  >
    {{ props.status }}
  </span>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props =defineProps<{
  status: string
}>()

const badgeClass = computed(() => {
  switch (props.status) {
    case 'Completed':
      return 'bg-green-100 text-green-800'
    case 'In Progress':
      return 'bg-blue-100 text-blue-800'
    case 'Todo':
      return 'bg-gray-100 text-gray-800'
    case 'Blocked':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
})
</script>
