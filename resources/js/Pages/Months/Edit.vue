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
                <h1 style="margin: 10px; font-size: 18px;">Bloqueando días para <strong>{{ props.user.name }} </strong> el mes  <strong>{{month.name}}</strong></h1>
                <table class="bg-gray-100" style="width: 100%;">
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
                                <td v-for="dayOfWeek in 7" :key="dayOfWeek">
                                    <template v-if="!daysExtra[(week - 1) * 7 + dayOfWeek - 1]"></template>
                                    <template v-else-if="daysExtra[(week - 1) * 7 + dayOfWeek - 1].day === 44"></template>
                                    <template v-else>
                                        <div style="border: 2px solid black; padding: 10px; margin-bottom: 10px;">
                                            <div>
                                                <label :for="'checkbox_' + daysExtra[(week - 1) * 7 + dayOfWeek - 1].day" style="font-size: 20px;">{{ daysExtra[(week - 1) * 7 + dayOfWeek - 1].day }}</label>
                                            </div>
                                            <div>
                                                <input type="checkbox" :id="'checkbox_' + daysExtra[(week - 1) * 7 + dayOfWeek - 1].day" :value="daysExtra[(week - 1) * 7 + dayOfWeek - 1].day" v-model="selectedDays">
                                            </div>
                                        </div>
                                    </template>
                                </td>
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