
<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link } from '@inertiajs/vue3';
    import { reactive, onMounted, computed } from 'vue';
    import axios from 'axios';
    import CompareTable from '@/Components/CompareTable.vue';
    import { getToday } from '@/common';

    const form = reactive({
        startDate: null,
        endDate: null,
        type: 'pm',
        company_id: '',
        shop_id: '',
        pic_id: '',
        brand_id: '',
        season_id: '',
        unit_id: '',
        face: '',
        designer_id: '',
    });

    const data = reactive({
        companies: [],
        shops: [],
        rows: [],
    });

    // 表示制御
    const showFilters = computed(() => ['pm','pw','py','co_total','sh_total','pic_total','bd_total','ss_total','un_total','fa_total','de_total'].includes(form.type));
    const showFiltersCompany = computed(() => ['pm','pw','py','co_total','sh_total'].includes(form.type));
    const showFiltersShop = computed(() => ['pm','pw','sh_total'].includes(form.type));

    onMounted(() => {
        const today = getToday();
        form.startDate = today;
        form.endDate = today;
        getData();
    });

    const getData = async () => {
        try {
            const res = await axios.get('/api/sales-comparison', { params: form });
            data.rows = res.data.rows;
            data.companies = res.data.companies;
            data.shops = res.data.shops;
        } catch (e) { console.error(e.message); }
    };

    const clearFilters = async () => {
        form.company_id = '';
        form.shop_id = '';
        form.pic_id = '';
        form.brand_id = '';
        form.season_id = '';
        form.unit_id = '';
        form.face = '';
        form.designer_id = '';
        await getData();
    };
    </script>

    <template>
    <Head title="前年対比" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">前年対比</h2>
        </template>

        <div class="p-4">
            <form @submit.prevent="getData" class="mb-4">
                <div class="flex gap-2 flex-wrap">
                    <input type="date" v-model="form.startDate" class="border rounded px-2 py-1" />
                    <span>〜</span>
                    <input type="date" v-model="form.endDate" class="border rounded px-2 py-1" />
                    <select v-model="form.type" class="border rounded px-2 py-1">
                        <option value="pm">月別</option>
                        <option value="pw">週別</option>
                        <option value="py">年別</option>
                        <option value="co_total">社累計</option>
                        <option value="sh_total">店累計</option>
                        <option value="pic_total">担当者累計</option>
                        <option value="bd_total">ブランド累計</option>
                        <option value="ss_total">シーズン累計</option>
                        <option value="un_total">ユニット累計</option>
                        <option value="fa_total">フェイス累計</option>
                        <option value="de_total">デザイナー累計</option>
                    </select>

                    <button type="submit" class="bg-blue-500 text-white px-4 py-1 rounded">検索</button>
                    <button @click="clearFilters" type="button" class="bg-gray-500 text-white px-4 py-1 rounded">クリア</button>
                </div>

                <div class="flex gap-2 mt-2 flex-wrap">
                    <div v-if="showFiltersCompany">
                        <select v-model="form.company_id" class="border rounded px-2 py-1">
                            <option value="">会社選択なし</option>
                            <option v-for="c in data.companies" :key="c.id" :value="c.id">{{ c.co_name }}</option>
                        </select>
                    </div>

                    <div v-if="showFiltersShop">
                        <select v-model="form.shop_id" class="border rounded px-2 py-1">
                            <option value="">店舗選択なし</option>
                            <option v-for="s in data.shops" :key="s.id" :value="s.id">{{ s.shop_name }}</option>
                        </select>
                    </div>
                </div>
            </form>

            <CompareTable :data="{ rows: data.rows }" :type="form.type" />

        </div>
    </AuthenticatedLayout>
    </template>
