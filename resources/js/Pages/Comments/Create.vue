<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head ,Link} from '@inertiajs/vue3';
    import { reactive } from 'vue';
    import { Inertia } from '@inertiajs/inertia';
    import { useForm ,usePage } from '@inertiajs/vue3'


    const props = defineProps({
        errors: Object,
        report_id: Number,
    });


    const page = usePage();

    const form = useForm({
        report_id: props.report_id,
        comment: page.props.old?.comment ?? null,
    });

    const storeComment = () => {
        Inertia.post('/comments', form)
    }

    // 戻るボタンの処理
    const goBack = () => {
        window.history.back();
    };


</script>

<template>
    <Head title="コメント登録" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">コメント登録</h2>
            <div class="mt-4">
                <button
                    type="button"
                    @click="goBack"
                    class="w-32 h-8 ml-24 text-gray-700 bg-gray-200 border border-gray-300 focus:outline-none hover:bg-gray-300 rounded text-ml">
                    戻る
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <section class="text-gray-600 body-font relative">

                            <form @submit.prevent="storeComment">
                            <div class="container px-5 py-8 mx-auto">
                                <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                <div class="flex flex-wrap -m-2">


                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="report_id" class="leading-7 text-sm text-gray-600">Report_ID</label>
                                            <input disabled id="report_id" name="report_id" v-model="form.report_id" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.report_id" class="text-red-500">{{ errors.report_id }}</div>
                                        </div>
                                    </div>

                                    <div class="p-2 w-full">
                                    <div class="relative">
                                        <label for="comment" class="leading-7 text-sm text-gray-600">    コメント</label>
                                        <textarea id="comment" name="comment"  v-model="form.comment" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out"></textarea>
                                        <div v-if="errors.comment" class="text-red-500">{{ errors.comment }}</div>
                                    </div>
                                    </div>




                                    <div class="p-2 w-full">
                                        <button class="w-32 h-8 flex mx-auto text-white bg-pink-500 border-0 py-2 pl-12 focus:outline-none hover:bg-pink-600 rounded text-sm"> 登録</button>
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
