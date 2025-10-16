<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link } from '@inertiajs/vue3';
    import FlashMessage from '@/Components/FlashMessage.vue';
    </script>

    <template>
      <Head title="データINDEX" />

      <AuthenticatedLayout>
        <template #header>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            データINDEX
          </h2>
        </template>

        <!-- フラッシュメッセージ -->
        <FlashMessage />

        <div class="py-4 text-gray-900">
          <!-- 1段目 -->
          <div class="flex px-4 py-2 md:w-2/3">
            <Link
              :href="route('admin.data.area_index')"
              class="w-32 h-8 ml-4 flex-auto text-sm text-white bg-indigo-400 hover:bg-indigo-600 rounded flex items-center justify-center"
            >
              エリアデータ
            </Link>
            <Link
              :href="route('admin.data.company_index')"
              class="w-32 h-8 ml-4 flex-auto text-sm text-white bg-indigo-400 hover:bg-indigo-600 rounded flex items-center justify-center"
            >
              会社データ
            </Link>
            <Link
              :href="route('admin.data.shop_index')"
              class="w-32 h-8 ml-4 flex-auto text-sm text-white bg-indigo-400 hover:bg-indigo-600 rounded flex items-center justify-center"
            >
              店舗データ
            </Link>
          </div>


          <!-- 2段目 -->
          <div class="flex px-4 py-2 md:w-2/3">
            <Link :href="route('admin.data.unit_index')" class="w-32 h-8 ml-4 flex-auto text-sm text-white bg-indigo-400 hover:bg-indigo-600 rounded flex items-center justify-center">UNITデータ</Link>
            <Link :href="route('admin.data.brand_index')" class="w-32 h-8 ml-4 flex-auto text-sm text-white bg-indigo-400 hover:bg-indigo-600 rounded flex items-center justify-center">BRANDデータ</Link>
            <Link :href="route('admin.data.hinban_index')" class="w-32 h-8 ml-4 flex-auto text-sm text-white bg-indigo-400 hover:bg-indigo-600 rounded flex items-center justify-center">品番データ</Link>
          </div>

          <!-- 3段目 -->
          <div class="flex px-4 py-2 md:w-2/3">
            <Link :href="route('admin.data.sku_index')" class="w-32 h-8 ml-4 flex-auto text-sm text-white bg-indigo-400 hover:bg-indigo-600 rounded flex items-center justify-center">SKUデータ</Link>
            <Link :href="route('admin.data.col_index')" class="w-32 h-8 ml-4 flex-auto text-sm text-white bg-indigo-400 hover:bg-indigo-600 rounded flex items-center justify-center">Colデータ</Link>
            <Link :href="route('admin.data.size_index')" class="w-32 h-8 ml-4 flex-auto text-sm text-white bg-indigo-400 hover:bg-indigo-600 rounded flex items-center justify-center">Sizeデータ</Link>
          </div>

          <!-- 4段目 -->
          <div class="flex px-4 py-2 md:w-2/3">
            <Link :href="route('admin.data.sales_index')" class="w-32 h-8 ml-4 flex-auto text-sm text-white bg-indigo-400 hover:bg-indigo-600 rounded flex items-center justify-center">売上データ</Link>
            <Link :href="route('admin.data.stock_index')" class="w-32 h-8 ml-4 flex-auto text-sm text-white bg-indigo-400 hover:bg-indigo-600 rounded flex items-center justify-center">在庫データ</Link>
            <Link :href="route('admin.data.yosan_index')" class="w-32 h-8 ml-4 flex-auto text-sm text-white bg-indigo-400 hover:bg-indigo-600 rounded flex items-center justify-center">予算データ</Link>
          </div>

          <!-- 5段目 -->
          <div class="flex px-4 py-2 md:w-2/3">
            <Link :href="route('admin.data.ym_index')" class="w-32 h-8 ml-4 flex-auto text-sm text-white bg-indigo-400 hover:bg-indigo-600 rounded flex items-center justify-center">YMデータ</Link>
            <Link :href="route('admin.data.yw_index')" class="w-32 h-8 ml-4 flex-auto text-sm text-white bg-indigo-400 hover:bg-indigo-600 rounded flex items-center justify-center">YWデータ</Link>
            <Link :href="route('admin.data.ymd_index')" class="w-32 h-8 ml-4 flex-auto text-sm text-white bg-indigo-400 hover:bg-indigo-600 rounded flex items-center justify-center">YMDデータ</Link>
          </div>

          <!-- 6段目 -->
          <div class="flex px-4 py-2 md:w-2/3">
            <Link :href="route('admin.data.y_index')" class="w-32 h-8 ml-4 flex-auto text-sm text-white bg-indigo-400 hover:bg-indigo-600 rounded flex items-center justify-center">Yデータ</Link>
            <Link :href="route('admin.hinban_image_check')" class="w-32 h-8 ml-4 flex-auto text-sm text-white bg-indigo-400 hover:bg-indigo-600 rounded flex items-center justify-center">品番写真未登録</Link>
            <Link :href="route('admin.sku_image_check')" class="w-32 h-8 ml-4 flex-auto text-sm text-white bg-indigo-400 hover:bg-indigo-600 rounded flex items-center justify-center">SKU写真未登録</Link>
          </div>
        </div>
      </AuthenticatedLayout>
    </template>


