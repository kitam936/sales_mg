<script setup>
    import { ref, watch } from 'vue';
    import axios from 'axios';
    import Pagination3 from '@/Components/Pagination3.vue';

    const props = defineProps({
      filters: { type: Object, required: true },
      fetchTrigger: { type: Number, required: true },
    });

    const datas = ref({
      data: [],
      links: [],
      current_page: 1,
      last_page: 1,
      total: 0,
    });

    const getStockData = async (page = 1) => {
      try {
        const res = await axios.get('/api/stock', {
          params: { page, ...props.filters },
        });
        datas.value = {
          data: res.data.data || [],
          links: res.data.links || [],
          current_page: res.data.current_page || 1,
          last_page: res.data.last_page || 1,
          total: res.data.total || 0,
        };
      } catch (e) {
        console.error(e);
      }
    };

    watch(() => props.fetchTrigger, () => getStockData(1));

    const changePage = (url) => {
      if (!url) return;
      const urlObj = new URL(url, window.location.origin);
      const page = urlObj.searchParams.get('page') || 1;
      getStockData(page);
    };
    </script>

    <template>
      <div class="overflow-x-auto">
        <table class="w-full text-sm border border-gray-200">
          <thead>
            <tr class="bg-gray-100">
              <th class="px-2 py-1 text-left">ID</th>
              <th class="px-2 py-1 text-left">名称</th>
              <th class="px-2 py-1 text-center">数</th>
              <th class="px-2 py-1 text-center">コスト</th>
              <th class="px-2 py-1 text-center">上代</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in datas.data" :key="row.id" class="border-b">
              <td class="px-2 py-1"  style="font-variant-numeric:tabular-nums">{{ row.id ?? 99}}</td>
              <td class="px-2 py-1"  style="font-variant-numeric:tabular-nums">{{ row.name ?? 'その他'}}</td>
              <td class="px-2 py-1 text-right"  style="font-variant-numeric:tabular-nums">{{ Number(row.stock_total).toLocaleString() }}</td>
              <td class="px-2 py-1 text-right"  style="font-variant-numeric:tabular-nums"> {{ Math.floor(Number(row.stock_cost_total)/1000).toLocaleString() }}</td>
              <td class="px-2 py-1 text-right"  style="font-variant-numeric:tabular-nums">{{ Math.floor(Number(row.stock_zaikogaku_total)/1000).toLocaleString() }}</td>
            </tr>
            <tr v-if="!datas.data.length">
              <td colspan="4" class="text-center py-2 text-gray-500">該当データがありません</td>
            </tr>
          </tbody>
        </table>

        <div class="mt-2">
          <Pagination3 :links="datas.links" @change="changePage" />
        </div>
      </div>
    </template>

