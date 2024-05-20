<template>
  <div id="chart">
    <apexchart type="line" height="400" :options="chartOptions" :series="series"></apexchart>
  </div>
</template>

<script>
import VueApexCharts from 'vue-apexcharts'
import Vue from 'vue'
Vue.use(VueApexCharts)
Vue.component('apexchart', VueApexCharts)

export default {
  name: 'LineChart',
  props: ['seriesD'],
  data () {
    return {
      series: [],
      chartOptions: {}
    }
  },
  created () {
  },
  mounted () {
    this.loadData()
  },
  watch: {
    seriesD (val) {
      this.series = val
    }
  },
  methods: {
    loadData () {
      this.series = this.seriesD
      this.chartOptions = {
        chart: {
          type: 'line',
          toolbar: {
            show: true
          }
        },
        series: this.seriesD,
        stroke: {
          show: true,
          curve: 'smooth',
          lineCap: 'butt',
          colors: undefined,
          width: 5,
          dashArray: 0
        },
        xaxis: {
          categories: ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'],
          tooltip: {
            enabled: true,
            offsetY: -35
          }
        },
        yaxis: {
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false
          },
          labels: {
            show: true,
            formatter: function (val) {
              return Number.parseFloat(val).toFixed(1).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
            }
          }
        },
        dataLabels: {
          enabled: true,
          formatter: function (val) {
            return Number.parseFloat(val).toFixed(1).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
          }
        },
        plotOptions: {
          bar: {
            dataLabels: {
              columnWidth: '25%',
              position: 'top' // top, center, bottom
            }
          }
        },
        grid: {
          row: {
            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
            opacity: 0.5
          }
        }
      }
    }
  }
}
</script>
