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

    // デシル分析かどうかの判定
    const isDecile = computed(() => props.type === 'decile');

    // 車種別分析かどうかの判定
    const isCarCategory = computed(() => props.type === 'CarCategory');

    // STAFF別分析かどうかの判定
    const isStaff = computed(() => props.type === 'Staff');
    </script>

    <template>
      <div class="w-2/3 mx-auto sm:px-4 lg:px-4 border">
        <table class="bg-white table-auto w-full text-center whitespace-no-wrap">
          <thead>
            <tr>
              <!-- 通常集計 -->
              <th v-if="!isDecile && !isCarCategory && !isStaff" class="px-2 py-1 bg-gray-100">年月日</th>
              <th v-if="!isDecile && !isCarCategory && !isStaff" class="px-2 py-1 bg-gray-100">Total</th>
              <th v-if="props.type === 'perMonth'" class="px-2 py-1 bg-gray-100">12ヶ月移動平均</th>

              <!-- デシル分析 -->
              <th v-if="isDecile" class="px-2 py-1 bg-gray-100">グループ</th>
              <th v-if="isDecile" class="px-2 py-1 bg-gray-100">平均</th>
              <th v-if="isDecile" class="px-2 py-1 bg-gray-100">合計金額</th>
              <th v-if="isDecile" class="px-2 py-1 bg-gray-100">構成比</th>

              <!-- 車種別分析 -->
              <th v-if="isCarCategory" class="px-2 py-1 bg-gray-100">車種名</th>
              <th v-if="isCarCategory" class="px-2 py-1 bg-gray-100">合計金額</th>

              <!-- STAFF別分析 -->
              <th v-if="isStaff" class="px-2 py-1 bg-gray-100">STAFF名</th>
              <th v-if="isStaff" class="px-2 py-1 bg-gray-100">合計金額</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in props.data.data" :key="item.id || item.decile || item.date">
              <!-- 通常集計 -->
              <td v-if="!isDecile && !isCarCategory && !isStaff" class="border px-2 py-1" style="font-variant-numeric:tabular-nums">{{ item.date }}</td>
              <td v-if="!isDecile && !isCarCategory && !isStaff" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Number(item.total ?? 0).toLocaleString() }}</td>

              <!-- perMonth の場合だけ移動平均 -->
              <td v-if="props.type === 'perMonth'" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">
                {{
                  (props.data.movingAverages && props.data.movingAverages[index] != null)
                    ? Number(props.data.movingAverages[index]).toLocaleString()
                    : '-'
                }}
              </td>

              <!-- デシル分析 -->
              <td v-if="isDecile" class="border px-2 py-1">{{ item.decile }}</td>
              <td v-if="isDecile" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Number(item.avg ?? 0).toLocaleString() }}</td>
              <td v-if="isDecile" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Number(item.totalPerGroup ?? 0).toLocaleString() }}</td>
              <td v-if="isDecile" class="border px-2 py-1 text-right">{{ Number(item.ratio ?? 0).toLocaleString() }}%</td>

              <!-- 車種別分析 -->
              <td v-if="isCarCategory" class="border px-2 py-1">{{ item.car_name }}</td>
              <td v-if="isCarCategory" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Number(item.total ?? 0).toLocaleString() }}</td>

              <!-- STAFF別分析 -->
              <td v-if="isStaff" class="border px-2 py-1">{{ item.staff_name }}</td>
              <td v-if="isStaff" class="border px-2 py-1 text-right" style="font-variant-numeric:tabular-nums">{{ Number(item.total ?? 0).toLocaleString() }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>
