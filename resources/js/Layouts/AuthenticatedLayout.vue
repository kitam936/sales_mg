<script setup>
    import { ref } from 'vue';
    import ApplicationLogo from '@/Components/ApplicationLogo.vue';
    import Dropdown from '@/Components/Dropdown.vue';
    import DropdownLink from '@/Components/DropdownLink.vue';
    import NavLink from '@/Components/NavLink.vue';
    import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
    import { Link } from '@inertiajs/vue3';

    const showingNavigationDropdown = ref(false);
    </script>

    <template>
      <div class="min-h-[100dvh] flex flex-col bg-gray-100 overflow-auto">
        <!-- ナビゲーション -->
        <nav class="border-b border-gray-100 bg-white flex-shrink-0">
          <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between items-center">
              <!-- 左側ロゴ -->
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <Link :href="route('dashboard')">
                    <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />
                  </Link>
                </div>
                <!-- ナビリンク（大画面用） -->
                <div class="hidden sm:ml-10 sm:flex sm:space-x-8">
                  <NavLink :href="route('menu')" :active="route().current('menu')">Menu</NavLink>
                </div>
              </div>

              <!-- 右側設定ドロップダウン -->
              <div class="hidden sm:flex sm:items-center sm:ml-6">
                <Dropdown align="right" width="48">
                  <template #trigger>
                    <button class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none">
                      {{ $page.props.auth.user.name }}
                      <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                      </svg>
                    </button>
                  </template>
                  <template #content>
                    <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                  </template>
                </Dropdown>
              </div>

              <!-- ハンバーガーメニュー（小画面） -->
              <div class="flex sm:hidden">
                <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none">
                  <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- レスポンシブメニュー -->
          <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
            <div class="space-y-1 pb-3 pt-2">
              <ResponsiveNavLink :href="route('menu')" :active="route().current('menu')">Menu</ResponsiveNavLink>
            </div>
            <div class="border-t border-gray-200 pb-1 pt-4">
              <div class="px-4">
                <div class="text-base font-medium text-gray-800">{{ $page.props.auth.user.name }}</div>
                <div class="text-sm font-medium text-gray-500">{{ $page.props.auth.user.email }}</div>
              </div>
              <div class="mt-3 space-y-1">
                <ResponsiveNavLink :href="route('logout')" method="post" as="button">Log Out</ResponsiveNavLink>
              </div>
            </div>
          </div>
        </nav>

        <!-- ページヘッダー -->
        <header v-if="$slots.header" class="bg-white shadow flex-shrink-0">
          <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <slot name="header"/>
          </div>
        </header>

        <!-- ページコンテンツ -->
        <main class="flex-1 overflow-auto">
          <slot/>
        </main>
      </div>
    </template>
