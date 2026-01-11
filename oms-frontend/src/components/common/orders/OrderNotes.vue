<script setup>
import useOrderForm from '@/composables/useOrderForm'
import { useOrderStore } from '@/stores/order'
import { ref } from 'vue'
const orderStore = useOrderStore()

const orderForm = useOrderForm()

</script>
<template>
    <div v-if="orderStore.showNotesModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-full max-w-lg p-6">
            <h2 class="text-lg font-semibold mb-4">
                Order #{{ orderStore.formNotes.order_id }} Notes
            </h2>

            <!-- Notes List -->
            <div class="space-y-3 max-h-60 overflow-y-auto mb-4">
                <div v-for="note in orderStore.statusHistory" :key="note.id" class="border rounded p-3 text-sm">
                    <p class="text-gray-800">{{ note.note }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ note.author?.name || 'System' }} · {{ note.created_at }}
                    </p>
                </div>

                <p v-if="orderStore.statusHistory.length === 0" class="text-gray-500 text-sm">
                    No notes yet
                </p>
            </div>

            <!-- Add Note -->

            <textarea v-model="orderStore.formNotes.notes" class="w-full border rounded p-2 mb-3"
                placeholder="Add a note..." />
            <p v-if="orderStore.errors.notes" class="text-red-500 text-sm mt-1">
                {{ orderStore.errors.notes }}
            </p>
            <!-- Uploaded Files -->
<div class="mt-4">
  <h3 class="text-sm font-semibold mb-2">
    Uploaded Files
  </h3>

  <div v-if="orderStore.uploadedFiles.length" class="space-y-2">
    <div
      v-for="file in orderStore.uploadedFiles"
      :key="file.id"
      class="flex items-center justify-between border rounded p-2 text-sm"
    >
      <div class="truncate">
        📎 {{ file.file_name }}
      </div>

      <button
       @click="orderForm.downloadFile(file)"
        class="text-blue-600 text-xs"
        target="_blank"
      >
        Download
    </button>
    </div>
  </div>

  <p v-else class="text-gray-500 text-sm">
    No files uploaded yet
  </p>
</div>

            <!-- File Upload -->
            <div class="mt-3">
                <label class="block text-sm font-medium mb-1">
                    Attach file (PDF/Image)
                </label>

                <input type="file" multiple @change="orderForm.uploadMultipleFiles" />
                <div v-if="orderStore.uploading" class="mt-2">
                    <div class="h-2 bg-gray-200 rounded">
                        <div class="h-2 bg-blue-600 rounded transition-all"
                            :style="{ width: orderStore.uploadProgress + '%' }"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Uploading... {{ orderStore.uploadProgress }}%
                    </p>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <button class="px-4 py-2 bg-gray-300 rounded" @click="orderForm.closeNotes">
                    Cancel
                </button>
                <button class="px-4 py-2 bg-blue-600 text-white rounded" @click="orderForm.saveNotes">
                    Save
                </button>
            </div>
        </div>
    </div>

</template>