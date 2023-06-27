<script setup>
defineProps({
  order: Object,
  orderStatuses: Object
})
</script>

<template>
  <small class="text-white p-1 rounded" :class="{
    'bg-emerald-500': ['paid', 'completed'].includes(order.status),
    'bg-orange-400': order.status === 'shipped',
    'bg-red-500': order.status === 'canceled',
    'bg-gray-400': order.status === 'unpaid',
  }">
    {{ orderStatuses[order.status] }}
  </small>
</template>

<style scoped>
</style>
