<script setup>
    import { ref } from 'vue'
    import { useForm, router } from '@inertiajs/vue3'
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

    const props = defineProps({
      companies: Array,
      areas: Array,
      shops: Array,
      filter: Object, // Laravel から選択値を受け取る
    })

    const form = useForm({
      sh_id: '',
      report: '',
      image1: null,
      image2: null,
      image3: null,
      image4: null
    })

    // フィルタ初期値を ref で包む
    const co_id = ref(props.filter?.co_id ? Number(props.filter.co_id) : null)
    const area_id = ref(props.filter?.area_id ? Number(props.filter.area_id) : null)

    // フィルタ変更時に再読み込み
    const reloadFiltered = () => {
      const params = {}
      if (co_id.value) params.co_id = co_id.value
      if (area_id.value) params.area_id = area_id.value

      router.get(route('reports.create'), params, { preserveState: true })
    }

    // フィルタリセット
    const resetFilters = () => {
      co_id.value = null
      area_id.value = null
      reloadFiltered()
    }

    // 登録処理
    const submit = () => {
      form.post(route('reports.store'), {
        forceFormData: true,
        onSuccess: () => form.reset()
      })
    }

    // 戻るボタンの処理
    const goBack = () => {
        window.history.back();
    };
    </script>

    <template>
      <AuthenticatedLayout>
        <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">店舗Report登録 </h2>
        <div class="ml-2 flex mt-2 mb-4">
        <button
            type="button"
            @click="goBack"
            class="w-40 h-10 text-sm text-gray-700 bg-gray-200 border-0 py-1 px-2 hover:bg-gray-300 rounded"
          >
            戻る
          </button>
        </div>





          <div class="mb-0 flex space-x-2">
            <select v-model.number="co_id" @change="reloadFiltered" class="w-28 h-8 rounded text-sm  py-1">
              <option :value="null">社選択</option>
              <option v-for="co in props.companies" :key="co.id" :value="co.id">{{ co.co_name }}</option>
            </select>

            <select v-model.number="area_id" @change="reloadFiltered" class="w-28 h-8 rounded text-sm  py-1">
              <option :value="null">エリア選択</option>
              <option v-for="ar in props.areas" :key="ar.id" :value="ar.id">{{ ar.area_name }}</option>
            </select>

            <button type="button" @click="resetFilters"
              class="w-24 h-8 text-sm text-white bg-blue-500 rounded hover:bg-blue-700">
              選択リセット
            </button>
          </div>
        </template>

        <div class="p-1 bg-white rounded shadow">
          <form @submit.prevent="submit" enctype="multipart/form-data" class="ml-2">
            <select v-model="form.sh_id" class="w-32 h-8 mb-2 rounded text-sm  py-1">
              <option value="">店舗選択</option>
              <option v-for="s in props.shops" :key="s.id" :value="s.id">{{ s.shop_name }}</option>
            </select>

            <textarea v-model="form.report" rows="8" required
              class="w-full bg-gray-100 rounded border border-gray-300 focus:ring-2 focus:ring-indigo-200 p-2 text-base"></textarea>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
              <div v-for="i in 4" :key="i">
                <input type="file" :id="'image' + i" :name="'image' + i" @input="form['image'+i]=$event.target.files[0]"
                  accept="image/png,image/jpeg,image/jpg"
                  class="w-full bg-gray-100 rounded border border-gray-300 text-sm p-1"/>
              </div>
            </div>

            <div class="mt-6 mb-16 flex justify-center">
              <button type="submit"
                class="w-40 h-10 text-sm text-white bg-pink-500 rounded hover:bg-pink-700">
                登録
              </button>
            </div>
          </form>
        </div>
      </AuthenticatedLayout>
    </template>
