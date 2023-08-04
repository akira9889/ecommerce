<script setup>
import TableHeaderCell from '../../components/core/Table/TableHeaderCell.vue';
import Spinner from '../../components/core/Spinner.vue';
import store from '../../store';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { DotsVerticalIcon, MenuIcon, PencilIcon, TrashIcon } from "@heroicons/vue/outline";

defineProps({
  products: Object,
  sortField: String,
  sortDirection: String
})

const emit = defineEmits(['sortProduct', 'editProduct']);

function sortProduct(field) {
  emit('sortProduct', field)
}

function editProduct(product) {
  emit('clickEdit', product);
}

function deleteProduct(product) {
  if (!confirm(`本当に削除してもいいですか？`)) {
    return
  }
  store.dispatch('deleteProduct', product.id)
    .then(() => {
      emit('clickDelete')
    })
}
</script>

<template>
  <div class="">
    <table class="table-auto w-full">
      <thead>
        <tr>
          <TableHeaderCell @click="sortProduct" field="id" :sort-field="sortField" :sort-direction="sortDirection">ID
          </TableHeaderCell>
          <TableHeaderCell sort-field="sortField" :sort-direction="sortDirection">画像</TableHeaderCell>
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
        <tr v-for="(product, index) of products.data" class="animate-fade-in-down"
          :style="{ 'animation-delay': `${index * 0.05}s` }">
          <td class="border-b p-2">{{ product.id }}</td>
          <td class="border-b p-2">
            <img class="w-16" :src="product.image ? product.image : 'http://localhost:28001/storage/images/no_image.png'" alt="product.title">
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
                  class="absolute z-10 left-full -top-1/2 w-28 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-">
                  <div class="px-1 py-1">
                    <MenuItem v-slot="{ active }">
                    <button :class="[
                      active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                      'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                    ]" @click="editProduct(product)">
                      <PencilIcon :active="active" class="mr-2 h-5 w-5 text-indigo-400" aria-hidden="true" />
                      編集
                    </button>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                    <button :class="[
                      active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                      'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                    ]" @click="deleteProduct(product)">
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

  </div>
</template>

<style scoped>
</style>
