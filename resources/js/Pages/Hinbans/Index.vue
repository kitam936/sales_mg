<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head ,Link } from '@inertiajs/vue3';
    import FlashMessage from '@/Components/FlashMessage.vue';
    import Pagination from '@/Components/Pagination.vue';
    import { Inertia } from '@inertiajs/inertia';
    import { ref } from 'vue';


    defineProps({
        items: Object,
        item_categories: Array,
        car_categories: Array
    });

    const search = ref('')
    const item_category_id = ref('')
    const car_category_id = ref('')
    // ref の値を取得するには .valueが必要
    const searchItems = () => {
        Inertia.get(route('items.index', { search: search.value ,item_category_id: item_category_id.value ,car_category_id: car_category_id.value}))
    }

    // 検索条件をリセットする関数
    const resetFilters = () => {
        search.value = '';
        item_category_id.value = '';
        car_category_id.value = '';
        Inertia.get(route('items.index'), { preserveState: true });
    }

    // 戻るボタンの処理
    const goBack = () => {
        window.history.back();
    };

</script>

<template>
    <Head title="商品一覧" />

    <AuthenticatedLayout>
        <template #header>

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">商品一覧</h2>
            <div class="flex">

                <div class="ml-4 md:ml-24 mt-4">
                    <Link as="button" :href="route('menu')" class="w-32 h-8 bg-indigo-500 text-sm text-white ml-0 hover:bg-indigo-600 rounded">Menu</Link>
                </div>

                <div class="ml-4 md:ml-24 mt-4">
                    <Link as="button" :href="route('items.create')" class="w-32 h-8 bg-green-500 text-sm text-white ml-0 hover:bg-green-600 rounded">商品登録</Link>
                </div>


                </div>
        </template>

        <div class="py-4">

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <FlashMessage/>
                    <div class="p-6 text-gray-900">


                        <div class="md:flex ml-0 md:ml-12 mb-4">
                            <div class="flex">
                            <div class="p-2 relative mt-2 ">
                                <!-- <label for="role_id" class="leading-7 text-sm text-gray-600">Role</label> -->
                                <select id="item_category_id" name="item_category_id" v-model="item_category_id" class="h-8 w-32 rounded border border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                    <option value="" selected>種別選択</option> <!-- 変更: 選択なしのオプションを追加 -->
                                    <option v-for="category in item_categories" :key="category.id" :value="category.id">{{ category.item_category_name }}</option>
                                </select>
                            </div>

                            <div class="p-2 relative mt-2 ">
                                <!-- <label for="role_id" class="leading-7 text-sm text-gray-600">Role</label> -->
                                <select id="car_category_id" name="car_category_id" v-model="car_category_id" class="h-8 w-32 rounded border border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                    <option value="" selected>車種選択</option> <!-- 変更: 選択なしのオプションを追加 -->
                                    <option v-for="car_category in car_categories" :key="car_category.id" :value="car_category.id">{{ car_category.car_name }}</option>
                                </select>
                            </div>
                        </div>

                            <div class="flex ml-2 h-8 mt-2">

                                <input class="h-8 w-60 rounded" type="text" name="search" v-model="search" placeholder="ワード検索">
                                <button class="ml-2 bg-blue-300 text-white w-32 h-8 rounded "
                                @click="searchItems">検索</button>
                                <button class="ml-2 bg-gray-300 text-white w-32 h-8 rounded" @click="resetFilters">クリア</button>
                            </div>

                        </div>
                        <div class=" mx-auto w-full sm:px-4 lg:px-4 border ">

                        <table class="bg-white table-auto w-full text-center whitespace-no-wrap">
                            <thead>
                                <tr>
                                    <th class="w-2/12 md:1/13 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">id</th>
                                    <th class="w-2/12 md:1/13 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 hidden sm:table-cell">種別</th>
                                    <th class="w-2/12 md:1/13 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">車種</th>
                                    <th class="w-2/12 md:1/13 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">品名</th>
                                    <th class="w-2/12 md:3/13 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">price</th>
                                    <th class="w-2/12 md:3/13 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100  hidden sm:table-cell">cost</th>
                                    <th class="w-2/12 md:3/13 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100  hidden sm:table-cell">info</th>

                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="item in items.data" :key="item.id">
                                    <td class="border-b-2 boder-gray-200">
                                        <Link class="text-indigo-500" :href="route('items.show',{item:item.id})">{{ item.id }} </Link>
                                    </td>
                                    <td class="border-b-2 boder-gray-200 hidden sm:table-cell">{{ item.item_category_name  }} </td>
                                    <td class="border-b-2 boder-gray-200">{{ item.car_name  }} </td>
                                    <td class="border-b-2 boder-gray-200">{{ item.item_name  }} </td>
                                    <td class="border-b-2 boder-gray-200 text-right">{{ item.item_price ? item.item_price.toLocaleString() : '' }} </td>
                                    <td class="border-b-2 boder-gray-200 text-right  hidden sm:table-cell">{{ item.item_cost ? item.item_cost.toLocaleString() : '' }} </td>
                                    <td class="w-3/12 border-b-2 boder-gray-200  hidden sm:table-cell">{{ item.item_info.substring(0, 10) || '' }} </td>

                                </tr>
                            </tbody>

                        </table>
                        <Pagination :links="items.links" class="mt-4" ></Pagination>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
