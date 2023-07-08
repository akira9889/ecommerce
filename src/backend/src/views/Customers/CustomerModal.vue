<script setup>
import { ref, computed, onUpdated } from 'vue'
import {
  TransitionRoot,
  TransitionChild,
  Dialog,
  DialogPanel,
  DialogTitle,
} from '@headlessui/vue'
import { Core as YubinBangoCore } from 'yubinbango-core2'
import Spinner from '../../components/core/Spinner.vue';
import store from '../../store';
import CustomInput from '../../components/core/CustomInput.vue';
import { watch } from 'vue';

const props = defineProps({
  modelValue: Boolean,
  customer: {
    required: true,
    type: Object
  }
})

const customer = ref({
  ...props.customer
})

const countries = computed(() => store.state.countries.map(c => ({ key: c.code, text: c.name })))

const billingCountry = computed(() => store.state.countries.find(c => c.code === customer.value.billingAddress.country_code))
const billingStateOptions = computed(() => {
  if (!billingCountry.value || !billingCountry.value.states) return []
  return Object.entries(billingCountry.value.states).map(c => ({ key: c[0], text: c[1] }))
})

const shippingCountry = computed(() => store.state.countries.find(c => c.code === customer.value.shippingAddress.country_code))
const shippingStateOptions = computed(() => {
  if (!shippingCountry.value || !shippingCountry.value.states) return []
  return Object.entries(shippingCountry.value.states).map(c => ({ key: c[0], text: c[1] }))
})

const loading = ref(false)

let errorMsg = ref({})

const emit = defineEmits(['update:modelValue', 'close', 'getCustomers'])

const show = computed({
  get: () => props.modelValue,
  set: value => emit('update:modelValue', value)
})

function closeModal() {
  show.value = false
  errorMsg.value = {}
  emit('close');
}

watch(() => customer.value.billingAddress.country_code, (newCountryCode, oldCountryCode) => {
  if (!billingCountry.value || !billingCountry.value.states.hasOwnProperty(customer.value.billingAddress.state)) {
    customer.value.billingAddress.state = '';
  }
});

watch(() => customer.value.shippingAddress.country_code, (newCountryCode, oldCountryCode) => {
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

onUpdated(() => {
  customer.value = {
    id: props.customer.id,
    first_name: props.customer.first_name,
    last_name: props.customer.last_name,
    email: props.customer.email,
    phone: props.customer.phone,
    status: props.customer.status,
    billingAddress: {
      ...props.customer.billingAddress
    },
    shippingAddress: {
      ...props.customer.shippingAddress
    }
  }
})

function onSubmit() {
  loading.value = true
  if (customer.value.id) {
    store.dispatch('updateCustomer', customer.value)
      .then(response => {
        loading.value = false
        if (response.status === 200) {
          //TODO show notification
          store.dispatch('getCustomers')
          closeModal()
        }
      })
      .catch(({ response }) => {
        loading.value = false;
        errorMsg.value = response.data
      })
  }
}
</script>

<template>
  <TransitionRoot appear :show="show" as="template">
    <Dialog as="div" @close="closeModal" class="relative z-10">
      <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100"
        leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed inset-0 bg-black bg-opacity-75" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
          <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-0"
            enter-to="opacity-100 scale-100" leave="duration-200 ease-in" leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-0">
            <DialogPanel
              class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white text-left align-middle shadow-xl transition-all">
              <Spinner v-if="loading"
                class="absolute top-0 right-0 bottom-0 left-0 bg-white flex items-center justify-center z-50" />
              <header class="py-3 px-4 flex justify-between items-center">
                <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900">
                  {{ customer.id ? `ユーザーを更新: "${props.customer.last_name} ${props.customer.first_name}"` : '顧客を新規作成' }}
                </DialogTitle>
                <button @click="closeModal"
                  class="w-8 h-8 flex items-center justify-center rounded-full transition-colors cursor-pointer hover:bg-[rgba(0, 0, 0, .2)]">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </header>
              <form @submit.prevent="onSubmit">
                <div class="bg-white px-4 pt-5 pb-4">
                  <CustomInput class="mb-4" v-model="customer.last_name" label="性" :errorMsg="errorMsg.last_name" />
                  <CustomInput class="mb-4" v-model="customer.first_name" label="名" :errorMsg="errorMsg.first_name" />
                  <CustomInput class="mb-4" v-model="customer.email" label="メールアドレス" :errorMsg="errorMsg.email" />
                  <CustomInput class="mb-4" v-model="customer.phone" label="電話番号" :errorMsg="errorMsg.phone" />
                  <CustomInput type="checkbox" class="mb-4" v-model="customer.status" label="ステータス"
                    :errorMsg="errorMsg.status" />

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                      <h2 class="text-xl font-semibold mt-6 pb-2 border-b border-gray-300">請求先住所</h2>
                      <p v-if="errorMsg.billingAddress && Object.keys(errorMsg.billingAddress).length > 0"
                        class="text-red-500 text-sm">請求先住所を設定してください</p>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <CustomInput type="select" :select-options="countries"
                          v-model="customer.billingAddress.country_code" label="国•地域を選択してください" />
                        <CustomInput v-model="customer.billingAddress.zipcode" label="郵便番号" />
                        <CustomInput type="select" :select-options="billingStateOptions"
                          v-model="customer.billingAddress.state" label="都道府県を選択してください" />
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
                          <CustomInput type="select" :select-options="countries"
                            v-model="customer.shippingAddress.country_code" label="国•地域を選択してください" />
                          <CustomInput v-model="customer.shippingAddress.zipcode" label="郵便番号" />
                          <CustomInput type="select" :select-options="shippingStateOptions"
                            v-model="customer.shippingAddress.state" label="都道府県を選択してください" />
                          <CustomInput v-model="customer.shippingAddress.city" label="市町村" />
                          <CustomInput v-model="customer.shippingAddress.address1" label="丁目•番地•号" />
                          <CustomInput v-model="customer.shippingAddress.address2" label="建物名" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <footer class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                  <button type="submit" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 text-base font-medium  focus:outline-none focus:ring-2 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm
                              text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500">
                    送信
                  </button>
                  <button @click="closeModal" type="button"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    キャンセル
                  </button>
                </footer>
              </form>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<style scoped>
</style>
