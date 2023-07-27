<script setup>

</script>

<template>
  <div>
    <div class="grid gap-3 grid-cols-2">
      <router-link :to="{ name: 'reports.orders' }"
                    class="bg-white py-2 px-3  mx-2 text-gray-700 rounded-md shadow-md text-center hover:bg-indigo-600 hover:text-white transition"
                    active-class="text-white bg-indigo-400"
                    >
                    注文数
      </router-link>
      <router-link :to="{ name: 'reports.customers' }"
                    class="bg-white py-2 px-3  mx-2 text-gray-700 rounded-md shadow-md text-center hover:bg-indigo-400 hover:text-white transition"
                    active-class="text-white bg-indigo-400"
                    >
                    顧客登録数
      </router-link>
    </div>
    <div class="p-3 mt-3 bg-white rounded-md shadow-md grid grid-cols-1">
      <router-view />
    </div>
  </div>
</template>

<style scoped>
</style>
