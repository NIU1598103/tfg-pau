<script setup>
    import {Head, Link, useForm} from '@inertiajs/inertia-vue3';
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
    import { CheckCircleIcon, QuestionMarkCircleIcon } from '@heroicons/vue/24/outline';
    import { ref } from 'vue';

const props = defineProps({
        user: Object,
        actualUser: Object,
    });

    const form = useForm({
        id: props.user.id,
        name: props.user.name,
        email: props.user.email,
        type: props.user.type,
        admin: props.user.admin === 1 ? true : false,
        verified: props.user.verified === 1 ? true : false,
        transplant: props.user.transplant === 1 ? true : false,
        flexible: props.user.flexible === 1 ? true : false,
        total_guards: props.user.total_guards,
        weekend_guards: props.user.weekend_guards,
        weekend_backing: props.user.weekend_backing,
        less_guards_if_transplant: props.user.less_guards_if_transplant  === 1 ? true : false,

    });
    const formActualUser = useForm({
        id: props.actualUser.id,
        name: props.actualUser.name,
        email: props.actualUser.email,
        type: props.actualUser.type,
        admin: props.actualUser.admin === 1 ? true : false,
        verified: props.actualUser.verified === 1 ? true : false,
        transplant: props.actualUser.transplant === 1 ? true : false,
        flexible: props.actualUser.flexible === 1 ? true : false,
        total_guards: props.actualUser.total_guards,
        weekend_guards: props.actualUser.weekend_guards,
        weekend_backing: props.actualUser.weekend_backing,
        less_guards_if_transplant: props.actualUser.less_guards_if_transplant  === 1 ? true : false,
    });

    const openInfoDialog = ref(false);

    function submit(){
        form.post('/users/update');
    }
    function submitActUser(){
        formActualUser.post('/users/update');
    }

</script>

<template>
    <AppLayout>
        <template v-if="!actualUser.admin && actualUser.id !== user.id">
            Gaa
        </template>
        <template v-else-if="!actualUser.admin && actualUser.id === user.id">
            <Head>
                <title>Número de Guardias</title> 
            </Head>
            <div class="flex justify-center h-screen bg-white">

            <form @submit.prevent="submitActUser()">
                <div class="border-b border-gray-900/10 p-5 bg-gray-100">
                    <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="total-guards" class="block text-sm font-medium leading-6 text-gray-900">Guardias al mes:</label>
                        <div class="mt-2">
                            <input type="number" name="total_guards" id="total_guards" autocomplete="given-name" v-model="formActualUser.total_guards" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="festivas" class="block text-sm font-medium leading-6 text-gray-900">De las cuales en festivos:</label>
                        <div class="mt-2">
                            <input type="number" name="last-name" id="last-name" autocomplete="family-name" v-model="formActualUser.weekend_guards" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                        </div>
                    </div>                    
                    
                    </div>
                    <div class="relative flex gap-x-3">
                                <div class="flex h-6 items-center">
                                <input id="transplant" name="transplant" type="checkbox" v-model="formActualUser.flexible" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                                </div>
                                <div class="text-sm leading-6">
                                <label for="transplant" class="font-medium text-gray-900">Flexible</label>
                                <p class="text-gray-500">Si seleccionas esta casilla, el usuario se ofrece a hacer 1 guardia extra si es necesario.</p>
                                </div>
                                <div class="flex h-6 items-center">
                                <input id="transplant" name="transplant" type="checkbox" v-model="formActualUser.less_guards_if_transplant" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                                </div>
                                <div class="text-sm leading-6">
                                <label for="transplant" class="font-medium text-gray-900">Menos guardias en semana de trasplante</label>
                                <p class="text-gray-500">Si seleccionas esta casilla, el usuario hará 1 guardia menos el mes que le toque guardia de trasplante.</p>
                                </div>
                            </div>
                            <div class="flex justify-center mt-4">
                                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    Guardar
                                </button>
                            </div>
                            </div>
            </form>
            </div>
        </template>
        <template v-else>
            <Head>
                <title>Editar Perfil</title> 
            </Head>
            <div class="flex justify-center h-screen bg-white">
            <form @submit.prevent="submit()" class="bg-gray-100 p-4">
            <div class="space-y-5">
            <div class="border-b border-gray-900/10 pb-4">
                <h2 class="text-base font-semibold leading-7 text-gray-900">Perfil</h2>
                <p class="mt-1 text-sm leading-6 text-gray-600">Editando perfil de {{ user.name }}.</p>
            </div>

            <div class="border-b border-gray-900/10 pb-4">
                <h2 class="text-base font-semibold leading-7 text-gray-900">Información personal</h2>
                <p class="mt-1 text-sm leading-6 text-gray-600">Información personal del usuario.</p>

                <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                <div class="sm:col-span-3">
                    <label for="first-name" class="block text-sm font-medium leading-6 text-gray-900">Nombre</label>
                    <div class="mt-2">
                    <input type="text" name="name" id="name" autocomplete="given-name" v-model="form.name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Correo electónico</label>
                    <div class="mt-2">
                    <input id="email" name="email" type="email" autocomplete="email" v-model="form.email" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label for="tipo" class="block text-sm font-medium leading-6 text-gray-900">Tipo</label>
                    <div class="mt-2">
                    <select id="type" name="type" v-model="form.type" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6">
                        <option>Adjunto Senior</option>
                        <option>Adjunto Junior</option>
                        <option>R4</option>
                        <option>R3</option>
                        <option>R2</option>
                        <option>R1</option>
                        <option>Por determinar</option>
                    </select>
                    </div>
                </div>
                </div>
            </div>

            <div class="border-b border-gray-900/10 pb-4">
                <h2 class="text-base font-semibold leading-7 text-gray-900">Permisos</h2>

                <div class="mt-2 space-y-10">
                <fieldset>
                    <div class="mt-2 space-y-6">
                        <div v-if="user.id !== actualUser.id" class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                            <input id="admin" name="admin" type="checkbox" v-model="form.admin" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                            </div>
                            <div class="text-sm leading-6">
                            <label for="admin" class="font-medium text-gray-900">Administrador</label>
                            <p class="text-gray-500">Si seleccionas esta casilla, el usuario tendrá roles de administrador.</p>
                            </div>
                        </div>
                        

                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                            <input id="verified" name="verified" type="checkbox" v-model="form.verified" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                            </div>
                            <div class="text-sm leading-6">
                            <label for="verified" class="font-medium text-gray-900">Verificado</label>
                            <p class="text-gray-500">Si seleccionas esta casilla, el usuario pasará a estar verificado.</p>
                            </div>
                        </div>
                        

                        <div class="relative flex gap-x-3">
                            <div class="flex h-6 items-center">
                            <input id="transplant" name="transplant" type="checkbox" v-model="form.transplant" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                            </div>
                            <div class="text-sm leading-6">
                            <label for="transplant" class="font-medium text-gray-900">Guardia de trasplante</label>
                            <p class="text-gray-500">Si seleccionas esta casilla, el usuario puede hacer guardias de trasplante.</p>
                            </div>
                        </div>
                        
                    </div>
                </fieldset>
                </div>
            </div>
            <div class="border-b border-gray-900/10 pb-5 bg-gray-100">
                <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                <div class="sm:col-span-3">
                    <label for="total-guards" class="block text-sm font-medium leading-6 text-gray-900">Guardias al mes:</label>
                    <div class="mt-2">
                    <input type="number" name="total_guards" id="total_guards" autocomplete="given-name" v-model="form.total_guards" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label for="festivas" class="block text-sm font-medium leading-6 text-gray-900">De las cuales en festivos:</label>
                    <div class="mt-2">
                    <input type="number" name="last-name" id="last-name" autocomplete="family-name" v-model="form.weekend_guards" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                    </div>
                </div>
                

                
                
                </div>
                <div class="relative flex gap-x-3 p-5 bg-gray-100">
                            <div class="flex h-6 items-center">
                            <input id="transplant" name="transplant" type="checkbox" v-model="form.flexible" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                            </div>
                            <div class="text-sm leading-6">
                            <label for="transplant" class="font-medium text-gray-900">Flexible</label>
                            <p class="text-gray-500">Si seleccionas esta casilla, el usuario se ofrece a hacer 1 guardia extra si es necesario.</p>
                            </div>
                            <div class="flex h-6 items-center">
                            <input id="transplant" name="transplant" type="checkbox" v-model="form.less_guards_if_transplant" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                            </div>
                            <div class="text-sm leading-6">
                            <label for="transplant" class="font-medium text-gray-900">Menos guardias en semana de trasplante</label>
                            <p class="text-gray-500">Si seleccionas esta casilla, el usuario hará 1 guardia menos el mes que le toque guardia de trasplante.</p>
                            </div>
                        </div>
            </div>
            
        </div>

            <div class="mt-4 flex items-center justify-end gap-x-6 pb-4">
            <a :href="route('users.index', props.user.id)" class="text-sm font-semibold leading-6 text-gray-900">
                            Cancelar
            </a>
            <!-- <Link :href="route('users.index', user.id)" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            Guardar
                        </Link> -->
            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Guardar</button>
            </div>
        </form>
        </div>
        </template>
    </AppLayout>



    <template>
            <TransitionRoot as="template" :show="openInfoDialog">
                <Dialog as="div" class="relative z-10" @close="openInfoDialog = false">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
                </TransitionChild>

                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                <QuestionMarkCircleIcon class="h-15 w-15" aria-hidden="true" />
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <DialogTitle as="h3" class="text-base font-semibold leading-10 text-gray-900">Las guardias en fin de semana serán o el número de guardias en festivos indicada (completas), o los refuerzos de fin de semana indicados. Por ejemplo, si se indica 1 guardia en festivo ó 1 refuerzo de fin de semana, se considerará la opción que mejor se adapte a la hora de asignar todas las guardias. De momento no se puede indicar, por ejemplo, 2 en festivos ó 1+1 refuerzo de fin de semana.</DialogTitle> 
                            </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" @click="openInfoDialog = false" ref="cancelButtonRef">Cerrar</button>
                        </div>
                        </DialogPanel>
                    </TransitionChild>
                    </div>
                </div>
                </Dialog>
            </TransitionRoot>
        </template>
        
</template>