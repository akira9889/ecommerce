<script setup>
import { ref } from 'vue';
import CustomInput from '../../components/core/CustomInput.vue';
import Spinner from '../../components/core/Spinner.vue';
import { onMounted, computed } from 'vue';
import store from '../../store';

const user = ref({ ...store.state.user.data })

const loading = ref(false)

const errorMsg = ref({})

function submit() {
  loading.value = true
  store.dispatch('updateUser', user.value)
    .then(response => {
      loading.value = false
      if (response.status === 200) {
        loading.value = false
        store.commit('showToast', ['プロフィールを更新しました。'])
      }
    })
    .catch(({ response }) => {
      loading.value = false;
      user.value.password = '';
      user.value.password_confirmation = '';
      errorMsg.value = response.data.errors
    })
}
</script>

<template>
  <h1 class="text-3xl font-semibold">プロフィール</h1>
  <div class="bg-white px-4 pt-5 pb-4 mt-3  rounded-lg shadow animate-fade-in-down relative">
    <form @submit.prevent="submit" class="w-1/2 mx-auto">
      <div>
        <h2 class="text-xl font-semibold">ユーザー情報</h2>
        <p v-if="errorMsg.network" class="text-red-500 text-sm mb-4 text-center">{{ errorMsg?.network }}</p>
        <div class="grid grid-cols-2 gap-4 items-end">
          <CustomInput v-model="user.last_name" label="姓" :errorMsg="errorMsg?.last_name" />
          <CustomInput v-model="user.first_name" label="名" :errorMsg="errorMsg?.first_name" />
        </div>
        <div class="grid grid-cols-2 gap-4 items-end mt-3">
          <CustomInput v-model="user.last_kana" label="セイ" :errorMsg="errorMsg?.last_kana" />
          <CustomInput v-model="user.first_kana" label="メイ" :errorMsg="errorMsg?.first_kana" />
        </div>
        <CustomInput v-model="user.email" label="メールアドレス" :errorMsg="errorMsg?.email" />

        <h2 class="text-xl font-semibold mt-6 inline-block mr-3">パスワード</h2><span>※変更がなければ空欄</span>
        <CustomInput type="password" v-model="user.password" label="パスワード" :errorMsg="errorMsg?.password" />
        <CustomInput type="password" v-model="user.password_confirmation" label="パスワード確認" />
      </div>
      <footer class="sm:text-center mt-3">
        <button type="submit"
          class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 text-base font-medium  focus:outline-none focus:ring-2 focus:ring-offset-2 sm:w-auto sm:text-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500">
          送信
        </button>
      </footer>
      <Spinner v-if="loading"
        class="absolute top-0 right-0 bottom-0 left-0 bg-white flex items-center justify-center z-20" />
    </form>
  </div>
</template>

<style scoped>
</style>
