<template>
    <AuthenticatedLayout>
      <template #header>
        <div class="flex justify-between items-center">
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            SKUデータ
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
                <!-- <th class="w-2/12 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                  SKU_id
                </th> -->
                <th class="w-4/12 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                  Hinban
                </th>
                <th class="w-2/12 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                  Col
                </th>
                <th class="w-2/12 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                  Size
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="sku in skus.data" :key="sku.id">
                <!-- <td class="w-2/12 md:px-4 py-1">{{ sku.id }}</td> -->
                <td class="w-4/12 md:px-4 py-1">{{ sku.hinban_id }}</td>
                <td class="w-2/12 md:px-4 py-1">{{ sku.col_id }}</td>
                <td class="w-2/12 md:px-4 py-1">{{ sku.size_id }}</td>
              </tr>
            </tbody>
          </table>
          <!-- ページネーション -->
          <div class="mt-2">
            <Pagination :links="skus.links" />
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>

  <script setup>
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  import FlashMessage from '@/Components/FlashMessage.vue';
  import { usePage, router } from '@inertiajs/vue3';
  import Pagination from '@/Components/Pagination.vue';

  const props = defineProps({
    skus: Object // ページネーション付き,

  });





  const goBack = () => {
    router.get(route('admin.data.data_index'))
  }
  </script>
