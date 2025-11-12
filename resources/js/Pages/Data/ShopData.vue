
<template>
    <AuthenticatedLayout>
      <template #header>
        <div class="flex justify-between items-center">
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            店舗データ
          </h2>
          <button
            @click="goBack"
            class="w-32 text-center text-sm text-white bg-indigo-500 py-1 px-2 hover:bg-indigo-700 rounded"
          >
            戻る
          </button>
        </div>

        <!-- フィルタ -->
        <div class="mt-4 flex items-center">
          <select
            v-model="selectedCompany"
            @change="filterShops"
            class="w-40 h-8 text-sm border"
          >
            <option value="">全社</option>
            <option v-for="company in companies" :key="company.id" :value="company.id">
              {{ company.co_name }}
            </option>
          </select>
          <span class="ml-2 text-sm">※会社を選択してください</span>
          <button
            @click="resetFilter"
            class="w-20 h-8 bg-indigo-500 text-white ml-2 hover:bg-indigo-600 rounded"
          >
            全表示
          </button>
        </div>
      </template>

      <div class="py-0 border">
        <FlashMessage/>
        <div class="mx-auto sm:px-4 lg:px-4 border overflow-x-auto">
          <table class="md:w-full bg-white table-auto w-full text-center whitespace-no-wrap">
            <thead>
              <tr>
                <th class="px-2 py-1 bg-gray-100 text-sm font-medium">社コード</th>
                <th class="px-2 py-1 bg-gray-100 text-sm font-medium">社名</th>
                <th class="px-2 py-1 bg-gray-100 text-sm font-medium">店コード</th>
                <th class="px-2 py-1 bg-gray-100 text-sm font-medium">店名</th>
                <th class="px-2 py-1 bg-gray-100 text-sm font-medium">info</th>
                <th class="px-2 py-1 bg-gray-100 text-sm font-medium">編集</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="shop in shops.data" :key="shop.id">
                <td class="px-2 py-1 text-left">{{ shop.company_id }}</td>
                <td class="px-2 py-1 text-left">{{ shop.company.co_name }}</td>
                <td class="px-2 py-1 text-left">{{ shop.id }}</td>
                <td class="px-2 py-1 text-left">{{ shop.shop_name }}</td>
                <td class="px-2 py-1 text-left">{{ shop.shop_info }}</td>
                <td class="px-2 py-1 text-center">
                  <Link
                    :href="route('admin.data.shop_edit', { shop: shop.id })"
                    class="text-indigo-500"
                  >
                    編集
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- ページネーション -->
          <div class="mt-4">
            <Pagination :links="shops.links" />
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>

  <script setup>
  import { ref } from 'vue';
  import { Link, usePage } from '@inertiajs/vue3';
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  import FlashMessage from '@/Components/FlashMessage.vue';
  import Pagination from '@/Components/Pagination.vue';

  const props = defineProps({
    companies: Array,
    shops: Object, // Inertia の pagination
    filters: Object,
  })



  const selectedCompany = ref(props.filters?.co_id ?? '')

  const goBack = () => {
    window.location.href = route('admin.data.data_index')
  }

  const filterShops = () => {
    const params = selectedCompany.value ? { co_id: selectedCompany.value } : {}
    window.location.href = route('admin.data.shop_index', params)
  }

  const resetFilter = () => {
    selectedCompany.value = ''
    window.location.href = route('admin.data.shop_index')
  }

  console.log(usePage().props.value)
  </script>
