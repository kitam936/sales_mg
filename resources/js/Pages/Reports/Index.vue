<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head ,Link } from '@inertiajs/vue3';
    import FlashMessage from '@/Components/FlashMessage.vue';
    import { Inertia } from '@inertiajs/inertia';
    import { ref } from 'vue';
    import { nl2br } from '@/common';


    const props = defineProps({
        detail: Object,
        reports: Array,
        id: Number,
    });
    // 戻るボタンの処理
    const goBack = () => {
        window.history.back();
    };


</script>

<template>
    <Head title="Report一覧" />

    <AuthenticatedLayout>
        <template #header>

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">作業詳細/Report一覧</h2>

            <div class="flex mt-4">
                <div class="ml-4">
                    <button
                        type="button"
                        @click="goBack"
                        class="w-32 h-8 ml-2 md:ml-24 text-gray-700 bg-gray-200 border border-gray-300 focus:outline-none hover:bg-gray-300 rounded text-ml">
                        戻る
                    </button>
                </div>
                <div class="ml-4 md:ml-24 mb-0">
                    <Link as="button" :href="route('orders.index')" class="w-32 h-8 bg-indigo-500 text-sm text-white ml-0 hover:bg-indigo-600 rounded">Order一覧</Link>
                </div>
                </div>
        </template>

        <div class="py-6">

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <FlashMessage/>
                    <div class="p-6 text-gray-900">
                        <div class="flex">
                        <div class="ml-4 md:ml-24 mb-8">
                            <Link as="button" :href="route('reports.create2',{id:props.detail.id})" class="w-32 h-8 bg-indigo-500 text-sm text-white ml-0 hover:bg-indigo-600 rounded">Report登録</Link>
                        </div>

                        <div class="ml-4 md:ml-24 mb-8">
                            <Link as="button" :href="route('order_detail.edit',{id:props.detail.id})" class="w-32 h-8 bg-green-500 text-sm text-white ml-0 hover:bg-green-600 rounded">作業詳細編集</Link>
                        </div>
                        </div>

                        <div class="p-2 w-full">
                            <div class="flex">
                            <div class="relative">
                                <label for="detail_id" class="leading-7 text-sm text-gray-600">Detail_ID</label>
                                <div id="detail_id" name="detail_id" class="w-24 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ props.detail.id }}</div>

                            </div>
                            <div class="ml-2 relative">
                                <label for="detail_status" class="leading-7 text-sm text-gray-600">Status</label>
                                <div type="text" id="detail_status" name="detail_status"  class="w-32 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ props.detail.detail_status_name }}</div>

                            </div>
                            </div>
                            <div class="relative">
                                <label for="detail_info" class="leading-7 text-sm text-gray-600">詳細</label>
                                <div id="detail_info" name="detail_info" v-html="nl2br(detail.detail_info)" class="w-full h-12 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out">
                                </div>
                            </div>
                        </div>
                        <div class=" mx-auto w-full sm:px-4 lg:px-4 border ">

                        <table class="bg-white table-auto w-full text-center whitespace-no-wrap">
                            <thead>
                                <tr>
                                    <th class="w-2/12 md:1/13 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">Date</th>
                                    <th class="w-2/12 md:1/13 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">投稿者</th>
                                    <th class="w-2/12 md:1/13 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">Title</th>
                                    <th class="w-3/12 md:1/13 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">report</th>
                                    <th class="w-1/12 md:3/13 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">ｺﾒﾝﾄ</th>


                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="report in reports" :key="report.id">
                                    <td class="border-b-2 border-gray-200">
                                        <Link
                                          class="text-indigo-500"
                                          :href="route('reports.show2',{report:report.report_id})"
                                        >
                                          {{
                                            (() => {
                                              const d = new Date(report.created_at);
                                              const yy = String(d.getFullYear()).slice(-2); // 西暦下2桁
                                              const mm = d.getMonth() + 1;
                                              const dd = d.getDate();
                                              const hh = String(d.getHours()).padStart(2, '0');
                                              const mi = String(d.getMinutes()).padStart(2, '0');
                                              return `${yy}/${mm}/${dd} ${hh}:${mi}`;
                                            })()
                                          }}
                                        </Link>
                                      </td>
                                    <!-- <td class="border-b-2 boder-gray-200">{{ report.report_id  }} </td> -->
                                    <td class="border-b-2 boder-gray-200">{{ report.staff_name  }} </td>
                                    <td class="border-b-2 boder-gray-200">{{ report.title  }} </td>
                                    <td class="w-3/12 border-b-2 boder-gray-200">{{ report.report.substring(0, 15) }} </td>
                                    <td class="w-3/12 border-b-2 boder-gray-200">{{ report.comment_cnt }} </td>

                                </tr>
                            </tbody>

                        </table>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
