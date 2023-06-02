<script setup>
import ProductsTable from './ProductsTable.vue'
import ProductModal from './ProductModal.vue'
import { ref } from 'vue';
import store from '../../store';

const DEFAULT_PRODUCT = {
    id: '',
    title: '',
    image: '',
    description: '',
    price: ''
}

const showModal = ref(false)
const productModel = ref({ ...DEFAULT_PRODUCT })

function showProductModal() {
    showModal.value = true
}

function editProduct(product) {
    store.dispatch('getProduct', product.id)
        .then(({ data }) => {
            productModel.value = data
        })
    showProductModal()
}

function onModalClose() {
    productModel.value = { ...DEFAULT_PRODUCT }
}

</script>

<template>
    <div class="flex items-center justify-between mb-3">
        <h1 class="text-3xl font-semibold">商品一覧</h1>
        <button type="submit" @click="showProductModal"
            class="py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            商品を追加
        </button>
    </div>
    <ProductModal v-model="showModal" :product="productModel" @close="onModalClose"/>
    <ProductsTable @clickEdit="editProduct"/>
</template>

<style scoped>
</style>
