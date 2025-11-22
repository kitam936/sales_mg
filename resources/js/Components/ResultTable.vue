<script setup>
    import { computed } from 'vue';

    const props = defineProps({
        data: {
            type: Object,
            required: true
        },
        type: {
            type: String,
            required: true
        }
    });

    // type=pyかどうかの判定
    const isPY = computed(() => props.type === 'py');

    // type=pmかどうかの判定
    const isPM = computed(() => props.type === 'pm');

    // type=pwかどうかの判定
    const isPW = computed(() => props.type === 'pw');
    </script>

    <template>
        <div class="w-full mx-auto sm:px-4 lg:px-4 border">
        <table class="bg-white table-auto w-full text-center whitespace-no-wrap">
            <thead>
            <tr>
                <!-- 通常集計 -->
                <th v-if="isPY || isPM || isPW" class="px-2 py-1 bg-gray-100">年・月・週</th>
                <th v-if="isPY || isPM || isPW" class="px-2 py-1 bg-gray-100">合計売上</th>
                <th v-if="isPY || isPM || isPW" class="px-2 py-1 bg-gray-100">合計粗利</th>
                <th v-if="isPY || isPM || isPW" class="px-2 py-1 bg-gray-100">粗利率</th>
                <th v-if="isPM" class="px-2 py-1 bg-gray-100 hidden sm:table-cell">12ヶ月移動平均</th>
                <th v-if="isPM" class="px-2 py-1 bg-gray-100 hidden sm:table-cell">12ヶ月移動平均粗利</th>

                <!-- ComapnyTotal -->
                <th v-if="props.type === 'co_total'" class="px-2 py-1 bg-gray-100">社名</th>
                <th v-if="props.type === 'co_total'" class="px-2 py-1 bg-gray-100">合計売上</th>
                <th v-if="props.type === 'co_total'" class="px-2 py-1 bg-gray-100">合計粗利</th>
                <th v-if="props.type === 'co_total'" class="px-2 py-1 bg-gray-100">粗利率</th>
                <!-- ShopTotal -->
                <th v-if="props.type === 'sh_total'" class="px-2 py-1 bg-gray-100">店名</th>
                <th v-if="props.type === 'sh_total'" class="px-2 py-1 bg-gray-100">合計売上</th>
                <th v-if="props.type === 'sh_total'" class="px-2 py-1 bg-gray-100">合計粗利</th>
                <th v-if="props.type === 'sh_total'" class="px-2 py-1 bg-gray-100">粗利率</th>

                <!-- picTotal -->
                <th v-if="props.type === 'pic_total'" class="px-2 py-1 bg-gray-100">担当者名</th>
                <th v-if="props.type === 'pic_total'" class="px-2 py-1 bg-gray-100">合計売上</th>
                <th v-if="props.type === 'pic_total'" class="px-2 py-1 bg-gray-100">合計粗利</th>
                <th v-if="props.type === 'pic_total'" class="px-2 py-1 bg-gray-100">粗利率</th>

                <!-- BrandTotal -->
                <th v-if="props.type === 'bd_total'" class="px-2 py-1 bg-gray-100">ブランド名</th>
                <th v-if="props.type === 'bd_total'" class="px-2 py-1 bg-gray-100">合計売上</th>
                <th v-if="props.type === 'bd_total'" class="px-2 py-1 bg-gray-100">合計粗利</th>
                <th v-if="props.type === 'bd_total'" class="px-2 py-1 bg-gray-100">粗利率</th>

                <!-- SeasonTotal -->
                <th v-if="props.type === 'ss_total'" class="px-2 py-1 bg-gray-100">季節名</th>
                <th v-if="props.type === 'ss_total'" class="px-2 py-1 bg-gray-100">合計売上</th>
                <th v-if="props.type === 'ss_total'" class="px-2 py-1 bg-gray-100">合計粗利</th>
                <th v-if="props.type === 'ss_total'" class="px-2 py-1 bg-gray-100">粗利率</th>

                <!-- UnitTotal -->
                <th v-if="props.type === 'un_total'" class="px-2 py-1 bg-gray-100">Unit</th>
                <th v-if="props.type === 'un_total'" class="px-2 py-1 bg-gray-100">合計売上</th>
                <th v-if="props.type === 'un_total'" class="px-2 py-1 bg-gray-100">合計粗利</th>
                <th v-if="props.type === 'un_total'" class="px-2 py-1 bg-gray-100">粗利率</th>

                <!-- FaceTotal -->
                <th v-if="props.type === 'fa_total'" class="px-2 py-1 bg-gray-100">Face</th>
                <th v-if="props.type === 'fa_total'" class="px-2 py-1 bg-gray-100">合計売上</th>
                <th v-if="props.type === 'fa_total'" class="px-2 py-1 bg-gray-100">合計粗利</th>
                <th v-if="props.type === 'fa_total'" class="px-2 py-1 bg-gray-100">粗利率</th>

                <!-- DesignerTotal -->
                <th v-if="props.type === 'de_total'" class="px-2 py-1 bg-gray-100">デザイナー名</th>
                <th v-if="props.type === 'de_total'" class="px-2 py-1 bg-gray-100">合計売上</th>
                <th v-if="props.type === 'de_total'" class="px-2 py-1 bg-gray-100">合計粗利</th>
                <th v-if="props.type === 'de_total'" class="px-2 py-1 bg-gray-100">粗利率</th>

            </tr>
            </thead>

            <tbody>
            <tr v-for="(item, index) in props.data.data" :key="item.id || item.date">
                <!-- 通常集計 -->
                <td v-if="isPY || isPM || isPW" class="border px-2 py-1" style="font-variant-numeric:tabular-nums">{{ item.date }}</td>
                <td v-if="isPY || isPM || isPW" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.floor(Number(item.total ?? 0) / 1000).toLocaleString() }}</td>
                <td v-if="isPY || isPM || isPW" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.floor(Number(item.total_profit ?? 0) / 1000).toLocaleString() }}</td>
                <td v-if="isPY || isPM || isPW" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">
                    {{
                    (item.total && item.total_profit)
                        ? Math.round(((item.total_profit ?? 0) / (item.total ?? 0) * 100) * 10) / 10
                        : 0
                    }}
                </td>

                <!-- perMonth の場合だけ移動平均 -->
                <td v-if="props.type === 'pm'" class="border px-2 py-1 text-right hidden sm:table-cell" style="font-variant-numeric:tabular-nums">
                    {{
                    (props.data.movingAverages && props.data.movingAverages[index] != null)
                        ? Math.floor(Number(props.data.movingAverages[index]) / 1000).toLocaleString()
                        : '-'
                    }}
                </td>
                <td v-if="props.type === 'pm'" class="border px-2 py-1 text-right hidden sm:table-cell" style="font-variant-numeric:tabular-nums">
                    {{
                    (props.data.movingAveragesProfit && props.data.movingAveragesProfit[index] != null)
                        ? Math.floor(Number(props.data.movingAveragesProfit[index]) / 1000).toLocaleString()
                        : '-'
                    }}
                </td>


                <!-- CompanyTotal -->
                <td v-if="props.type === 'co_total'" class="border px-2 py-1">{{ item.co_name }}</td>
                <td v-if="props.type === 'co_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.floor(Number(item.total ?? 0) / 1000).toLocaleString() }}</td>
                <td v-if="props.type === 'co_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.floor(Number(item.total_profit ?? 0) / 1000).toLocaleString() }}</td>
                <td v-if="props.type === 'co_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.round(((item.total_profit ?? 0) / (item.total ?? 0) * 100) * 10) / 10 }}</td>
                <!-- ShopTotal -->
                <td v-if="props.type === 'sh_total'" class="border px-2 py-1">{{ item.shop_name }}</td>
                <td v-if="props.type === 'sh_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.floor(Number(item.total ?? 0) / 1000).toLocaleString() }}</td>
                <td v-if="props.type === 'sh_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.floor(Number(item.total_profit ?? 0) / 1000).toLocaleString() }}</td>
                <td v-if="props.type === 'sh_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.round(((item.total_profit ?? 0) / (item.total ?? 0) * 100) * 10) / 10 }}</td>

                <!-- picTotal -->
                <td v-if="props.type === 'pic_total'" class="border px-2 py-1">{{ item.pic_name }}</td>
                <td v-if="props.type === 'pic_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.floor(Number(item.total ?? 0) / 1000).toLocaleString() }}</td>
                <td v-if="props.type === 'pic_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.floor(Number(item.total_profit ?? 0) / 1000).toLocaleString() }}</td>
                <td v-if="props.type === 'pic_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.round(((item.total_profit ?? 0) / (item.total ?? 0) * 100) * 10) / 10 }}</td>

                <!-- BrandTotal -->
                <td v-if="props.type === 'bd_total'" class="border px-2 py-1">{{ item.brand_name }}</td>
                <td v-if="props.type === 'bd_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.floor(Number(item.total ?? 0) / 1000).toLocaleString() }}</td>
                <td v-if="props.type === 'bd_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.floor(Number(item.total_profit ?? 0) / 1000).toLocaleString() }}</td>
                <td v-if="props.type === 'bd_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.round(((item.total_profit ?? 0) / (item.total ?? 0) * 100) * 10) / 10 }}</td>

                <!-- SeasonTotal -->
                <td v-if="props.type === 'ss_total'" class="border px-2 py-1">{{ item.season_name }}</td>
                <td v-if="props.type === 'ss_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.floor(Number(item.total ?? 0) / 1000).toLocaleString() }}</td>
                <td v-if="props.type === 'ss_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.floor(Number(item.total_profit ?? 0) / 1000).toLocaleString() }}</td>
                <td v-if="props.type === 'ss_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.round(((item.total_profit ?? 0) / (item.total ?? 0) * 100) * 10) / 10 }}</td>

                <!-- UnitTotal -->
                <td v-if="props.type === 'un_total'" class="border px-2 py-1">{{ item.unit_code.slice(-2) }}</td>
                <td v-if="props.type === 'un_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.floor(Number(item.total ?? 0) / 1000).toLocaleString() }}</td>
                <td v-if="props.type === 'un_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.floor(Number(item.total_profit ?? 0) / 1000).toLocaleString() }}</td>
                <td v-if="props.type === 'un_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.round(((item.total_profit ?? 0) / (item.total ?? 0) * 100) * 10) / 10 }}</td>

                <!-- FaceTotal -->
                <td v-if="props.type === 'fa_total'" class="border px-2 py-1">{{ item.face }}</td>
                <td v-if="props.type === 'fa_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.floor(Number(item.total ?? 0) / 1000).toLocaleString() }}</td>
                <td v-if="props.type === 'fa_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.floor(Number(item.total_profit ?? 0) / 1000).toLocaleString() }}</td>
                <td v-if="props.type === 'fa_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.round(((item.total_profit ?? 0) / (item.total ?? 0) * 100) * 10) / 10 }}</td>

                <!-- DesignerTotal -->
                <td v-if="props.type === 'de_total'" class="border px-2 py-1">{{ item.designer_name }}</td>
                <td v-if="props.type === 'de_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.floor(Number(item.total ?? 0) / 1000).toLocaleString() }}</td>
                <td v-if="props.type === 'de_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.floor(Number(item.total_profit ?? 0) / 1000).toLocaleString() }}</td>
                <td v-if="props.type === 'de_total'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Math.round(((item.total_profit ?? 0) / (item.total ?? 0) * 100) * 10) / 10 }}</td>

            </tr>
            </tbody>
        </table>
        </div>
    </template>
