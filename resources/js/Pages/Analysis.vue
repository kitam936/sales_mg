<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link } from '@inertiajs/vue3';
    import { ref, reactive, onMounted, computed } from 'vue';
    import { getToday } from '@/common';
    import {get2YearsAgo} from '@/common';
    import Chart from '@/Components/Chart.vue';
    import ResultTable from '@/Components/ResultTable.vue';
    import CompareTable from '@/Components/CompareTable.vue';
    import SalesProductTable from '@/Components/SalesProductTable.vue';
    import SalesDigestTable from '@/Components/SalesDigestTable.vue';
    import axios from 'axios';
    import { watch } from 'vue';

    // 検索フォーム
    const form = reactive({
        startDate: null,
        endDate: null,
        type: '', // 分析タイプ
        compareType: 'monthly', // 比較タイプ
        shoukaType: 'hinban', // 消化率タイプ
        company_id: '',
        shop_id: '',
        pic_id: '',
        brand_id: '',
        season_id: '',
        unit_id: '',
        face: '',
        designer_id: '',
        year_code: '',
    });

    // 分析データ
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
        movingAveragesProfit: [],
        profits: [],
    });

    const rankingRefreshKey = ref(0);

    // CompareTable用
    const compareData = reactive({
        rows: [],
    });

    //shouka率用
    const shoukaData = reactive({
        rows: [],
    });

    // タブ管理
    const activeTab = ref('analysis'); // 'analysis' または 'compare'

    // フィルター表示条件

    const showFilters = computed(() =>
        ['py', 'pw', 'pm','sh_total','co_total','pic_total','bd_total','ss_total','un_total','fa_total','de_total'].includes(form.type)
    );
    const showFilters2 = computed(() =>
        ['py', 'pw', 'pm'].includes(form.type)
    );
    const showFilters3 = computed(() =>
        ['py', 'pw', 'pm','sh_total'].includes(form.type)
    );
    const showFilters4 = computed(() =>
        ['pic_total','de_total'].includes(form.type)
    );

    // 初期ロード
    onMounted(() => {
        const today = getToday();
        const twoYearsAgo = get2YearsAgo(); // ←変数名を修正
        form.startDate = twoYearsAgo;
        form.endDate = today;
        getData();
    });

    // 分析データ取得
    const getData = async() => {
        try{
            const res = await axios.get('/api/analysis', {
                params:{
                    startDate: form.startDate,
                    endDate: form.endDate,
                    type: form.type,
                    compareType: form.compareType,
                    shoukaType: form.shoukaType,
                    company_id: form.company_id,
                    shop_id: form.shop_id,
                    pic_id: form.pic_id,
                    brand_id: form.brand_id,
                    season_id: form.season_id,
                    unit_id: form.unit_id,
                    face: form.face,
                    designer_id: form.designer_id,
                    year_code: form.year_code,
                }
            });

            data.data = res.data.data;
            data.labels = res.data.labels;
            data.totals = res.data.totals;
            data.movingAverages = res.data.movingAverages ?? [];
            data.movingAveragesProfit = res.data.movingAveragesProfit ?? [];
            data.companies = res.data.companies;
            data.shops = res.data.shops;
            data.pics = res.data.pics;
            data.brands = res.data.brands;
            data.seasons = res.data.seasons;
            data.units = res.data.units;
            data.faces = res.data.faces;
            data.designers = res.data.designers;
            data.year_code = res.data.year_code;

        } catch(e) {
            console.log(e.message);
        }
    };

    // CompareTable用データ取得
    const getCompareData = async () => {
        try {
            const res = await axios.get('/sales/comparison', {
                params: {
                    compareType: form.compareType,
                    company_id: form.company_id,
                    shop_id: form.shop_id,
                    startDate: form.startDate,
                    endDate: form.endDate,
                }
            });
            compareData.rows = res.data.rows ?? [];
        } catch(e) {
            console.log(e.message);
        }
    };

    //shouka率用データ取得
    const getShoukaData = async () => {
        try {
            const res = await axios.get('/sales/shouka', {
                params: {
                    shoukaType: form.shoukaType,
                    company_id: form.company_id,
                    shop_id: form.shop_id,
                    brand_id: form.brand_id,
                    season_id: form.season_id,
                    unit_id: form.unit_id,
                    face: form.face,
                    designer_id: form.designer_id,
                    year_code: form.year_code,
                }
            });
            shoukaData.rows = res.data.rows ?? [];
        } catch(e) {
            console.log(e.message);
        }
    };

    // 消化率用の分析開始トリガー
    const shoukaTrigger = ref(0);

    // 分析ボタン押下
    const onAnalyze = async () => {
        await getData();
        if(activeTab.value === 'compare') {
            await getCompareData();
        }
        if (activeTab.value === 'ranking') {
            rankingRefreshKey.value++;
        }
        if(activeTab.value === 'shouka') {
            shoukaTrigger.value++; // 分析開始トリガーを更新
        }
    };

    // 検索条件クリア
    const clearFilters = async () => {
        form.company_id = '';
        form.shop_id = '';
        form.pic_id = '';
        form.brand_id = '';
        form.season_id = '';
        form.unit_id = '';
        form.face = '';
        form.designer_id = '';
        form.year_code = '';
        await onAnalyze();
    };

    // 会社が変わったら店舗を再取得
    watch(() => form.company_id, async (newCompanyId) => {
        try {
            const res = await axios.get('/api/shops', {
                params: { company_id: newCompanyId }
            });
            data.shops = res.data.shops;
            // 会社変更時は店舗選択をクリア
            form.shop_id = '';
        } catch (e) {
            console.error(e.message);
        }
    });

    </script>

    <template>
    <Head title="データ分析" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                データ分析
            </h2>

            <div class="flex gap-2 mt-4">
                <Link as="button" :href="route('menu')" class="w-32 h-8 bg-indigo-500 text-sm text-white hover:bg-indigo-600 rounded">Menu</Link>
                <!-- <Link as="button" :href="route('sales.comparison')" class="w-32 h-8 bg-indigo-500 text-sm text-white hover:bg-indigo-600 rounded">前年対比ページ</Link> -->
            </div>
        </template>

        <div class="py-2">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-3 text-gray-900">

                        <!-- タブ切替 -->
                        <div class="flex gap-2 mb-4">
                            <button @click="activeTab = 'analysis'" :class="activeTab === 'analysis' ? 'bg-indigo-500 text-white' : 'bg-gray-200'" class="w-32 px-4 py-1 rounded text-sm">分析</button>
                            <button @click="activeTab = 'compare'" :class="activeTab === 'compare' ? 'bg-indigo-500 text-white' : 'bg-gray-200'" class="w-32 px-4 py-1 rounded text-sm">昨対</button>
                            <button @click="activeTab = 'ranking'" :class="activeTab === 'ranking' ? 'bg-indigo-500 text-white' : 'bg-gray-200'" class="w-32 px-1 py-1 rounded text-sm">品番売順</button>
                            <button @click="activeTab = 'shouka'" :class="activeTab === 'shouka' ? 'bg-indigo-500 text-white' : 'bg-gray-200'" class="w-32 px-4 py-1 rounded text-sm">消化率</button>
                        </div>

                        <!-- 検索フォーム -->
                        <form @submit.prevent="onAnalyze">
                            <div><label  v-if="activeTab === 'analysis' || activeTab === 'ranking'"class="text-sm font-medium">期間指定:　※初期値は直近24ヶ月</label></div>
                            <div  v-if="activeTab === 'analysis'  || activeTab === 'ranking'" class="flex items-center mb-4">
                                <input v-model="form.startDate" type="date" class="h-8 w-32 rounded border ..." />
                                <label class="mx-2">～</label>
                                <input v-model="form.endDate" type="date" class="h-8 w-32 rounded border ..." />
                            </div>

                            <!-- 前年対比タイプ切替 -->
                            <div v-if="activeTab === 'compare' " class="flex gap-4 mb-2 items-center">
                                <span class="text-sm font-medium">表示タイプ選択:</span>
                                <label><input type="radio" value="monthly" v-model="form.compareType" /> 月別</label>
                                <label><input type="radio" value="weekly" v-model="form.compareType" /> 週別</label>
                            </div>
                            <!-- 分析タイプ切替 -->
                            <div  v-if="activeTab === 'analysis' " class="text-sm font-medium">分析タイプ選択:　※必須</div>
                            <div  v-if="activeTab === 'analysis'" class="flex flex-wrap gap-2 mt-2">
                                <label><input type="radio" value="py" v-model="form.type" /> 年推移</label>
                                <label><input type="radio" value="pm" v-model="form.type" /> 月推移</label>
                                <label><input type="radio" value="pw" v-model="form.type" /> 週推移</label>
                                <label><input type="radio" value="co_total" v-model="form.type" /> 社計</label>
                                <label><input type="radio" value="sh_total" v-model="form.type" /> 店計</label>
                                <label><input type="radio" value="pic_total" v-model="form.type" /> 担当計</label>
                                <label><input type="radio" value="bd_total" v-model="form.type" /> Brand計</label>
                                <label><input type="radio" value="ss_total" v-model="form.type" /> 季節計</label>
                                <label><input type="radio" value="un_total" v-model="form.type" /> Unit計</label>
                                <label><input type="radio" value="fa_total" v-model="form.type" /> Face計</label>
                                <label><input type="radio" value="de_total" v-model="form.type" /> デザイナー計</label>
                            </div>

                             <!-- 消化率タイプ切替 -->
                             <div v-if="activeTab === 'shouka' " class="gap-4 mb-2 items-center">

                            <span class="text-sm font-medium">表示タイプ選択:</span>
                            <div class="flex flex-wrap gap-2 ">
                                <div class="flex items-center gap-2 ">
                                <label><input type="radio" value="hinban" v-model="form.shoukaType" /> 品番別</label>
                                <label><input type="radio" value="brand" v-model="form.shoukaType" /> Brand別</label>
                                <label><input type="radio" value="season" v-model="form.shoukaType" /> 季節別</label>
                                 </div>
                                <div class="flex items-center mt-0 md:mt-0 gap-2 ">
                                <label><input type="radio" value="unit" v-model="form.shoukaType" /> Unit別</label>
                                <label><input type="radio" value="face" v-model="form.shoukaType" /> Face別</label>
                                <label><input type="radio" value="designer" v-model="form.shoukaType" /> デザイナー別</label>
                                </div>
                            </div>
                             </div>

                              <!-- typeに応じて絞り込みを表示 -->
                              <div class="items-center ml-0 mt-2">
                                <!-- <div class="flex items-center ml-0"> -->
                                    <div>
                                    <label class="ml-0 md:ml-2 md:mt-0 mr-2  font-medium text-sm">絞込検索:　※必要に応じて絞込条件を指定</label>
                                    </div>
                                    <div v-if="activeTab === 'shouka'" class="relative mb-2 mt-1">
                                        <select v-model="form.year_code" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                            <option value="">年度選択なし</option>
                                            <option v-for="y in data.year_code" :key="y.year_code" :value="y.year_code">
                                                {{ y.year_code }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="flex items-center ml-0">
                                    <!-- Company選択 -->
                                    <div class="md:flex">
                                <div class="flex">
                                    <div v-if="showFilters || activeTab === 'compare' || activeTab === 'ranking' || activeTab === 'shouka'" class="relative ">
                                        <select v-model="form.company_id" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                            <option value="">社選択なし</option>
                                            <option v-for="company in data.companies" :key="company.co_id" :value="company.co_id">
                                                {{ company.co_name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Shop選択 -->
                                    <div  v-if="showFilters || activeTab === 'compare' || activeTab === 'ranking' || activeTab === 'shouka'" class="relative ml-2">
                                        <select v-model="form.shop_id" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                            <option value="">店選択なし</option>
                                            <option v-for="shop in data.shops" :key="shop.shop_id" :value="shop.shop_id">
                                                {{ shop.shop_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flex">
                                    <!-- Brand選択 -->
                                    <div  v-if="showFilters || activeTab === 'compare' || activeTab === 'ranking' || activeTab === 'shouka'" class="relative md:ml-2 mt-2 md:mt-0">
                                        <select v-model="form.brand_id" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                            <option value="">Brand選択なし</option>
                                            <option v-for="brand in data.brands" :key="brand.brand_id" :value="brand.brand_id">
                                                {{ brand.brand_name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- PIC選択 -->
                                    <div  v-if="showFilters || activeTab === 'compare' || activeTab === 'ranking'" class="relative ml-2 mt-2 md:mt-0">
                                        <select v-model="form.pic_id" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                            <option value="">担当者選択なし</option>
                                            <option v-for="pic in data.pics" :key="pic.pic_id" :value="pic.pic_id">
                                                {{ pic.pic_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                </div>
                                </div>
                                </div>


                                <div class="items-center ml-0 mt-2">
                                <!-- <div class="flex items-center ml-0"> -->
                                    <!-- Season選択 -->
                                    <div class="md:flex">
                                        <div class="flex">
                                    <div v-if="showFilters || activeTab === 'compare' || activeTab === 'ranking' || activeTab === 'shouka'" class="relative ">
                                        <select v-model="form.season_id" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                            <option value="">季節選択なし</option>
                                            <option v-for="season in data.seasons" :key="season.season_id" :value="season.season_id">
                                                {{ season.season_name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Unit選択 -->
                                    <div v-if="showFilters || activeTab === 'compare' || activeTab === 'ranking' || activeTab === 'shouka'" class="relative ml-2">
                                        <select v-model="form.unit_id" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                            <option value="">Unit選択なし</option>
                                            <option v-for="unit in data.units" :key="unit.unit_id" :value="unit.unit_id">
                                                {{ unit.unit_id }}
                                            </option>
                                        </select>
                                    </div>
                                        </div>
                                    <div class="flex mt-2 md:mt-0">
                                    <!-- Face選択 -->
                                    <div v-if="showFilters || activeTab === 'compare' || activeTab === 'ranking' || activeTab === 'shouka'" class="relative md:ml-2 mt-0">
                                        <select v-model="form.face" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                            <option value="">Face選択なし</option>
                                            <option v-for="face in data.faces" :key="face.face" :value="face.face_code">
                                                {{ face.face_code }}--{{ face.face_item }}
                                            </option>
                                        </select>
                                    </div>
                                    <!-- Desiger選択 -->
                                    <div v-if="showFilters || activeTab === 'ranking' || activeTab === 'shouka'" class="relative ml-2">
                                        <select v-model="form.designer_id" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                            <option value="">Desiger選択なし</option>
                                            <option v-for="designer in data.designers" :key="designer.designer_id" :value="designer.designer_id">
                                                {{ designer.designer_name }}
                                            </option>
                                        </select>

                                    </div>
                                    </div>
                                </div>
                                </div>



                            <!-- 分析開始ボタン押下 -->
                            <div class="flex mt-2 mb-2">
                                <button type="button" @click="onAnalyze" class="w-32 ml-0 h-8 px-4 bg-blue-500 text-white rounded">
                                分析開始
                                </button>
                                <button type="button" @click="clearFilters" class="ml-4 w-32 h-8 bg-gray-500 text-white rounded">
                                絞込条件クリア
                                </button>
                            </div>
                            <div class="border-t mt-2 pt-2 text-sm text-gray-600">
                                <!-- ※分析タイプを選択してください。<br/> -->
                                <!-- ※必要に応じて絞込条件を指定してください。<br/> -->
                                ※分析には十数秒かかる場合があります。<br/>
                                <!-- ※データは千円単位で表示。 -->

                            </div>
                        </form>



                        <!-- 分析タブ -->
                        <div v-if="activeTab === 'analysis'">


                                    <Chart v-if="data.labels.length" :data="data" />


                            <ResultTable v-if="data.data && data.data.length" :data="data" :type="form.type" />
                        </div>

                        <!-- 比較タブ -->
                        <div v-if="activeTab === 'compare'">
                            <CompareTable v-if="compareData.rows.length" :data="compareData" :type="form.compareType" />
                            <div v-else class="text-center py-2"></div>
                        </div>

                        <!-- <div v-if="activeTab === 'ranking'">
                            <SalesHinbanTable v-if="data.data && data.data.length" :data="data" :type="'ranking'" />
                        </div> -->


                        <!-- ranking タブ -->
                        <div v-if="activeTab === 'ranking'">
                            <SalesProductTable :filters="form" />
                        </div>

                        <!-- 消化率タブ -->
                        <div v-if="activeTab === 'shouka'">
                            <SalesDigestTable :filters="form" :fetchTrigger="shoukaTrigger" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
    </template>
