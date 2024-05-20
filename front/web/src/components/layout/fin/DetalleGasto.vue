<template>
  <q-card v-if="show" style="margin-top:15px;">
    <q-card-main>
      <q-btn @click="close()" color="negative" icon="visibility_off">
        <q-tooltip>Ocultar información</q-tooltip>
      </q-btn>
      <div class="row q-mt-lg subtitulos" style="margin-left:15px;">
          Datos generales del gasto
      </div>
      <div class="row q-mt-lg">
        <div class="col-sm-3">
          <q-field icon="folder" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.nombre_actividad" type="text" inverted-color="dark" float-label="Actividad"></q-input>
          </q-field>
        </div>
        <div class="col-sm-3">
          <q-field icon="business" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.tipo_gasto" type="text" inverted-color="dark" float-label="Tipo de gasto"></q-input>
          </q-field>
        </div>
        <div class="col-sm-3">
          <q-field icon="date_range" icon-color="dark">
             <q-datetime readonly v-model="form.data.created" type="date" inverted-color="dark" float-label="Fecha" align="center"></q-datetime>
          </q-field>
        </div>
        <div class="col-sm-3">
          <q-checkbox readonly v-model="form.data.comprobado" label="Comprobar gasto" color="amber"/>
        </div>
        <div class="col-sm-12">
          <q-field icon="label" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.descripcion_gasto" type="text" inverted-color="dark" float-label="Descripción"></q-input>
          </q-field>
        </div>
        <div class="col-sm-3">
          <q-field icon="fas fa-dollar-sign" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.costo" type="text" inverted-color="dark" float-label="Presupuesto actual"></q-input>
          </q-field>
        </div>
        <div class="col-sm-3">
          <q-field icon="fas fa-dollar-sign" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.cantidad_disponible" type="text" inverted-color="dark" float-label="Presupuesto disponible"></q-input>
          </q-field>
        </div>
        <div class="col-sm-6">
          <q-field icon="fas fa-dollar-sign" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.monto_total_pagar" type="text" inverted-color="dark" float-label="Monto total a pagar"></q-input>
          </q-field>
        </div>
      </div>
      <div class="row q-mt-lg subtitulos" style="margin-left:15px;">
          Proveedor y datos bancarios
      </div>
      <div class="row q-mt-lg">
        <div class="col-sm-9">
          <q-field icon="fas fa-building" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.razon_social" inverted-color="dark" float-label="Razón social"/>
          </q-field>
        </div>
        <div class="col-sm-3">
          <q-field icon="fas fa-id-card" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.rfc" inverted-color="dark" float-label="RFC"/>
          </q-field>
        </div>
        <div class="col-sm-3">
          <q-field icon="account_balance" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.banco" inverted-color="dark" float-label="Banco"/>
          </q-field>
        </div>
        <div class="col-sm-3" v-if="form.data.pago === 'banco1'">
          <q-field icon="vpn_key" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.clabe" inverted color="teal" float-label="CLABE"/>
          </q-field>
        </div>
        <div class="col-sm-3" v-else>
          <q-field icon="vpn_key" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.clabe" inverted-color="dark" float-label="CLABE"/>
          </q-field>
        </div>
        <div class="col-sm-3" v-if="form.data.pago === 'tarjeta1'">
          <q-field icon="vpn_key" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.tarjeta1" inverted color="teal" float-label="TARJETA"/>
          </q-field>
        </div>
        <div class="col-sm-3" v-else>
          <q-field icon="vpn_key" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.tarjeta1" inverted-color="dark" float-label="TARJETA"/>
          </q-field>
        </div>
        <div class="col-sm-3">
          <q-field icon="account_balance" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.banco2" inverted-color="dark" float-label="Banco 2"/>
          </q-field>
        </div>
        <div class="col-sm-3" v-if="form.data.pago === 'banco2'">
          <q-field icon="vpn_key" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.clabe2" inverted color="teal" float-label="CLABE 2"/>
          </q-field>
        </div>
        <div class="col-sm-3" v-else>
          <q-field icon="vpn_key" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.clabe2" inverted-color="dark" float-label="CLABE 2"/>
          </q-field>
        </div>
        <div class="col-sm-3" v-if="form.data.pago === 'tarjeta2'">
          <q-field icon="vpn_key" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.tarjeta2" inverted color="teal" float-label="TARJETA 2"/>
          </q-field>
        </div>
        <div class="col-sm-3" v-else>
          <q-field icon="vpn_key" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.tarjeta2" inverted-color="dark" float-label="TARJETA 2"/>
          </q-field>
        </div>
        <div class="col-sm-3" v-if="form.data.pago === 'toka'">
          <q-field icon="vpn_key" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.toka" inverted color="teal" float-label="toka"/>
          </q-field>
        </div>
        <div class="col-sm-3" v-else>
          <q-field icon="vpn_key" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.toka" inverted-color="dark" float-label="toka"/>
          </q-field>
        </div>
        <div class="col-sm-3">
          <q-field icon="account_balance" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.banco3" inverted-color="dark" float-label="Banco 3"/>
          </q-field>
        </div>
        <div class="col-sm-3" v-if="form.data.pago === 'banco3'">
          <q-field icon="vpn_key" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.clabe3" inverted color="teal" float-label="CLABE 3"/>
          </q-field>
        </div>
        <div class="col-sm-3" v-else>
          <q-field icon="vpn_key" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.clabe3" inverted-color="dark" float-label="CLABE 3"/>
          </q-field>
        </div>
        <div class="col-sm-3" v-if="form.data.pago === 'tarjeta3'">
          <q-field icon="vpn_key" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.tarjeta3" inverted color="teal" float-label="TARJETA 3"/>
          </q-field>
        </div>
        <div class="col-sm-3" v-else>
          <q-field icon="vpn_key" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.tarjeta3" inverted-color="dark" float-label="TARJETA 3"/>
          </q-field>
        </div>
        <div class="col-sm-3">
          <q-field icon="account_balance" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.banco4" inverted-color="dark" float-label="Banco 4"/>
          </q-field>
        </div>
        <div class="col-sm-3" v-if="form.data.pago === 'banco4'">
          <q-field icon="vpn_key" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.clabe4" inverted color="teal" float-label="CLABE 4"/>
          </q-field>
        </div>
        <div class="col-sm-3" v-else>
          <q-field icon="vpn_key" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.clabe4" inverted-color="dark" float-label="CLABE 4"/>
          </q-field>
        </div>
        <div class="col-sm-3" v-if="form.data.pago === 'tarjeta4'">
          <q-field icon="vpn_key" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.tarjeta4" inverted color="teal" float-label="TARJETA 4"/>
          </q-field>
        </div>
        <div class="col-sm-3" v-else>
          <q-field icon="vpn_key" icon-color="dark">
            <q-input readonly upper-case v-model="form.data.tarjeta4" inverted-color="dark" float-label="TARJETA 4"/>
          </q-field>
        </div>
      </div>
    </q-card-main>
  </q-card>
</template>

<script>
import { mapActions } from 'vuex'

export default {
  name: 'DetalleGasto',
  props: {
    show: {
      type: Boolean
    },
    gasto_id: {
      type: Number
    }
  },
  created () {},
  watch: {
    show (newValue, old) {
      if (newValue === true) {
        this.clickFila()
      } else {
        this.close()
      }
    }
  },
  data () {
    return {
      form: {
        data: []
      }
    }
  },
  computed: {},
  methods: {
    ...mapActions({
      getGastoById: 'fin/gastos/getById'
    }),
    close () {
      this.$emit('closeCard', false)
    },
    clickFila () {
      this.show = false
      this.getGastoById({id: this.gasto_id}).then(({data}) => {
        if (data.result === 'success') {
          this.form.data = ''
          this.form.data = data.gasto[0]
          if (this.form.data.cantidad_disponible < 0) {
            this.form.data.cantidad_disponible = 0
          }
        }
      }).catch(error => {
        console.error(error)
      })
      this.show = true
    }
  }
}
</script>
