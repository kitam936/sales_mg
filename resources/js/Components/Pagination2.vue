<script setup>
    const props = defineProps({
      links: Array
    })

    const emit = defineEmits(['change'])
    </script>

    <template>
      <div v-if="links.length > 3">
        <div class="flex flex-wrap -mb-1">
          <template v-for="(link, index) in links" :key="index">

            <!-- disabled -->
            <button
              v-if="link.url === null"
              class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded"
              v-html="link.label"
              disabled
            />

            <!-- active or clickable -->
            <button
              v-else
              @click="emit('change', link.url)"
              class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white"
              :class="{ 'bg-blue-700 text-white': link.active }"
              v-html="link.label"
            />
          </template>
        </div>
      </div>
    </template>
