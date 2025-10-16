<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head ,Link} from '@inertiajs/vue3';
import { reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { useForm,usePage } from '@inertiajs/vue3'
// import { Link } from '@inertiajs/inertia-vue3';
import { onMounted } from 'vue';


const props = defineProps({
    comment: Object,
    errors : Object,
})

const form = useForm({
    id: props.comment.id,
    report_id: props.comment.report_id,
    comment: props.comment.comment,

});

const updateComment = () => {
    form.put(route('comments.update', { comment: form.id }));
};

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
    <Head title="comment編集" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">comment編集</h2>
            <div class="mt-4">
                <button
                    type="button"
                    @click="goBack"
                    class="w-32 h-8 ml-24 text-gray-700 bg-gray-200 border border-gray-300 focus:outline-none hover:bg-gray-300 rounded text-ml">
                    戻る
                </button>
            </div>
        </template>

        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-2 text-gray-900">
                        <section class="text-gray-600 body-font relative">

                            <form @submit.prevent="updateComment(form.id)" >
                            <div class="container px-5 py-2 mx-auto">
                                <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                <div class="flex flex-wrap -m-2">


                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="id" class="leading-7 text-sm text-gray-600">ID</label>
                                            <input readonly type="text" id="id" name="id" v-model="form.id" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.id" class="text-red-500">{{ errors.id }}</div>
                                        </div>
                                        <div class="relative">
                                            <label for="report_id" class="leading-7 text-sm text-gray-600">Report_ID</label>
                                            <input readonly type="text" id="report_id" name="report_id" v-model="form.report_id" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.report_id" class="text-red-500">{{ errors.report_id }}</div>
                                        </div>
                                    </div>

                                    <div class="p-2 w-full">
                                    <div class="relative">
                                        <label for="comment" class="leading-7 text-sm text-gray-600">comment</label>
                                        <textarea id="comment" name="comment"  v-model="form.comment" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out"></textarea>
                                        <div v-if="errors.comment" class="text-red-500">{{ errors.comment }}</div>
                                    </div>
                                    </div>


                                    <div class="p-2 w-full">
                                        <button class="w-32 h-8 flex mx-auto text-white bg-pink-500 border-0 py-2 pl-12 focus:outline-none hover:bg-pink-600 rounded text-sm"> 更新</button>
                                    </div>

                                </div>
                                </div>
                            </div>
                            </form>

                        </section>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
