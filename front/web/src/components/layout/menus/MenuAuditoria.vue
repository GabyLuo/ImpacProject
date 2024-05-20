<template>
  <div class="section-list">
    <!-- -->
    <q-list-header>
      <div class="q-list-header-helper" @click="showMe = !showMe" style="color: indigo-4;">
        <div class="row grey-4">
          <div class="col-xs-7">
            {{ sectionData.name }}
          </div>
          <div class="col-xs-1 offset-xs-4">
            <i class="iconoCatalogo" :class="[showMe ? '' : 'fa fa-angle-right', 'fa fa-angle-down']"></i>
          </div>
        </div>
      </div>
    </q-list-header>
    <!-- <q-card-separator /> -->
    <q-slide-transition>
      <div v-show="showMe" class="bg-grey-4" style="border-bottom: inset; border-width: 0.5px; border-bottom-color: #e1e1e1;">
        <template v-for="item in sectionData.items" >
          <q-item :key="item.label" class="q-item-extend" item :to="item.route" @click.native="navegar(item)" v-if="item.route!=='/reporte_projects'">
            <q-item-main @click="navegar(item)">
              <q-icon :name="item.icon" />
              <label class="q-item-label">{{item.label}}</label>
            </q-item-main>
          </q-item> <!-- aqui cambiamos cosas -->
          <q-item :key="item.label" class="q-item-extend" item v-if="item.label==='Auditoría' && item.route==='/reporte_projects'">
            <q-item-main>
              <!-- aqui modificamos -->
              <div class="q-list-header-helper" @click="showMe2 = !showMe2" v-if="item.label==='Licitaciones'">
                <div class="row">
                  <div class="col-xs-7">
                    <q-icon :name="item.icon" />
                    <label class="q-item-label">{{item.label}}</label>
                  </div>
                  <div class="col-xs-1 offset-xs-4">
                    <i class="iconoCatalogo" :class="[showMe2 ? '' : 'fa fa-angle-right', 'fa fa-angle-down']"></i>
                  </div>
                </div>
              </div>
              <div v-show="showMe2" class="bg-grey-4" v-if="item.label==='Licitaciones'" style="margin-top: 15px;">
                <template>
                  <q-item key="Licitaciones" class="q-item-extend" item to="/licitaciones" @click.native="navegarSingle('/licitaciones')">
                    <q-item-main @click="navegarSingle('/licitaciones')">
                      <q-icon name="local_library" />
                      <label class="q-item-label">Licitaciones</label>
                    </q-item-main>
                  </q-item>
                  <q-item key="Fianzas" class="q-item-extend" item to="/fianzas" @click.native="navegarSingle('/fianzas')">
                    <q-item-main @click="navegarSingle('/fianzas')">
                      <q-icon name="folder" />
                      <label class="q-item-label">Fianzas</label>
                    </q-item-main>
                  </q-item>
                  <!-- <q-item key="Repositorio" class="q-item-extend" item to="/repositorio" @click.native="navegarSingle('/repositorio')">
                    <q-item-main @click="navegarSingle('/repositorio')">
                      <q-icon name="folder" />
                      <label class="q-item-label">Repositorio</label>
                    </q-item-main>
                  </q-item> -->
                </template>
              </div>
              <!-- aqio termina la modificacion -->
            </q-item-main>
          </q-item> <!-- aqui termina -->
        </template>
      </div>
    </q-slide-transition>
    <!-- <q-list inset-delimiter/> -->
    <!-- <q-card-separator /> -->
    <!-- aqui va lo demas-->
  </div>
</template>

<script>
export default {
  name: 'menu-audi',
  props: ['sectionData'],
  data () {
    return {
      showMe: false,
      // la linea 51 no estaba
      showMe2: false
    }
  },
  methods: {
    navegar (item) {
      if (window.location.href === ('http://impact.beta.antfarm.mx' + item.route)) {
        window.location.assign('http://impact.beta.antfarm.mx' + item.route)
        // this.$route(window.location.href, 'http://impact.beta.antfarm.mx' + item.route)
      }
    },
    navegarSingle (item) {
      console.log(item)
      if (window.location.href === ('http://impact.beta.antfarm.mx' + item)) {
        window.location.assign('http://impact.beta.antfarm.mx' + item)
        // this.$route(window.location.href, 'http://impact.beta.antfarm.mx' + item.route)
      }
    }
  },
  watch: {
    $route (to, from) {
      this.$cssHelper()
    }
  }
}
</script>
