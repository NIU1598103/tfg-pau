<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { Link, Head } from '@inertiajs/inertia-vue3';
    defineProps({
        users: Array,
        months: Array,
    });
</script>

<template>
    <Head>
        <title>Meses</title> 
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
                    <a :href="route('months.edit', month.id)" class="px-3 py-1 bg-blue-500 text-white rounded-md">
                        Bloquear días
                    </a>
                    <a :href="route('months.consult', month.id)" class="px-3 py-1 bg-blue-500 text-white rounded-md">
                        Consulta mis bloqueos
                    </a>
                    
                    <a v-if="month.handled === 'ORGANIZED'"
                    :href="route('months.see_guards', month.id)"
                    class="px-3 py-1 bg-blue-500 text-white rounded-md">
                        Ver calendario de guardias
                    </a>
                    <span v-else class="px-3 py-1 bg-gray-500 text-white rounded-md">
                        Calendario aún no disponible
                    </span>
                    <!-- <Link :href="route('months.edit', month.id)" class="px-3 py-1 bg-blue-500 text-white rounded-md">
                        Bloquear días
                    </Link>
                    <Link :href="route('months.consult', month.id)" class="px-3 py-1 bg-blue-500 text-white rounded-md">
                        Consultar mes
                    </Link> -->

                    <!-- <Link :href="route('guards.edit', month.id)" class="px-3 py-1 bg-blue-500 text-white rounded-md">
                        Editar guardias
                    </Link>
                    <Link :href="route('guards.consult', month.id)" class="px-3 py-1 bg-blue-500 text-white rounded-md">
                        Consultar mes
                    </Link> -->
                    </li>
                </ul>
                </div>
        </div>
    </AppLayout>
</template>