<script setup>
    import {Link, Head} from '@inertiajs/inertia-vue3';
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { ref, computed } from 'vue';
    import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
    import { ExclamationTriangleIcon, CheckCircleIcon  } from '@heroicons/vue/24/outline'
    import {useForm} from '@inertiajs/inertia-vue3';

    const props = defineProps({
        users: Array,
    });
    const openConfirmationDialog = ref(false);
    const openSuccessDialog = ref(false);
    const userName = ref("");
    const userId = ref("");

    const perPage = 5;
    const currentPage = ref(1);

    const totalPages = computed(() => Math.ceil(props.users.length / perPage));

    const paginatedUsers = computed(() => {
    const startIndex = (currentPage.value - 1) * perPage;
    const endIndex = startIndex + perPage;
    return props.users.slice(startIndex, endIndex);
    });


    const form = useForm({
        name: userName,
        id: userId,
    });

    const showDialog = (user) => {
    // console.log('Mostrar diálogo para el usuario:', user);
    userName.value = user.name;
    openConfirmationDialog.value = true;
    userId.value = user.id;
    // console.log('Mostrar diálogo para el usuario:', userId.value);
    };
    
    const confirm = () => {
        openConfirmationDialog.value = false;
        
        form.post('/users/delete_confirm');
        // form.post('/users/delete_confirm').then(() => {
        //         openSuccessDialog.value = true;
        // });
    };
    const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
    };

    const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
    };
</script>

<template>
    <Head>
        <title>Usuarios</title> 
    </Head>
    <AppLayout>
        <template v-if="$page.props.auth.user.admin">
            <div class="flex justify-center h-screen bg-white">
                <div>
                    
                <h1 class="text-base font-semibold leading-7 text-gray-900 mt-2">Lista usuarios:</h1>
                
                <ul role="list" class="divide-y divide-gray-200 bg-gray-100">
                    <li v-for="user in paginatedUsers" :key="user.email" class="flex justify-between gap-x-6 py-5">
                    <div class="flex min-w-0 gap-x-4 mr-10 ml-4">
                        <img class="h-12 w-12 flex-none rounded-full bg-gray-50" :src="user.profile_photo_url" alt="" />
                        <div class="min-w-0 flex-auto">
                            <p class="text-sm font-semibold leading-6 text-gray-900">{{ user.name }}</p>
                            <p class="mt-1 truncate text-xs leading-5 text-gray-500">{{ user.email }}</p>
                        </div>
                    </div>
                    <div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end">
                        <p v-if="!user.verified" class="text-sm leading-6 text-gray-900">No verificado</p>
                        <p v-else class="text-sm leading-6 text-gray-900">Verificado</p>
                        <p v-if="!user.admin" class="mt-1 text-xs leading-5 text-gray-500">
                        No es admin
                        </p>
                        <div v-else class="mt-1 flex items-center gap-x-1.5">
                        <div class="flex-none rounded-full bg-yellow-500/20 p-1">
                            <div class="h-1.5 w-1.5 rounded-full bg-yellow-500" />
                        </div>
                        <p class="text-xs leading-5 text-gray-500">Admin</p>
                        </div>
                    </div>
                    <div class="mr-4">
                        <!-- <Link :href="route('users.edit', user.id)" class="block mb-2 px-3 py-1 bg-blue-500 text-white rounded-md text-center">
                            Editar
                        </Link> -->
                        <a :href="route('users.edit', user.id)" class="block mb-2 px-3 py-1 bg-blue-500 text-white rounded-md text-center">Editar</a>

                        <button @click="showDialog(user)" class="block px-3 py-1 bg-red-500 text-white rounded-md text-center">
                            Eliminar
                        </button>
                    </div>
                    </li>
                </ul>
                <div class="flex justify-center bg-white mt-2">
                    <button @click="prevPage" :disabled="currentPage === 1" class="mr-2 px-3 py-1 bg-blue-500 text-white rounded-md">Anterior</button>
                    <button @click="nextPage" :disabled="currentPage === totalPages" class="ml-2 px-3 py-1 bg-blue-500 text-white rounded-md">Siguiente</button>
                </div>
                </div>
            </div>
        </template>
        <template v-if="!$page.props.auth.user.admin">
            <h1>Acceso no autorizado.</h1>
        </template>
    </AppLayout>


        <template>
            <TransitionRoot as="template" :show="openConfirmationDialog">
                <Dialog as="div" class="relative z-10" @close="openConfirmationDialog = false">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
                </TransitionChild>

                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <ExclamationTriangleIcon class="h-6 w-6 text-red-600" aria-hidden="true" />
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <DialogTitle as="h3" class="text-base font-semibold leading-6 text-gray-900">Eliminar cuenta</DialogTitle>
                                <div class="mt-2">
                                <p class="text-sm text-gray-500">¿Estás segur@ que quieres eliminar la cuenta de {{ userName }}? Esta acción no es reversible.</p>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto" @click="confirm()">Confirmar</button>
                            <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" @click="openConfirmationDialog = false" ref="cancelButtonRef">Cancelar</button>
                        </div>
                        </DialogPanel>
                    </TransitionChild>
                    </div>
                </div>
                </Dialog>
            </TransitionRoot>
        </template>
        <template>
            <TransitionRoot as="template" :show="openSuccessDialog">
                <Dialog as="div" class="relative z-10" @close="openSuccessDialog = false">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
                </TransitionChild>

                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <CheckCircleIcon class="h-15 w-15 text-green-400" aria-hidden="true" />
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <DialogTitle as="h3" class="text-base font-semibold leading-10 text-gray-900">Cuenta eliminada con éxito</DialogTitle> 
                            </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" @click="openSuccessDialog = false" ref="cancelButtonRef">Cerrar</button>
                        </div>
                        </DialogPanel>
                    </TransitionChild>
                    </div>
                </div>
                </Dialog>
            </TransitionRoot>
        </template>
</template>