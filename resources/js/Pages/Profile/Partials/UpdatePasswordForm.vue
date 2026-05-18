<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Perbarui Kata Sandi</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Pastikan akun Anda menggunakan kata sandi yang panjang dan acak demi keamanan.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-5">
            <div>
                <label for="current_password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Kata Sandi Saat Ini</label>
                <input
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500 transition-all"
                    autocomplete="current-password"
                />
                <p v-if="form.errors.current_password" class="text-red-500 text-xs mt-1.5">{{ form.errors.current_password }}</p>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Kata Sandi Baru</label>
                <input
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500 transition-all"
                    autocomplete="new-password"
                />
                <p v-if="form.errors.password" class="text-red-500 text-xs mt-1.5">{{ form.errors.password }}</p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Konfirmasi Sandi Baru</label>
                <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500 transition-all"
                    autocomplete="new-password"
                />
                <p v-if="form.errors.password_confirmation" class="text-red-500 text-xs mt-1.5">{{ form.errors.password_confirmation }}</p>
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-5 py-2.5 bg-slate-800 dark:bg-slate-700 hover:bg-slate-700 dark:hover:bg-slate-600 text-white font-medium rounded-xl shadow-md disabled:opacity-50 transition-all"
                >
                    Ubah Kata Sandi
                </button>

                <Transition
                    enter-active-class="transition ease-in-out duration-300"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out duration-300"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                        Tersimpan
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
