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

    // 粗利率（％）: ラベル数に揃えて安全に計算
    const profitRates = computed(() =>
        labels.value.map((_, i) => {
            const s = Number(sales.value[i] ?? 0);
            const p = Number(profits.value[i] ?? 0);
            let rate = s > 0 ? (p / s) * 100 : 0;
            // 上限下限で制限
            rate = Math.min(Math.max(rate, -100), 100);
            return Number(rate.toFixed(1));
        })
    );

    // データを千円単位
    const salesInK = computed(() => sales.value.map(v => v / 1000));
    const profitsInK = computed(() => profits.value.map(v => v / 1000));
    const salesMovingAvgInK = computed(() => salesMovingAvg.value.map(v => v === null ? null : v / 1000));
    const profitMovingAvgInK = computed(() => profitMovingAvg.value.map(v => v === null ? null : v / 1000));

    // Chart.js データ
    const chartData = computed(() => ({
      labels: labels.value,
      datasets: [
        {
          label: "売上",
          data: salesInK.value,
          backgroundColor: "rgba(75, 192, 192, 0.6)",
          stack: "stack1",
          yAxisID: "y",
          order: 1,
          barPercentage: 0.8,
          categoryPercentage: 0.8,
          grouped: false
        },
        {
            label: "粗利",
            data: profitsInK.value,
            backgroundColor: "rgba(0, 0, 128, 0.9)", // 濃い紺
            borderColor: "rgba(0, 0, 128, 1)",
            borderWidth: 1,
            yAxisID: "y",
            order: 2,
            barPercentage: 0.8,
            categoryPercentage: 0.8,
            grouped: false
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
          data: profitRates.value.map((v, i) => ({ x: i, y: v })),
          type: "scatter",
          yAxisID: "y1",
          borderColor: "rgba(54, 162, 235, 1)",
          backgroundColor: "rgba(54, 162, 235, 0.1)",
          pointRadius: 3,
          showLine: true
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
          beginAtZero: true,
          min: 0,         // 下限0%
          max: 100,       // 上限100%
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
