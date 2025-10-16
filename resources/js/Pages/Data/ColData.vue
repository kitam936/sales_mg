<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link, usePage } from '@inertiajs/vue3';
    import FlashMessage from '@/Components/FlashMessage.vue';
    import Pagination from '@/Components/Pagination.vue';

    // サーバーから渡ってくる props
    const props = defineProps({
      cols: Array
    })
    </script>

    <template>
      <AuthenticatedLayout>
        <Head title="Colデータ" />

        <!-- ヘッダー -->
        <template #header>
          <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
              Colデータ
            </h2>
            <Link
              :href="route('admin.data.data_index')"
              class="w-32 text-center text-sm text-white bg-indigo-500 border-0 py-1 px-2
                     focus:outline-none hover:bg-indigo-700 rounded"
            >
              戻る
            </Link>
          </div>
        </template>

        <!-- フラッシュメッセージ -->
        <FlashMessage />

        <!-- テーブル -->
        <div class="py-6 border">
          <div class="mx-auto sm:px-4 lg:px-4 border">
            <table class="md:w-2/3 bg-white table-auto text-center whitespace-nowrap">
              <thead>
                <tr>
                  <th class="w-2/12 md:px-4 py-1 title-font tracking-wider font-medium
                             text-gray-900 text-sm bg-gray-100">
                    col_id
                  </th>
                  <th class="w-4/12 md:px-4 py-1 title-font tracking-wider font-medium
                             text-gray-900 text-sm bg-gray-100">
                    Col名
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="col in cols" :key="col.id">
                  <td class="w-2/12 md:px-4 py-1">{{ col.id }}</td>
                  <td class="w-4/12 md:px-
