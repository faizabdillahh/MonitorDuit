<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="mb-6 text-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Daftar Akun</h2>
            <p class="text-sm text-gray-500 mt-1">Mulai kelola keuangan Anda.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label for="name" class="block text-[11px] font-semibold text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Nama Lengkap</label>
                <input
                    id="name"
                    type="text"
                    class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-brand-500 transition-all placeholder:text-gray-400"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Nama Lengkap"
                />
                <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
            </div>

            <div>
                <label for="email" class="block text-[11px] font-semibold text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Email</label>
                <input
                    id="email"
                    type="email"
                    class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-brand-500 transition-all placeholder:text-gray-400"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="nama@email.com"
                />
                <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
            </div>

            <div>
                <label for="password" class="block text-[11px] font-semibold text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Kata Sandi</label>
                <input
                    id="password"
                    type="password"
                    class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-brand-500 transition-all placeholder:text-gray-400"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter"
                />
                <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-[11px] font-semibold text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Konfirmasi Sandi</label>
                <input
                    id="password_confirmation"
                    type="password"
                    class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-brand-500 transition-all placeholder:text-gray-400"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Ulangi kata sandi"
                />
                <p v-if="form.errors.password_confirmation" class="text-red-500 text-xs mt-1">{{ form.errors.password_confirmation }}</p>
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    :class="['w-full py-2.5 px-4 bg-brand-600 text-white font-semibold text-sm rounded-lg hover:bg-brand-700 transition-colors', form.processing ? 'opacity-70 cursor-not-allowed' : '']"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Mendaftar...</span>
                    <span v-else>Daftar Sekarang</span>
                </button>
            </div>
            
            <p class="text-center text-sm text-gray-500 mt-6 pt-4 border-t border-gray-100 dark:border-gray-800">
                Sudah punya akun?
                <Link :href="route('login')" class="font-semibold text-brand-600 hover:text-brand-700 ml-1">
                    Masuk
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>
