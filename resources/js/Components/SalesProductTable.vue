<script setup>
    import { ref, watch } from 'vue';
    import { Link } from '@inertiajs/vue3';
    import axios from 'axios';
    import ImageThumbnail from '@/Components/ImageThumbnail.vue';
    import Pagination2 from '@/Components/Pagination2.vue';

    const props = defineProps({
      filters: { type: Object, required: true },
    });

    // 初期値は API レスポンスの想定形に合わせると安全
    const datas = ref({
      data: [],
      links: [],
    });

    const loading = ref(false);

    // データ取得
    const fetchData = async (page = 1) => {
      loading.value = true;
      try {
        const res = await axios.get('/api/sales-products', {
          params: { ...props.filters, page, type: 'ranking' },
        });

        // 念のためデータ構造の崩れ防止
        datas.value = {
          data: res.data?.data ?? [],
          links: res.data?.links ?? [],
        };

      } catch (e) {
        console.error(e);
        datas.value = { data: [], links: [] };
      } finally {
        loading.value = false;
      }
    };

    // filters 変更監視（初期ロード含む）
    watch(
        () => props.filters.triggerRanking,
        (newVal, oldVal) => {
          // ボタン押下後のみ fetchData
          fetchData(1);
        }
      );

    // ページ切替
    const changePage = (url) => {
        const page = new URL(url).searchParams.get('page') ?? 1;
        fetchData(page);
      };
    </script>

    <template>
      <div>
        <div v-if="loading" class="text-sm text-gray-500 mb-2">
          読み込み中...
        </div>

        <div v-if="!datas.data.length" class="text-left text-sm py-2 ">
           ※ 統合品番管理の社店では表示されません
        </div>

        <table class="table-auto w-full border">
          <thead class="bg-gray-100">
            <tr>
              <th class="py-1">品番</th>
              <th class="text-center hidden sm:table-cell">品名</th>
              <th class="text-center">元売価</th>
              <th class="text-center">売上数</th>
              <th class="text-center">画像</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="d in datas.data"
              :key="d.hinban_id"
            >
              <td class="text-center text-indigo-600" style="font-variant-numeric:tabular-nums">
                <Link :href="route('hinbans.show2', { id: d.hinban_id })">{{ d.hinban_id }}
                </Link></td>
              <td class="text-center hidden sm:table-cell">
                {{ d.hinban_name }}
              </td>
              <td class="text-center" style="font-variant-numeric:tabular-nums">{{ d.m_price }}</td>
              <td class="text-center" style="font-variant-numeric:tabular-nums">{{ d.pcs_total }}</td>

              <td class="text-center">
                <Link :href="route('hinbans.show2', { id: d.hinban_id })">
                  <div class="w-20 mx-auto overflow-hidden">
                    <ImageThumbnail :filename="d.filename" />
                  </div>
                </Link>
              </td>
            </tr>
          </tbody>

        </table>



        <!-- ページネーション -->
        <Pagination2
            :links="datas.links"
            class="mt-4"
            @change="changePage"
        />
      </div>
    </template>

