<script setup>
    const props = defineProps({
      links: { type: Array, default: () => [] },
    });

    const emit = defineEmits(['change']);
    </script>

    <template>
      <div v-if="links && links.length">
        <div class="flex flex-wrap -mb-1">
          <template v-for="(link, index) in links" :key="index">
            <button
              v-if="!link.url"
              class="mr-1 mb-1 px-4 py-2 text-sm text-gray-400 border rounded"
              v-html="link.label"
              disabled
            />
            <button
              v-else
              @click="emit('change', link.url)"
              class="mr-1 mb-1 px-4 py-2 text-sm border rounded hover:bg-white"
              :class="{ 'bg-blue-700 text-white': link.active }"
              v-html="link.label"
            />
          </template>
        </div>
      </div>
    </template>


