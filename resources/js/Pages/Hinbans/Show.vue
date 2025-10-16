<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head ,Link} from '@inertiajs/vue3';
import {nl2br} from '@/common'
import { Inertia } from '@inertiajs/inertia';

defineProps({
    item : Object
})

const deleteItem = id => {
    Inertia.delete(route('items.destroy',{item:id}),{
        onBefore:() => confirm('本当に削除しますか？')
    })
}
// 戻るボタンの処理
const goBack = () => {
    window.history.back();
};

</script>

<template>
    <Head title="商品詳細" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">商品詳細</h2>
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
                    <Link as="button" :href="route('items.index')" class="w-32 h-8 bg-indigo-500 text-sm text-white ml-0 hover:bg-indigo-600 rounded">商品一覧</Link>
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
                                    <div class="ml-2 w-full">

                                    <div class="relative">
                                        <label for="id" class="leading-7 text-sm text-gray-600">ID</label>
                                        <div id="id" name="id" class="w-full bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ item.id }}</div>

                                    </div>

                                    <div class="relative">
                                        <label for="prod_code" class="leading-7 text-sm text-gray-600">商品番号</label>
                                        <div id="prod_code" name="prod_code" class="w-full bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ item.prod_code }}</div>

                                    </div>

                                    <div class="relative">
                                        <label for="car_category_id" class="leading-7 text-sm text-gray-600">車種</label>
                                        <div id="car_category_id" name="car_category_id" class="w-full bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ item.car_name }}</div>

                                    </div>

                                    <div class="relative">
                                        <label for="item_name" class="leading-7 text-sm text-gray-600">商品名</label>
                                        <div id="item_name" name="item_name" class="w-full bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ item.item_name }}</div>

                                    </div>
                                    </div>
                                    <div class="ml-2 w-full">
                                    <div class="relative">
                                        <label for="item_info" class="leading-7 text-sm text-gray-600">詳細</label>
                                        <div id="item_info" name="item_info" v-html="nl2br(item.item_info)" class="w-full bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out">
                                        </div>
                                    </div>
                                    </div>
                                    <div class="ml-2 w-full">
                                    <div class="relative">
                                        <label for="item_price" class="leading-7 text-sm text-gray-600">商品価格</label>
                                        <div id="item_price" name="item_price" class="w-full bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ item.item_price }}</div>
                                    </div>
                                    </div>
                                    <div class="ml-2 w-full">
                                        <div class="relative">
                                            <label for="item_cost" class="leading-7 text-sm text-gray-600">商品原価</label>
                                            <div id="item_cost" name="item_cost" class="w-full bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ item.item_cost }}</div>
                                        </div>
                                    </div>

                                    <div class="ml-2 w-full">
                                        <div class="relative">
                                            <div class="w-full mb-1">
                                                    <div v-if="item.item_image">
                                                            <img :src="`/storage/items/${item.item_image}`" alt="Item Image">
                                                    </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex mt-4">

                                    <div class="ml-2 w-full">
                                        <Link as="button" :href="route('items.edit',{item:item.id})" class="w-32 h-8 flex mx-auto text-white bg-green-500 border-0 py-2 pl-9 focus:outline-none hover:bg-green-600 rounded text-sm">商品編集</Link>
                                    </div>
                                    <div class="ml-12 w-full">
                                        <button class="w-32 h-8 flex mx-auto text-white bg-red-500 border-0 py-2 pl-9 focus:outline-none hover:bg-red-600 rounded text-sm" @click="deleteItem(item.id)" >削除する</button>
                                    </div>
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
