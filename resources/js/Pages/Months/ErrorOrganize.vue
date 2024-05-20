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
            <h1 class="text-base font-semibold leading-7 text-gray-900 mt-4 mb-2">Users sin días bloqueados el mes <b>{{ month.name }}</b>:</h1>
                <ul role="list" class="divide-y divide-gray-200 bg-gray-100 p-4">
                    <li v-for="user in users" :key="user.id" class="flex justify-between gap-x-6 py-5">
                    <div class="flex min-w-0 gap-x-4">
                        <div class="min-w-0 flex-auto">
                            <p class="text-sm font-semibold leading-6 text-gray-900">{{ user.name }}</p>
                        </div>
                    </div>
                    
                    </li>
                </ul>
                </div>
        </div>
    </AppLayout>
</template>