<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import { ref ,reactive ,onMounted} from 'vue';
    import { getToday } from '@/common';
    import Chart from '@/Components/Chart.vue';
    import axios from 'axios';
    import ResultTable from '@/Components/ResultTable.vue';
    import { Link } from '@inertiajs/vue3';

    const form = reactive({
        startDate: null,
        endDate: null,
        type: 'perMonth', // 初期値
        customer_id: '',
        car_category_id: '',
    });

    const data = reactive({
        customers: [],
        car_categories: [],
        data: [],
        labels: [],
        totals: [],
        movingAverages: []
    });

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
                    customer_id: form.customer_id,
                    car_category_id: form.car_category_id
                }
            })
            .then(res => {
                data.data = res.data.data;
                data.labels = res.data.labels;
                data.totals = res.data.totals;
                data.movingAverages = res.data.movingAverages ?? []; // ★追加
                data.customers = res.data.customers;
                data.car_categories = res.data.car_categories;
            })
        }catch(e) {
            console.log(e.message)
        }
    }

   // 🔹 検索条件クリア
    const clearFilters = async () => {
        try {
            // form の選択条件をリセット
            form.customer_id = '';
            form.car_category_id = '';

            // 再検索
            await axios.get('/api/analysis', {
                params:{
                    startDate: form.startDate,
                    endDate: form.endDate,
                    type: form.type,
                    customer_id: form.customer_id,
                    car_category_id: form.car_category_id
                }
            })
            .then(res => {
                data.data = res.data.data;
                data.labels = res.data.labels;
                data.totals = res.data.totals;
                // ✅ ここは API から受け取ったまま使う
                data.customers = res.data.customers;
                data.car_categories = res.data.car_categories;
                data.movingAverages = res.data.movingAverages ?? [];
            })
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

                                <label class="mr-2 text-sm">分析タイプ:</label>
                                <div class="mr-2 mt-2">
                                    <div class="flex flex-wrap gap-2">
                                    <label><input type="radio" value="perYear" v-model="form.type" /> 年別</label>
                                    <label><input type="radio" value="perMonth" v-model="form.type" /> 月別</label>
                                    <label><input type="radio" value="perWeek" v-model="form.type" /> 週別</label>
                                    <label><input type="radio" value="perDay" v-model="form.type" /> 日別</label>
                                    </div>
                                </div>



                                <div class=" md:flex mt-2">


                                    <label class="ml-0 md:ml-2 md:mt-0 mr-2 text-sm">絞込検索:</label>
                                    <div class="flex items-center ml-0">
                                    <!-- User選択 -->
                                    <div class="flex">
                                    <div class="relative ">
                                        <select v-model="form.customer_id" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                            <option value="">User選択なし</option>
                                            <option v-for="customer in data.customers" :key="customer.id" :value="customer.id">
                                                {{ customer.car_name }}--{{ customer.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- 車種選択 -->
                                    <div class="relative ml-2">
                                        <select v-model="form.car_category_id" class="h-8 w-36 rounded border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                            <option value="">車種選択なし</option>
                                            <option v-for="car_category in data.car_categories" :key="car_category.id" :value="car_category.id">
                                                {{ car_category.car_name }}
                                            </option>
                                        </select>
                                    </div>
                                    </div>
                                </div>
                                </div>

                                <div class="flex mt-4">
                                    <button type="submit" class="w-32 ml-4 md:ml-16 h-8 px-4 bg-blue-500 text-white rounded">分析</button>
                                    <button @click="clearFilters" class="md:ml-12 ml-4 w-32 h-8 bg-gray-500 text-white px-1 py-1 rounded">絞込条件クリア</button>
                                    </div>
                                <br>
                                <label class="mr-2 text-sm">集計タイプ:</label>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <label class="ml-0"><input type="radio" value="Staff" v-model="form.type" /> STAFF別</label>
                                    <label class="ml-0"><input type="radio" value="CarCategory" v-model="form.type" /> 車種別</label>
                                    <label class="ml-0"><input type="radio" value="decile" v-model="form.type" />デシル分析</label>
                                </div>


                                <div class="flex mt-2 mb-4">
                                <button type="submit" class="w-32 ml-4 md:ml-16 h-8 px-4 bg-blue-500 text-white rounded">分析</button>
                                <!-- <button @click="clearFilters" class="md:ml-12 ml-4 w-32 h-8 bg-gray-500 text-white px-3 py-1 rounded">絞込条件クリア</button> -->
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
