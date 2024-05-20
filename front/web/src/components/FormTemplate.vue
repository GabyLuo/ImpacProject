<template>
  <div v-show="state !== 'fetch'" class="q-table-container q-table-top">
    <div class="row gutter-sm">
      <q-item-side left>
        <q-btn icon="undo" color="red" v-if="state == 'insert'" @click="cancel" />
      </q-item-side>
      <q-item-main>
      </q-item-main>
      <q-item-side right>
        <q-btn icon="add" color="blue" v-if="state == 'fetch'"  @click="insert" />
        <q-btn icon="save" color="blue" v-if="state == 'insert'" @click="saveInsert" />
      </q-item-side>
    </div>
    <q-field icon="security" helper="Helper">
      <div class="row no-wrap">
        <q-input inverted color="blue" v-model="state" type="email" suffix="@email.com" float-label="Email address" />
        <q-input inverted color="blue" v-model="state" type="email" suffix="@email.com" float-label="Email address" />
        <q-input inverted color="blue" v-model="state" type="email" suffix="@email.com" float-label="Email address" />
      </div>
    </q-field>
  </div>
</template>

<script>
export default {
  name: 'form-template',
  props: ['route'],
  data () {
    return {
      state: 'fetch'
    }
  },
  methods: {
    insert () {
      this.state = 'insert'
    },
    fetch () {
      this.state = 'fetch'
    },
    saveInsert () {

    },
    saveEdit () {

    },
    cancel () {
      this.fetch()
      this.$emit('cancel')
    }
  }
}
</script>

<style scoped>
div.no-wrap .q-input{
  margin: 0 5px 5px;
}
.q-field {
  margin-top: 16px;
}
</style>
