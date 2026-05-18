<script setup>
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.reset();
    form.clearErrors();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-bold text-red-600 dark:text-red-400">Hapus Akun</h2>
            <p class="mt-1 text-sm text-red-500 dark:text-red-300">
                Setelah akun Anda dihapus, seluruh sumber daya dan data di dalamnya akan dihapus secara permanen. Sebelum menghapus akun Anda, harap unduh data atau informasi apa pun yang ingin Anda simpan.
            </p>
        </header>

        <button
            type="button"
            @click="confirmUserDeletion"
            class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl shadow-md transition-all"
        >
            Hapus Akun Saya
        </button>

        <!-- Modal -->
        <Teleport to="body">
            <div v-if="confirmingUserDeletion" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    
                    <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="closeModal"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-slate-800">
                        <div class="px-6 pt-6 pb-6">
                            <h3 class="text-lg leading-6 font-bold text-slate-900 dark:text-white" id="modal-title">
                                Apakah Anda yakin ingin menghapus akun?
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                                    Setelah akun Anda dihapus, semua data dan riwayat transaksi akan dihapus secara permanen. Masukkan kata sandi Anda untuk mengonfirmasi penghapusan.
                                </p>
                                
                                <div>
                                    <label for="password" class="sr-only">Kata Sandi</label>
                                    <input
                                        id="password"
                                        ref="passwordInput"
                                        v-model="form.password"
                                        type="password"
                                        class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-red-500 transition-all placeholder:text-slate-400"
                                        placeholder="Kata Sandi"
                                        @keyup.enter="deleteUser"
                                    />
                                    <p v-if="form.errors.password" class="text-red-500 text-xs mt-1.5">{{ form.errors.password }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-200 dark:border-slate-800">
                            <button
                                type="button"
                                @click="closeModal"
                                class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-medium rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                            >
                                Batal
                            </button>
                            <button
                                type="button"
                                class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl shadow-md transition-all disabled:opacity-50"
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                                @click="deleteUser"
                            >
                                Ya, Hapus Akun
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </section>
</template>
