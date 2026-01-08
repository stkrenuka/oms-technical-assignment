<template>
     <div class="bg-white rounded shadow p-6">
      <h2 class="text-xl font-semibold mb-4">Orders</h2>

      <table class="w-full border">
        <thead class="bg-gray-100">
          <tr>
            <th class="p-2 border">Order ID</th>
            <th class="p-2 border">Customer</th>
            <th class="p-2 border">Total</th>
            <th class="p-2 border">Status</th>
            <th class="p-2 border">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr class="text-center">
            <td class="p-2 border">#1001</td>
            <td class="p-2 border">John Doe</td>
            <td class="p-2 border">$250</td>
            <td class="p-2 border">
              <span class="px-2 py-1 text-sm bg-green-100 text-green-700 rounded">
                Completed
              </span>
            </td>
            <td class="p-2 border">
              <button class="px-3 py-1 bg-blue-600 text-white rounded">View</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    </template>