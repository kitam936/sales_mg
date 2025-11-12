<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
    import FlashMessage from '@/Components/FlashMessage.vue';
    import Pagination from '@/Components/Pagination.vue';

    const flash = usePage().props.flash;

    // 各フォーム
    const salesForm = useForm({ sales_data: null });
    const stockForm = useForm({ stock_data: null });
    const skuForm = useForm({ sku_data: null });
    const hinbanForm = useForm({ hinban_data: null });
    const shopForm = useForm({ shop_data: null });
    const companyForm = useForm({ co_data: null });
    const yosanForm = useForm({ yosan_data: null });
    const areaForm = useForm({ ar_data: null });
    const unitForm = useForm({ unit_data: null });
    const brandForm = useForm({ brand_data: null });
    const colForm = useForm({ col_data: null });
    const sizeForm = useForm({ size_data: null });
    const ymForm = useForm({ ym_data: null });
    const ywForm = useForm({ yw_data: null });
    const ymdForm = useForm({ ymd_data: null });
    const yForm = useForm({ y_data: null });

    const submitForm = (form, routeName) => {
      form.post(route(routeName), {
        forceFormData: true,
        onSuccess: () => form.reset(),
      });
    };
    </script>

    <template>
      <Head title="各種データ登録" />

      <AuthenticatedLayout>
        <template #header>

            <h2 class="flex font-semibold text-xl text-gray-800 leading-tight">
              各種データ登録
            </h2>
            <div class="flex">
            <div class="p-2 ">
                <Link as="button" :href="route('admin.data.data_menu')" class="w-40 h-8 flex text-white bg-indigo-500 border-0 py-1 pl-12 focus:outline-none hover:bg-indigo-600 rounded text-ml">Data管理</Link>
            </div>
            <div class="p-4 w-40 ml-20 text-sm items-right mb-0">
              <Link :href="route('admin.data.create')" class="text-black bg-gray-200 py-2 px-8 hover:bg-gray-300 rounded">
                クリア
              </Link>
            </div>
          </div>
        </template>

        <div class="py-2">
          <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 text-gray-900 dark:text-gray-100">

                <!-- フラッシュメッセージ -->
                <FlashMessage />

                <!-- 共通アップロードコンポーネント化しても良いが、ここでは全部明示 -->

                <!-- 売上データ -->
                <form @submit.prevent="submitForm(salesForm, 'admin.data.sales_upload')" class="p-1">
                  <label class="text-sm text-gray-600">売上データ　</label>
                  <input type="file" accept=".csv" @change="e => salesForm.sales_data = e.target.files[0]" class="ml-3 w-1/3" />
                  <button type="submit" class="w-36 text-sm text-white bg-green-500 py-1 px-4 hover:bg-green-600 rounded">売上データ追加</button>
                </form>

                <!-- 在庫データ -->
                <form @submit.prevent="submitForm(stockForm, 'admin.data.stock_upload')" class="p-1">
                  <label class="text-sm text-gray-600">在庫データ　</label>
                  <input type="file" accept=".csv" @change="e => stockForm.stock_data = e.target.files[0]" class="ml-3 w-1/3" />
                  <button type="submit" class="w-36 text-sm text-white bg-green-500 py-1 px-4 hover:bg-green-600 rounded">在庫データ更新</button>
                </form>

                <!-- SKUデータ -->
                <form @submit.prevent="submitForm(skuForm, 'admin.data.sku_upsert')" class="p-1">
                  <label class="text-sm text-gray-600">SKUデータ　</label>
                  <input type="file" accept=".csv" @change="e => skuForm.sku_data = e.target.files[0]" class="ml-3 w-1/3" />
                  <button type="submit" class="w-36 text-sm text-white bg-green-500 py-1 px-4 hover:bg-green-600 rounded">SKUデータ更新</button>
                </form>

                <!-- 品番データ -->
                <form @submit.prevent="submitForm(hinbanForm, 'admin.data.hinban_upsert')" class="p-1">
                  <label class="text-sm text-gray-600">品番データ　</label>
                  <input type="file" accept=".csv" @change="e => hinbanForm.hinban_data = e.target.files[0]" class="ml-3 w-1/3" />
                  <button type="submit" class="w-36 text-sm text-white bg-green-500 py-1 px-4 hover:bg-green-600 rounded">品番データ更新</button>
                </form>

                <!-- 店舗データ -->
                <form @submit.prevent="submitForm(shopForm, 'admin.data.shop_upsert')" class="p-1">
                  <label class="text-sm text-gray-600">店舗データ　</label>
                  <input type="file" accept=".csv" @change="e => shopForm.shop_data = e.target.files[0]" class="ml-3 w-1/3" />
                  <button type="submit" class="w-36 text-sm text-white bg-blue-500 py-1 px-4 hover:bg-blue-600 rounded">店舗データ更新</button>
                </form>

                <!-- 会社データ -->
                <form @submit.prevent="submitForm(companyForm, 'admin.data.company_upsert')" class="p-1">
                  <label class="text-sm text-gray-600">会社データ　</label>
                  <input type="file" accept=".csv" @change="e => companyForm.co_data = e.target.files[0]" class="ml-3 w-1/3" />
                  <button type="submit" class="w-36 text-sm text-white bg-blue-500 py-1 px-4 hover:bg-blue-600 rounded">会社データ更新</button>
                </form>

                <!-- 予算データ -->
                <form @submit.prevent="submitForm(yosanForm, 'admin.data.yosan_upload')" class="p-1">
                  <label class="text-sm text-gray-600">予算データ　</label>
                  <input type="file" accept=".csv" @change="e => yosanForm.yosan_data = e.target.files[0]" class="ml-3 w-1/3" />
                  <button type="submit" class="w-36 text-sm text-white bg-blue-500 py-1 px-4 hover:bg-blue-600 rounded">予算データ更新</button>
                </form>

                <!-- エリアデータ -->
                <form @submit.prevent="submitForm(areaForm, 'admin.data.area_upsert')" class="p-1">
                  <label class="text-sm text-gray-600">エリアデータ　</label>
                  <input type="file" accept=".csv" @change="e => areaForm.ar_data = e.target.files[0]" class="ml-3 w-1/3" />
                  <button type="submit" class="w-36 text-sm text-white bg-indigo-500 py-1 px-4 hover:bg-indigo-600 rounded">エリアデータ更新</button>
                </form>

                <!-- UNITデータ -->
                <form @submit.prevent="submitForm(unitForm, 'admin.data.unit_upsert')" class="p-1">
                  <label class="text-sm text-gray-600">UNITデータ　</label>
                  <input type="file" accept=".csv" @change="e => unitForm.unit_data = e.target.files[0]" class="ml-3 w-1/3" />
                  <button type="submit" class="w-36 text-sm text-white bg-indigo-500 py-1 px-4 hover:bg-indigo-600 rounded">UNITデータ更新</button>
                </form>

                <!-- Brandデータ -->
                <form @submit.prevent="submitForm(brandForm, 'admin.data.brand_upsert')" class="p-1">
                  <label class="text-sm text-gray-600">ブランドデータ</label>
                  <input type="file" accept=".csv" @change="e => brandForm.brand_data = e.target.files[0]" class="ml-3 w-1/3" />
                  <button type="submit" class="w-36 text-sm text-white bg-indigo-500 py-1 px-4 hover:bg-indigo-600 rounded">Brandデータ更新</button>
                </form>

                <!-- Colデータ -->
                <form @submit.prevent="submitForm(colForm, 'admin.data.col_upsert')" class="p-1">
                  <label class="text-sm text-gray-600">カラーデータ　</label>
                  <input type="file" accept=".csv" @change="e => colForm.col_data = e.target.files[0]" class="ml-3 w-1/3" />
                  <button type="submit" class="w-36 text-sm text-white bg-indigo-500 py-1 px-4 hover:bg-indigo-600 rounded">Colデータ更新</button>
                </form>

                <!-- Sizeデータ -->
                <form @submit.prevent="submitForm(sizeForm, 'admin.data.size_upsert')" class="p-1">
                  <label class="text-sm text-gray-600">サイズデータ　</label>
                  <input type="file" accept=".csv" @change="e => sizeForm.size_data = e.target.files[0]" class="ml-3 w-1/3" />
                  <button type="submit" class="w-36 text-sm text-white bg-indigo-500 py-1 px-4 hover:bg-indigo-600 rounded">Sizeデータ更新</button>
                </form>

                <!-- YMデータ -->
                <!-- <form @submit.prevent="submitForm(ymForm, 'admin.data.ym_upsert')" class="p-1">
                  <label class="text-sm text-gray-600">YMデータ</label>
                  <input type="file" accept=".csv" @change="e => ymForm.ym_data = e.target.files[0]" class="ml-3 w-1/3" />
                  <button type="submit" class="w-36 text-sm text-white bg-indigo-500 py-1 px-4 hover:bg-indigo-600 rounded">YMデータ更新</button>
                </form> -->

                <!-- YWデータ -->
                <!-- <form @submit.prevent="submitForm(ywForm, 'admin.data.yw_upsert')" class="p-1">
                  <label class="text-sm text-gray-600">YWデータ</label>
                  <input type="file" accept=".csv" @change="e => ywForm.yw_data = e.target.files[0]" class="ml-3 w-1/3" />
                  <button type="submit" class="w-36 text-sm text-white bg-indigo-500 py-1 px-4 hover:bg-indigo-600 rounded">YWデータ更新</button>
                </form> -->

                <!-- YMDデータ -->
                <form @submit.prevent="submitForm(ymdForm, 'admin.data.ymd_upsert')" class="p-1">
                  <label class="text-sm text-gray-600">YMDデータ　</label>
                  <input type="file" accept=".csv" @change="e => ymdForm.ymd_data = e.target.files[0]" class="ml-3 w-1/3" />
                  <button type="submit" class="w-36 text-sm text-white bg-indigo-500 py-1 px-4 hover:bg-indigo-600 rounded">YMDデータ更新</button>
                </form>

                <!-- Yデータ -->
                <!-- <form @submit.prevent="submitForm(yForm, 'admin.data.y_upsert')" class="p-1">
                  <label class="text-sm text-gray-600">Yデータ</label>
                  <input type="file" accept=".csv" @change="e => yForm.y_data = e.target.files[0]" class="ml-3 w-1/3" />
                  <button type="submit" class="w-36 text-sm text-white bg-indigo-500 py-1 px-4 hover:bg-indigo-600 rounded">Yデータ更新</button>
                </form> -->

              </div>
            </div>
          </div>
        </div>
      </AuthenticatedLayout>
    </template>
