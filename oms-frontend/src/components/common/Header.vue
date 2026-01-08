<template>
      <!-- Right Side -->
      <div class="flex-1 flex flex-col">

        <!-- Header -->
        <header class="bg-white shadow px-6 py-4 flex justify-between items-center">

          <!-- Left: Page title -->
          <div>
            <h1 class="text-xl font-semibold text-gray-800">
              Admin Dashboard
            </h1>
            <p class="text-sm text-gray-500">
              Welcome back
            </p>
          </div>

          <!-- Right: User info -->
          <div class="flex items-center space-x-4">
            <div class="text-right">
              <p class="text-sm font-medium text-gray-800">
                John Doe
              </p>
              <p class="text-xs text-gray-500">
                Admin
              </p>
            </div>

            <div
              class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-700 font-bold"
            >
              JD
            </div>

            <button
              class="px-3 py-2 text-sm bg-red-600 text-white rounded hover:bg-red-700"
            >
              Logout
            </button>
          </div>

        </header>

        <!-- Page Content -->
        <main class="flex-1 p-6">
          <slot />
        </main>

      </div>
    </template>