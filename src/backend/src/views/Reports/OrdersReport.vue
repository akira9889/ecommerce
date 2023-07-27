<script setup>
import { ref } from 'vue';
import axiosClient from '../../axios'
import { onMounted } from 'vue';
import Bar from '../../components/core/Charts/Bar.vue';
import CustomInput from '../../components/core/CustomInput.vue'
import Spinner from '../../components/core/Spinner.vue';

const chartData = ref({
  labels: [],
  datasets: [{
    data: [],
    backgroundColor: [],
  }]
})

const options = {
  responsive: true,
  maintainAspectRatio: false,
  scales: {
    y: {
      ticks: {
        stepSize: 1
      }
    }
  }
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

const chosenDate = ref('1m')

const loading = ref(false)

onMounted(() => {
  getData()
})

function onChangeDate() {
  getData()
}

function getData() {
  loading.value = true
  axiosClient.get('reports/orders', { params: { d: chosenDate.value } })
    .then(({ data }) => {
      chartData.value = data
      loading.value = false
    })
}

</script>

<template>
  <div>
    <CustomInput type="select" @change="onChangeDate" v-model="chosenDate" :select-options="dateOptions" />
    <div class="h-[300px] flex items-center justify-center">
      <Bar v-if="!loading" :data="chartData" :options="options" />
      <Spinner v-else message=""/>
    </div>
  </div>
</template>

<style scoped>
</style>
