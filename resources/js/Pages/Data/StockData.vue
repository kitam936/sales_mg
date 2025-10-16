
<template>
    <AuthenticatedLayout>
      <template #header>
        <div class="flex justify-between items-center">
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            在庫データ
          </h2>
          <button
            @click="goBack"
            class="w-32 text-center text-sm text-white bg-indigo-500 border-0 py-1 px-2 focus:outline-none hover:bg-indigo-700 rounded"
          >
            戻る
          </button>
        </div>
        <FlashMessage />
      </template>

      <div class="py-6 border">
        <div class="mx-auto sm:px-4 lg:px-4 border">
          <table class="md:w-2/3 bg-white table-auto text-center whitespace-nowrap">
            <thead>
              <tr>
                <th class="w-2/12 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">shop_id</th>
                <th class="w-4/12 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">品番</th>
                <th class="w-2/12 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">pcs</th>
                <th class="w-4/12 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">kingaku</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="stock in stocks.data" :key="stock.id">
                <td class="w-2/12 md:px-4 py-1">{{ stock.shop_id }}</td>
                <td class="w-4/12 md:px-4 py-1">{{ stock.hinban_id }}</td>
                <td class="w-2/12 md:px-4 py-1">{{ stock.pcs }}</td>
                <td class="w-4/12 md:px-4 py-1 text-right">{{ formatNumber(stock.zaikogaku) }}</td>
              </tr>
            </tbody>
          </table>

          <!-- ページネーション -->
          <div class="mt-4">
            <Pagination :links="stocks.links" />
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>

  <script setup>
  import { Inertia } from '@inertiajs/inertia';
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  import FlashMessage from '@/Components/FlashMessage.vue';
  import Pagination from '@/Components/Pagination.vue';

  const props = defineProps({
    stocks: Object,
    flashStatus: String,
  })

  const goBack = () => {
    Inertia.visit(route('admin.data.data_index'))
  }

  const formatNumber = (num) => {
    return Number(num).toLocaleString()
  }
  </script>
