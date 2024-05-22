<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { Link, Head } from '@inertiajs/inertia-vue3';
    defineProps({
        users: Array,
        month: Object,
    });

</script>

<template>
    <AppLayout>
        <div class="flex justify-center h-screen bg-white">
            <div>
                <h1 class="text-base font-semibold leading-7 text-gray-900 mt-4 mb-2 text-center">Users sin días bloqueados el mes <b>{{ month.name }}</b>:</h1>
                <ul role="list" class="flex flex-wrap justify-center gap-4 bg-gray-100 p-4">
                    <li v-for="user in users" :key="user.id" class="flex-none w-1/6 py-5">
                        <div class="flex min-w-0 gap-x-4 text-center">
                            <div class="min-w-0 flex-auto">
                                <p class="text-sm font-semibold text-gray-900">{{ user.name }}</p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>