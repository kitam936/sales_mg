<script setup>
    import { reactive } from 'vue';
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link } from '@inertiajs/vue3';
    import FlashMessage from '@/Components/FlashMessage.vue'; // Bladeのx-flash-message相当

    // Propsでコントローラーから渡されるデータを受け取る
    const props = defineProps({
        sizes: Array,
        flash: String, // session('status') 相当
    });
    </script>

    <template>
      <AuthenticatedLayout>
        <Head title="Sizeデータ" />

        <template #header>
          <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
              Sizeデータ
            </h2>
            <div class="w-40 text-right">
              <Link
                :href="route('admin.data.data_index')"
                class="w-32 text-center text-sm text-white bg-indigo-500 border-0 py-1 px-2 focus:outline-none hover:bg-indigo-700 rounded"
              >
                戻る
              </Link>
            </div>
          </div>
          <FlashMessage v-if="flash" :status="flash" />
        </template>

        <div class="py-6 border">
          <div class="mx-auto sm:px-4 lg:px-4 border">
            <table class="md:w-2/3 bg-white table-auto text-center whitespace-nowrap">
              <thead>
                <tr>
                  <th class="w-2/12 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                    Size_id
                  </th>
                  <th class="w-4/12 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                    Size名
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="size in sizes" :key="size.id">
                  <td class="w-2/12 md:px-4 py-1">{{ size.id }}</td>
                  <td class="w-4/12 md:px-4 py-1">{{ size.size_name }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </AuthenticatedLayout>
    </template>
