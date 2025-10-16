<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link, usePage } from '@inertiajs/vue3';
    import FlashMessage from '@/Components/FlashMessage.vue';
    import Pagination from '@/Components/Pagination.vue';

    // コントローラーから渡された props を受け取る
    const props = defineProps({
      areas: Array
    })
    </script>

    <template>
      <AuthenticatedLayout>
        <Head title="エリアデータ" />

        <!-- ヘッダー -->
        <template #header>
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <div class="flex items-center">
              <div>エリアデータ</div>
              <div class="w-40 ml-60 text-sm text-right mb-0">
                <Link
                  :href="route('admin.data.data_index')"
                  class="w-32 inline-block text-center text-sm text-white bg-indigo-500 border-0 py-1 px-2 hover:bg-indigo-700 rounded"
                >
                  戻る
                </Link>
              </div>
            </div>
          </h2>
        </template>

        <!-- フラッシュメッセージ -->
        <FlashMessage />

        <!-- 本体 -->
        <div class="py-6 border">
          <div class="mx-auto sm:px-4 lg:px-4 border">
            <table class="md:w-2/3 bg-white table-auto text-center whitespace-no-wrap">
              <thead>
                <tr>
                  <th class="w-2/12 md:px-4 py-1 font-medium text-gray-900 text-sm bg-gray-100">Area_id</th>
                  <th class="w-4/12 md:px-4 py-1 font-medium text-gray-900 text-sm bg-gray-100">エリア名</th>
                  <th class="w-4/12 md:px-4 py-1 font-medium text-gray-900 text-sm bg-gray-100">詳細</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="area in areas" :key="area.id">
                  <td class="w-2/12 md:px-4 py-1">{{ area.id }}</td>
                  <td class="w-4/12 md:px-4 py-1">{{ area.area_name }}</td>
                  <td class="w-4/12 md:px-4 py-1">{{ area.area_info }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </AuthenticatedLayout>
    </template>
