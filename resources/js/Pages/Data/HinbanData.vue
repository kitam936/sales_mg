
<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link, useForm, router } from '@inertiajs/vue3';
    import { ref, watch } from 'vue';
    import FlashMessage from '@/Components/FlashMessage.vue';
    import Pagination from '@/Components/Pagination.vue';

    const props = defineProps({
      years: Array,
      brands: Array,
      units: Array,
      products: Object, // ページネーション付き
      filters: Object   // { year_code, brand_code, unit_code }
    });

    // 検索条件
    const form = useForm({
      year_code: props.filters.year_code || '',
      brand_code: props.filters.brand_code || '',
      unit_code: props.filters.unit_code || ''
    });

    // 値変更で即検索
    watch(
      () => [form.year_code, form.brand_code, form.unit_code],
      () => {
        router.get(route('admin.data.hinban_index'), form, { preserveState: true, replace: true });
      }
    );

    const resetFilters = () => {
      form.year_code = '';
      form.brand_code = '';
      form.unit_code = '';
      router.get(route('admin.data.hinban_index'));
    };
    </script>

    <template>
      <AuthenticatedLayout>
        <Head title="品番データ" />

        <template #header>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <div class="flex">
              <div>品番データ</div>
              <div class="w-40 ml-60 text-sm items-right mb-0">
                <Link
                  :href="route('admin.data.data_index')"
                  class="w-32 text-center text-sm text-white bg-indigo-500 border-0 py-1 px-2 focus:outline-none hover:bg-indigo-700 rounded"
                >
                  戻る
                </Link>
              </div>
            </div>
          </h2>
        </template>

        <!-- フラッシュメッセージ -->
        <FlashMessage />

        <!-- 検索フォーム -->
        <div class="mt-4">
          <div class="lg:flex">
            <div class="md:flex">
              <label for="year_code" class="items-center text-sm mt-2">年度CD：</label>
              <select v-model="form.year_code" id="year_code" class="w-24 h-8 text-sm pt-1 mr-2 mb-2 border">
                <option value="">指定なし</option>
                <option v-for="year in years" :key="year.year_code" :value="year.year_code">
                  {{ year.year_code }}
                </option>
              </select>

              <label for="brand_code" class="items-center text-sm mt-2">Brand：</label>
              <select v-model="form.brand_code" id="brand_code" class="w-24 h-8 text-sm pt-1 border mb-2 mr-4">
                <option value="">指定なし</option>
                <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                  {{ brand.id }}
                </option>
              </select>
            </div>

            <div class="flex">
              <label for="unit_code" class="items-center text-sm mt-2">Unit：</label>
              <select v-model="form.unit_code" id="unit_code" class="w-24 h-8 text-sm pt-1 mr-4 mb-2 border">
                <option value="">指定なし</option>
                <option v-for="unit in units" :key="unit.id" :value="unit.id">
                  {{ unit.id }}
                </option>
              </select>

              <div>
                <button
                  type="button"
                  class="w-20 h-8 bg-indigo-500 text-white ml-2 hover:bg-indigo-600 rounded"
                  @click="resetFilters"
                >
                  全表示
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- 一覧テーブル -->
        <div class="py-6 border">
          <div class="mx-auto sm:px-4 lg:px-4 border">
            <table class="md:w-2/3 bg-white table-auto w-full text-center whitespace-no-wrap">
              <thead>
                <tr>
                  <th class="w-1/15 py-1 text-sm bg-gray-100">年</th>
                  <th class="w-1/15 py-1 text-sm bg-gray-100">BR</th>
                  <th class="w-1/15 py-1 text-sm bg-gray-100">UNIT</th>
                  <th class="w-1/15 py-1 text-sm bg-gray-100">Face</th>
                  <th class="w-1/15 py-1 text-sm bg-gray-100">品番</th>
                  <th class="w-1/15 py-1 text-sm bg-gray-100">品名</th>
                  <th class="w-3/15 py-1 text-sm bg-gray-100">元売価</th>
                  <th class="w-3/15 py-1 text-sm bg-gray-100">現売価</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="product in products.data" :key="product.id">
                  <td class="py-1">{{ product.year_code }}</td>
                  <td class="py-1">{{ product.brand_id }}</td>
                  <td class="py-1">{{ product.unit_id }}</td>
                  <td class="py-1">{{ product.face }}</td>
                  <td class="py-1">{{ product.id }}</td>
                  <td class="py-1 text-left">{{ product.hinban_name }}</td>
                  <td class="py-1 text-right">{{ Number(product.m_price).toLocaleString() }}</td>
                  <td class="py-1 text-right">{{ Number(product.price).toLocaleString() }}</td>
                </tr>
              </tbody>
            </table>

            <!-- ページネーション -->
            <div class="mt-2">
              <Pagination :links="products.links" />
            </div>
          </div>
        </div>
      </AuthenticatedLayout>
    </template>
