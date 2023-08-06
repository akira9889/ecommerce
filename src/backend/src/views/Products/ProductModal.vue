<script setup>
import { ref, computed, onUpdated } from 'vue'
import {
  TransitionRoot,
  TransitionChild,
  Dialog,
  DialogPanel,
  DialogTitle,
} from '@headlessui/vue'
import Spinner from '../../components/core/Spinner.vue';
import store from '../../store';
import CustomInput from '../../components/core/CustomInput.vue';

const props = defineProps({
  modelValue: Boolean,
  product: {
    required: true,
    type: Object
  }
})

const emit = defineEmits(['update:modelValue', 'close', 'submit'])

const product = ref({})

const loading = ref(false)

const errorMsg = ref({})

const show = computed({
  get: () => props.modelValue,
  set: value => emit('update:modelValue', value)
})

function closeModal() {
  show.value = false
  errorMsg.value = {}
  emit('close');
}

onUpdated(() => {
  product.value = {
    ...props.product
  }
})

function submit() {
  loading.value = true
  if (product.value.id) {
    store.dispatch('updateProduct', product.value)
      .then(response => {
        loading.value = false
        if (response.status === 200) {
          emit('submit')
        }
      })
      .catch(({ response }) => {
        loading.value = false;
        errorMsg.value = response.data.errors
      })
  } else {
    store.dispatch('createProduct', product.value)
      .then(response => {
        loading.value = false
        if (response.status === 201) {
          emit('submit')
        }
      })
      .catch(({ response }) => {
        loading.value = false;
        errorMsg.value = response.data.errors
      })
  }
}
</script>

<template>
  <TransitionRoot appear :show="show" as="template">
    <Dialog as="div" @close="closeModal" class="relative">
      <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100"
        leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed inset-0 bg-black bg-opacity-75 z-20" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto z-30">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
          <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100" leave="duration-200 ease-in" leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95">
            <DialogPanel
              class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white text-left align-middle shadow-xl transition-all">
              <Spinner v-if="loading"
                class="absolute top-0 right-0 bottom-0 left-0 bg-white flex items-center justify-center z-40" />
              <header class="py-3 px-4 flex justify-between items-center">
                <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900">
                  {{ product.id ? `商品を更新: "${props.product.title}"` : '商品を新規作成' }}
                </DialogTitle>
                <button @click="closeModal"
                  class="w-8 h-8 flex items-center justify-center rounded-full transition-colors cursor-pointer hover:bg-[rgba(0, 0, 0, .2)]">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                  </svg>

                </button>
              </header>
              <form @submit.prevent="submit">
                <div class="bg-white px-4 pt-5 pb-4">
                  <CustomInput class="mb-4" v-model="product.title" label="商品名" :errorMsg="errorMsg?.title" />
                  <CustomInput type="file" class="mb-4" label="商品画像" @change="file => product.image = file"
                    :errorMsg="errorMsg?.image" />
                  <CustomInput type="textarea" class="mb-4" v-model="product.description" label="説明"
                    :errorMsg="errorMsg?.description" />
                  <CustomInput type="number" class="mb-4" v-model="product.price" label="値段" append="円" min="1"
                    :errorMsg="errorMsg?.price" />
                  <CustomInput type="checkbox" v-model="product.published" label="公開" :errorMsg="errorMsg?.published" />
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
