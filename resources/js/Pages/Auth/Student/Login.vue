<template>
    <div class="max-w-md mx-auto p-6 bg-white shadow-md rounded-lg">
        <h1 class="text-2xl font-bold mb-6 text-center">Student Login</h1>
        <form @submit.prevent="submit" class="space-y-4">
            <div class="flex flex-col">
                <label for="email" class="text-sm font-medium text-gray-700">Email</label>
                <input id="email" v-model="form.email" type="email" placeholder="Email" required
                    class="mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
                <span v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</span>
            </div>
            <div class="flex flex-col">
                <label for="password" class="text-sm font-medium text-gray-700">Password</label>
                <input id="password" v-model="form.password" type="password" placeholder="Password" required
                    class="mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
                <span v-if="form.errors.password" class="text-red-500 text-sm">{{ form.errors.password }}</span>
            </div>
            <button type="submit"
                class="w-full bg-blue-500 text-white py-2 px-4 rounded-md shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Log In
            </button>
            <Link href="/students/create" class="text-blue-500 hover:underline text-center">
            <p class="mt-5">Create Student Account</p>
            </Link>
        </form>
    </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'

const form = useForm({
    email: '',
    password: '',
});

const submit = () => {
    form.post('/login');
};
</script>