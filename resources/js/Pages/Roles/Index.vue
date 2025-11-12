<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head ,Link } from '@inertiajs/vue3';
    import FlashMessage from '@/Components/FlashMessage.vue';

    import { Inertia } from '@inertiajs/inertia';
    import { ref } from 'vue';

    defineProps({
        roles: Array,

    });


</script>

<template>
    <Head title="Role一覧" />

    <AuthenticatedLayout>
        <template #header>

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Role一覧</h2>

            <div class="flex">

                <div class="ml-2 md:ml-24 mt-4">
                    <Link as="button" :href="route('admin.data.data_index')" class="w-32 h-8 bg-indigo-500 text-sm text-white ml-0 hover:bg-indigo-600 rounded">Index</Link>
                </div>
                <div class="ml-4 md:ml-24 mt-4">
                    <Link as="button" :href="route('roles.create')" class="w-32 h-8 bg-green-500 text-sm text-white ml-0 hover:bg-green-600 rounded">Role登録</Link>
                </div>
            </div>
        </template>

        <div class="py-4">

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <FlashMessage/>
                    <div class="p-2 text-gray-900">

                        <div class=" mx-auto w-full sm:px-4 lg:px-4 border ">

                        <table class="bg-white table-auto w-full text-center whitespace-no-wrap">
                            <thead>
                                <tr>
                                    <th class="w-1/15 md:1/15 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">id</th>
                                    <th class="w-2/15 md:2/15 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">RoleName</th>
                                    <th class="w-3/15 md:3/15 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">Role_Info</th>

                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="role in roles" :key="role.id">
                                    <td class="border-b-2 boder-gray-200 hidden sm:table-cell ">
                                        <Link class="text-indigo-500" :href="route('roles.show',{role:role.id})">{{ role.id }} </Link>
                                    </td>
                                    <td class="border-b-2 boder-gray-200">{{ role.role_name }} </td>
                                    <td class="border-b-2 boder-gray-200 text-left">{{ role.role_info ? role.role_info.substring(0, 15) : '' }} </td>

                                </tr>

                            </tbody>
                        </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
