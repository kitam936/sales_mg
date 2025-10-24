<script setup>
    import { ref, watch } from 'vue'
    import { useForm, router } from '@inertiajs/vue3'
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
    import { Inertia } from '@inertiajs/inertia'
    import FlashMessage from '@/Components/FlashMessage.vue'

    const props = defineProps({
      reports: Object,   // Laravelから渡されるページネート済みreports
      areas: Array,
      companies: Array,
      filters: Object    // co_id, ar_id, sh_name の初期値
    })

    // フィルタの初期値
    const co_id = ref(props.filters?.co_id ?? null)
    const ar_id = ref(props.filters?.ar_id ?? null)
    const sh_name = ref(props.filters?.sh_name ?? '')

    // フィルタ変更時にページ再読み込み
    const submitFilters = () => {
      router.get(route('reports.index'), {
        co_id: co_id.value,
        ar_id: ar_id.value,
        sh_name: sh_name.value
      }, { preserveState: true, replace: true })
    }
    </script>

    <template>
      <AuthenticatedLayout>
        <template #header>
          <h2 class="mb-2 font-semibold text-xl text-gray-800 leading-tight">店舗Report一覧</h2>
          <FlashMessage />
          <div class="flex space-x-4 mb-4">

            <button
              class="w-32 h-8 text-white bg-indigo-500 hover:bg-indigo-700 rounded"
              @click="$inertia.visit(route('menu'))"
            >
              Menu
            </button>
            <button
              class="w-32 h-8 text-white bg-indigo-500 hover:bg-indigo-700 rounded"
              @click="$inertia.visit(route('reports.create'))"
            >
              新規Report
            </button>
          </div>

          <form @submit.prevent="submitFilters" class="mt-4 space-y-2">
            <span class="text-sm text-gray-800">※エリア・会社を選択してください</span>
            <div class="md:flex space-x-2">
              <select v-model="ar_id" @change="submitFilters" class="w-32 h-8 rounded text-sm">
                <option value="null">全エリア</option>
                <option v-for="area in areas" :key="area.id" :value="area.id">{{ area.area_name }}</option>
              </select>

              <select v-model="co_id" @change="submitFilters" class="w-32 h-8 rounded text-sm">
                <option value="null">全社</option>
                <option v-for="co in companies" :key="co.id" :value="co.id">{{ co.co_name }}</option>
              </select>

              <input
                v-model="sh_name"
                @change="submitFilters"
                placeholder="店名検索入力(一部でも)"
                class="w-52 h-8 rounded text-sm"
              />

              <button type="button"
                      @click="() => { co_id = null; ar_id = null; sh_name = ''; submitFilters() }"
                      class="w-20 h-8 bg-blue-500 text-white hover:bg-blue-600 rounded"
              >
                全表示
              </button>
            </div>
          </form>
        </template>

        <div class="py-6 border">
          <div class="mx-auto sm:px-4 lg:px-4 border">
            <table class="w-full table-auto text-center bg-white">
              <thead>
                <tr class="bg-gray-100 text-sm">
                  <th class="py-1 px-2">ID</th>
                  <th class="py-1 px-2">Date</th>
                  <th class="py-1 px-2">社名</th>
                  <th class="py-1 px-2">店名</th>
                  <th class="py-1 px-2">コメント数</th>
                  <th class="py-1 px-2">状態</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="report in reports.data" :key="report.id" class="text-sm">
                  <td class="py-1 px-2">{{ report.id }}</td>
                  <td class="py-1 px-2">
                    <a :href="route('reports.show', { report: report.id })" class="text-indigo-500">
                      {{ new Date(report.created_at).toLocaleDateString('ja-JP', { year:'2-digit', month:'2-digit', day:'2-digit' }) }}
                    </a>
                  </td>
                  <td class="py-1 px-2">{{ report.co_name }}</td>
                  <td class="py-1 px-2">{{ report.shop_name }}</td>
                  <td class="py-1 px-2">{{ report.comment_count }}</td>
                  <td class="py-1 px-2" :class="{'text-red-600': !report.report_reads || (report.comment_count>0 && !report.comment_reads)}">
                    {{ (report.comment_count>0 && report.report_reads && report.comment_reads) || (report.comment_count===0 && report.report_reads) ? '既読' : '未読' }}
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- ページネーション -->
            <Pagination :links="reports.links" class="mt-4" ></Pagination>
          </div>
        </div>
      </AuthenticatedLayout>
    </template>
