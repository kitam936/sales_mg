<script setup>
    import { ref, watch, onMounted } from 'vue';
    import axios from 'axios';
    import ImageThumbnail from '@/Components/ImageThumbnail.vue';
    import Pagination2 from '@/Components/Pagination2.vue';

    const props = defineProps({
      filters: { type: Object, required: true },
    });

    // データ本体
    const datas = ref({
      data: [],
      links: [],
    });

    // データ取得
    const getShoukaData = async (page = 1) => {
      try {
        const res = await axios.get('/api/sales-digest', {
          params: {
            page,
            shoukaType: props.filters.shoukaType,
            company_id: props.filters.company_id,
            shop_id: props.filters.shop_id,
            brand_id: props.filters.brand_id,
            season_id: props.filters.season_id,
            unit_id: props.filters.unit_id,
            face: props.filters.face,
            designer_id: props.filters.designer_id,
          },
        });

        datas.value = res.data;
      } catch (e) {
        console.log(e);
      }
    };

    // 初回ロード
    onMounted(() => {
      getShoukaData();
    });

    // 絞込 / 期間 / shoukaType 変更時に再取得
    watch(
      () => props.filters,
      () => {
        getShoukaData();
      },
      { deep: true }
    );

    // Pagination
    const changePage = (url) => {
      if (!url) return;
      const page = url.split('page=')[1];
      getShoukaData(page);
    };
    </script>

    <template>
      <div>
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-100">
              <th class="px-2 py-1">name</th>
              <th class="px-2 py-1">売上数</th>
              <th class="px-2 py-1">在庫数</th>
              <th class="px-2 py-1">消化率</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="row in datas.data" :key="row.id">
              <td class="px-2 py-1">{{ row.name }}</td>
              <td class="px-2 py-1">{{ row.sales_total }}</td>
              <td class="px-2 py-1">{{ row.stock_total }}</td>
              <td class="px-2 py-1 font-bold">
                {{ row.rate }}%
              </td>
            </tr>
          </tbody>
        </table>

        <!-- ページネーション -->
        <Pagination2
          :links="datas.links"
          @change-page="changePage"
        />
      </div>
    </template>

