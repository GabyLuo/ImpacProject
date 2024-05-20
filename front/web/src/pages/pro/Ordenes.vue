<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Órdenes de producción"/>
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
                  :data="ordenes"
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
                      <q-td key="folio" :props="props">{{ props.row.folio }}</q-td>
                      <q-td key="fecha_produccion" :props="props">{{ props.row.fecha_produccion }}</q-td>
                      <q-td key="razon_social" :props="props">{{ props.row.razon_social }}</q-td>
                      <q-td key="empresa" :props="props">{{ props.row.empresa }}</q-td>
                      <q-td key="status" :props="props"><q-chip dense :icon="props.row.icon" :color="props.row.color" text-color="white">{{ props.row.status }}</q-chip></q-td>
                      <q-td key="acciones" style="text-align: left;" :props="props">
                        <q-btn small flat @click="editRow(props.row)" color="blue-6" icon="edit">
                          <q-tooltip>Editar</q-tooltip>
                        </q-btn>
                        <!-- <q-btn small flat @click="deleteSingleRow(props.row.id)" color="negative" icon="delete">
                          <q-tooltip>Eliminar</q-tooltip>
                        </q-btn> -->
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

    <div v-if="views.create">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Órdenes de producción" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el label="Nueva orden"/>
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
          <div class="col q-pa-md col-sm-12">
           <!--  <div class="row q-col-gutter-xs">
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
            </div> -->
          </div>
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="person" icon-color="dark" :error="$v.form.fields.tipo.$error" error-label="Seleccione un tipo de orden">
                  <q-select v-model="form.fields.tipo" inverted color="dark" float-label="Tipo" :options="ordenesOptions" filter @input="getFolioNuevo()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="style" icon-color="dark" :error="$v.form.fields.folio.$error" error-label="Ingrese un folio">
                  <q-input readonly upper-case v-model="form.fields.folio" type="text" inverted color="dark" float-label="Folio" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-3" v-if="form.fields.tipo !== 'EMPAQUETADO'">
                <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha_produccion.$error" error-label="Seleccione la fecha">
                  <q-datetime format="DD/MM/YYYY" v-model="form.fields.fecha_produccion" type="date" inverted color="dark" float-label="Fecha de producción" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-3" v-else>
                <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha_produccion.$error" error-label="Seleccione la fecha">
                  <q-datetime format="DD/MM/YYYY" v-model="form.fields.fecha_produccion" type="date" inverted color="dark" float-label="Fecha de empaquetado" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-info" icon-color="dark">
                  <q-input readonly upper-case v-model="form.fields.status" type="text" inverted color="dark" float-label="Status" maxlength="50" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="business" icon-color="dark" :error="$v.form.fields.empresa_id_origen.$error" error-label="Seleccione una empresa">
                  <q-select v-model="form.fields.empresa_id_origen" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter @input="cargaAlmacenes()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="store" icon-color="dark" :error="$v.form.fields.almacen_id_origen.$error" error-label="Seleccione un almacén">
                  <q-select readonly v-model="form.fields.almacen_id_origen" inverted color="dark" float-label="Almacén origen" :options="almacenesOptions" filter/>
                </q-field>
              </div>
              <!-- <div class="col-sm-3">
                <q-field icon="business" icon-color="dark" :error="$v.form.fields.empresa_id_destino.$error" error-label="Seleccione una empresa">
                  <q-select v-model="form.fields.empresa_id_destino" inverted color="dark" float-label="Empresa destino" :options="empresasOptions" filter @input="cargaAlmacenesDestino()"/>
                </q-field>
              </div> -->
              <div class="col-sm-3">
                <q-field icon="store" icon-color="dark" :error="$v.form.fields.almacen_id_destino.$error" error-label="Seleccione un almacén">
                  <q-select readonly v-model="form.fields.almacen_id_destino" inverted color="dark" float-label="Almacén destino" :options="almacenesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="person" icon-color="dark">
                  <q-select v-model="form.fields.cliente_id" inverted color="dark" float-label="Cliente" :options="clientesOptions" filter/>
                </q-field>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

      <!-- Modal editar TIPO DE MOVIMIENTO   -->
    <div v-if="views.update">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-5">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Órden de producción" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el :label="form.fields.folio" v-if="form.fields.tipo === 'PRODUCCIÓN'"/>
                <q-breadcrumbs-el :label="form.fields.folio" v-else/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-7 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn @click="atras()" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
              <q-btn @click="update()" class="btn_guardar" icon-right="save" :loading="loadingButton" v-if="this.form.fields.status === 'NUEVO'" style="margin-right: 5px;">Guardar</q-btn>
                  <q-btn @click="ejecutar()" color="amber-5" icon="fas fa-bolt" :loading="loadingButton" v-if="this.form.fields.status === 'NUEVO'"><q-tooltip class="bg-amber-5">Ejecutar</q-tooltip></q-btn>
                  <q-btn @click="cancelar()" class="bg-negative"   icon="highlight_off" :loading="loadingButton" v-if="this.form.fields.status === 'PRODUCIENDO'" style="margin-right: 5px;color: white;"><q-tooltip class="bg-negative">Cancelar</q-tooltip></q-btn>
                  <q-btn @click="finalizar()" class="bg-green" icon="done" :loading="loadingButton" v-if="this.form.fields.status === 'PRODUCIENDO'" style="margin-right: 5px;color: white;"><q-tooltip class="bg-green">Finalizar</q-tooltip></q-btn>
                  <q-btn @click="exportPDF()" color="red-14" icon="fas fa-file-pdf" class="red-14"><q-tooltip class="bg-red-14">GENERAR PDF</q-tooltip></q-btn>
            </div>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
<!--           <div class="col q-pa-md col-sm-12">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6 pull-left">
                <div class="col-xs-12 col-sm-12 col-md-12 pull-left" >
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
                <q-field icon="person" icon-color="dark" :error="$v.form.fields.tipo.$error" error-label="Seleccione un tipo de orden">
                  <q-select v-model="form.fields.tipo" inverted color="dark" :disable="getBlocked()" float-label="Tipo" :options="ordenesOptions" filter @input="getFolioNuevo()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="style" icon-color="dark" :error="$v.form.fields.folio.$error" error-label="Ingrese un folio">
                  <q-input readonly upper-case v-model="form.fields.folio" :disable="getBlocked()" type="text" inverted color="dark" float-label="Folio" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-3" v-if="form.fields.tipo !== 'EMPAQUETADO'">
                <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha_produccion.$error" error-label="Seleccione la fecha">
                  <q-datetime format="DD/MM/YYYY" :disable="getBlocked()" v-model="form.fields.fecha_produccion" type="date" inverted color="dark" float-label="Fecha de producción" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-3" v-else>
                <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha_produccion.$error" error-label="Seleccione la fecha">
                  <q-datetime format="DD/MM/YYYY" :disable="getBlocked()" v-model="form.fields.fecha_produccion" type="date" inverted color="dark" float-label="Fecha de empaquetado" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-info" icon-color="dark">
                  <q-input readonly upper-case v-model="form.fields.status" :disable="getBlocked()" type="text" inverted color="dark" float-label="Status" maxlength="50" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="person" icon-color="dark">
                  <q-select v-model="form.fields.cliente_id" inverted color="dark" :disable="getBlocked()" float-label="Cliente" :options="clientesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="business" icon-color="dark" :error="$v.form.fields.empresa_id_origen.$error" error-label="Seleccione una empresa">
                  <q-select v-model="form.fields.empresa_id_origen" inverted :disable="getBlocked()" color="dark" float-label="Empresa de origen" :options="empresasOptions" filter @input="cargaAlmacenes()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="store" icon-color="dark" :error="$v.form.fields.almacen_id_origen.$error" error-label="Seleccione un almacén">
                  <q-select v-model="form.fields.almacen_id_origen" :disable="getBlocked()" inverted color="dark" float-label="Almacén origen" :options="almacenesOptions" filter/>
                </q-field>
              </div>
              <!-- <div class="col-sm-3">
                <q-field icon="business" icon-color="dark" :error="$v.form.fields.empresa_id_destino.$error" error-label="Seleccione una empresa">
                  <q-select v-model="form.fields.empresa_id_destino" inverted color="dark" float-label="Empresa destino" :options="empresasOptions" filter @input="cargaAlmacenesDestino()"/>
                </q-field>
              </div> -->
              <div class="col-sm-3">
                <q-field icon="store" icon-color="dark" :error="$v.form.fields.almacen_id_destino.$error" error-label="Seleccione un almacén">
                  <q-select v-model="form.fields.almacen_id_destino" :disable="getBlocked()" inverted color="dark" float-label="Almacén destino" :options="almacenesOptions" filter/>
                </q-field>
              </div>
            </div>
            <q-tabs v-model="tab" class="shadow-1" inverted animated swipeable color="teal" align="justify" style="margin-top: 20px;">
              <q-tab name="detalles" slot="title" icon="person" label="DETALLES"/>
              <q-tab name="bom" slot="title" icon="school" label="BOM"/>

              <q-tab-pane name="detalles">
                <div class="row q-mt-lg">
                  <div class="col-sm-11 subtitulos pull-left">
                    Detalles de la orden
                  </div>
                  <div class="col-sm-1 pull-right">
                    <q-btn round dense small flat @click="crearDetalle()" style="border-radius: 15%;"  class="btn_guardar" icon="add" v-if="form.fields.status === 'NUEVO'">
                      <q-tooltip>Agregar</q-tooltip>
                    </q-btn>
                    <!-- <q-btn round dense small flat @click="getDetalles()" class="btn_guardar" icon="add">
                      <q-tooltip>Agregar</q-tooltip>
                    </q-btn> -->
                  </div>
                </div>
                <div class="row q-mt-sm" style="margin-top:10px;">
                  <div class="col-sm-12">
                    <q-search color="primary" v-model="form.filter"/>
                  </div>
                  <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
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
                          <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                          <q-td key="cantidad" :props="props">{{ props.row.cantidad }}</q-td>
                          <q-td key="acciones" :props="props">
                            <q-btn small flat @click="editDetalle(props.row)" color="blue-6" icon="edit" v-if="form.fields.status === 'NUEVO'">
                              <q-tooltip>Editar</q-tooltip>
                            </q-btn>
                            <q-btn small flat @click="deleteSingleDetalle(props.row.id)" color="negative" icon="delete" v-if="form.fields.status === 'NUEVO'">
                              <q-tooltip>Eliminar</q-tooltip>
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
              <q-tab-pane name="bom">
                <div class="col-sm-12 q-mt-sm" id="sticky-arbol">
                  <q-btn small flat @click="tipo_vista_detalles = 'detallado'" color="indigo" icon="fas fa-eye">&nbsp;Detalle
                  </q-btn>
                  <q-btn small flat @click="tipo_vista_detalles = 'compacto'" color="purple" icon="fas fa-eye">&nbsp;Compacto
                  </q-btn>
                  <div class="q-table-container q-table-dense" style="margin-top:20px;" v-if="tipo_vista_detalles === 'detallado'">
                    <div class="q-table-middle scroll">
                      <table class="q-table">
                        <thead>
                          <tr style="text-align: center;">
                            <th>Nombre</th>
                            <th>Cantidad a producir</th>
                            <th style="text-align: right;">Cantidad por pieza</th>
                            <th style="text-align: right;">Total requerido</th>
                            <th style="text-align: right;">Existencia</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(item ,index)  in (arrayTreeObj)" :key="index" v-bind:class="[(item.id != selectedRowID.id) ? 'my-label':'text-green bg-light-green-11','']">
                            <td data-th="Nombre" @click="toggle(item, index)" style="cursor: pointer;text-align:left;">
                              <span class="q-tree-link q-tree-label" v-bind:style="setPadding(item)">
                                <q-icon  style="cursor: pointer;font-size:15px;" :name="iconName(item)" :color="item.color"></q-icon>
                                {{item.nombre}}
                              </span>
                            </td>
                            <td data-th="Cantidad a producir" style="text-align: center; font-weight: bold;">{{item.cantidad_a_producir}}</td>
                            <td data-th="Cantidad por pieza" style="text-align: right;">{{item.cantidad_por_pieza}}</td>
                            <td data-th="Total" style="text-align: right;">{{item.total_necesario}} </td>
                            <td data-th="Existencia" style="text-align: right; color: red;" v-if="item.total_necesario > item.existencia">{{item.existencia}}</td>
                            <td data-th="Existencia" style="text-align: right;" v-else>{{item.existencia}}</td>
                          </tr>
                        </tbody>
                      </table>
                      <!-- <q-inner-loading visible="false">
                        <q-spinner-dots size="64px" color="primary" />
                      </q-inner-loading> -->
                    </div>
                  </div>
                  <div class="q-table-container q-table-dense" style="margin-top:20px;" v-else>
                    <div class="q-table-middle scroll">
                      <table class="q-table">
                        <thead>
                          <tr style="text-align: center;">
                            <th style="text-align: left;">Nombre</th>
                            <th style="text-align: right;">Total requerido</th>
                            <th style="text-align: right;">Existencia</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(item ,index)  in (arrayTreeObjCompacto)" :key="index" v-bind:class="[(item.id != selectedRowID.id) ? 'my-label':'text-black bg-white','']">
                            <td data-th="Nombre" @click="toggle(item, index)" style="cursor: pointer;text-align:left;">
                              <span class="q-tree-link q-tree-label" v-bind:style="setPadding(item)">
                                <q-icon  style="cursor: pointer;font-size:15px;" :name="iconName(item)" :color="item.color"></q-icon>
                                {{item.nombre}}
                              </span>
                            </td>
                            <td data-th="Total" style="text-align: right;">{{item.total_necesario}} </td>
                            <td data-th="Existencia" style="text-align: right; color: red;" v-if="item.total_necesario > item.existencia">{{item.existencia}}</td>
                            <td data-th="Existencia" style="text-align: right;" v-else>{{item.existencia}}</td>
                          </tr>
                        </tbody>
                      </table>
                      <!-- <q-inner-loading visible="false">
                        <q-spinner-dots size="64px" color="primary" />
                      </q-inner-loading> -->
                    </div>
                  </div>
                </div>
              </q-tab-pane>
            </q-tabs>

            <q-modal v-if="detalle_orden" style="background-color: rgba(0,0,0,0.7);" v-model="detalle_orden" :content-css="{width: '80vw', height: '35vh'}">
              <q-modal-layout>
                <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
                  <div class="col-sm-6">
                    <q-toolbar-title>
                      Agregar producto
                    </q-toolbar-title>
                  </div>
                  <div class="col-sm-6 pull-right">
                    <q-btn
                      flat
                      round
                      dense
                      color="white"
                      @click="detalle_orden = false"
                      icon="fas fa-times-circle"
                    />
                  </div>
                </q-toolbar>
                <div class="row q-mt-lg" style="margin-lef:10px;margin-right: 15px;">
                  <div class="col-sm-4">
                    <q-field icon="fas fa-tasks" icon-color="dark" :error="$v.form_detalles.fields.categoria_id.$error" error-label="Ingrese una categoría">
                      <q-select v-model="form_detalles.fields.categoria_id" inverted color="dark" float-label="Categoría" :options="tiposOptions" filter @input="cargarLineasByCategoria()"/>
                    </q-field>
                  </div>
                  <div class="col-sm-4">
                    <q-field icon="view_week" icon-color="dark" :error="$v.form_detalles.fields.linea_id.$error" error-label="Ingrese una línea">
                      <q-select v-model="form_detalles.fields.linea_id" inverted color="dark" float-label="Línea" :options="lineasOptions" filter @input="getProductosOpt()"/>
                    </q-field>
                  </div>
                  <div class="col-sm-4">
                    <q-field icon="fas fa-box" icon-color="dark" :error="$v.form_detalles.fields.producto_id.$error" error-label="Seleccione un producto">
                      <q-select v-model="form_detalles.fields.producto_id" inverted color="dark" float-label="Producto" :options="productosOptions" filter @input="getPresentacion()"/>
                    </q-field>
                  </div>
                </div>
                <div class="row q-mt-lg" style="margin-right: 15px;">
                  <div class="col-sm-4">
                    <q-field icon="fas fa-box" icon-color="dark">
                      <q-select readonly v-model="form_detalles.fields.presentacion_id" inverted color="dark" float-label="Unidad" :options="presentacionesOptions" filter/>
                    </q-field>
                  </div>
                  <div class="col-sm-4">
                    <q-field icon="label" icon-color="dark" :error="$v.form_detalles.fields.cantidad.$error" error-label="Ingrese una cantidad">
                      <q-input upper-case v-model="form_detalles.fields.cantidad" type="text" inverted color="dark" float-label="Cantidad" maxlength="50" @keyup="isNumber($event,'cantidad')"/>
                    </q-field>
                  </div>
                  <div class="col-sm-4 pull-right">
                    <q-btn @click="guardarDetalle()" class="btn_guardar" icon-right="save" :loading="loadingButton" v-if="editar_detalle === false">Guardar</q-btn>
                    <q-btn @click="editarDetalle()" class="btn_actualizar" icon-right="save" :loading="loadingButton" v-if="editar_detalle === true">Actualizar</q-btn>
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
import { required, /* maxLength, */ minValue, minLength } from 'vuelidate/lib/validators'
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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'INVENTARIOS'.toUpperCase()) {
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
      tipo_vista_detalles: 'detallado',
      detalle_orden: false,
      editar_detalle: false,
      tab: 'detalles',
      lineasOptions: [{label: '---Seleccione---', value: 0}],
      almacenesOptions: [{label: '---Seleccione---', value: 0}],
      almacenesOptionsDestino: [{label: '---Seleccione---', value: 0}],
      productosOptions: [{label: '---Seleccione---', value: 0}],
      ordenesOptions: [ {label: 'PRODUCCIÓN', value: 'PRODUCCIÓN'}, {label: 'EMPAQUETADO', value: 'EMPAQUETADO'}, {label: '--Seleccione--', value: 'all'} ],
      loadingButton: false,
      actividades: [],
      actividad: [],
      selectedRowID: {},
      isExpanded: true,
      itemId: null,
      views: {
        grid: true,
        create: false,
        update: false
      },
      form: {
        fields: {
          id: 0,
          cliente_id: 0,
          fecha_produccion: '',
          folio: '',
          status: '',
          almacen_id_origen: 0,
          almacen_id_destino: 0,
          empresa_id_origen: 0,
          empresa_id_destino: 0,
          tipo: ''
        },
        // data: [],
        columns: [
          { name: 'folio', label: 'Folio', field: 'folio', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha_produccion', label: 'Fecha producción', field: 'fecha_produccion', sortable: true, type: 'string', align: 'center' },
          { name: 'razon_social', label: 'Cliente', field: 'razon_social', sortable: true, type: 'string', align: 'left' },
          { name: 'empresa', label: 'Empresa', field: 'empresa', sortable: true, type: 'string', align: 'left' },
          { name: 'status', label: 'Status', field: 'status', sortable: true, type: 'string', align: 'left' },
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
        data_tree: [],
        data_tree_compacto: [],
        validacion_existencias: 'si',
        producto: '',
        fields: {
          id: 0,
          orden_id: 0,
          categoria_id: 0,
          linea_id: 0,
          producto_id: 0,
          presentacion_id: 0,
          cantidad: 0
        },
        // data: [],
        columns: [
          { name: 'nombre', label: 'Nombre', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'cantidad', label: 'Cantidad', field: 'cantidad', sortable: true, type: 'string', align: 'right' },
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
      ordenes: 'pro/ordenes/ordenes'
    }),
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
    },
    clientesOptions () {
      let clientes = this.$store.getters['ventas/clientes/selectOptions']
      clientes.splice(0, 0, {label: '---Seleccione---', value: 0})
      clientes.sort(function (a, b) {
        if (a.razon_social > b.razon_social) {
          return 1
        }
        if (a.razon_social < b.razon_social) {
          return -1
        }
        return 0
      })
      return clientes
    },
    tiposOptions () {
      let tipos = this.$store.getters['inv/tipos_articulos/selectOptions']
      tipos.push({label: '---Seleccione---', value: 0})
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
    arrayTreeObj () {
      let newObj = []
      this.recursive(this.form_detalles.data_tree, newObj, 0, this.itemId, this.isExpanded)
      return newObj
    },
    arrayTreeObjCompacto () {
      let newObjCompacto = []
      this.recursive(this.form_detalles.data_tree_compacto, newObjCompacto, 0, this.itemId, this.isExpanded)
      return newObjCompacto
    }
  },
  created () {
    this.loadAll()
  },
  watch: {
    async 'tab' (newValue, old) {
      if (newValue === 'bom') {
        this.getDetalles()
      }
    }
  },
  methods: {
    ...mapActions({
      getOrdenes: 'pro/ordenes/refresh',
      saveOrdenes: 'pro/ordenes/save',
      updateOrdenes: 'pro/ordenes/update',
      deleteOrdenes: 'pro/ordenes/delete',
      getEmpresas: 'vnt/empresa/refresh',
      getClientes: 'ventas/clientes/refresh',
      getFolioConsecutivo: 'pro/ordenes/getFolioConsecutivo',
      getAlmacenesByEmpresa: 'inv/almacenes/getByEmpresa',
      getDetalleByOrden: 'pro/ordenes_detalles/getByOrden',
      getTiposProductos: 'inv/tipos_articulos/refresh',
      getPresentaciones: 'inv/tipos_presentaciones/refresh',
      getLineasByCategoria: 'inv/lineas/getByCategoria',
      getProductosByLinea: 'inv/productos/getProductoByLinea',
      saveDetalle: 'pro/ordenes_detalles/save',
      updateDetalle: 'pro/ordenes_detalles/update',
      deleteDetalle: 'pro/ordenes_detalles/delete'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getOrdenes()
      await this.getEmpresas()
      await this.getClientes()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    getBlocked () {
      if (this.form.fields.status === 'FINALIZADO') {
        return true
      } else {
        return false
      }
    },
    save () {
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        this.saveOrdenes(params).then(({data}) => {
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
            this.form.fields.folio = data.folio
            this.form_detalles.fields.orden_id = data.id
            this.getDetalles()
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
        this.updateOrdenes(params).then(({data}) => {
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
            this.loadAll()
            this.setView('grid')
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
      if (row.cliente_id === null) {
        this.form.fields.cliente_id = 0
      } else {
        this.form.fields.cliente_id = row.cliente_id
      }
      this.form.fields.fecha_produccion = this.stringToDate(row.fecha_produccion, 'dd/mm/yyyy', '/')
      this.form.fields.folio = row.folio
      this.form.fields.status = row.status
      this.form.fields.empresa_id_origen = row.empresa_id_origen
      this.form.fields.empresa_id_destino = row.empresa_id_destino
      this.cargaAlmacenes()
      this.cargaAlmacenesDestino()
      this.form.fields.almacen_id_origen = row.almacen_id_origen
      this.form.fields.almacen_id_destino = row.almacen_id_destino
      this.form.fields.tipo = row.tipo
      this.form_detalles.fields.orden_id = row.id
      this.getDetalles()
      this.setView('update')
    },
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este tipo de artículo?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete(id)
      }).catch(() => {})
    },
    stringToDate (_date, _format, _delimiter) {
      var formatLowerCase = _format.toLowerCase()
      var formatItems = formatLowerCase.split(_delimiter)
      var dateItems = _date.split(_delimiter)
      var monthIndex = formatItems.indexOf('mm')
      var dayIndex = formatItems.indexOf('dd')
      var yearIndex = formatItems.indexOf('yyyy')
      var month = parseInt(dateItems[monthIndex])
      month -= 1
      var formatedDate = new Date(dateItems[yearIndex], month, dateItems[dayIndex])
      return formatedDate
    },
    delete (id) {
      let params = {id: id}
      this.deleteOrdenes(params).then(({data}) => {
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
      this.form.fields.cliente_id = 0
      this.form.fields.fecha_produccion = ''
      this.form.fields.folio = ''
      this.form.fields.status = 'NUEVO'
      this.form.fields.almacen_id_origen = 0
      this.form.fields.almacen_id_destino = 0
      this.form.fields.empresa_id_origen = 0
      this.form.fields.empresa_id_destino = 0
      this.form.fields.tipo = 'all'

      this.form_detalles.data = []
      this.form_detalles.data_tree = []
      this.form_detalles.data_tree_compacto = []

      this.getFolioNuevo()
      this.setView('create')
    },
    async cargaAlmacenes () {
      this.almacenesOptions = []
      this.form.fields.almacen_id_origen = 0
      this.form.fields.almacen_id_destino = 0
      await this.getAlmacenesByEmpresa({empresa_id: this.form.fields.empresa_id_origen}).then(({data}) => {
        this.almacenesOptions.push({label: '---Seleccione---', value: 0})
        if (data.almacenes.length > 0) {
          data.almacenes.forEach(almacen => {
            this.almacenesOptions.push({label: almacen.nombre, value: almacen.id})
          })
        }
        if (this.form.fields.tipo === 'PRODUCCIÓN') {
          this.form.fields.almacen_id_origen = data.materia_prima
          this.form.fields.almacen_id_destino = data.produccion
          if (this.form.fields.almacen_id_origen === 0 && this.form.fields.almacen_id_destino > 0) {
            this.$showMessage('Advertencia', 'No existe almacén de materia prima para esta empresa, seleccione otra o cree un almacén de este tipo.')
          }
          if (this.form.fields.almacen_id_origen > 0 && this.form.fields.almacen_id_destino === 0) {
            this.$showMessage('Advertencia', 'No existe almacén de producción para esta empresa, seleccione otra o cree un almacén de este tipo.')
          }
          if (this.form.fields.almacen_id_origen === 0 && this.form.fields.almacen_id_destino === 0) {
            this.$showMessage('Advertencia', 'No existen almacenes de materia prima y producción para esta empresa, seleccione otra o cree almacenes de este tipo.')
          }
        }
        if (this.form.fields.tipo === 'EMPAQUETADO') {
          this.form.fields.almacen_id_origen = data.producto_terminado
          this.form.fields.almacen_id_destino = data.producto_terminado
          if (this.form.fields.almacen_id_origen === 0) {
            this.$showMessage('Advertencia', 'No existe almacén de producto terminado para esta empresa, seleccione otra o cree un almacén de este tipo.')
          }
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async cargaAlmacenesDestino () {
      this.almacenesOptionsDestino = []
      this.form.fields.almacen_id_destino = 0
      await this.getAlmacenesByEmpresa({empresa_id: this.form.fields.empresa_id_destino}).then(({data}) => {
        this.almacenesOptionsDestino.push({label: '---Seleccione---', value: 0})
        if (data.almacenes.length > 0) {
          data.almacenes.forEach(almacen => {
            this.almacenesOptionsDestino.push({label: almacen.nombre, value: almacen.id})
          })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async getFolioNuevo () {
      if (this.form.fields.tipo !== 'all') {
        this.form.fields.folio = ''
        await this.getFolioConsecutivo({tipo: this.form.fields.tipo}).then(({data}) => {
          if (data.result === 'success') {
            this.form.fields.folio = data.folio
          }
        }).catch(error => {
          console.error(error)
        })
        if (this.form.fields.empresa_id_origen > 0) {
          await this.cargaAlmacenes()
        }
      }
    },
    isNumber (evt, input) {
      switch (input) {
        case 'telefono':
          this.form.fields.telefono = this.form.fields.telefono.replace(/[^0-9.]/g, '')
          // this.$v.form.fields.telefono.$touch()
          break
        case 'numero_exterior':
          this.form.fields.numero_exterior = this.form.fields.numero_exterior.replace(/[^0-9.]/g, '')
          this.$v.form.fields.numero_exterior.$touch()
          break
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
    async crearDetalle () {
      this.getDetalles()
      this.editar_detalle = false
      this.limpiarDetalle()
      // this.getTiposProductos()
      this.getPresentaciones()
      // this.cargarLineasByCategoria()
      this.getTiposProductos()
      this.detalle_orden = true
    },
    limpiarDetalle () {
      this.$v.form_detalles.$reset()
      this.form_detalles.fields.categoria_id = 0
      this.form_detalles.fields.linea_id = 0
      this.form_detalles.fields.cantidad = 0
      this.form_detalles.fields.presentacion_id = 0
      this.form_detalles.fields.producto_id = 0
    },
    async getDetalles () {
      this.form_detalles.data = []
      await this.getDetalleByOrden({orden_id: this.form.fields.id, almacen_origen: this.form.fields.almacen_id_origen}).then(({data}) => {
        if (data.detalles.length > 0) {
          this.form_detalles.data = data.detalles
          this.form_detalles.data_tree = data.detalles_orden
          this.form_detalles.data_tree_compacto = data.detalles_compacto
          this.form_detalles.validacion_existencias = data.cantidad_suficiente
          this.form_detalles.producto = data.producto
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async cargarLineasByCategoria () {
      this.lineasOptions = []
      this.form.fields.linea_id = 0
      await this.getLineasByCategoria({categoria_id: this.form_detalles.fields.categoria_id}).then(({data}) => {
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
    async getProductosOpt () {
      this.productosOptions = []
      this.form_detalles.fields.producto_id = 0
      await this.getProductosByLinea({linea: this.form_detalles.fields.linea_id}).then(({data}) => {
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
    async getPresentacion () {
      // this.getPresentaciones()
      await axios.get(`/productos/getPresentacionByProducto/${this.form_detalles.fields.producto_id}`).then(({data}) => {
        this.form_detalles.fields.presentacion_id = data.productos[0].presentacion_id
        // this.form.loading = false
      }).catch(error => {
        console.error(error)
      })
    },
    guardarDetalle () {
      this.$v.form_detalles.$touch()
      if (!this.$v.form_detalles.$error) {
        let params = this.form_detalles.fields
        this.saveDetalle(params).then(({data}) => {
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
            this.limpiarDetalle()
            this.getDetalles()
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
    editDetalle (row) {
      this.form_detalles.fields.id = row.id
      this.form_detalles.fields.categoria_id = row.categoria_id
      this.cargarLineasByCategoria()
      this.form_detalles.fields.linea_id = row.linea_id
      this.form_detalles.fields.cantidad = row.cantidad
      this.getProductosOpt()
      this.form_detalles.fields.producto_id = row.producto_id
      this.getPresentacion()
      this.form_detalles.fields.presentacion_id = row.presentacion_id
      this.editar_detalle = true
      this.detalle_orden = true
    },
    editarDetalle () {
      this.$v.form_detalles.$touch()
      if (!this.$v.form_detalles.$error) {
        let params = this.form_detalles.fields
        this.updateDetalle(params).then(({data}) => {
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
            this.editar_detalle = false
            this.limpiarDetalle()
            this.getDetalles()
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
    deleteSingleDetalle (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este registro?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.borrarDetalle(id)
      }).catch(() => {})
    },
    borrarDetalle (id) {
      let params = {id: id}
      this.deleteDetalle(params).then(({data}) => {
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
          this.getDetalles()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    recursive (obj, newObj, level, itemId, isExpend) {
      let vm = this
      obj.forEach(function (o) {
        if (o.bom && o.bom.length !== 0) {
          o.level = level
          newObj.push(o)
          if (o.id === itemId) {
            o.expend = isExpend
          }
          if (o.expend === true) {
            vm.recursive(o.bom, newObj, o.level + 1, itemId, isExpend)
          }
        } else {
          o.level = level
          newObj.push(o)
          return false
        }
      })
    },
    iconName (item) {
      if (item.expend === true && item.bom && item.bom.length > 0) {
        return 'fas fa-minus-circle'
      }

      if (item.expend === false && item.bom && item.bom.length > 0) {
        return 'fas fa-plus-circle'
      }

      return 'fas fa-check'
    },
    toggle (item, index) {
      let vm = this
      vm.itemId = item.id
      // show  sub items after click on + (more)
      if (item.expend === false && item.bom !== undefined) {
        if (item.bom.length !== 0) {
          vm.recursive(item.bom, [], item.level + 1, item.id, true)
        }
      }

      if (item.expend === true && item.bom !== undefined) {
        item.bom.forEach(function (o) {
          o.expend = false
        })

        // this.item.expend = undefined
        vm.$set(item, 'expend', false)
        vm.itemId = null
      }
    },
    setPadding (item) {
      return `padding-left: ${item.level * 20}px;`
    },
    ejecutar () {
      this.getDetalles()
      if (this.form_detalles.validacion_existencias === 'si') {
        this.form.fields.status = 'PRODUCIENDO'
        this.update()
      } else {
        this.$showMessage('Error', 'La cantidad de ' + this.form_detalles.producto + ' es insuficiente, no se ejecutará la orden')
      }
    },
    cancelar () {
      this.form.fields.status = 'CANCELADO'
      this.update()
    },
    finalizar () {
      this.form.fields.status = 'FINALIZADO'
      this.update()
    },
    atras () {
      this.loadAll()
      this.setView('grid')
    },
    exportPDF () {
      let uri
      uri = process.env.API + `reportesPDF/exportPDF_orden/${this.form.fields.id}`
      window.open(uri, '_blank')
    }
  },
  validations: {
    form: {
      fields: {
        fecha_produccion: {required},
        folio: {required},
        almacen_id_origen: {required, minValue: minValue(1)},
        almacen_id_destino: {required, minValue: minValue(1)},
        empresa_id_origen: {required, minValue: minValue(1)},
        tipo: {required, minLength: minLength(6)} // ,
        // empresa_id_destino: {required, minValue: minValue(1)}
      }
    },
    form_detalles: {
      fields: {
        categoria_id: {required, minValue: minValue(1)},
        linea_id: {required, minValue: minValue(1)},
        producto_id: {required, minValue: minValue(1)},
        cantidad: {required, minValue: minValue(1)}
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
</style>
