<template>
  <span
    :class="badgeClass"
    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
  >
 {{ props.priority }}
  </span>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  priority: string
}>()

const badgeClass = computed(() => {
  switch (props.priority) {
    case 'High':
      return 'bg-red-100 text-red-800'
    case 'Medium':
      return 'bg-yellow-100 text-yellow-800'
    case 'Low':
      return 'bg-gray-100 text-gray-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
})
</script>
