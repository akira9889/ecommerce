<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Core as YubinBangoCore } from 'yubinbango-core2'
import Spinner from '../../components/core/Spinner.vue';
import store from '../../store';
import CustomInput from '../../components/core/CustomInput.vue';
import { useRoute } from 'vue-router';
import router from '../../router';
const route = useRoute()

const DEFAULT_CUSTOMER = {
  id: '',
  first_name: '',
  last_name: '',
  first_kana: '',
  last_kana: '',
  email: '',
  phone: '',
  status: false,
  billingAddress: {
    country_code: '',
    zipcode: '',
    state: '',
    city: '',
    address1: '',
    address2: '',
  },
  shippingAddress: {
    country_code: '',
    zipcode: '',
    state: '',
    city: '',
    address1: '',
    address2: '',
  },
}

const customer = ref({ ...DEFAULT_CUSTOMER })
const loading = ref(false)
let errorMsg = ref({})

onMounted(() => {
  loading.value = true
  store.dispatch('getCustomer', route.params.id)
    .then(({ data }) => {
      customer.value = data
      loading.value = false
    })
})

const countries = computed(() => store.state.countries.map(c => ({ key: c.code, text: c.name })))

const billingCountry = computed(() => store.state.countries.find(c => {
  return c.code === customer.value.billingAddress.country_code
}))
const billingStateOptions = computed(() => {
  if (!billingCountry.value || !billingCountry.value.states) return []
  return Object.entries(billingCountry.value.states).map(c => ({ key: c[0], text: c[1] }))
})

const shippingCountry = computed(() => store.state.countries.find(c => c.code === customer.value.shippingAddress.country_code))
const shippingStateOptions = computed(() => {
  if (!shippingCountry.value || !shippingCountry.value.states) return []
  return Object.entries(shippingCountry.value.states).map(c => ({ key: c[0], text: c[1] }))
})

watch(() => customer.value.billingAddress.country_code, () => {
  if (!billingCountry.value || !billingCountry.value.states.hasOwnProperty(customer.value.billingAddress.state)) {
    customer.value.billingAddress.state = '';
  }
});

watch(() => customer.value.shippingAddress.country_code, () => {
  if (!shippingCountry.value || !shippingCountry.value.states.hasOwnProperty(customer.value.shippingAddress.state)) {
    customer.value.shippingAddress.state = '';
  }
});

watch(() => customer.value.billingAddress.zipcode, (newZipcode) => {
  if (newZipcode) {
    new YubinBangoCore(newZipcode, (addr) => {
      let state = billingStateOptions.value.find(s => {
        return s.text === addr.region
      })

      if (state) {
        customer.value.billingAddress.state = state.key;
        customer.value.billingAddress.city = addr.locality;
        customer.value.billingAddress.city += addr.street;
      }

    });
  }
});

watch(() => customer.value.shippingAddress.zipcode, (newZipcode) => {
  if (newZipcode) {
    new YubinBangoCore(newZipcode, (addr) => {
      let state = shippingStateOptions.value.find(s => {
        return s.text === addr.region
      })

      if (state) {
        customer.value.shippingAddress.state = state.key;
        customer.value.shippingAddress.city = addr.locality;
        customer.value.shippingAddress.city += addr.street;
      }

    });
  }
});

function onSubmit() {
  loading.value = true
  if (customer.value.id) {
    store.dispatch('updateCustomer', customer.value)
      .then(() => {
        loading.value = false
        errorMsg.value = {}
        store.commit('showToast', [`顧客情報を更新しました。`])
        router.push({name: 'app.customers'})
      })
      .catch(({ response }) => {
        loading.value = false;
        errorMsg.value = response.data
      })
  }
}
</script>

<template>
  <div class="bg-white p-5">
    <h2 class="flex justify-between items-center text-2xl font-bold pb-2 border-b border-gray-300">
      顧客情報
    </h2>
    <form @submit.prevent="onSubmit" class="relative">
      <Spinner v-if="loading"
        class="absolute top-0 right-0 bottom-0 left-0 bg-white flex items-center justify-center z-20" />
      <div>
        <div class="md:w-1/2">
          <div class="grid grid-cols-2 gap-4 items-end">
            <CustomInput v-model="customer.last_name" label="姓" :errorMsg="errorMsg.last_name" />
            <CustomInput v-model="customer.first_name" label="名" :errorMsg="errorMsg.first_name" />
          </div>
          <div class="grid grid-cols-2 gap-4 items-end">
            <CustomInput v-model="customer.last_kana" label="セイ" :errorMsg="errorMsg.last_kana" />
            <CustomInput v-model="customer.first_kana" label="メイ" :errorMsg="errorMsg.first_kana" />
          </div>
          <CustomInput v-model="customer.email" label="メールアドレス" :errorMsg="errorMsg.email" />
          <CustomInput v-model="customer.phone" label="電話番号" :errorMsg="errorMsg.phone" />
          <CustomInput type="checkbox" v-model="customer.status" label="ステータス" :errorMsg="errorMsg.status" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <h2 class="text-xl font-semibold mt-6 pb-2 border-b border-gray-300">請求先住所</h2>
            <p v-if="errorMsg.billingAddress && Object.keys(errorMsg.billingAddress).length > 0"
              class="text-red-500 text-sm">請求先住所を設定してください</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
              <CustomInput type="select" :select-options="countries" v-model="customer.billingAddress.country_code"
                label="国•地域" />
              <CustomInput v-model="customer.billingAddress.zipcode" label="郵便番号" />
              <CustomInput type="select" :select-options="billingStateOptions" v-model="customer.billingAddress.state"
                label="都道府県" />
              <CustomInput v-model="customer.billingAddress.city" label="市町村" />
              <CustomInput v-model="customer.billingAddress.address1" label="丁目•番地•号" />
              <CustomInput v-model="customer.billingAddress.address2" label="建物名" />
            </div>
          </div>
          <div>
            <h2 class="text-xl font-semibold mt-6 pb-2 border-b border-gray-300">配達先住所</h2>
            <p v-if="errorMsg.shippingAddress && Object.keys(errorMsg.shippingAddress).length > 0"
              class="text-red-500 text-sm">配達先住所を設定してください</p>
            <div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <CustomInput type="select" :select-options="countries" v-model="customer.shippingAddress.country_code"
                  label="国•地域" />
                <CustomInput v-model="customer.shippingAddress.zipcode" label="郵便番号" />
                <CustomInput type="select" :select-options="shippingStateOptions" v-model="customer.shippingAddress.state"
                  label="都道府県" />
                <CustomInput v-model="customer.shippingAddress.city" label="市町村" />
                <CustomInput v-model="customer.shippingAddress.address1" label="丁目•番地•号" />
                <CustomInput v-model="customer.shippingAddress.address2" label="建物名" />
              </div>
            </div>
          </div>
        </div>
      </div>
      <footer class="sm:flex sm:flex-row-reverse mt-3">
        <button type="submit" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 text-base font-medium  focus:outline-none focus:ring-2 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm
                                text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500">
          送信
        </button>
        <router-link :to="{name: 'app.customers'}" type="button"
          class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
          キャンセル
        </router-link>
      </footer>
    </form>
  </div>
</template>

<style scoped>
</style>
