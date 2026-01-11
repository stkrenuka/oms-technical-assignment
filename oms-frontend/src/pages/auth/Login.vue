<template>
  <div class="w-full max-w-md lg:max-w-lg xl:max-w-sm mx-auto">
    <div class="flex p-4 sm:p-6 min-h-screen items-center">
      <div class="w-full">
        <div class="flex flex-col h-full">
          <div class="my-auto bg-white dark:bg-gray-200 rounded-lg shadow-md p-6">
            <!-- Header -->
            <div class="text-center">
              <div class="flex justify-center mb-4">
                <img src="@/assets/images/light-logo.png" alt="Logo" class="h-10">
              </div>
              <h5 class="text-lg font-semibold text-gray-800">
                Welcome Back!
              </h5>
              <p class="text-sm text-gray-500 mt-2">
                Sign in to continue to Order Management System.
              </p>
            </div>
            <!-- Form -->
            <form class="mt-6 space-y-4" @submit.prevent="submitLogin">
              <!-- Email -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Email
                </label>
                <input v-model="form.email" type="email" autofocus autocomplete="username" placeholder="Enter email"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm
             focus:outline-none focus:ring-2 focus:ring-blue-500" />
                <div v-if="errors.email" class="text-sm text-red-600 mt-1">
                  <div v-for="msg in errors.email" :key="msg">{{ msg }}</div>
                </div>
              </div>
              <!-- Password -->
              <div>
                <label class="text-sm font-medium text-gray-700 mb-1">
                  Password
                </label>
                <input v-model="form.password" type="password" placeholder="Enter password" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm
             focus:outline-none focus:ring-2 focus:ring-blue-500" />
                <div v-if="errors.general" class="text-sm text-red-600 mt-1">
                  <div> {{ errors.general[0] }}</div>
                </div>
              </div>
              <!-- Submit -->
              <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white
           font-medium py-2 rounded-md transition">
                Log In
              </button>
            </form>
            <br>
            <!-- Footer -->
            <p class="text-center text-sm text-gray-600">
              Not have an account?
              <router-link :to="{ name: 'register' }" class="text-blue-600 hover:underline font-medium">
                Sign up
              </router-link>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import useAuth from '@/composables/auth'
const { loginForm: form, errors, submitLogin } = useAuth();
</script>