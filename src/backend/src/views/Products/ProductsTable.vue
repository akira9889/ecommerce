<script setup>
import { ref } from 'vue';
import TableHeaderCell from '../../components/core/Table/TableHeaderCell.vue';
import Spinner from '../../components/core/Spinner.vue';
import store from '../../store';
import { onMounted, computed } from 'vue';
import { PRODUCTS_PER_PAGE } from '../../constants'
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { DotsVerticalIcon, MenuIcon, PencilIcon, TrashIcon } from "@heroicons/vue/outline";

const perPage = ref(PRODUCTS_PER_PAGE)
const search = ref('')
const products = computed(() => store.state.products)
const sortField = ref('updated_at')
const sortDirection = ref('desc')

const emit = defineEmits(['clickEdit'])

onMounted(() => {
  getProducts()
})

function getProducts(url = null) {
  store.dispatch('getProducts', {
    url,
    sort_field: sortField.value,
    sort_direction: sortDirection.value,
    search: search.value,
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
  if (sortField.value === field) {
    if (sortDirection.value === 'asc') {
      sortDirection.value = 'desc'
    } else {
      sortDirection.value = 'asc'
    }
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }

  getProducts()
}

function editProduct(product) {
  emit('clickEdit', product);
}

function deleteProduct(product) {
  if (!confirm(`本当に削除してもいいですか？`)) {
    return
  }
  store.dispatch('deleteProduct', product.id)
    .then(res => {
      //TODO Show notification
      store.dispatch('getProducts')
    })
}
</script>

<template>
  <div class="bg-white p-5 rounded-lg shadow animate-fade-in-down">
    <div class="flex justify-between border-b-2 pb-3">
      <div class="flex items-center">
        <span class="whitespace-nowrap mr-3">Per Page</span>
        <select @change="getProducts(null)" v-model="perPage"
          class="appearance-none relative block w-24 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
          <option value="5">5</option>
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
      </div>
      <div>
        <input type="text" v-model="search" @change="getProducts(null)" placeholder="商品を検索"
          class="appearance-none relative block w-48 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm">
      </div>
    </div>
    <table class="table-auto w-full">
      <thead>
        <tr>
          <TableHeaderCell @click="sortProduct" field="id" :sort-field="sortField" :sort-direction="sortDirection">ID
          </TableHeaderCell>
          <TableHeaderCell :sort-field="sortField" :sort-direction="sortDirection">画像</TableHeaderCell>
          <TableHeaderCell @click="sortProduct" field="title" :sort-field="sortField" :sort-direction="sortDirection">タイトル
          </TableHeaderCell>
          <TableHeaderCell @click="sortProduct" field="price" :sort-field="sortField" :sort-direction="sortDirection">値段
          </TableHeaderCell>
          <TableHeaderCell @click="sortProduct" field="updated_at" :sort-field="sortField"
            :sort-direction="sortDirection">最終更新日</TableHeaderCell>
          <TableHeaderCell field="actions">
            変更
          </TableHeaderCell>
        </tr>
      </thead>
      <tbody v-if="!products.loading">
        <tr v-for="(product, index) of products.data"  class="animate-fade-in-down" :style="{'animation-delay': `${index * 0.05}s`}">
          <td class="border-b p-2">{{ product.id }}</td>
          <td class="border-b p-2">
            <img class="w-16" :src="product.image" alt="product.title">
          </td>
          <td class="border-b p-2 max-w-[200px] white-space-nowrap overflow-hidden text-ellipsis">{{
            product.title }}</td>
          <td class="border-b p-2">{{ product.price }}</td>
          <td class="border-b p-2">{{ product.updated_at }}</td>
          <td class="border-b p-2">
            <Menu as="div" class="relative inline-block text-left">
              <div>
                <MenuButton class="inline-flex items-center justify-center rounded-full w-10 h-10 bg-black bg-opacity-0">
                  <DotsVerticalIcon class="h-5 w-5 text-indigo-500" aria-hidden="true" />

                </MenuButton>
              </div>
              <transition enter-active-class="transition duration-100 ease-out"
                enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-in" leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0">
                <MenuItems
                  class="absolute z-10 right-0 mt-2 w-32 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-">
                  <div class="px-1 py-1">
                    <MenuItem v-slot="{ active }">
                    <button :class="[
                      active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                      'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                    ]"
                    @click="editProduct(product)">
                      <PencilIcon :active="active" class="mr-2 h-5 w-5 text-indigo-400" aria-hidden="true" />
                      編集
                    </button>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                    <button :class="[
                      active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                      'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                    ]"
                    @click="deleteProduct(product)">
                      <TrashIcon :active="active" class="mr-2 h-5 w-5 text-indigo-400" aria-hidden="true" />
                      削除
                    </button>
                    </MenuItem>
                  </div>
                </MenuItems>
              </transition>
            </Menu>
          </td>
        </tr>
      </tbody>
      <tbody v-else>
        <tr>
          <td colspan="5">
            <Spinner />
          </td>
        </tr>
      </tbody>
    </table>
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
