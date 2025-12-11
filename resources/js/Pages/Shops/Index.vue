<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head ,Link } from '@inertiajs/vue3';
    import FlashMessage from '@/Components/FlashMessage.vue';
    import Pagination from '@/Components/Pagination.vue';
    import { Inertia } from '@inertiajs/inertia';
    import { ref } from 'vue';

    defineProps({
        shops: Object,
        areas:Array,
        companies:Array,
    });

    const search = ref('')
    const area_id = ref('')
    const company_id = ref('')
    const is_selling = ref('')
    // ref の値を取得するには .valueが必要
    const searchShops = () => {
        Inertia.get(route('shops.index', { search: search.value ,area_id: area_id.value, company_id: company_id.value, is_selling:is_selling.value}), { preserveState: true, preserveScroll: true });
    }

    const resetFilters = () => {
        search.value = '';
        area_id.value = '';
        company_id.value = '';
        is_selling.value = '';
        Inertia.get(route('shops.index'), { preserveState: true, preserveScroll: true });
    }


</script>

<template>
    <Head title="Shop一覧" />

    <AuthenticatedLayout>
        <template #header>

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Shop一覧</h2>

            <div class="flex">

                <div class="ml-2 md:ml-24 mt-4">
                    <Link as="button" :href="route('menu')" class="w-40 h-10 bg-indigo-500 text-sm text-white ml-0 hover:bg-indigo-600 rounded">Menu</Link>
                </div>
                <div class="ml-2 md:ml-24 mt-4">
                    <Link as="button" :href="route('shops.create')" class="w-40 h-10 bg-green-500 text-sm text-white ml-0 hover:bg-green-600 rounded">Shop登録</Link>
                </div>
            </div>
        </template>

        <div class="py-4">

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <FlashMessage/>
                    <div class="p-2 text-gray-900">

                        <div class="md:flex md:ml-12 mb-2">
                            <div class="flex">
                            <div class="p-2 relative mt-0 ">
                                <!-- <label for="role_id" class="leading-7 text-sm text-gray-600">Role</label> -->
                                <select id="area_id" name="area_id" v-model="area_id" class="h-8 w-32 rounded border border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                    <option value="" selected>地域選択</option> <!-- 変更: 選択なしのオプションを追加 -->
                                    <option v-for="area in areas" :key="area.id" :value="area.id">{{ area.area_name }}</option>
                                </select>
                            </div>

                            <div class="p-2 relative mt-0 ">
                                <!-- <label for="role_id" class="leading-7 text-sm text-gray-600">Role</label> -->
                                <select id="company_id" name="company_id" v-model="company_id" class="h-8 w-32 rounded border border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                    <option value="" selected>取引先選択</option> <!-- 変更: 選択なしのオプションを追加 -->
                                    <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.co_name }}</option>
                                </select>
                            </div>
                            </div>

                            <div class="flex ml-2 h-8 mr-2 mt-2 mb-4">
                                <!-- <label class="mr-2" for="is_selling">展開:</label> -->
                                <select id="is_selling" v-model="is_selling" class="h-8 w-32 rounded border border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                    <option value="">展開状態</option>
                                    <option value="1">展開中</option>
                                    <option value="0">休業</option>
                                </select>
                            </div>

                            <div class="flex ml-2 h-8 mr-8 mt-2 mb-4">

                                <input class="h-8 w-60 rounded" type="text" name="search" v-model="search" placeholder="ワード検索">
                                <button class="ml-2 bg-blue-300 text-white px-2 w-20 h-8 rounded "
                                @click="searchShops">検索</button>
                                <button class="ml-2 bg-gray-400 text-white px-2 w-24 h-8 rounded"
                                @click="resetFilters">全表示</button>

                            </div>

                        </div>

                        <div class=" mx-auto w-full sm:px-4 lg:px-4 border ">

                        <table class="bg-white table-auto w-full text-center whitespace-no-wrap">
                            <thead>
                                <tr>
                                    <th class="w-1/15 md:1/15 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">id</th>
                                    <th class="w-2/15 md:2/15 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 ">area</th>
                                    <th class="w-2/15 md:2/15 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">取引先名</th>
                                    <th class="w-2/15 md:2/15 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">店名</th>
                                    <th class="w-3/15 md:3/15 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100  hidden sm:table-cell ">info</th>
                                    <th class="w-1/15 md:1/15 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 hidden sm:table-cell ">状態</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="shop in shops.data" :key="shop.shop_id">
                                    <td class="border-b-2 boder-gray-200">
                                        <Link class="text-indigo-500" :href="route('shops.show',{shop:shop.shop_id})">{{ shop.shop_id }} </Link>
                                    </td>
                                    <td class="border-b-2 boder-gray-200 ">{{ shop.area_name }} </td>
                                    <td class="border-b-2 boder-gray-200">{{ shop.co_name }} </td>
                                    <td class="border-b-2 boder-gray-200">{{ shop.shop_name }} </td>
                                    <td class="border-b-2 boder-gray-200 text-left hidden sm:table-cell ">{{ shop.shop_info ? shop.shop_info.substring(0, 15) : '' }} </td>
                                    <td class="border-b-2 boder-gray-200 hidden sm:table-cell ">
                                        <span v-if="shop.is_selling == 1 ">展開中</span>
                                        <span v-if="shop.is_selling == 0 ">休止</span>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                        <Pagination :links="shops.links" class="mt-4" ></Pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
