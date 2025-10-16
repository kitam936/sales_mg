<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head ,Link} from '@inertiajs/vue3';
import { reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { useForm ,usePage } from '@inertiajs/vue3'
import {Core as YubinBangouCore} from 'yubinbango-core2';


defineProps({
    errors : Object,
    roles: Array,
    shops: Array,
})


const page = usePage();

const form = useForm({
    name: page.props.old?.name ?? null,
    email: page.props.old?.email ?? null,
    shop_id: page.props.old?.shop_id ?? "",
    role_id: page.props.old?.role_id ?? "",
    user_info: page.props.old?.user_info ?? null,
    postcode: page.props.old?.postcode ?? null,
    address: page.props.old?.address ?? null,
    tel: page.props.old?.tel ?? null,
    password: page.props.old?.password ?? null,
    password_confirmation: page.props.old?.password_confirmation ?? null,
});

const fetchAddress = () => {
    new YubinBangouCore(String(form.postcode),(value) => {
        form.address = value.region + value.locality + value.street
    })
}

const storeUser = () => {
    Inertia.post('/users', form)
}

// 戻るボタンの処理
const goBack = () => {
    window.history.back();
};

</script>

<template>
    <Head title="User登録" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">User登録</h2>
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
                    <Link as="button" :href="route('users.index')" class="w-32 h-8 bg-indigo-500 text-sm text-white ml-0 hover:bg-indigo-600 rounded">User一覧</Link>
                </div>
            </div>
        </template>

        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-2 text-gray-900">
                        <section class="text-gray-600 body-font relative">

                            <form @submit.prevent="storeUser" >
                            <div class="container px-5 py-2 mx-auto">
                                <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                <div class="flex flex-wrap -m-2">


                                    <div class="ml-2 w-full">
                                        <div class="relative">
                                            <label for="name" class="leading-7 text-sm text-gray-600">Name</label>
                                            <input type="text" id="name" name="name" v-model="form.name" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.name" class="text-red-500">{{ errors.name }}</div>
                                        </div>
                                        <div class="relative">
                                            <label for="email" class="leading-7 text-sm text-gray-600">mail</label>
                                            <input type="email" id="email" name="email" v-model="form.email" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.email" class="text-red-500">{{ errors.email }}</div>
                                        </div>
                                    </div>
                                    <div class="flex ml-2 w-full">
                                        <div class="p-0 relative">
                                            <label for="shop_id" class="leading-7 text-sm text-gray-600">ShopID</label>
                                            <select id="shop_id" name="shop_id" v-model="form.shop_id" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                <option value="" >Shop選択</option>
                                                <option v-for="shop in shops" :key="shop.id" :value="shop.id">{{ shop.shop_name }}</option>
                                            </select>
                                            <div v-if="errors.shop_id" class="text-red-500">{{ errors.shop_id }}</div>
                                        </div>
                                        <div class="p-0 ml-2 relative">
                                            <label for="role_id" class="leading-7 text-sm text-gray-600">RoleID</label>
                                            <select id="role_id" name="role_id" v-model="form.role_id" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                <option value="" >Role選択</option>
                                                <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.role_name }}</option>
                                            </select>
                                            <div v-if="errors.role_id" class="text-red-500">{{ errors.role_id }}</div>
                                        </div>
                                    </div>
                                    <div class="ml-2 w-full">
                                    <div class="relative">
                                        <label for="user_info" class="leading-7 text-sm text-gray-600">詳細</label>
                                        <textarea id="user_info" name="user_info"  v-model="form.user_info" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out"></textarea>
                                        <div v-if="errors.user_info" class="text-red-500">{{ errors.user_info }}</div>
                                    </div>
                                    </div>
                                    <div class="ml-2 w-full">
                                        <div class="relative">
                                            <label for="postcode" class="leading-7 text-sm text-gray-600">郵便番号</label>
                                            <input type="text" id="postcode" name="postcode" @change="fetchAddress" v-model="form.postcode" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.postcode" class="text-red-500">{{ errors.postcode }}</div>
                                        </div>
                                        <div class="relative">
                                            <label for="address" class="leading-7 text-sm text-gray-600">住所</label>
                                            <input type="text" id="address" name="address" v-model="form.address" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.address" class="text-red-500">{{ errors.address }}</div>
                                        </div>
                                        <div class="relative">
                                            <label for="tel" class="leading-7 text-sm text-gray-600">TEL</label>
                                            <input type="text" id="tel" name="tel" v-model="form.tel" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.tel" class="text-red-500">{{ errors.tel }}</div>
                                        </div>
                                    </div>
                                </div>
                                    <div class="relative">
                                        <label for="password" class="leading-7 text-sm text-gray-600">パスワード</label>
                                        <input type="password" id="password" name="password" v-model="form.password" required class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                    </div>
                                    <div class="relative">
                                        <label for="password_confirmation" class="leading-7 text-sm text-gray-600">パスワード確認</label>
                                        <input type="password" id="password_confirmation" name="password_confirmation" v-model="form.password_confirmation" required class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
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
