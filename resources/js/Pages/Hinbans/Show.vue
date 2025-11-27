<script setup>
    import { ref, onMounted } from 'vue'
    import axios from 'axios'
    import ChartSales from '@/Components/ChartSales.vue'
    import ResultHinbanTable from '@/Components/ResultHinbanTable.vue'
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Link } from '@inertiajs/vue3'

    const props = defineProps({
        id: [Number, String], // Number でも String でも OK
    })
    const hinban = ref(null)
    const skus = ref([])
    const weekly = ref([])
    const monthly = ref([])
    const mode = ref('weekly') // 'weekly' or 'monthly'

    onMounted(async () => {
        const res = await axios.get(`/api/hinbans/${props.id}`)
        hinban.value = res.data.hinban
        skus.value = res.data.skus
        weekly.value = res.data.weekly
        monthly.value = res.data.monthly
    })
    </script>

    <template>
        <AuthenticatedLayout>
            <div class="p-4">

                <!-- 戻るボタン -->

                <div class="flex" >
                <h2 class="text-xl font-bold mb-4">
                    品番詳細

                </h2>
                <div class="mt-2 ml-24">
                    <Link href="/analysis?tab=ranking"
                        class="px-4 py-2 text-white bg-indigo-500 rounded hover:bg-indigo-600">
                    分析に戻る
                    </Link>
                </div>
            </div>

                <!-- 基本情報 -->
                <div class="flex gap-4 mt-2 mb-0">
                    <img v-if="hinban?.thumbnail_url"
                         :src="`/storage/images/${hinban.thumbnail_url}`"
                         class="w-32 h-32 object-cover border" />



                    <div>
                        <p>品番ID: {{ hinban?.hinban_id }}</p>
                        <p>ブランド: {{ hinban?.brand_name }}</p>
                        <p>Unit: {{ hinban?.unit_id }}</p>
                        <p>品名: {{ hinban?. name }}</p>
                        <p>上代: ¥ {{ hinban?.price }}</p>
                    </div>
                </div>

                 <!-- SKU画像一覧 -->
                <div class="flex flex-wrap mt-0">
                    <div
                    v-for="sku in skus"
                    :key="sku.id"
                    class="w-20 md:w-20 p-2"
                    >
                    <div class="text-gray-700 mb-1">
                        Col: {{ sku.col_id }}
                    </div>

                    <!-- <Link :href="route('sku_image_show', { sku: sku.sku_id })"> -->

                    <div class="border rounded-md overflow-hidden">
                        <img
                        v-if="sku.filename"
                        :src="`/storage/sku_images/${sku.filename}`"
                        class="w-20 h-auto"
                        alt="SKU画像"
                        />

                    </div>
                    <!-- </Link> -->
                    </div>
                </div>

                <!-- 切替タブ -->
                <div class="mb-4">
                    <button
                        class="px-4 py-2 border rounded"
                        :class="mode === 'weekly' ? 'bg-blue-500 text-white' : ''"
                        @click="mode = 'weekly'"
                    >週次</button>

                    <button
                        class="px-4 py-2 border rounded"
                        :class="mode === 'monthly' ? 'bg-blue-500 text-white' : ''"
                        @click="mode = 'monthly'"
                    >月次</button>
                </div>

                ※売上数は全社合計値です


                <!-- Chart -->
                <ChartSales
                    v-if="mode === 'weekly'"
                    :labels="weekly.map(x => x.yw)"
                    :values="weekly.map(x => x.total)"
                />

                <ChartSales
                    v-if="mode === 'monthly'"
                    :labels="monthly.map(x => x.ym)"
                    :values="monthly.map(x => x.total)"
                />

                <!-- Table -->
                <ResultHinbanTable
                    v-if="mode === 'weekly'"
                    :rows="weekly"
                    type="weekly"
                />

                <ResultHinbanTable
                    v-if="mode === 'monthly'"
                    :rows="monthly"
                    type="monthly"
                />

            </div>
        </AuthenticatedLayout>
    </template>
