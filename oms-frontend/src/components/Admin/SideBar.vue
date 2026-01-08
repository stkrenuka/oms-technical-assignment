<template>
    <!-- Sidebar -->
      <aside class="w-64 bg-gray-900 text-white">
        <div class="p-6 text-xl font-bold border-b border-gray-700">
          Admin Panel
        </div>

        <nav class="p-4 space-y-2">
          <router-link
            :to="{ name: 'admin.dashboard' }"
            class="block px-4 py-2 rounded hover:bg-gray-800"
            active-class="bg-gray-800"
          >
            Dashboard
          </router-link>

          <router-link
            :to="{ name: 'admin.orders' }"
            class="block px-4 py-2 rounded hover:bg-gray-800"
          >
            Orders
          </router-link>

          <router-link
            :to="{ name: 'admin.customers' }"
            class="block px-4 py-2 rounded hover:bg-gray-800"
          >
            Customers
          </router-link>

          <router-link
            :to="{ name: 'admin.products' }"
            class="block px-4 py-2 rounded hover:bg-gray-800"
          >
            Products
          </router-link>

          <button
            class="block w-full text-left px-4 py-2 rounded hover:bg-red-600"
          >
            Logout
          </button>
        </nav>
      </aside>
    </template>