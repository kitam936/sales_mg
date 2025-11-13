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

    const formattedRows = computed(() => {
        return props.data.rows.map(r => ({
            ...r,
            sales_rate_display: r.sales_rate !== null ? r.sales_rate + '%' : '-',
            profit_rate_display: r.profit_rate !== null ? r.profit_rate + '%' : '-',
            sales_class: r.sales_rate > 100 ? 'text-blue-600' : (r.sales_rate < 100 ? 'text-red-600' : ''),
            profit_class: r.profit_rate > 100 ? 'text-blue-600' : (r.profit_rate < 100 ? 'text-red-600' : ''),
        }));
    });
    </script>

    <template>
    <div class="w-full mx-auto sm:px-4 lg:px-4 border">
        <table class="bg-white table-auto w-full text-center whitespace-no-wrap">
            <thead>
                <tr>
                    <th class="px-2 py-1 text-sm bg-gray-100">年月日</th>
                    <th class="px-2 py-1 text-sm bg-gray-100">売上</th>
                    <th class="px-2 py-1 text-sm bg-gray-100 hidden sm:table-cell">前年売上</th>
                    <th class="px-2 py-1 text-sm bg-gray-100">前年比</th>
                    <th class="px-2 py-1 text-sm bg-gray-100">粗利</th>
                    <th class="px-2 py-1 text-sm bg-gray-100 hidden sm:table-cell">前年粗利</th>
                    <th class="px-2 py-1 text-sm bg-gray-100">前年比</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(r, index) in formattedRows" :key="`${r.period}-${index}`">
                    <td class="border px-2 py-1 text-sm "style="font-variant-numeric:tabular-nums">{{ r.period }}</td>
                    <td class="border px-2 py-1 text-sm text-right" style="font-variant-numeric:tabular-nums">{{ Math.floor(r.sales / 1000).toLocaleString() }}</td>
                    <td class="border px-2 py-1 text-sm text-right hidden sm:table-cell" style="font-variant-numeric:tabular-nums">{{ Math.floor(r.sales_prev / 1000).toLocaleString() }}</td>
                    <td class="border px-2 py-1 text-sm text-right" :class="r.sales_class" style="font-variant-numeric:tabular-nums">{{ r.sales_rate_display }}</td>
                    <td class="border px-2 py-1 text-sm text-right" style="font-variant-numeric:tabular-nums">{{ Math.floor(r.profit / 1000).toLocaleString() }}</td>
                    <td class="border px-2 py-1 text-sm text-right hidden sm:table-cell" style="font-variant-numeric:tabular-nums">{{ Math.floor(r.profit_prev / 1000).toLocaleString() }}</td>
                    <td class="border px-2 py-1 text-sm text-right" :class="r.profit_class" style="font-variant-numeric:tabular-nums">{{ r.profit_rate_display }}</td>
                </tr>
                <tr v-if="formattedRows.length === 0">
                    <td colspan="7" class="py-4 text-center text-gray-500">データがありません</td>
                </tr>
            </tbody>
        </table>
    </div>
    </template>
