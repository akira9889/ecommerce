<script setup>
import { ref, onMounted } from 'vue';
import store from '../../store';
import { useRoute } from 'vue-router';
import axiosClient from '../../axios';
import OrderStatus from './OrderStatus.vue';
import Spinner from '../../components/core/Spinner.vue'
const route = useRoute()

const order = ref({})
const orderStatuses = ref([])
const loading = ref(false)

onMounted(() => {
    getOrder()

    axiosClient.get('/orders/statuses')
        .then(({ data }) => {
            orderStatuses.value = data
        })
})

function getOrder() {
    store.dispatch('getOrder', route.params.id)
        .then(({ data }) => {
            order.value = data
        })
}

function onStatusChange() {
    loading.value = true
    axiosClient.post(`/orders/change-status/${order.value.id}/${order.value.status}`)
        .then(() => {
            loading.value = false
            store.commit('showToast', [`注文状況が「${orderStatuses.value[order.value.status]}」に更新されました。`])
        })
        .catch(({ response }) => {
            loading.value = false
            store.commit('showToast', [response.data.message, 'error'])
            getOrder()
        })
}

</script>

<template>
    <div v-if="order.id" class="max-w-[90%] mx-auto">
        <!-- Order Details -->
        <h2 class="flex justify-between items-center text-2xl font-bold pb-2 border-b border-gray-300">
            注文詳細
            <OrderStatus :order="order" :orderStatuses="orderStatuses" />
        </h2>
        <table class="table-sm">
            <tbody>
                <tr>
                    <td class="font-bold">注文番号</td>
                    <td>{{ order.id }}</td>
                </tr>
                <tr>
                    <td class="font-bold">注文日</td>
                    <td>{{ order.created_at }}</td>
                </tr>
                <tr>
                    <td class="font-bold">注文状況</td>
                    <td>
                        <div class="flex items-center">
                            <select v-if="order.status !== 'canceled'" v-model="order.status" @change="onStatusChange">
                                <option v-for="(status, key) in orderStatuses" :value="key" :key="key">{{ status }}</option>
                            </select>
                            <OrderStatus v-else :order="order" :orderStatuses="orderStatuses" />
                            <Spinner v-if="loading" class="h-14" message="変更中" />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="font-bold">商品の小計：</td>
                    <td>￥{{ order.total_price }}</td>
                </tr>
            </tbody>
        </table>
        <!--/ Order Details -->

        <!-- Customer Details -->
        <h2 class="text-2xl font-bold mt-6 pb-2 border-b border-gray-300">顧客詳細</h2>
        <table class="table-sm">
            <tbody>
                <tr>
                    <td class="font-bold">氏名</td>
                    <td>{{ order.customer.last_name }} {{ order.customer.first_name }}</td>
                </tr>
                <tr>
                    <td class="font-bold">メールアドレス</td>
                    <td>{{ order.customer.email }}</td>
                </tr>
                <tr>
                    <td class="font-bold">電話番号</td>
                    <td>
                        {{ order.customer.phone }}
                    </td>
                </tr>
                <tr>
                    <td class="font-bold">商品の小計：</td>
                    <td>￥{{ order.total_price }}</td>
                </tr>
            </tbody>
        </table>
        <!--/ Customer Details -->

        <!-- Addresses Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <h2 class="text-xl font-semibold mt-6 pb-2 border-b border-gray-300">請求先住所</h2>
                <div>
                    〒{{ order.customer.billingAddress.zipcode.slice(0, 3) + '-' +
                        order.customer.billingAddress.zipcode.slice(3) }} <br>
                    {{ order.customer.billingAddress.country }} <br>
                    {{ order.customer.billingAddress.state }}{{ order.customer.billingAddress.city }}{{
                        order.customer.billingAddress.address1 }}{{ order.customer.billingAddress.address2 }}
                </div>
            </div>
            <div>
                <h2 class="text-xl font-semibold mt-6 pb-2 border-b border-gray-300">配達先住所</h2>

                <div>
                    〒{{ order.customer.shippingAddress.zipcode.slice(0, 3) + '-' +
                        order.customer.shippingAddress.zipcode.slice(3) }} <br>
                    {{ order.customer.shippingAddress.country }} <br>
                    {{ order.customer.shippingAddress.state }}{{ order.customer.shippingAddress.city }}{{
                        order.customer.shippingAddress.address1 }}{{ order.customer.shippingAddress.address2 }}
                </div>
            </div>
        </div>
        <!--/ Addresses Details -->

        <!-- Order Item -->
        <h2 class="text-xl font-semibold mt-6 pb-2 border-b border-gray-300">注文商品</h2>
        <div v-for="item in order.items" class="flex flex-col sm:flex-row items-center gap-4 mt-4">
            <a href="#" class="w-36 h-32 flex items-center justify-center overflow-hidden">
                <img :src="item.product.image" class="object-cover" alt="" />
            </a>
            <div class="flex flex-col justify-between  flex-1">
                <div class="flex justify-between mb-3">
                    <h3>
                        {{ item.product.title }}
                    </h3>
                </div>
                <div class="flex flex-col sm:flex-row justify-between items-center">
                    <div class="flex items-center">数量: {{ item.quantity }}</div>
                    <span class="text-lg font-semibold"> ¥{{ item.unit_price }} </span>
                </div>
            </div>
        </div>
        <!--/ Order Item -->
    </div>
</template>

<style scoped>
</style>
