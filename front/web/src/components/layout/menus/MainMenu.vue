<template>
  <q-scroll-area style="width: 255px; height: 100%">
    <q-list no-border link inset-delimiter class="left-drawer-list">
      <template v-for="section in menuData">
        <menu-template v-bind:key="section.name" :sectionData="section" bordered/>
      </template>
    </q-list>
  </q-scroll-area>
</template>

<script>
import MenuTemplate from '../../../components/layout/menus/MenuTemplate'
import MenuAudi from '../../../components/layout/menus/MenuAuditoria'

export default {
  components: {
    MenuTemplate,
    MenuAudi
  },
  computed: {
    menuData () {
      return this.$store.getters['user/menuData']
      // let data = this.$store.getters['user/menuData']
      // data.sort(function (a, b) {
      //   return a.ord - b.ord
      // })
      // return data
    }
  }
}
</script>
