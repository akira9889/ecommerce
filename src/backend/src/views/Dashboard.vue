<script setup>
import axiosClient from '../axios'
import { onMounted, ref } from 'vue';
import Spinner from '../components/core/Spinner.vue'
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
import { Doughnut } from 'vue-chartjs'
import { UserIcon } from '@heroicons/vue/outline'
import CustomInput from '../components/core/CustomInput.vue'

ChartJS.register(ArcElement, Tooltip, Legend)

const loading = ref([])

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

const fetchData = async (url, dataRef, formatter = (data) => data) => {
  loading.value[url] = true
  try {
    const { data } = await axiosClient.get(`dashboard/${url}`, { params: { d: chosenDate.value } })
    dataRef.value = formatter(data)
  } catch (error) {
    console.error(error)
  } finally {
    loading.value[url] = false
  }
}

onMounted(async () => {
  await Promise.all([
    fetchData('products-count', productsCount),
    fetchData('customers-count', customersCount),
    fetchData('orders-count', paidOrders),
    fetchData('income-amount', totalIncome, data => new Intl.NumberFormat('ja-JP').format(data)),
    fetchData('latest-orders', latestOrders, data => data.data),
    fetchData('latest-customers', latestCustomers),
    fetchData('orders-by-country', ordersByCountry, countries => {
      const chartData = {
        labels: [],
        datasets: [{
          backgroundColor: ['#41B883', '#E46651'],
          data: []
        }]
      }
      countries.forEach(c => {
        chartData.labels.push(c.name)
        chartData.datasets[0].data.push(c.count)
      })
      return chartData
    }),
  ])
})

function updateDashboard() {
  Promise.all([
    fetchData('customers-count', customersCount),
    fetchData('orders-count', paidOrders),
    fetchData('income-amount', totalIncome, data => new Intl.NumberFormat('ja-JP').format(data)),
    fetchData('orders-by-country', ordersByCountry, countries => {
      const chartData = {
        labels: [],
        datasets: [{
          backgroundColor: ['#41B883', '#E46651'],
          data: []
        }]
      }
      countries.forEach(c => {
        chartData.labels.push(c.name)
        chartData.datasets[0].data.push(c.count)
      })
      return chartData
    }),
  ])
}

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false
}

const dateOptions = ref([
  { key: 'today', text: '今日' },
  { key: '1d', text: '昨日' },
  { key: '1w', text: '過去1週間' },
  { key: '2w', text: '過去２週間' },
  { key: '1m', text: '過去1ヶ月間' },
  { key: '3m', text: '過去3ヶ月間' },
  { key: '6m', text: '過去６ヶ月間' },
  { key: '1y', text: '過去1年間' },
  { key: 'all', text: '全期間' },
])

const chosenDate = ref('all')

function onChangeDate() {
  updateDashboard()
}
</script>

<template>
  <div class="flex mb-2 items-center justify-between">
    <h1 class="text-3xl mb-3 font-semibold">ダッシュボード</h1>
    <CustomInput type="select" @change="onChangeDate" v-model="chosenDate" :select-options="dateOptions" />
  </div>
  <hr>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
    <!-- Active Products -->
    <div class="animate-fade-in-down bg-white py-6 px-5 rounded-lg shadow-xl flex flex-col items-center justify-center"
      style="animation-delay: 0.2s;">
      <label class="text-lg block font-semibold mb-2">公開商品</label>
      <template v-if="!loading['products-count']">
        <span class="text-3xl font-semibold py-2">{{ productsCount }}<span class="text-sm
        font-normal ml-2 inline-block">個</span></span>
      </template>
      <Spinner v-else message="" width="w-8" height="h-8" />
    </div>
    <!--/ Active Products -->
    <!-- Active Customers -->
    <div class="animate-fade-in-down bg-white py-6 px-5 rounded-lg shadow-xl flex flex-col items-center justify-center"
      style="animation-delay: 0.1s;">
      <label class="text-lg block font-semibold mb-2">アクティブ顧客</label>
      <template v-if="!loading['customers-count']">
        <span class="text-3xl font-semibold py-2">{{ customersCount }}<span
            class="text-sm font-normal ml-2 inline-block">人</span></span>
      </template>
      <Spinner v-else message="" width="w-8" height="h-8" />
    </div>
    <!--/ Active Customers -->
    <!-- Paid Orders -->
    <div class="animate-fade-in-down bg-white py-6 px-5 rounded-lg shadow-xl flex flex-col items-center justify-center"
      style="animation-delay: 0.3s;">
      <label class="text-lg block font-semibold mb-2">支払い済み注文</label>
      <template v-if="!loading['orders-count']">
        <span class="text-3xl font-semibold py-2">{{ paidOrders }}<span
            class="text-sm font-normal ml-2 inline-block">件</span></span>
      </template>
      <Spinner v-else message="" width="w-8" height="h-8" />
    </div>
    <!--/ Paid Orders -->
    <!-- Total Income -->
    <div class="animate-fade-in-down bg-white py-6 px-5 rounded-lg shadow-xl flex flex-col items-center"
      style="animation-delay: 0.4s;">
      <label class="text-lg block font-semibold mb-2">売上額</label>
      <template v-if="!loading['income-amount']">
        <span class="text-3xl py-2">{{ totalIncome }}<span class="text-sm font-normal ml-2 inline-block">円</span></span>
      </template>
      <Spinner v-else message="" width="w-8" height="h-8" />
    </div>
    <!--/ Total Income -->
  </div>

  <div class="grid grid-rows-2 grid-flow-row grid-cols-1 md:grid-cols-3 gap-3">
    <div class="md:col-span-2 md:row-span-2 bg-white py-6 px-5 rounded-lg shadow-xl flex flex-col">
      <label class="font-semibold">最新の注文</label>
      <template v-if="!loading['latest-orders']">
        <div v-for="order of latestOrders" :key="order.id" class="py-2">
          <p>
            <router-link :to="{ name: 'app.orders.view', params: { id: order.id } }" class="mr-3 text-indigo-500"
              href="">注文ID {{ order.id }}</router-link>
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
        <Doughnut v-if="!loading['orders-by-country']" :data="ordersByCountry" :options="chartOptions" />
        <Spinner v-else message="" />
      </div>
    </div>
    <div class="bg-white py-6 px-2 rounded-lg shadow-xl flex flex-col items-center justify-center">
      <label class="font-semibold">新規顧客</label>
      <div v-if="!loading['latest-customers']" class="lg:w-fit md:w-full w-fit text-left ">
        <router-link :to="{ name: 'app.customers.view', params: { id: customer.id } }" v-for="customer of latestCustomers"
          :key="customer.email" class="mb-3 flex hover:bg-gray-100 items-center">
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
