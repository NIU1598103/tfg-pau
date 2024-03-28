<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import {Link} from '@inertiajs/inertia-vue3';
    import {useForm} from '@inertiajs/inertia-vue3';


    const props = defineProps({
        user: Object,
    });

    const submit = () => {
            
            const form = useForm({
                name: props.user.name,
                id: props.user.id,
            });
            // Inertia.post(route("users.update_blocked_days"), formattedSelectedDays);
            form.post('/users/delete_confirm');
    };
</script>

<template>
    <AppLayout>
        <main v-if="!$page.props.auth.user.admin" class="grid min-h-full place-items-center bg-white px-6 py-24 sm:py-32 lg:px-8">
            <div class="text-center">
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl">Acceso no autorizado</h1>
            <p class="mt-6 text-base leading-7 text-gray-600">Acceso sólo para usuarios con rol de administrador.</p>
            
            </div>
        </main>
    <main v-else class="grid min-h-full place-items-center bg-white px-6 py-24 sm:py-32 lg:px-8">
        <form @submit.prevent="submit">

            <div class="text-center">
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl">¿Eliminar a {{ user.name }}?</h1>
            <p class="mt-6 text-base leading-7 text-gray-600">Al hacerlo, la cuenta será eliminada por completo.</p>
        </div>
        <div class="flex justify-center">
        <!-- <Link :href="route('users.delete_confirm', user.id)" class="px-3 py-1 bg-red-500 text-white rounded-md mr-2">
                    Eliminar
                </Link> -->
        <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded-md mr-2">
            Eliminar</button>
        <Link :href="route('users.index')" class="px-3 py-1 bg-blue-500 text-white rounded-md">
            Cancelar
        </Link>
        </div>
        </form>
    </main>
    </AppLayout>
</template>