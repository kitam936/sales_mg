<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head ,Link} from '@inertiajs/vue3';

import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    dept: Object
})

const deleteDept = (id) => {
    Inertia.delete(route('depts.destroy', { dept: id }), {
        onBefore: () => confirm('本当に削除しますか？')
    })
}

// 戻るボタンの処理
const goBack = () => {
    window.history.back();
};

</script>

<template>
    <Head title="dept詳細" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dept詳細</h2>
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
                    <Link as="button" :href="route('depts.index')" class="w-32 h-8 bg-indigo-500 text-sm text-white ml-0 hover:bg-indigo-600 rounded">Dept一覧</Link>
                </div>
            </div>
        </template>

        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-2 text-gray-900">
                        <section class="text-gray-600 body-font relative">


                            <div class="container px-5 py-2 mx-auto">
                                <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                <div class="flex flex-wrap -m-2">


                                    <div class="ml-2 w-full">
                                        <div class="relative">
                                            <label for="dept_id" class="leading-7 text-sm text-gray-600">ID</label>
                                            <div type="text" id="dept_id" name="dept_id"  class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ dept.id }}</div>

                                        </div>

                                        <div class="relative">
                                            <label for="dept_name" class="leading-7 text-sm text-gray-600">DeptName</label>
                                            <div type="text" id="dept_name" name="dept_name"  class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ dept.dept_name }}</div>

                                        </div>

                                    </div>

                                    <div class="ml-2 w-full">
                                    <div class="relative">
                                        <label for="dept_info" class="leading-7 text-sm text-gray-600">詳細</label>
                                        <textarea id="dept_info" name="dept_info" :value="dept.dept_info" readonly class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out"></textarea>

                                    </div>
                                    </div>



                                </div>


                                <div class="flex mt-8">
                                    <div class="ml-2 w-full">
                                        <Link as="button" :href="route('depts.edit',{dept:dept.id})" class="w-32 h-8 flex mx-auto text-white bg-green-500 border-0 py-2 pl-12 focus:outline-none hover:bg-green-600 rounded text-sm">編集</Link>
                                    </div>
                                    <div class="ml-2 w-full">
                                        <button class="w-32 h-8 flex mx-auto text-white bg-red-500 border-0 py-2 pl-8 focus:outline-none hover:bg-red-600 rounded text-sm" @click="deleteDept(dept.id)" >削除する</button>
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
