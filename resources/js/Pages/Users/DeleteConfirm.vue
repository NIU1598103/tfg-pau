<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import {Link} from '@inertiajs/inertia-vue3';

    defineProps({
        name: String,
    })
</script>

<template>
    <AppLayout>
        <main v-if="!$page.props.auth.user.verified" class="grid min-h-full place-items-center bg-white px-6 py-24 sm:py-32 lg:px-8">
            <div class="text-center">
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl">Pendiente de validación</h1>
            <p class="mt-6 text-base leading-7 text-gray-600">Tendrás que esperar a que tu cuenta sea validada por un administrador.</p>
            
            </div>
        </main>
        <main v-else class="grid min-h-full place-items-center bg-white px-6 py-24 sm:py-32 lg:px-8">
            <div class="text-center">
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl">Usuario {{ name }} eliminado.</h1>
            <p class="mt-6 text-base leading-7 text-gray-600">La cuenta ha sido eliminada con éxito.</p>
        </div>
        
        </main>
    </AppLayout>
</template>