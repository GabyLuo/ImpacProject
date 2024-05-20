<template>
  <div id="chart">
    <apexchart type="donut" height="400" :options="chartOptions" :series="series"></apexchart>
  </div>
</template>

<script>
import VueApexCharts from 'vue-apexcharts'
import Vue from 'vue'
Vue.use(VueApexCharts)
Vue.component('apexchart', VueApexCharts)

export default {
  name: 'Donut',
  props: ['labelsD', 'seriesD'],
  data () {
    return {
      series: [],
      chartOptions: {},
      labels: []
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
    },
    labelsD (val) {
      this.chartOptions = {
        chart: {
          type: 'donut'
        },
        series: this.seriesD,
        legend: {
          show: true,
          position: 'bottom'
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
        // colors: ['#f44336', '#9c27b0', '#2196f3', '#4caf50', '#ff9800', '#795548', '#ffeb3b', '#e91e63'],
        labels: this.labelsD,
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 200
            },
            legend: {
              position: 'bottom'
            }
          }
        }]
      }
    }
  },
  methods: {
    loadData () {
      this.series = this.seriesD
      this.chartOptions = {
        chart: {
          type: 'donut'
        },
        series: this.seriesD,
        legend: {
          show: true,
          position: 'bottom'
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
              return Number.parseFloat(val).toFixed(1).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,') + ' MXN'
            }
          }
        },
        // colors: ['#f44336', '#9c27b0', '#2196f3', '#4caf50', '#ff9800', '#795548', '#ffeb3b', '#e91e63'],
        labels: this.labelsD,
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 200
            },
            legend: {
              position: 'bottom'
            }
          }
        }]
      }
    }
  }
}
</script>
