<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Productos" />
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
          <div class="col q-pa-xs col-sm-12">
            <div class="row q-col-gutter-xs">
              <div class="q-pb-xs col-sm-3">
                <q-field icon="fas fa-tasks" icon-color="dark">
                  <q-select v-model="form.fields.tipo_id" inverted color="dark" float-label="Categoría" :options="tiposOptions" filter @input="getProductos()"/>
                </q-field>
              </div>
            </div>
          </div>
          <div class="col q-pa-md">
            <div class="col q-pa-md">
              <div class="col-sm-12">
                <q-search color="primary" v-model="form.filter"/>
              </div>
              <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                <q-table id="sticky-table-newstyle"
                  bordered
                  :data="form.data"
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
                      <q-td key="codigo_compuesto" :props="props">{{ props.row.codigo_compuesto }}</q-td>
                      <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                      <q-td key="categoria" :props="props">{{ props.row.categoria }}</q-td>
                      <q-td key="presentacion" :props="props">{{ props.row.presentacion }}</q-td>
                      <q-td key="linea" :props="props">{{ props.row.linea }}</q-td>
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
                <q-inner-loading :visible="form.loading">
                  <q-spinner-dots size="64px" color="primary" />
                </q-inner-loading>
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
                <q-breadcrumbs-el label="Productos" to="" @click.native="atras()"/>
                <q-breadcrumbs-el label="Nuevo producto"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
                 <div class="q-pa-sm q-gutter-sm">
                  <q-btn @click="atras()" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
                  <q-btn @click="save()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
                </div>
              </div>
        </div>
      </div>
      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <!-- <div class="col q-pa-md col-sm-12">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6 pull-left">
                <div class="col-xs-12 col-sm-12 col-md-12 pull-left" >
                  <q-btn @click="atras()" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
                </div>
              </div>
              <div class="col-sm-6 pull-right">
                <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
                  <q-btn @click="save()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
                </div>
              </div>
            </div>
          </div> -->
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="fas fa-tasks" icon-color="dark" :error="$v.form.fields.tipo_id.$error" error-label="Ingrese una categoría">
                  <q-select v-model="form.fields.tipo_id" inverted color="dark" float-label="Categoría" :options="tiposOptions" filter @input="cargarLineasByCategoria()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="view_week" icon-color="dark" :error="$v.form.fields.linea_id.$error" error-label="Ingrese una categoría">
                  <q-select v-model="form.fields.linea_id" inverted color="dark" float-label="Línea" :options="lineasOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-box" icon-color="dark" :error="$v.form.fields.presentacion_id.$error" error-label="Elija una presentación">
                  <q-select v-model="form.fields.presentacion_id" inverted color="dark" float-label="Unidad" :options="presentacionesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="dialpad" icon-color="dark" :error="$v.form.fields.codigo.$error" error-label="Ingrese un código">
                  <q-input upper-case v-model="form.fields.codigo" type="text" inverted color="dark" float-label="Código" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-box" icon-color="dark" :error="$v.form.fields.nombre.$error" error-label="Ingrese un nombre">
                  <q-input upper-case v-model="form.fields.nombre" type="text" inverted color="dark" float-label="Nombre" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <!--<q-field icon="fas fa-box" icon-color="dark">
                  <q-select v-model="form.fields.clave_producto_id" inverted color="dark" float-label="Clave producto servicio" :options="claveProductoOptions" filter/>
                </q-field> -->
                <q-field icon="fas fa-box" icon-color="dark">
                  <q-input v-model="form.fields.clave_producto_id" type="text" inverted color="dark" float-label="Clave producto servicio" maxlength="100">
                    <q-autocomplete @search="searchClavProd" />
                  </q-input>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="label" icon-color="dark">
                  <q-input upper-case v-model="form.fields.numero_parte" type="text" inverted color="dark" float-label="Número de parte" maxlength="100" />
                </q-field>
              </div>
            </div>
            <div class="row q-pa-md">
              Precios
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-2">
                <div class="icono_field">
                  <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                    <q-input v-model="form.fields.precio_a" v-money="money" inverted color="dark" float-label="Precio a" suffix="MXN"></q-input>
                  </q-field>
                </div>
              </div>
              <div class="col-sm-2">
                <div class="icono_field">
                  <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                    <q-input v-model="form.fields.precio_b" v-money="money" inverted color="dark" float-label="Precio b" suffix="MXN"></q-input>
                  </q-field>
                </div>
              </div>
              <div class="col-sm-2">
                <div class="icono_field">
                  <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                    <q-input v-model="form.fields.precio_c" v-money="money" inverted color="dark" float-label="Precio c" suffix="MXN"></q-input>
                  </q-field>
                </div>
              </div>
              <div class="col-sm-2">
                <div class="icono_field">
                  <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                    <q-input v-model="form.fields.precio_d" v-money="money" inverted color="dark" float-label="Precio d" suffix="MXN"></q-input>
                  </q-field>
                </div>
              </div>
              <div class="col-sm-2">
                <div class="icono_field">
                  <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                    <q-input v-model="form.fields.precio_e" v-money="money" inverted color="dark" float-label="Precio e" suffix="MXN"></q-input>
                  </q-field>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

      <!-- Modal editar TIPO DE PRESENTACIÓN   -->
    <div v-if="views.update">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Productos" to="" @click.native="atras()"/>
                <q-breadcrumbs-el label="Modificar producto"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
               <q-btn @click="atras()" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
               <q-btn @click="update()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
             </div>
             </div>
        </div>
      </div>
      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <!-- <div class="col q-pa-md col-sm-12">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6 pull-left">
                <div class="col-xs-12 col-sm-12 col-md-12 pull-left" >
                  <q-btn @click="atras()" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
                </div>
              </div>
              <div class="col-sm-6 pull-right">
                <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
                </div>
              </div>
            </div>
          </div> -->
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="fas fa-tasks" icon-color="dark" :error="$v.form.fields.tipo_id.$error" error-label="Ingrese una categoría">
                  <q-select v-model="form.fields.tipo_id" inverted color="dark" float-label="Categoría" :options="tiposOptions" filter @input="cargarLineasByCategoria()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="view_week" icon-color="dark" :error="$v.form.fields.linea_id.$error" error-label="Ingrese una categoría">
                  <q-select v-model="form.fields.linea_id" inverted color="dark" float-label="Línea" :options="lineasOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-box" icon-color="dark" :error="$v.form.fields.presentacion_id.$error" error-label="Elija una presentación">
                  <q-select v-model="form.fields.presentacion_id" inverted color="dark" float-label="Unidad" :options="presentacionesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="dialpad" icon-color="dark" :error="$v.form.fields.codigo.$error" error-label="Ingrese un código">
                  <q-input upper-case v-model="form.fields.codigo" type="text" inverted color="dark" float-label="Código" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-box" icon-color="dark" :error="$v.form.fields.nombre.$error" error-label="Ingrese un nombre">
                  <q-input upper-case v-model="form.fields.nombre" type="text" inverted color="dark" float-label="Nombre" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <!--<q-field icon="fas fa-box" icon-color="dark">
                  <q-select v-model="form.fields.clave_producto_id" inverted color="dark" float-label="Clave producto servicio" :options="claveProductoOptions" filter/>
                </q-field> -->
                <q-field icon="fas fa-box" icon-color="dark">
                  <q-input v-model="form.fields.clave_producto_id" type="text" inverted color="dark" float-label="Clave producto servicio" maxlength="100">
                    <q-autocomplete @search="searchClavProd" />
                  </q-input>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="label" icon-color="dark">
                  <q-input upper-case v-model="form.fields.numero_parte" type="text" inverted color="dark" float-label="Número de parte" maxlength="100" />
                </q-field>
              </div>
            </div>
            <div class="row q-pa-md">
              Precios
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-2">
                <div class="icono_field">
                  <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                    <q-input v-model="form.fields.precio_a" v-money="money" inverted color="dark" float-label="Precio a" suffix="MXN"></q-input>
                  </q-field>
                </div>
              </div>
              <div class="col-sm-2">
                <div class="icono_field">
                  <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                    <q-input v-model="form.fields.precio_b" v-money="money" inverted color="dark" float-label="Precio b" suffix="MXN"></q-input>
                  </q-field>
                </div>
              </div>
              <div class="col-sm-2">
                <div class="icono_field">
                  <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                    <q-input v-model="form.fields.precio_c" v-money="money" inverted color="dark" float-label="Precio c" suffix="MXN"></q-input>
                  </q-field>
                </div>
              </div>
              <div class="col-sm-2">
                <div class="icono_field">
                  <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                    <q-input v-model="form.fields.precio_d" v-money="money" inverted color="dark" float-label="Precio d" suffix="MXN"></q-input>
                  </q-field>
                </div>
              </div>
              <div class="col-sm-2">
                <div class="icono_field">
                  <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                    <q-input v-model="form.fields.precio_e" v-money="money" inverted color="dark" float-label="Precio e" suffix="MXN"></q-input>
                  </q-field>
                </div>
              </div>
            </div>
            <div class="row q-pa-md">
              <div class="col-sm-11 pull-left">
                Lista de materiales
              </div>
              <div class="col-sm-1 pull-right">
                <q-btn round dense small flat @click="crearBom()" class="btn_guardar" icon="add">
                  <q-tooltip>Agregar</q-tooltip>
                </q-btn>
              </div>
            </div>
            <div class="col q-pa-md border-panel">
              <div class="col-sm-12">
                <q-search color="primary" v-model="form.filter"/>
              </div>
                <q-table id="sticky-table-newstyle"
                  :data="form_bom.data"
                  :columns="form_bom.columns"
                  :selected.sync="form_bom.selected"
                  :filter="form_bom.filter"
                  color="positive"
                  title=""
                  :dense="true"
                  :pagination.sync="form_bom.pagination"
                  :loading="form_bom.loading"
                  :rows-per-page-options="form_bom.rowsOptions">
                  <template slot="body" slot-scope="props">
                    <q-tr :props="props">
                      <!-- <q-td key="almacen" :props="props">{{ props.row.almacen }}</q-td> -->
                      <q-td key="categoria" :props="props">{{ props.row.categoria }}</q-td>
                      <q-td key="unidad" :props="props">{{ props.row.unidad }}</q-td>
                      <q-td key="producto" :props="props">{{ props.row.producto }}</q-td>
                      <q-td key="cantidad" :props="props">{{ props.row.cantidad }}</q-td>
                      <q-td key="acciones" :props="props">
                        <q-btn small flat @click="editBom(props.row)" color="blue-6" icon="edit">
                          <q-tooltip>Editar</q-tooltip>
                        </q-btn>
                        <q-btn small flat @click="deleteSingleBom(props.row.id)" color="negative" icon="delete">
                          <q-tooltip>Eliminar</q-tooltip>
                        </q-btn>
                      </q-td>
                    </q-tr>
                  </template>
                </q-table>
                <q-inner-loading :visible="form_bom.loading">
                  <q-spinner-dots size="64px" color="primary" />
                </q-inner-loading>
            </div>

            <q-modal v-if="bom" style="background-color: rgba(0,0,0,0.7);" v-model="bom" :content-css="{width: '80vw', height: '35vh'}">
              <q-modal-layout>
                <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
                  <div class="col-sm-6">
                    <q-toolbar-title>
                      Agregar material
                    </q-toolbar-title>
                  </div>
                  <div class="col-sm-6 pull-right">
                    <q-btn
                      flat
                      round
                      dense
                      color="white"
                      @click="bom = false"
                      icon="fas fa-times-circle"
                    />
                  </div>
                </q-toolbar>
                <div class="row q-mt-lg" style="margin-lef:10px;margin-right: 15px;">
                  <!-- <div class="col-sm-3">
                    <q-field icon="business" icon-color="dark" :error="$v.form_bom.fields.empresa_id.$error" error-label="Seleccione una empresa">
                      <q-select v-model="form_bom.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter @input="cargaAlmacenes()"/>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="store" icon-color="dark" :error="$v.form_bom.fields.almacen_id.$error" error-label="Seleccione un almacén">
                      <q-select v-model="form_bom.fields.almacen_id" inverted color="dark" float-label="Almacén" :options="almacenesOptions" filter/>
                    </q-field>
                  </div> -->
                  <div class="col-sm-4">
                    <q-field icon="fas fa-tasks" icon-color="dark" :error="$v.form_bom.fields.categoria_id.$error" error-label="Ingrese una categoría">
                      <q-select v-model="form_bom.fields.categoria_id" inverted color="dark" float-label="Categoría" :options="tiposOptions" filter @input="cargarLineasByCategoria2()"/>
                    </q-field>
                  </div>
                  <div class="col-sm-4">
                    <q-field icon="view_week" icon-color="dark" :error="$v.form_bom.fields.linea_id.$error" error-label="Ingrese una línea">
                      <q-select v-model="form_bom.fields.linea_id" inverted color="dark" float-label="Línea" :options="lineasBomOptions" filter @input="getProductosOpt()"/>
                    </q-field>
                  </div>
                  <div class="col-sm-4">
                    <q-field icon="fas fa-box" icon-color="dark" :error="$v.form_bom.fields.material_id.$error" error-label="Seleccione un producto">
                      <q-select v-model="form_bom.fields.material_id" inverted color="dark" float-label="Producto" :options="productosOptions" filter @input="getPresentacion()"/>
                    </q-field>
                  </div>
                </div>
                <div class="row q-mt-lg" style="margin-right: 15px;">
                  <div class="col-sm-4">
                    <q-field icon="fas fa-box" icon-color="dark">
                      <q-select readonly v-model="form_bom.fields.presentacion_id" inverted color="dark" float-label="Unidad" :options="presentacionesOptions" filter/>
                    </q-field>
                  </div>
                  <div class="col-sm-4">
                    <q-field icon="label" icon-color="dark" :error="$v.form_bom.fields.cantidad.$error" error-label="Ingrese una cantidad">
                      <q-input upper-case v-model="form_bom.fields.cantidad" type="text" inverted color="dark" float-label="Cantidad" maxlength="50" @keyup="isNumber($event,'cantidad')"/>
                    </q-field>
                  </div>
                  <div class="col-sm-4 pull-right">
                    <q-btn @click="guardarBom()" class="btn_guardar" icon-right="save" :loading="loadingButton" v-if="editar_bom === false">Guardar</q-btn>
                    <q-btn @click="editarBom()" class="btn_actualizar" icon-right="save" :loading="loadingButton" v-if="editar_bom === true">Actualizar</q-btn>
                  </div>
                </div>
                <!-- <div class="row q-mt-lg pull-right" style="margin-right: 15px;"> -->
                <!-- </div> -->
                  <!-- <q-tree
                    :nodes="props"
                    :expanded.sync="propsExpanded"
                    color="red"
                    node-key="label"
                  /> -->
                <!-- </div> -->
              </q-modal-layout>
            </q-modal>

          </div>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { required, maxLength, minValue, minLength } from 'vuelidate/lib/validators'
import {VMoney} from 'v-money'
import axios from 'axios'

export default {
  components: {},
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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'INVENTARIOS'.toUpperCase() || propiedades[i].toUpperCase() === 'COMPRAS'.toUpperCase()) {
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
      almacenesOptions: [{label: '---Seleccione---', value: 0}],
      bom: false,
      editar_bom: false,
      lineasOptions: [{label: '---Seleccione---', value: 0}],
      lineasBomOptions: [{label: '---Seleccione---', value: 0}],
      productosOptions: [{label: '---Seleccione---', value: 0}],
      money: {
        decimal: '.',
        thousands: ',',
        precision: 2,
        masked: false
      },
      loadingButton: false,
      views: {
        grid: true,
        create: false,
        update: false
      },
      form: {
        fields: {
          id: 0,
          nombre: '',
          tipo_id: 0,
          codigo: '',
          linea_id: 0,
          presentacion_id: 0,
          clave_producto_id: 0,
          precio_a: 0,
          precio_b: 0,
          precio_c: 0,
          precio_d: 0,
          precio_e: 0,
          numero_parte: ''
        },
        data: [],
        columns: [
          { name: 'codigo_compuesto', label: 'Código', field: 'codigo_compuesto', sortable: true, type: 'string', align: 'left' },
          { name: 'nombre', label: 'Nombre', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'categoria', label: 'Categoría', field: 'categoria', sortable: true, type: 'string', align: 'left' },
          { name: 'presentacion', label: 'Presentación', field: 'presentacion', sortable: true, type: 'string', align: 'left' },
          { name: 'linea', label: 'Línea', field: 'linea', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_bom: {
        fields: {
          id: 0,
          producto_id: 0,
          material_id: 0,
          cantidad: 0,
          almacen_id: 0,
          empresa_id: 0,
          linea_id: 0,
          presentacion_id: 0,
          categoria_id: 0
        },
        // data: [],
        columns: [
          // { name: 'almacen', label: 'Almacén', field: 'almacen', sortable: true, type: 'string', align: 'left' },
          { name: 'categoria', label: 'Categoría', field: 'categoria', sortable: true, type: 'string', align: 'left' },
          { name: 'unidad', label: 'Unidad', field: 'unidad', sortable: true, type: 'string', align: 'left' },
          { name: 'producto', label: 'Producto', field: 'producto', sortable: true, type: 'string', align: 'left' },
          { name: 'cantidad', label: 'Cantidad', field: 'cantidad', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        data: [],
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
  directives: {money: VMoney},
  computed: {
    ...mapGetters({
      productos: 'inv/productos/productos',
      claveProductoOptions: 'inv/productos/selectClaveProductosOptions'
    }),
    tiposOptions () {
      let tipos = this.$store.getters['inv/tipos_articulos/selectOptions']
      tipos.splice(0, 0, {label: '---Seleccione---', value: 0})
      tipos.sort(function (a, b) {
        if (a.nombre > b.nombre) {
          return 1
        }
        if (a.nombre < b.nombre) {
          return -1
        }
        return 0
      })
      return tipos
    },
    presentacionesOptions () {
      let presentaciones = this.$store.getters['inv/tipos_presentaciones/selectOptions']
      presentaciones.splice(0, 0, {label: '---Seleccione---', value: 0})
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
    empresasOptions () {
      let empresas = this.$store.getters['vnt/empresa/selectOptions']
      empresas.splice(0, 0, {label: '---Seleccione---', value: 0})
      empresas.sort(function (a, b) {
        if (a.razon_social > b.razon_social) {
          return 1
        }
        if (a.razon_social < b.razon_social) {
          return -1
        }
        return 0
      })
      return empresas
    }
  },
  created () {
    this.loadAll()
  },
  methods: {
    ...mapActions({
      getProductosByCategoria: 'inv/productos/getByCategoria',
      saveProductos: 'inv/productos/save',
      updateProductos: 'inv/productos/update',
      deleteProductos: 'inv/productos/delete',
      getTiposProductos: 'inv/tipos_articulos/refresh',
      getLineas: 'inv/lineas/refresh',
      getPresentaciones: 'inv/tipos_presentaciones/refresh',
      getLineasByCategoria: 'inv/lineas/getByCategoria',
      getClaveProductos: 'inv/productos/getClaveProductos',
      getBomByProducto: 'inv/bom/getByProducto',
      getEmpresas: 'vnt/empresa/refresh',
      getAlmacenesByEmpresa: 'inv/almacenes/getByEmpresa',
      getProductosByLinea: 'inv/productos/getProductoByLinea',
      saveBom: 'inv/bom/save',
      updateBom: 'inv/bom/update',
      deleteBom: 'inv/bom/delete'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getTiposProductos()
      await this.getProductos()
      await this.getLineas()
      await this.getPresentaciones()
      // await this.getClaveProductos()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    async getProductos () {
      this.form.data = []
      await this.getProductosByCategoria({categoria: this.form.fields.tipo_id}).then(({data}) => {
        if (data.productos.length > 0) {
          this.form.data = data.productos
        }
      }).catch(error => {
        console.error(error)
      })
    },
    evaluaMonto (campo) {
      this.cadena2 = '' + campo
      this.cadena = this.cadena2.split(',')
      this.monto_a_pagar = ''
      for (var i = 0; i < this.cadena.length; i++) {
        this.monto_a_pagar = this.monto_a_pagar + this.cadena[i]
      }
      return parseFloat(this.monto_a_pagar)
    },
    save () {
      this.form.fields.nombre = this.form.fields.nombre.trim()
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        params.precio_a_validado = this.evaluaMonto(this.form.fields.precio_a)
        params.precio_b_validado = this.evaluaMonto(this.form.fields.precio_b)
        params.precio_c_validado = this.evaluaMonto(this.form.fields.precio_C)
        params.precio_d_validado = this.evaluaMonto(this.form.fields.precio_d)
        params.precio_e_validado = this.evaluaMonto(this.form.fields.precio_e)
        this.saveProductos(params).then(({data}) => {
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
            this.form_bom.fields.producto_id = data.id
            this.getBom()
            // this.loadAll()
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
      this.form.fields.nombre = this.form.fields.nombre.trim()
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        params.precio_a_validado = this.evaluaMonto(this.form.fields.precio_a)
        params.precio_b_validado = this.evaluaMonto(this.form.fields.precio_b)
        params.precio_c_validado = this.evaluaMonto(this.form.fields.precio_c)
        params.precio_d_validado = this.evaluaMonto(this.form.fields.precio_d)
        params.precio_e_validado = this.evaluaMonto(this.form.fields.precio_e)
        this.updateProductos(params).then(({data}) => {
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
            // this.loadAll()
            // this.setView('grid')
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    editRow (row) {
      this.form.fields.id = row.id
      this.form.fields.nombre = row.nombre
      this.form.fields.tipo_id = row.tipo_id
      this.form.fields.codigo = row.codigo
      this.form.fields.presentacion_id = row.presentacion_id
      this.form.fields.clave_producto_id = row.clave_producto_id
      this.cargarLineasByCategoria()
      this.form.fields.linea_id = row.linea_id
      this.form.fields.precio_a = row.precio_a
      this.form.fields.precio_b = row.precio_b
      this.form.fields.precio_c = row.precio_c
      this.form.fields.precio_d = row.precio_d
      this.form.fields.precio_e = row.precio_e
      this.form.fields.numero_parte = row.numero_parte

      this.form_bom.fields.producto_id = row.id
      this.limpiarBom()
      this.getBom()
      this.setView('update')
    },
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este producto?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete(id)
      }).catch(() => {})
    },
    delete (id) {
      let params = {id: id}
      this.deleteProductos(params).then(({data}) => {
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
      this.form.fields.nombre = ''
      this.form.fields.tipo_id = 0
      this.form.fields.codigo = ''
      this.form.fields.linea_id = 0
      this.form.fields.presentacion_id = 0
      this.form.fields.clave_producto_id = 0
      this.form.fields.precio_a = 0
      this.form.fields.precio_b = 0
      this.form.fields.precio_c = 0
      this.form.fields.precio_d = 0
      this.form.fields.precio_e = 0
      this.form.fields.numero_parte = ''
      this.setView('create')
    },
    async cargarLineasByCategoria () {
      this.lineasOptions = []
      this.form.fields.linea_id = 0
      await this.getLineasByCategoria({categoria_id: this.form.fields.tipo_id}).then(({data}) => {
        this.lineasOptions.push({label: '---Seleccione---', value: 0})
        if (data.lineas.length > 0) {
          data.lineas.forEach(linea => {
            this.lineasOptions.push({label: linea.nombre, value: linea.id})
          })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async cargarLineasByCategoria2 () {
      this.lineasBomOptions = []
      this.form_bom.fields.linea_id = 0
      await this.getLineasByCategoria({categoria_id: this.form_bom.fields.categoria_id}).then(({data}) => {
        this.lineasBomOptions.push({label: '---Seleccione---', value: 0})
        if (data.lineas.length > 0) {
          data.lineas.forEach(linea => {
            this.lineasBomOptions.push({label: linea.nombre, value: linea.id})
          })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    atras () {
      this.loadAll()
      this.setView('grid')
      this.lineasOptions = []
      this.lineasOptions.push({label: '---Seleccione---', value: 0})
    },
    async getBom () {
      this.form_bom.data = []
      await this.getBomByProducto({producto_id: this.form.fields.id}).then(({data}) => {
        if (data.bom.length > 0) {
          this.form_bom.data = data.bom
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async crearBom () {
      this.bom = true
      this.editar_bom = false
      // await this.getEmpresas()
      // await this.cargaAlmacenes()
      await this.getTiposProductos()
      this.limpiarBom()
    },
    async cargaAlmacenes () {
      this.almacenesOptions = []
      this.form_bom.fields.almacen_id = 0
      await this.getAlmacenesByEmpresa({empresa_id: this.form_bom.fields.empresa_id}).then(({data}) => {
        this.almacenesOptions.push({label: '---Seleccione---', value: 0})
        if (data.almacenes.length > 0) {
          data.almacenes.forEach(almacen => {
            this.almacenesOptions.push({label: almacen.nombre, value: almacen.id})
          })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async getProductosOpt () {
      this.productosOptions = []
      this.form_bom.fields.material_id = 0
      await this.getProductosByLinea({linea: this.form_bom.fields.linea_id}).then(({data}) => {
        this.productosOptions.push({label: '---Seleccione---', value: 0})
        if (data.productosOpt.length > 0) {
          data.productosOpt.forEach(producto => {
            this.productosOptions.push({label: producto.nombre, value: producto.id})
          })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    isNumber (evt, input) {
      switch (input) {
        case 'cantidad':
          this.form_bom.fields.cantidad = this.form_bom.fields.cantidad.replace(/[^0-9.]/g, '')
          this.$v.form_bom.fields.cantidad.$touch()
          break
        default:
          break
      }
    },
    guardarBom () {
      this.$v.form_bom.$touch()
      if (!this.$v.form_bom.$error) {
        let params = this.form_bom.fields
        this.saveBom(params).then(({data}) => {
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
            this.limpiarBom()
            this.getBom()
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    editarBom () {
      this.$v.form_bom.$touch()
      if (!this.$v.form_bom.$error) {
        let params = this.form_bom.fields
        this.updateBom(params).then(({data}) => {
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
            this.bom = false
            this.editar_bom = false
            this.getBom()
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    async getPresentacion () {
      await axios.get(`/productos/getPresentacionByProducto/${this.form_bom.fields.material_id}`).then(({data}) => {
        this.form_bom.fields.presentacion_id = data.productos[0].presentacion_id
        // this.form.loading = false
      }).catch(error => {
        console.error(error)
      })
    },
    limpiarBom () {
      this.$v.form_bom.$reset()
      // this.form_bom.fields.empresa_id = 0
      // this.form_bom.fields.almacen_id = 0
      this.form_bom.fields.categoria_id = 0
      this.form_bom.fields.linea_id = 0
      // this.form_bom.fields.producto_id = 0
      this.form_bom.fields.cantidad = 0
      this.form_bom.fields.presentacion_id = 0
      this.form_bom.fields.material_id = 0
    },
    editBom (row) {
      this.form_bom.fields.id = row.id
      this.form_bom.fields.categoria_id = row.categoria_id
      this.cargarLineasByCategoria2()
      this.form_bom.fields.linea_id = row.linea_id
      this.form_bom.fields.cantidad = row.cantidad
      this.form_bom.fields.presentacion_id = row.presentacion_id
      this.getProductosOpt()
      this.form_bom.fields.material_id = row.material_id
      this.bom = true
      this.editar_bom = true
    },
    deleteSingleBom (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este registro?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.borrarBom(id)
      }).catch(() => {})
    },
    borrarBom (id) {
      let params = {id: id}
      this.deleteBom(params).then(({data}) => {
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
          this.getBom()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    searchClavProd (terms, done) {
      this.getClaveProductos({ search: terms }).then(() => {
        done(this.claveProductoOptions)
      }).catch(error => {
        console.error(error)
      })
    }
  },
  validations: {
    form: {
      fields: {
        nombre: { required, maxLength: maxLength(100) },
        tipo_id: { required, minValue: minValue(1) },
        codigo: { required, minLength: minLength(1) },
        linea_id: { required, minValue: minValue(1) },
        presentacion_id: { required, minValue: minValue(1) }
      }
    },
    form_bom: {
      fields: {
        // empresa_id: {required, minValue: minValue(1)},
        // almacen_id: {required, minValue: minValue(1)},
        categoria_id: {required, minValue: minValue(1)},
        linea_id: {required, minValue: minValue(1)},
        producto_id: {required, minValue: minValue(1)},
        cantidad: {required, minValue: minValue(0.0001)},
        material_id: {required, minValue: minValue(1)}
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
</style>
