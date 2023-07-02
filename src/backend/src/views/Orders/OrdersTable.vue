<script setup>
import TableHeaderCell from '../../components/core/Table/TableHeaderCell.vue';
import Spinner from '../../components/core/Spinner.vue';
import OrderStatus from './OrderStatus.vue';
import store from '../../store';
import { ref, onMounted, computed } from 'vue';
import { PRODUCTS_PER_PAGE } from '../../constants'
import axiosClient from '../../axios';

const perPage = ref(PRODUCTS_PER_PAGE)
const search = ref('')
const orders = computed(() => store.state.orders)
const sortField = ref('updated_at')
const sortDirection = ref('desc')

const emit = defineEmits(['clickEdit'])
const orderStatuses = ref([])

onMounted(() => {
  getOrders()

    axiosClient.get('/orders/statuses')
    .then(({ data }) => {
      orderStatuses.value = data
    })
})

function getOrders(url = null) {
  store.dispatch('getOrders', {
    url,
    sort_field: sortField.value,
    sort_direction: sortDirection.value,
    search: search.value,
    perPage: perPage.value
  })
}

function getForPage(link) {
  if (!link.url || link.active) {
    return
  }
  getOrders(link.url)
}

function sortOrder(field) {
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

  getOrders()
}

</script>

<template>
  <div class="bg-white p-5 rounded-lg shadow animate-fade-in-down">
    <div class="flex justify-between border-b-2 pb-3">
      <div class="flex items-center">
        <select @change="getOrders(null)" v-model="perPage"
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
        <input type="text" v-model="search" @change="getOrders(null)" placeholder="注文IDを検索"
          class="appearance-none relative block w-48 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
      </div>
    </div>
    <table class="table-auto w-full">
      <thead>
        <tr>
          <TableHeaderCell @click="sortOrder" field="id" :sort-field="sortField" :sort-direction="sortDirection">
            ID
          </TableHeaderCell>
          <TableHeaderCell :sort-field="sortField" :sort-direction="sortDirection">
            顧客
          </TableHeaderCell>
          <TableHeaderCell @click="sortOrder" field="status" :sort-field="sortField" :sort-direction="sortDirection">
            注文状況
          </TableHeaderCell>
          <TableHeaderCell @click="sortOrder" field="total_price" :sort-field="sortField" :sort-direction="sortDirection">
            値段
          </TableHeaderCell>
          <TableHeaderCell @click="sortOrder" field="crated_at" :sort-field="sortField" :sort-direction="sortDirection">
            日付
          </TableHeaderCell>
          <TableHeaderCell field="actions">
            詳細
          </TableHeaderCell>
        </tr>
      </thead>
      <tbody v-if="!orders.loading">
        <tr v-for="(order, index) of orders.data" class="animate-fade-in-down"
          :style="{ 'animation-delay': `${index * 0.05}s` }">
          <td class="border-b p-2">{{ order.id }}</td>
          <td class="border-b p-2">{{ order.customer.last_name }} {{ order.customer.first_name }}</td>
          <td class="border-b p-2">
            <OrderStatus :order="order" :orderStatuses="orderStatuses"/>
          </td>
          <td class="border-b p-2">{{ order.total_price }}円</td>
          <td class="border-b p-2 max-w-[200px] white-space-nowrap overflow-hidden text-ellipsis">{{
            order.created_at }}</td>
          <td class="border-b p-2">
            <router-link :to="{ name: 'app.orders.view', params: { id: order.id } }" class="w-8 h-8 rounded-full text-indigo-700 border border-indigo-700 flex justify-center items-center hover:text-white hover:bg-indigo-700">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </router-link>
          </td>
        </tr>
      </tbody>
      <tbody v-else>
        <tr>
          <td colspan="5">
            <Spinner />
          </td>
        </tr>
      </tbody>
    </table>
    <div v-if="!orders.loading && orders.total > orders.limit" class="flex justify-between items-center mt-5">
      <span>{{ orders.from }}件から{{ orders.to }}件を表示</span>
      <nav class="relative z-0 inline-flex justify-center rounded-md shadow-sm -space-x-px" aria-label="Pagination">
        <a v-for="(link, i) of orders.links" :key="i" :disabled="!link.url" href="#" @click.prevent="getForPage(link)"
          aria-current="page"
          class="relative inline-flex items-center px-4 py-2 border text-sm font-medium whitespace-nowrap" :class="[
            link.active
              ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
              : 'bg-white border-gray-300 text-gray-500 hover:bg-indigo-50',
            i === 0 ? 'rounded-l-md' : '',
            i === orders.links.length - 1 ? 'rounded-r-md' : '',
            !link.url ? 'bg-gray-100 text-gray-700' : ''
          ]" v-html="link.label">
        </a>
      </nav>
    </div>
  </div>
</template>

<style scoped>
</style>
