<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';

    import { usePage } from '@inertiajs/inertia-vue3';

    const page = usePage();

</script>

<template>
    <AppLayout>
        <main v-if="$page.props.auth.user.verified" class="grid min-h-full place-items-center bg-white px-6 py-24 sm:py-32 lg:px-8">
            <div class="text-center">
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl">¡Acción completada con éxito!</h1>
            <p class="mt-6 text-base leading-7 text-gray-600">Hola {{ $page.props.auth.user.name }}, tu petición se ha realizado exitosamente.</p>
            </div>
        </main>
        <main v-else class="grid min-h-full place-items-center bg-white px-6 py-24 sm:py-32 lg:px-8">
            <div class="text-center">
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl">Acceso no autorizado</h1>
            <p class="mt-6 text-base leading-7 text-gray-600">Acceso sólo para usuarios con la cuenta verificada.</p>
            
            </div>
        </main>
    </AppLayout>
</template>