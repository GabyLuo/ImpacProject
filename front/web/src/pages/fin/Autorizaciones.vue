<template>
  <q-page>
    <div class="layout-padding">
      <div class="row" v-if="views.grid">
        <div class="col-sm-12">
          <div class="row justify-between">
            <div class="col-sm-6">
              <q-btn size="15px" icon="done_all" disable class="btn_categoria" label="AUTORIZACIONES" />
            </div>
          </div>

          <div class="row q-mt-lg" style="margin-top: 50px;"> <!-- AQUI EMPIEZAN LOS FILTROS -->
            <div class="col-sm-4">
              <q-field icon="work" icon-color="dark">
                <q-select v-model="form.fields.proyecto_id" inverted color="dark" float-label="Presupuesto" :options="proyectosOptions2" filter @input="selectActividades()"/>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="folder" icon-color="dark">
                <q-select v-model="form.fields.actividad_id" inverted color="dark" float-label="Actividad" :options="actividadesByProyectoOptions2" filter @input="presupuestoActividad()"/>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="forward" icon-color="dark">
                <q-select v-model="form.fields.status" inverted color="dark" float-label="Estatus" :options="form.selectStatus" filter/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-4">
              <q-field icon="person" icon-color="dark">
                <q-select v-model="form.fields.responsable_id" inverted color="dark" float-label="Asignado a:" :options="usuariosOptions2" filter/>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="fa-check" icon-color="dark">
                <q-select v-model="form.fields.comprobado" inverted color="dark" float-label="Comprobado" :options="form.selectComprobada" filter/>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="search" icon-color="dark">
                <q-input v-model="form_solicitudes.filter" type="text" inverted color="dark" float-label="Buscar" maxlenght=1000></q-input>
              </q-field>
            </div>
          </div>

          <div class="row justify-end" style="text-align: right">
          <div class="col-sm-6">
            <br>
            <q-btn @click="filtrar()" inverted color="orange" icon-right="fas fa-filter" :loading="loadingButton">Filtrar</q-btn>
            </div>
          </div>

          <div class="row q-mt-sm" style="margin-top:50px;">
            <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
              <q-table id="sticky-table"
                :data="solicitudeslibres"
                :columns="form_solicitudes.columns"
                :selected.sync="form_solicitudes.selected"
                :filter="form_solicitudes.filter"
                color="positive"
                title=""
                :pagination.sync="form_solicitudes.pagination"
                :loading="form_solicitudes.loading"
                :rows-per-page-options="form_solicitudes.rowsOptions"
                >
                <template slot="body" slot-scope="props">
                  <q-tr :props="props">
                    <q-td key="id" :props="props">{{ props.row.id }}</q-td>
                    <q-td key="nombre_proyecto" :props="props">{{ props.row.nombre_proyecto }}</q-td>
                    <q-td key="fecha_creado" :props="props">{{ props.row.fecha_creado }}</q-td>
                    <q-td key="fecha_solicitado" :props="props">{{ props.row.fecha_solicitado }}</q-td>
                    <q-td key="responsable_solicitud" :props="props">{{ props.row.responsable_solicitud }}</q-td>
                    <q-td key="status" :props="props"><q-chip dense :icon="props.row.icon" :color="props.row.color" text-color="white">{{ props.row.status }}</q-chip></q-td>
                    <q-td key="acciones" :props="props">
                    <q-btn small flat @click="verInformacion(props.row)" color="blue" icon="edit">
                        <q-tooltip>Ver detalles</q-tooltip>
                    </q-btn>
                    </q-td>
                  </q-tr>
                </template>
              </q-table>
              <q-inner-loading :visible="form_solicitudes.loading">
                <q-spinner-dots size="64px" color="primary" />
              </q-inner-loading>
            </div>
          </div> <!-- AQUI ACABAN LOS FILTROS -->

        </div>
      </div>

      <!-- VISTA para rellenar datos de la NUEVA SOLICITUD -->
      <div class="row" v-if="views.update">
        <div class="col-sm-12">
          <div class="row justify-between">
            <div class="col-sm-3">
              <h5 style="margin: 7px 0; font-weight: bold">SOLICITUD  #{{form_solicitudes.fields.id}}</h5>
            </div>
            <div class="col-sm-9">
              <div class="row justify-end">
                <!-- <div class="col-sm-2" style="text-align: right;"> -->
                  <q-btn v-if="this.form_solicitudes.fields.autorizacion_id===this.form_solicitudes.fields.orden" style="margin-right: 10px" @click="form_solicitudes.solicitudes_proyecto = true" class="btn_rechazar" icon="clear">Rechazar</q-btn>

                  <q-btn v-if="this.form_solicitudes.fields.autorizacion_id===this.form_solicitudes.fields.orden" style="margin-right: 10px" @click="autorizar()" class="btn_regresar" icon="done">Autorizar</q-btn>
                <!-- </div> -->
                <!-- <div class="col-sm-3" style="text-align: right"> -->
                  <q-btn @click="regresar()" class="btn_regresar" icon="fa-arrow-left" />
                <!-- </div> -->
              </div>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-4">
              <q-field icon="work" icon-color="dark" >
                <q-select readonly v-model="form_solicitudes.fields.proyecto_id" inverted color="dark" float-label="Presupuesto" :options="proyectosOptions" filter @input="selectActividades()"/>
              </q-field>
            </div>
            <div class="col-sm-2" style="margin-left:40px;">
              <q-checkbox readonly v-model="incluir_iva" label="Aplicar IVA" color="amber"/>
            </div>
            <div class="col-sm-2">
              <q-checkbox readonly v-model="comprobar_solicitud" label="Comprobada" color="amber"/>
            </div>
          </div>
          <div class="row q-mt-lg" v-if="form_gastos.modificar">
            <div class="col-sm-3">
              <q-field icon="folder" icon-color="dark" :error="$v.form_gastos.fields.actividad_id.$error" error-label="Elija una actividad">
                <q-select readonly v-model="form_gastos.fields.actividad_id" inverted color="dark" float-label="Actividad" :options="actividadesByProyectoOptions2" filter @input="presupuestoActividad()"/>
              </q-field>
            </div>
            <!-- <div class="col-sm-2">
              <div class="icono_field">
                <q-field  icon="fas fa-dollar-sign" icon-color="dark" helper="Presupuesto actual">
                  <money readonly disable v-model="form_gastos.fields.presupuesto_real" v-bind="money" style="height:55px;width:100%;" v-bind:class="{ moneyBien: true, moneyError: false }"></money>
                </q-field>
              </div>
            </div> -->
            <div class="col-sm-2">
              <q-field icon="fas fa-dollar-sign" icon-color="dark">
                <q-input readonly v-model="form_gastos.fields.presupuesto_real" type="text" inverted color="dark" float-label="Presupuesto actual" suffix="MXN"></q-input>
              </q-field>
            </div>
            <!-- <div class="col-sm-2">
              <div class="icono_field">
                <q-field  icon="fas fa-dollar-sign" icon-color="dark" helper="Disponible">
                  <money readonly v-model="form_gastos.fields.presupuesto_disponible" v-bind="money" style="height:53px;width:100%;" v-bind:class="{ moneyBien: true, moneyError: false }"></money>
                </q-field>
              </div>
            </div> -->
            <div class="col-sm-2">
              <q-field icon="fas fa-dollar-sign" icon-color="dark">
                <q-input readonly v-model="form_gastos.fields.presupuesto_disponible" type="text" inverted color="dark" float-label="Presupuesto disponible" suffix="MXN"></q-input>
              </q-field>
            </div>
            <div class="col-sm-5">
              <q-field icon="insert_comment" icon-color="dark">
                <q-input readonly upper-case v-model="form_gastos.fields.descripcion_gasto" type="text" inverted color="dark" float-label="Descripción gasto" maxlenght=1000></q-input>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg"  v-if="form_gastos.modificar">
            <div class="col-sm-3">
              <q-field icon="business" icon-color="dark" :error="$v.form_gastos.fields.proveedor_id.$error" error-label="Elija un proveedor">
                <q-select readonly v-model="form_gastos.fields.proveedor_id" inverted color="dark" float-label="Proveedor" :options="proveedoresOptions" filter/>
              </q-field>
            </div>
            <!-- <div class="col-sm-2">
              <div class="icono_field">
                <q-field  icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form_gastos.fields.monto.$error" error-label="Ingrese el monto ($0.00 - $1,000,000,000.00)" helper="Monto">
                  <money v-model="form_gastos.fields.monto" v-bind="money" style="height:53px;width:100%;" v-bind:class="{ moneyBien: !($v.form_gastos.fields.monto.$error), moneyError: $v.form_gastos.fields.monto.$error }" @input="$v.form_gastos.fields.monto.$touch"></money>
                </q-field>
              </div>
            </div> -->
            <div class="col-sm-2">
              <div class="icono_field">
                <q-field  icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form_gastos.fields.monto.$error" error-label="Ingrese el monto ($0.00 - $1,000,000,000.00)">
                  <q-input readonly v-model="form_gastos.fields.monto" v-bind="money" v-bind:class="{ moneyBien: !($v.form_gastos.fields.monto.$error), moneyError: $v.form_gastos.fields.monto.$error }" @input="$v.form_gastos.fields.monto.$touch" inverted color="dark" float-label="Monto"></q-input>
                </q-field>
              </div>
            </div>
            <div class="col-sm-2">
              <q-field icon="business" icon-color="dark">
                <q-select readonly v-model="form_gastos.fields.tipo" inverted color="dark" float-label="Tipo de gasto" :options="gastosOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-2" v-if="form_gastos.modificar">
              <q-field icon="date_range" icon-color="dark">
                <q-datetime readonly v-model="form_gastos.fields.fecha_ejercido" type="date" inverted color="dark" float-label="Fecha ejercido" align="center"></q-datetime>
              </q-field>
            </div>
            <div class="col-sm-2" style="margin-left:40px;">
              <q-checkbox v-model="comprobar_gasto" label="Comprobar gasto" color="amber"/>
            </div>
            <div class="col-sm-2 offset-sm-10 pull-right">
              <q-btn @click="actualizarGastos()" style="background-color: orange;" icon-right="fas fa-dollar-sign" :loading="loadingButton" v-if="form_gastos.modificar">Guardar cambios</q-btn>
            </div>
          </div>
          <!-- AGREGAR COMENTARIOS POR SOLICITUD -->
          <div class="row q-mt-lg">
            <!-- <div class="col-sm-12"> -->
              <q-collapsible class="col-sm-12" @click="cargarComentarios()" v-model="open_comentarios" style="border-bottom: 1px solid green;" icon="insert_comment" label="Comentarios de la solicitud">
                <!-- aqui va el contenido -->
                <div class="row q-mt-lg">
                  <div class="col-sm-3">
                    <q-field icon="person" icon-color="dark">
                      <q-select v-model="form_comentarios.fields.usuario_destino" inverted color="dark" float-label="Comentario para:" :options="usuariosOptions2" filter/>
                    </q-field>
                  </div>
                  <div class="col-sm-9">
                    <q-field icon="insert_comment" icon-color="dark" :error="$v.form_comentarios.fields.descripcion.$error" error-label="Escriba un comentario">
                      <q-input upper-case v-model="form_comentarios.fields.descripcion" type="text" inverted color="dark" float-label="Escriba un comentario o descripción de la solicitud" maxlenght=1000></q-input>
                    </q-field>
                  </div>
                  <div class="col-sm-2 offset-sm-10 pull-right">
                    <q-btn @click="agregarComentario()" class="btn_guardar" icon-right="insert_comment" :loading="loadingButton">Agregar comentario</q-btn>
                  </div>
                </div>
                <div class="row q-mt-lg">
                  <q-list highlight inset-separator class="col-sm-12" v-for="msje in form_solicitudes.mensajes" :key="msje.id">
                    <q-item multiline>
                      <q-item-side>
                        <q-item-tile avatar>
                          <img src="statics/usuario_avatar.png">
                        </q-item-tile>
                      </q-item-side>
                      <q-item-main>
                        <q-item-tile label>{{ msje.autor }}</q-item-tile>
                        <q-item-tile sublabel lines="4">{{ msje.mensaje }}</q-item-tile>
                      </q-item-main>
                      <q-item-side right>
                        <q-item-tile stamp>{{ msje.fecha_mensaje }}</q-item-tile>
                      </q-item-side>
                    </q-item>
                  </q-list>
                </div>
              </q-collapsible> <!-- AQUI TERMINA LO DE AGREGAR COMENTARIOS -->
              <!-- </div> -->
          <!-- AGREGAR ARCHIVOS POR SOLICITUD -->
              <q-collapsible class="col-sm-12" @click="cargarArchivos()" v-model="open_archivos" style="border-bottom: 1px solid green;" icon="note" label="Archivos de la solicitud">
                <!-- aqui va el contenido -->
                <div class="col-sm-3 pull-right">
            <input style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="fileInputFormato" accept=".jpg,.jpeg,.pdf,.png" @change="uploadFormato()" />
            <q-btn small flat @click="$refs.fileInputFormato.click()" icon-right="attach_file" color="green" :loading="loadingButton">
              <i class="cloud-upload" aria-hidden="true"></i>&nbsp;Cargar Archivo
            </q-btn>
          </div>
          <div class="row q-mt-sm" style="margin-top:30px; margin-right:30px;">
            <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
              <q-table id="sticky-table"
                :data="form_archivos.data"
                :columns="form_archivos.columns"
                :selected.sync="form_archivos.selected"
                :filter="form_archivos.filter"
                color="positive"
                title=""
                :pagination.sync="form_archivos.pagination"
                :loading="form_archivos.loading"
                :rows-per-page-options="form_archivos.rowsOptions"
                >
                <template slot="body" slot-scope="props">
                  <q-tr :props="props">
                    <q-td key="name" :props="props">{{ props.row.name }}</q-td>
                    <q-td key="acciones" :props="props">
                      <q-btn small flat @click="descargarFormato(props.row)" color="blue-8" icon="cloud_download">
                        <q-tooltip>Descargar</q-tooltip>
                      </q-btn>
                      <q-btn small flat @click="deleteFormato(props.row)" color="negative" icon="delete">
                        <q-tooltip>Eliminar</q-tooltip>
                      </q-btn>
                    </q-td>
                  </q-tr>
                </template>
              </q-table>
              <q-inner-loading :visible="form_archivos.loading">
                <q-spinner-dots size="64px" color="primary" />
              </q-inner-loading>
            </div>
          </div>
              </q-collapsible> <!-- AQUI TERMINA LO DE AGREGAR ARCHIVOS -->

          <div class="row q-mt-lg subtitulos" style="margin-left:40px;">
            GASTOS DE LA SOLICITUD
          </div>

          <div class="row q-mt-sm">
            <div class="row" style="width:60vw;">
              <q-search hide-underline color="secondary" v-model="form_gastos.filter" />
            </div>
            <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
              <q-table id="sticky-table"
                :data="form_gastos.data"
                :columns="form_gastos.columns"
                :selected.sync="form_gastos.selected"
                :filter="form_gastos.filter"
                color="positive"
                title=""
                :pagination.sync="form_gastos.pagination"
                :loading="form_gastos.loading"
                :rows-per-page-options="form_gastos.rowsOptions"
                >
                <q-tr slot="bottom-row" slot-scope="props">
                    <q-td colspan="100%" style="text-align: right;">
                    <strong>Total: ${{ form_solicitudes.fields.totalcopia }} MXN</strong>
                    </q-td>
                  </q-tr>
                <template slot="body" slot-scope="props">
                  <q-tr :props="props">
                    <q-td key="nombre_actividad" :props="props">{{ props.row.nombre_actividad }}</q-td>
                    <q-td key="cotizacion" :props="props">
                      <div style="display: inline;" v-for="cot in props.row.cotizacion" :key="cot.id">
                        <q-btn small flat @click="cotizacionAccion(cot)" :icon="cot.icon" :color="cot.color">
                          <q-tooltip>{{ cot.name }}</q-tooltip>
                        </q-btn>
                      </div>
                      <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="cotizacionInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadCotizacionFile()" />
                      <q-btn small flat @click="selectedCotizacionDocumento(props.row)" color="green-9" :loading="loadingButton">
                        <q-icon name="cloud_upload" />&nbsp;
                        <q-tooltip>Subir cotización</q-tooltip>
                      </q-btn>
                    </q-td>
                    <q-td key="factura" :props="props">
                      <div style="display: inline;" v-for="fac in props.row.factura" :key="fac.id">
                        <q-btn small flat @click="facturaAccion(fac)" :icon="fac.icon" :color="fac.color">
                          <q-tooltip>{{ fac.name }}</q-tooltip>
                        </q-btn>
                      </div>
                      <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="facturaInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadFacturaFile()" />
                      <q-btn small flat @click="selectedFacturaDocumento(props.row)" color="green-9" :loading="loadingButton">
                        <q-icon name="cloud_upload" />&nbsp;
                        <q-tooltip>Subir factura</q-tooltip>
                      </q-btn>
                    </q-td>
                    <q-td key="comprobante" :props="props">
                      <div style="display: inline;" v-for="com in props.row.comprobante" :key="com.id">
                        <q-btn small flat @click="comprobanteAccion(com)" :icon="com.icon" :color="com.color">
                          <q-tooltip>{{ com.name }}</q-tooltip>
                        </q-btn>
                      </div>
                      <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="comprobanteInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadComprobanteFile()" />
                      <q-btn small flat @click="selectedComprobanteDocumento(props.row)" color="green-9" :loading="loadingButton">
                        <q-icon name="cloud_upload" />&nbsp;
                        <q-tooltip>Subir comprobante</q-tooltip>
                      </q-btn>
                    </q-td>
                    <q-td key="comprobado" :props="props"><q-checkbox readonly v-model="props.row.comprobado" color="teal"></q-checkbox></q-td>
                    <q-td key="montocopia" :props="props">${{ props.row.montocopia }}</q-td>
                    <q-td key="iva" :props="props">${{ props.row.iva }}</q-td>
                    <q-td key="total" :props="props">${{ props.row.total }}</q-td>
                    <q-td key="acciones" :props="props">
                      <q-btn small flat @click="editSingleGasto(props.row)" color="blue" icon="edit">
                        <q-tooltip>Ver detalles</q-tooltip>
                      </q-btn>
                    </q-td>
                  </q-tr>
                </template>
              </q-table>
              <q-inner-loading :visible="form_gastos.loading">
                <q-spinner-dots size="64px" color="primary" />
              </q-inner-loading>
            </div>
          </div>

          <!-- AQUI EMPIEZA EL MODAL DE RECHAZAR LA SOLICITUD -->
      <q-modal no-backdrop-dismiss v-if="form_solicitudes.solicitudes_proyecto" style="background-color: rgba(0,0,0,0.7);" v-model="form_solicitudes.solicitudes_proyecto" :content-css="{width: '50vw', height: '230px'}">
        <q-modal-layout>
          <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
            <div class="col-sm-10">
              <q-toolbar-title>
                Rechazar solicitud
              </q-toolbar-title>
            </div>
            <div class="col-sm-2 pull-right">
              <q-btn
                flat
                round
                dense
                color="white"
                @click="form_solicitudes.solicitudes_proyecto = false"
                icon="fas fa-times-circle"
              />
            </div>
          </q-toolbar>
        </q-modal-layout>
        <div class="row q-mt-lg" style="margin-top:70px;margin-right:20px;">
          <div class="col-sm-12">
            <q-field icon="insert_comment" icon-color="dark" :error="$v.form_rechazar.fields.descripcion.$error" error-label="Escriba un comentario de porque se rechaza la solicitud">
              <q-input upper-case v-model="form_rechazar.fields.descripcion" type="text" inverted color="dark" float-label="Escriba un comentario o descripción de porque se rechaza la solicitud" maxlenght=1000></q-input>
            </q-field>
          </div>
        </div>
        <div class="row q-mt-lg" style="margin-right:20px;">
          <div class="col-sm-4 offset-sm-8 pull-right">
            <q-btn @click="rechazar()" class="btn_rechazar" icon="clear" :loading="loadingButton">Rechazar</q-btn>
          </div>
        </div>
      </q-modal> <!-- AQUI TERMINA EL MODAL DE RECHAZAR UNA SOLICITUD -->
        </div>

        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { required, maxValue, minValue } from 'vuelidate/lib/validators'
import {Money} from 'v-money'
import axios from 'axios'

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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase()) {
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
      isExpanded: true,
      loadingButton: false,
      incluir_iva: false,
      comprobar_solicitud: false,
      comprobar_gasto: false,
      open_archivos: false,
      open_comentarios: false,
      mensajes: [],

      money: {
        decimal: '.',
        thousands: ',',
        suffix: ' MXN',
        precision: 2,
        masked: false
      },

      views: {
        grid: true,
        update: false
      },
      form: { // para los filtros
        fields: {
          status: 'all',
          proyecto_id: 0,
          responsable_id: 0,
          actividad_id: 0,
          comprobado: 'all'
        },
        selectComprobada: [ { 'label': 'SI', 'value': 'SI' }, { 'label': 'NO', 'value': 'NO' }, { 'label': 'Todos', 'value': 'all' } ],
        selectStatus: [ { 'label': 'CREADO', 'value': 'CREADO' }, { 'label': 'SOLICITADO', 'value': 'SOLICITADO' }, { 'label': 'Todos', 'value': 'all' } ]
      },
      form_solicitudes: {
        fields: {
          id: 0,
          status: '',
          fecha_solicitado: 0,
          fecha_creado: '',
          nombre_proyecto: '',
          proyecto_id: 0,
          autorizacion_id: 0,
          responsable_solicitud: '',
          creador: 0,
          total: 0,
          totalcopia: 0,
          iva: '',
          comprobado: 'NO',
          subtotal: 0, // este campo se usa para hacer el calculo de suma de gastos
          autorizador_id: 0,
          orden: 0,
          autorizadores: []
        },
        columns: [
          {name: 'id', label: '# Solicitud', field: 'id', sortable: true, type: 'string', align: 'right'},
          { name: 'nombre_proyecto', label: 'Presupuesto', field: 'nombre_proyecto', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha_creado', label: 'Fecha creación', field: 'fecha_creado', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_solicitado', label: 'Fecha de solicitud', field: 'fecha_solicitado', sortable: true, type: 'string', align: 'center' },
          { name: 'responsable_solicitud', label: 'Asignado', field: 'responsable_solicitud', sortable: true, type: 'string', align: 'left' },
          { name: 'status', label: 'Estatus', field: 'status', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', type: 'string', align: 'center' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        solicitudes_proyecto: false,
        loading: false,
        mensajes: []
      },
      form_gastos: {
        tipoOptions: [ { 'label': 'HOSPEDAJE', 'value': 1 }, { 'label': 'ALIMENTACIÓN', 'value': 2 }, { 'label': 'TRANSPORTE', 'value': 3 }, { 'label': 'PAPELERÍA', 'value': 4 } ],
        columns: [
          {name: 'nombre_actividad', label: 'Actividad', field: 'nombre_actividad', sortable: true, type: 'string', align: 'left'},
          {name: 'cotizacion', label: 'Cotización', field: 'cotizacion', sortable: true, type: 'string', align: 'left'},
          {name: 'factura', label: 'Factura', field: 'factura', sortable: true, type: 'string', align: 'left'},
          {name: 'comprobante', label: 'Comprobante', field: 'comprobante', sortable: true, type: 'string', align: 'left'},
          {name: 'comprobado', label: 'Comprobado', field: 'comprobado', sortable: true, type: 'string', align: 'left'},
          {name: 'montocopia', label: 'Monto solicitado', field: 'montocopia', sortable: true, type: 'string', align: 'right'},
          {name: 'iva', label: 'IVA', field: 'iva', sortable: true, type: 'string', align: 'right'},
          {name: 'total', label: 'Total', field: 'total', sortable: true, type: 'string', align: 'right'},
          { name: 'acciones', label: 'Acciones', field: 'acciones', type: 'string', align: 'center' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false,
        modificar: false,
        modal_informacion: false,
        numero_gastos: 0,
        fields: {
          id: 0,
          actividad_id: 0,
          monto: 0.00,
          montocopia: 0,
          fecha_ejercido: '',
          proveedor_id: 0,
          solicitud_id: 0,
          presupuesto_real: 0, // sse usa para validaciones
          presupuesto_disponible: 0, // sse usa para validaciones
          presupuesto_disponible_validar: 0, // sse usa para validaciones
          nombre_actividad: '',
          nombre_proveedor: '',
          descripcion_gasto: '',
          total: 0,
          tipo: 0,
          comprobado: 'NO',
          iva: 0
        }
      },
      form_archivos: {
        columns: [
          {name: 'name', label: 'Archivo', field: 'name', sortable: true, type: 'string', align: 'left'},
          {name: 'acciones', label: 'Acciones', field: 'acciones', type: 'string', align: 'right'}
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false,
        fields: {
          id: 0,
          name: '',
          descripcion: '',
          usuario_destino: 0
        }
      },
      form_comentarios: {
        columns: [
          {name: 'autor', label: 'Autor', field: 'autor', sortable: true, type: 'string', align: 'left'},
          {name: 'comentario', label: 'Acciones', field: 'acciones', type: 'string', align: 'right'}
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false,
        fields: {
          autor: 0,
          comentario: '',
          usuario_destino: 0
        }
      },
      form_rechazar: {
        niveles: [ { 'label': 'COLABORADORES', 'value': 1 }, { 'label': 'SOLICITANTES', 'value': 2 }, { 'label': 'AUTORIZADORES', 'value': 3 } ],
        niveles2: [ { 'label': 'COLABORADORES', 'value': 1 }, { 'label': 'SOLICITANTES', 'value': 2 } ],
        fields: {
          autor: 0,
          comentario: '',
          usuario_destino: 0,
          nivel: 0, // usada para rechazar
          orden: 0
        }
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
      recursosOptions: 'vnt/recurso/selectOptions',
      actividadesOptions: 'pmo/actividades/selectOtherOptions',
      gastosOptions: 'fin/tiposgastos/selectOptions',
      proveedoresOptions: 'pmo/proveedor/selectOptions',
      usuariosOptions: 'sys/users/selectOptionsName',
      proyectosOptions: 'pmo/proyecto/selectOptions',
      proyectos: 'pmo/proyecto/proyectos',
      solicitudeslibres: 'fin/autorizar/solicitudeslibres',
      nivelesOptions: 'pmo/autorizadores/selectOtherOptions'
    }),
    actividadesByProyectoOptions () {
      let actividades = this.$store.getters['pmo/actividades/selectOtherOptions'].filter(p => p.proyecto_id === this.form.fields.proyecto_id)
      return actividades
    },
    actividadesByProyectoOptions2 () {
      let actividades = this.$store.getters['pmo/actividades/selectOtherOptions'].filter(p => p.proyecto_id === this.form_solicitudes.fields.proyecto_id)
      actividades.push({ 'label': 'Todos', 'value': 0 })
      return actividades
    },
    usuariosOptions2 () {
      let usuarios = this.$store.getters['sys/users/selectOptionsName']
      usuarios.push({ 'label': 'Todos', 'value': 0 })
      return usuarios
    },
    proyectosOptions2 () {
      let proyectos2 = this.$store.getters['pmo/proyecto/selectOptions']
      proyectos2.push({ 'label': 'Todos', 'value': 0 })
      return proyectos2
    },
    arrayTreeObj () {
      let newObj = []
      this.recursive(this.actividades, newObj, 0, this.itemId, this.isExpanded)
      return newObj
    }
  },
  created () {
    this.loadAll()
  },
  watch: {
    incluir_iva (newValue, old) {
      if (newValue === true) {
        this.form_solicitudes.fields.total = parseFloat(this.form_solicitudes.fields.subtotal) + parseFloat(this.form_solicitudes.fields.subtotal * 0.16)
        this.form_solicitudes.fields.totalcopia = this.currencyFormat(this.form_solicitudes.fields.total)
        this.form_solicitudes.fields.iva = 'SI'
      }
      if (newValue === false) {
        this.form_solicitudes.fields.total = parseFloat(this.form_solicitudes.fields.subtotal)
        this.form_solicitudes.fields.totalcopia = this.currencyFormat(this.form_solicitudes.fields.total)
        this.form_solicitudes.fields.iva = 'NO'
      }
    },
    comprobar_solicitud (newValue, old) {
      if (newValue === true) {
        this.form_solicitudes.fields.comprobado = 'SI'
      }
      if (newValue === false) {
        this.form_solicitudes.fields.comprobado = 'NO'
      }
    },
    comprobar_gasto (newValue, old) {
      if (newValue === true) {
        this.form_gastos.fields.comprobado = 'SI'
        console.log('gasto comprobado')
      }
      if (newValue === false) {
        this.form_gastos.fields.comprobado = 'NO'
        console.log('gasto no comprobado')
      }
    },
    open_archivos (newValue, old) {
      if (newValue === true) {
        this.cargarArchivos()
        console.log('cargando archivos')
      }
    },
    open_comentarios (newValue, old) {
      if (newValue === true) {
        this.cargarComentarios()
        console.log('cargando comentarios')
      }
    }
  },
  methods: {
    ...mapActions({
      getProyectos: 'pmo/proyecto/refresh',
      getTiposGastos: 'fin/tiposgastos/refresh',
      getSolicitudes: 'fin/autorizar/refresh',
      getRecursos: 'vnt/recurso/refresh',
      getProveedores: 'pmo/proveedor/refresh',
      getUsuarios: 'sys/users/refresh',
      getActividades: 'pmo/actividades/refresh',
      getActividadesByProyecto: 'pmo/carga/getByProyecto',
      getGastosByActividad: 'fin/gastos/getByActividad',
      getGastosBySolicitud: 'fin/gastos/getBySolicitud',
      getPresupuestoActividad: 'pmo/actividades/getPresupuestoActividad',
      getProveedorById: 'pmo/proveedor/getById',
      getActividad: 'pmo/actividades/getByActividad',
      updateGasto: 'fin/gastos/update',
      updateSolicitud: 'fin/solicitudes/update',
      getSolicitudById: 'fin/solicitudes/getById',
      filtrarSolicitudes: 'fin/solicitudes/filtrar',
      rechazarSolicitud: 'fin/solicitudes/rechazar',
      archivos: 'fin/archivos_solicitudes/getArchivosBySolicitud',
      eliminarArchivo: 'fin/archivos_solicitudes/deleteFormato',
      guardarComentario: 'fin/mensajes/save',
      mensajesBySolicitud: 'fin/mensajes/getMensajesBySolicitud',
      autorizarSolicitud: 'fin/autorizar/save',
      deleteCotizacion: 'fin/cotizacion/deleteFormato',
      deleteFactura: 'fin/factura/deleteFormato',
      deleteComprobante: 'fin/comprobante/deleteFormato'
    }),

    recursive (obj, newObj, level, itemId, isExpend) {
      let vm = this

      obj.forEach(function (o) {
        if (o.children && o.children.length !== 0) {
          o.level = level
          newObj.push(o)
          if (o.id === itemId) {
            o.expend = isExpend
          }
          if (o.expend === true) {
            vm.recursive(o.children, newObj, o.level + 1, itemId, isExpend)
          }
        } else {
          o.level = level
          newObj.push(o)
          return false
        }
      })
    },
    iconName (item) {
      if (item.expend === true) {
        return 'fas fa-minus-circle'
      }

      if (item.children && item.children.length > 0) {
        return 'fas fa-plus-circle'
      }

      return 'fas fa-check'
    },
    toggle (item, index) {
      let vm = this
      vm.itemId = item.id
      // show  sub items after click on + (more)
      if (item.expend === undefined && item.children !== undefined) {
        if (item.children.length !== 0) {
          vm.recursive(item.children, [], item.level + 1, item.id, true)
        }
      }

      if (item.expend === true && item.children !== undefined) {
        item.children.forEach(function (o) {
          o.expend = undefined
        })

        // this.item.expend = undefined
        vm.$set(item, 'expend', undefined)
        vm.itemId = null
      }
    },
    setPadding (item) {
      return `padding-left: ${item.level * 15}px;`
    },

    async loadAll () {
      // await this.getRecursos()
      // await this.getSolicitudes()
      await this.getProyectos()
      await this.getProveedores()
      await this.getUsuarios()
      await this.getActividades()
      await this.filtrar()
      await this.getTiposGastos()
    },
    setView (view) {
      this.views.grid = false
      this.views.update = false
      this.views[view] = true
    },
    filtrar () {
      this.loadingButton = true
      this.form_solicitudes.loading = true
      this.form_solicitudes.data = []
      let params = this.form.fields
      this.getSolicitudes(params).then(({data}) => {
        this.loadingButton = false
        if (data.solicitudeslibres.length > 0) {
          data.solicitudeslibres.forEach(function (element) {
            if (element.status === 'CREADO') {
              element.color = 'amber'
              element.icon = 'fas fa-arrow-circle-right'
            } else if (element.status === 'SOLICITADO') {
              element.color = 'green'
              element.icon = 'fas fa-check-circle'
            } else if (element.status === 'AUTORIZADO') {
              element.color = 'blue-10'
              element.icon = 'fas fa-check-circle'
            } else if (element.status === 'RECHAZADO') {
              element.color = 'orange-10'
              element.icon = 'clear'
            }
          })
          this.form_solicitudes.data = data.solicitudes
        }
        this.form_solicitudes.loading = false
      }).catch(error => {
        console.error(error)
        this.form_solicitudes.loading = false
      })
    },
    selectActividades () {
      this.$v.form_gastos.$reset()
      this.form_gastos.fields.id = 0
      this.form_gastos.fields.actividad_id = 0
      this.form_gastos.fields.presupuesto_disponible = 0
      this.form_gastos.fields.presupuesto_disponible_validar = 0
      this.form_gastos.fields.presupuesto_real = 0
      this.form_gastos.fields.nombre_actividad = ''
      this.form_gastos.fields.nombre_proveedor = ''
      this.form_gastos.fields.monto = 0.00
      this.form_gastos.fields.proveedor_id = 0
      this.form_gastos.fields.descripcion_gasto = ''
      this.form_gastos.fields.subtotal = 0
      this.form_gastos.fields.total = 0
      this.incluir_iva = false
      this.form_solicitudes.fields.iva = 'NO'
      this.comprobar_gasto = false
      this.form_gastos.fields.comprobado = 'NO'
    },
    currencyFormat (num) {
      return Number.parseFloat(num).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
    },
    presupuestoActividad () {
      this.getPresupuestoActividad({id: this.form_gastos.fields.actividad_id}).then(({data}) => {
        if (data.result === 'success') {
          this.form_gastos.fields.presupuesto_disponible = this.currencyFormat(data.actividades[0].cantidad_disponible)
          this.form_gastos.fields.presupuesto_disponible_validar = data.actividades[0].cantidad_disponible
          this.form_gastos.fields.presupuesto_real = this.currencyFormat(data.actividades[0].costo)
          this.form_gastos.fields.nombre_actividad = data.actividades[0].nombre
        }
      }).catch(error => {
        console.error(error)
      })
    },
    limpiar_gasto () {
      this.$v.form_gastos.$reset()
      this.form_gastos.fields.id = 0
      this.form_gastos.fields.monto = 0.00
      this.form_gastos.fields.tipo = 0
      this.form_gastos.fields.fecha_ejercido = ''
      this.form_gastos.fields.descripcion_gasto = ''
      this.form_gastos.fields.actividad_id = 0
      this.form_gastos.fields.proveedor_id = 0
      this.form_gastos.fields.presupuesto_real = 0
      this.form_gastos.fields.presupuesto_disponible = 0
      this.form_gastos.fields.presupuesto_disponible_validar = 0
      this.form_gastos.fields.nombre_actividad = ''
      this.form_gastos.fields.nombre_proveedor = ''
      this.form_gastos.fields.comprobado = ''
      this.comprobar_gasto = false
      this.form_gastos.fields.comprobado = 'NO'
    },
    editSingleGasto (row) {
      this.form_gastos.fields.id = row.id
      this.form_gastos.fields.proveedor_id = row.proveedor_id
      this.form_gastos.fields.actividad_id = row.actividad_id
      this.presupuestoActividad()
      this.form_gastos.fields.monto = row.monto
      this.form_gastos.fields.tipo = row.tipo
      this.form_gastos.fields.descripcion_gasto = row.descripcion_gasto
      this.form_gastos.fields.fecha_ejercido = row.fecha_ejercido
      this.form_gastos.fields.comprobado = row.comprobado
      if (row.comprobado === 'SI') {
        this.comprobar_gasto = true
      } if (row.comprobado === 'NO') {
        this.comprobar_gasto = false
      }
      this.form_gastos.modificar = true
    },
    actualizarGastos () {
      let params = this.form_gastos.fields
      this.updateGasto(params).then(({data}) => {
        if (data.result === 'success') {
          this.$showMessage(data.message.title, data.message.content)
          this.form_gastos.modificar = false
          this.form_solicitudes.fields.total = data.total
          this.form_solicitudes.fields.subtotal = data.total
          this.form_solicitudes.fields.totalcopia = data.totalcopia
          if (this.form_solicitudes.fields.iva === 'SI') {
            this.form_solicitudes.fields.total = parseFloat(this.form_solicitudes.fields.subtotal) + parseFloat(this.form_solicitudes.fields.subtotal * 0.16)
          }
          this.form_solicitudes.fields.totalcopia = this.currencyFormat(this.form_solicitudes.fields.total)
          this.limpiar_gasto()
          this.cargarGastos()
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async verInformacion (row) {
      this.form_gastos.fields.solicitud_id = row.id
      this.form_solicitudes.fields.id = row.id
      this.form_solicitudes.fields.status = row.status
      this.form_solicitudes.fields.fecha_solicitado = row.fecha_solicitado
      this.form_solicitudes.fields.orden = row.orden
      this.form_solicitudes.fields.autorizacion_id = row.autorizacion_id
      this.form_solicitudes.fields.fecha_creado = row.fecha_creado
      this.form_solicitudes.fields.proyecto_id = row.proyecto_id
      this.form_solicitudes.fields.creador = row.creador
      if (row.comprobado === 'SI') {
        this.comprobar_solicitud = true
      }
      this.form_solicitudes.fields.comprobado = row.comprobado
      this.form_solicitudes.fields.nombre_proyecto = row.nombre_proyecto
      this.form_solicitudes.fields.responsable_solicitud = row.responsable_solicitud
      this.form_solicitudes.fields.total = row.total
      this.form_solicitudes.fields.subtotal = row.total
      if (row.iva === 'SI') {
        this.incluir_iva = true
      }
      this.form_solicitudes.fields.iva = row.iva
      this.form_solicitudes.fields.totalcopia = row.totalcopia
      this.form_solicitudes.mensajes = []
      this.form_solicitudes.fields.autorizador_id = row.autorizador_id
      this.form_solicitudes.fields.autorizadores = row.autorizadores
      this.setView('update')
      await this.cargarGastos()
    },
    async cargarGastos () {
      this.form_gastos.loading = true
      this.form_gastos.data = []
      this.getGastosBySolicitud({id: this.form_solicitudes.fields.id}).then(({data}) => {
        if (data.gastos.length > 0) {
          if (this.form_solicitudes.fields.iva === 'NO') {
            data.gastos.forEach(function (element) {
              element.iva = 0
              element.total = element.montocopia
            })
          }
          data.gastos.forEach(function (element) {
            if (element.comprobado === 'SI') {
              element.comprobado = true
            } else {
              element.comprobado = false
            }
            if (element.cotizacion.length > 0) {
              element.cotizacion.forEach(function (documento) {
                if (documento.doc_type === 'docx') {
                  documento.color = 'blue-9'
                  documento.icon = 'insert_drive_file'
                } else if (documento.doc_type === 'pdf') {
                  documento.color = 'red-10'
                  documento.icon = 'description'
                } else if (documento.doc_type === 'jpg' || documento.doc_type === 'jpg' || documento.doc_type === 'png') {
                  documento.color = 'brown'
                  documento.icon = 'insert_photo'
                }
              })
            }
            if (element.factura.length > 0) {
              element.factura.forEach(function (documento) {
                if (documento.doc_type === 'docx') {
                  documento.color = 'blue-9'
                  documento.icon = 'insert_drive_file'
                } else if (documento.doc_type === 'pdf') {
                  documento.color = 'red-10'
                  documento.icon = 'description'
                } else if (documento.doc_type === 'jpg' || documento.doc_type === 'jpg' || documento.doc_type === 'png') {
                  documento.color = 'brown'
                  documento.icon = 'insert_photo'
                }
              })
            }
            if (element.comprobante.length > 0) {
              element.comprobante.forEach(function (documento) {
                if (documento.doc_type === 'docx') {
                  documento.color = 'blue-9'
                  documento.icon = 'insert_drive_file'
                } else if (documento.doc_type === 'pdf') {
                  documento.color = 'red-10'
                  documento.icon = 'description'
                } else if (documento.doc_type === 'jpg' || documento.doc_type === 'jpg' || documento.doc_type === 'png') {
                  documento.color = 'brown'
                  documento.icon = 'insert_photo'
                }
              })
            }
          })
          this.form_gastos.data = data.gastos
        }
        this.form_gastos.loading = false
      }).catch(error => {
        console.log(error)
        this.form_gastos.loading = false
      })
    },
    async cargarArchivos () {
      this.form_archivos.loading = true
      this.form_archivos.data = []
      await this.archivos({solicitud_id: this.form_solicitudes.fields.id}).then(({data}) => {
        if (data.archivos_solicitudes.length > 0) {
          this.form_archivos.data = data.archivos_solicitudes
        }
        this.form_archivos.loading = false
      }).catch(error => {
        console.log(error)
        this.form_archivos.loading = false
      })
    },
    uploadFormato () {
      try {
        this.loadingButton = true
        let file = this.$refs.fileInputFormato.files[0]
        let formData = new FormData()
        console.log(file.name)
        // this.form_bases.fields.name2 = file.name
        formData.append('file', file)
        formData.append('formato_requisito_id', 0)
        formData.append('nombre', file.name)
        formData.append('solicitud_id', this.form_solicitudes.fields.id)
        if (file === null || file === undefined) {
          this.loadingButton = false
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' /* || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document' */) {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('archivos_solicitudes/uploadArchivo', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              if (response.data.result === 'success') {
                this.$showMessage('Exito', 'Se ha cargado el archivo')
                this.cargarArchivos()
                // this.form_bases.fields.observaciones_archivo = ''
              } else {
                if (response.data.err !== '') {
                  console.log(file.type.split('/')[1].toLowerCase())
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
            console.log(file.type.split('/')[1].toLowerCase())
          }
        } else {
          this.loadingButton = false
          this.$showMessage('Error', 'No ha seleccionado un archivo con la extensión .jpg, .jpeg, .png, .pdf, .xml')
        }
      } catch (error) {
        this.loadingButton = false
      }
    },
    descargarFormato (row) {
      window.open(process.env.API + '/public/assets/expedientes/solicitudes/' + row.name)
    },
    deleteFormato (row) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este formato de requisitos?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.eliminarArchivo({id: row.id}).then(({data}) => {
          if (data.result === 'success') {
            this.$showMessage(data.message.title, data.message.content)
            this.cargarArchivos()
          }
        }).catch(error => {
          console.log(error)
        })
      }).catch(() => {})
    },
    autorizar () {
      let params = this.form_solicitudes.fields
      this.autorizarSolicitud(params).then(({data}) => {
        if (data.result === 'success') {
          this.$showMessage(data.message.title, data.message.content)
          this.setView('grid')
          this.filtrar()
        }
      }).catch(error => {
        console.error(error)
      })
    },
    regresar () {
      this.form_solicitudes.fields.status = ''
      this.filtrar()
      this.form_gastos.modificar = false
      this.limpiar_gasto()
      this.limpiar_comentario()
      this.form_solicitudes.mensajes = []
      this.incluir_iva = false
      this.form_solicitudes.fields.iva = 'NO'
      this.comprobar_gasto = false
      this.form_gastos.fields.comprobado = 'NO'
      this.comprobar_solicitud = false
      this.setView('grid')
    },
    limpiar_comentario () {
      this.$v.form_comentarios.$reset()
      this.form_comentarios.fields.descripcion = ''
    },
    agregarComentario () {
      this.$v.form_comentarios.$touch()
      if (!this.$v.form_comentarios.$error) {
        this.guardarComentario({solicitud_id: this.form_solicitudes.fields.id, mensaje: this.form_comentarios.fields.descripcion, user_destino: this.form_comentarios.fields.usuario_destino}).then(({data}) => {
          if (data.result === 'success') {
            this.$showMessage(data.message.title, data.message.content)
            this.limpiar_comentario()
            this.cargarComentarios()
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    async cargarComentarios () {
      this.form_solicitudes.mensajes = []
      console.log(this.form_solicitudes.fields.id)
      await this.mensajesBySolicitud({solicitud_id: this.form_solicitudes.fields.id}).then(({data}) => {
        if (data.mensajes.length > 0) {
          this.form_solicitudes.mensajes = data.mensajes
        }
      }).catch(error => {
        console.log(error)
      })
    }, // RECA
    limpiar_rechazar () {
      this.$v.form_rechazar.$reset()
      this.form_rechazar.fields.descripcion = ''
      this.form_rechazar.fields.nivel = 0
      this.form_rechazar.fields.orden = 0
    },
    rechazar () {
      this.$v.form_rechazar.$touch()
      if (!this.$v.form_rechazar.$error) {
        this.rechazarSolicitud({solicitud_id: this.form_solicitudes.fields.id, mensaje: this.form_rechazar.fields.descripcion}).then(({data}) => {
          if (data.result === 'success') {
            this.$showMessage(data.message.title, data.message.content)
            this.limpiar_rechazar()
            this.filtrar()
            this.setView('grid')
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    selectedCotizacionDocumento (row) {
      this.$refs.cotizacionInput.value = ''
      this.registro_cotizacion = row
      this.$refs.cotizacionInput.click()
    },
    uploadCotizacionFile () {
      try {
        let file = this.$refs.cotizacionInput.files[0]
        let formData = new FormData()
        console.log(file.name)
        console.log(this.registro_cotizacion.id)
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('gasto_id', this.registro_cotizacion.id)
        formData.append('formato_requisito_id', 0)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('cotizaciones_gastos/uploadArchivo', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              console.log(response)
              if (response.data.result === 'success') {
                this.$showMessage('Exito', 'Se ha cargado el archivo')
                this.cargarGastos()
              } else {
                if (response.data.err !== '') {
                  console.log(file.type.split('/')[1].toLowerCase())
                  this.$showMessage('Errores en archivo', response.data.err)
                }
              }
              this.$q.loading.hide()
            }).catch(error => {
              console.error(error)
              this.$q.loading.hide()
            })
          } else {
            this.$showMessage('Error', 'El tipo de archivo no es el requerido para este requisito.')
            console.log(file.type.split('/')[1].toLowerCase())
          }
        } else {
          this.$showMessage('Error', 'No ha seleccionado un archivo con la extensión .jpg, .jpeg, .png, .pdf, .xml')
        }
      } catch (error) {
        console.log(error)
      }
    },
    cotizacionAccion (cotizacion) {
      this.$q.dialog({
        title: 'Confirmar acción',
        message: '¿Qué desea hacer con el archivo?',
        ok: 'Descargar',
        cancel: 'Eliminar',
        preventClose: true
      }).then(() => {
        this.descargarCotizacion(cotizacion.id, cotizacion.doc_type)
      }).catch(() => {
        this.borrarCotizacion(cotizacion)
      })
    },
    descargarCotizacion (name, ext) {
      console.log(name)
      let id = name
      let uri = process.env.API + `cotizaciones_gastos/getFile/${id}/${ext}`
      window.open(uri, '_blank')
    },
    borrarCotizacion (cotizacion) {
      let params = cotizacion
      this.deleteCotizacion(params).then(({data}) => {
        if (data.result === 'success') {
          this.$showMessage(data.message.title, data.message.content)
          this.cargarGastos()
        }
      }).catch(error => {
        console.error(error)
      })
    },
    selectedFacturaDocumento (row) {
      this.$refs.facturaInput.value = ''
      this.registro_factura = row
      this.$refs.facturaInput.click()
    },
    uploadFacturaFile () {
      try {
        let file = this.$refs.facturaInput.files[0]
        let formData = new FormData()
        console.log(file.name)
        console.log(this.registro_factura.id)
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('gasto_id', this.registro_factura.id)
        formData.append('formato_requisito_id', 0)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('facturas_gastos/uploadArchivo', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              console.log(response)
              if (response.data.result === 'success') {
                this.$showMessage('Exito', 'Se ha cargado el archivo')
                this.cargarGastos()
              } else {
                if (response.data.err !== '') {
                  console.log(file.type.split('/')[1].toLowerCase())
                  this.$showMessage('Errores en archivo', response.data.err)
                }
              }
              this.$q.loading.hide()
            }).catch(error => {
              console.error(error)
              this.$q.loading.hide()
            })
          } else {
            this.$showMessage('Error', 'El tipo de archivo no es el requerido para este requisito.')
            console.log(file.type.split('/')[1].toLowerCase())
          }
        } else {
          this.$showMessage('Error', 'No ha seleccionado un archivo con la extensión .jpg, .jpeg, .png, .pdf, .xml')
        }
      } catch (error) {
        console.log(error)
      }
    },
    facturaAccion (factura) {
      this.$q.dialog({
        title: 'Confirmar acción',
        message: '¿Qué desea hacer con la factura?',
        ok: 'Descargar',
        cancel: 'Eliminar',
        preventClose: true
      }).then(() => {
        this.descargarFactura(factura.id, factura.doc_type)
      }).catch(() => {
        this.borrarFactura(factura)
      })
    },
    descargarFactura (name, ext) {
      console.log(name)
      let id = name
      let uri = process.env.API + `facturas_gastos/getFile/${id}/${ext}`
      window.open(uri, '_blank')
    },
    borrarFactura (factura) {
      let params = factura
      this.deleteFactura(params).then(({data}) => {
        if (data.result === 'success') {
          this.$showMessage(data.message.title, data.message.content)
          this.cargarGastos()
        }
      }).catch(error => {
        console.error(error)
      })
    },
    selectedComprobanteDocumento (row) {
      this.$refs.comprobanteInput.value = ''
      this.registro_comprobante = row
      this.$refs.comprobanteInput.click()
    },
    uploadComprobanteFile () {
      try {
        let file = this.$refs.comprobanteInput.files[0]
        let formData = new FormData()
        console.log(file.name)
        console.log(this.registro_comprobante.id)
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('gasto_id', this.registro_comprobante.id)
        formData.append('formato_requisito_id', 0)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('comprobantes_gastos/uploadArchivo', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              console.log(response)
              if (response.data.result === 'success') {
                this.$showMessage('Exito', 'Se ha cargado el archivo')
                this.cargarGastos()
              } else {
                if (response.data.err !== '') {
                  console.log(file.type.split('/')[1].toLowerCase())
                  this.$showMessage('Errores en archivo', response.data.err)
                }
              }
              this.$q.loading.hide()
            }).catch(error => {
              console.error(error)
              this.$q.loading.hide()
            })
          } else {
            this.$showMessage('Error', 'El tipo de archivo no es el requerido para este requisito.')
            console.log(file.type.split('/')[1].toLowerCase())
          }
        } else {
          this.$showMessage('Error', 'No ha seleccionado un archivo con la extensión .jpg, .jpeg, .png, .pdf, .xml')
        }
      } catch (error) {
        console.log(error)
      }
    },
    comprobanteAccion (comprobante) {
      this.$q.dialog({
        title: 'Confirmar acción',
        message: '¿Qué desea hacer con el comprobante?',
        ok: 'Descargar',
        cancel: 'Eliminar',
        preventClose: true
      }).then(() => {
        this.descargarComprobante(comprobante.id, comprobante.doc_type)
      }).catch(() => {
        this.borrarComprobante(comprobante)
      })
    },
    descargarComprobante (name, ext) {
      console.log(name)
      let id = name
      let uri = process.env.API + `comprobantes_gastos/getFile/${id}/${ext}`
      window.open(uri, '_blank')
    },
    borrarComprobante (comprobante) {
      let params = comprobante
      this.deleteComprobante(params).then(({data}) => {
        if (data.result === 'success') {
          this.$showMessage(data.message.title, data.message.content)
          this.cargarGastos()
        }
      }).catch(error => {
        console.error(error)
      })
    }
  },
  validations: {
    form_gastos: {
      fields: {
        monto: {required, minValue: minValue(0), maxValue: maxValue(1000000000)},
        actividad_id: {required, minValue: minValue(1)},
        proveedor_id: {required, minValue: minValue(1)},
        tipo: {required, minValue: minValue(1)}
      }
    },
    form_comentarios: {
      fields: {
        descripcion: {required}
      }
    },
    form_rechazar: {
      fields: {
        descripcion: {required}
      }
    }
  }
}
</script>

<style lang="stylus" scoped>

</style>
