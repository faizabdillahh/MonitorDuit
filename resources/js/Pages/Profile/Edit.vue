<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

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
    <Head title="Profil Pengguna" />

    <AppLayout title="Profil Pengguna">
        <div class="max-w-4xl mx-auto space-y-6">
            
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Profil Pengguna</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola informasi pribadi dan keamanan akun Anda.</p>
            </div>

            <!-- Update Profile Info -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 md:p-8 shadow-sm">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </div>

            <!-- Update Password -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 md:p-8 shadow-sm">
                <UpdatePasswordForm class="max-w-xl" />
            </div>

            <!-- Delete User -->
            <div class="bg-red-50 dark:bg-red-500/10 rounded-2xl border border-red-200 dark:border-red-500/20 p-6 md:p-8 shadow-sm">
                <DeleteUserForm class="max-w-xl" />
            </div>
        </div>
    </AppLayout>
</template>
