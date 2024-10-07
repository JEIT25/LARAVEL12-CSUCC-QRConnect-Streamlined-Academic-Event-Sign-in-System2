<template>
    <div class="max-w-2xl mx-auto p-4 bg-white shadow-md rounded-lg">
        <h1 class="text-2xl font-bold mb-6 text-center">Create Student Account</h1>
        <form @submit.prevent="submit" class="space-y-4">
            <div class="flex flex-col">
                <label for="fname" class="text-sm font-medium text-gray-700">First Name</label>
                <input id="fname" v-model.trim="form.fname" placeholder="First Name" required
                    class="mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
                <span v-if="form.errors.fname" class="text-red-500 text-sm">{{ form.errors.fname }}</span>
            </div>
            <div class="flex flex-col">
                <label for="lname" class="text-sm font-medium text-gray-700">Last Name</label>
                <input id="lname" v-model.trim="form.lname" placeholder="Last Name" required
                    class="mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
                <span v-if="form.errors.lname" class="text-red-500 text-sm">{{ form.errors.lname }}</span>
            </div>
            <div class="flex flex-col">
                <label for="program" class="text-sm font-medium text-gray-700">Program</label>
                <input id="program" v-model.trim="form.program" placeholder="Program" required
                    class="mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
                <span v-if="form.errors.program" class="text-red-500 text-sm">{{ form.errors.program }}</span>
            </div>
            <div class="flex flex-col">
                <label for="school_id_number" class="text-sm font-medium text-gray-700">School ID Number</label>
                <input id="school_id_number" v-model.trim="form.school_id_number" placeholder="School ID Number"
                    class="mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
                <span v-if="form.errors.school_id_number" class="text-red-500 text-sm">{{ form.errors.school_id_number
                    }}</span>
            </div>
            <div class="flex flex-col">
                <label for="birth_date" class="text-sm font-medium text-gray-700">Birth Date</label>
                <input id="birth_date" v-model.trim="form.birth_date" type="date" placeholder="Birth Date" required
                    class="mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
                <span v-if="form.errors.birth_date" class="text-red-500 text-sm">{{ form.errors.birth_date }}</span>
            </div>
            <div class="flex flex-col">
                <label for="email" class="text-sm font-medium text-gray-700">Email</label>
                <input id="email" v-model.trim="form.email" type="email" placeholder="Email" required
                    class="mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
                <span v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</span>
            </div>
            <div class="flex flex-col">
                <label for="password" class="text-sm font-medium text-gray-700">Password</label>
                <input id="password" v-model.trim="form.password" type="password" placeholder="Password" required
                    class="mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
                <span v-if="form.errors.password" class="text-red-500 text-sm">{{ form.errors.password }}</span>
            </div>
            <div class="flex flex-col">
                <label for="password_confirmation" class="text-sm font-medium text-gray-700">Confirm Password</label>
                <input id="password_confirmation" v-model.trim="form.password_confirmation" type="password"
                    placeholder="Confirm Password" required
                    class="mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
            </div>
            <button type="submit"
                class="w-full bg-blue-500 text-white py-2 px-4 rounded-md shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Create Account
            </button>
            <Link href="/facilitators/login" class="text-blue-500 hover:underline text-center">
            <p class="mt-5">Log In with Student Account</p>
            </Link>
        </form>
    </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'

const form = useForm({
    lname: '',
    fname: '',
    program: '',
    school_id_number: '',
    birth_date: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/students');
};
</script>
