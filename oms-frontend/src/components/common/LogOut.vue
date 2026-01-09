<template>
    <button class="block w-full text-left px-4 py-2 rounded hover:bg-red-600" @click="logout">
        Logout
    </button>
</template>
<script setup>
import useAuth from '@/composables/auth'

const { logout } = useAuth();
</script>