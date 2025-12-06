<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head ,Link } from '@inertiajs/vue3';
    import FlashMessage from '@/Components/FlashMessage.vue';
    import Pagination from '@/Components/Pagination.vue';
    import { Inertia } from '@inertiajs/inertia';
    import { ref } from 'vue';

    defineProps({
        companies: Object,
        pics:Array
    });

    const search = ref('')
    const pic_id = ref('')
    // ref の値を取得するには .valueが必要
    const searchcompanies = () => {
        Inertia.get(route('company.index', { search: search.value ,pic_id: pic_id.value}))
    }

    const resetFilters = () => {
        search.value = '';
        pic_id.value = '';
        Inertia.get(route('company.index'), { preserveState: true, preserveScroll: true });
    }


</script>

<template>
    <Head title="取引先一覧" />

    <AuthenticatedLayout>
        <template #header>

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">取引先一覧</h2>

            <div class="flex">

                <div class="ml-2 md:ml-24 mt-4">
                    <Link as="button" :href="route('menu')" class="w-32 h-8 bg-indigo-500 text-sm text-white ml-0 hover:bg-indigo-600 rounded">Menu</Link>
                </div>
                <div class="ml-4 md:ml-24 mt-4">
                    <Link as="button" :href="route('company.create')" class="w-32 h-8 bg-green-500 text-sm text-white ml-0 hover:bg-green-600 rounded">取引先登録</Link>
                </div>
            </div>
        </template>

        <div class="py-4">

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <FlashMessage/>
                    <div class="p-2 text-gray-900">

                        <div class="md:flex md:ml-12 mb-2">
                            <div class="p-2 relative mt-0 ">
                                <!-- <label for="role_id" class="leading-7 text-sm text-gray-600">Role</label> -->
                                <select id="pic_id" name="pic_id" v-model="pic_id" class="h-8 w-32 rounded border border focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-0 px-1 leading-8 transition-colors duration-200 ease-in-out">
                                    <option value="" selected>担当者選択</option> <!-- 変更: 選択なしのオプションを追加 -->
                                    <option v-for="pic in pics" :key="pic.id" :value="pic.id">{{ pic.name }}</option>
                                </select>
                            </div>

                            <div class="flex ml-2 h-8 mr-8 mt-2 mb-4">

                                <input class="h-8 w-60 rounded" type="text" name="search" v-model="search" placeholder="ワード検索">
                                <button class="ml-2 bg-blue-300 text-white px-2 w-20 h-8 rounded "
                                @click="searchcompanies">検索</button>
                                <button class="ml-2 bg-gray-300 text-white px-2 w-24 h-8 rounded"
                                @click="resetFilters">クリア</button>

                            </div>

                        </div>

                        <div class=" mx-auto w-full sm:px-4 lg:px-4 border ">

                        <table class="bg-white table-auto w-full text-center whitespace-no-wrap">
                            <thead>
                                <tr>
                                    <th class="w-1/15 md:1/15 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">id</th>
                                    <th class="w-2/15 md:2/15 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">取引先名</th>
                                    <th class="w-3/15 md:3/15 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">info</th>
                                    <th class="w-2/15 md:2/15 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 ">担当者</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="company in companies.data" :key="company.company_id">
                                    <td class="border-b-2 boder-gray-200">
                                        <Link class="text-indigo-500" :href="route('company.show',{company:company.company_id})">{{ company.company_id }} </Link>
                                    </td>
                                    <td class="border-b-2 boder-gray-200 ">{{ company.co_name }} </td>
                                    <td class="border-b-2 boder-gray-200 text-left">{{ company.co_info ? company.co_info.substring(0, 15) : '' }} </td>
                                    <td class="border-b-2 boder-gray-200">{{ company.pic_name }} </td>
                                </tr>

                            </tbody>
                        </table>
                        <Pagination :links="companies.links" class="mt-4" ></Pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
