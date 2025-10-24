
<script setup>
    import { router, usePage } from '@inertiajs/vue3'

    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'


    const props = defineProps({
      image: Object,
      sku_images: Array,
      login_user: Object
    })

    // 削除処理
    const deletePost = (id) => {
      if (confirm('本当に削除してもいいですか？')) {
        router.delete(route('admin.sku_image_destroy', { sku: id }))
      }
    }
    </script>

    <template>
      <AuthenticatedLayout title="SKU画像">
        <template #header>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-2">
            SKU画像
          </h2>

          <div class="flex">
            <!-- 戻るボタン -->
            <div>
              <input
                type="hidden"
                class="pl-0 ml-0 md:ml-2 w-32 h-6 items-center bg-gray-100 border rounded"
                name="hinban_id2"
                :value="image.hinban_id"
              />
              <div class="p-2 w-full ml-4 flex mt-2 md:mt-0">
                <button
                  type="button"
                  @click="router.visit(route('image_show', { hinban: image.hinban_id }))"
                  class="w-32 h-8 text-white text-sm bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded"
                >
                  戻る
                </button>
              </div>
            </div>

            <!-- 削除ボタン -->
            <div class="p-2 w-full ml-4 flex mt-0" v-if="login_user.role_id <= 2">
              <div
                class="w-32 h-8 text-center text-sm text-white bg-red-500 border-0 pt-2 px-2 focus:outline-none hover:bg-red-700 rounded cursor-pointer"
                @click="deletePost(image.sku_id)"
              >
                削除
              </div>
            </div>
          </div>
        </template>

        <!-- メイン内容 -->
        <div class="py-2">
          <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-2 bg-white border-b border-gray-200 text-center">
                {{ image.hinban_id }} -- {{ image.col_id }} -- {{ image.size_id }}
                <img
                  class="w-full mx-auto mt-2"
                  :src="`/storage/sku_images/${image.filename}`"
                  alt="SKU画像"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- サムネイルリスト -->

      </AuthenticatedLayout>
    </template>
