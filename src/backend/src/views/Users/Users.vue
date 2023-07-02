<script setup>
import UsersTable from './UsersTable.vue'
import UserModal from './UserModal.vue'
import { ref } from 'vue';

const DEFAULT_USER = {
    id: '',
    name: '',
    email: '',
    password: '',
}

const showModal = ref(false)
const userModel = ref({ ...DEFAULT_USER })

function showUserModal() {
    showModal.value = true
}

function editUser(user) {
    userModel.value = user
    showUserModal()

}

function onModalClose() {
    userModel.value = { ...DEFAULT_USER }
}

</script>

<template>
    <div class="flex items-center justify-between mb-3">
        <h1 class="text-3xl font-semibold">ユーザー一覧</h1>
        <button type="submit" @click="showUserModal"
            class="py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            ユーザーを追加
        </button>
    </div>
    <UserModal v-model="showModal" :user="userModel" @close="onModalClose" />
    <UsersTable @clickEdit="editUser" />
</template>

<style scoped>
</style>
