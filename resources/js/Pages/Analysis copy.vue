<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import { ref ,reactive ,onMounted} from 'vue';
    import { getToday } from '@/common';
    import Chart from '@/Components/Chart.vue';
    import axios from 'axios';
    import ResultTable from '@/Components/ResultTable.vue';
    import { Link } from '@inertiajs/vue3';
    import { computed } from 'vue';

    const form = reactive({
        startDate: null,
        endDate: null,
        type: '', // 初期値
        company_id: '',
        shop_id: '',
        pic_id: '',
        brand_id: '',
        season_id: '',
        unit_id: '',
        face_id: '',
        designer_id: '',

    });

    const data = reactive({
        companies: [],
        shops: [],
        pics: [],
        brands: [],
        seasons: [],
        units: [],
        faces: [],
        designers: [],
        data: [],
        labels: [],
        totals: [],
        movingAverages: [],
        movingAveragesProfit:[]
    });

    // computedでtypeが時間単位系のときのみ絞り込み表示
    const showFilters = computed(() =>
    ['py', 'pw', 'pm'].includes(form.type)
    )

    // 初期ロード
    onMounted(() => {
        const today = getToday();
        form.startDate = today;
        form.endDate = today;
        getData();
    });

    const getData = async() => {
        try{
            await axios.get('/api/analysis', {
                params:{
                    startDate: form.startDate,
                    endDate: form.endDate,
                    type: form.type,
                    company_id: form.company_id,
                    shop_id: form.shop_id,
                    pic_id: form.pic_id,
                    brand_id: form.brand_id,
                    season_id: form.season_id,
                    unit_id: form.unit_id,
                    face_id: form.face_id,
                    designer_id: form.designer_id,
                }
            })
            .then(res => {
                data.data = res.data.data;
                data.labels = res.data.labels;
                data.totals = res.data.totals;
                data.movingAverages = res.data.movingAverages ?? []; // ★追加
                data.movingAveragesProfit = res.data.movingAveragesProfit ?? []; // ★追加
                data.companies = res.data.companies;
                data.shops = res.data.shops;
                data.pics = res.data.pics;
                data.brands = res.data.brands;
                data.seasons = res.data.seasons;
                data.units = res.data.units;
                data.faces = res.data.faces;
                data.designers = res.data.designers;
            })
        }catch(e) {
            console.log(e.message)
        }
    }

   // 🔹 検索条件クリア
    const clearFilters = async () => {
        try {
            // form の選択条件をリセット
            form.company_id = '';
            form.shop_id = '';
            form.pic_id = '';
            form.brand_id = '';
            form.season_id = '';
            form.unit_id = '';
            form.face_id = '';
            form.designer_id = '';

            // 再検索
            await axios.get('/api.analysis', {
                params:{
                    startDate: form.startDate,
                    endDate: form.endDate,
                    type: form.type,
                    company_id: form.company_id,
                    shop_id: form.shop_id,
                    pic_id: form.pic_id,
                    brand_id: form.brand_id,
                    season_id: form.season_id,
                    unit_id: form.unit_id,
                    face_id: form.face_id,
                    designer_id: form.designer_id,
                }
            })
            .then(res => {
                data.data = res.data.data;
                data.labels = res.data.labels;
                data.totals = res.data.totals;
                // ✅ ここは API から受け取ったまま使う
                data.companies = res.data.companies;
                data.shops = res.data.shops;
                data.pics = res.data.pics;
                data.brands = res.data.brands;
                data.seasons = res.data.seasons;
                data.units = res.data.units;
                data.faces = res.data.faces;
                data.designers = res.data.designers;
                data.movingAverages = res.data.movingAverages ?? [];
                data.movingAveragesProfit = res.data.movingAveragesProfit ?? [];
            })
            .catch(error => console.error(error))
        } catch(e) {
            console.log(e.message)
        }
    };
    </script>

    <template>
        <Head title="データ分析" />

        <AuthenticatedLayout>
            <template #header>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    データ分析
                </h2>

                <div class="ml-4 md:ml-12 mb-0 mt-4">
                    <Link as="button" :href="route('menu')" class="w-32 h-8 bg-indigo-500 text-sm text-white ml-0 hover:bg-indigo-600 rounded">Menu</Link>
                </div>
            </template>

            <div class="py-2">
                <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-3 text-gray-900">
                            <form @submit.prevent="getData">

                                <label class="mr-2 text-sm">期間指定:</label>
                                <div class="flex items-center mb-4">
                                <!-- <label class="mr-2">開始日:</label> -->
                                <input v-model="form.startDate" type="date" class="h-8 w-32 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out" />
                                <label class="ml-2 mr-2">～</label>
                                <input v-model="form.endDate" type="date" class="h-8 w-32 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out" />
                                </div>

                                <!-- 分析タイプ -->
                                <label class="mr-2 text-sm">分析タイプ:</label>
                                <div class="mr-2 mt-2">
                                    <div class="flex flex-wrap gap-2">
                                    <label><input type="radio" value="py" v-model="form.type" /> 年別</label>
                                    <label><input type="radio" value="pm" v-model="form.type" /> 月別</label>
                                    <label><input type="radio" value="pw" v-model="form.type" /> 週別</label>
                                    <label><input type="radio" value="co_total" v-model="form.type" /> 社累計</label>
                                    <label><input type="radio" value="sh_total" v-model="form.type" /> 店累計</label>
                                    <label><input type="radio" value="pic_total" v-model="form.type" /> 担当者累計</label>
                                    <label><input type="radio" value="bd_total" v-model="form.type" /> ブランド累計</label>
                                    <label><input type="radio" value="ss_total" v-model="form.type" /> シーズン累計</label>
                                    <label><input type="radio" value="un_total" v-model="form.type" /> ユニット累計</label>
                                    <label><input type="radio" value="fa_total" v-model="form.type" /> フェイス累計</label>
                                    <label><input type="radio" value="de_total" v-model="form.type" /> デザイナー累計</label>
                                    </div>
                                </div>


                                 <!-- typeに応じて絞り込みを表示 -->
                                <div v-if="showFilters" class="md:flex items-center mb-3">

                                    <label class="ml-0 md:ml-2 md:mt-0 mr-2 text-sm">絞込検索:</label>
                                    <div class="flex items-center ml-0">
                                    <!-- Company選択 -->
                                    <div class="flex">
                                    <div class="relative ">
                                        <select v-model="form.company_id" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                            <option value="">社選択なし</option>
                                            <option v-for="company in data.companies" :key="company.co_id" :value="company.co_id">
                                                {{ company.co_name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Shop選択 -->
                                    <div class="relative ml-2">
                                        <select v-model="form.shop_id" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                            <option value="">店選択なし</option>
                                            <option v-for="shop in data.shops" :key="shop.shop_id" :value="shop.shop_id">
                                                {{ shop.shop_name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- PIC選択 -->
                                    <div class="relative ml-2">
                                        <select v-model="form.pic_id" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                            <option value="">PIC選択なし</option>
                                            <option v-for="pic in data.pics" :key="pic.pic_id" :value="pic.pic_id">
                                                {{ pic.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                </div>
                                </div>


                                <div v-if="showFilters" class="flex items-center ml-0">
                                    <!-- Season選択 -->
                                    <div class="flex">
                                    <div class="relative ">
                                        <select v-model="form.season_id" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                            <option value="">季節選択なし</option>
                                            <option v-for="season in data.seasons" :key="season.season_id" :value="season.season_id">
                                                {{ season.season_name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Unit選択 -->
                                    <div class="relative ml-2">
                                        <select v-model="form.unit_id" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                            <option value="">Unit選択なし</option>
                                            <option v-for="unit in data.units" :key="unit.unit_id" :value="unit.unit_id">
                                                {{ unit.unit_id }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Face選択 -->
                                    <div class="relative ml-2">
                                        <select v-model="form.face_id" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                            <option value="">Face選択なし</option>
                                            <option v-for="face in data.faces" :key="face.face_id" :value="face.face_id">
                                                {{ face.Face_code }}
                                            </option>
                                        </select>
                                    </div>
                                    <!-- Desiger選択 -->
                                    <div class="relative ml-2">
                                        <select v-model="form.designer_id" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                            <option value="">Desiger選択なし</option>
                                            <option v-for="designer in data.designers" :key="designer.designer_id" :value="designer.designer_id">
                                                {{ designer.designer_name }}
                                            </option>
                                        </select>

                                    </div>
                                </div>
                                </div>


                                <div class="flex mt-4">
                                    <button type="submit" class="w-32 ml-4 md:ml-16 h-8 px-4 bg-blue-500 text-white rounded">分析</button>
                                    <button @click="clearFilters" class="md:ml-12 ml-4 w-32 h-8 bg-gray-500 text-white px-1 py-1 rounded">絞込条件クリア</button>
                                </div>

                            </form>

                            <Chart v-show="data.data.length" :data="data" />
                            <ResultTable
                            v-if="data.data && data.data.length > 0"
                            :data="data"
                            :type="form.type"
                        />

                        </div>
                    </div>
                </div>
            </div>


        </AuthenticatedLayout>
    </template>
