<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head ,Link} from '@inertiajs/vue3';
    import { reactive } from 'vue';
    import { Inertia } from '@inertiajs/inertia';
    import { useForm ,usePage } from '@inertiajs/vue3'


    const props = defineProps({
        errors: Object,
        detail : Object,
        id: Number,
    });


    const page = usePage();

    const form = useForm({
        detail_id: props.id,
        //title: page.title?.title ?? null,
        //report: page.title?.report ?? null,
        title: page.props.old?.title ?? null,
        report: page.props.old?.report ?? null,
        image1: null,
        image2: null,
        image3: null,
        image4: null,

    });



    const handleFileUpload_1 = (event) => {
        form.image1 = event.target.files[0];
    }

    const handleFileUpload_2 = (event) => {
        form.image2 = event.target.files[0];
    }

    const handleFileUpload_3 = (event) => {
        form.image3 = event.target.files[0];
    }

    const handleFileUpload_4 = (event) => {
        form.image4 = event.target.files[0];
    }

    const storeReport = () => {
        Inertia.post('/reports', form)
    }

    // 戻るボタンの処理
    const goBack = () => {
        window.history.back();
    };


</script>

<template>
    <Head title="Report登録" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Report登録</h2>

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

                            <form @submit.prevent="storeReport" enctype="multipart/form-data">
                            <div class="container px-5 py-2 mx-auto">
                                <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                <div class="flex flex-wrap -m-2">


                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="detail_id" class="leading-7 text-sm text-gray-600">Detail_ID</label>
                                            <input disabled id="detail_id" name="detail_id" v-model="form.detail_id" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.detail_id" class="text-red-500">{{ errors.detail_id }}</div>
                                        </div>
                                        <div class="relative">
                                            <label for="title" class="leading-7 text-sm text-gray-600">Title</label>
                                            <input type="text" id="title" name="title" v-model="form.title" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.title" class="text-red-500">{{ errors.title }}</div>
                                        </div>
                                    </div>

                                    <div class="ml-2 p-0 w-full">
                                    <div class="relative">
                                        <label for="report" class="leading-7 text-sm text-gray-600">Report</label>
                                        <textarea id="report" name="report"  v-model="form.report" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out"></textarea>
                                        <div v-if="errors.report" class="text-red-500">{{ errors.report }}</div>
                                    </div>
                                    </div>
                                    <div class="ml-2 w-full">
                                        <div class="relative">
                                            <label for="image1" class="leading-7 text-sm mt-2 text-gray-800 dark:text-gray-200 ">画像1</label>
                                            <input type="file" id="image2" name="image1" accept="image/png,image/jpeg,image/jpg" @change="handleFileUpload_1" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.image1" class="text-red-500">{{ errors.image1 }}</div>
                                        </div>
                                    </div>
                                    <div class="ml-2 w-full">
                                        <div class="relative">
                                            <label for="image2" class="leading-7 text-sm mt-2 text-gray-800 dark:text-gray-200 ">画像2</label>
                                            <input type="file" id="image2" name="image2" accept="image/png,image/jpeg,image/jpg" @change="handleFileUpload_2" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.image2" class="text-red-500">{{ errors.image2 }}</div>
                                        </div>
                                    </div>
                                    <div class="ml-2 w-full">
                                        <div class="relative">
                                            <label for="image3" class="leading-7 text-sm mt-2 text-gray-800 dark:text-gray-200 ">画像3</label>
                                            <input type="file" id="image3" name="image3" accept="image/png,image/jpeg,image/jpg" @change="handleFileUpload_3" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.image3" class="text-red-500">{{ errors.image3 }}</div>
                                        </div>
                                    </div>
                                    <div class="ml-2 w-full">
                                        <div class="relative">
                                            <label for="image4" class="leading-7 text-sm mt-2 text-gray-800 dark:text-gray-200 ">画像4</label>
                                            <input type="file" id="image4" name="image4" accept="image/png,image/jpeg,image/jpg" @change="handleFileUpload_4" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.image4" class="text-red-500">{{ errors.image4 }}</div>
                                        </div>
                                    </div>




                                    <div class="mt-2 p-2 w-full">
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
