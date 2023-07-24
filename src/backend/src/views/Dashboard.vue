<script setup>
import axiosClient from '../axios'
import { onMounted, ref } from 'vue';
import Spinner from '../components/core/Spinner.vue'
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
import { Doughnut } from 'vue-chartjs'
import { UserIcon } from '@heroicons/vue/outline'


ChartJS.register(ArcElement, Tooltip, Legend)

const loading = ref({
  customersCount: true,
  productsCount: true,
  paidOrders: true,
  totalIncome: true,
  ordersByCountry: true,
  latestCustomers: true,
  latestOrders: true,
})

const customersCount = ref(0)
const productsCount = ref(0)
const paidOrders = ref(0)
const totalIncome = ref(0)
const ordersByCountry = ref(
  {
    labels: [],
    datasets: [{
      data: [],
      backgroundColor: [],
    }]
  }
)
const latestCustomers = ref({})
const latestOrders = ref({})

onMounted(() => {
  axiosClient.get('dashboard/customers-count')
    .then(({ data }) => {
      customersCount.value = data
      loading.value.customersCount = false
    })
  axiosClient.get('dashboard/products-count')
    .then(({ data }) => {
      productsCount.value = data
      loading.value.productsCount = false
    })
  axiosClient.get('dashboard/orders-count')
    .then(({ data }) => {
      paidOrders.value = data
      loading.value.paidOrders = false
    })
  axiosClient.get('dashboard/income-amount')
    .then(({ data }) => {
      totalIncome.value = new Intl.NumberFormat('ja-JP', { maximumSignificantDigits: 3 }).format(data)
      loading.value.totalIncome = false
    })
  axiosClient.get(`/dashboard/orders-by-country`)
    .then(({ data: countries }) => {
      const chartData = {
        labels: [],
        datasets: [{
          backgroundColor: ['#41B883', '#E46651'],
          data: []
        }]
      }
      countries.forEach(c => {
        chartData.labels.push(c.name);
        chartData.datasets[0].data.push(c.count);
      })
      ordersByCountry.value = chartData
      loading.value.ordersByCountry = false
    })
  axiosClient.get('dashboard/latest-customers')
    .then(({ data }) => {
      latestCustomers.value = data
      loading.value.latestCustomers = false
    })
  axiosClient.get('dashboard/latest-orders')
    .then(({ data }) => {
      latestOrders.value = data.data
      loading.value.latestOrders = false
    })
})

const options = {
  responsive: true,
  maintainAspectRatio: false
}
</script>

<template>
  <h1 class="text-4xl mb-3">ダッシュボード</h1>
  <hr>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
    <!-- Active Customers -->
    <div class="bg-white py-6 px-5 rounded-lg shadow-xl flex flex-col items-center justify-center">
      <label class="text-lg block font-semibold mb-2">アクティブ顧客</label>
      <template v-if="!loading.customersCount">
        <span class="text-3xl font-semibold py-2">{{ customersCount }}<span
            class="text-sm font-normal ml-2 inline-block">人</span></span>
      </template>
      <Spinner v-else message="" width="w-8" height="h-8" />
    </div>
    <!--/ Active Customers -->
    <!-- Active Products -->
    <div class="bg-white py-6 px-5 rounded-lg shadow-xl flex flex-col items-center justify-center">
      <label class="text-lg block font-semibold mb-2">公開商品</label>
      <template v-if="!loading.customersCount">
        <span class="text-3xl font-semibold py-2">{{ productsCount }}<span class="text-sm
        font-normal ml-2 inline-block">個</span></span>
      </template>
      <Spinner v-else message="" width="w-8" height="h-8" />
    </div>
    <!--/ Active Products -->
    <!-- Paid Orders -->
    <div class="bg-white py-6 px-5 rounded-lg shadow-xl flex flex-col items-center justify-center">
      <label class="text-lg block font-semibold mb-2">支払い済み注文</label>
      <template v-if="!loading.customersCount">
        <span class="text-3xl font-semibold py-2">{{ paidOrders }}<span
            class="text-sm font-normal ml-2 inline-block">件</span></span>
      </template>
      <Spinner v-else message="" width="w-8" height="h-8" />
    </div>
    <!--/ Paid Orders -->
    <!-- Total Income -->
    <div class="bg-white py-6 px-5 rounded-lg shadow-xl flex flex-col items-center">
      <label class="text-lg block font-semibold mb-2">売上額</label>
      <template v-if="!loading.customersCount">
        <span class="text-3xl py-2">{{ totalIncome }}<span class="text-sm font-normal ml-2 inline-block">円</span></span>
      </template>
      <Spinner v-else message="" width="w-8" height="h-8" />
    </div>
    <!--/ Total Income -->
  </div>

  <div class="grid grid-rows-2 grid-flow-row grid-cols-1 md:grid-cols-3 gap-3">
    <div class="md:col-span-2 md:row-span-2 bg-white py-6 px-5 rounded-lg shadow-xl flex flex-col">
      <label class="font-semibold">最新の注文</label>
      <template v-if="!loading.latestOrders">
        <div v-for="order of latestOrders" :key="order.id" class="py-2">
          <p>
            <router-link :to="{ name: 'app.orders.view', params: { id: order.id } }" class="mr-3 text-indigo-500" href="">注文ID {{ order.id }}</router-link>
          </p>
          <div class="flex justify-between">
            <div class="flex">
              <p class="mr-3">{{ order.number_of_items }}個の商品</p>
              <p>{{ $filters.currencyJPY(order.total_price) }}</p>
            </div>
            <p>{{ order.created_at }}</p>
          </div>
          <hr>
        </div>
      </template>
      <Spinner v-else message="" width="w-8" height="h-8" />
    </div>
    <div class="bg-white py-6 px-5 rounded-lg shadow-xl flex flex-col items-center justify-center">
      <label class="font-semibold">国ごとの注文数</label>
      <div class="w-full">
        <Doughnut v-if="!loading.ordersByCountry" :data="ordersByCountry" :options="options" />
        <Spinner v-else message="" />
      </div>
    </div>
    <div class="bg-white py-6 px-2 rounded-lg shadow-xl flex flex-col items-center justify-center">
      <label class="font-semibold">新規顧客</label>
      <div v-if="!loading.latestCustomers" class="lg:w-fit md:w-full w-fit text-left ">
        <router-link to="/" v-for="customer of latestCustomers" :key="customer.email"
          class="mb-3 flex hover:bg-gray-100 items-center">
          <div class="w-5 h-5 lg:w-12 lg:h-12 bg-gray-200 flex items-center justify-center rounded-full mr-2">
            <UserIcon class="lg:w-5 lg:h-5 w-4 h-4" />
          </div>
          <div class="lg:tex-sm text-xs flex flex-col md:w-4/5">
            <h3>{{ customer.last_name }} {{ customer.first_name }}</h3>
            <p class="break-words">{{ customer.email }}</p>
          </div>
        </router-link>
      </div>
      <Spinner v-else message="" width="w-8" height="h-8" />
    </div>
  </div>
</template>

<style scoped>
</style>
