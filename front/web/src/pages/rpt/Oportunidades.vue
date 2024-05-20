<template>
  <q-page>
    <div class="q-pa-sm panel-header">
      <div class="row">
        <div class="col-sm-4">
          <div class="q-pa-sm q-gutter-sm">
            <q-breadcrumbs align="left">
              <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
              <q-breadcrumbs-el class="page-title" label="Prospección"/>
            </q-breadcrumbs>
          </div>
        </div>
        <div class="col-sm-8 pull-right">
          <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
           <q-btn @click="cargarDatosReporte()" color="green" icon="fas fa-search" :loading="loadingButton" style="margin-top:5px;"><q-tooltip>Buscar</q-tooltip></q-btn>
           <q-btn @click="exportCSV()" color="green" icon="fas fa-file-excel" style="margin-top:5px;"><q-tooltip>Generar CSV</q-tooltip></q-btn>
           <q-btn @click="borrar()" color="red" icon="loop" style="margin-top:5px;"><q-tooltip>Limpiar</q-tooltip></q-btn>
         </div>
       </div>
      </div>
    </div>

    <div class="q-pa-md bg-grey-3">
      <div class="row bg-white border-panel">
        <div class="col q-pa-md">
          <div class="row q-col-gutter-xs">
            <div class="col-sm-3">
              <q-field icon="person" icon-color="dark">
                <q-select v-model="form.fields.ejecutivo_id" inverted color="dark" float-label="Ejecutivo de ventas" :options="vendedoresOptions" filter v-if="this.usuario !== 'Ventas'"></q-select>
                <q-select readonly v-model="form.fields.ejecutivo_id" inverted color="dark" float-label="Ejecutivo de ventas" :options="vendedoresOptions" filter v-else></q-select>
              </q-field>
            </div>
            <div class="col-sm-3" icon-color="dark">
              <q-field icon="fas fa-building" icon-color="dark">
                <q-select v-model="form.fields.sector_id" inverted color="dark" float-label="Sector" :options="programasOptions" filter @input="selectSubprograma()"/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="fas fa-globe" icon-color="dark">
                <q-select v-model="form.fields.subsector_id" inverted color="dark" float-label="Subsector" :options="subprogramasOptions" filter></q-select>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="fas fa-info" icon-color="dark">
                <q-select v-model="form.fields.status" inverted color="dark" float-label="Status" :options="statusOptions" filter></q-select>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="fas fa-building" icon-color="dark">
                <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="person" icon-color="dark">
                <q-select v-model="form.fields.aliado_id" inverted color="dark" float-label="Aliado" :options="aliadosOptions" filter></q-select>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="label" icon-color="dark">
                <q-select v-model="form.fields.tipo" inverted color="dark" float-label="Tipo" :options="tiposOptions" filter></q-select>
              </q-field>
            </div>
            <div class="col-sm-3" v-if="form.fields.tipo === 'GDP'">
              <q-field icon="vpn_lock" icon-color="dark">
                <q-select v-model="form.fields.orden" inverted color="dark" float-label="Orden de gobierno" :options="ordenOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-3" v-if="form.fields.tipo === 'GDP'">
              <q-field icon="business" icon-color="dark">
                <q-select v-model="form.fields.ente" inverted color="dark" float-label="Tipo de ente" :options="enteOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-3" v-if="form.fields.tipo === 'GDP'">
              <q-field icon="layers" icon-color="dark">
                <q-select v-model="form.fields.adjudicacion" inverted color="dark" float-label="Método de adjudicación" :options="adjudicacionOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-3" v-if="form.fields.tipo === 'GDP' && form.fields.orden !== 'FEDERAL' && form.fields.orden !== 'all'">
              <q-field icon="fas fa-globe" icon-color="dark">
                <q-select v-model="form.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="municipiosOportunidad()"/>
              </q-field>
            </div>
            <div class="col-sm-3" v-if="form.fields.tipo === 'GDP' && form.fields.orden === 'MUNICIPAL' && form.fields.orden !== 'all'">
              <q-field icon="fas fa-map-pin" icon-color="dark">
                <q-select v-model="form.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="municipiosOptions" filter></q-select>
              </q-field>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="q-px-md bg-grey-3">
      <div class="row bg-white border-panel">
        <div class="col q-pa-md" style="padding: 0;">
          <div class="col q-pa-md ">
            <div class="col-sm-12" style="padding-bottom: 10px;">
              <q-search color="primary" v-model="form.filter"/>
            </div>
            <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
              <q-table id="sticky-table-newstyle"
                  :data="form.data"
                  :columns="form.columnsESTATAL"
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
                      <q-td  key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                      <q-td  key="nombre_compania" :props="props">{{ props.row.nombre_compania }}</q-td>
                      <q-td  key="aliado" :props="props">{{ props.row.aliado }}</q-td>
                      <q-td  key="sector" :props="props">{{ props.row.sector }}</q-td>
                      <q-td  key="subsector" :props="props">{{ props.row.subsector }}</q-td>
                      <q-td  key="empresa" :props="props">{{ props.row.empresa }}</q-td>
                      <q-td  key="orden" :props="props">{{ props.row.orden }}</q-td>
                      <q-td  key="ente" :props="props">{{ props.row.ente }}</q-td>
                      <q-td  key="adjudicacion" :props="props">{{ props.row.adjudicacion }}</q-td>
                      <q-td  key="estado" :props="props">{{ props.row.estado }}</q-td>
                      <q-td  key="municipio" :props="props">{{ props.row.municipio }}</q-td>
                      <q-td  key="valor" :props="props">${{ currencyFormat(props.row.valor) }}</q-td>
                      <q-td  key="valor_final" :props="props">${{ currencyFormat(props.row.valor_final) }}</q-td>
                      <q-td  key="creada" :props="props">{{ props.row.creada }}</q-td>
                      <q-td  key="fecha_ganada" :props="props">{{ props.row.fecha_ganada }}</q-td>
                      <q-td key="nickname" :props="props">{{ props.row.nickname }}</q-td>
                      <q-td key="status" :props="props">{{ props.row.status }}</q-td>
                      <q-td key="estimada" :props="props">{{ props.row.estimada }}</q-td>
                      <q-td key="avance_estimado" :props="props"><span style="position: absolute;z-index: 1;right: 42%;top:35%">{{props.row.avance_estimado}}%</span>
                        <q-progress :percentage="parseInt(props.row.avance_estimado)" color="blue" stripe animate style="height: 20px;"></q-progress></q-td>
                      <q-td key="paso" :props="props">{{ props.row.paso }}</q-td>
                      <q-td key="porcentaje" :props="props" style="width: 8%"><span style="position: absolute;z-index: 1;right: 42%;top:35%">{{props.row.porcentaje}}%</span>
                        <q-progress :percentage="parseInt(props.row.porcentaje)" :color="props.row.color" stripe animate style="height: 20px;"></q-progress>
                      </q-td>
                      <q-td key="dias" :props="props">{{ props.row.dias }}</q-td>
                      <q-td key="acciones" :props="props">
                        <q-btn small flat @click="modalComentario(true, props.row.id)" color="orange" icon="fas fa fa-sticky-note">
                          <q-tooltip>Ver comentarios</q-tooltip>
                        </q-btn>
                      </q-td>
                    </q-tr>
                  </template>
                    <q-tr slot="bottom-row" slot-scope="props">
                      <q-td colspan="15%" style="text-align: right;">
                        <strong>Valor Total: ${{currencyFormat(valor_total)}} MXN</strong>
                      </q-td>
                      <q-td colspan="10%" style="text-align: right;">
                        <strong>Valor Total Final: ${{currencyFormat(valor_total_final)}} MXN</strong>
                      </q-td>
                    </q-tr>
                </q-table>
                <q-inner-loading :visible="form.loading">
                  <q-spinner-dots size="64px" color="primary" />
                </q-inner-loading>
            </div>
          </div>
        </div>
      </div>
    </div>
    <historial :show="modal_comentario" :oportunidad_id="form.fields.id" @closeModal="modalComentario($event)"/>
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import Historial from '../../components/layout/crm/Historial'
import axios from 'axios'

export default {
  components: {
    Historial
  },
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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'DIRECTOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'VENTAS'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR GDP'.toUpperCase()) {
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
      usuario: '',
      loadingButton: false,
      selectYear: [ { label: '' + (new Date().getFullYear() - 1), value: '' + (new Date().getFullYear() - 1) }, { label: '' + (new Date().getFullYear()), value: '' + (new Date().getFullYear()) }, { label: '' + (new Date().getFullYear() + 1), value: '' + (new Date().getFullYear() + 1) } ],
      year: '' + (new Date().getFullYear()),
      reportes: [],
      municipiosOptions: [{label: '---Seleccione---', value: 0}],
      statusOptions: [ { 'label': 'Todos', 'value': 'all' }, { 'label': 'PENDIENTE', 'value': 'PENDIENTE' }, { 'label': 'LOGRADA', 'value': 'LOGRADA' }, { 'label': 'NO LOGRADA', 'value': 'NO LOGRADA' } ],
      tiposOptions: [ { label: 'VENTAS GOBIERNO', value: 'GDP' }, { label: 'VENTAS DIRECTAS', value: 'RETAIL' }, { label: 'Todos', value: 'all' } ],
      tipo_prospecto: [ { label: 'NUEVO', value: 'NUEVO' }, { label: 'EXISTENTE', value: 'EXISTENTE' } ],
      ordenOptions: [ { label: 'FEDERAL', value: 'FEDERAL' }, { label: 'ESTATAL', value: 'ESTATAL' }, { label: 'MUNICIPAL', value: 'MUNICIPAL' }, { label: '--Seleccione--', value: 'all' } ],
      enteOptions: [ { label: 'EJECUTIVO', value: 'EJECUTIVO' }, { label: 'DEPENDENCIA', value: 'DEPENDENCIA' }, { label: 'OPD', value: 'OPD' }, { label: '--Seleccione--', value: 'all' } ],
      adjudicacionOptions: [ { label: 'LICITACIÓN', value: 'LICITACIÓN' }, { label: 'ADJUDICACIÓN DIRECTA', value: 'ADJUDICACIÓN DIRECTA' }, { label: 'INVITACIÓN A 3', value: 'INVITACIÓN A 3' }, { label: '--Seleccione--', value: 'all' } ],
      views: {
        grid: true,
        create: false,
        update: false
      },
      modal_comentario: false,
      form: {
        data: [],
        fields: {
          id: 0,
          ejecutivo_id: 0,
          sector_id: 0,
          subsector_id: 0,
          status: 'all',
          tipo: 'all',
          empresa_id: 0,
          orden: 'all',
          ente: 'all',
          adjudicacion: 'all',
          estado_id: 0,
          municipio_id: 0,
          aliado_id: 0,
          valor_total: 0,
          valor_total_final: 0
        },
        columnsGDP: [
          { name: 'nombre', label: 'Oportunidad', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'nombre_compania', label: 'Prospecto', field: 'nombre_compania', sortable: true, type: 'string', align: 'left' },
          { name: 'aliado', label: 'Aliado', field: 'aliado', sortable: true, type: 'string', align: 'left' },
          { name: 'sector', label: 'Sector', field: 'sector', sortable: true, type: 'string', align: 'left' },
          { name: 'subsector', label: 'Subsector', field: 'subsector', sortable: true, type: 'string', align: 'left' },
          { name: 'empresa', label: 'Empresa', field: 'empresa', sortable: true, type: 'string', align: 'left' },
          { name: 'orden', label: 'Orden de gobierno', field: 'orden', sortable: true, type: 'string', align: 'left' },
          { name: 'ente', label: 'Tipo de ente', field: 'ente', sortable: true, type: 'string', align: 'left' },
          { name: 'adjudicacion', label: 'Adjudicación', field: 'adjudicacion', sortable: true, type: 'string', align: 'left' },
          { name: 'valor', label: 'Valor', field: 'valor', sortable: true, type: 'string', align: 'right' },
          { name: 'valor_final', label: 'Valor final', field: 'valor_final', sortable: true, type: 'string', align: 'right' },
          { name: 'creada', label: 'Creación', field: 'creada', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_ganada', label: 'Fecha ganada', field: 'fecha_ganada', sortable: true, type: 'string', align: 'center' },
          { name: 'nickname', label: 'Ejecutivo', field: 'nickname', sortable: true, type: 'string', align: 'left' },
          { name: 'status', label: 'Status', field: 'status', sortable: true, type: 'string', align: 'left' },
          { name: 'estimada', label: 'Avance estimado', field: 'estimada', sortable: true, type: 'string', align: 'left' },
          { name: 'avance_estimado', label: '% estimado', field: 'avance_estimado', sortable: true, type: 'string', align: 'left' },
          { name: 'paso', label: 'Avance real', field: 'paso', sortable: true, type: 'string', align: 'left' },
          { name: 'porcentaje', label: '% real', field: 'porcentaje', sortable: true, type: 'string', align: 'left' },
          { name: 'dias', label: 'Días CRM', field: 'dias', sortable: true, type: 'string', align: 'right' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
          // { name: 'fecha_termino', label: 'Fecha de término', field: 'fecha_termino', sortable: true, type: 'string', align: 'left' },
          // { name: 'empresa', label: 'Empresa', field: 'empresa', sortable: true, type: 'string', align: 'left' }
        ],
        columnsRETAIL: [
          { name: 'nombre', label: 'Oportunidad', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'nombre_compania', label: 'Prospecto', field: 'nombre_compania', sortable: true, type: 'string', align: 'left' },
          { name: 'aliado', label: 'Aliado', field: 'aliado', sortable: true, type: 'string', align: 'left' },
          { name: 'sector', label: 'Sector', field: 'sector', sortable: true, type: 'string', align: 'left' },
          { name: 'subsector', label: 'Subsector', field: 'subsector', sortable: true, type: 'string', align: 'left' },
          { name: 'empresa', label: 'Empresa', field: 'empresa', sortable: true, type: 'string', align: 'left' },
          { name: 'valor', label: 'Valor', field: 'valor', sortable: true, type: 'string', align: 'right' },
          { name: 'valor_final', label: 'Valor final', field: 'valor_final', sortable: true, type: 'string', align: 'right' },
          { name: 'creada', label: 'Creación', field: 'creada', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_ganada', label: 'Fecha ganada', field: 'fecha_ganada', sortable: true, type: 'string', align: 'center' },
          { name: 'nickname', label: 'Ejecutivo', field: 'nickname', sortable: true, type: 'string', align: 'left' },
          { name: 'status', label: 'Status', field: 'status', sortable: true, type: 'string', align: 'left' },
          { name: 'estimada', label: 'Avance estimado', field: 'estimada', sortable: true, type: 'string', align: 'left' },
          { name: 'avance_estimado', label: '% estimado', field: 'avance_estimado', sortable: true, type: 'string', align: 'left' },
          { name: 'paso', label: 'Avance real', field: 'paso', sortable: true, type: 'string', align: 'left' },
          { name: 'porcentaje', label: '% real', field: 'porcentaje', sortable: true, type: 'string', align: 'left' },
          { name: 'dias', label: 'Días CRM', field: 'dias', sortable: true, type: 'string', align: 'right' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
          // { name: 'fecha_termino', label: 'Fecha de término', field: 'fecha_termino', sortable: true, type: 'string', align: 'left' },
          // { name: 'empresa', label: 'Empresa', field: 'empresa', sortable: true, type: 'string', align: 'left' }
        ],
        columnsESTATAL: [
          { name: 'nombre', label: 'Oportunidad', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'nombre_compania', label: 'Prospecto', field: 'nombre_compania', sortable: true, type: 'string', align: 'left' },
          { name: 'aliado', label: 'Aliado', field: 'aliado', sortable: true, type: 'string', align: 'left' },
          { name: 'sector', label: 'Sector', field: 'sector', sortable: true, type: 'string', align: 'left' },
          { name: 'subsector', label: 'Subsector', field: 'subsector', sortable: true, type: 'string', align: 'left' },
          { name: 'empresa', label: 'Empresa', field: 'empresa', sortable: true, type: 'string', align: 'left' },
          { name: 'orden', label: 'Orden de gobierno', field: 'orden', sortable: true, type: 'string', align: 'left' },
          { name: 'ente', label: 'Tipo de ente', field: 'ente', sortable: true, type: 'string', align: 'left' },
          { name: 'adjudicacion', label: 'Adjudicación', field: 'adjudicacion', sortable: true, type: 'string', align: 'left' },
          { name: 'estado', label: 'Estado', field: 'estado', sortable: true, type: 'string', align: 'left' },
          { name: 'valor', label: 'Valor', field: 'valor', sortable: true, type: 'string', align: 'right' },
          { name: 'valor_final', label: 'Valor final', field: 'valor_final', sortable: true, type: 'string', align: 'right' },
          { name: 'creada', label: 'Creación', field: 'creada', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_ganada', label: 'Fecha ganada', field: 'fecha_ganada', sortable: true, type: 'string', align: 'center' },
          { name: 'nickname', label: 'Ejecutivo', field: 'nickname', sortable: true, type: 'string', align: 'left' },
          { name: 'status', label: 'Status', field: 'status', sortable: true, type: 'string', align: 'left' },
          { name: 'estimada', label: 'Avance estimado', field: 'estimada', sortable: true, type: 'string', align: 'left' },
          { name: 'avance_estimado', label: '% estimado', field: 'avance_estimado', sortable: true, type: 'string', align: 'left' },
          { name: 'paso', label: 'Avance real', field: 'paso', sortable: true, type: 'string', align: 'left' },
          { name: 'porcentaje', label: '% real', field: 'porcentaje', sortable: true, type: 'string', align: 'left' },
          { name: 'dias', label: 'Días CRM', field: 'dias', sortable: true, type: 'string', align: 'right' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
          // { name: 'fecha_termino', label: 'Fecha de término', field: 'fecha_termino', sortable: true, type: 'string', align: 'left' },
          // { name: 'empresa', label: 'Empresa', field: 'empresa', sortable: true, type: 'string', align: 'left' }
        ],
        columnsMUNICIPAL: [
          { name: 'nombre', label: 'Oportunidad', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'nombre_compania', label: 'Prospecto', field: 'nombre_compania', sortable: true, type: 'string', align: 'left' },
          { name: 'aliado', label: 'Aliado', field: 'aliado', sortable: true, type: 'string', align: 'left' },
          { name: 'sector', label: 'Sector', field: 'sector', sortable: true, type: 'string', align: 'left' },
          { name: 'subsector', label: 'Subsector', field: 'subsector', sortable: true, type: 'string', align: 'left' },
          { name: 'empresa', label: 'Empresa', field: 'empresa', sortable: true, type: 'string', align: 'left' },
          { name: 'orden', label: 'Orden de gobierno', field: 'orden', sortable: true, type: 'string', align: 'left' },
          { name: 'ente', label: 'Tipo de ente', field: 'ente', sortable: true, type: 'string', align: 'left' },
          { name: 'adjudicacion', label: 'Adjudicación', field: 'adjudicacion', sortable: true, type: 'string', align: 'left' },
          { name: 'estado', label: 'Estado', field: 'estado', sortable: true, type: 'string', align: 'left' },
          { name: 'municipio', label: 'Municipio', field: 'municipio', sortable: true, type: 'string', align: 'left' },
          { name: 'valor', label: 'Valor', field: 'valor', sortable: true, type: 'string', align: 'right' },
          { name: 'valor_final', label: 'Valor final', field: 'valor_final', sortable: true, type: 'string', align: 'right' },
          { name: 'creada', label: 'Creación', field: 'creada', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_ganada', label: 'Fecha ganada', field: 'fecha_ganada', sortable: true, type: 'string', align: 'center' },
          { name: 'nickname', label: 'Ejecutivo', field: 'nickname', sortable: true, type: 'string', align: 'left' },
          { name: 'status', label: 'Status', field: 'status', sortable: true, type: 'string', align: 'left' },
          { name: 'estimada', label: 'Avance estimado', field: 'estimada', sortable: true, type: 'string', align: 'left' },
          { name: 'avance_estimado', label: '% estimado', field: 'avance_estimado', sortable: true, type: 'string', align: 'left' },
          { name: 'paso', label: 'Avance real', field: 'paso', sortable: true, type: 'string', align: 'left' },
          { name: 'porcentaje', label: '% real', field: 'porcentaje', sortable: true, type: 'string', align: 'left' },
          { name: 'dias', label: 'Días CRM', field: 'dias', sortable: true, type: 'string', align: 'right' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
          // { name: 'fecha_termino', label: 'Fecha de término', field: 'fecha_termino', sortable: true, type: 'string', align: 'left' },
          // { name: 'empresa', label: 'Empresa', field: 'empresa', sortable: true, type: 'string', align: 'left' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      }
    }
  },
  mounted () {
    this.loadAll()
  },
  computed: {
    ...mapGetters({
      // programasOptions: 'vnt/programa/selectOptions'
    }),
    programasOptions () {
      let programas = this.$store.getters['vnt/programa/selectOptions']
      programas.splice(0, 0, { 'label': 'Todos', 'value': 0 })
      return programas
    },
    subprogramasOptions () {
      let subprogramas = this.$store.getters['vnt/subprograma/selectOtherOptions'].filter(p => p.programa_id === this.form.fields.sector_id)
      subprogramas.sort((a, b) => {
        return (a.label < b.label) ? -1 : (a.label > b.label) ? 1 : 0
      })
      subprogramas.splice(0, 0, { 'label': 'Todos', 'value': 0 })
      return subprogramas
    },
    vendedoresOptions () {
      let vendedores = this.$store.getters['sys/users/selectOptionsName']
      vendedores.splice(0, 0, { 'label': 'Todos', 'value': 0 })
      return vendedores
    },
    estadosOptions () {
      let estados = this.$store.getters['vnt/estado/selectOptions']
      estados.splice(0, 0, {label: '---Seleccione---', value: 0})
      estados.sort(function (a, b) {
        if (a.nombre > b.nombre) {
          return 1
        }
        if (a.nombre < b.nombre) {
          return -1
        }
        return 0
      })
      return estados
    },
    empresasOptions () {
      let empresas = this.$store.getters['vnt/empresa/selectOptions']
      empresas.splice(0, 0, {label: '---Seleccione---', value: 0})
      return empresas
    },
    aliadosOptions () {
      let aliados = this.$store.getters['com/aliados/selectOptions']
      aliados.splice(0, 0, {label: '---Seleccione---', value: 0})
      return aliados
    }
  },
  methods: {
    ...mapActions({
      getProgramas: 'vnt/programa/refresh',
      getSubprogramas: 'vnt/subprograma/refresh',
      getVendedores: 'sys/users/refresh',
      getEstados: 'vnt/estado/refresh',
      getEmpresas: 'vnt/empresa/refresh',
      getMunicipiosByEstado: 'vnt/municipio/getByEstado',
      getAliados: 'com/aliados/refresh',
      getUser: 'user/refresh'
    }),
    async loadAll () {
      this.form.loading = true
      await this.isAdmin()
      await this.getVendedores()
      await this.getProgramas()
      await this.getSubprogramas()
      await this.getEmpresas()
      await this.getEstados()
      await this.getAliados()
      await this.cargarDatosReporte()
      this.form.loading = false
    },
    async isAdmin () {
      let respuesta = []
      await this.getUser().then(({data}) => {
        if (data.role[0] === 'Ventas') {
          this.form.fields.ejecutivo_id = data.user.id
        }
        this.usuario = data.role[0]
      }).catch(error => {
        console.log(error)
      })
      return respuesta
    },
    selectSubprograma () {
      this.form.fields.subsector_id = 0
    },
    async cargarDatosReporte () {
      this.form.loading = true
      this.form.data = []
      await axios.get(`/reportes/reporte_oportunidades/${this.form.fields.ejecutivo_id}/${this.form.fields.sector_id}/${this.form.fields.subsector_id}/${this.form.fields.status}/${this.form.fields.empresa_id}/${this.form.fields.tipo}/${this.form.fields.orden}/${this.form.fields.ente}/${this.form.fields.adjudicacion}/${this.form.fields.estado_id}/${this.form.fields.municipio_id}/${this.form.fields.aliado_id}`).then(({data}) => {
        this.form.data = data.reporte
        this.valor_total = data.valor
        this.valor_total_final = data.valor_final
      }).catch(error => {
        console.error(error)
      })
      this.form.loading = false
    },
    exportCSV () {
      let uri = process.env.API + `reportes/exportCSV_oportunidades/${this.form.fields.ejecutivo_id}/${this.form.fields.sector_id}/${this.form.fields.subsector_id}/${this.form.fields.status}/${this.form.fields.empresa_id}/${this.form.fields.tipo}/${this.form.fields.orden}/${this.form.fields.ente}/${this.form.fields.adjudicacion}/${this.form.fields.estado_id}/${this.form.fields.municipio_id}/${this.form.fields.aliado_id}`
      window.open(uri, '_blank')
    },
    borrar () {
      this.form.fields.ejecutivo_id = 0
      this.form.fields.sector_id = 0
      this.form.fields.subsector_id = 0
      this.form.fields.status = 'all'
      this.form.fields.empresa_id = 0
      this.form.fields.tipo = 'all'
      this.form.fields.orden = 'all'
      this.form.fields.ente = 'all'
      this.form.fields.adjudicacion = 'all'
      this.form.fields.estado_id = 0
      this.form.fields.municipio_id = 0
      this.form.fields.aliado_id = 0
    },
    currencyFormat (num) {
      return Number.parseFloat(num).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
    },
    async municipiosOportunidad () {
      this.municipiosOptions = []
      this.form.fields.municipio_id = 0
      await this.getMunicipiosByEstado({estado_id: this.form.fields.estado_id}).then(({data}) => {
        this.municipiosOptions.push({label: '---Seleccione---', value: 0})
        if (data.municipios.length > 0) {
          data.municipios.forEach(municipio => {
            this.municipiosOptions.push({label: municipio.nombre, value: municipio.id})
          })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    modalComentario (show, id) {
      this.modal_comentario = show
      this.form.fields.id = id
    }
  }
}
</script>

<style scoped>
#sticky-table-scroll .q-table th{
  text-align: center;
}
</style>
