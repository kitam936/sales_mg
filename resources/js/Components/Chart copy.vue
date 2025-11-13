<script setup>
    import { Chart, registerables } from "chart.js";
    import { BarChart } from "vue-chart-3";
    import { computed } from "vue";

    Chart.register(...registerables);

    const props = defineProps({
      data: Object
    });

    // ラベル
    const labels = computed(() => props.data.labels || []);

    // 元データ
    const sales = computed(() => (props.data.data?.map(d => Number(d.total)) || []));
    const profits = computed(() => (props.data.data?.map(d => Number(d.total_profit)) || []));

    // 移動平均
    const salesMovingAvg = computed(() =>
      (props.data.movingAverages || []).map(v => v === null ? null : Number(v))
    );
    const profitMovingAvg = computed(() =>
      (props.data.movingAveragesProfit || []).map(v => v === null ? null : Number(v))
    );

    // 粗利率（％）
    const profitRates = computed(() =>
      sales.value.map((s, i) => s > 0 ? ((profits.value[i] / s) * 100).toFixed(1) : 0)
    );

    // データを千円単位に変換
    const salesInK = computed(() => sales.value.map(v => v / 1000));
    const profitsInK = computed(() => profits.value.map(v => v / 1000));
    const salesMovingAvgInK = computed(() => salesMovingAvg.value.map(v => v === null ? null : v / 1000));
    const profitMovingAvgInK = computed(() => profitMovingAvg.value.map(v => v === null ? null : v / 1000));

    // 粗利正・負で分ける
    const profitsPositive = computed(() =>
      profitsInK.value.map(v => (v >= 0 ? v : 0))
    );

    const profitsNegative = computed(() =>
      profitsInK.value.map(v => (v < 0 ? v : 0))
    );

    // Chart.js データ
    const chartData = computed(() => ({
      labels: labels.value,
      datasets: [

        {
          label: "粗利 (正)",
          data: profitsPositive.value,
          backgroundColor: "rgba(255, 159, 64, 0.9)",
          stack: "stack1",
          yAxisID: "y"
        },
        {
            label: "売上",
            data: salesInK.value,
            backgroundColor: "rgba(75, 192, 192, 0.6)",
            stack: "stack1",
            yAxisID: "y"
          },
        {
          label: "粗利 (負)",
          data: profitsNegative.value,
          backgroundColor: "rgba(255, 99, 132, 0.9)",
          stack: "stack1",
          yAxisID: "y"
        },
        {
          label: "売上12ヶ月移動平均",
          data: salesMovingAvgInK.value,
          borderColor: "rgba(255, 99, 132, 1)",
          backgroundColor: "rgba(255, 99, 132, 0.1)",
          type: "line",
          fill: false,
          tension: 0.3,
          borderWidth: 2,
          pointRadius: 2,
          yAxisID: "y"
        },
        {
          label: "粗利12ヶ月移動平均",
          data: profitMovingAvgInK.value,
          borderColor: "rgba(0, 0, 128, 1)",
          backgroundColor: "rgba(0, 128, 0, 0.1)",
          type: "line",
          fill: false,
          tension: 0.3,
          borderWidth: 2,
          pointRadius: 2,
          yAxisID: "y"
        },
        {
        label: "粗利率（％）",
        data: profitRates.value.map((v, i) => ({ x: i, y: v })), // scatter は {x, y} 形式
        type: "scatter",
        yAxisID: "y1",
        borderColor: "rgba(54, 162, 235, 1)",
        backgroundColor: "rgba(54, 162, 235, 0.1)",
        pointRadius: 3,
        showLine: true// 線は描かない
        }
      ]
    }));

    // Chart.js オプション
    const options = {
      responsive: true,
      interaction: { mode: "index", intersect: false },
      plugins: {
        legend: { position: "top" },
        tooltip: {
          callbacks: {
            label: function(context) {
              if (context.dataset.yAxisID === "y1") {
                return `${context.raw}%`;
              } else {
                return context.raw.toLocaleString() + " 千円";
              }
            }
          }
        },
        title: {
          display: true,
          text: "売上・粗利・12ヶ月移動平均・粗利率",
          font: { size: 16 }
        }
      },
      scales: {
        x: { stacked: true },
        y: {
          type: "linear",
          stacked: true,
          beginAtZero: true,
          title: { display: true, text: "金額（千円）" },
          ticks: { callback: value => value.toLocaleString() + " 千円" },
          grid: { drawTicks: true, drawBorder: true }
        },
        y1: {
          type: "linear",
          position: "right",
          stacked: false,
          offset: true,
          beginAtZero: true,
          title: { display: true, text: "粗利率（%）" },
          ticks: { callback: value => value + "%" },
          grid: { drawOnChartArea: false }
        }
      }
    };
    </script>

    <template>
      <div v-if="props.data?.labels?.length">
        <BarChart :chartData="chartData" :chartOptions="options" />
      </div>
    </template>
