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
        <Head title="Daftar Akun" />

        <div class="mb-8 text-center">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Buat Akun Baru</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Mulai pantau pengeluaran Anda dengan bantuan AI.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nama Lengkap</label>
                <input
                    id="name"
                    type="text"
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all placeholder:text-slate-400"
                    v-model="form.name"
                    required
                    autofocus
                    placeholder="Budi Santoso"
                    autocomplete="name"
                />
                <p v-if="form.errors.name" class="text-red-500 text-xs mt-1.5">{{ form.errors.name }}</p>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Alamat Email</label>
                <input
                    id="email"
                    type="email"
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all placeholder:text-slate-400"
                    v-model="form.email"
                    required
                    placeholder="nama@email.com"
                    autocomplete="username"
                />
                <p v-if="form.errors.email" class="text-red-500 text-xs mt-1.5">{{ form.errors.email }}</p>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Kata Sandi</label>
                <input
                    id="password"
                    type="password"
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all placeholder:text-slate-400"
                    v-model="form.password"
                    required
                    placeholder="Minimal 8 karakter"
                    autocomplete="new-password"
                />
                <p v-if="form.errors.password" class="text-red-500 text-xs mt-1.5">{{ form.errors.password }}</p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Konfirmasi Kata Sandi</label>
                <input
                    id="password_confirmation"
                    type="password"
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all placeholder:text-slate-400"
                    v-model="form.password_confirmation"
                    required
                    placeholder="Ulangi kata sandi Anda"
                    autocomplete="new-password"
                />
                <p v-if="form.errors.password_confirmation" class="text-red-500 text-xs mt-1.5">{{ form.errors.password_confirmation }}</p>
            </div>

            <div class="pt-4">
                <button
                    type="submit"
                    :class="['w-full py-3.5 px-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:-translate-y-0.5 transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900', form.processing ? 'opacity-70 cursor-not-allowed' : '']"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Membuat Akun...
                    </span>
                    <span v-else>Daftar Sekarang</span>
                </button>
            </div>

            <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-800 text-center">
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    Sudah memiliki akun?
                    <Link
                        :href="route('login')"
                        class="font-semibold text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 transition-colors ml-1"
                    >
                        Masuk di sini
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
