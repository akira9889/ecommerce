<script setup>
import { onMounted, onUnmounted, ref, computed } from 'vue';
import Sidebar from './Sidebar.vue'
import Navbar from './Navbar.vue';
import Spinner from './core/Spinner.vue';
import Toast from './core/Toast.vue';
import store from '../store';

const sidebarOpened = ref(true)

const currentUser = computed(() => store.state.user.data)

function toggleSidebar() {
  sidebarOpened.value = !sidebarOpened.value
}

onMounted(() => {
  store.dispatch('getUser')
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
  <div v-if="currentUser.id" class="min-h-full bg-gray-200 flex">
    <!-- Sidebar -->
    <Sidebar :class="{ '-ml-[200px]': !sidebarOpened }" />
    <!-- Sidebar -->
    <div class="flex-1">
      <Navbar @toggle-sidebar="toggleSidebar" />
      <!-- Content -->
      <main class="p-6">
        <div class="p-4 rounded bg-white">
          <router-view></router-view>
        </div>
      </main>
      <!-- Content -->
    </div>
  </div>
  <div v-else class="min-h-full bg-gray-200 flex items-center justify-center">
    <Spinner />
  </div>
  <Toast />
</template>


<style scoped>
</style>
