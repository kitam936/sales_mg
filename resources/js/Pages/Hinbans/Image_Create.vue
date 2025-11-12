<template>
    <div class="p-6">
      <!-- ヘッダー -->
      <div class="flex justify-between items-center mb-6">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          画像アップロード
        </h2>

      </div>
      <div class="ml-8">
        <div class="p-2 ">
            <Link as="button" :href="route('admin.data.data_menu')" class="w-40 h-8 flex text-white bg-indigo-500 border-0 py-1 pl-12 focus:outline-none hover:bg-indigo-600 rounded text-ml">Data管理</Link>
        </div>
        </div>

      <!-- フラッシュメッセージ -->
      <FlashMessage :status="status" />

      <!-- 品番画像アップロード -->
      <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
        <h2 class="font-semibold text-lg text-gray-800 mb-4">品番画像</h2>

        <form @submit.prevent="submitHinban">
          <div class="flex items-center gap-6">
            <div class="w-1/2">
              <label class="leading-7 text-sm text-gray-600">品番画像</label>
              <input
                type="file"
                multiple
                accept="image/png,image/jpeg,image/jpg"
                @change="handleHinbanFiles"
                class="w-full bg-gray-100 rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-1 px-3 text-gray-700 outline-none"
              />
            </div>

            <div class="flex mt-6">
              <button
                type="submit"
                class="h-10 text-white bg-indigo-500 py-2 px-8 rounded text-lg hover:bg-indigo-600"
              >
                登録する
              </button>
            </div>
          </div>
        </form>
      </div>

      <!-- SKU画像アップロード -->
      <div class="bg-white shadow-sm rounded-lg p-6">
        <h2 class="font-semibold text-lg text-gray-800 mb-4">SKU画像</h2>

        <form @submit.prevent="submitSku">
          <div class="flex items-center gap-6">
            <div class="w-1/2">
              <label class="leading-7 text-sm text-gray-600">SKU画像</label>
              <input
                type="file"
                multiple
                accept="image/png,image/jpeg,image/jpg"
                @change="handleSkuFiles"
                class="w-full bg-gray-100 rounded border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-1 px-3 text-gray-700 outline-none"
              />
            </div>

            <div class="flex mt-6">
              <button
                type="submit"
                class="h-10 text-white bg-indigo-500 py-2 px-8 rounded text-lg hover:bg-indigo-600"
              >
                登録する
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </template>

  <script setup>
  import { ref } from 'vue'
  import { useForm } from '@inertiajs/vue3'
  import FlashMessage from '@/Components/FlashMessage.vue'
  import { Link } from '@inertiajs/vue3'

  const props = defineProps({
    status: String
  })

  // 品番画像アップロード用フォーム
  const hinbanForm = useForm({
    files: []
  })

  // SKU画像アップロード用フォーム
  const skuForm = useForm({
    files: []
  })

  const handleHinbanFiles = (e) => {
    hinbanForm.files = Array.from(e.target.files)
  }

  const handleSkuFiles = (e) => {
    skuForm.files = Array.from(e.target.files)
  }

  const submitHinban = () => {
    hinbanForm.post(route('admin.image_store'), {
      forceFormData: true,
    })
  }

  const submitSku = () => {
    skuForm.post(route('admin.sku_image_store'), {
      forceFormData: true,
    })
  }
  </script>

