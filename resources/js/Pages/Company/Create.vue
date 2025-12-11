<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head ,Link} from '@inertiajs/vue3';
import { reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { useForm ,usePage } from '@inertiajs/vue3'



defineProps({
    errors : Object,
    pics: Array,
})


const page = usePage();

const form = useForm({
    co_id: page.props.old?.co_id ?? "",
    co_name: page.props.old?.co_name ?? null,
    co_info: page.props.old?.co_info ?? null,
    pic_id: page.props.old?.pic_id ?? "",
});



const storeCompany = () => {
    Inertia.post('/company', form)
}

// 戻るボタンの処理
const goBack = () => {
    window.history.back();
};

</script>

<template>
    <Head title="取引先登録" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">取引先登録</h2>
            <div class="flex mt-4">
                <div class="">
                    <button
                        type="button"
                        @click="goBack"
                        class="w-40 h-10 ml-2 md:ml-24 text-gray-700 bg-gray-200 border border-gray-300 focus:outline-none hover:bg-gray-300 rounded text-ml">
                        戻る
                    </button>
                </div>
                <div class="ml-2 md:ml-24 mb-0">
                    <Link as="button" :href="route('company.index')" class="w-40 h-10 bg-indigo-500 text-sm text-white ml-0 hover:bg-indigo-600 rounded">取引先一覧</Link>
                </div>
            </div>
        </template>

        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-2 text-gray-900">
                        <section class="text-gray-600 body-font relative">

                            <form @submit.prevent="storeCompany" >
                            <div class="container px-5 py-2 mx-auto">
                                <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                <div class="flex flex-wrap -m-2">


                                    <div class="ml-2 w-full">
                                        <div class="relative">
                                            <label for="co_id" class="leading-7 text-sm text-gray-600">取引先ID</label>
                                            <input type="text" id="co_id" name="co_id" v-model="form.co_id" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.co_id" class="text-red-500">{{ errors.co_id }}</div>
                                        </div>
                                        <div class="relative">
                                            <label for="co_name" class="leading-7 text-sm text-gray-600">取引先名</label>
                                            <input type="text" id="co_name" name="co_name" v-model="form.co_name" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.co_name" class="text-red-500">{{ errors.co_name }}</div>
                                        </div>
                                    </div>
                                    <div class="flex ml-2 w-full">
                                        <div class="p-0 relative">
                                            <label for="pic_id" class="leading-7 text-sm text-gray-600">担当者</label>
                                            <select id="pic_id" name="pic_id" v-model="form.pic_id" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                <option value="" >担当者選択</option>
                                                <option v-for="pic in pics" :key="pic.id" :value="pic.id">{{ pic.name }}</option>
                                            </select>
                                            <div v-if="errors.pic_id" class="text-red-500">{{ errors.pic_id }}</div>
                                        </div>

                                    </div>
                                    <div class="ml-2 w-full">
                                    <div class="relative">
                                        <label for="co_info" class="leading-7 text-sm text-gray-600">詳細</label>
                                        <textarea id="co_info" name="co_info"  v-model="form.co_info" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out"></textarea>
                                        <div v-if="errors.co_info" class="text-red-500">{{ errors.co_info }}</div>
                                    </div>
                                    </div>

                                </div>


                                    <div class="ml-2 mt-4 mb-12 w-full">
                                        <button class="w-40 h-10 flex mx-auto text-white bg-pink-500 border-0 py-2 pl-16 focus:outline-none hover:bg-pink-600 rounded text-sm"> 登録</button>
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
