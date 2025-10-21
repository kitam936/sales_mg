<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Head, Link, router } from '@inertiajs/vue3'
    import { ref, watch } from 'vue'

    const props = defineProps({
      years: Array,
      brands: Array,
      seasons: Array,
      units: Array,
      faces: Array,
      images: Object,
      filters: Object, // コントローラーから現在の検索条件を渡す
    })

    // 各検索条件をrefに
    const year_code = ref(props.filters.year_code || '')
    const brand_code = ref(props.filters.brand_code || '')
    const season_code = ref(props.filters.season_code || '')
    const unit_code = ref(props.filters.unit_code || '')
    const face = ref(props.filters.face || '')
    const hinban_code = ref(props.filters.hinban_code || '')

    // 検索実行
    function search() {
      router.get(route('hinbans.index'), {
        year_code: year_code.value,
        brand_code: brand_code.value,
        season_code: season_code.value,
        unit_code: unit_code.value,
        face: face.value,
        hinban_code: hinban_code.value,
      })
    }

    // 各セレクトの変更を監視して即送信
    watch([year_code, brand_code, season_code, unit_code, face], search)
    </script>

    <template>
      <Head title="画像リスト" />

      <AuthenticatedLayout>
        <template #header>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-2">
            画像リスト
          </h2>

          <div class="md:flex md:ml-0 mb-2">
            <div class="ml-4 flex mt-2 md:mt-0">
              <div class="pl-2 mt-0 ml-0">
                <Link
                  :href="route('menu')"
                  class="w-32 h-8 text-center text-sm text-white bg-indigo-500 border-0 py-1 px-2 hover:bg-indigo-700 rounded inline-block"
                >
                  Menu
                </Link>
              </div>

            </div>
          </div>

          <div class="mt-4 space-y-2">
            <div class="lg:flex flex-wrap items-center gap-2">
              <!-- 年度 -->
              <label for="year_code" class="text-sm mt-2 text-gray-800">年度CD：</label>
              <select v-model="year_code" id="year_code" class="w-24 h-8 rounded text-sm pt-1 mr-2 mb-2 border">
                <option value="">指定なし</option>
                <option v-for="year in years" :key="year.year_code" :value="year.year_code">
                  {{ year.year_code }}
                </option>
              </select>

              <!-- ブランド -->
              <label for="brand_code" class="text-sm mt-2 text-gray-800">Brand：</label>
              <select v-model="brand_code" id="brand_code" class="w-24 h-8 rounded text-sm pt-1 mr-2 mb-2 border">
                <option value="">指定なし</option>
                <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                  {{ brand.id }}
                </option>
              </select>

              <!-- 季節 -->
              <label for="season_code" class="text-sm mt-2 text-gray-800">季節CD：</label>
              <select v-model="season_code" id="season_code" class="w-24 h-8 rounded text-sm pt-1 mr-2 mb-2 border">
                <option value="">指定なし</option>
                <option v-for="season in seasons" :key="season.season_id" :value="season.season_id">
                  {{ season.season_name }}
                </option>
              </select>

              <!-- Unit -->
              <label for="unit_code" class="text-sm mt-2 text-gray-800">Unit：</label>
              <select v-model="unit_code" id="unit_code" class="w-24 h-8 rounded text-sm pt-1 mr-2 mb-2 border">
                <option value="">指定なし</option>
                <option v-for="unit in units" :key="unit.unit_code" :value="unit.unit_code">
                  {{ unit.id }}
                </option>
              </select>

              <!-- Face -->
              <label for="face" class="text-sm mt-2 text-gray-800">Face：</label>
              <select v-model="face" id="face" class="w-24 h-8 rounded text-sm pt-1 mr-2 mb-2 border">
                <option value="">指定なし</option>
                <option v-for="f in faces" :key="f.face" :value="f.face">
                  {{ f.face }}
                </option>
              </select>

              <!-- 品番 -->
              <label for="hinban_code" class="text-sm mt-2 text-gray-800">品番：</label>
              <input
                v-model="hinban_code"
                id="hinban_code"
                placeholder="品番入力（一部でも可）"
                class="w-36 h-8 rounded text-sm pt-1 border"
                @keyup.enter="search"
              />

              <button
                type="button"
                @click="search"
                class="w-12 h-8 ml-2 text-sm text-center text-gray-900 bg-gray-200 py-0 px-2 hover:bg-gray-300 rounded"
              >
                検索
              </button>

              <Link
                :href="route('hinbans.index')"
                class="w-16 h-8 bg-blue-500 text-sm text-white ml-2 hover:bg-blue-600 rounded text-center py-1"
              >
                全表示
              </Link>
            </div>
          </div>
        </template>

        <div class="py-4">
          <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex flex-wrap">
                  <div
                    v-for="image in images.data"
                    :key="image.id"
                    class="w-1/2 md:w-1/4 p-2 md:p-4"
                  >
                    <div class="border rounded-md p-2">
                      <div class="text-gray-700">{{ image.hinban_id }}</div>
                      <div class="text-gray-700">{{ image.hinban_name.slice(0, 12) }}</div>

                      <!-- <Link :href="route('image_show', { hinban: image.hinban_id })"> -->
                        <img
                          v-if="image.filename"
                          :src="`/storage/images/${image.filename}`"
                          class="rounded-md mt-2"
                          alt="thumbnail"
                        />
                        <div v-else class="w-full h-32 bg-gray-100 flex items-center justify-center text-gray-400">
                          No Image
                        </div>
                      <!-- </Link> -->

                      <div class="text-gray-700 mt-2 ml-4">売価 {{ image.m_price }}円</div>
                    </div>
                  </div>
                </div>

                <!-- ページネーション -->
                <Pagination :links="images.links" class="mt-4" ></Pagination>
              </div>
            </div>
          </div>
        </div>
      </AuthenticatedLayout>
    </template>
