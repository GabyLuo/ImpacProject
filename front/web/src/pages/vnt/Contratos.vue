<template>
  <q-page>
    <div class="layout-padding">
      <div class="row" v-if="views.grid">
        <div class="col-sm-12">
          <div class="row justify-between">
            <div class="col-sm-6">
              <q-btn size="15px" icon="chrome_reader_mode" disable class="btn_categoria" label="CONTRATOS" />
            </div>
            <div class="col-sm-6">
              <div class="row justify-end">
                <div class="col-sm-4" style="text-align: right">
                  <q-btn @click="newRowContrato()" class="btn_nuevo" icon="add">
                    Nuevo
                  </q-btn>
                  <q-btn @click="updateProjects()" class="btn_nuevo" icon="add">
                    Actualizar
                  </q-btn>
                  <q-btn @click="updateFacturasCobranza()" class="btn_nuevo" icon="add">
                    Actualizar Facturas
                  </q-btn>
                  <q-btn @click="updateFacturasCobranzaProyectos()" class="btn_nuevo" icon="add">
                    Actualizar Facturas Proyectos
                  </q-btn>
                  <q-btn @click="updateFacturasRepetidas()" class="btn_nuevo" icon="add">
                    Actualizar Repetidos
                  </q-btn>
                </div>
              </div>
            </div>
          </div>

          <div class="row q-mt-sm" style="margin-top:50px;">
            <div class="row" style="width:60vw;">
              <q-search hide-underline color="secondary" v-model="form.filter"/>
            </div>
            <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
              <q-table id="sticky-table"
                :data="contratos"
                :columns="form.columns"
                :selected.sync="form.selected"
                :filter="form.filter"
                color="positive"
                title=""
                :dense="true"
                :pagination.sync="form.pagination"
                :loading="form.loading"
                :rows-per-page-options="form.rowsOptions">
                <template slot="body" slot-scope="props">
                  <q-tr :props="props">
                    <q-td key="num_contrato" :props="props" @click.native="clicFilaContrato(props.row)" style="cursor: pointer;">{{ props.row.num_contrato }}</q-td>
                    <q-td key="recurso" :props="props" @click.native="clicFilaContrato(props.row)" style="cursor: pointer;">{{ props.row.recurso }}</q-td>
                    <q-td key="licitacion" :props="props">{{ props.row.licitacion }}</q-td>
                    <q-td key="razon_social" :props="props">{{ props.row.razon_social }}</q-td>
                    <q-td key="fecha_inicio" :props="props"><label style="margin-right:14.2% !important;">{{ props.row.fecha_inicio }}</label></q-td>
                    <q-td key="fecha_fin" :props="props"><label style="margin-right:14.2% !important;">{{ props.row.fecha_fin }}</label></q-td>
                    <q-td key="acciones" :props="props">
                      <q-btn small flat @click="editRowContrato(props.row)" color="blue-6" icon="edit">
                        <q-tooltip>Editar</q-tooltip>
                      </q-btn>
                      <q-btn small flat @click="deleteSingleContrar(props.row.id)" color="negative" icon="delete">
                        <q-tooltip>Eliminar</q-tooltip>
                      </q-btn>
                    </q-td>
                  </q-tr>
                </template>
              </q-table>
              <q-inner-loading :visible="form.loading">
                <q-spinner-dots size="64px" color="primary" />
              </q-inner-loading>
            </div>
          </div>
        </div>
      </div>

    <!--Crear un contrato-->

      <div class="row" v-if="views.create">
        <div class="col-sm-12">
          <div class="row justify-between">
            <div class="col-sm-9">
              <h5 style="margin: 7px 0; font-weight: bold">NUEVO CONTRATO</h5>
            </div>
            <div class="col-sm-3">
              <div class="row justify-end">
                <div class="col-sm-4" style="text-align: right">
                  <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" />
                </div>
              </div>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-4">
              <q-field icon="fas fa-file" icon-color="dark" :error="$v.form.fields.num_contrato.$error" error-label="Ingrese el número de contrato">
                <q-input v-model="form.fields.num_contrato" type="number" inverted color="dark" float-label="Número de contrato" numeric-keyboard-toggle/>
              </q-field>
            </div>
            <div class="col-sm-8">
              <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fields.empresa_id.$error" error-label="Elija una empresa">
                <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-4" style="margin-top:5px;">
              <q-field icon="fas fa-microchip" icon-color="dark" :error="$v.form.fields.recurso_id.$error" error-label="Elija un proyecto">
                <q-select v-model="form.fields.recurso_id" inverted color="dark" float-label="Proyecto" :options="recursosOptions" filter @input="cargarLicitaciones()"/>
              </q-field>
            </div>
            <div class="col-sm-4" style="margin-top:5px;">
              <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha_inicio.$error" error-label="Ingrese la fecha inicio">
                <q-datetime v-model="form.fields.fecha_inicio" type="date" inverted color="dark" float-label="Fecha inicio" align="center"></q-datetime>
              </q-field>
            </div>
            <div class="col-sm-4" style="margin-top:5px;">
              <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha_fin.$error" error-label="Ingrese la fecha fin">
                <q-datetime v-model="form.fields.fecha_fin" type="date" inverted color="dark" float-label="Fecha fin" align="center"></q-datetime>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" v-if="cliente !== ''">
            <div class="col-sm-12">
              <q-field icon="fas fa-user" icon-color="dark">
                <q-input readonly inverted color="dark" float-label="Cliente" v-model="cliente"></q-input>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-8" style="margin-top:2px;">
              <q-field icon="fas fa-book" icon-color="dark">
                <q-select v-model="form.fields.licitacion_id" inverted color="dark" float-label="Licitación" :options="licitacionesOptions" filter clearable/>
              </q-field>
            </div>
            <div class="col-sm-4">
              <div class="icono_field">
                <q-field icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form.fields.monto_total.$error" error-label="Rango del monto ($0.00 - $1,000,000,000.00)">
                  <money v-model="form.fields.monto_total" v-bind="form.money" style="height:53px;width:100%;" v-bind:class="{ moneyBien: !($v.form.fields.monto_total.$error), moneyError: $v.form.fields.monto_total.$error }" @input="$v.form.fields.monto_total.$touch"></money>
                </q-field>
              </div>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-6">
              <q-field icon="fas fa-user" icon-color="dark" :error="$v.form.fields.rep_legal_contrato.$error" error-label="Ingrese el nombre del representante">
                <q-input upper-case inverted color="dark" float-label="Representante legal del contrato" v-model="form.fields.rep_legal_contrato"></q-input>
              </q-field>
            </div>
          </div>
          <div v-if="!Datofactura" class="row q-mt-lg">
             <div class="col-sm-2 offset-sm-10 pull-right">
              <q-btn @click="save()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
            </div>
          </div>
          <div v-if="Datofactura"  class="row q-mt-lg">

            <div class="col-sm-4">
              <q-field icon="description" icon-color="dark" :error="$v.form.file.nombre.$error" error-label="Ingrese el nombre del archivo">
                <q-input upper-case inverted color="dark" float-label="Nombre del archivo" v-model="form.file.nombre"></q-input>
              </q-field>
            </div>
            <div style="width: 230px">
              <q-field icon="date_range" icon-color="dark" :error="$v.form.file.fecha_factura.$error" error-label="Ingrese la fecha de factura">
                <q-datetime v-model="form.file.fecha_factura" type="date" inverted color="dark" float-label="Fecha de factura" align="center"></q-datetime>
              </q-field>
            </div>
            <div style="width: 230px" >
              <q-field icon="date_range" icon-color="dark" :error="$v.form.file.fecha_pago.$error" error-label="Ingrese la fecha de pago">
                <q-datetime  v-model="form.file.fecha_pago" type="date" inverted color="dark" float-label="Fecha de pago" align="center"></q-datetime>
              </q-field>
            </div>

            <q-field  icon="attach_file" icon-color="dark" >
              <input   style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="fileInputFormato" accept=".pdf,.docx" @change="uploadFormato()" />
              <q-btn style="height: 50px;width:160px"  @click="$refs.fileInputFormato.click()" class="btn_atach"  :loading="loadingButton">Adjuntar Factura</q-btn>
            </q-field>
          </div>
                  <div v-if="Datofactura" class="row q-mt-sm" style="margin-top:50px;">
            <div class="row" style="width:60vw;">
              <q-search hide-underline color="secondary" v-model="form.filter"/>
            </div>
            <div  class="col-sm-12 q-mt-sm" id="sticky-table-scroll" >
              <q-table id="sticky-table"
                :data="facturas"
                :columns="form.fileColumn"
                :selected.sync="form.selected"
                :filter="form.filter"
                color="positive"
                title=""
                :dense="true"
                :pagination.sync="form.pagination"
                :loading="form.loading"
                :rows-per-page-options="form.rowsOptions">
                <template slot="body" slot-scope="props">
                  <q-tr :props="props">
                    <q-td key="id" :props="props" style="cursor: pointer;">{{ props.row.factura_id }}</q-td>
                    <q-td key="nombre" :props="props"  style="cursor: pointer;">{{ props.row.name }}</q-td>
                    <q-td key="extension" :props="props">{{ props.row.doc_type }}</q-td>
                    <q-td key="fecha_factura" :props="props">{{ props.row.fecha_factura }}</q-td>
                    <q-td key="fecha_pago" :props="props">{{ props.row.fecha_pago }}</q-td>
                    <q-td key="acciones" :props="props">
                      <q-btn small flat @click="abrirDocumento(props.row.name+'.'+props.row.doc_type)" color="blue-6" icon="get_app">
                        <q-tooltip>Ver</q-tooltip>
                      </q-btn>
                      <q-btn small flat @click="deleteDoc(props.row.factura_id)" color="negative" icon="delete">
                        <q-tooltip>Eliminar</q-tooltip>
                      </q-btn>
                    </q-td>
                  </q-tr>
                </template>
              </q-table>
              <q-inner-loading :visible="form.loading">
                <q-spinner-dots size="64px" color="primary" />
              </q-inner-loading>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal editar CONTRATO -->
      <div class="row" v-if="views.update">
        <div class="col-sm-12">
          <div class="row justify-between">
            <div class="col-sm-9">
              <h5 style="margin: 7px 0; font-weight: bold">MODIFICAR CONTRATO</h5>
            </div>
            <div class="col-sm-3">
              <div class="row justify-end">
                <div class="col-sm-4" style="text-align: right">
                  <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" />
                </div>
              </div>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-4">
              <q-field icon="fas fa-file" icon-color="dark" :error="$v.form.fields.num_contrato.$error" error-label="Ingrese el número de contrato">
                <q-input v-model="form.fields.num_contrato" type="number" inverted color="dark" float-label="Número de contrato" numeric-keyboard-toggle/>
              </q-field>
            </div>
            <div class="col-sm-8">
              <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fields.empresa_id.$error" error-label="Elija una empresa">
                <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-4" style="margin-top:5px;">
              <q-field icon="fas fa-microchip" icon-color="dark" :error="$v.form.fields.recurso_id.$error" error-label="Elija un proyecto">
                <q-select v-model="form.fields.recurso_id" inverted color="dark" float-label="Proyecto" :options="recursosOptions" filter @input="cargarLicitaciones()"/>
              </q-field>
            </div>
            <div class="col-sm-4" style="margin-top:5px;">
              <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha_inicio.$error" error-label="Ingrese la fecha inicio">
                <q-datetime v-model="form.fields.fecha_inicio" type="date" inverted color="dark" float-label="Fecha inicio" align="center"></q-datetime>
              </q-field>
            </div>
            <div class="col-sm-4" style="margin-top:5px;">
              <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha_fin.$error" error-label="Ingrese la fecha fin">
                <q-datetime v-model="form.fields.fecha_fin" type="date" inverted color="dark" float-label="Fecha fin" align="center"></q-datetime>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" v-if="cliente !== ''">
            <div class="col-sm-12">
              <q-field icon="fas fa-user" icon-color="dark">
                <q-input readonly inverted color="dark" float-label="Cliente" v-model="cliente"></q-input>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-8">
              <q-field icon="fas fa-book" icon-color="dark">
                <q-select v-model="form.fields.licitacion_id" inverted color="dark" float-label="Licitación" :options="licitacionesOptions" filter clearable/>
              </q-field>
            </div>
            <div class="col-sm-4">
              <div class="icono_field">
                <q-field icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form.fields.monto_total.$error" error-label="Rango del monto ($0.00 - $1,000,000,000.00)">
                  <money v-model="form.fields.monto_total" v-bind="form.money" style="height:53px;width:100%;" v-bind:class="{ moneyBien: !($v.form.fields.monto_total.$error), moneyError: $v.form.fields.monto_total.$error }" @input="$v.form.fields.monto_total.$touch"></money>
                </q-field>
              </div>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-6">
              <q-field icon="fas fa-user" icon-color="dark" :error="$v.form.fields.rep_legal_contrato.$error" error-label="Ingrese el nombre del representante">
                <q-input upper-case inverted color="dark" float-label="Representante legal del contrato" v-model="form.fields.rep_legal_contrato"></q-input>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-2 offset-sm-10 pull-right">
              <q-btn @click="update()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
            </div>
          </div>

          <div v-if="Datofactura"  class="row q-mt-lg">

            <div class="col-sm-4">
              <q-field icon="description" icon-color="dark" :error="$v.form.file.nombre.$error" error-label="Ingrese el nombre del archivo">
                <q-input upper-case inverted color="dark" float-label="Nombre del archivo" v-model="form.file.nombre"></q-input>
              </q-field>
            </div>
            <div style="width: 230px">
              <q-field icon="date_range" icon-color="dark" :error="$v.form.file.fecha_factura.$error" error-label="Ingrese la fecha de factura">
                <q-datetime v-model="form.file.fecha_factura" type="date" inverted color="dark" float-label="Fecha de factura" align="center"></q-datetime>
              </q-field>
            </div>
            <div style="width: 230px" >
              <q-field icon="date_range" icon-color="dark" :error="$v.form.file.fecha_pago.$error" error-label="Ingrese la fecha de pago">
                <q-datetime  v-model="form.file.fecha_pago" type="date" inverted color="dark" float-label="Fecha de pago" align="center"></q-datetime>
              </q-field>
            </div>

            <q-field  icon="attach_file" icon-color="dark" >
              <input   style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="fileInputFormato" accept=".pdf,.docx" @change="uploadFormato()" />
              <q-btn style="height: 50px;width:160px"  @click="$refs.fileInputFormato.click()" class="btn_atach"  :loading="loadingButton">Adjuntar Factura</q-btn>
            </q-field>
          </div>
                  <div v-if="Datofactura" class="row q-mt-sm" style="margin-top:50px;">
            <div class="row" style="width:60vw;">
              <q-search hide-underline color="secondary" v-model="form.filter"/>
            </div>
            <div  class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
              <q-table id="sticky-table"
                :data="facturas"
                :columns="form.fileColumn"
                :selected.sync="form.selected"
                :filter="form.filter"
                color="positive"
                title=""
                :dense="true"
                :pagination.sync="form.pagination"
                :loading="form.loading"
                :rows-per-page-options="form.rowsOptions">
                <template slot="body" slot-scope="props">
                  <q-tr :props="props">
                    <q-td key="id" :props="props"  style="cursor: pointer;">{{ props.row.factura_id }}</q-td>
                    <q-td key="nombre" :props="props"  style="cursor: pointer;">{{ props.row.name }}</q-td>
                    <q-td key="extension" :props="props">{{ props.row.doc_type }}</q-td>
                    <q-td key="fecha_factura" :props="props">{{ props.row.fecha_factura }}</q-td>
                    <q-td key="fecha_pago" :props="props">{{ props.row.fecha_pago }}</q-td>
                    <q-td key="acciones" :props="props">
                      <q-btn small flat @click="abrirDocumento(props.row.factura_id,props.row.doc_type)" color="blue-6" icon="get_app">
                        <q-tooltip>Ver</q-tooltip>
                      </q-btn>
                      <q-btn small flat @click="deleteDoc(props.row.factura_id)" color="negative" icon="delete">
                        <q-tooltip>Eliminar</q-tooltip>
                      </q-btn>
                    </q-td>
                  </q-tr>
                </template>
              </q-table>
              <q-inner-loading :visible="form.loading">
                <q-spinner-dots size="64px" color="primary" />
              </q-inner-loading>
            </div>
          </div>
        </div>
      </div>

      <q-modal v-if="informacion" style="background-color: rgba(0,0,0,0.7);" v-model="informacion" :content-css="{width: '50vw', height: '320px'}">
        <q-modal-layout>
          <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
            <div class="col-sm-6">
              <q-toolbar-title>
                Contrato
              </q-toolbar-title>
            </div>
            <div class="col-sm-6 pull-right">
              <q-btn
                flat
                round
                dense
                color="white"
                @click="informacion = false"
                icon="fas fa-times-circle"
              />
            </div>
          </q-toolbar>
          <div class="row q-mt-lg" style="margin-top:5px;">
            <div class="col-sm-12" v-if="objetoInformacion!==null">
              <div style="font-weight:bold;font-size:1.2em;text-align:center;color:gray;" v-if="parseInt(objetoInformacion.num_contrato) > 0">Número de contrato: {{objetoInformacion.num_contrato}}</div>
              <ul style="list-style:none;padding-left:15px;">
                <li><label style="font-weight:bold;">Proyecto: </label><label style="margin-left:5px;">{{objetoInformacion.recurso}}</label></li>
                <li><label style="font-weight:bold;">Cliente: </label><label style="margin-left:5px;">{{objetoInformacion.cliente}}</label></li>
                <li><label style="font-weight:bold;">Empresa: </label><label style="margin-left:5px;">{{objetoInformacion.razon_social}}</label></li>
                <li><label style="font-weight:bold;">Licitación: </label><label style="margin-left:5px;">{{objetoInformacion.licitacion}}</label></li>
                <li><label style="font-weight:bold;">Fecha inicio: </label><label style="margin-left:5px;">{{objetoInformacion.fecha_inicio}}</label></li>
                <li><label style="font-weight:bold;">Fecha fin: </label><label style="margin-left:5px;">{{objetoInformacion.fecha_fin}}</label></li>
                <li><label style="font-weight:bold;">Representante legal del contrato: </label><label style="margin-left:5px;">{{objetoInformacion.rep_legal_contrato}}</label></li>
                <li><label style="font-weight:bold;">Monto: </label><label style="margin-left:5px;">{{objetoInformacion.monto_total}}</label></li>
              </ul>
            </div>
          </div>
        </q-modal-layout>
      </q-modal>

    </div>
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { required, minValue, maxValue, numeric, helpers, minLength } from 'vuelidate/lib/validators'
import moment from 'moment'
import {Money} from 'v-money'
import axios from 'axios'

const nombreRep = helpers.regex('nombreRep', /^[A-ZÁÉÍÓÚÑ]+(\s{1}[A-ZÁÉÍÓÚÑ]+)*$/)

export default {
  components: {Money},
  beforeRouteEnter (to, from, next) {
    next(vm => {
      // if (vm.$store.getters['user/rolesIds'].includes(vm.$roles.SUPER_ADMIN) || vm.$store.getters['user/privileges'].include[vm.$privileges.VER_patron]) {
      //   next()
      // } else {
      //   next(from.path === '/' ? '/dashboard' : from.path)
      // }
      let condicion = false
      let propiedades = vm.$store.getters['user/role']
      for (let i = 0; i < propiedades.length; i++) {
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase()) {
          condicion = true
        }
      }
      if (condicion) {
        next()
      } else {
        next(from.path === '/' ? '/dashboard' : from.path)
      }
    })
  },
  data () {
    return {
      loadingButton: false,
      informacion: false,
      objetoInformacion: null,
      licitacionesOptions: [],
      factura: false,
      views: {
        grid: true,
        create: false,
        update: false
      },
      form: {
        money: {
          decimal: '.',
          thousands: ',',
          suffix: ' MXN',
          precision: 2,
          masked: false
        },
        file: {
          nombre: '',
          fecha_pago: '',
          fecha_factura: ''
        },
        fields: {
          id: 0,
          recurso_id: 0,
          empresa_id: 0,
          licitacion_id: 0,
          fecha_inicio: '',
          fecha_fin: '',
          monto_total: 0,
          rep_legal_contrato: '',
          num_contrato: 0
        },
        // data: [],
        columns: [
          { name: 'num_contrato', label: 'Contrato', field: 'num_contrato', sortable: true, type: 'string', align: 'center' },
          { name: 'recurso', label: 'Proyecto', field: 'recurso', sortable: true, type: 'string', align: 'left' },
          { name: 'licitacion', label: 'Licitación', field: 'licitacion', sortable: true, type: 'string', align: 'left' },
          { name: 'razon_social', label: 'Empresa', field: 'razon_social', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha_inicio', label: 'Fecha firma', field: 'fecha_inicio', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_fin', label: 'Fecha fin', field: 'fecha_fin', sortable: true, type: 'string', align: 'center' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'left' }
        ],
        fileColumn: [
          { name: 'id', label: 'Factura', field: 'factura', sortable: true, type: 'string', align: 'center' },
          { name: 'nombre', label: 'Nombre Factura', field: 'nombre', sortable: true, type: 'string', align: 'center' },
          { name: 'extension', label: 'Extensión', field: 'extension', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_factura', label: 'Fecha de Factura', field: 'fecha_factura', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_pago', label: 'Fecha de pago', field: 'fecha_pago', sortable: true, type: 'string', align: 'center' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'center' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      modal: {
        x: 0,
        y: 0,
        transition: null
      }
    }
  },
  computed: {
    ...mapGetters({
      recursosOptions: 'vnt/recurso/selectOtherOptions',
      empresasOptions: 'vnt/empresa/selectOptions',
      contratos: 'vnt/contrato/contratos',
      facturas: 'vnt/contratoFactura/facturas'
    }),
    cliente () {
      let valor = ''
      if (parseInt(this.form.fields.recurso_id) > 0) {
        this.recursosOptions.forEach((element) => {
          if (parseInt(element.value) === parseInt(this.form.fields.recurso_id)) {
            valor = element.cliente
          }
        })
      }
      return valor
    },
    Datofactura () {
      return this.form.fields.id > 0
    }
  },
  created () {
    this.loadAll()
  },
  watch: {
    informacion (newValue, old) {
      if (newValue === false) {
        this.objetoInformacion = null
      }
    }
  },
  methods: {
    ...mapActions({
      getContratos: 'vnt/contrato/refresh',
      getContratosbyID: 'vnt/contrato/getByID',
      saveContrato: 'vnt/contrato/save',
      updateContrato: 'vnt/contrato/update',
      deleteContrato: 'vnt/contrato/delete',
      getRecursos: 'vnt/recurso/refresh',
      getEmpresas: 'vnt/empresa/refresh',
      getLicitacionesByRecurso: 'lic/licitacion/getByRecurso',
      getFacturas: 'vnt/contratoFactura/getFacturas',
      deleteDocFacturas: 'vnt/contratoFactura/delete',
      updateMontos: 'pmo/proyecto/updateMontos',
      updateFacturas: 'vnt/remisionesFactura/agregarNombre',
      updateFacturasProyecto: 'vnt/remisionesFactura/agregarNombreFacturas',
      remisionesRepetidas: 'vnt/remisionesFactura/remisionesRepetidas'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getRecursos()
      await this.getEmpresas()
      if (this.$route.params.id > 0) {
        await this.getContratosbyID({id: this.$route.params.id})
      } else {
        await this.getContratos()
      }
      this.form.loading = false
    },
    async cargarFacturas () {
      await this.getFacturas({factura_id: this.form.fields.id}).then(({data}) => {
      }).catch(error => {
        console.error(error)
      })
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    async cargarLicitaciones () {
      this.licitacionesOptions = []
      this.form.fields.licitacion_id = 0
      await this.getLicitacionesByRecurso({recurso_id: this.form.fields.recurso_id}).then(({data}) => {
        this.licitacionesOptions.push({label: '---Ninguno---', value: 0})
        if (data.licitaciones.length > 0) {
          data.licitaciones.forEach(licitacion => {
            this.licitacionesOptions.push({label: (licitacion.folio + ' - ' + licitacion.titulo), value: licitacion.id})
          })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    save () {
      this.$v.form.fields.$touch()
      if (!this.$v.form.fields.$error) {
        if (this.validarFechas(this.form.fields.fecha_inicio, this.form.fields.fecha_fin)) {
          this.loadingButton = true
          let params = this.form.fields
          this.saveContrato(params).then(({data}) => {
            this.loadingButton = false
            this.$showMessage(data.message.title, data.message.content)
            if (data.result === 'success') {
              this.form.fields.id = data.message.id
              this.cargarFacturas()
            }
          }).catch(error => {
            console.error(error)
          })
        } else {
          this.mensajeNotify('Revisa las fechas', 3000, 'negative', 'fas fa-exclamation-triangle', 'La fecha de inicio no debe ser mayor a la fecha fin.', 'top-right')
        }
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    update () {
      this.$v.form.fields.$touch()
      if (!this.$v.form.fields.$error) {
        if (this.validarFechas(this.form.fields.fecha_inicio, this.form.fields.fecha_fin)) {
          this.loadingButton = true
          let params = this.form.fields
          this.updateContrato(params).then(({data}) => {
            this.loadingButton = false
            this.$showMessage(data.message.title, data.message.content)
            if (data.result === 'success') {
              this.setView('update')
            }
          }).catch(error => {
            console.error(error)
          })
        } else {
          this.mensajeNotify('Revisa las fechas', 3000, 'negative', 'fas fa-exclamation-triangle', 'La fecha de inicio no debe ser mayor a la fecha fin.', 'top-right')
        }
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    async editRowContrato (row) {
      this.licitacionesOptions = []
      let contrato = { ...row }
      this.form.fields.id = contrato.id
      this.form.fields.recurso_id = contrato.recurso_id
      this.form.fields.empresa_id = contrato.empresa_id
      await this.cargarLicitaciones()
      if (this.form.fields.id > 0) {
        this.cargarFacturas()
      }
      if (contrato.licitacion_id === null) {
        this.form.fields.licitacion_id = 0
      } else {
        this.form.fields.licitacion_id = contrato.licitacion_id
      }
      if (contrato.fecha_inicio === null) {
        this.form.fields.fecha_inicio = ''
      } else {
        this.form.fields.fecha_inicio = moment(String(contrato.fecha_inicio)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      }
      this.form.fields.num_contrato = parseInt(contrato.num_contrato)
      if (contrato.fecha_fin === null) {
        this.form.fields.fecha_fin = ''
      } else {
        this.form.fields.fecha_fin = moment(String(contrato.fecha_fin)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      }
      this.form.fields.monto_total = contrato.monto_total
      this.form.fields.rep_legal_contrato = contrato.rep_legal_contrato
      this.setView('update')
    },
    deleteSingleContrar (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este contrato?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete(id)
      }).catch(() => {})
    },
    deleteDoc (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este Archivo?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.deleteDocID(id)
      }).catch(() => {})
    },
    async deleteDocID (id) {
      let params = {id: id}
      this.deleteDocFacturas(params).then(({data}) => {
        this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          this.cargarFacturas()
        }
      }).catch(error => {
        console.error(error)
      })
    },
    delete (id) {
      let params = {id: id}
      this.deleteContrato(params).then(({data}) => {
        this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          this.loadAll()
          this.setView('grid')
        }
      }).catch(error => {
        console.error(error)
      })
    },
    newRowContrato () {
      this.licitacionesOptions = []
      this.$v.form.$reset()
      this.form.fields.id = 0
      this.form.fields.recurso_id = 0
      this.form.fields.empresa_id = 0
      this.form.fields.licitacion_id = 0
      this.form.fields.fecha_inicio = ''
      this.form.fields.num_contrato = ''
      this.form.fields.fecha_fin = ''
      this.form.fields.monto_total = 0
      this.form.fields.rep_legal_contrato = ''
      this.setView('create')
    },
    clicFilaContrato (row) {
      this.informacion = true
      this.objetoInformacion = {'num_contrato': row.num_contrato, 'recurso': row.recurso, 'cliente': row.cliente, 'razon_social': row.razon_social, 'licitacion': row.licitacion, 'fecha_inicio': row.fecha_inicio, 'fecha_fin': row.fecha_fin, 'rep_legal_contrato': row.rep_legal_contrato, 'monto_total': '$' + this.formatMoney(row.monto_total)}
    },
    validarFechas (fechaInicio, fechaFin) {
      if (moment(String(fechaInicio)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss') <= moment(String(fechaFin)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')) {
        return true
      }
      return false
    },
    formatMoney (amount, decimalCount = 2, decimal = '.', thousands = ',') {
      try {
        decimalCount = Math.abs(decimalCount)
        decimalCount = isNaN(decimalCount) ? 2 : decimalCount

        const negativeSign = amount < 0 ? '-' : ''

        let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString()
        let j = (i.length > 3) ? i.length % 3 : 0

        return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : '')
      } catch (e) {
        console.log(e)
      }
    },
    mensajeNotify (message, time, color, icon, detail, position) {
      this.$q.notify({
        message: message,
        timeout: time,
        color: color,
        icon: icon,
        detail: detail,
        position: position
      })
    },
    uploadFormato () {
      this.$v.form.file.$touch()
      if (!this.$v.form.file.$error) {
        try {
          this.loadingButton = true
          this.$q.loading.show()
          let file = this.$refs.fileInputFormato.files[0]
          let formData = new FormData()
          formData.append('file', file)
          formData.append('nombre', this.form.file.nombre)
          formData.append('contrato_id', this.form.fields.id)
          formData.append('fecha_factura', moment(String(this.form.file.fecha_factura)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss'))
          formData.append('fecha_pago', moment(String(this.form.file.fecha_pago)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss'))
          if (file === null || file === undefined) {
            this.loadingButton = false
          } else if (file.type) {
            if (file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document' || file.type.split('/')[1].toLowerCase() === 'pdf') {
              this.$q.loading.show({message: 'Subiendo archivo...'})
              axios.post('facturasContratos/uploadArchivo', formData, {
                headers: {
                  'Content-Type': 'multipart/form-data'
                }
              }).then(response => {
                if (response.data.result === 'success') {
                  this.$showMessage('Exito', response.data.message.content)
                  this.cargarFacturas()
                  this.$v.form.$reset()
                  this.form.file.nombre = ''
                  this.form.file.fecha_pago = ''
                  this.form.file.fecha_factura = ''
                  if (this.views.update !== '') {
                    this.setView('update')
                  } else {
                    this.setView('create')
                  }
                } else {
                  if (response.data.err !== '') {
                    this.$showMessage('Errores en archivo', response.data.err)
                  }
                }
                this.loadingButton = false
                this.$q.loading.hide()
              }).catch(error => {
                this.loadingButton = false
                console.error(error)
                this.$q.loading.hide()
              })
            } else {
              this.loadingButton = false
              this.$showMessage('Error', 'El tipo de archivo no es el requerido para este requisito.')
            }
          } else {
            this.loadingButton = false
            this.$showMessage('Error', 'No ha seleccionado un archivo con las extensión .docx o .pdf')
          }
        } catch (error) {
          this.loadingButton = false
        }
      } else {
        this.$showMessage('Error', 'llene los campos obligatorios')
      }
    },
    abrirDocumento (name, ext) {
      console.log(name)
      let nombre = name
      let uri = process.env.API + `facturasContratos/getFile/${nombre}/${ext}`
      window.open(uri, '_blank')
    },
    updateProjects () {
      this.loadingButton = true
      let params = this.form.fields
      this.updateMontos(params).then(({data}) => {
        this.loadingButton = false
        this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          this.setView('update')
        }
      }).catch(error => {
        console.error(error)
      })
    },
    updateFacturasCobranza () {
      this.loadingButton = true
      let params = this.form.fields
      this.updateFacturas(params).then(({data}) => {
        this.loadingButton = false
        this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          this.setView('update')
        }
      }).catch(error => {
        console.error(error)
      })
    },
    updateFacturasCobranzaProyectos () {
      this.loadingButton = true
      let params = this.form.fields
      this.updateFacturasProyecto(params).then(({data}) => {
        this.loadingButton = false
        this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          this.setView('update')
        }
      }).catch(error => {
        console.error(error)
      })
    },
    updateFacturasRepetidas () {
      this.loadingButton = true
      let params = this.form.fields
      this.remisionesRepetidas(params).then(({data}) => {
        this.loadingButton = false
        this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          this.setView('update')
        }
      }).catch(error => {
        console.error(error)
      })
    }
  },
  validations: {
    form: {
      fields: {
        recurso_id: {required, minValue: minValue(1)},
        empresa_id: {required, minValue: minValue(1)},
        fecha_inicio: {required},
        fecha_fin: {required},
        num_contrato: {required, minValue: minValue(1), maxValue: maxValue(1000000000), numeric},
        monto_total: {required, minValue: minValue(0), maxValue: maxValue(1000000000)},
        rep_legal_contrato: {required, nombreRep}
      },
      file: {
        nombre: {required, minLength: minLength(5)},
        fecha_pago: {required},
        fecha_factura: {required}
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
</style>
