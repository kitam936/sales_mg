<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head ,Link} from '@inertiajs/vue3';
    import {nl2br} from '@/common'
    import { Inertia } from '@inertiajs/inertia';
    import FlashMessage from '@/Components/FlashMessage.vue'

    defineProps({
        report : Object,
        comment_exist: [Boolean, Object], // 両方許可
        comments: Array,
        login_user: [Object, Number],  // ← 両方許可
    })

    const deleteReport = id => {
        Inertia.delete(route('reports.destroy',{report:id}),{
            onBefore:() => confirm('本当に削除しますか？')
        })
    }

     // 戻るボタンの処理
    const goBack = () => {
        window.history.back();
    };

    </script>

    <template>
        <Head title="Report詳細" />

        <AuthenticatedLayout>
            <template #header>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Report詳細</h2>

                <div class="flex mt-4">
                    <div class="ml-8 md:ml-24 mt-0">
                        <Link as="button" :href="route('reports.index')" class="w-32 h-8 bg-indigo-500 text-sm text-white ml-0 hover:bg-indigo-600 rounded">Report一覧</Link>
                    </div>

                    <div class="ml-8 mb-0">
                        <Link as="button" :href="route('comments.create',{report:report.id})" class="w-32 h-8 bg-green-500 text-sm text-white ml-0 hover:bg-green-600 rounded">コメント登録</Link>
                    </div>
                </div>
            </template>

            <div class="py-3">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <FlashMessage />
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-3 text-gray-900">
                            <section class="text-gray-600 body-font relative">



                                <div class="container px-2 py-2 mx-auto">
                                    <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                    <div class="flex flex-wrap -m-2">
                                        <div class="md:flex p-2 w-full">

                                        <div class="relative">
                                            <label for="id" class="leading-7 text-sm text-gray-600">Report_ID</label>
                                            <div id="id" name="id" class="text-sm w-16 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ report.id }}</div>

                                        </div>
                                        <div class="flex">
                                        <div class="ml-0 md:ml-2 relative">
                                            <label for="co_id" class="leading-7 text-sm text-gray-600">取引先</label>
                                            <div id="co_id" name="co_id" class="text-sm w-24 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ report.co_name }}</div>

                                        </div>
                                        <div class="ml-2 relative">
                                            <label for="shop_id" class="leading-7 text-sm text-gray-600">店名</label>
                                            <div id="shop_id" name="shop_id" class="text-sm w-40 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ report.shop_name }}</div>

                                        </div>
                                        </div>
                                        </div>


                                        <div class="p-0 ml-2  w-full">
                                        <div class="relative">
                                            <label for="report" class="leading-7 text-sm text-gray-600">Report</label>
                                            <div id="report" name="report" v-html="nl2br(report.report)" class="w-full bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out">
                                            </div>
                                        </div>
                                        </div>


                                        <div class=" p-2">
                                            <div class=" relative">
                                                <div v-if="report.image1" class="w-full mb-1">
                                                    <label for="image1" class="leading-7 text-sm mt-2 text-gray-800 dark:text-gray-200 ">画像1</label>
                                                        <div >
                                                                <img :src="`/storage/reports/${report.image1}`" alt="Image1">
                                                        </div>

                                                </div>
                                                <div  v-if="report.image2" class=" w-full mb-1">
                                                    <label for="image2" class="leading-7 text-sm mt-2 text-gray-800 dark:text-gray-200 ">画像2</label>
                                                        <div>
                                                            <img :src="`/storage/reports/${report.image2}`" alt="Image2">
                                                        </div>

                                                </div>
                                                <div v-if="report.image3" class="w-full mb-1">
                                                    <label for="image3" class="leading-7 text-sm mt-2 text-gray-800 dark:text-gray-200 ">画像3</label>
                                                        <div >
                                                            <img :src="`/storage/reports/${report.image3}`" alt="Image3">
                                                        </div>

                                                </div>
                                                <div v-if="report.image4" class="w-full mb-1">
                                                    <label for="image4" class="leading-7 text-sm mt-2 text-gray-800 dark:text-gray-200 ">画像4</label>
                                                        <div >
                                                            <img :src="`/storage/reports/${report.image4}`" alt="Image4">
                                                        </div>

                                                </div>

                                            </div>
                                        </div>
                                    <div v-if="report.user_id == login_user" class="ml-4 mt-4 mb-2 flex">

                                        <div class="p-2 w-full">
                                            <Link as="button" :href="route('reports.edit',{report:report.id})" class="w-32 h-8 flex mx-auto text-white bg-green-500 border-0 py-2 pl-7 focus:outline-none hover:bg-green-600 rounded text-sm">Report編集</Link>
                                        </div>
                                        <div v-if="!(comment_exist) " class="ml-4 p-2 w-full">
                                            <button class="w-32 h-8 flex mx-auto text-white bg-red-500 border-0 py-2 pl-10 focus:outline-none hover:bg-red-600 rounded text-sm" @click="deleteReport(report.id)" >削除する</button>
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


            <div class=" mx-auto w-full sm:px-4 lg:px-4 border ">

                <table class="bg-white table-auto w-full text-center whitespace-no-wrap">
                    <thead>
                        <tr>
                            <th class="py-1 px-2  title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 hidden sm:table-cell">ID</th>
                            <th class="py-1 px-2 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">Date</th>
                            <th class="py-1 px-2 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">投稿者</th>
                            <th class="py-1 px-2 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">comment</th>
                            <th class="py-1 px-2 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">状態</th>

                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="comment in comments" :key="report.id">
                            <td class="py-1 px-2 hidden sm:table-cell">{{ comment.id  }} </td>
                            <td class="py-1 px-2">
                                <Link class="text-indigo-500" :href="route('comments.show',{comment:comment.id})">
                                    {{ new Date(comment.created_at).toLocaleDateString('ja-JP', { year:'2-digit', month:'2-digit', day:'2-digit' }) }}
                                </Link>
                            </td>
                            <td class="py-1 px-2">{{ comment.name  }} </td>
                            <td class="py-1 px-2 ">{{ comment.comment.substring(0, 20) }} </td>
                            <div v-if="comment.comment_reads"  class="py-1 px-2">
                            <td class="text-sm text-center  ">既読</td>
                            </div>
                            <div v-else  class="py-1 px-2">
                             <td class="text-red-600 text-sm text-center  ">未読</td>
                            </div>
                        </tr>
                    </tbody>

                </table>


                </div>
        </AuthenticatedLayout>
    </template>
