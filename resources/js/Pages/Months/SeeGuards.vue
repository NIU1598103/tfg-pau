<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import { ref } from 'vue';
    import { Inertia } from '@inertiajs/inertia';
    import {useForm} from '@inertiajs/inertia-vue3';
    import { Head } from '@inertiajs/vue3';
    import html2pdf from 'html2pdf.js';

    const props = defineProps({
        month: Object,
        daysExtra: Array,
        
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

    const downloadPDF = () => {
        const element = document.getElementById('pdf-content');
        const options = {
        margin: [0, 2, 0, 2], // [arriba, derecha, abajo, izquierda] en mm
        filename: `calendario_${props.month.name}.pdf`,
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };
        
        html2pdf().from(element).set(options).save();
    };
</script>

<template>
    <Head>
        <title>Consultar días</title> 
    </Head>
    <AppLayout>
        <div class="flex justify-center h-screen bg-white"  id="pdf-content">
            <div>
            <h1 style="margin: 10px; font-size: 18px;"> Calendario de guardias para el mes <strong>{{ month.name }}</strong> </h1>
                <table class="bg-gray-100" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Lunes</th>
                            <th>Martes</th>
                            <th>Miércoles</th>
                            <th>Jueves</th>
                            <th>Viernes</th>
                            <th>Sábado</th>
                            <th>Domingo</th>
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
                                            <div v-if="daysExtra[(week - 1) * 7 + dayOfWeek - 1].ref_name !== null">
                                                {{ daysExtra[(week - 1) * 7 + dayOfWeek - 1].user_name }} + {{ daysExtra[(week - 1) * 7 + dayOfWeek - 1].ref_name }}
                                            </div>
                                            <div v-else>
                                                {{ daysExtra[(week - 1) * 7 + dayOfWeek - 1].user_name }}
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
                <button @click="downloadPDF" class="mt-2 bg-blue-500 text-white py-2 px-4 rounded">Descargar PDF</button>

            </div>
        </div>
    </AppLayout>
</template>