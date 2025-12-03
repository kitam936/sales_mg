<template>
    <AuthenticatedLayout>
      <template #header>
        <div class="flex">
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            データ登録結果
          </h2>
        </div>
      </template>

      <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="flex p-6 text-gray-900 dark:text-gray-100">
              <div>
                データを{{ count }}件 登録しました。
              </div>
              <div class="pl-20">
                <button
                  @click="goToDataCreate"
                  class="w-32 text-center text-sm text-white bg-indigo-500 border-0 py-1 px-2 focus:outline-none hover:bg-indigo-700 rounded"
                >
                  データ管理画面
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>

  <script setup>
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  //import { Inertia } from '@inertiajs/vue3';
  import { Inertia } from '@inertiajs/inertia';
  import { defineProps } from 'vue';

  const props = defineProps({
    count: Number, // コントローラから渡す件数
  });

  const goToDataCreate = () => {
    Inertia.visit(route('admin.data.create'));
  };
  </script>
