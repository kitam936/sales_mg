<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
            Dijon販売管理 v1.1
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">

                        ボタンを押してMenu画面へ進んでください。
                    </div>

                    <!-- ★ メニュー画面へのリンク -->
                    <div class="ml-24 mb-12">
                        <Link
                        :href="route('menu')"
                        class="w-32 h-10 inline-flex items-center rounded-md bg-indigo-600 px-9 py-2 text-ml font-semibold text-white hover:bg-indigo-700"
                        >
                            Menuへ
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
