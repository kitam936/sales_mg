<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head ,Link} from '@inertiajs/vue3';
import { reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { useForm } from '@inertiajs/vue3'
// import { Link } from '@inertiajs/inertia-vue3';


const props = defineProps({
    item: Object,
    categories: Array,
    car_categories: Array,
    errors : Object,
})

const form = useForm({
    id: props.item.id,
    item_category_id: props.item.item_category_id ,
    car_category_id: props.item.car_category_id ,
    prod_code: props.item.prod_code ,
    item_name: props.item.item_name ,
    item_info: props.item.item_info ,
    item_price: props.item.item_price ,
    item_cost: props.item.item_cost ,
    item_image: props.item.item_image, // ファイルは保持不可（これは仕様です）
});

const handleFileUpload = (event) => {
    form.item_image = event.target.files[0];
}

// const updateItem = (id) => {
//     Inertia.put(route('items.update',{item:id}),form)
// }

const updateItem = (id) => {
    const formData = new FormData();

    formData.append('id', form.id);
    formData.append('item_category_id', form.item_category_id);
    formData.append('car_category_id', form.car_category_id);
    formData.append('prod_code', form.prod_code);
    formData.append('item_name', form.item_name);
    formData.append('item_price', form.item_price);
    formData.append('item_cost', form.item_cost);
    formData.append('item_info', form.item_info);

    if (form.item_image instanceof File) {
        formData.append('item_image', form.item_image);
    }

    Inertia.post(route('items.update', { item: id }), formData, {
        forceFormData: true, // ←これ重要
        onProgress: () => {},
        onSuccess: () => {},
        onError: () => {},
    });
};

const deleteItem = id => {
    Inertia.delete(route('items.destroy',{item:id}),{
        onBefore:() => confirm('本当に削除しますか？')
    })
}

// 戻るボタンの処理
const goBack = () => {
    window.history.back();
};
</script>

<template>
    <Head title="商品編集" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">商品編集</h2>
            <div class="flex mt-4">
                <div class="">
                    <button
                        type="button"
                        @click="goBack"
                        class="w-32 h-8 ml-24 text-gray-700 bg-gray-200 border border-gray-300 focus:outline-none hover:bg-gray-300 rounded text-ml">
                        戻る
                    </button>
                </div>
                <div class="ml-24 mb-0">
                    <Link as="button" :href="route('items.index')" class="w-32 h-8 bg-indigo-500 text-sm text-white ml-0 hover:bg-indigo-600 rounded">商品一覧</Link>
                </div>
            </div>
        </template>

        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-2 text-gray-900">
                        <section class="text-gray-600 body-font relative">

                            <form @submit.prevent="updateItem(form.id)" enctype="multipart/form-data">
                            <div class="container px-5 py-2 mx-auto">
                                <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                <div class="flex flex-wrap -m-2">

                                    <div class="ml-2 relative">
                                        <label for="item_category_id" class="leading-7 text-sm text-gray-600">種別ID</label>
                                        <select id="item_category_id" name="item_category_id" v-model="form.item_category_id" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <option value="" disabled>種別選択</option>
                                            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.item_category_name }}</option>
                                        </select>
                                        <div v-if="errors.item_category_id" class="text-red-500">{{ errors.item_category_id }}</div>
                                    </div>
                                    <div class="ml-2 relative">
                                        <label for="car_category_id" class="leading-7 text-sm text-gray-600">車種</label>
                                        <select id="car_category_id" name="car_category_id" v-model="form.car_category_id" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <option value="" disabled>車種選択</option>
                                            <option v-for="car_category in car_categories" :key="car_category.id" :value="car_category.id">{{ car_category.car_name }}</option>
                                        </select>
                                        <div v-if="errors.item_category_id" class="text-red-500">{{ errors.item_category_id }}</div>
                                    </div>
                                    <div class="ml-2 w-full">
                                        <div class="relative">
                                            <label for="prod_code" class="leading-7 text-sm text-gray-600">商品番号</label>
                                            <input type="text" id="prod_code" name="prod_code" v-model="form.prod_code" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.prod_code" class="text-red-500">{{ errors.prod_code }}</div>
                                        </div>
                                        <div class="relative">
                                            <label for="item_name" class="leading-7 text-sm text-gray-600">商品名</label>
                                            <input type="text" id="item_name" name="item_name" v-model="form.item_name" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            <div v-if="errors.item_name" class="text-red-500">{{ errors.item_name }}</div>
                                        </div>
                                    </div>
                                    <div class="flex ml-2 w-full">
                                    <div class="relative">
                                        <label for="item_price" class="leading-7 text-sm text-gray-600">Price</label>
                                        <input type="number" id="item_price" name="item_price" v-model="form.item_price" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                        <div v-if="errors.item_price" class="text-red-500">{{ errors.item_price }}</div>
                                    </div>

                                    <div class="ml-2 relative">
                                        <label for="item_cost" class="leading-7 text-sm text-gray-600">Cost</label>
                                        <input type="number" id="item_cost" name="item_cost" v-model="form.item_cost" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                        <div v-if="errors.item_cost" class="text-red-500">{{ errors.item_cost }}</div>
                                    </div>

                                    </div>
                                    <div class="ml-2 w-full">
                                    <div class="relative">
                                        <label for="item_info" class="leading-7 text-sm text-gray-600">詳細</label>
                                        <textarea id="item_info" name="item_info"  v-model="form.item_info" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out"></textarea>
                                        <div v-if="errors.item_info" class="text-red-500">{{ errors.item_info }}</div>
                                    </div>
                                    </div>

                                    <div class="ml-2 w-40">
                                        <div class="relative">
                                        <label for="item_image_0" class="leading-7 text-sm text-gray-600">保存画像</label>
                                            <div class="w-full mb-1">
                                                    <div v-if="item.item_image">
                                                            <img :src="`/storage/items/${item.item_image}`" alt="Item Image">
                                                    </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="ml-2 w-full">
                                        <div class="relative">
                                            <label for="item_image" class="leading-7 text-sm mt-2 text-gray-800 dark:text-gray-200 ">変更画像選択</label>
                                            <input type="file" id="item_image" name="item_image" baccept="image/png,image/jpeg,image/jpg" @change="handleFileUpload" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                        </div>
                                    </div>

                                    <div class="ml-2 mt-4 w-full">
                                        <button class="w-32 h-8 flex mx-auto text-white bg-pink-500 border-0 py-2 pl-12 focus:outline-none hover:bg-pink-600 rounded text-sm"> 更新</button>
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
