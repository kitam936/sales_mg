<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link, usePage } from '@inertiajs/vue3';
    import FlashMessage from '@/Components/FlashMessage.vue';
    import Pagination from '@/Components/Pagination.vue';

    const props = defineProps({
      brands: Array, // コントローラーから Inertia::render で渡される
    });
    </script>

    <template>
      <AuthenticatedLayout>
        <Head title="BRANDデータ" />

        <!-- Header -->
        <template #header>
          <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
              BRANDデータ
            </h2>
            <Link
              :href="route('admin.data.data_index')"
              class="w-32 text-center text-sm text-white bg-indigo-500 border-0 py-1 px-2 rounded hover:bg-indigo-700"
            >
              戻る
            </Link>
          </div>
        </template>

        <!-- Flash メッセージ -->
        <FlashMessage />

        <!-- Main Content -->
        <div class="py-6 border">
          <div class="mx-auto sm:px-4 lg:px-4 border overflow-x-auto">
            <table class="md:w-2/3 bg-white table-auto text-center whitespace-nowrap">
              <thead>
                <tr>
                  <th class="w-2/12 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                    Brand_id
                  </th>
                  <th class="w-4/12 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                    BRAND名
                  </th>
                  <th class="w-2/12 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                    info
                  </th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="brand in brands" :key="brand.id">
                  <td class="w-2/12 md:px-4 py-1">{{ brand.id }}</td>
                  <td class="w-4/12 md:px-4 py-1">{{ brand.brand_name }}</td>
                  <td class="w-2/12 md:px-4 py-1">{{ brand.brand_info }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </AuthenticatedLayout>
    </template>
