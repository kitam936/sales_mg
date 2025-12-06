<script setup>
    import { Link } from '@inertiajs/vue3';

    const props = defineProps({
      links: Array,
      filters: Object,  // ★検索条件
    });

    // 検索条件をクエリとして付けるための関数
    function filterUrl(url) {
      if (!url) return null;

      const queryString = new URLSearchParams(props.filters).toString();

      // すでに ?page= が含まれているので & でつなぐ
      return url + '&' + queryString;
    }
    </script>

    <template>
      <div v-if="links.length > 3">
        <div class="flex flex-wrap -mb-1">
          <template v-for="(link, index) in links" :key="index">

            <!-- disabled -->
            <div
              v-if="link.url === null"
              class="mr-1 mb-1 px-4 py-3 text-sm leading-4
                     text-gray-400 border rounded"
              v-html="link.label"
            />

            <!-- enabled links -->
            <Link
              v-else
              class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded
                     hover:bg-white focus:border-indigo-500 focus:text-indigo-500"
              :class="{ 'bg-blue-700 text-white': link.active }"
              :href="filterUrl(link.url)"
              v-html="link.label"
            />
          </template>
        </div>
      </div>
    </template>
