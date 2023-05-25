<script setup>
import ProductsTable from './ProductsTable.vue'
</script>

<template>
    <div class="flex items-center justify-between mb-3">
        <h1 class="text-3xl font-semibold">商品一覧</h1>
        <button type="submit"
            class="py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            商品を追加
        </button>
    </div>
    <ProductsTable />
</template>


<style scoped>
</style>
