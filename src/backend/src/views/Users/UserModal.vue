<script setup>
import { ref, computed, watch, onUpdated } from 'vue'
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

const user = ref({})

const loading = ref(false)

const errorMsg = ref({})

const props = defineProps({
  modelValue: Boolean,
  user: {
    required: true,
    type: Object
  }
})

const emit = defineEmits(['update:modelValue', 'close', 'submit'])

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
  user.value = {
    ...props.user
  }
})

function submit() {
  loading.value = true
  if (user.value.id) {
    store.dispatch('updateUser', user.value)
      .then(response => {
        loading.value = false
        if (response.status === 200) {
          emit('submit')
        }
      })
      .catch(({ response }) => {
        loading.value = false;
        user.value.password = '';
        user.value.password_confirmation = '';
        errorMsg.value = response.data.errors
      })
  } else {
    store.dispatch('createUser', user.value)
      .then(response => {
        loading.value = false
        errorMsg.value = {}
        if (response.status === 201) {
          emit('submit')
        }
      })
      .catch(({ response }) => {
        loading.value = false;
        user.value.password = '';
        user.value.password_confirmation = '';
        errorMsg.value = response.data.errors
      })
  }
}
</script>

<template>
  <TransitionRoot appear :show="show" as="template">
    <Dialog as="div" @close="closeModal">
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
                class="absolute top-0 right-0 bottom-0 left-0 bg-white flex items-center justify-center z-20" />
              <header class="py-3 px-4 flex justify-between items-center">
                <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900">
                  {{ user.id ? `ユーザーを更新: ${props.user.last_name} ${props.user.first_name}` : 'ユーザーを新規作成' }}
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
                  <p v-if="errorMsg.network" class="text-red-500 text-sm mb-4 text-center">{{ errorMsg.network }}</p>
                  <div class="grid grid-cols-2 gap-4 items-end">
                    <CustomInput v-model="user.last_name" label="姓"
                    :errorMsg="errorMsg.last_name" />
                    <CustomInput v-model="user.first_name" label="名" :errorMsg="errorMsg?.first_name" />
                  </div>
                  <div class="grid grid-cols-2 gap-4 items-end">
                    <CustomInput v-model="user.last_kana" label="セイ" :errorMsg="errorMsg?.last_kana" />
                    <CustomInput v-model="user.first_kana" label="メイ" :errorMsg="errorMsg?.first_kana" />
                  </div>
                  <CustomInput v-model="user.email" label="メールアドレス" :errorMsg="errorMsg?.email" />
                  <CustomInput type="password" v-model="user.password" label="パスワード"
                    :errorMsg="errorMsg.password" />
                  <CustomInput type="password" v-model="user.password_confirmation" label="パスワード確認" />
                </div>
                <footer class="px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
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
