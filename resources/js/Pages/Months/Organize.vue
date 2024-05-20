<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { Link, Head } from '@inertiajs/inertia-vue3';
    import {useForm} from '@inertiajs/inertia-vue3';

    defineProps({
        users: Array,
        months: Array,
    });

    
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
            <h1 class="text-base font-semibold leading-7 text-gray-900 mt-4 mb-2">Meses disponibles:</h1>
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