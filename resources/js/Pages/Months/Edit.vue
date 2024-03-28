<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { ref } from 'vue';
    import { Inertia } from '@inertiajs/inertia';
    import {useForm} from '@inertiajs/inertia-vue3';
    import { Head } from '@inertiajs/vue3';

    const props = defineProps({
        month: Object,
        days: Array,
        daysExtra: Array,
        user: Object,
        
    });
    const weeksInMonth = () => {
            const [year, month_number] = props.month.name.split('-');
            // month_number = 11;
            var firstOfMonth = new Date(year, month_number - 1, 1);
            var day = firstOfMonth.getDay() || 6;
            day = day === 1 ? 0 : day;
            if (day) { day-- }
            var diff = 7 - day;
            var lastOfMonth = new Date(year, month_number, 0);
            var lastDate = lastOfMonth.getDate();
            if (lastOfMonth.getDay() === 1) {
                diff--;
            }
            var result = Math.ceil((lastDate - diff) / 7);
            return result + 1;
        };

        const selectedDays = ref([]);
        const submitForm = () => {
            const formattedSelectedDays = {};
            selectedDays.value.forEach(day => {
                const monthName = props.month.name;
                if (!formattedSelectedDays.hasOwnProperty(monthName)) {
                    formattedSelectedDays[monthName] = [];
                }
                formattedSelectedDays[monthName].push(day);
            });
            console.log('Días seleccionados el mes', props.month.name,':', formattedSelectedDays[props.month.name]);
            console.log('Días seleccionados el mes', props.month.name,':', selectedDays.value);
            const form = useForm({
                month: props.month.name,
                days_selected: selectedDays.value,
            });
            // Inertia.post(route("users.update_blocked_days"), formattedSelectedDays);
            form.post('/users/update_blocked_days');
    };


</script>

<template>
    <Head>
        <title>Bloquear días</title> 
    </Head>
    <AppLayout>
        <form @submit.prevent="submitForm">

        <div class="flex justify-center h-screen bg-white">
            <div>
            <h1> {{month.name}}, {{ weeksInMonth() }}, {{ props.user.name }} </h1>
                <table class="bg-gray-100">
                    <thead>
                        <tr>
                            <th>Lun</th>
                            <th>Mar</th>
                            <th>Mié</th>
                            <th>Jue</th>
                            <th>Vie</th>
                            <th>Sáb</th>
                            <th>Dom</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="week in weeksInMonth()" :key="week">
                            <tr>

                                <!-- <div v-for="day in daysExtra">{{day.day}}</div> -->
                                <!-- <template v-if="added === 1"> -->
                                <td v-for="dayOfWeek in 7" :key="dayOfWeek">
                                    <template v-if="!daysExtra[(week - 1) * 7 + dayOfWeek - 1]"></template>
                                    <template v-else-if="daysExtra[(week - 1) * 7 + dayOfWeek - 1].day === 44"></template>
                                    <template v-else>
                                        <input type="checkbox" :id="'checkbox_' + daysExtra[(week - 1) * 7 + dayOfWeek - 1].day" :value="daysExtra[(week - 1) * 7 + dayOfWeek - 1].day" v-model="selectedDays">
                                        <label :for="'checkbox_' + daysExtra[(week - 1) * 7 + dayOfWeek - 1].day">{{ daysExtra[(week - 1) * 7 + dayOfWeek - 1].day }}</label>
                                        <!-- {{ daysExtra[(week - 1) * 7 + dayOfWeek - 1].day }} -->
                                    </template>
                                </td>
                                <!-- </template>
                                <template v-else>
                                <td v-for="dayOfWeek in 7" :key="dayOfWeek">
                                    <template v-if="daysExtra[(week - 1) * 7 + dayOfWeek - 1]">
                                        <input type="checkbox" :id="'checkbox_' + daysExtra[(week - 1) * 7 + dayOfWeek - 1].day" :value="daysExtra[(week - 1) * 7 + dayOfWeek - 1].day" v-model="selectedDays">
                                        <label :for="'checkbox_' + daysExtra[(week - 1) * 7 + dayOfWeek - 1].day">{{ daysExtra[(week - 1) * 7 + dayOfWeek - 1].day }}</label>
                                    </template>
                                    <template v-else></template>
                                </td>
                                </template> -->
                            </tr>
                        </template>
                    </tbody>
                </table>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 mt-4 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Bloquear días</button>
            </div>
        </div>
        </form>
    </AppLayout>
</template>