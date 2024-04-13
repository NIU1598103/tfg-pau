<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { Link, Head } from '@inertiajs/inertia-vue3';
    import { ref, computed } from 'vue';
    import {useForm} from '@inertiajs/inertia-vue3';

    const props = defineProps({
        weeks: Array,
        guardians: Array,
    });
    const selectedGuardians = ref({});
    const perPage = 5;
    const currentPage = ref(1);

    const totalPages = computed(() => Math.ceil(props.weeks.length / perPage));

    const paginatedWeeks = computed(() => {
    const startIndex = (currentPage.value - 1) * perPage;
    const endIndex = startIndex + perPage;
    return props.weeks.slice(startIndex, endIndex);
    });

    const confirmGuardian = (week, guardian) => {
        // console.log('Semana seleccionada:', week);
        // console.log('Guardián seleccionado:', guardian);
        const form = useForm({
                    week: encodeURIComponent(JSON.stringify(week)),
                    guardian: guardian,
                });
        form.post('/weeks/update_guard_transplant');

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
    <AppLayout>
    <template v-if="$page.props.auth.user.admin">

      <div class="flex justify-center h-screen bg-white">
        <div>
          <h1 class="text-base font-semibold leading-7 text-gray-900 mt-4 mb-2">Semanas:</h1>
          <div v-for="(week, weekNumber) in paginatedWeeks" :key="weekNumber">
            <ul role="list" class="bg-gray-100 p-4">
              <li>
                <div v-for="(day, index) in week" :key="index">
                  <div v-if="day.week_day === 1">
                    Del día <b>{{ day.day }}/{{ day.month }}/{{ day.year }}</b>
                  </div>
                  <div v-if="day.week_day === 0">
                    al día <b>{{ day.day }}/{{ day.month }}/{{ day.year }}</b>
                    <div v-if="day.name_guardTransplant" class="bg-blue-200 mb-3">
                      Guardia de transplante: <b>{{ day.name_guardTransplant }}</b>
                    </div>
                    <div v-else class="bg-red-200 mb-3">
                      Guardia de transplante no asignado
                    </div>
                  </div>
                </div>
                <select v-model="selectedGuardians[weekNumber]" class="ml-3">
                  <option v-for="guardian in guardians" :key="guardian.id" :value="guardian.id">{{ guardian.name }}</option>
                </select>
                <button @click="confirmGuardian(week, selectedGuardians[weekNumber])" class="px-3 py-1 bg-blue-500 text-white rounded-md ml-10">Confirmar</button>
              </li>
            </ul>
          </div>
          <div class="p-2 flex justify-center">
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
  </template>