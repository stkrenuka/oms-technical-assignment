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
                Create Account
              </h5>
              <p class="text-sm text-gray-500 mt-2">
                Sign up to start using Order Management System.
              </p>
            </div>
            <!-- Form -->
            <form class="mt-6 space-y-4" @submit.prevent="submitSignup">
              <!-- Full Name -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Full Name
                </label>
                <input v-model="form.name" type="text" placeholder="Enter full name"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                <p v-if="errors.name" class="text-red-600 text-sm">{{ errors.name[0] }}</p>
              </div>
              <!-- Email -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Email Address
                </label>
                <input v-model="form.email" type="email" autocomplete="email" placeholder="Enter email"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                <p v-if="errors.email" class="text-red-600 text-sm">{{ errors.email[0] }}</p>
              </div>
              <!-- Password -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Password
                </label>
                <input v-model="form.password" type="password" placeholder="Create password"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                <p v-if="errors.password" class="text-red-600 text-sm">{{ errors.password[0] }}</p>
              </div>
              <!-- Confirm Password -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Confirm Password
                </label>
                <input v-model="form.password_confirmation" type="password" placeholder="Confirm password"
                  class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
              </div>
              <div v-if="errors.general" class="text-sm text-red-600 mt-1">
                <div> {{ errors.general[0] }}</div>
              </div>
              <!-- Submit -->
              <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-md transition">
                Sign Up
              </button>
              <!-- Footer -->
              <p class="text-center text-sm text-gray-600">
                Already have an account?
                <router-link :to="{ name: 'login' }" class="text-blue-600 hover:underline font-medium">
                  Log in
                </router-link>
              </p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import useAuth from '@/composables/auth'
const {
  registerForm: form,
  submitSignup,
  errors,
  processing
} = useAuth()
</script>