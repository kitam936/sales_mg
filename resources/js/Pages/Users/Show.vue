<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head ,Link} from '@inertiajs/vue3';

import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    user: Object
})

const deleteUser = (id) => {
    Inertia.delete(route('users.destroy', { user: id }), {
        onBefore: () => confirm('本当に削除しますか？')
    })
}

// 戻るボタンの処理
const goBack = () => {
    window.history.back();
};

</script>

<template>
    <Head title="User詳細" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">User詳細</h2>
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


                            <div class="container px-5 py-2 mx-auto">
                                <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                <div class="flex flex-wrap -m-2">


                                    <div class="ml-2 w-full">
                                        <div class="relative">
                                            <label for="id" class="leading-7 text-sm text-gray-600">ID</label>
                                            <div type="text" id="user_id" name="user_id"  class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ user.id }}</div>

                                        </div>

                                        <div class="relative">
                                            <label for="name" class="leading-7 text-sm text-gray-600">Name</label>
                                            <div type="text" id="name" name="name"  class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ user.name }}</div>

                                        </div>
                                        <div class="relative">
                                            <label for="email" class="leading-7 text-sm text-gray-600">mail</label>
                                            <div type="email" id="email" name="email" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ user.email }}</div>

                                        </div>
                                    </div>
                                    <div class="flex ml-2 w-full">
                                        <div class="p-0 relative">
                                            <label for="shop_id" class="w-32 leading-7 text-sm text-gray-600">属性</label>
                                            <!-- <select id="shop_id" name="shop_id"  class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                <option value="" disabled>Shop選択</option>
                                                <option v-for="shop in shops" :key="shop.id" :value="shop.id">{{ shop.shop_name }}</option>
                                            </select> -->
                                            <div type="shop_id" id="shop_id" name="shop_id" class="w-32 bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ user.shop_name }}</div>

                                        </div>
                                        <div class="p-0 w-40 ml-2 relative">
                                            <label for="role_id" class="leading-7 text-sm text-gray-600">Role</label>
                                            <!-- <select id="role_id" name="role_id"  class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                <option value="" disabled>Role選択</option>
                                                <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.role_name }}</option>
                                            </select> -->
                                            <div type="role_id" id="role_id" name="role_id" class="w-40 bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ user.role_name }}</div>
                                        </div>
                                    </div>
                                    <div class="ml-2 w-full">
                                    <div class="relative">
                                        <label for="user_info" class="leading-7 text-sm text-gray-600">詳細</label>
                                        <textarea id="user_info" name="user_info" :value="user.user_info" readonly class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out"></textarea>

                                    </div>
                                    </div>

                                    <div class="ml-2 w-full"></div>
                                    <div class="ml-2 w-full">
                                        <div class="relative">
                                            <label for="postcode" class="leading-7 text-sm text-gray-600">郵便番号</label>
                                            <div type="text" id="postcode" name="postcode" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ user.postcode }}</div>

                                        </div>
                                        <div class="relative">
                                            <label for="address" class="leading-7 text-sm text-gray-600">住所</label>
                                            <div type="text" id="address" name="address" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ user.address }}</div>

                                        </div>
                                        <div class="relative">
                                            <label for="tel" class="leading-7 text-sm text-gray-600">TEL</label>
                                            <div type="text" id="tel" name="tel" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ user.tel }}</div>

                                        </div>
                                    </div>
                                </div>


                                <div class="flex mt-8">
                                    <div class="ml-2 w-full">
                                        <Link as="button" :href="route('users.edit',{user:user.id})" class="w-32 h-8 flex mx-auto text-white bg-green-500 border-0 py-2 pl-12 focus:outline-none hover:bg-green-600 rounded text-sm">編集</Link>
                                    </div>
                                    <div class="ml-2 w-full">
                                        <button class="w-32 h-8 flex mx-auto text-white bg-red-500 border-0 py-2 pl-8 focus:outline-none hover:bg-red-600 rounded text-sm" @click="deleteUser(user.id)" >削除する</button>
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
