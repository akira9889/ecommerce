<script setup>
import UsersTable from './UsersTable.vue'
import UserModal from './UserModal.vue'
import { ref, watch, computed, onMounted } from 'vue'
import store from '../../store';
import { USERS_PER_PAGE } from '../../constants'

const users = computed(() => store.state.users)
const perPage = ref(USERS_PER_PAGE)
const search = ref('')
const sortField = ref('updated_at')
const sortDirection = ref('desc')

const editingUser = ref({})
const showModal = ref(false)

onMounted(() => {
    getUsers()
})

let timer = null;

watch(search, (newSearch) => {
    if (timer) {
        clearTimeout(timer);
    }
    timer = setTimeout(() => {
        getUsers(null, newSearch);
    }, 1000);
});

function getUsers(url = null, newSearch = search.value) {
    store.dispatch('getUsers', {
        url,
        sort_field: sortField.value,
        sort_direction: sortDirection.value,
        search: newSearch,
        perPage: perPage.value
    });
}

function getForPage(link) {
    if (!link.url || link.active) {
        return
    }
    getUsers(link.url, search.value)
}

function sortUser(field) {
    sortDirection.value = (sortField.value === field && sortDirection.value === 'asc') ? 'desc' : 'asc';
    sortField.value = field
    getUsers()
}

function createNewUser() {
    editingUser.value = {}
    showUserModal()
};

function editUser(user) {
    editingUser.value = user
    showUserModal()
}

function deleteUser() {
    getUsers()
}

function showUserModal() {
    showModal.value = true
}

function closeUserModal() {
    showModal.value = false
}

function submit() {
    closeUserModal()
    sortField.value = 'updated_at'
    sortDirection.value = 'desc'
    search.value = '';
    getUsers()
}

</script>

<template>
    <div class="flex items-center justify-between mb-3">
        <h1 class="text-3xl font-semibold">管理者一覧</h1>
        <button type="button" @click="createNewUser"
            class="py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            管理者を追加
        </button>
    </div>
    <div class="flex justify-between px-5 pb-3 items-end">
        <div class="flex items-center">
            <select @change="getUsers(null)" v-model="perPage"
                class="appearance-none relative block w-24 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span class="whitespace-nowrap ml-3">件ごとに表示</span>
        </div>
        <div>
            <p class="text-xs mb-1">氏名(カナ)</p>
            <input type="text" v-model="search" placeholder="カタカナ検索"
                class="appearance-none relative block w-48 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
        </div>
    </div>
    <UserModal v-model="showModal" :user="editingUser" @close="closeUserModal" @submit="submit" />
    <UsersTable :users="users" :sortField="sortField" :sortDirection="sortDirection" @sortUser="sortUser"
        @editUser="editUser" @deleteUser="deleteUser"/>
    <div v-if="!users.loading && users.total > users.limit" class="flex justify-between items-center mt-5">
        <span>{{ users.from }}件から{{ users.to }}件を表示</span>
        <nav class="relative z-0 inline-flex justify-center rounded-md shadow-sm -space-x-px" aria-label="Pagination">
            <a v-for="(link, i) of users.links" :key="i" :disabled="!link.url" href="#" @click.prevent="getForPage(link)"
                aria-current="page"
                class="relative inline-flex items-center px-4 py-2 border text-sm font-medium whitespace-nowrap" :class="[
                    link.active
                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                        : 'bg-white border-gray-300 text-gray-500 hover:bg-indigo-50',
                    i === 0 ? 'rounded-l-md' : '',
                    i === users.links.length - 1 ? 'rounded-r-md' : '',
                    !link.url ? 'bg-gray-100 text-gray-700' : ''
                ]" v-html="link.label">
            </a>
        </nav>
    </div>
</template>

<style scoped>
</style>
