<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head, Link, router } from '@inertiajs/vue3'
    import { ref, reactive, onMounted } from 'vue'
    import axios from 'axios'
    import ChartSales from '@/Components/ChartSales.vue'
    import ResultHinbanTable from '@/Components/ResultHinbanTable.vue'

    const props = defineProps({
      image: Object,
      sku_images: Array,
      login_user: Object,
    })

    // 戻るボタン
    const goBack = () => {
        window.history.back();
    };

    // 削除処理
    const deleteImage = (hinban) => {
      if (confirm('本当に削除してもいいですか？')) {
        router.delete(route('admin.image_destroy', { hinban }))
      }
    }

    // 売上データ
    const salesData = reactive({
        weekly: [],
        monthly: [],
        rows: [],  // テーブル用
    })

    // タブ切替
    const activeTab = ref('weekly') // 'weekly' または 'monthly'

    // データ取得
    const fetchSalesData = async () => {
        try {
            const res = await axios.get(`/api/hinbans/${props.image.hinban_id}/sales_trend`)
            salesData.weekly = res.data.weekly
            salesData.monthly = res.data.monthly

            updateTabData()
        } catch(e) {
            console.error(e)
        }
    }

    // タブ切替時に表示データを更新
    const updateTabData = () => {
        if(activeTab.value === 'weekly') {
            // { yw, total } の形に整形
            salesData.rows = salesData.weekly.map(w => ({
                yw: w.week_end,
                total: w.total
            }))
        } else {
            salesData.rows = salesData.monthly.map(m => ({
                ym: m.ym,
                total: m.total
            }))
        }
    }

    onMounted(() => {
        fetchSalesData()
    })
    </script>

    <template>
      <Head title="商品画像" />

      <AuthenticatedLayout>
        <template #header>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-2">
            商品詳細
          </h2>

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

          <div v-if="login_user.role_id <= 2" class="p-2 w-full ml-4 flex mt-2">
            <div
              @click="deleteImage(image.hinban_id)"
              class="cursor-pointer w-32 h-8 text-center text-sm text-white bg-red-500 py-2 px-2 hover:bg-red-700 rounded"
            >
              削除
            </div>
          </div>
        </template>

        <!-- 商品画像 -->
        <div class="py-2">
          <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-4 bg-white border-b border-gray-200">
                <div class="text-gray-800 mb-2">
                  {{ image.hinban_id }} ： {{ image.hinban_name }}
                </div>
                <div class="text-gray-700 mb-2">{{ image.hinban_info }}</div>
                <div class="text-gray-700 mb-4">
                  元売価：{{ image.m_price }}円 　/ 　コスト：{{ image.cost }}円
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
          </div>
        </div>

        <!-- 売上推移グラフ -->
        <div class="py-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
          <h3 class="text-lg font-medium text-gray-800 mb-2">売上推移</h3>

          <!-- タブ切替 -->
          <div class="flex gap-2 mb-2">
            <button @click="activeTab='weekly'; updateTabData()"
              :class="activeTab==='weekly' ? 'bg-indigo-500 text-white' : 'bg-gray-200'"
              class="px-4 py-1 rounded text-sm">
              週次
            </button>
            <button @click="activeTab='monthly'; updateTabData()"
              :class="activeTab==='monthly' ? 'bg-indigo-500 text-white' : 'bg-gray-200'"
              class="px-4 py-1 rounded text-sm">
              月次
            </button>
          </div>

          <!-- グラフ -->
          <ChartSales
            v-if="salesData.rows.length"
            :labels="salesData.rows.map(r => r.yw || r.ym)"
            :values="salesData.rows.map(r => r.total)"
            />

          <!-- テーブル -->
          <ResultHinbanTable
            v-if="salesData.rows.length"
            :rows="salesData.rows"
            :type="activeTab"
          />
        </div>
      </AuthenticatedLayout>
    </template>

