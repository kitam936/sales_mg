<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link } from '@inertiajs/vue3';
    import { ref, reactive, onMounted, computed, watch } from 'vue';
    import { getToday, get2YearsAgo } from '@/common';
    import Chart from '@/Components/Chart.vue';
    import ResultTable from '@/Components/ResultTable.vue';
    import CompareTable from '@/Components/CompareTable.vue';
    import SalesProductTable from '@/Components/SalesProductTable.vue';
    import SalesDigestTable from '@/Components/SalesDigestTable.vue';
    import StockTable from '@/Components/StockTable.vue';
    import axios from 'axios';

    // フォーム
    const form = reactive({
        startDate: null,
        endDate: null,
        type: '',
        compareType: 'monthly',
        shoukaType: '',
        zaikoType: '',
        company_id: '',
        shop_id: '',
        pic_id: '',
        brand_id: '',
        season_id: '',
        unit_id: '',
        face: '',
        designer_id: '',
        year_code: '',
        triggerRanking: 0,
        triggerZaiko: 0,
    });

    // データ
    const data = reactive({
        companies: [],
        shops: [],
        pics: [],
        brands: [],
        seasons: [],
        units: [],
        faces: [],
        designers: [],
        year_code: [],
        data: [],
        labels: [],
        totals: [],
        movingAverages: [],
        movingAveragesProfit: [],
        profits: [],
    });

    // Compare用
    const compareData = reactive({ rows: [] });
    // 消化率用
    const shoukaData = reactive({ rows: [] });
    // 在庫用
    const zaikoData = reactive({ rows: [] });

    // タブ管理
    const activeTab = ref('analysis');
    // 折りたたみ
    const filtersOpen = ref(true);

    // フィルター表示条件
    const showFilters = computed(() =>
        ['py', 'pw', 'pm','sh_total','co_total','pic_total','bd_total','ss_total','un_total','fa_total','de_total'].includes(form.type)
    );

    onMounted(() => {
        const today = getToday();
        const twoYearsAgo = get2YearsAgo();
        form.startDate = twoYearsAgo;
        form.endDate = today;
        getData();
    });

    // データ取得
    const getData = async () => {
        try { const res = await axios.get('/api/analysis', { params: { ...form } }); Object.assign(data, res.data); } catch(e){ console.log(e.message); }
    };
    const getCompareData = async () => { try { const res = await axios.get('/sales/comparison', { params: { ...form } }); compareData.rows = res.data.rows ?? []; } catch(e){ console.log(e.message); } };
    const getShoukaData = async () => { try { const res = await axios.get('/sales/shouka', { params: { ...form } }); shoukaData.rows = res.data.rows ?? []; } catch(e){ console.log(e.message); } };
    const getZaikoData = async () => { try { const res = await axios.get('/stock', { params: { ...form } }); zaikoData.rows = res.data.rows ?? []; } catch(e){ console.log(e.message); } };

    const shoukaTrigger = ref(0);
    const zaikoTrigger = ref(0);

    const onAnalyze = async () => {
        filtersOpen.value = false;
        await getData();
        if(activeTab.value === 'compare') await getCompareData();
        if(activeTab.value === 'ranking') form.triggerRanking++;
        if(activeTab.value === 'shouka') shoukaTrigger.value++;
        if(activeTab.value === 'zaiko') zaikoTrigger.value++;
    };

    const clearFilters = async () => {
        Object.assign(form, { company_id:'', shop_id:'', pic_id:'', brand_id:'', season_id:'', unit_id:'', face:'', designer_id:'', year_code:'' });
        await onAnalyze();
    };

    // 会社変更で店舗更新
    watch(() => form.company_id, async (newVal) => {
        try { const res = await axios.get('/api/shops', { params: { company_id: newVal } }); data.shops = res.data.shops; form.shop_id=''; } catch(e){ console.error(e.message); }
    });
    </script>

    <template>
    <Head title="データ分析" />
    <AuthenticatedLayout>
    <template #header>
    <h2 class="text-xl font-semibold leading-tight text-gray-800">データ分析</h2>
    <div class="flex gap-2 mt-4">
      <Link as="button" :href="route('menu')" class="w-40 h-10 bg-indigo-500 text-sm text-white hover:bg-indigo-600 rounded">Menu</Link>
    </div>
    </template>

    <div class="py-2">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
    <div class="p-3 text-gray-900">

    <!-- タブ切替 -->
    <div class="flex gap-2 mb-4">
      <button @click="activeTab='analysis'" :class="activeTab==='analysis'?'bg-indigo-500 text-white':'bg-gray-200'" class="w-32 px-2 py-1 rounded text-sm">分析</button>
      <button @click="activeTab='compare'" :class="activeTab==='compare'?'bg-indigo-500 text-white':'bg-gray-200'" class="w-32 px-2 py-1 rounded text-sm">昨対</button>
      <button @click="activeTab='ranking'" :class="activeTab==='ranking'?'bg-indigo-500 text-white':'bg-gray-200'" class="w-32 px-2 py-1 rounded text-sm">品番順</button>
      <button @click="activeTab='shouka'" :class="activeTab==='shouka'?'bg-indigo-500 text-white':'bg-gray-200'" class="w-32 px-2 py-1 rounded text-sm">消化率</button>
      <button @click="activeTab='zaiko'" :class="activeTab==='zaiko'?'bg-indigo-500 text-white':'bg-gray-200'" class="w-32 px-2 py-1 rounded text-sm">在庫</button>
    </div>

    <!-- 日付 -->
    <div  v-if="activeTab==='analysis'||activeTab==='ranking'">
      <div><label class="text-sm font-medium">期間指定:　※初期値は直近24ヶ月</label></div>
      <div  class="flex items-center gap-2 mb-2">
      <input v-model="form.startDate" type="date" class="h-8 w-32 rounded border" />
      <span>～</span>
      <input v-model="form.endDate" type="date" class="h-8 w-32 rounded border" />
      </div>
    </div>

    <!-- 分析タイプ -->
    <div v-if="activeTab==='analysis'" class="flex flex-wrap gap-2 mb-2">
     <div  class="text-sm font-medium">分析タイプ選択:　※必須</div>
     <div class="flex flex-wrap gap-2 mb-2">
      <label><input type="radio" value="py" v-model="form.type"/> 年推移</label>
      <label><input type="radio" value="pm" v-model="form.type"/> 月推移</label>
      <label><input type="radio" value="pw" v-model="form.type"/> 週推移</label>
      <label><input type="radio" value="co_total" v-model="form.type"/> 社計</label>
      <label><input type="radio" value="sh_total" v-model="form.type"/> 店計</label>
      <label><input type="radio" value="pic_total" v-model="form.type"/> 担当計</label>
      <label><input type="radio" value="bd_total" v-model="form.type"/> Brand計</label>
      <label><input type="radio" value="ss_total" v-model="form.type"/> 季節計</label>
      <label><input type="radio" value="un_total" v-model="form.type"/> Unit計</label>
      <label><input type="radio" value="fa_total" v-model="form.type"/> Face計</label>
      <label><input type="radio" value="de_total" v-model="form.type"/> デザイナー計</label>
    </div>
    </div>

    <!-- 前年対比タイプ -->
    <div v-if="activeTab==='compare'" class="flex gap-4 mb-2 items-center">
      <span class="text-sm font-medium">表示タイプ選択:</span>
      <label><input type="radio" value="monthly" v-model="form.compareType"/> 月別</label>
      <label><input type="radio" value="weekly" v-model="form.compareType"/> 週別</label>
    </div>

    <!-- 在庫タイプ -->
    <div v-if="activeTab==='zaiko'">
      <div><span class="text-sm font-medium">在庫種別:　※必須</span></div>
      <div class="flex flex-wrap gap-2 mb-2">
      <label><input type="radio" value="hinban" v-model="form.zaikoType"/> 品番計</label>
      <label><input type="radio" value="brand" v-model="form.zaikoType"/> Brand計</label>
      <label><input type="radio" value="season" v-model="form.zaikoType"/> 季節計</label>
      <label><input type="radio" value="unit" v-model="form.zaikoType"/> Unit計</label>
      <label><input type="radio" value="face" v-model="form.zaikoType"/> Face計</label>
      <label><input type="radio" value="co" v-model="form.zaikoType"/> 社計</label>
      <label><input type="radio" value="shop" v-model="form.zaikoType"/> 店計</label>
    </div>
    </div>

    <!-- 消化率タイプ -->
    <div v-if="activeTab==='shouka'">
      <div><span class="text-sm font-medium">表示タイプ:　※必須</span></div>
      <div class="flex flex-wrap gap-2 mb-2">
      <label><input type="radio" value="hinban" v-model="form.shoukaType"/> 品番別</label>
      <label><input type="radio" value="brand" v-model="form.shoukaType"/> Brand別</label>
      <label><input type="radio" value="season" v-model="form.shoukaType"/> 季節別</label>
      <label><input type="radio" value="unit" v-model="form.shoukaType"/> Unit別</label>
      <label><input type="radio" value="face" v-model="form.shoukaType"/> Face別</label>
      <label><input type="radio" value="designer" v-model="form.shoukaType"/> デザイナー別</label>
    </div>
    </div>

    <!-- 折りたたみセレクトボックス -->
    <div class="mb-0 mt-2">
      <button type="button" @click="filtersOpen=!filtersOpen" class="px-4 py-1 bg-gray-300 rounded mb-2">
        {{ filtersOpen ? '絞込条件非表示':'絞込条件表示' }}
      </button>
      <span class="text-sm">　※必要に応じて条件を指定</span>
      <div v-show="filtersOpen" class="border p-2 rounded mb-2 flex flex-wrap gap-2">
        <!-- 元の select v-if はすべて保持 -->
        <div v-if="activeTab==='shouka'||activeTab==='zaiko'">
          <select v-model="form.year_code" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
            <option value="">年度選択なし</option>
            <option v-for="y in data.year_code" :key="y.year_code" :value="y.year_code">{{ y.year_code }}</option>
          </select>
        </div>
        <div v-if="showFilters||activeTab==='compare'||activeTab==='ranking'||activeTab==='shouka'||activeTab==='zaiko'">
          <select v-model="form.company_id" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
            <option value="">社選択なし</option>
            <option v-for="co in data.companies" :key="co.co_id" :value="co.co_id">{{ co.co_name }}</option>
          </select>
        </div>
        <div v-if="showFilters||activeTab==='compare'||activeTab==='ranking'||activeTab==='shouka'||activeTab==='zaiko'">
          <select v-model="form.shop_id" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
            <option value="">店選択なし</option>
            <option v-for="shop in data.shops" :key="shop.shop_id" :value="shop.shop_id">{{ shop.shop_name }}</option>
          </select>
        </div>
        <div v-if="showFilters||activeTab==='compare'||activeTab==='ranking'||activeTab==='shouka'||activeTab==='zaiko'">
          <select v-model="form.brand_id" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
            <option value="">Brand選択なし</option>
            <option v-for="brand in data.brands" :key="brand.brand_id" :value="brand.brand_id">{{ brand.brand_name }}</option>
          </select>
        </div>
        <div v-if="showFilters||activeTab==='compare'">
          <select v-model="form.pic_id" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
            <option value="">担当者選択なし</option>
            <option v-for="pic in data.pics" :key="pic.pic_id" :value="pic.pic_id">{{ pic.pic_name }}</option>
          </select>
        </div>
        <div v-if="showFilters||activeTab==='compare'||activeTab==='ranking'||activeTab==='shouka'||activeTab==='zaiko'">
          <select v-model="form.season_id" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
            <option value="">季節選択なし</option>
            <option v-for="season in data.seasons" :key="season.season_id" :value="season.season_id">{{ season.season_name }}</option>
          </select>
        </div>
        <div v-if="showFilters||activeTab==='compare'||activeTab==='ranking'||activeTab==='shouka'||activeTab==='zaiko'">
          <select v-model="form.unit_id" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
            <option value="">Unit選択なし</option>
            <option v-for="unit in data.units" :key="unit.unit_id" :value="unit.unit_id">{{ unit.unit_id }}</option>
          </select>
        </div>
        <div v-if="showFilters||activeTab==='compare'||activeTab==='ranking'||activeTab==='shouka'||activeTab==='zaiko'">
          <select v-model="form.face" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
            <option value="">Face選択なし</option>
            <option v-for="face in data.faces" :key="face.face" :value="face.face_code">{{ face.face_code }}--{{ face.face_item }}</option>
          </select>
        </div>
        <div v-if="showFilters||activeTab==='ranking'||activeTab==='shouka'">
          <select v-model="form.designer_id" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
            <option value="">デザイナー選択なし</option>
            <option v-for="d in data.designers" :key="d.designer_id" :value="d.designer_id">{{ d.designer_name }}</option>
          </select>
        </div>
      </div>
    </div>

    <!-- 分析開始 / クリア -->
    <div>
        <label class="ml-0 md:ml-2 md:mt-0 mr-2  font-medium text-sm">必要項目を選んで分析開始ボタンをクリック</label>
    </div>
    <div class="flex mt-2 mb-4">
      <button type="button" @click="onAnalyze" class="w-40 h-8 px-4 bg-blue-500 text-white rounded">分析開始</button>
      <button type="button" @click="clearFilters" class="ml-2 w-40 h-8 bg-gray-500 text-white rounded">絞込条件クリア</button>
    </div>

    <!-- タブ表示 -->
    <div v-if="activeTab==='analysis'">
      <Chart v-if="data.labels.length" :data="data"/>
      <ResultTable v-if="data.data.length" :data="data" :type="form.type"/>
    </div>
    <div v-if="activeTab==='compare'">
      <CompareTable v-if="compareData.rows.length" :data="compareData" :type="form.compareType"/>
    </div>
    <div v-if="activeTab==='ranking'">
      <SalesProductTable :filters="form"/>
    </div>
    <div v-if="activeTab==='shouka'">
      <SalesDigestTable :filters="form" :fetchTrigger="shoukaTrigger"/>
    </div>
    <div v-if="activeTab==='zaiko'">
      <StockTable :filters="form" :fetchTrigger="zaikoTrigger"/>
    </div>

    </div></div></div></div>
    </AuthenticatedLayout>
    </template>
