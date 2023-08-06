<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import TableHeaderCell from '../../components/core/Table/TableHeaderCell.vue';
import Spinner from '../../components/core/Spinner.vue';
import store from '../../store';
import { CUSTOMERS_PER_PAGE } from '../../constants'
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { DotsVerticalIcon, PencilIcon, TrashIcon } from "@heroicons/vue/outline";

const per_page = ref(CUSTOMERS_PER_PAGE)
const search = ref('')
const customers = computed(() => store.state.customers)
const sortField = ref('updated_at')
const sortDirection = ref('desc')

onMounted(() => {
  getCustomers()
})

let timer = null;

watch(search, (newSearch) => {
  if (timer) {
    clearTimeout(timer);
  }
  timer = setTimeout(() => {
    getCustomers(null, newSearch);
  }, 1000);
});

function getCustomers(url = null, newSearch = search.value) {
  store.dispatch('getCustomers', {
    url,
    sort_field: sortField.value,
    sort_direction: sortDirection.value,
    search: newSearch,
    per_page: per_page.value
  });
}

function getForPage(link) {
  if (!link.url || link.active) {
    return
  }
  getCustomers(link.url, search.value)
}

function sortCustomer(field) {
  if (sortField.value === field) {
    if (sortDirection.value === 'asc') {
      sortDirection.value = 'desc'
    } else {
      sortDirection.value = 'asc'
    }
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }

  getCustomers()
}

function deleteCustomer(customer) {
  if (!confirm(`本当に削除してもいいですか？`)) {
    return
  }
  store.dispatch('deleteCustomer', customer.id)
    .then(() => {
      store.commit('showToast', [`顧客を削除しました。`])
      store.dispatch('getCustomers')
    })
}
</script>

<template>
  <div class="bg-white p-5 rounded-lg shadow animate-fade-in-down">
    <div class="flex justify-between border-b-2 pb-3 items-end">
      <div class="flex items-center">
        <select @change="getCustomers(null)" v-model="per_page"
          class="appearance-none relative block w-24 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
          <option value="5">5</option>
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
        <span class="whitespace-nowrap ml-3">件ごとに表示</span>
      </div>
      <div class="flex items-end">
        <div>
          <p class="text-xs mb-1">【氏名(カナ)、電話番号、メールアドレス】</p>
          <input type="text" v-model="search" placeholder="顧客を検索"
            class="appearance-none relative block w-48 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
        </div>
        <button type="button" @click="search = ''"
          class="ml-4 py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">リセット</button>
      </div>
    </div>
    <table class="table-auto w-full">
      <thead>
        <tr>
          <TableHeaderCell @click="sortCustomer" field="user_id" :sort-field="sortField" :sort-direction="sortDirection">
            ID
          </TableHeaderCell>
          <TableHeaderCell @click="sortCustomer" field="name" :sort-field="sortField" :sort-direction="sortDirection">氏名
          </TableHeaderCell>
          <TableHeaderCell @click="sortCustomer" field="email" :sort-field="sortField" :sort-direction="sortDirection">
            メールアドレス
          </TableHeaderCell>
          <TableHeaderCell @click="sortCustomer" field="phone" :sort-field="sortField" :sort-direction="sortDirection">
            電話番号
          </TableHeaderCell>
          <TableHeaderCell @click="sortCustomer" field="status" :sort-field="sortField" :sort-direction="sortDirection">
            ステータス
          </TableHeaderCell>
          <TableHeaderCell @click="sortCustomer" field="updated_at" :sort-field="sortField"
            :sort-direction="sortDirection">
            更新日
          </TableHeaderCell>
          <TableHeaderCell field="actions">
            変更
          </TableHeaderCell>
        </tr>
      </thead>
      <tbody v-if="!customers.loading">
        <tr v-for="(customer, index) of customers.data" class="animate-fade-in-down"
          :style="{ 'animation-delay': `${index * 0.05}s` }">
          <td class="border-b p-2">{{ customer.id }}</td>
          <td class="border-b p-2">
            {{ customer.last_name }} {{ customer.first_name }}
          </td>
          <td class="border-b p-2 max-w-[200px] white-space-nowrap overflow-hidden text-ellipsis">{{
            customer.email }}</td>
          <td class="border-b p-2">{{ customer.phone }}</td>
          <td class="border-b p-2">{{ customer.status ? 'アクティヴ' : '非アクティブ' }}</td>
          <td class="border-b p-2">{{ customer.updated_at }}</td>
          <td class="border-b p-2">
            <Menu as="div" class="relative inline-block text-left">
              <div>
                <MenuButton class="inline-flex items-center justify-center rounded-full w-10 h-10 bg-black bg-opacity-0">
                  <DotsVerticalIcon class="h-5 w-5 text-indigo-500" aria-hidden="true" />
                </MenuButton>
              </div>
              <transition enter-active-class="transition duration-100 ease-out"
                enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-in" leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0">
                <MenuItems
                  class="absolute z-10 left-[85%] -top-1/2 w-24 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-">
                  <div class="px-1 py-1">
                    <MenuItem v-slot="{ active }">
                    <router-link :class="[
                      active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                      'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                    ]" :to="{ name: 'app.customers.view', params: { id: customer.id } }">
                      <PencilIcon :active="active" class="mr-2 h-5 w-5 text-indigo-400" aria-hidden="true" />
                      編集
                    </router-link>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                    <button :class="[
                      active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                      'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                    ]" @click="deleteCustomer(customer)">
                      <TrashIcon :active="active" class="mr-2 h-5 w-5 text-indigo-400" aria-hidden="true" />
                      削除
                    </button>
                    </MenuItem>
                  </div>
                </MenuItems>
              </transition>
            </Menu>
          </td>
        </tr>
      </tbody>
      <tbody v-else>
        <tr>
          <td colspan="7">
            <Spinner />
          </td>
        </tr>
      </tbody>
    </table>
    <div v-if="!customers.loading && customers.total > customers.limit" class="flex justify-between items-center mt-5">
      <span>{{ customers.from }}件から{{ customers.to }}件を表示</span>
      <nav class="relative z-0 inline-flex justify-center rounded-md shadow-sm -space-x-px" aria-label="Pagination">
        <a v-for="(link, i) of customers.links" :key="i" :disabled="!link.url" href="#" @click.prevent="getForPage(link)"
          aria-current="page"
          class="relative inline-flex items-center px-4 py-2 border text-sm font-medium whitespace-nowrap" :class="[
            link.active
              ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
              : 'bg-white border-gray-300 text-gray-500 hover:bg-indigo-50',
            i === 0 ? 'rounded-l-md' : '',
            i === customers.links.length - 1 ? 'rounded-r-md' : '',
            !link.url ? 'bg-gray-100 text-gray-700' : ''
          ]" v-html="link.label">
        </a>
      </nav>
    </div>
  </div>
</template>

<style scoped>
</style>
