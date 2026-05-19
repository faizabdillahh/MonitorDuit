<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

const user = usePage().props.auth?.user

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profil" />
    <AppLayout>
        <!-- Profile Header (Instagram-style) -->
        <div class="flex items-center gap-5 mb-8">
            <div
              class="w-20 h-20 sm:w-24 sm:h-24 rounded-full flex items-center justify-center text-white text-2xl font-bold shrink-0"
              style="background: linear-gradient(135deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);"
            >
              {{ user?.name?.[0]?.toUpperCase() }}
            </div>
            <div class="min-w-0">
              <h1 class="text-xl font-semibold text-gray-900 dark:text-white">{{ user?.name }}</h1>
              <p class="text-sm text-gray-400 mt-0.5 truncate">{{ user?.email }}</p>
            </div>
        </div>

        <div class="space-y-5">
            <!-- Update Profile Info -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Informasi Profil</h2>
                </div>
                <div class="p-5">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-lg"
                    />
                </div>
            </div>

            <!-- Update Password -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Ubah Kata Sandi</h2>
                </div>
                <div class="p-5">
                    <UpdatePasswordForm class="max-w-lg" />
                </div>
            </div>

            <!-- Delete User -->
            <div class="bg-white dark:bg-gray-900 border border-red-100 dark:border-red-500/20 rounded-xl overflow-hidden">
                <div class="px-5 py-3 border-b border-red-100 dark:border-red-500/10">
                    <h2 class="text-sm font-semibold text-red-600 dark:text-red-400">Hapus Akun</h2>
                </div>
                <div class="p-5">
                    <DeleteUserForm class="max-w-lg" />
                </div>
            </div>

            <!-- Informasi & Bantuan -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden mt-8">
                <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-800">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Informasi</h2>
                </div>
                <div class="p-2 flex flex-col">
                    <Link :href="route('about')" class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors rounded-lg flex items-center justify-between">
                        Tentang Kami
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </Link>
                    <Link :href="route('help')" class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors rounded-lg flex items-center justify-between">
                        Pusat Bantuan
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </Link>
                    <Link :href="route('privacy')" class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors rounded-lg flex items-center justify-between">
                        Kebijakan Privasi
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </Link>
                    <Link :href="route('terms')" class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors rounded-lg flex items-center justify-between">
                        Syarat & Ketentuan
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </Link>
                </div>
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50 flex justify-between items-center text-xs text-gray-400">
                    <p>&copy; {{ new Date().getFullYear() }} MonitorDuit</p>
                    <p>Versi 1.2.1</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
