<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { Link, Head } from '@inertiajs/inertia-vue3';
    import {useForm} from '@inertiajs/inertia-vue3';

    defineProps({
        users: Array,
        months: Array,
        flash: Object
    });

    const newMonthForm = useForm({
        date: '', // Campo para la nueva fecha del mes en formato "yyyy-mm"
    });
    const handleCreateMonth = () => {
    newMonthForm.post('/months/create', {
        onSuccess: () => {
            newMonthForm.reset();
            },
        });
    };
    
    const handleOrganizeGuardiasClick = (monthId) => {
        // Aquí puedes colocar la lógica que deseas ejecutar cuando se haga clic en el botón
        console.log(monthId);
        const form = useForm({
                month: monthId,
            });
            // Inertia.post(route("users.update_blocked_days"), formattedSelectedDays);
            form.post('/months/organize_guards');
    }
    const cleanGuardsClick = (monthId) => {
        // Aquí puedes colocar la lógica que deseas ejecutar cuando se haga clic en el botón
        console.log(monthId);
        const form = useForm({
                month: monthId,
            });
            // Inertia.post(route("users.update_blocked_days"), formattedSelectedDays);
            form.post('/months/clean_guards');
    }
    
</script>

<template>
    <Head>
        <title>Organizar guardias</title> 
    </Head>
    <AppLayout>
        <div class="flex justify-center h-screen bg-white">
            <div>
                <!-- Mostrar mensajes flash -->
                <div v-if="flash.success" class="mb-4 text-green-500">
                    {{ flash.success }}
                </div>
                <div v-if="flash.error" class="mb-4 text-red-500">
                    {{ flash.error }}
                </div>
                <div class="text-center">
                    <!-- Formulario para crear un nuevo mes -->
                    <form @submit.prevent="handleCreateMonth" class="mb-4 inline-block">
                        <label for="date" class="block text-sm font-medium text-gray-700">Nuevo Mes (yyyy-mm):</label>
                        <input
                            v-model="newMonthForm.date"
                            type="month"
                            id="date"
                            name="date"
                            required
                            class="mt-1 block px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        />
                        <button
                            type="submit"
                            class="mt-2 px-3 py-2 bg-green-500 text-white rounded-md"
                        >
                            Crear Nuevo Mes
                        </button>
                    </form>
                </div>
                <h1 class="text-base text-center font-semibold leading-7 text-gray-900 mt-4 mb-2">Meses disponibles:</h1>

                <ul role="list" class="divide-y divide-gray-200 bg-gray-100 p-4">
                    <li v-for="month in months" :key="month.id" class="flex justify-between gap-x-6 py-5">
                    <div class="flex min-w-0 gap-x-4">
                        <div class="min-w-0 flex-auto">
                            <p class="text-sm font-semibold leading-6 text-gray-900">{{ month.name }}</p>
                        </div>
                    </div>
                    <button @click="handleOrganizeGuardiasClick(month.id)" class="px-3 py-1 bg-blue-500 text-white rounded-md">
                        Organizar guardias
                    </button>
                    <a v-if="month.handled === 'ORGANIZED'" :href="route('months.see_guards', month.id)" class="px-3 py-1 bg-blue-500 text-white rounded-md">
                        Ver guardias
                    </a>
                    <a v-else-if="month.handled === 'BLOCK_DAYS'" :href="route('months.error_organize', month.id)" class="px-3 py-1 bg-blue-500 text-white rounded-md">
                        Usuarios sin días bloqueados
                    </a>
                    <div v-else class="px-3 py-1 bg-gray-500 text-white rounded-md">
                        No disponible
                    </div>
                    <button @click="cleanGuardsClick(month.id)" class="px-3 py-1 bg-red-500 text-white rounded-md">
                        Borrar guardias
                    </button>
                    <!-- <Link :href="route('months.clean_guards', month.id)" class="px-3 py-1 bg-blue-500 text-white rounded-md">
                        Borrar guardias
                    </Link> -->
                  
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>