<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="mb-8 text-center">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Selamat Datang Kembali</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Masuk ke akun Anda untuk mencatat pengeluaran.</p>
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-brand-600 bg-brand-50 dark:bg-brand-500/10 p-3 rounded-xl border border-brand-200 dark:border-brand-500/20 text-center">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Alamat Email</label>
                <input
                    id="email"
                    type="email"
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all placeholder:text-slate-400"
                    v-model="form.email"
                    required
                    autofocus
                    placeholder="nama@email.com"
                    autocomplete="username"
                />
                <p v-if="form.errors.email" class="text-red-500 text-xs mt-1.5">{{ form.errors.email }}</p>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Kata Sandi</label>
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-xs font-semibold text-brand-600 hover:text-brand-500 dark:text-brand-400 transition-colors"
                    >
                        Lupa sandi?
                    </Link>
                </div>
                <input
                    id="password"
                    type="password"
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all placeholder:text-slate-400"
                    v-model="form.password"
                    required
                    placeholder="••••••••"
                    autocomplete="current-password"
                />
                <p v-if="form.errors.password" class="text-red-500 text-xs mt-1.5">{{ form.errors.password }}</p>
            </div>

            <div class="flex items-center">
                <label class="flex items-center gap-2 cursor-pointer group">
                    <Checkbox name="remember" v-model:checked="form.remember" class="rounded border-slate-300 dark:border-slate-700 text-brand-500 focus:ring-brand-500" />
                    <span class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">
                        Ingat saya di perangkat ini
                    </span>
                </label>
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    :class="['w-full py-3.5 px-4 bg-gradient-to-r from-brand-500 to-teal-500 text-white font-bold rounded-xl shadow-lg shadow-brand-500/25 hover:shadow-brand-500/40 hover:-translate-y-0.5 transition-all focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900', form.processing ? 'opacity-70 cursor-not-allowed' : '']"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Memproses...
                    </span>
                    <span v-else>Masuk ke Dashboard</span>
                </button>
            </div>

            <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-800 text-center">
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    Belum punya akun?
                    <Link
                        :href="route('register')"
                        class="font-semibold text-brand-600 hover:text-brand-500 dark:text-brand-400 transition-colors ml-1"
                    >
                        Daftar sekarang
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
