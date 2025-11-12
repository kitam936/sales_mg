<script setup>
    import { ref, computed } from 'vue'
    import { usePage, router } from '@inertiajs/vue3'
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import Pagination from '@/Components/Pagination.vue'

    // Inertia の props を取得
    const page = usePage()
    const props = page.props

    // Laravel から渡されたデータ（安全に初期化）
    const years   = props.years ?? []
    const brands  = props.brands ?? []

    const units   = props.units ?? []
    const faces   = props.faces ?? []

    // ページネーション付きデータ
    const sku_images = ref(props.sku_images?.data ?? [])
    const links  = computed(() => props.sku_images?.links ?? [])



    // フィルター初期値
    const filters = props.filters ?? {}
    const year_code   = ref(filters.year_code ?? '')
    const brand_code  = ref(filters.brand_code ?? '')

    const unit_code   = ref(filters.unit_code ?? '')
    const face        = ref(filters.face ?? '')

    // フィルター変更時
    const submitFilters = () => {
        router.get(route('admin.sku_image_check'), {
          year_code: year_code.value,
          brand_code: brand_code.value,

          unit_code: unit_code.value,
          face: face.value,
        }, {
          replace: true,       // URL を置き換える（ブラウザ履歴に残らない）
          preserveScroll: true // スクロール位置を維持
        })
      }

      const clearFilters = () => {
        year_code.value = ''
        brand_code.value = ''

        unit_code.value = ''
        face.value = ''

        // フィルター解除後に再検索
        router.get(route('admin.sku_image_check'), {}, {
          replace: true,
          preserveScroll: true
        })
      }

      const downloadCsv = () => {
        const params = new URLSearchParams({
          year_code: year_code.value,
          brand_code: brand_code.value,

          unit_code: unit_code.value,
          face: face.value
        }).toString()

        // 新しいタブで CSV をダウンロード
        window.open(`${route('admin.sku_image_csv')}?${params}`, '_blank')
      }

    // デバッグ用
    console.log('years:', years)
    console.log('brands:', brands)

    console.log('units:', units)
    console.log('faces:', faces)
    console.log('sku_images:', sku_images.value)
    console.log('filters:', filters)
    </script>

    <template>
      <AuthenticatedLayout>
        <template #header>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            SKU写真未登録リスト
          </h2>

          <div class="flex mt-2 space-x-2">
            <button type="button" @click="router.visit(route('admin.data.data_index'))"
              class="w-32 h-8 bg-indigo-500 text-white rounded hover:bg-indigo-600 text-sm">Index</button>

            <button
              type="button"
              @click="downloadCsv"
              class="w-32 h-8 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm"
            >
              CSVダウンロード
            </button>
          </div>

          <div class="flex mt-4 space-x-4">
            <select v-model="year_code" @change="submitFilters" class="w-24 h-8 rounded text-sm">
              <option value="">年度</option>
              <option v-for="y in years" :key="y.year_code" :value="y.year_code">{{ y.year_code }}</option>
              <option v-if="years.length === 0" disabled>データなし</option>
            </select>

            <select v-model="brand_code" @change="submitFilters" class="w-24 h-8 rounded text-sm">
              <option value="">ブランド</option>
              <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.id }}</option>
              <option v-if="brands.length === 0" disabled>データなし</option>
            </select>

            <!-- <select v-model="season_code" @change="submitFilters" class="w-24 h-8 rounded text-sm">
              <option value="">季節</option>
              <option v-for="s in seasons" :key="s.season_id" :value="s.season_id">{{ s.season_name }}</option>
              <option v-if="seasons.length === 0" disabled>データなし</option>
            </select> -->

            <select v-model="unit_code" @change="submitFilters" class="w-24 h-8 rounded text-sm">
              <option value="">Unit</option>
              <option v-for="u in units" :key="u.unit_code" :value="u.unit_code">{{ u.id }}</option>
              <option v-if="units.length === 0" disabled>データなし</option>
            </select>

            <select v-model="face" @change="submitFilters" class="w-24 h-8 rounded text-sm">
              <option value="">Face</option>
              <option v-for="f in faces" :key="f.face" :value="f.face">{{ f.face }}</option>
              <option v-if="faces.length === 0" disabled>データなし</option>
            </select>
            <button
            type="button"
            @click="clearFilters"
            class="w-32 h-8 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm"
            >
            条件クリア
            </button>

          </div>


        </template>

        <div class="py-6 border">
          <div class="mx-auto sm:px-4 lg:px-4 border">
            <table class="w-full text-center bg-white table-auto whitespace-no-wrap">
              <thead>
                <tr>
                  <th class="w-1/15 py-1 text-sm  text-center bg-gray-100">BR</th>
                  <!-- <th class="w-2/15 py-1 text-sm  text-center bg-gray-100">季節</th> -->
                  <th class="w-1/15 py-1 text-sm  text-center bg-gray-100">UNIT</th>
                  <th class="w-1/15 py-1 text-sm  text-center bg-gray-100">Face</th>
                  <th class="w-2/8 py-1 text-sm  text-center bg-gray-100">品番</th>
                  <th class="w-2/8 py-1 text-sm  text-center bg-gray-100">Col</th>
                  <th class="w-2/8 py-1 text-sm  text-center bg-gray-100">SZ</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="img in sku_images" :key="img.sku">
                  <td class="text-center">{{ img.brand_id ?? '-' }}</td>
                  <!-- <td class="text-center">{{ img.season_name ?? '-' }}</td> -->
                  <td class="text-center">{{ img.unit ?? '-' }}</td>
                  <td class="text-center">{{ img.face ?? '-' }}</td>
                  <td class="text-center">{{ img.hinban ?? '-' }}</td>
                  <td class="text-center">{{ img.col_id ?? '-' }}</td>
                  <td class="text-center">{{ img.size_id ?? '-' }}</td>
                </tr>
                <tr v-if="sku_images.length === 0">
                  <td colspan="6" class="text-center">該当データがありません</td>
                </tr>
              </tbody>
            </table>

            <!-- ページネーション -->
            <Pagination :links="links" class="mt-4" />
          </div>
        </div>
      </AuthenticatedLayout>
    </template>
