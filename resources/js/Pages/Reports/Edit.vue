<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head ,Link} from '@inertiajs/vue3';
import { reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { useForm,usePage } from '@inertiajs/vue3'
// import { Link } from '@inertiajs/inertia-vue3';
import { onMounted } from 'vue';


const props = defineProps({
    report: Object,
    errors : Object,
})

const form = useForm({
    id: props.report.id,
    shop_id: props.report.shop_id,
    report: props.report.report,
    image1: null,
    image2: null,
    image3: null,
    image4: null,
});



// ファイルアップロード用ハンドラー
const handleFileUpload1 = (event) => {
    form.image1 = event.target.files[0];
};
const handleFileUpload2 = (event) => {
    form.image2 = event.target.files[0];
};
const handleFileUpload3 = (event) => {
    form.image3 = event.target.files[0];
};
const handleFileUpload4 = (event) => {
    form.image4 = event.target.files[0];
};

const updateReport = (id) => {
    const formData = new FormData();
    formData.append('_method', 'PUT'); // ← 重要
    formData.append('id', form.id);
    formData.append('shop_id', form.shop_id);
    formData.append('report', form.report);

    if (form.image1 instanceof File) {
        formData.append('image1', form.image1);
    }
    if (form.image2 instanceof File) {
        formData.append('image2', form.image2);
    }
    if (form.image3 instanceof File) {
        formData.append('image3', form.image3);
    }
    if (form.image4 instanceof File) {
        formData.append('image4', form.image4);
    }

    Inertia.post(route('reports.update', { report: id }), formData, {
        forceFormData: true,
    });
};

const deleteReport = id => {
    Inertia.delete(route('reports.destroy',{report:id}),{
        onBefore:() => confirm('本当に削除しますか？')
    })
}

const del_image1 = id => {
    Inertia.delete(route('reports.del_image1',{report:id}),{
        onBefore:() => confirm('本当に削除しますか？')
    })
}
const del_image2 = id => {
    Inertia.delete(route('reports.del_image2',{report:id}),{
        onBefore:() => confirm('本当に削除しますか？')
    })
}
const del_image3 = id => {
    Inertia.delete(route('reports.del_image3',{report:id}),{
        onBefore:() => confirm('本当に削除しますか？')
    })
}
const del_image4 = id => {
    Inertia.delete(route('reports.del_image4',{report:id}),{
        onBefore:() => confirm('本当に削除しますか？')
    })
}

 // 戻るボタンの処理
 const goBack = () => {
    window.history.back();
};

</script>

<template>
    <Head title="Report編集" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Report編集</h2>
            <div class="mt-4">
                <button
                    type="button"
                    @click="goBack"
                    class="w-32 h-8 ml-24 text-white bg-indigo-500 border border-gray-300 focus:outline-none hover:bg-indigo-600 rounded text-ml">
                    戻る
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <section class="text-gray-600 body-font relative">

                            <form @submit.prevent="updateReport(form.id)" enctype="multipart/form-data">
                            <div class="container px-5 py-8 mx-auto">
                                <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                <div class="flex flex-wrap -m-2">


                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="id" class="leading-7 text-sm text-gray-600">ID</label>
                                            <input disabled type="text" id="id" name="id" v-model="form.id" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.id" class="text-red-500">{{ errors.id }}</div>
                                        </div>
                                        <div class="relative">
                                            <label for="shop_id" class="leading-7 text-sm text-gray-600">店ID</label>
                                            <input disabled type="text" id="shop_id" name="shop_id" v-model="form.shop_id" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.shop_id" class="text-red-500">{{ errors.shop_id }}</div>
                                        </div>
                                        <div class=" relative">
                                            <div class="relative">
                                                <label for="title" class="leading-7 text-sm text-gray-600">店名</label>
                                                <div class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ report.co_name }}---{{ report.shop_name }}</div>
                                                <div v-if="errors.title" class="text-red-500">{{ errors.title }}</div>
                                            </div>
                                            <div v-if="errors.report_category_id" class="text-red-500">{{ errors.title }}</div>
                                        </div>
                                    </div>

                                    <div class="p-2 w-full">
                                    <div class="relative">
                                        <label for="report" class="leading-7 text-sm text-gray-600">Report</label>
                                        <textarea id="report" name="report"  v-model="form.report" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out"></textarea>
                                        <div v-if="errors.report" class="text-red-500">{{ errors.report }}</div>
                                    </div>
                                    </div>

                                    <div class="flex p-2 w-full">
                                        <div v-if="report.image1" class="relative">
                                        <label class="leading-7 text-sm text-gray-600">保存画像1</label>
                                            <div class="w-full mb-1">
                                                <div >
                                                    <img :src="`/storage/reports/${report.image1}`" alt="Image1">
                                                </div>
                                                <div class="h-8 ml-16 mt-4">
                                                    <button class="ml-2 bg-red-500 text-white px-2 h-8 rounded" @click.prevent="del_image1(report.id)">削除</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="report.image2" class="relative">
                                            <label class="leading-7 text-sm text-gray-600">保存画像2</label>
                                                <div class="w-full mb-1">
                                                    <div >
                                                        <img :src="`/storage/reports/${report.image2}`" alt="Image2">
                                                    </div>
                                                    <div class="h-8 ml-16 mt-4">
                                                        <button class="ml-2 bg-red-500 text-white px-2 h-8 rounded" @click.prevent="del_image2(report.id)">削除</button>
                                                    </div>
                                                </div>
                                        </div>
                                        <div v-if="report.image3" class="relative">
                                            <label class="leading-7 text-sm text-gray-600">保存画像3</label>
                                                <div class="w-full mb-1">
                                                    <div >
                                                        <img :src="`/storage/reports/${report.image3}`" alt="Image3">
                                                    </div>
                                                    <div class="h-8 ml-16 mt-4">
                                                        <button class="ml-2 bg-red-500 text-white px-2 h-8 rounded" @click.prevent="del_image3(report.id)">削除</button>
                                                    </div>
                                                </div>
                                        </div>
                                        <div  v-if="report.image4" class="relative">
                                            <label class="leading-7 text-sm text-gray-600">保存画像4</label>
                                                <div class="w-full mb-1">
                                                    <div>
                                                        <img :src="`/storage/reports/${report.image4}`" alt="Image4">
                                                    </div>
                                                    <div class="h-8 ml-16 mt-4">
                                                        <button class="ml-2 bg-red-500 text-white px-2 h-8 rounded" @click.prevent="del_image4(report.id)">削除</button>
                                                    </div>
                                                </div>
                                        </div>

                                    </div>

                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="image1" class="leading-7 text-sm mt-2 text-gray-800 dark:text-gray-200 ">変更画像選択1</label>
                                            <input type="file" id="image1" name="image1" accept="image/png,image/jpeg,image/jpg" @change="handleFileUpload1" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.image1" class="text-red-500">{{ errors.image1 }}</div>
                                        </div>

                                    </div>
                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="image2" class="leading-7 text-sm mt-2 text-gray-800 dark:text-gray-200 ">変更画像選択2</label>
                                            <input type="file" id="image2" name="image2" accept="image/png,image/jpeg,image/jpg" @change="handleFileUpload2" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.image2" class="text-red-500">{{ errors.image2 }}</div>
                                        </div>
                                    </div>
                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="image3" class="leading-7 text-sm mt-2 text-gray-800 dark:text-gray-200 ">変更画像選択3</label>
                                            <input type="file" id="image3" name="image3" accept="image/png,image/jpeg,image/jpg" @change="handleFileUpload3" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.image3" class="text-red-500">{{ errors.image3 }}</div>
                                        </div>
                                    </div>
                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="image4" class="leading-7 text-sm mt-2 text-gray-800 dark:text-gray-200 ">変更画像選択4</label>
                                            <input type="file" id="image4" name="image4" accept="image/png,image/jpeg,image/jpg" @change="handleFileUpload4" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.image4" class="text-red-500">{{ errors.image4 }}</div>
                                        </div>
                                    </div>

                                    <div class="p-2 w-full">
                                        <button class="flex mx-auto text-white bg-pink-500 border-0 py-2 px-8 focus:outline-none hover:bg-pink-600 rounded text-lg"> 更新</button>
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
