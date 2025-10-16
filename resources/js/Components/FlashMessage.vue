<script setup>
    import { ref, watch } from 'vue';
    import { usePage } from '@inertiajs/vue3';

    const page = usePage();
    const flash = ref(page.props.flash || {});
    const visible = ref(false);

    // flash.messageが更新されたら表示する
    watch(
      () => page.props.flash,
      (newFlash) => {
        flash.value = newFlash;
        if (flash.value.message) {
          visible.value = true;

          // 5秒後に自動で非表示
          setTimeout(() => {
            visible.value = false;
          }, 5000);
        }
      },
      { immediate: true, deep: true }
    );

    // 手動で閉じる
    const close = () => {
      visible.value = false;
    };
    </script>

    <template>
      <div v-if="visible && flash.message"
           :class="[
             'p-2 text-sm rounded mb-2 flex justify-between items-center',
             flash.status === 'alert' ? 'bg-red-400 text-white' : 'bg-blue-300 text-white'
           ]">

        <span>{{ flash.message }}</span>

        <!-- 閉じるボタン -->
        <button @click="close" class="ml-4 text-white font-bold">×</button>
      </div>
    </template>


