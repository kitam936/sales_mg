<script setup>
    import { Head, Link } from '@inertiajs/vue3';

    defineProps({
        canLogin: {
            type: Boolean,
        },
        canRegister: {
            type: Boolean,
        },
        laravelVersion: {
            type: String,
            required: true,
        },
        phpVersion: {
            type: String,
            required: true,
        },
    });

    function handleImageError() {
        document.getElementById('screenshot-container')?.classList.add('!hidden');
        document.getElementById('docs-card')?.classList.add('!row-span-1');
        document.getElementById('docs-card-content')?.classList.add('!flex-row');
        document.getElementById('background')?.classList.add('!hidden');
    }
    </script>

    <template>
        <Head title="Welcome" />
        <div class="bg-sky-50 text-black/50 dark:bg-black dark:text-white/50">
            <!-- <img
                id="background"
                class="absolute -left-20 top-0 max-w-[877px]"
                src="https://laravel.com/assets/img/welcome/background.svg"
            /> -->
            <div
                class="relative flex min-h-screen flex-col items-center justify-center selection:bg-[#2563eb] selection:text-white"
            >
                <div class="relative mt-8 w-full max-w-2xl px-6 lg:max-w-7xl">
                    <header
                        class="grid grid-cols-2 items-center gap-2 py-2 lg:grid-cols-3"
                    >
                        <div class="flex w-44 lg:col-start-2 lg:justify-center">
                            <div>

                                <img src="/images/logo.png" alt="Logo">
                            </div>
                        </div>
                        <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
                            <Link
                                v-if="$page.props.auth.user"
                                :href="route('menu')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#2563eb] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Menu
                            </Link>

                            <template v-else>
                                <Link
                                    :href="route('login')"
                                    class="text-xl rounded-md px-3 py-2 text-indigo-700 ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#2563eb] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                >
                                <span class="text-xl font-semibold "> [Log in]</span>
                                </Link>

                                <!-- <Link
                                    v-if="canRegister"
                                    :href="route('register')"
                                    class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#2563eb] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                >
                                    Register
                                </Link> -->
                            </template>
                        </nav>
                    </header>

                    <main class="mt-6">
                        <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">


                            <div
                                href="https://laracasts.com"
                                class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#2563eb] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#2563eb]"
                            >


                                <div class="pt-3 sm:pt-5">
                                    <h2
                                        class="text-xl font-semibold text-black dark:text-white"
                                    >
                                    Dijon販売管理 v1.1
                                    </h2>

                                    <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                        ※dijon_salesは移転リニューアルしました<br>
                                        　URLは　sales-mg.dijon1988.net　です。<br><br>

                                        　ID・パスワードは変更ありません。<br>
                                        　[log in]　からアクセスしてください。<br><br>

                                        　パスワードを忘れた場合　および<br>
                                        　このサイトに関する問い合わせは<br>
                                        　sales@dijon1988.net までお願いします。<br>
                                    </p>
                                </div>


                            </div>

                            <div
                                href="https://laravel-news.com"
                                class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#2563eb] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#2563eb]"
                            >


                                <div class="pt-3 sm:pt-5">
                                    <!-- <h2
                                        class="text-xl font-semibold text-black dark:text-white"
                                    >
                                        モデル企業
                                    </h2> -->

                                    <p class="mt-0 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">

                                        下記機能が追加されました。<br><br>

                                        ①各項目別の売上・粗利・在庫状況<br>
                                        （年推移・月推移・週推移・期間累計）<br>
                                        　　　および12ヶ月移動平均値の確認<br>
                                        ②各項目別消化率の確認<br>
                                        ③商品リストに画像がつきました<br>
                                        ④品番別の売上数推移（月・週）の確認<br>
                                        ⑤品番別売上ランキング(画像付)の確認<br>
                                        　
                                    </p>

                                </div>


                            </div>


                        </div>
                    </main>

                    <footer
                        class="py-16 text-center text-sm text-black dark:text-white/70"
                    >
                        Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
                    </footer>
                </div>
            </div>
        </div>
    </template>
