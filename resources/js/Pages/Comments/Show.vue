<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head ,Link} from '@inertiajs/vue3';
import {nl2br} from '@/common'
import { Inertia } from '@inertiajs/inertia';
import FlashMessage from '@/Components/FlashMessage.vue'
defineProps({
    comment: Object,
    login_user: [Object, Number],  // ← 両方許可
})

const deleteComment = id => {
    Inertia.delete(route('comments.destroy',{comment:id}),{
        onBefore:() => confirm('本当に削除しますか？')
    })
}

// 戻るボタンの処理
const goBack = () => {
    window.history.back();
};

</script>

<template>
    <Head title="コメント詳細" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">コメント詳細</h2>
            <div class="mt-4">
                <!-- <button
                    type="button"
                    @click="goBack"
                    class="w-32 h-8 ml-24 text-gray-700 bg-gray-200 border border-gray-300 focus:outline-none hover:bg-gray-300 rounded text-ml">
                    戻る
                </button> -->
                <div class="ml-4 md:ml-24 mt-0">
                    <Link as="button" :href="route('reports.show',{report:comment.report_id})" class="w-32 h-8 bg-indigo-500 text-sm text-white ml-0 hover:bg-indigo-600 rounded">Report詳細</Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-3 text-gray-900">
                        <section class="text-gray-600 body-font relative">
<!--
                            <div class="ml-24 mb-0">
                                <Link as="button" :href="route('comments.create')" class="w-32 h-8 bg-indigo-500 text-sm text-white ml-0 hover:bg-indigo-600 rounded">コメント登録</Link>
                            </div> -->

                            <div class="container px-5 py-8 mx-auto">
                                <FlashMessage/>
                                <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                <div class="flex flex-wrap -m-2">
                                    <div class="flex p-2 w-full">

                                    <div class="relative">
                                        <label for="id" class="leading-7 text-sm text-gray-600">Comment_ID</label>
                                        <div id="id" name="id" class="w-24 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ comment.id }}</div>

                                    </div>


                                    </div>


                                    <div class="p-2 w-full">
                                    <div class="relative">
                                        <label for="comment" class="leading-7 text-sm text-gray-600">コメント</label>
                                        <div id="comment" name="comment" v-html="nl2br(comment.comment)" class="w-full bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out">
                                        </div>
                                    </div>
                                    </div>

                                    <div v-if="comment.user_id == login_user" class="p-2 w-full">
                                    <div class="p-2 w-full">
                                        <Link as="button" :href="route('comments.edit',{comment:comment.id})" class="w-32 h-8 flex mx-auto text-white bg-green-500 border-0 py-2 pl-7 focus:outline-none hover:bg-green-600 rounded text-sm">コメント編集</Link>
                                    </div>
                                    <div class="p-2 w-full">
                                        <button class="w-32 h-8 flex mx-auto text-white bg-red-500 border-0 py-2 pl-9 focus:outline-none hover:bg-red-600 rounded text-sm" @click="deleteComment(comment.id)" >削除する</button>
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
