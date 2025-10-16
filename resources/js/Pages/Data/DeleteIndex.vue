<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link, useForm } from '@inertiajs/vue3';
    import FlashMessage from '@/Components/FlashMessage.vue';
    import Pagination from '@/Components/Pagination.vue';

    // props はコントローラから渡されるデータ
    const props = defineProps({
      YMs: Array,
      max_YM: Number,
      bg_YMs: Array,
      bg_max_YM: Number,
      years: Array,
      flash: Object
    })

    // フォーム定義（複数削除フォームをそれぞれ管理）
    const salesForm = useForm({ YM1: props.max_YM, YM2: props.max_YM })
    const yosanForm = useForm({ YM1: props.bg_max_YM, YM2: props.bg_max_YM })
    const hinbanForm = useForm({ year1: '', year2: '' })

    // 単独削除系は CSRF 対策だけで OK
    const simpleForm = useForm({})
    </script>

    <template>
      <Head title="データ削除" />

      <AuthenticatedLayout>
        <template #header>
          <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">データ削除</h2>
            <Link
              :href="route('admin.data.delete_index')"
              class="text-black bg-gray-200 border-0 py-2 px-8 hover:bg-gray-300 rounded text-sm"
            >
              クリア
            </Link>
          </div>
        </template>

        <div class="py-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
          <!-- フラッシュメッセージ -->
          <FlashMessage />

          <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-900">
            <!-- 売上データ削除 -->
            <div class="mb-6">
              <span class="block mb-2">データ選択削除</span>
              <form
                @submit.prevent="salesForm.delete(route('admin.data.sales_destroy'))"
                class="flex items-center gap-2"
              >
                <select v-model="salesForm.YM1" class="w-32 h-8 rounded text-sm">
                  <option :value="props.max_YM">年月選択(from)</option>
                  <option v-for="ym in props.YMs" :key="ym.YM" :value="ym.YM">
                    {{ Math.floor(ym.YM / 100) % 100 }}年{{ ym.YM % 100 }}月
                  </option>
                </select>
                <span>～</span>
                <select v-model="salesForm.YM2" class="w-32 h-8 rounded text-sm">
                  <option :value="props.max_YM">年月選択(to)</option>
                  <option v-for="ym in props.YMs" :key="ym.YM" :value="ym.YM">
                    {{ Math.floor(ym.YM / 100) % 100 }}年{{ ym.YM % 100 }}月
                  </option>
                </select>
                <button
                  type="submit"
                  class="text-sm w-32 text-white bg-red-500 py-1 px-4 rounded hover:bg-red-600"
                >
                  売上データ削除
                </button>
              </form>
            </div>

            <!-- 予算データ削除 -->
            <div class="mb-6">
              <form
                @submit.prevent="yosanForm.delete(route('admin.data.yosan_destroy'))"
                class="flex items-center gap-2"
              >
                <select v-model="yosanForm.YM1" class="w-32 h-8 rounded text-sm">
                  <option :value="props.bg_max_YM">年月選択(from)</option>
                  <option v-for="ym in props.bg_YMs" :key="ym.YM" :value="ym.YM">
                    {{ Math.floor(ym.YM / 100) % 100 }}年{{ ym.YM % 100 }}月
                  </option>
                </select>
                <span>～</span>
                <select v-model="yosanForm.YM2" class="w-32 h-8 rounded text-sm">
                  <option :value="props.bg_max_YM">年月選択(to)</option>
                  <option v-for="ym in props.bg_YMs" :key="ym.YM" :value="ym.YM">
                    {{ Math.floor(ym.YM / 100) % 100 }}年{{ ym.YM % 100 }}月
                  </option>
                </select>
                <button
                  type="submit"
                  class="text-sm w-32 text-white bg-red-500 py-1 px-4 rounded hover:bg-red-600"
                >
                  予算データ削除
                </button>
              </form>
            </div>

            <!-- 品番削除 -->
            <div class="mb-6">
              <form
                @submit.prevent="hinbanForm.delete(route('admin.data.hinban_destroy'))"
                class="flex items-center gap-2"
              >
                <select v-model="hinbanForm.year1" class="w-32 h-8 rounded text-sm">
                  <option value="">年度選択(from)</option>
                  <option v-for="year in props.years" :key="year.year_code" :value="year.year_code">
                    {{ year.year_code }}年度
                  </option>
                </select>
                <span>～</span>
                <select v-model="hinbanForm.year2" class="w-32 h-8 rounded text-sm">
                  <option value="">年度選択(to)</option>
                  <option v-for="year in props.years" :key="year.year_code" :value="year.year_code">
                    {{ year.year_code }}年度
                  </option>
                </select>
                <button
                  type="submit"
                  class="text-sm w-32 text-white bg-red-500 py-1 px-4 rounded hover:bg-red-600"
                >
                  品番削除
                </button>
              </form>
            </div>

            <!-- データ全削除 -->
            <div class="mt-8">
              <span class="block mb-2">データ全削除</span>

              <div class="flex flex-wrap gap-2">
                <form @submit.prevent="simpleForm.delete(route('admin.data.stock_destroy'))">
                  <button class="w-36 text-sm text-white bg-indigo-500 py-1 px-4 hover:bg-indigo-600 rounded">在庫データ削除</button>
                </form>
                <form @submit.prevent="simpleForm.delete(route('admin.data.sku_destroy'))">
                  <button class="w-36 text-sm text-white bg-indigo-500 py-1 px-4 hover:bg-indigo-600 rounded">SKUデータ削除</button>
                </form>
                <form @submit.prevent="simpleForm.delete(route('admin.data.col_destroy'))">
                  <button class="w-36 text-sm text-white bg-indigo-500 py-1 px-4 hover:bg-indigo-600 rounded">Colデータ削除</button>
                </form>
                <form @submit.prevent="simpleForm.delete(route('admin.data.size_destroy'))">
                  <button class="w-36 text-sm text-white bg-indigo-500 py-1 px-4 hover:bg-indigo-600 rounded">Sizeデータ削除</button>
                </form>
                <form @submit.prevent="simpleForm.delete(route('admin.data.unit_destroy'))">
                  <button class="w-36 text-sm text-white bg-indigo-500 py-1 px-4 hover:bg-indigo-600 rounded">Unitデータ削除</button>
                </form>
                <form @submit.prevent="simpleForm.delete(route('admin.data.brand_destroy'))">
                  <button class="w-36 text-sm text-white bg-indigo-500 py-1 px-4 hover:bg-indigo-600 rounded">Brandデータ削除</button>
                </form>
                <form @submit.prevent="simpleForm.delete(route('admin.data.shop_destroy_all'))">
                  <button class="w-36 text-sm text-white bg-indigo-500 py-1 px-4 hover:bg-indigo-600 rounded">店舗データ削除</button>
                </form>
                <form @submit.prevent="simpleForm.delete(route('admin.data.company_destroy'))">
                  <button class="w-36 text-sm text-white bg-indigo-500 py-1 px-4 hover:bg-indigo-600 rounded">会社データ削除</button>
                </form>
                <form @submit.prevent="simpleForm.delete(route('admin.data.area_destroy'))">
                  <button class="w-36 text-sm text-white bg-indigo-500 py-1 px-4 hover:bg-indigo-600 rounded">エリアデータ削除</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </AuthenticatedLayout>
    </template>


