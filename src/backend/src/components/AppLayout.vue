<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import Sidebar from './Sidebar.vue'
import Navbar from './Navbar.vue';

const sidebarOpened = ref(true)

function toggleSidebar() {
  sidebarOpened.value = !sidebarOpened.value
}

onMounted(() => {
  handleSidebarOpened()
  window.addEventListener('resize', handleSidebarOpened)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleSidebarOpened)
})

function handleSidebarOpened() {
    sidebarOpened.value = window.outerWidth > 768
}
</script>

<template>
  <div class="min-h-full bg-gray-200 flex">
    <!-- Sidebar -->
    <Sidebar :class="{'-ml-[200px]': !sidebarOpened}"/>
    <!-- Sidebar -->
    <div class="flex-1">
      <Navbar @toggle-sidebar="toggleSidebar"/>
      <!-- Content -->
      <main class="p-6">
        <div class="p-4 rounded bg-white">
          <router-view></router-view>
        </div>
      </main>
      <!-- Content -->
    </div>
  </div>
</template>


<style scoped>
</style>
