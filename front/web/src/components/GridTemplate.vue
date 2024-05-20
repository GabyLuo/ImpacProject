<template >
  <q-table
    :data="serverData"
    :columns="columns"
    :filter="filter"
    row-key="name"
    :pagination.sync="serverPagination"
    :loading="loading"
    @request="request"
    v-show="state === 'fetch'"
  >
    <template slot="top" slot-scope="props">
      <q-item-side left>
        <q-search hide-underline inverted color="grey" v-model="filter" />
      </q-item-side>
      <q-item-main label="">
      </q-item-main>
      <q-item-side right>
        <q-btn icon="add" color="blue" v-if="state == 'fetch'"  @click="insert" />
      </q-item-side>
    </template>
    <template slot="top-right" slot-scope="props">
      <q-btn icon="add" color="blue" v-if="state == 'fetch'"  @click="insert" />
    </template>
  </q-table>
</template>

<script>
import axios from 'axios'

export default {
  name: 'grid-template',
  props: ['columns', 'dataRoute'],
  data () {
    return {
      state: 'fetch',
      filter: '',
      loading: false,
      serverPagination: {
        page: 1,
        sortBy: null,
        descending: false,
        rowsNumber: 10 // specifying this determines pagination is server-side
      },
      serverData: []
    }
  },
  methods: {
    request ({pagination, filter}) {
      this.loading = true
      let cleanFilter = filter
      if (cleanFilter === '') {
        cleanFilter = '_none_'
      }
      let columns = []
      for (var column in this.columns) {
        columns.push(this.columns[column].field)
      }
      let columnString = columns.join(',')
      axios.get(`${this.dataRoute}/${pagination.rowsPerPage}/${pagination.page}/${pagination.sortBy}/${pagination.descending}/${cleanFilter}/${columnString}`)
        .then(({data}) => {
          this.serverPagination = pagination
          this.serverPagination.rowsNumber = data.page.total_items
          this.serverData = data.page.items
          this.loading = false
        })
        .catch(error => {
          // there's an error... do SOMETHING
          console.log(error)
          // we tell QTable to exit the "loading" state
          this.loading = false
        })
    },
    insert () {
      this.state = 'insert'
      this.$emit('insert')
    },
    fetch () {
      this.state = 'fetch'
    }
  },
  mounted () {
    this.request({
      pagination: this.serverPagination,
      filter: this.filter
    })
  }
}
</script>
