<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head, Link, router } from '@inertiajs/vue3'

    const props = defineProps({
      image: Object,
      sku_images: Array,
      login_user: Object,
    })

    // 削除処理
    const deleteImage = (hinban) => {
      if (confirm('本当に削除してもいいですか？')) {
        router.delete(route('admin.image_destroy', { hinban }))
      }
    }

    // 戻るボタンの処理
    const goBack = () => {
        window.history.back();
    };
    </script>

    <template>
      <Head title="商品画像" />

      <AuthenticatedLayout>
        <template #header>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-2">
            商品画像
          </h2>

          <!-- 戻るボタン・画像リストボタン -->
          <div class="flex">
            <div class="ml-6 flex mt-2">
              <button
                type="button"
                @click="goBack"
                class="w-32 h-8 text-white text-sm bg-indigo-500 border-0 py-1 px-2 hover:bg-indigo-600 rounded"
              >
                戻る
              </button>
              <Link
                :href="route('hinbans.index')"
                class="w-32 h-8 ml-6 text-white text-sm bg-indigo-500 border-0 py-1 px-2 hover:bg-indigo-600 rounded text-center"
              >
                商品リスト
              </Link>
            </div>
          </div>

          <!-- 削除ボタン -->
          <div v-if="login_user.role_id <= 2" class="p-2 w-full ml-4 flex mt-2">
            <div
              @click="deleteImage(image.hinban_id)"
              class="cursor-pointer w-32 h-8 text-center text-sm text-white bg-red-500 py-2 px-2 hover:bg-red-700 rounded"
            >
              削除
            </div>
          </div>
        </template>

        <!-- 商品画像メイン -->
        <div class="py-2">
          <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-4 bg-white border-b border-gray-200">
                <div class="text-gray-800 mb-2">
                  {{ image.hinban_id }} ： {{ image.hinban_name }}
                </div>
                <div class="text-gray-700 mb-2">{{ image.hinban_info }}</div>
                <div class="text-gray-700 mb-4">
                  マスタ売価：{{ image.m_price }}円
                </div>

                <div class="md:w-3/4">
                  <img
                    v-if="image.filename"
                    class="w-full mx-auto rounded"
                    :src="`/storage/images/${image.filename}`"
                    alt="商品画像"
                  />
                  <div
                    v-else
                    class="w-full h-64 bg-gray-100 flex items-center justify-center text-gray-400 rounded"
                  >
                    No Image
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- SKU画像一覧 -->
        <div class="flex flex-wrap mt-4">
          <div
            v-for="sku in sku_images"
            :key="sku.id"
            class="w-1/3 md:w-1/4 p-2"
          >
            <div class="text-gray-700 mb-1">
              Col: {{ sku.col_id }} / Size: {{ sku.size_name }}
            </div>

            <!-- <Link :href="route('sku_image_show', { sku: sku.sku_id })"> -->

            <div class="border rounded-md overflow-hidden">
              <img
                v-if="sku.filename"
                :src="`/storage/sku_images/${sku.filename}`"
                class="w-full h-auto"
                alt="SKU画像"
              />
              <div
                v-else
                class="w-full h-32 bg-gray-100 flex items-center justify-center text-gray-400"
              >
                No Image
              </div>
            </div>
            <!-- </Link> -->
          </div>
        </div>
      </AuthenticatedLayout>
    </template>
