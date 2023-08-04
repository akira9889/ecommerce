<script setup>
import ProductsTable from './ProductsTable.vue'
import ProductModal from './ProductModal.vue'
import { ref, computed, onMounted, watch } from 'vue';
import { PRODUCTS_PER_PAGE } from '../../constants'
import store from '../../store';

const products = computed(() => store.state.products)
const perPage = ref(PRODUCTS_PER_PAGE)
const search = ref('')
const sortField = ref('updated_at')
const sortDirection = ref('desc')

const showModal = ref(false)
const editingProduct = ref({})

onMounted(() => {
    getProducts()
})


let timer = null;
watch(search, (newSearch) => {
    if (timer) {
        clearTimeout(timer);
    }
    timer = setTimeout(() => {
        getProducts(null, newSearch);
    }, 1000);
});

function getProducts(url = null, newSearch = search.value) {
    store.dispatch('getProducts', {
        url,
        sort_field: sortField.value,
        sort_direction: sortDirection.value,
        search: newSearch,
        perPage: perPage.value
    })
}

function getForPage(link) {
    if (!link.url || link.active) {
        return
    }
    getProducts(link.url)
}

function sortProduct(field) {
    sortDirection.value = (sortField.value === field && sortDirection.value === 'asc') ? 'desc' : 'asc';
    sortField.value = field
    getProducts()
}

function createNewProduct() {
    editingProduct.value = {}
    showProductModal()
};

function editProduct(product) {
    editingProduct.value = product
    showProductModal()
}

function deleteProduct() {
    getProducts()
}

function showProductModal() {
    showModal.value = true
}

function closeProductModal() {
    showModal.value = false
}

function submit() {
    closeProductModal()
    sortField.value = 'updated_at'
    sortDirection.value = 'desc'
    search.value = '';
    getProducts()
}
</script>

<template>
    <h1 class="text-3xl font-semibold">商品一覧</h1>
    <div class="bg-white p-5 rounded-lg shadow animate-fade-in-down mt-3">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center">
                <select @change="getProducts(null)" v-model="perPage"
                    class="appearance-none relative block w-24 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="whitespace-nowrap ml-3">件ごとに表示</span>
            </div>
            <div class="flex">
                <input type="text" v-model="search" @change="getProducts(null)" placeholder="タイトルを検索"
                    class="appearance-none relative block w-48 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm  mr-3">
                <button type="submit" @click="createNewProduct"
                    class="py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    商品を追加
                </button>
            </div>
        </div>
        <ProductModal v-model="showModal" :product="editingProduct" @close="closeProductModal" @submit="submit" />
        <ProductsTable :products="products" :sortField="sortField" :sortDirection="sortDirection" @sortProduct="sortProduct"
            @clickEdit="editProduct" @clickDelete="deleteProduct" />
        <div v-if="!products.loading && products.total > products.limit" class="flex justify-between items-center mt-5">
            <span>{{ products.from }}件から{{ products.to }}件を表示</span>
            <nav class="relative z-0 inline-flex justify-center rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <a v-for="(link, i) of products.links" :key="i" :disabled="!link.url" href="#" @click.prevent="getForPage(link)"
                    aria-current="page"
                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium whitespace-nowrap" :class="[
                        link.active
                            ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                            : 'bg-white border-gray-300 text-gray-500 hover:bg-indigo-50',
                        i === 0 ? 'rounded-l-md' : '',
                        i === products.links.length - 1 ? 'rounded-r-md' : '',
                        !link.url ? 'bg-gray-100 text-gray-700' : ''
                    ]" v-html="link.label">
                </a>
            </nav>
        </div>
    </div>
</template>

<style scoped>
</style>
