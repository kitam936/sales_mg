
<template>
    <AuthenticatedLayout>
      <template #header>
        <div class="flex justify-between items-center">
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">売上データ</h2>
          <button
            @click="$inertia.get(route('admin.data.data_index'))"
            class="w-32 text-center text-sm text-white bg-indigo-500 py-1 px-2 rounded hover:bg-indigo-700"
          >
            戻る
          </button>
        </div>
      </template>

      <FlashMessage :status="flashStatus" />

      <div class="py-6 border">
        <div class="mx-auto sm:px-4 lg:px-4 border">
          <table class="md:w-3/4 bg-white table-auto w-full text-center whitespace-nowrap">
            <thead>
              <tr>
                <th class="px-4 py-1 bg-gray-100 text-sm font-medium">Date</th>
                <th class="px-4 py-1 bg-gray-100 text-sm font-medium">shop_id</th>
                <th class="px-4 py-1 bg-gray-100 text-sm font-medium">品番</th>
                <th class="px-4 py-1 bg-gray-100 text-sm font-medium">pcs</th>
                <th class="px-4 py-1 bg-gray-100 text-sm font-medium">tanka</th>
                <th class="px-4 py-1 bg-gray-100 text-sm font-medium">kingaku</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="sale in sales.data" :key="sale.id">
                <td class="px-4 py-1">{{ sale.sales_date }}</td>
                <td class="px-4 py-1">{{ sale.shop_id }}</td>
                <td class="px-4 py-1">{{ sale.id }}</td>
                <td class="px-4 py-1">{{ sale.pcs }}</td>
                <td class="px-4 py-1 text-right">{{ formatNumber(sale.tanka) }}</td>
                <td class="px-4 py-1 text-right">{{ formatNumber(sale.kingaku) }}</td>
              </tr>
            </tbody>
          </table>

          <!-- ページネーション -->
          <div class="mt-2">
            <Pagination :links="sales.links" />
          </div>

        </div>
      </div>
    </AuthenticatedLayout>
  </template>

  <script setup>
  import { usePage } from '@inertiajs/vue3';
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  import FlashMessage from '@/Components/FlashMessage.vue';
  import Pagination from '@/Components/Pagination.vue';

  const { props } = usePage()
  const sales = props.value.sales
  const flashStatus = props.value.flash?.status || null

  const formatNumber = (val) => {
    return Number(val).toLocaleString()
  }
  </script>
