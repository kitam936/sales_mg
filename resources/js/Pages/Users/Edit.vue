<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head ,Link} from '@inertiajs/vue3';
import { reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { useForm ,usePage} from '@inertiajs/vue3'



const props = defineProps({
    user: Object,
    depts: Array,
    roles: Array,
    errors : Object,
})


const form = useForm({
    id: props.user.id,
    name: props.user.name,
    email: props.user.email,
    dept_id: props.user.dept_id,
    role_id: props.user.role_id,
    user_info: props.user.user_info,
    mailService: props.user.mailService,
    password: '',
    password_confirmation: '',
  });

  const updateUser = () => {
    form.put(route('users.update', { user: form.id }));
  };

const fetchAddress = () => {
    new YubinBangouCore(String(form.postcode),(value) => {
        form.address = value.region + value.locality + value.street
    })
}

// 戻るボタンの処理
const goBack = () => {
    window.history.back();
};


</script>

<template>
    <Head title="User編集" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Staff編集</h2>
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
                    <Link as="button" :href="route('users.index')" class="w-32 h-8 bg-indigo-500 text-sm text-white ml-0 hover:bg-indigo-600 rounded">Staff一覧</Link>
                </div>
            </div>
        </template>

        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-2 text-gray-900">
                        <section class="text-gray-600 body-font relative">

                            <form @submit.prevent="updateUser(form.id)">
                                <div class="container px-5 py-2 mx-auto">
                                    <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                    <div class="flex flex-wrap -m-2">

                                        <!-- BEGIN: User Form Fields -->
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
                                                <label for="dept_id" class="leading-7 text-sm text-gray-600">所属</label>
                                                <select id="dept_id" name="dept_id" v-model="form.dept_id" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                    <option value="" disabled>所属選択</option>
                                                    <option v-for="dept in depts" :key="dept.id" :value="dept.id">{{ dept.dept_name }}</option>
                                                </select>
                                                <div v-if="errors.dept_id" class="text-red-500">{{ errors.dept_id }}</div>
                                            </div>
                                            <div class="p-0 ml-2 relative">
                                                <label for="role_id" class="leading-7 text-sm text-gray-600">権限</label>
                                                <select id="role_id" name="role_id" v-model="form.role_id" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                    <option value="" disabled>権限選択</option>
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

                                        <div class="ml-2 w-1/2 ">
                                            <label for="mailService" class="leading-7 text-sm  text-gray-800 dark:text-gray-200 ">メール配信</label>
                                            <div class="relative flex justify-around">
                                                <div><input type="radio" name="mailService" value="1" class="mr-2" v-model="form.mailService">配信可</div>
                                                <div><input type="radio" name="mailService" value="0" class="mr-2" v-model="form.mailService">配信不可</div>
                                            </div>
                                        </div>
                                    </div>
                                        <div class="relative">
                                            <label for="password" class="leading-7 text-sm text-gray-600">パスワード</label>
                                            <input type="password" id="password" name="password" v-model="form.password" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                        </div>
                                        <div class="relative">
                                            <label for="password_confirmation" class="leading-7 text-sm text-gray-600">パスワード確認</label>
                                            <input type="password" id="password_confirmation" name="password_confirmation" v-model="form.password_confirmation" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                        </div>

                                        <div class="ml-2 mt-4 w-full">
                                            <button class="w-32 h-8 flex mx-auto text-white bg-pink-500 border-0 py-2 pl-12 focus:outline-none hover:bg-pink-600 rounded text-sm"> 更新</button>
                                        </div>
                                        <!-- END: User Form Fields -->

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

