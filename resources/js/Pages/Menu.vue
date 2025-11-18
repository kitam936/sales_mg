<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { usePage ,router} from '@inertiajs/vue3';
import { ref } from 'vue';
import { computed } from 'vue';

const page = usePage();

const props = defineProps({
    reports: [Object,Boolean],
    comments: [Object,Boolean],
    login_user: [Object],
})

const logout = () => {
    router.post(route('logout'), {}, {
        onSuccess: () => {
            // ログアウト後にログイン画面へ
            window.location.href = route('login');
        },
        onError: (error) => {
            console.error('Logout failed:', error);
        },
    });
};

</script>

<template>
    <Head title="Menu" />

    <AuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">GMS販売管理 v2</h2>
        </template>

        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-4">
                <!-- <h2 class="font-semibold text-xl text-gray-800 leading-tight">　Menu</h2> -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="py-6 pl-2 text-gray-900">


                        <section class="text-gray-600 body-font relative">

                            <h3 class="ml-4 font-semibold text-xl text-indigo-800 leading-tight">
                                分析
                            </h3>
                                <div class="md:flex ">

                                    <div class="flex">
                                    <div class="p-2 ">
                                        <Link as="button" :href="route('analysis')" class="w-40 flex mx-auto text-white bg-indigo-500 border-0 h-10 py-2 pl-12 focus:outline-none hover:bg-indigo-600 rounded text-ml">データ分析</Link>
                                    </div>
                                    <!-- <div class="p-2 ">
                                        <Link as="button" class="w-40 flex mx-auto text-white bg-indigo-500 border-0 h-10 py-2 pl-12 focus:outline-none hover:bg-indigo-600 rounded text-ml">商品分析</Link>
                                    </div> -->
                                    </div>
                                </div>
                                <br>

                            <h3 class="ml-4 font-semibold text-xl text-indigo-800 leading-tight">
                                店舗Report
                            </h3>
                            <div v-if="reports" class="ml-12 text-ml text-red-600">
                                ※ 未読のReportがあります !
                            </div>

                            <div v-if="comments" class="ml-12 text-ml text-red-600">
                                ※ 未読のReportコメントがあります !
                            </div>
                                <div class="p-2 ">
                                    <Link as="button" :href="route('reports.index')" class="w-40 flex text-white bg-indigo-500 border-0 h-10 py-2 pl-9 focus:outline-none hover:bg-indigo-600 rounded text-ml">店舗Report</Link>
                                </div>

                            <br>


                            <h3 class="ml-4 font-semibold text-xl text-indigo-800 leading-tight">
                                各種Data
                            </h3>

                                <div class="md:flex ">
                                    <div class="flex">
                                    <div class="p-2 ">
                                        <Link as="button" :href="route('hinbans.index')" class="w-40 flex mx-auto text-white bg-indigo-500 border-0 h-10 py-2 pl-12 focus:outline-none hover:bg-indigo-600 rounded text-ml">商品リスト</Link>
                                    </div>
                                    <div class="p-2 ">
                                        <Link as="button" :href="route('shops.index')" class="w-40 flex mx-auto text-white bg-indigo-500 border-0 h-10 py-2 pl-11 focus:outline-none hover:bg-indigo-600 rounded text-ml">店舗リスト</Link>
                                    </div>
                                    </div>

                                <div class="flex">
                                    <div class="p-2 ">
                                        <Link as="button" :href="route('company.index')" class="w-40 flex mx-auto text-white bg-indigo-500 border-0 h-10 py-2 pl-9 focus:outline-none hover:bg-indigo-600 rounded text-ml">取引先リスト</Link>
                                    </div>
                                    <div class="p-2 ">
                                        <Link as="button" :href="route('users.index')" class="w-40 flex mx-auto text-white bg-indigo-500 border-0 h-10 py-2 pl-11 focus:outline-none hover:bg-indigo-600 rounded text-ml">User管理</Link>
                                    </div>
                                </div>
                            </div>

                            <br>

                            <div class="flex">
                                <div v-if="login_user.role_id <= 2" class="p-2 ">
                                    <Link as="button" :href="route('admin.data.data_menu')" class="w-40 flex text-white bg-blue-500 border-0 h-10 py-2 pl-12 focus:outline-none hover:bg-blue-600 rounded text-ml">Data管理</Link>
                                </div>
                                <div class="flex ">
                                    <!-- ログアウトボタン -->
                                    <button @click="logout" class="w-40 flex ml-2 mt-2 text-white bg-gray-500 h-10 py-2 pl-12 hover:bg-red-gray rounded">
                                        ログアウト
                                    </button>
                                </div>
                            </div>

                        </section>
                    </div>
                </div>
            </div>
        </div>



    </AuthenticatedLayout>
</template>
