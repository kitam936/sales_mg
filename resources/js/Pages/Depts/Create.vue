<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head ,Link} from '@inertiajs/vue3';
import { reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { useForm ,usePage } from '@inertiajs/vue3'



defineProps({
    errors : Object,

})


const page = usePage();

const form = useForm({
    dept_name: page.props.old?.dept_name ?? null,
    dept_info: page.props.old?.dept_info ?? null,
});

const storeDept = () => {
    Inertia.post('/depts', form)
}

// 戻るボタンの処理
const goBack = () => {
    window.history.back();
};

</script>

<template>
    <Head title="Dept登録" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dept登録</h2>
            <div class="flex mt-4">
                <div class="">
                    <button
                        type="button"
                        @click="goBack"
                        class="w-32 h-8 ml-4 md:ml-24 text-gray-700 bg-gray-200 border border-gray-300 focus:outline-none hover:bg-gray-300 rounded text-ml">
                        戻る
                    </button>
                </div>
                <div class="ml-4 md:ml-24 mb-0">
                    <Link as="button" :href="route('depts.index')" class="w-32 h-8 bg-indigo-500 text-sm text-white ml-0 hover:bg-indigo-600 rounded">dept一覧</Link>
                </div>
            </div>
        </template>

        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-2 text-gray-900">
                        <section class="text-gray-600 body-font relative">

                            <form @submit.prevent="storeDept" >
                            <div class="container px-5 py-2 mx-auto">
                                <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                <div class="flex flex-wrap -m-2">


                                    <div class="ml-2 w-full">
                                        <div class="relative">
                                            <label for="dept_name" class="leading-7 text-sm text-gray-600">DeptName</label>
                                            <input type="text" id="dept_name" name="dept_name" v-model="form.dept_name" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.dept_name" class="text-red-500">{{ errors.dept_name }}</div>
                                        </div>
                                    </div>

                                    <div class="ml-2 w-full">
                                    <div class="relative">
                                        <label for="dept_info" class="leading-7 text-sm text-gray-600">詳細</label>
                                        <textarea id="dept_info" name="dept_info"  v-model="form.dept_info" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out"></textarea>
                                        <div v-if="errors.dept_info" class="text-red-500">{{ errors.dept_info }}</div>
                                    </div>
                                    </div>

                                </div>


                                    <div class="ml-2 mt-4 w-full">
                                        <button class="w-32 h-8 flex mx-auto text-white bg-pink-500 border-0 py-2 pl-12 focus:outline-none hover:bg-pink-600 rounded text-sm"> 登録</button>
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
