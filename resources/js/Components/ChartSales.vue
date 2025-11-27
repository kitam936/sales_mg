<script setup>
    import { ref, onMounted, watch } from 'vue'
    import  Chart  from 'chart.js/auto'


    const props = defineProps({
        labels: Array,
        values: Array,
    })

    const canvas = ref(null)
    let chart = null

    watch(() => props.values, () => {
        if (chart) chart.destroy()
        createChart()
    })

    function createChart() {
        chart = new Chart(canvas.value, {
            type: 'line',
            data: {
                labels: props.labels,
                datasets: [{
                    label: '売上数',
                    data: props.values,
                    borderColor: 'blue',       // 線の色
                    backgroundColor: 'rgba(0,0,255,0.2)', // 塗りつぶしの色（薄青）
                    fill: false,
                    tension: 0.2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        })
    }

    onMounted(createChart)
    </script>

    <template>
        <div class="w-full overflow-x-auto" style="height: 300px;">
            <canvas ref="canvas"></canvas>
        </div>
    </template>
