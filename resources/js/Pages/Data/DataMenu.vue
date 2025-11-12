<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link } from '@inertiajs/vue3';
    </script>

    <template>
      <Head title="データ管理Menu" />

      <AuthenticatedLayout>
        <template #header>
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            データ管理Menu
          </h2>

          <div class="md:flex-auto p-1 text-gray-900 dark:text-gray-100">
            <div class="flex px-4 py-4 md:w-2/3">
                <Link
              :href="route('menu')"
              class="w-32 h-8 text-center text-sm text-white bg-indigo-500 border-0 py-1 px-2 hover:bg-indigo-700 rounded inline-block"
            >
              Menu
            </Link>
              <Link
                :href="route('admin.data.data_index')"
                class="w-32 h-8 ml-4 flex-auto p-0 text-sm text-white bg-indigo-400 hover:bg-indigo-600 rounded flex items-center justify-center"
              >
                Index
              </Link>
              <Link
                :href="route('admin.data.create')"
                class="w-32 h-8 ml-4 flex-auto p-0 text-sm text-white bg-indigo-400 hover:bg-indigo-600 rounded flex items-center justify-center"
              >
                反映・更新
              </Link>
              <Link
                :href="route('admin.image_create')"
                class="w-32 h-8 ml-4 flex-auto p-0 text-sm text-white bg-indigo-400 hover:bg-indigo-600 rounded flex items-center justify-center"
              >
                画像UL
              </Link>
              <Link
                :href="route('admin.data.delete_index')"
                class="w-32 h-8 ml-4 flex-auto p-0 text-sm text-white bg-pink-400 hover:bg-pink-600 rounded flex items-center justify-center"
              >
                削除
              </Link>
            </div>
          </div>
        </template>
      </AuthenticatedLayout>
    </template>

