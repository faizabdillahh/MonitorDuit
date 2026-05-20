<script setup>
import { ref } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Eye, EyeOff } from 'lucide-vue-next';

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

const showPassword = ref(false);

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
                    :class="[
                        'w-full px-3 py-2 rounded-lg text-sm outline-none focus:ring-1 transition-all',
                        form.errors.email 
                            ? 'bg-red-50 dark:bg-red-900/10 border-red-500 text-red-900 dark:text-red-200 focus:border-red-500 focus:ring-red-500 placeholder-red-300 dark:placeholder-red-400/50' 
                            : 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white focus:ring-brand-500 placeholder:text-gray-400'
                    ]"
                    v-model="form.email"
                    required
                    autofocus
                    placeholder="nama@email.com"
                    autocomplete="username"
                    @invalid="(e) => e.target.setCustomValidity(e.target.value === '' ? 'Email wajib diisi.' : 'Format email tidak valid.')"
                    @input="(e) => e.target.setCustomValidity('')"
                />
                <p v-if="form.errors.email" class="text-red-500 dark:text-red-400 text-xs mt-1.5 font-medium flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ form.errors.email }}
                </p>
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
                <div class="relative">
                    <input
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        :class="[
                            'w-full px-3 py-2 pr-10 rounded-lg text-sm outline-none focus:ring-1 transition-all',
                            form.errors.password 
                                ? 'bg-red-50 dark:bg-red-900/10 border-red-500 text-red-900 dark:text-red-200 focus:border-red-500 focus:ring-red-500 placeholder-red-300 dark:placeholder-red-400/50' 
                                : 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white focus:ring-brand-500 placeholder:text-gray-400'
                        ]"
                        v-model="form.password"
                        required
                        placeholder="••••••••"
                        autocomplete="current-password"
                        @invalid="(e) => e.target.setCustomValidity('Kata sandi wajib diisi.')"
                        @input="(e) => e.target.setCustomValidity('')"
                    />
                    <button type="button" tabindex="-1" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" @click="showPassword = !showPassword">
                        <Eye v-if="!showPassword" class="w-4 h-4" />
                        <EyeOff v-else class="w-4 h-4" />
                    </button>
                </div>
                <p v-if="form.errors.password" class="text-red-500 dark:text-red-400 text-xs mt-1.5 font-medium flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ form.errors.password }}
                </p>
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
