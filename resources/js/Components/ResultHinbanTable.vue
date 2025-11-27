<script setup>
    const props = defineProps({
        rows: Array,
        type: String, // weekly | monthly
    })
    </script>

    <template>
        <table class="w-full mt-6 border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-2 py-1">
                        {{ type === 'weekly' ? '年週' : '年月' }}
                    </th>
                    <th class="border px-2 py-1">売上数</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="r in rows" :key="r.yw || r.ym">
                    <td class="border px-2 py-1" style="font-variant-numeric:tabular-nums">{{ r.yw || r.ym }}</td>
                    <td class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ r.total }}</td>
                </tr>
            </tbody>
        </table>
    </template>
