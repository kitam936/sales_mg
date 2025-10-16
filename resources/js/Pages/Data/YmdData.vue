<template>
    <AuthenticatedLayout>
      <template #header>
        <div class="flex justify-between items-center">
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            YMDデータ
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
                <th class="w-2/12 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                  YMD
                </th>
                <th class="w-4/12 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                  prev_YMD
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="ymd in ymds" :key="ymd.id">
                <td class="w-2/12 md:px-4 py-1">{{ ymd.YMD }}</td>
                <td class="w-4/12 md:px-4 py-1">{{ ymd.prev_YMD }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>

  <script setup>
  import { usePage } from '@inertiajs/vue3';
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  import FlashMessage from '@/Components/FlashMessage.vue';

  // Laravelから渡された props
  const { ymds, flashStatus } = usePage().props.value

  const goBack = () => {
    window.location.href = route('admin.data.data_index')
  }
  </script>
