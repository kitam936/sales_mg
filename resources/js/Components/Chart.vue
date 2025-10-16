<script setup>
    import { Chart, registerables } from "chart.js";
    import { BarChart } from "vue-chart-3";
    import { reactive, computed, watch } from "vue";

    const props = defineProps({
      data: Object
    });

    // ラベルとデータを計算
    const labels = computed(() => props.data.labels || []);
    const totals = computed(() => props.data.totals || []);
    const movingAverages = computed(() => props.data.movingAverages || []);

    // Chart.js 登録
    Chart.register(...registerables);

    // datasets の順序を変えて線グラフを前面に表示
    const barData = reactive({
        labels: labels,
        datasets: [
          {
            label: '12ヶ月移動平均',
            data: movingAverages,
            borderColor: "rgba(255, 99, 132, 1)",
            backgroundColor: "rgba(255, 99, 132, 0.1)",
            type: 'line',
            fill: false,
            tension: 0.3,
            borderWidth: 2,
            pointRadius: 3
          },
          {
            label: '売上',
            data: totals,
            backgroundColor: "rgba(75, 192, 192, 0.5)", // 半透明にする
            type: 'bar'
          }
        ]
      });

      const options = {
        responsive: true,
        plugins: {
          legend: {
            position: 'top'
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                // 千円単位に変換
                const value = context.raw ?? 0;
                return (value / 1000).toLocaleString() + ' 千円';
              }
            }
          }
        },
        scales: {
          y: {
            ticks: {
              // Y軸の目盛りも千円単位で表示
              callback: function(value) {
                return (value / 1000).toLocaleString() + ' 千円';
              }
            }
          }
        }
      };
    </script>

    <template>
      <div v-if="props.data && props.data.labels && props.data.labels.length">
        <!-- <BarChart :chartData="barData" /> -->
        <BarChart :chartData="barData" :chartOptions="options" />
      </div>
    </template>
