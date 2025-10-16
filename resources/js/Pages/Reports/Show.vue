<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head ,Link} from '@inertiajs/vue3';
    import {nl2br} from '@/common'
    import { Inertia } from '@inertiajs/inertia';
    import FlashMessage from '@/Components/FlashMessage.vue'

    defineProps({
        report : Object,
        comments: Array,
        login_user: Object,
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

                <div class="mt-4">
                    <button
                        type="button"
                        @click="goBack"
                        class="w-32 h-8 ml-24 text-gray-700 bg-gray-200 border border-gray-300 focus:outline-none hover:bg-gray-300 rounded text-ml">
                        戻る
                    </button>
                </div>
            </template>

            <div class="py-3">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <FlashMessage />
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-3 text-gray-900">
                            <section class="text-gray-600 body-font relative">

                                <div class="ml-24 mb-0">
                                    <Link as="button" :href="route('comments.create2',{id:report.id})" class="w-32 h-8 bg-indigo-500 text-sm text-white ml-0 hover:bg-indigo-600 rounded">コメント登録</Link>
                                </div>

                                <div class="container px-5 py-2 mx-auto">
                                    <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                    <div class="flex flex-wrap -m-2">
                                        <div class="flex p-2 w-full">

                                        <div class="relative">
                                            <label for="id" class="leading-7 text-sm text-gray-600">Report_ID</label>
                                            <div id="id" name="id" class="w-24 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ report.id }}</div>

                                        </div>

                                        <div class="ml-2 relative">
                                            <label for="detail_id" class="leading-7 text-sm text-gray-600">Detail_ID</label>
                                            <div id="detail_id" name="detail_id" class="w-24 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ report.detail_id }}</div>

                                        </div>
                                        <div class="ml-2 relative">
                                            <label for="title" class="leading-7 text-sm text-gray-600">Title</label>
                                            <div id="title" name="title" class="w-60 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ report.title }}</div>

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
                                    <div v-if="report.staff_id == login_user" class="mt-4 mb-2 flex">

                                        <div class="p-2 w-full">
                                            <Link as="button" :href="route('reports.edit',{report:report.id})" class="w-32 h-8 flex mx-auto text-white bg-green-500 border-0 py-2 pl-7 focus:outline-none hover:bg-green-600 rounded text-sm">Report編集</Link>
                                        </div>
                                        <div class="p-2 w-full">
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
                            <th class="w-2/12 md:1/13 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">Date</th>
                            <th class="w-2/12 md:1/13 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">投稿者</th>
                            <th class="w-2/12 md:3/13 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">comment</th>


                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="comment in comments" :key="report.id">
                            <td class="border-b-2 boder-gray-200">
                                <Link class="text-indigo-500" :href="route('comments.show',{comment:comment.id})">{{ comment.created_at  }} </Link>
                            </td>
                            <!-- <td class="border-b-2 boder-gray-200">{{ report.report_id  }} </td> -->
                            <td class="border-b-2 boder-gray-200">{{ comment.name  }} </td>
                            <td class="w-3/12 border-b-2 boder-gray-200">{{ comment.comment.substring(0, 20) }} </td>

                        </tr>
                    </tbody>

                </table>


                </div>
        </AuthenticatedLayout>
    </template>
