<script setup>
import CustomersTable from './CustomersTable.vue'
import CustomerModal from './CustomerModal.vue'
import { onMounted, ref } from 'vue';
import store from '../../store';

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

const showModal = ref(false)
const customerModel = ref({ ...DEFAULT_CUSTOMER })

function showCustomerModal() {
    showModal.value = true
}

function editCustomer(customer) {
    store.dispatch('getCustomer', customer.id)
        .then(({ data }) => {
            customerModel.value = data
            showCustomerModal()
        })
}

function onModalClose() {
    customerModel.value = { ...DEFAULT_CUSTOMER }
}
</script>

<template>
    <div class="flex items-center justify-between mb-3">
        <h1 class="text-3xl font-semibold">顧客一覧</h1>
    </div>
    <CustomerModal v-model="showModal" :customer="customerModel" @close="onModalClose" />
    <CustomersTable @clickEdit="editCustomer" />
</template>

<style scoped>
</style>
