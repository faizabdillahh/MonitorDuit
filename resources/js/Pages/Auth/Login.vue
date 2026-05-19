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

        <div class="mb-6 text-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Selamat Datang</h2>
            <p class="text-sm text-gray-500 mt-1">Masuk untuk melihat catatan Anda.</p>
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-brand-600 bg-brand-50 dark:bg-brand-500/10 p-3 rounded-lg text-center">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label for="email" class="block text-[11px] font-semibold text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Email</label>
                <input
                    id="email"
                    type="email"
                    class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-brand-500 transition-all placeholder:text-gray-400"
                    v-model="form.email"
                    required
                    autofocus
                    placeholder="nama@email.com"
                    autocomplete="username"
                />
                <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kata Sandi</label>
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-xs font-medium text-brand-600 hover:text-brand-700 transition-colors"
                    >
                        Lupa sandi?
                    </Link>
                </div>
                <input
                    id="password"
                    type="password"
                    class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-brand-500 transition-all placeholder:text-gray-400"
                    v-model="form.password"
                    required
                    placeholder="••••••••"
                    autocomplete="current-password"
                />
                <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
            </div>

            <div class="flex items-center">
                <label class="flex items-center gap-2 cursor-pointer group">
                    <Checkbox name="remember" v-model:checked="form.remember" class="rounded border-gray-300 dark:border-gray-700 text-brand-600 focus:ring-brand-600 w-4 h-4" />
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        Ingat saya
                    </span>
                </label>
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    :class="['w-full py-2.5 px-4 bg-brand-600 text-white font-semibold text-sm rounded-lg hover:bg-brand-700 transition-colors', form.processing ? 'opacity-70 cursor-not-allowed' : '']"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Memproses...</span>
                    <span v-else>Masuk</span>
                </button>
            </div>
            
            <p class="text-center text-sm text-gray-500 mt-6 pt-4 border-t border-gray-100 dark:border-gray-800">
                Belum punya akun?
                <Link :href="route('register')" class="font-semibold text-brand-600 hover:text-brand-700 ml-1">
                    Daftar
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>
