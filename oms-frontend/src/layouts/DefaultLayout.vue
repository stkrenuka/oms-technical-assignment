<!-- src/layouts/DefaultLayout.vue -->
<script setup>
import AdminSidebar from '@/components/Admin/SideBar.vue';
import Header from '@/components/common/Header.vue';

</script>

<template>
  <section class="bg-gray-100 min-h-screen">
    <div class="flex min-h-screen">

      <!-- Sidebar -->
      <AdminSidebar />
  <!-- Right Side -->
      <div class="flex-1 flex flex-col">

        <!-- Header -->
        <Header />

        <!-- Page Content -->
        <main class="flex-1 p-6">
          <slot />
        </main>

      </div>

    </div>
  </section>
</template>
