<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head ,Link} from '@inertiajs/vue3';

import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    shop: Object
})

const deleteShop = (id) => {
    Inertia.delete(route('shops.destroy', { shop: id }), {
        onBefore: () => confirm('本当に削除しますか？')
    })
}

// 戻るボタンの処理
const goBack = () => {
    window.history.back();
};

</script>

<template>
    <Head title="shop詳細" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Shop詳細</h2>
            <div class="flex mt-4">
                <div class="">
                    <button
                        type="button"
                        @click="goBack"
                        class="w-32 h-8 ml-4 md:ml-24 text-gray-700 bg-gray-200 border border-gray-300 focus:outline-none hover:bg-gray-300 rounded text-ml">
                        戻る
                    </button>
                </div>
                <div class="ml-4 md:ml-24 mb-0">
                    <Link as="button" :href="route('shops.index')" class="w-32 h-8 bg-indigo-500 text-sm text-white ml-0 hover:bg-indigo-600 rounded">shop一覧</Link>
                </div>
            </div>
        </template>

        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-2 text-gray-900">
                        <section class="text-gray-600 body-font relative">


                            <div class="container px-5 py-2 mx-auto">
                                <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                <div class="flex flex-wrap -m-2">

                                    <div class="flex ml-2 w-full">
                                        <div class="relative">
                                            <label for="id" class="leading-7 text-sm text-gray-600">ShopID</label>
                                            <div type="text" id="shop_id" name="shop_id"  class="w-40 bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ shop.id }}</div>

                                        </div>
                                        <div class="p-0 ml-2 relative">
                                            <label for="area_id" class="w-32 leading-7 text-sm text-gray-600">Area</label>
                                            <div type="area_id" id="area_id" name="area_id" class="w-40 bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ shop.area_name }}</div>

                                        </div>

                                    </div>
                                    <div class="md:flex ml-2 w-full">


                                        <div class="p-0 w-40 ml-0 relative">
                                            <label for="company_id" class="leading-7 text-sm text-gray-600">取引先</label>
                                            <div type="company_id" id="company_id" name="company_id" class="w-40 bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ shop.co_name }}</div>
                                        </div>

                                        <div class="relative md:ml-2">
                                            <label for="shop_name" class="leading-7 text-sm text-gray-600">店名</label>
                                            <div type="text" id="shop_name" name="shop_name"  class="w-60 bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ shop.shop_name }}</div>

                                        </div>

                                        <div class="relative md:ml-2">
                                            <label for="is_selling" class="leading-7 text-sm text-gray-600">展開状態</label>
                                            <div v-if="shop.is_selling == 1" class="w-20 bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">展開中</div>
                                            <div v-else class="w-20 bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">休業</div>
                                        </div>

                                    </div>

                                    <div class="ml-2 w-full">
                                    <div class="relative">
                                        <label for="shop_info" class="leading-7 text-sm text-gray-600">詳細</label>
                                        <textarea id="shop_info" name="shop_info" :value="shop.shop_info" readonly class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out"></textarea>

                                    </div>
                                    </div>



                                </div>


                                <div class="flex mt-8">
                                    <div class="ml-2 w-full">
                                        <Link as="button" :href="route('shops.edit',{shop:shop.id})" class="w-32 h-8 flex mx-auto text-white bg-green-500 border-0 py-2 pl-12 focus:outline-none hover:bg-green-600 rounded text-sm">編集</Link>
                                    </div>
                                    <div class="ml-2 w-full">
                                        <button class="w-32 h-8 flex mx-auto text-white bg-red-500 border-0 py-2 pl-8 focus:outline-none hover:bg-red-600 rounded text-sm" @click="deleteShop(shop.id)" >削除する</button>
                                    </div>
                                </div>

                                </div>
                            </div>


                        </section>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
