<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Compras" />
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
                 <q-btn class="btn_nuevo" icon="add" label="Nuevo" @click="newRow()" />
            </div>
          </div>
        </div>
      </div>
      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md" style="padding: 0;">
            <div class="col q-pa-md">
              <div class="col-sm-12" style="padding-bottom: 10px;">
                <q-search color="primary" v-model="form.filter"/>
              </div>
              <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                <q-table id="sticky-table-newstyle"
                    :data="compras"
                    :columns="form.columns"
                    :selected.sync="form.selected"
                    :filter="form.filter"
                    color="positive"
                    title=""
                    :dense="true"
                    class="bg-white"
                    :pagination.sync="form.pagination"
                    :loading="form.loading"
                    :rows-per-page-options="form.rowsOptions">
                    <template slot="body" slot-scope="props">
                      <q-tr :props="props">
                        <q-td key="folio" :props="props">{{ props.row.folio }}</q-td>
                        <q-td key="status" :props="props"><q-chip dense :icon="props.row.icon" :color="props.row.color" text-color="white">{{ props.row.status }}</q-chip></q-td>
                        <q-td key="empresa" :props="props">{{ props.row.empresa }}</q-td>
                        <q-td key="proveedor" :props="props">{{ props.row.proveedor }}</q-td>
                        <q-td key="fecha_compra" :props="props">{{ props.row.fecha_compra }}</q-td>
                        <q-td key="acciones" :props="props">
                          <q-btn small flat @click="editRow(props.row)" color="blue-6" icon="edit">
                            <q-tooltip>Editar</q-tooltip>
                          </q-btn>
                          <q-btn small flat @click="deleteSingleRow(props.row.id)" color="negative" icon="delete">
                            <q-tooltip>Eliminar</q-tooltip>
                          </q-btn>
                        </q-td>
                      </q-tr>
                    </template>
                  </q-table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!--Crear un municipio-->

    <div v-if="views.create">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Compras" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el label="Nuevo"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
             <q-btn @click="atras()" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
           </div>
         </div>
        </div>
      </div>
      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="dialpad" icon-color="dark" :error="$v.form.fields.folio.$error" error-label="Escriba el folio">
                  <q-input readonly upper-case v-model="form.fields.folio" type="text" inverted color="dark" float-label="Folio" maxlength="100"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha.$error" error-label="Seleccione una fecha">
                  <q-datetime v-model="form.fields.fecha" type="date" inverted color="dark" float-label="Fecha" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="person" icon-color="dark">
                  <q-select v-model="form.fields.proveedor_id" inverted color="dark" float-label="Proveedor" :options="proveedoresOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="fas fa-info" icon-color="dark">
                  <q-input readonly upper-case v-model="form.fields.status" type="text" inverted color="dark" float-label="Status" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="work" icon-color="dark">
                  <q-select v-model="form.fields.project_id" inverted color="dark" float-label="Presupuesto" :options="proyectoOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="business" icon-color="dark" :error="$v.form.fields.empresa_id.$error" error-label="Seleccione una empresa">
                  <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter @input="cargaDireccionesSucursales()"/>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="fas fa-dollar-sign" icon-color="dark">
                  <q-input upper-case v-model="form.fields.condiciones_pago" type="text" inverted color="dark" float-label="Condiciones de pago"/>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="local_shipping" icon-color="dark">
                  <q-input upper-case v-model="form.fields.condiciones_entrega" type="text" inverted color="dark" float-label="Condiciones de entrega"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-car" icon-color="dark">
                  <q-input upper-case v-model="form.fields.transporte" type="text" inverted color="dark" float-label="Transporte"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-box" icon-color="dark">
                  <q-select v-model="form.fields.entrega" inverted color="dark" float-label="Entrega" :options="entregaOptions" filter @input="cargaDireccionesSucursales()"/>
                </q-field>
              </div>
              <div class="col-sm-6" v-if="form.fields.entrega === 'EMPRESA'">
                <q-field icon="store" icon-color="dark">
                  <q-select v-model="form.fields.direccion_id" inverted color="dark" stack-label="Dirección fiscal" :options="direccionesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-6" v-if="form.fields.entrega === 'SUCURSAL'">
                <q-field icon="store" icon-color="dark">
                  <q-select v-model="form.fields.sucursal_id" inverted color="dark" stack-label="Sucursal" :options="sucursalesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-6" v-if="form.fields.entrega === 'OTRO'">
                <q-field icon="store" icon-color="dark">
                  <q-input upper-case v-model="form.fields.direccion_entrega" type="text" inverted color="dark" float-label="Especifique la dirección de entrega"/>
                </q-field>
              </div>
              <div class="col-sm-12">
                <q-field icon="local_library" icon-color="dark">
                  <q-input upper-case v-model="form.fields.observaciones" type="textarea" inverted color="dark" float-label="Observaciones"/>
                </q-field>
              </div>
            </div>
            <div class="row q-mt-lg">
              <div class="col-sm-2 offset-sm-10 pull-right">
                <q-btn @click="save()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
      <div v-if="views.update">
        <div class="q-pa-sm panel-header">
          <div class="row">
            <div class="col-sm-6">
              <div class="q-pa-sm q-gutter-sm">
                <q-breadcrumbs align="left">
                  <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                  <q-breadcrumbs-el label="Compras" to="" @click.native="setView('grid')"/>
                  <q-breadcrumbs-el :label="form.fields.folio"/>
                </q-breadcrumbs>
              </div>
            </div>
          <div class="col-sm-6 pull-right">
            <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
             <q-btn @click="atras()" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
             <q-btn @click="exportPDF()" color="red" icon-right="fas fa-file-pdf" v-if="form.fields.status === 'COMPRA'">GENERAR PDF</q-btn>
           </div>
         </div>
         </div>
      </div>
      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="dialpad" icon-color="dark" :error="$v.form.fields.folio.$error" error-label="Escriba el folio">
                  <q-input readonly upper-case v-model="form.fields.folio" type="text" inverted color="dark" float-label="Folio" maxlength="100"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha.$error" error-label="Seleccione una fecha">
                  <q-datetime v-model="form.fields.fecha" type="date" inverted color="dark" float-label="Fecha" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="person" icon-color="dark">
                  <q-select v-model="form.fields.proveedor_id" inverted color="dark" float-label="Proveedor" :options="proveedoresOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="fas fa-info" icon-color="dark">
                  <q-input readonly upper-case v-model="form.fields.status" type="text" inverted color="dark" float-label="Status" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="work" icon-color="dark">
                  <q-select v-model="form.fields.project_id" inverted color="dark" float-label="Presupuesto" :options="proyectoOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="business" icon-color="dark" :error="$v.form.fields.empresa_id.$error" error-label="Seleccione una empresa">
                  <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="fas fa-dollar-sign" icon-color="dark">
                  <q-input upper-case v-model="form.fields.condiciones_pago" type="text" inverted color="dark" float-label="Condiciones de pago"/>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="local_shipping" icon-color="dark">
                  <q-input upper-case v-model="form.fields.condiciones_entrega" type="text" inverted color="dark" float-label="Condiciones de entrega"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-car" icon-color="dark">
                  <q-input upper-case v-model="form.fields.transporte" type="text" inverted color="dark" float-label="Transporte"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-box" icon-color="dark">
                  <q-select v-model="form.fields.entrega" inverted color="dark" float-label="Entrega" :options="entregaOptions" filter @input="cargaDireccionesSucursales()"/>
                </q-field>
              </div>
              <div class="col-sm-6" v-if="form.fields.entrega === 'EMPRESA'">
                <q-field icon="store" icon-color="dark">
                  <q-select v-model="form.fields.direccion_id" inverted color="dark" stack-label="Dirección fiscal" :options="direccionesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-6" v-if="form.fields.entrega === 'SUCURSAL'">
                <q-field icon="store" icon-color="dark">
                  <q-select v-model="form.fields.sucursal_id" inverted color="dark" stack-label="Sucursal" :options="sucursalesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-6" v-if="form.fields.entrega === 'OTRO'">
                <q-field icon="store" icon-color="dark">
                  <q-input upper-case v-model="form.fields.direccion_entrega" type="text" inverted color="dark" float-label="Especifique la dirección de entrega"/>
                </q-field>
              </div>
              <div class="col-sm-12">
                <q-field icon="local_library" icon-color="dark">
                  <q-input upper-case v-model="form.fields.observaciones" type="textarea" inverted color="dark" float-label="Observaciones"/>
                </q-field>
              </div>
            </div>
            <div class="row q-mt-lg">
              <div class="col-sm-12 pull-right">
                <!-- <q-btn @click="update()" class="btn_guardar" icon-right="save" :loading="loadingButton" v-if="form.fields.status === 'NUEVO'">Guardar</q-btn> -->
                <q-btn @click="update()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
                <q-btn @click="solicitar()" class="btn_solicitar" icon-right="check" :loading="loadingButton" v-if="form.fields.status === 'NUEVO' && form_detalles.data.length > 0">Solicitar</q-btn>
                <q-btn @click="aprobar()" color="lime-10" icon-right="check" :loading="loadingButton" v-if="form.fields.status === 'SOLICITADO'">Aprobar</q-btn>
                <q-btn @click="comprar()" color="teal-10" icon-right="check" :loading="loadingButton" v-if="form.fields.status === 'ORDEN DE COMPRA'">COMPRAR</q-btn>
                <q-btn @click="rechazar()" color="orange-10" icon-right="clear" :loading="loadingButton" v-if="form.fields.status !== 'COMPRA' && form.fields.status !== 'NUEVO'">RECHAZAR</q-btn>
                <q-btn @click="crear_solicitudes(form.fields.project_id)" class="btn_guardar" icon-right="fas fa-dollar-sign" :loading="loadingButton" v-if="form.fields.status === 'COMPRA' && form.fields.project_id > 0">GENERAR SOLICITUD</q-btn>
              </div>
            </div>

            <q-tabs v-model="tab" class="shadow-1" inverted animated swipeable color="teal" align="justify" style="margin-top: 20px;">
            <q-tab name="detalles" slot="title" icon="fas fa-box" label="DETALLES"/>
            <q-tab :disable="form.fields.status !== 'APROBADO' && form.fields.status !== 'ORDEN DE COMPRA' && form.fields.status !== 'COMPRA'" name="recepciones" slot="title" icon="book" label="RECEPCIONES"/>

            <q-tab-pane name="detalles">
              <div class="row q-col-gutter-xs">
                <div class="row q-mt-lg subtitulos" v-if="form.fields.status === 'NUEVO'">
                  Detalles de la compra
                </div>
              </div>
              <div class="row q-col-gutter-xs" style="margin-top: 10px;" v-if="form.fields.status === 'NUEVO' || this.form_detalles.editar === true">
                <div class="col-sm-12 pull-right" v-if="form.fields.status === 'NUEVO'" style="padding-bottom: 15px;">
                  <q-btn @click="modalProducto(true)" icon-right="add" color="green" :loading="loadingButton">Nuevo producto</q-btn>
                  <q-btn @click="exportCSV()" color="green" icon-right="fas fa-file-excel">
                    Descargar plantilla
                  </q-btn>
                  <input style="display:none" inverted color="dark" stack-label="Archivo csv" type="file" name="" value="" ref="fileInputCSV" accept=".csv" @change="cargarCSV()">
                  <q-btn @click="$refs.fileInputCSV.click()" color="tertiary" :loading="loadingButton">
                    <i class="fa fa-upload" aria-hidden="true"></i>&nbsp;Subir CSV
                  </q-btn>
                  <q-btn @click="deleteAll()" color="red-10" icon-right="delete">
                    Eliminar
                  </q-btn>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-box" icon-color="dark" :error="$v.form_detalles.fields.producto_id.$error" error-label="Seleccione un producto">
                    <q-select v-model="form_detalles.fields.producto_id" inverted color="dark" float-label="Producto" :options="productosOptions" filter @input="getPresentacion()" :readonly="form.fields.status !== 'NUEVO'"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-box" icon-color="dark">
                    <q-select readonly v-model="form_detalles.fields.presentacion_id" inverted color="dark" float-label="Unidad" :options="presentacionesOptions" filter/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="style" icon-color="dark" :error="$v.form_detalles.fields.cantidad.$error" error-label="Ingrese una cantidad">
                    <q-input upper-case v-model="form_detalles.fields.cantidad" type="text" inverted color="dark" float-label="Cantidad" maxlength="50" @keyup="isNumber($event,'cantidad')" :readonly="form.fields.status !== 'NUEVO'"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form_detalles.fields.costo.$error" error-label="Ingrese un costo">
                    <q-input upper-case v-model="form_detalles.fields.costo" type="text" inverted color="dark" float-label="Costo" maxlength="50" suffix="MXN" @keyup="isNumber($event,'costo')" :readonly="form.fields.status !== 'NUEVO'"/>
                  </q-field>
                </div>
                <div class="col-sm-9">
                  <q-field icon="label" icon-color="dark">
                    <q-input upper-case v-model="form_detalles.fields.descripcion" type="text" inverted color="dark" float-label="Descripción (opcional)"/>
                  </q-field>
                </div>
                <div class="col-sm-12 pull-right">
                  <q-btn @click="saveMovimientosDetalles()" class="btn_guardar" icon-right="add" :loading="loadingButton" v-if="this.form_detalles.editar === false && this.form.fields.status === 'NUEVO'">Agregar</q-btn>
                  <q-btn @click="updateMovimientosDetalles()" class="btn_actualizar" icon-right="save" :loading="loadingButton" v-if="this.form_detalles.editar === true && this.form.fields.status !== 'COMPRA'">Actualizar</q-btn>
                </div>
              </div>
              <div class="row q-mt-sm" style="margin-top:10px;">
                <div class="col-sm-12">
                  <q-search color="primary" v-model="form_detalles.filter"/>
                </div>
                <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                  <q-table id="sticky-table-newstyle"
                    :data="form_detalles.data"
                    :columns="form_detalles.columns"
                    :selected.sync="form_detalles.selected"
                    :filter="form_detalles.filter"
                    color="positive"
                    title=""
                    :dense="true"
                    :pagination.sync="form_detalles.pagination"
                    :loading="form_detalles.loading"
                    :rows-per-page-options="form_detalles.rowsOptions">
                    <template slot="body" slot-scope="props">
                      <q-tr :props="props">
                        <q-td key="producto" :props="props">{{ props.row.producto }}</q-td>
                        <q-td key="cantidad" :props="props">{{ props.row.cantidad }}</q-td>
                        <q-td key="costo" :props="props">${{ props.row.costo }}</q-td>
                        <q-td key="importe" :props="props">${{ props.row.importe }}</q-td>
                        <q-td key="total_recibido" :props="props">{{ props.row.total_recibido }}</q-td>
                        <q-td key="descripcion" :props="props">{{ props.row.descripcion }}</q-td>
                        <q-td key="acciones" :props="props">
                          <q-btn small flat @click="editRowDetalle(props.row)" color="blue-6" icon="edit" v-if="form.fields.status !== 'COMPRA'">
                            <q-tooltip>Editar</q-tooltip>
                          </q-btn>
                          <q-btn small flat @click="deleteSingleRowDetalle(props.row.id)" color="negative" icon="delete" v-if="form.fields.status === 'NUEVO'">
                            <q-tooltip>Eliminar</q-tooltip>
                          </q-btn>
                          <q-btn small flat color="cyan" icon="fas fa-check" v-if="form.fields.status !== 'NUEVO'">
                            <q-tooltip>{{ form.fields.status }}</q-tooltip>
                          </q-btn>
                        </q-td>
                      </q-tr>
                    </template>
                  </q-table>
                  <q-inner-loading :visible="form_detalles.loading">
                    <q-spinner-dots size="64px" color="primary" />
                  </q-inner-loading>
                </div>
              </div>
            </q-tab-pane>
            <q-tab-pane name="recepciones">
              <recepciones :compra_id="form.fields.id"/>
            </q-tab-pane>
          </q-tabs>
          </div>
        </div>
      </div>
      <products :show="modal_producto" @closeModal="modalProducto($event)"/>
    </div>
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { required, minValue } from 'vuelidate/lib/validators'
import moment from 'moment'
import axios from 'axios'
import Recepciones from '../../components/layout/cmp/Recepciones'
import Products from '../../components/layout/cmp/Products'

export default {
  components: {
    Recepciones,
    Products
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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'INVENTARIOS'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'AUXILIAR'.toUpperCase() || propiedades[i].toUpperCase() === 'VENTAS'.toUpperCase() || propiedades[i].toUpperCase() === 'COMPRAS'.toUpperCase()) {
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
      direccionesOptions: [],
      sucursalesOptions: [],
      entregaOptions: [ { label: 'EMPRESA', value: 'EMPRESA' }, { label: 'SUCURSAL', value: 'SUCURSAL' }, { label: 'OTRO', value: 'OTRO' } ],
      modal_producto: false,
      tab: 'detalles',
      loadingButton: false,
      views: {
        grid: true,
        create: false,
        update: false
      },
      form: {
        fields: {
          id: 0,
          folio: '',
          fecha: '',
          proveedor_id: '',
          status: 0,
          project_id: 0,
          empresa_id: 0,
          condiciones_pago: '',
          condiciones_entrega: '',
          transporte: '',
          entrega: '',
          direccion_id: 0,
          sucursal_id: 0,
          direccion_entrega: '',
          observaciones: ''
        },
        // data: [],
        columns: [
          { name: 'folio', label: 'Folio', field: 'folio', sortable: true, type: 'string', align: 'center' },
          { name: 'status', label: 'Status', field: 'status', sortable: true, type: 'string', align: 'left' },
          { name: 'empresa', label: 'Empresa', field: 'empresa', sortable: true, type: 'string', align: 'left' },
          { name: 'proveedor', label: 'Proveedor', field: 'proveedor', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_compra', label: 'Fecha', field: 'fecha_compra', sortable: true, type: 'string', align: 'center' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_detalles: {
        data: [],
        editar: false,
        fields: {
          id: 0,
          producto_id: 0,
          presentacion_id: 0,
          cantidad: 0,
          costo: 0,
          compra_id: 0,
          descripcion: ''
        },
        // data: [],
        columns: [
          { name: 'producto', label: 'Producto', field: 'producto', sortable: true, type: 'string', align: 'left' },
          { name: 'cantidad', label: 'Cantidad', field: 'cantidad', sortable: true, type: 'string', align: 'right' },
          { name: 'costo', label: 'Costo', field: 'costo', sortable: true, type: 'string', align: 'right' },
          { name: 'importe', label: 'Importe', field: 'importe', sortable: true, type: 'string', align: 'right' },
          { name: 'total_recibido', label: 'Recibido', field: 'total_recibido', sortable: true, type: 'string', align: 'right' },
          { name: 'descripcion', label: 'Descripción', field: 'descripcion', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
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
      compras: 'cmp/compras/compras'
    }),
    proveedoresOptions () {
      let proveedores = this.$store.getters['pmo/proveedor/selectOptions']
      proveedores.splice(0, 0, {label: '---Seleccione---', value: 0})
      proveedores.sort(function (a, b) {
        if (a.nombre > b.nombre) {
          return 1
        }
        if (a.nombre < b.nombre) {
          return -1
        }
        return 0
      })
      return proveedores
    },
    productosOptions () {
      let productos = this.$store.getters['inv/productos/selectOptions']
      productos.push({label: '---Seleccione---', value: 0})
      productos.sort(function (a, b) {
        if (a.nombre > b.nombre) {
          return 1
        }
        if (a.nombre < b.nombre) {
          return -1
        }
        return 0
      })
      return productos
    },
    presentacionesOptions () {
      let presentaciones = this.$store.getters['inv/tipos_presentaciones/selectOptions']
      presentaciones.push({label: '---Seleccione---', value: 0})
      presentaciones.sort(function (a, b) {
        if (a.nombre > b.nombre) {
          return 1
        }
        if (a.nombre < b.nombre) {
          return -1
        }
        return 0
      })
      return presentaciones
    },
    proyectoOptions () {
      let proyecto = this.$store.getters['pmo/proyecto/selectOptions']
      proyecto.splice(0, 0, {label: '---Seleccione---', value: 0})
      proyecto.sort(function (a, b) {
        if (a.nombre_proyecto > b.nombre_proyecto) {
          return 1
        }
        if (a.nombre_proyecto < b.nombre_proyecto) {
          return -1
        }
        return 0
      })
      return proyecto
    },
    empresasOptions () {
      let empresa = this.$store.getters['vnt/empresa/selectOptions']
      empresa.splice(0, 0, {label: '---Seleccione---', value: 0})
      empresa.sort(function (a, b) {
        if (a.razon_social > b.razon_social) {
          return 1
        }
        if (a.razon_social < b.razon_social) {
          return -1
        }
        return 0
      })
      return empresa
    }
  },
  created () {
    this.loadAll()
  },
  watch: {
    tab (newValue, old) {
      if (newValue === 'detalles') {
        this.getDetallesByMovimiento()
      }
    }
  },
  methods: {
    ...mapActions({
      getCompras: 'cmp/compras/refresh',
      saveCompras: 'cmp/compras/save',
      updateCompras: 'cmp/compras/update',
      deleteCompras: 'cmp/compras/delete',
      getProveedores: 'pmo/proveedor/refresh',
      getEmpresas: 'vnt/empresa/refresh',
      getMunicipiosByEstado: 'vnt/municipio/getByEstado',
      getFolioConsecutivo: 'cmp/compras/getFolioConsecutivo', //
      getTiposMovimientos: 'inv/tipos_movimientos/refresh',
      getProductos: 'inv/productos/refresh',
      getPresentaciones: 'inv/tipos_presentaciones/refresh',
      getDetalles: 'cmp/compras_detalles/getByCompra',
      saveDetalles: 'cmp/compras_detalles/save',
      updateDetalles: 'cmp/compras_detalles/update',
      deleteDetalles: 'cmp/compras_detalles/delete',
      getProyectos: 'pmo/proyecto/refresh',
      deleteByCompra: 'cmp/compras_detalles/deleteByCompra'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getCompras()
      await this.getProveedores()
      await this.getProductos()
      await this.getPresentaciones()
      await this.getProyectos()
      await this.getEmpresas()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    async getFolioNuevo () {
      this.form.fields.folio = ''
      await this.getFolioConsecutivo().then(({data}) => {
        if (data.result === 'success') {
          this.form.fields.folio = data.folio
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async cargaMunicipios () {
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
    isNumber (evt, input) {
      switch (input) {
        case 'cantidad':
          this.form_detalles.fields.cantidad = this.form_detalles.fields.cantidad.replace(/[^0-9.]/g, '')
          this.$v.form_detalles.fields.cantidad.$touch()
          break
        case 'costo':
          this.form_detalles.fields.costo = this.form_detalles.fields.costo.replace(/[^0-9.]/g, '')
          this.$v.form_detalles.fields.costo.$touch()
          break
        default:
          break
      }
    },
    save () {
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        this.form.fields.fecha = moment(String(this.form.fields.fecha)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
        let params = this.form.fields
        this.saveCompras(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'check',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.form.fields.id = data.id
            this.form_detalles.fields.compra_id = data.id
            this.limpiarDetalles()
            this.getDetallesByMovimiento()
            this.setView('update')
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    update () {
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        params.direccion_id = this.form.fields.direccion_id
        this.$q.loading.show({message: 'Cargando...'})
        this.updateCompras(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'check',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.$q.loading.hide()
          } else {
            this.$q.loading.hide()
            this.$showMessage(data.message.title, data.message.content)
          }
          this.form.fields.status = data.status
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    async editRow (row) {
      this.form.fields.id = row.id
      this.form.fields.folio = row.folio
      this.form.fields.fecha = row.fecha
      this.form.fields.proveedor_id = row.proveedor_id
      this.form.fields.status = row.status
      this.form.fields.project_id = row.project_id
      this.form.fields.empresa_id = row.empresa_id
      this.form.fields.condiciones_pago = row.condiciones_pago
      this.form.fields.condiciones_entrega = row.condiciones_entrega
      this.form.fields.transporte = row.transporte
      this.form.fields.entrega = row.entrega
      if (row.direccion_id === null) {
        this.form.fields.direccion_id = 0
      } else {
        this.form.fields.direccion_id = row.direccion_id
      }
      if (row.sucursal_id === null) {
        this.form.fields.sucursal_id = 0
      } else {
        this.form.fields.sucursal_id = row.sucursal_id
      }
      this.form.fields.direccion_entrega = row.direccion_entrega
      this.form_detalles.fields.compra_id = row.id
      this.form.fields.observaciones = row.observaciones
      if (this.form.fields.empresa_id > 0) {
        await this.getDirecciones()
        await this.getSucursales()
      }
      this.limpiarDetalles()
      this.getDetallesByMovimiento()
      this.setView('update')
    },
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar esta compra?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete(id)
      }).catch(() => {})
    },
    delete (id) {
      let params = {id: id}
      this.deleteCompras(params).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            // only required parameter is the message:
            message: data.message.content,
            timeout: 3000,
            type: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'delete',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
          this.loadAll()
          this.setView('grid')
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    newRow () {
      this.$v.form.$reset()
      this.form.fields.id = 0
      this.form.fields.folio = ''
      this.form.fields.fecha = new Date()
      this.form.fields.status = 'NUEVO'
      this.form.fields.proveedor_id = ''
      this.form.fields.project_id = 0
      this.form.fields.empresa_id = 0
      this.form.fields.condiciones_pago = ''
      this.form.fields.condiciones_entrega = ''
      this.form.fields.transporte = ''
      this.form.fields.entrega = ''
      this.form.fields.direccion_id = 0
      this.form.fields.sucursal_id = 0
      this.form.fields.direccion_entrega = ''
      this.form.fields.observaciones = ''
      this.getFolioNuevo()
      this.setView('create')
    },
    atras () {
      this.setView('grid')
      this.loadAll()
    },
    async getPresentacion () {
      await axios.get(`/productos/getPresentacionByProducto/${this.form_detalles.fields.producto_id}`).then(({data}) => {
        this.form_detalles.fields.presentacion_id = data.productos[0].presentacion_id
        // this.form.loading = false
      }).catch(error => {
        console.error(error)
      })
    },
    async saveMovimientosDetalles () {
      this.$v.form_detalles.$touch()
      if (!this.$v.form_detalles.$error) {
        this.loadingButton = true
        let params = this.form_detalles.fields
        this.saveDetalles(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'check',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.limpiarDetalles()
            this.getDetallesByMovimiento()
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    async updateMovimientosDetalles () {
      this.$v.form_detalles.$touch()
      if (!this.$v.form_detalles.$error) {
        this.loadingButton = true
        let params = this.form_detalles.fields
        this.updateDetalles(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'check',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.form_detalles.editar = false
            this.limpiarDetalles()
            this.getDetallesByMovimiento()
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    async getDetallesByMovimiento () {
      this.form_detalles.data = []
      await this.getDetalles({compra_id: this.form_detalles.fields.compra_id}).then(({data}) => {
        if (data.movimientos_detalles.length > 0) {
          this.form_detalles.data = data.movimientos_detalles
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async editRowDetalle (row) {
      //
      this.form_detalles.fields.id = row.id
      this.form_detalles.fields.producto_id = row.producto_id
      this.form_detalles.fields.presentacion_id = row.presentacion_id
      this.form_detalles.fields.cantidad = row.cantidad
      this.form_detalles.fields.costo = row.costo
      this.form_detalles.fields.descripcion = row.descripcion
      this.form_detalles.fields.compra_id = this.form.fields.id
      this.form_detalles.editar = true
      //
      this.$v.form_detalles.$reset()
    },
    async limpiarDetalles () {
      //
      this.form_detalles.fields.id = 0
      this.form_detalles.fields.producto_id = 0
      this.form_detalles.fields.presentacion_id = 0
      this.form_detalles.fields.cantidad = 0
      this.form_detalles.fields.costo = 0
      this.form_detalles.fields.descripcion = ''
      this.form_detalles.fields.compra_id = this.form.fields.id
      this.form_detalles.editar = false
      //
      this.$v.form_detalles.$reset()
    },
    deleteSingleRowDetalle (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este detalle de compra?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.deleteMovimientoDetalle(id)
      }).catch(() => {})
    },
    deleteMovimientoDetalle (id) {
      let params = {id: id}
      this.deleteDetalles(params).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            // only required parameter is the message:
            message: data.message.content,
            timeout: 3000,
            type: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'delete',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
          this.getDetallesByMovimiento()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    solicitar () {
      this.form.fields.status = 'SOLICITADO'
      this.update()
    },
    aprobar () {
      this.form.fields.status = 'ORDEN DE COMPRA'
      this.update()
    },
    comprar () {
      this.form.fields.status = 'COMPRA'
      this.update()
    },
    rechazar () {
      this.form.fields.status = 'NUEVO'
      this.update()
    },
    crear_solicitudes (id) {
      this.$router.push({ path: `/ejecucion_presupuestos/${id}/compra/${this.form.fields.id}/${this.form.fields.proveedor_id}/${this.form.fields.empresa_id}` })
    },
    exportPDF () {
      let uri
      uri = process.env.API + `compras/exportPDF_compra/${this.form.fields.id}`
      window.open(uri, '_blank')
    },
    modalProducto (show) {
      this.modal_producto = show
      if (show === false) {
        this.getProductos()
      }
    },
    exportCSV () {
      let uri = process.env.API + 'compras_detalles/exportCSV'
      window.open(uri, '_blank')
    },
    loadingMessage (mensaje) {
      this.$q.loading.show({message: mensaje})
    },
    cargarCSV () {
      this.loadingMessage('Cargando los datos del csv ...')
      let file = this.$refs.fileInputCSV.files[0]
      let formData = new FormData()
      formData.append('file', file)
      formData.append('compra_id', this.form.fields.id)
      axios.post('/compras_detalles/cargarCSV', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }).then(response => {
        if (response.data.result === 'success') {
          this.$q.notify({
            // only required parameter is the message:
            message: response.data.message,
            timeout: 3000,
            type: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'done',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
          this.getDetallesByMovimiento()
          this.$q.loading.hide()
        } else {
          this.$q.loading.hide()
          if (response.data.err !== '') {
            this.$showMessage('Errores en archivo', response.data.err)
          }
        }
      }).catch(error => {
        this.$q.loading.hide()
        console.error(error)
      })
    },
    deleteAll () {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar todos los detalles de la compra?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.deleteAllDetails()
      }).catch(() => {})
    },
    deleteAllDetails () {
      let params = {compra_id: this.form.fields.id}
      this.deleteByCompra(params).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            // only required parameter is the message:
            message: data.message.content,
            timeout: 3000,
            type: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'delete',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
          this.getDetallesByMovimiento()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async getDirecciones () {
      this.direccionesOptions = []
      await axios.get(`/direcciones/getByEmpresaOptions/${this.form.fields.empresa_id}`).then(({data}) => {
        this.direccionesOptions = data.direcciones
      }).catch(error => {
        console.error(error)
      })
    },
    async getSucursales () {
      this.sucursalesOptions = []
      await axios.get(`/sucursales/getByEmpresaOptions/${this.form.fields.empresa_id}`).then(({data}) => {
        this.sucursalesOptions = data.sucursales
      }).catch(error => {
        console.error(error)
      })
    },
    cargaDireccionesSucursales () {
      if (this.form.fields.empresa_id) {
        if (this.form.fields.entrega === 'EMPRESA') {
          this.getDirecciones()
        }
        if (this.form.fields.entrega === 'SUCURSAL') {
          this.getSucursales()
        }
      }
    }
  },
  validations: {
    form: {
      fields: {
        folio: { required },
        fecha: { required },
        empresa_id: { required, minValue: minValue(1) }
      }
    },
    form_detalles: {
      fields: {
        producto_id: { required, minValue: minValue(1) },
        cantidad: { required },
        costo: {required, minValue: minValue(0)}
      }
    }
  }
}
</script>

<style scoped>
</style>
