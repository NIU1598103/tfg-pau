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
        daysBlocked: Array,
        
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




</script>

<template>
    <Head>
        <title>Consultar días</title> 
    </Head>
    <AppLayout>
        <div class="flex justify-center h-screen bg-white">
            <div>
            <h1 style="margin: 10px; font-size: 18px;"> Calendario de guardias para el mes <strong>{{ month.name }}</strong> </h1>
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
                                                <strong>{{ daysExtra[(week - 1) * 7 + dayOfWeek - 1].day }}</strong>
                                            </div>
                                            <div>
                                                {{ daysExtra[(week - 1) * 7 + dayOfWeek - 1].user_name }} + {{ daysExtra[(week - 1) * 7 + dayOfWeek - 1].ref_name }}
                                            </div>
                                            <div>
                                                Trasplante: {{ daysExtra[(week - 1) * 7 + dayOfWeek - 1].name_guardTransplant }}
                                            </div>
                                        </div>


                                    </template>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>