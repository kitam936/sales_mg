
<div class="mt-4">
    <img
      v-if="!filename"
      src="/images/no_image.jpg"
      alt="No image"
    />
    <img
      v-else
      :src="`/storage/public/images/${filename}`"
      alt="image"
    />
  </div>
</template>

<script setup>
defineProps({
  filename: {
    type: String,
    default: ''
  }
})
</script>
