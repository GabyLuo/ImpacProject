<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-5">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Inventario"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-7 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn @click="newRow()" class="btn_pagar" icon="add">
                <q-tooltip class="bg-green">Entrada</q-tooltip>
              </q-btn>
              <q-btn @click="newRowSalida()" class="btn_rechazar" icon="arrow_forward" style="margin-left: 10px;">
                <q-tooltip class="bg-deep-orange">Salida</q-tooltip>
              </q-btn>
              <q-btn @click="newInventario()" class="btn_solicitar" icon="add" style="margin-left: 10px;">
                <q-tooltip class="bg-light-blue">Inventario inicial</q-tooltip>
              </q-btn>
              <q-btn @click="newTraspaso()" class="btn_actualizar" icon="arrow_forward" style="margin-left: 10px;">
                <q-tooltip class="bg-orange-4">Traspaso</q-tooltip>
              </q-btn>
            </div>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md" style="padding: 0;">
            <div class="col q-pa-md ">
              <div class="col-sm-12" style="padding-bottom: 10px;">
                <q-search color="primary" v-model="form.filter"/>
              </div>
              <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                <q-table id="sticky-table-newstyle"
                  :data="movimientos"
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
                      <q-td key="created" :props="props">{{ props.row.created }}</q-td>
                      <q-td key="movimiento" :props="props">{{ props.row.movimiento }}</q-td>
                      <q-td key="empresa" :props="props">{{ props.row.empresa }}</q-td>
                      <q-td key="almacen" :props="props">{{ props.row.almacen }}</q-td>
                      <q-td key="proveedor" :props="props">{{ props.row.proveedor }}</q-td>
                      <q-td key="recepcion" :props="props">{{ props.row.recepcion }}</q-td>
                      <q-td key="status" :props="props"><q-chip style="border-radius: 0;" dense :icon="props.row.icon" :color="props.row.color" text-color="white">{{ props.row.status }}</q-chip></q-td>
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

    <div v-if="views.create">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Inventario" to="" @click.native="atras()"/>
                <q-breadcrumbs-el label="Nuevo"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
                <div class="q-pa-sm q-gutter-sm">
                  <q-btn @click="atras()" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
                  <q-btn @click="save()" class="btn_guardar" icon-right="save" :loading="loadingButton"/>
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
              <div class="col-sm-2">
                <q-field icon="swap_horiz" icon-color="dark" :error="$v.form.fields.tipo_id.$error" error-label="Seleccione un movimiento">
                  <q-select readonly v-model="form.fields.tipo_id" inverted color="dark" float-label="Tipo de movimiento" :options="movimientosOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="business" icon-color="dark">
                  <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter @input="cargaAlmacenes()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="store" icon-color="dark" :error="$v.form.fields.almacen_id.$error" error-label="Seleccione un almacén">
                  <q-select v-model="form.fields.almacen_id" inverted color="dark" float-label="Almacén" :options="almacenesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="style" icon-color="dark" :error="$v.form.fields.folio.$error" error-label="Ingrese una folio">
                  <q-input readonly upper-case v-model="form.fields.folio" type="text" inverted color="dark" float-label="Folio" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="fas fa-info" icon-color="dark">
                  <q-input readonly upper-case v-model="form.fields.status" type="text" inverted color="dark" float-label="Status" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha.$error" error-label="Seleccione la fecha">
                  <q-datetime v-model="form.fields.fecha" type="date" inverted color="dark" float-label="Fecha" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="forward" icon-color="dark"  error-label="Seleccione un destino">
                  <q-select v-model="form.fields.destino" inverted color="dark" float-label="Destino" :options="destinosOptions" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="list" icon-color="dark" error-label="Escriba Observacion">
                  <q-input v-model="form.fields.observaciones" filled type="text" inverted color="dark"  float-label="Observaciones" />
                </q-field>
              </div>
              <div class="col-sm-6" v-if="form.fields.tipo_id === 1">
                <q-field icon="person" icon-color="dark"  error-label="Seleccione un proveedor">
                  <q-select v-model="form.fields.proveedor_id" inverted color="dark" float-label="Proveedor" :options="proveedoresOptions" />
                </q-field>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

      <!-- Modal editar ALMACÉN   -->
    <div v-if="views.update">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Inventario" to="" @click.native="atras()"/>
                <q-breadcrumbs-el :label="form.fields.folio"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn @click="atras()" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 5px;"/>
              <q-btn @click="update()" class="btn_guardar" icon-right="save" :loading="loadingButton" style="margin-right: 5px;" v-if="form.fields.status === 'NUEVO'">Guardar</q-btn>
                  <q-btn @click="ejecutar()" class="btn_solicitar" icon-right="save" :loading="loadingButton" v-if="form.fields.status === 'NUEVO' && form_detalles.data.length > 0" style="margin-right: 5px;">Ejecutar</q-btn>
                  <q-btn @click="cancelar()" class="btn_rechazar" icon-right="save" :loading="loadingButton" v-if="form.fields.status === 'EJECUTADO'" style="margin-right: 5px;">Cancelar</q-btn>
                  <q-btn @click="exportPDF()" color="green" icon-right="fas fa-file-pdf" v-if="form.fields.status === 'EJECUTADO'">GENERAR PDF</q-btn>
                </div>
            </div>
          </div>
        </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md col-sm-12">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6 pull-left">
                <div class="col-xs-12 col-sm-12 col-md-12 pull-left" >
                  <!-- <q-btn @click="atras()" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/> -->
                </div>
              </div>
              <div class="col-sm-6 pull-right">
                <div class="col-xs-12 col-sm-12 col-md-12 pull-right">
                  <!-- <q-btn @click="update()" class="btn_guardar" icon-right="save" :loading="loadingButton" style="margin-right: 5px;" v-if="form.fields.status === 'NUEVO'">Guardar</q-btn>
                  <q-btn @click="ejecutar()" class="btn_solicitar" icon-right="save" :loading="loadingButton" v-if="form.fields.status === 'NUEVO' && form_detalles.data.length > 0" style="margin-right: 5px;">Ejecutar</q-btn>
                  <q-btn @click="cancelar()" class="btn_rechazar" icon-right="save" :loading="loadingButton" v-if="form.fields.status === 'EJECUTADO'" style="margin-right: 5px;">Cancelar</q-btn>
                  <q-btn @click="exportPDF()" color="green" icon-right="fas fa-file-pdf" v-if="form.fields.status === 'EJECUTADO'">GENERAR PDF</q-btn> -->
                </div>
              </div>
            </div>
          </div>
          <div class="col q-pa-md">
            <div class="row q-pa-md">
              Información del movimiento
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-2">
                <q-field icon="swap_horiz" icon-color="dark" :error="$v.form.fields.tipo_id.$error" error-label="Seleccione un movimiento">
                  <q-select readonly v-model="form.fields.tipo_id" inverted color="dark" float-label="Tipo de movimiento" :options="movimientosOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="business" icon-color="dark">
                  <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="store" icon-color="dark" :error="$v.form.fields.almacen_id.$error" error-label="Seleccione un almacén">
                  <q-select v-model="form.fields.almacen_id" inverted color="dark" float-label="Almacén" :options="almacenesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="style" icon-color="dark" :error="$v.form.fields.folio.$error" error-label="Ingrese una folio">
                  <q-input readonly upper-case v-model="form.fields.folio" type="text" inverted color="dark" float-label="Folio" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="fas fa-info" icon-color="dark">
                  <q-input readonly upper-case v-model="form.fields.status" type="text" inverted color="dark" float-label="Status" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha.$error" error-label="Seleccione la fecha">
                  <q-datetime v-model="form.fields.fecha" type="date" inverted color="dark" float-label="Fecha" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="forward" icon-color="dark"  error-label="Seleccione un destino">
                  <q-select v-model="form.fields.destino" inverted color="dark" float-label="Destino" :options="destinosOptions" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="list" icon-color="dark" error-label="Escriba Observacion">
                  <q-input v-model="form.fields.observaciones" filled type="text" inverted color="dark"  float-label="Observaciones" />
                </q-field>
              </div>
              <div class="col-sm-6" v-if="form.fields.tipo_id === 1">
                <q-field icon="person" icon-color="dark"  error-label="Seleccione un proveedor">
                  <q-select v-model="form.fields.proveedor_id" inverted color="dark" float-label="Proveedor" :options="proveedoresOptions" />
                </q-field>
              </div>
            </div>
            <div class="row q-pa-md" v-if="form_recepciones.fields.existe_recepcion === 'SI'">
              Información de la recepción
            </div>
            <div class="row q-col-gutter-xs" v-if="form_recepciones.fields.existe_recepcion === 'SI'">
              <div class="col-sm-6">
                <q-field icon="business" icon-color="dark">
                  <q-input readonly v-model="form_recepciones.fields.razon_social" filled type="text" inverted color="dark" float-label="Observaciones" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="style" icon-color="dark">
                  <q-input readonly v-model="form_recepciones.fields.folio_recepcion" filled type="text" inverted color="dark" float-label="Folio recepción" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-user" icon-color="dark">
                  <q-input readonly v-model="form_recepciones.fields.nickname" filled type="text" inverted color="dark" float-label="Recibe" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="style" icon-color="dark">
                  <q-input readonly v-model="form_recepciones.fields.folio_compra" filled type="text" inverted color="dark" float-label="Folio compra" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-dollar-sign" icon-color="dark">
                  <q-input readonly v-model="form_recepciones.fields.subtotal" filled type="text" inverted color="dark" float-label="Subtotal" suffix="MXN"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-dollar-sign" icon-color="dark">
                  <q-input readonly v-model="form_recepciones.fields.total" filled type="text" inverted color="dark" float-label="Total" suffix="MXN"/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs" v-if="form_detalles.fields.movimiento_id > 0">
              <div class="row q-mt-lg subtitulos">
                Detalles del movimiento
              </div>
            </div>
            <div class="row q-col-gutter-xs" v-if="form_detalles.fields.movimiento_id > 0 && form.fields.status === 'NUEVO'">
              <div class="col-sm-3">
                <q-field icon="fas fa-box" icon-color="dark" :error="$v.form_detalles.fields.producto_id.$error" error-label="Seleccione un producto">
                  <q-select v-model="form_detalles.fields.producto_id" inverted color="dark" float-label="Producto" :options="productosOptions" filter @input="getPresentacion()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-box" icon-color="dark">
                  <q-select readonly v-model="form_detalles.fields.presentacion_id" inverted color="dark" float-label="Unidad" :options="presentacionesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="style" icon-color="dark" :error="$v.form_detalles.fields.cantidad.$error" error-label="Ingrese una cantidad">
                  <q-input upper-case v-model="form_detalles.fields.cantidad" type="text" inverted color="dark" float-label="Cantidad" maxlength="50" @keyup="isNumber($event,'cantidad')"/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form_detalles.fields.costo.$error" error-label="Ingrese un costo">
                  <q-input upper-case v-model="form_detalles.fields.costo" type="text" inverted color="dark" float-label="Costo" maxlength="50" suffix="MXN" @keyup="isNumber($event,'costo')"/>
                </q-field>
              </div>
              <div class="col-sm-2 pull-right">
                <q-btn @click="saveMovimientosDetalles()" class="btn_guardar" icon-right="add" :loading="loadingButton" v-if="this.form_detalles.editar === false && form.fields.status === 'NUEVO'">Agregar</q-btn>
                <q-btn @click="updateMovimientosDetalles()" class="btn_actualizar" icon-right="save" :loading="loadingButton" v-if="this.form_detalles.editar === true && form.fields.status === 'NUEVO'">Actualizar</q-btn>
                <!-- <q-btn @click="getDetallesByMovimiento()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardarrrrrr</q-btn> -->
              </div>
            </div>
            <div class="row q-mt-sm" style="margin-top:10px;" v-if="form_detalles.fields.movimiento_id > 0">
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
                      <q-td key="acciones" :props="props">
                        <q-btn small flat @click="editRowDetalle(props.row)" color="blue-6" icon="edit" v-if="form.fields.status === 'NUEVO'">
                          <q-tooltip>Editar</q-tooltip>
                        </q-btn>
                        <q-btn small flat @click="deleteSingleRowDetalle(props.row.id)" color="negative" icon="delete" v-if="form.fields.status === 'NUEVO'">
                          <q-tooltip>Eliminar</q-tooltip>
                        </q-btn>
                        <q-btn small flat color="cyan" icon="fas fa-check" v-if="form.fields.status === 'EJECUTADO'">
                          <q-tooltip>Ejecutado</q-tooltip>
                        </q-btn>
                        <q-btn small flat color="red-10" icon="highlight_off" v-if="form.fields.status === 'CANCELADO'">
                          <q-tooltip>Cancelado</q-tooltip>
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
          </div>
        </div>
      </div>
    </div>

    <div v-if="views.createSalida">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Inventario" to="" @click.native="atras()"/>
                <q-breadcrumbs-el label="Nuevo"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
               <q-btn @click="atras()" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
               <q-btn @click="save()" class="btn_guardar" icon-right="save" :loading="loadingButton"/>
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
                </div>
              </div>
              <div class="col-sm-6 pull-right">
                <div class="col-xs-12 col-sm-12 col-md-12 pull-right">
                </div>
              </div>
            </div>
          </div> -->
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs" style="padding-top: 20px;">
              <div class="col-sm-2">
                <q-field icon="swap_horiz" icon-color="dark" :error="$v.form.fields.tipo_id.$error" error-label="Seleccione un movimiento">
                  <q-select readonly v-model="form.fields.tipo_id" inverted color="dark" float-label="Tipo de movimiento" :options="movimientosOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="business" icon-color="dark">
                  <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter @input="cargaAlmacenes()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="store" icon-color="dark" :error="$v.form.fields.almacen_id.$error" error-label="Seleccione un almacén">
                  <q-select v-model="form.fields.almacen_id" inverted color="dark" float-label="Almacén" :options="almacenesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="style" icon-color="dark" :error="$v.form.fields.folio.$error" error-label="Ingrese una folio">
                  <q-input upper-case v-model="form.fields.folio" type="text" inverted color="dark" float-label="Folio" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="fas fa-info" icon-color="dark">
                  <q-input readonly upper-case v-model="form.fields.status" type="text" inverted color="dark" float-label="Status" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha.$error" error-label="Seleccione la fecha">
                  <q-datetime v-model="form.fields.fecha" type="date" inverted color="dark" float-label="Fecha" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="forward" icon-color="dark"  error-label="Seleccione un destino">
                  <q-select v-model="form.fields.destino" inverted color="dark" float-label="Destino" :options="destinosOptions" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="list" icon-color="dark" error-label="Escriba Observacion">
             <q-input v-model="form.fields.observaciones" filled type="text" inverted color="dark"  float-label="Observaciones" />
           </q-field>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="views.updateSalida">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Inventario" to="" @click.native="atras()"/>
                <q-breadcrumbs-el :label="form.fields.folio"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
                <div class="q-pa-sm q-gutter-sm">
                  <q-btn @click="atras()" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
                </div>
              </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md col-sm-12">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6 pull-left">
                <div class="col-xs-12 col-sm-12 col-md-12 pull-left" >
                 <!--  <q-btn @click="atras()" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/> -->
                </div>
              </div>
              <div class="col-sm-6 pull-right">
                <div class="col-xs-12 col-sm-12 col-md-12 pull-right">
                  <q-btn @click="update()" class="btn_guardar" icon-right="save" :loading="loadingButton" style="margin-right: 5px;" v-if="form.fields.status === 'NUEVO'">Guardar</q-btn>
                  <q-btn @click="ejecutar()" class="btn_solicitar" icon-right="save" :loading="loadingButton" v-if="form.fields.status === 'NUEVO' && form_detalles.data.length > 0" style="margin-right: 5px;">Ejecutar</q-btn>
                  <q-btn @click="cancelar()" class="btn_rechazar" icon-right="save" :loading="loadingButton" v-if="form.fields.status === 'EJECUTADO'" style="margin-right: 5px;">Cancelar</q-btn>
                  <q-btn @click="exportPDF()" color="green" icon-right="fas fa-file-pdf" v-if="form.fields.status === 'EJECUTADO'">GENERAR PDF</q-btn>
                </div>
              </div>
            </div>
          </div>
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs" style="padding-top: 20px;">
              <div class="col-sm-2">
                <q-field icon="swap_horiz" icon-color="dark" :error="$v.form.fields.tipo_id.$error" error-label="Seleccione un movimiento">
                  <q-select readonly v-model="form.fields.tipo_id" inverted color="dark" float-label="Tipo de movimiento" :options="movimientosOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="business" icon-color="dark">
                  <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="store" icon-color="dark" :error="$v.form.fields.almacen_id.$error" error-label="Seleccione un almacén">
                  <q-select v-model="form.fields.almacen_id" inverted color="dark" float-label="Almacén" :options="almacenesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="style" icon-color="dark" :error="$v.form.fields.folio.$error" error-label="Ingrese una folio">
                  <q-input upper-case v-model="form.fields.folio" type="text" inverted color="dark" float-label="Folio" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="fas fa-info" icon-color="dark">
                  <q-input readonly upper-case v-model="form.fields.status" type="text" inverted color="dark" float-label="Status" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha.$error" error-label="Seleccione la fecha">
                  <q-datetime v-model="form.fields.fecha" type="date" inverted color="dark" float-label="Fecha" align="center"></q-datetime>
                </q-field>
              </div>
             <div class="col-sm-4">
                <q-field icon="forward" icon-color="dark"  error-label="Seleccione un destino">
                  <q-select v-model="form.fields.destino" inverted color="dark" float-label="Destino" :options="destinosOptions" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="list" icon-color="dark" error-label="Escriba Observacion">
             <q-input v-model="form.fields.observaciones" filled type="text" inverted color="dark"  float-label="Observaciones" />
           </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs" v-if="form_detalles.fields.movimiento_id > 0">
              <div class="row q-mt-lg subtitulos">
                Detalles del movimiento
              </div>
            </div>
            <div class="row q-col-gutter-xs" v-if="form_detalles.fields.movimiento_id > 0 && form.fields.status === 'NUEVO'">
              <div class="col-sm-3">
                <q-field icon="fas fa-box" icon-color="dark" :error="$v.form_detalles.fields.producto_id.$error" error-label="Seleccione un producto">
                  <q-select v-model="form_detalles.fields.producto_id" inverted color="dark" float-label="Producto" :options="productosOptions" filter @input="getPresentacion()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-box" icon-color="dark">
                  <q-select readonly v-model="form_detalles.fields.presentacion_id" inverted color="dark" float-label="Unidad" :options="presentacionesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="style" icon-color="dark" :error="$v.form_detalles.fields.cantidad.$error" error-label="Ingrese una cantidad">
                  <q-input upper-case v-model="form_detalles.fields.cantidad" type="text" inverted color="dark" float-label="Cantidad" maxlength="50" @keyup="isNumber($event,'cantidad')"/>
                </q-field>
              </div>
              <div class="col-sm-3 pull-right">
                <q-btn @click="saveMovimientosDetalles()" class="btn_guardar" icon-right="add" :loading="loadingButton" v-if="this.form_detalles.editar === false">Agregar</q-btn>
                <q-btn @click="updateMovimientosDetalles()" class="btn_actualizar" icon-right="save" :loading="loadingButton" v-if="this.form_detalles.editar === true">Actualizar</q-btn>
                <!-- <q-btn @click="getDetallesByMovimiento()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardarrrrrr</q-btn> -->
              </div>
            </div>
            <div class="row q-mt-sm" style="margin-top:10px;" v-if="form_detalles.fields.movimiento_id > 0">
              <div class="col-sm-12">
                <q-search color="primary" v-model="form_detalles.filter" class="col-12" />
              </div>
              <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                <q-table id="sticky-table-newstyle"
                  :data="form_detalles.data"
                  :columns="form_detalles.columnsSalida"
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
                      <q-td key="acciones" :props="props">
                        <q-btn small flat @click="editRowDetalle(props.row)" color="blue-6" icon="edit" v-if="form.fields.status === 'NUEVO'">
                          <q-tooltip>Editar</q-tooltip>
                        </q-btn>
                        <q-btn small flat @click="deleteSingleRowDetalle(props.row.id)" color="negative" icon="delete" v-if="form.fields.status === 'NUEVO'">
                          <q-tooltip>Eliminar</q-tooltip>
                        </q-btn>
                        <q-btn small flat color="cyan" icon="fas fa-check" v-if="form.fields.status === 'EJECUTADO'">
                          <q-tooltip>Ejecutado</q-tooltip>
                        </q-btn>
                        <q-btn small flat color="red-10" icon="highlight_off" v-if="form.fields.status === 'CANCELADO'">
                          <q-tooltip>Cancelado</q-tooltip>
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
          </div>
        </div>
      </div>
    </div>

    <div v-if="views.createTraspaso">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Inventario" to="" @click.native="atras()"/>
                <q-breadcrumbs-el label="Nuevo"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn @click="atras()" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
              <q-btn @click="save()" class="btn_guardar" icon-right="save" :loading="loadingButton"/>
            </div></div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <!-- <div class="col q-pa-md col-sm-12">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6 pull-left">
                <div class="col-xs-12 col-sm-12 col-md-12 pull-left" >
                </div>
              </div>
              <div class="col-sm-6 pull-right">
                <div class="col-xs-12 col-sm-12 col-md-12 pull-right">
                </div>
              </div>
            </div>
          </div> -->
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs" style="padding-top: 20px;">
              <div class="col-sm-3">
                <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha.$error" error-label="Seleccione la fecha">
                  <q-datetime v-model="form.fields.fecha" type="date" inverted color="dark" float-label="Fecha" align="center"></q-datetime>
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
                <q-field icon="swap_horiz" icon-color="dark" :error="$v.form.fields.tipo_id.$error" error-label="Seleccione un movimiento">
                  <q-select readonly v-model="form.fields.tipo_id" inverted color="dark" float-label="Tipo de movimiento" :options="movimientosOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="business" icon-color="dark">
                  <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter @input="cargaAlmacenes()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="store" icon-color="dark" :error="$v.form.fields.almacen_id.$error" error-label="Seleccione un almacén">
                  <q-select v-model="form.fields.almacen_id" inverted color="dark" float-label="Almacén origen" :options="almacenesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="style" icon-color="dark" :error="$v.form.fields.folio.$error" error-label="Ingrese un folio">
                  <q-input readonly upper-case v-model="form.fields.folio" type="text" inverted color="dark" float-label="Folio" maxlength="50" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="swap_horiz" icon-color="dark">
                  <q-select readonly v-model="form.fields.tipo_id_2" inverted color="dark" float-label="Tipo de movimiento" :options="movimientosOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="business" icon-color="dark">
                  <q-select v-model="form.fields.empresa_id_2" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter @input="cargaAlmacenesDestino()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="store" icon-color="dark" :error="$v.form.fields.almacen_id_2.$error" error-label="Seleccione un almacén">
                  <q-select v-model="form.fields.almacen_id_2" inverted color="dark" float-label="Almacén destino" :options="almacenesOptionsDestino" filter/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="style" icon-color="dark" :error="$v.form.fields.folio.$error" error-label="Ingrese un folio">
                  <q-input readonly upper-case v-model="form.fields.folio" type="text" inverted color="dark" float-label="Folio" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="forward" icon-color="dark"  error-label="Seleccione un destino">
                  <q-select v-model="form.fields.destino" inverted color="dark" float-label="Destino" :options="destinosOptions" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="list" icon-color="dark" error-label="Escriba Observacion">
             <q-input v-model="form.fields.observaciones" filled type="text" inverted color="dark"  float-label="Observaciones" />
           </q-field>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="views.updateTraspaso">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Inventario" to="" @click.native="atras()"/>
                <q-breadcrumbs-el :label="form.fields.folio"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn @click="update()" class="btn_guardar" icon-right="save" :loading="loadingButton" style="margin-right: 5px;" v-if="form.fields.status === 'NUEVO'">Guardar</q-btn>
                  <q-btn @click="ejecutar()" class="btn_solicitar" icon-right="save" :loading="loadingButton" v-if="form.fields.status === 'NUEVO' && form_detalles.data.length > 0" style="margin-right: 5px;">Ejecutar</q-btn>
                  <q-btn @click="cancelar()" class="btn_rechazar" icon-right="save" :loading="loadingButton" v-if="form.fields.status === 'EJECUTADO'" style="margin-right: 5px;">Cancelar</q-btn>
                  <q-btn @click="exportPDF()" color="green" icon-right="fas fa-file-pdf" v-if="form.fields.status === 'EJECUTADO'">GENERAR PDF</q-btn>
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
                <div class="col-xs-12 col-sm-12 col-md-12 pull-right">
                </div>
              </div>
            </div>
          </div> -->
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs" style="padding-top: 20px;">
              <div class="col-sm-2">
                <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha.$error" error-label="Seleccione la fecha">
                  <q-datetime v-model="form.fields.fecha" type="date" inverted color="dark" float-label="Fecha" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="fas fa-info" icon-color="dark">
                  <q-input readonly upper-case v-model="form.fields.status" type="text" inverted color="dark" float-label="Status" maxlength="50" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="swap_horiz" icon-color="dark" :error="$v.form.fields.tipo_id.$error" error-label="Seleccione un movimiento">
                  <q-select readonly v-model="form.fields.tipo_id" inverted color="dark" float-label="Tipo de movimiento" :options="movimientosOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="business" icon-color="dark">
                  <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter @input="cargaAlmacenes()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="store" icon-color="dark" :error="$v.form.fields.almacen_id.$error" error-label="Seleccione un almacén">
                  <q-select v-model="form.fields.almacen_id" inverted color="dark" float-label="Almacén origen" :options="almacenesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="style" icon-color="dark" :error="$v.form.fields.folio.$error" error-label="Ingrese un folio">
                  <q-input readonly upper-case v-model="form.fields.folio" type="text" inverted color="dark" float-label="Folio" maxlength="50" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="swap_horiz" icon-color="dark">
                  <q-select readonly v-model="form.fields.tipo_id_2" inverted color="dark" float-label="Tipo de movimiento" :options="movimientosOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="business" icon-color="dark">
                  <q-select v-model="form.fields.empresa_id_2" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter @input="cargaAlmacenesDestino()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="store" icon-color="dark" :error="$v.form.fields.almacen_id_2.$error" error-label="Seleccione un almacén">
                  <q-select v-model="form.fields.almacen_id_2" inverted color="dark" float-label="Almacén destino" :options="almacenesOptionsDestino" filter/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="style" icon-color="dark">
                  <q-input readonly upper-case v-model="form.fields.folio_2" type="text" inverted color="dark" float-label="Folio" maxlength="50" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs" v-if="form_detalles.fields.movimiento_id > 0">
              <div class="row q-mt-lg subtitulos" style="padding-bottom:10px;">
                Detalles del movimiento
              </div>
            </div>
            <div class="row q-col-gutter-xs" v-if="form_detalles.fields.movimiento_id > 0 && form.fields.status === 'NUEVO'">
              <div class="col-sm-3">
                <q-field icon="fas fa-box" icon-color="dark" :error="$v.form_detalles.fields.producto_id.$error" error-label="Seleccione un producto">
                  <q-select v-model="form_detalles.fields.producto_id" inverted color="dark" float-label="Producto" :options="productosOptions" filter @input="getPresentacion()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-box" icon-color="dark">
                  <q-select readonly v-model="form_detalles.fields.presentacion_id" inverted color="dark" float-label="Unidad" :options="presentacionesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="style" icon-color="dark" :error="$v.form_detalles.fields.cantidad.$error" error-label="Ingrese una cantidad">
                  <q-input upper-case v-model="form_detalles.fields.cantidad" type="text" inverted color="dark" float-label="Cantidad" maxlength="50" @keyup="isNumber($event,'cantidad')"/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form_detalles.fields.costo.$error" error-label="Ingrese un costo">
                  <q-input upper-case v-model="form_detalles.fields.costo" type="text" inverted color="dark" float-label="Costo" maxlength="50" suffix="MXN" @keyup="isNumber($event,'costo')"/>
                </q-field>
              </div>
              <div class="col-sm-2 pull-right">
                <q-btn @click="saveMovimientosDetalles()" class="btn_guardar" icon-right="add" :loading="loadingButton" v-if="this.form_detalles.editar === false">Agregar</q-btn>
                <q-btn @click="updateMovimientosDetalles()" class="btn_actualizar" icon-right="save" :loading="loadingButton" v-if="this.form_detalles.editar === true">Actualizar</q-btn>
                <!-- <q-btn @click="getDetallesByMovimiento()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardarrrrrr</q-btn> -->
              </div>
            </div>
            <div class="row q-col-gutter-xs" style="margin-top:10px;" v-if="form_detalles.fields.movimiento_id > 0">
              <div class="row" style="width:60vw;">
                <q-search hide-underline color="secondary" v-model="form_detalles.filter" class="col-12" />
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
                      <q-td key="costo" :props="props">{{ props.row.costo }}</q-td>
                      <q-td key="importe" :props="props">{{ props.row.importe }}</q-td>
                      <q-td key="acciones" :props="props">
                        <q-btn small flat @click="editRowDetalle(props.row)" color="blue-6" icon="edit" v-if="form.fields.status === 'NUEVO'">
                          <q-tooltip>Editar</q-tooltip>
                        </q-btn>
                        <q-btn small flat @click="deleteSingleRowDetalle(props.row.id)" color="negative" icon="delete" v-if="form.fields.status === 'NUEVO'">
                          <q-tooltip>Eliminar</q-tooltip>
                        </q-btn>
                        <q-btn small flat color="cyan" icon="fas fa-check" v-if="form.fields.status === 'EJECUTADO'">
                          <q-tooltip>Ejecutado</q-tooltip>
                        </q-btn>
                        <q-btn small flat color="red-10" icon="highlight_off" v-if="form.fields.status === 'CANCELADO'">
                          <q-tooltip>Cancelado</q-tooltip>
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
          </div>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { required, maxLength, minLength, minValue } from 'vuelidate/lib/validators'
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
      almacenesOptionsDestino: [{label: '---Seleccione---', value: 0}],
      loadingButton: false,
      views: {
        grid: true,
        create: false,
        update: false,
        createSalida: false,
        updateSalida: false,
        createTraspaso: false,
        updateTraspaso: false
      },
      form: {
        fields: {
          id: 0,
          id2: 0,
          tipo_id: 0,
          empresa_id: 0,
          almacen_id: 0,
          tipo_id_2: 0,
          empresa_id_2: 0,
          almacen_id_2: 0,
          folio: '',
          folio_2: '',
          status: '',
          fecha: '',
          observaciones: '',
          destino: 0,
          proveedor_id: 0
        },
        // data: [],
        columns: [
          { name: 'folio', label: 'Folio', field: 'folio', sortable: true, type: 'string', align: 'left' },
          { name: 'created', label: 'Creado', field: 'created', sortable: true, type: 'string', align: 'left' },
          { name: 'movimiento', label: 'Movimiento', field: 'movimiento', sortable: true, type: 'string', align: 'left' },
          { name: 'empresa', label: 'Empresa', field: 'empresa', sortable: true, type: 'string', align: 'left' },
          { name: 'almacen', label: 'Almacén', field: 'almacen', sortable: true, type: 'string', align: 'left' },
          { name: 'proveedor', label: 'Proveedor', field: 'proveedor', sortable: true, type: 'string', align: 'left' },
          { name: 'recepcion', label: 'Recepción', field: 'recepcion', sortable: true, type: 'string', align: 'left' },
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
        editar: false,
        fields: {
          id: 0,
          producto_id: 0,
          presentacion_id: 0,
          cantidad: 0,
          costo: 0,
          movimiento_id: 0
        },
        // data: [],
        columns: [
          { name: 'producto', label: 'Producto', field: 'producto', sortable: true, type: 'string', align: 'left' },
          { name: 'cantidad', label: 'Cantidad', field: 'cantidad', sortable: true, type: 'string', align: 'right' },
          { name: 'costo', label: 'Costo', field: 'costo', sortable: true, type: 'string', align: 'right' },
          { name: 'importe', label: 'Importe', field: 'importe', sortable: true, type: 'string', align: 'right' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        columnsSalida: [
          { name: 'producto', label: 'Producto', field: 'producto', sortable: true, type: 'string', align: 'left' },
          { name: 'cantidad', label: 'Cantidad', field: 'cantidad', sortable: true, type: 'string', align: 'right' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_recepciones: {
        fields: {
          existe_recepcion: '',
          razon_social: '',
          folio_recepcion: '',
          nickname: '',
          folio_compra: '',
          subtotal: '',
          total: ''
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
      movimientos: 'inv/movimientos/movimientos'
    }),
    movimientosOptions () {
      let tipos = this.$store.getters['inv/tipos_movimientos/selectOptions']
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
    empresasOptions () {
      let empresas = this.$store.getters['vnt/empresa/selectOptions']
      empresas.push({label: '---Seleccione---', value: 0})
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
    destinosOptions () {
      let destinos = this.$store.getters['crm/destinos/selectOptions']
      destinos.splice(0, 0, {label: '---Seleccione---', value: 0})
      destinos.sort(function (a, b) {
        if (a.nombre_destino > b.nombre_destino) {
          return 1
        }
        if (a.nombre_destino < b.nombre_destino) {
          return -1
        }
        return 0
      })
      return destinos
    },
    proveedoresOptions () {
      let proveedor = this.$store.getters['pmo/proveedor/selectOptions']
      proveedor.splice(0, 0, {label: '---Seleccione---', value: 0})
      proveedor.sort(function (a, b) {
        if (a.nombre_comercial > b.nombre_comercial) {
          return 1
        }
        if (a.nombre_comercial < b.nombre_comercial) {
          return -1
        }
        return 0
      })
      return proveedor
    }
  },
  created () {
    this.loadAll()
  },
  methods: {
    ...mapActions({
      getMovimientos: 'inv/movimientos/refresh',
      saveMovimientos: 'inv/movimientos/save',
      updateMovimientos: 'inv/movimientos/update',
      deleteMovimientos: 'inv/movimientos/delete',
      getEmpresas: 'vnt/empresa/refresh',
      getProveedores: 'pmo/proveedor/refresh',
      getTiposMovimientos: 'inv/tipos_movimientos/refresh',
      getAlmacenesByEmpresa: 'inv/almacenes/getByEmpresa',
      getProductos: 'inv/productos/refresh',
      getPresentaciones: 'inv/tipos_presentaciones/refresh',
      getDetalles: 'inv/movimientos_detalles/getByMovimiento',
      saveDetalles: 'inv/movimientos_detalles/save',
      updateDetalles: 'inv/movimientos_detalles/update',
      deleteDetalles: 'inv/movimientos_detalles/delete',
      getFolioConsecutivo: 'inv/movimientos/getFolioConsecutivo',
      getDestinos: 'crm/destinos/refresh'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getEmpresas()
      await this.getProveedores()
      await this.getMovimientos()
      await this.getTiposMovimientos()
      await this.getProductos()
      await this.getPresentaciones()
      await this.cargaAlmacenesDestino()
      await this.cargaAlmacenes()
      await this.getDestinos()
      await this.cargaAlmacenesDestino()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views.createSalida = false
      this.views.updateSalida = false
      this.views.createTraspaso = false
      this.views.updateTraspaso = false
      this.views[view] = true
    },
    async cargaAlmacenes () {
      this.almacenesOptions = []
      this.form.fields.almacen_id = 0
      await this.getAlmacenesByEmpresa({empresa_id: this.form.fields.empresa_id}).then(({data}) => {
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
    async cargaAlmacenesDestino () {
      this.almacenesOptionsDestino = []
      this.form.fields.almacen_id_2 = 0
      await this.getAlmacenesByEmpresa({empresa_id: this.form.fields.empresa_id_2}).then(({data}) => {
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
    async save () {
      if (this.form.fields.tipo === 1 || this.form.fields.tipo === 2 || this.form.fields.tipo === 3) {
        this.form.fields.empresa_id_2 = this.form.fields.empresa_id
      }
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        if (this.form.fields.tipo_id === 5 && this.form.fields.almacen_id === this.form.fields.almacen_id_2) {
          this.$showMessage('¡Advertencia!', 'Seleccione un almacén de destino diferente al de origen.')
          this.loadingButton = false
        }
        if (this.form.fields.tipo_id !== 10) {
          let params = this.form.fields
          this.saveMovimientos(params).then(({data}) => {
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
              if (this.form.fields.tipo_id === 1 || this.form.fields.tipo_id === 3) {
                this.form_detalles.fields.costo = 0
                this.setView('update')
                console.log('editar')
              }
              if (this.form.fields.tipo_id === 2) {
                this.form_detalles.fields.costo = 1
                this.setView('updateSalida')
                console.log('editarSalida')
              }
              if (this.form.fields.tipo_id === 5) {
                this.form_detalles.fields.costo = 1
                this.setView('updateTraspaso')
                console.log('editarTraspaso')
              }
              this.form_detalles.fields.movimiento_id = data.id
              this.form.fields.id = data.id
              if (this.form.fields.tipo_id === 5) {
                this.form.fields.id2 = data.id2
                this.form.fields.folio_2 = data.folio_2
                this.form.fields.almacen_id_2 = data.almacen_id_2
                this.form.fields.tipo_id_2 = data.tipo_id_2
              }
              this.getDetallesByMovimiento()
              this.form.fields.folio = data.folio
              this.loadingButton = false
            } else {
              this.$showMessage(data.message.title, data.message.content)
            }
          }).catch(error => {
            console.error(error)
          })
        }
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
        this.loadingButton = false
      }
    },
    update () {
      if (this.form.fields.tipo === 1 || this.form.fields.tipo === 2 || this.form.fields.tipo === 3) {
        this.form.fields.empresa_id_2 = this.form.fields.empresa_id
      }
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        this.updateMovimientos(params).then(({data}) => {
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
          } else {
            this.$showMessage(data.message.title, data.message.content)
            this.form.fields.status = 'NUEVO'
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
        this.form.fields.status = 'NUEVO'
      }
    },
    async editRow (row) {
      console.log(row)
      this.form.fields.id = row.id
      this.form.fields.empresa_id = row.empresa_id
      this.form.fields.destino = row.destino
      this.form.fields.observaciones = row.observaciones
      this.form.fields.folio = row.folio
      this.form.fields.tipo_id = row.tipo_id
      if (row.proveedor_id === null) {
        this.form.fields.proveedor_id = 0
      } else {
        this.form.fields.proveedor_id = row.proveedor_id
      }
      if (this.form.fields.tipo_id > 0) {
        this.cargaAlmacenes()
      }
      this.form.fields.almacen_id = row.almacen_id
      this.form.fields.status = row.status
      this.form.fields.fecha = row.created
      //
      this.form_detalles.fields.id = 0
      this.form_detalles.fields.producto_id = 0
      this.form_detalles.fields.presentacion_id = 0
      this.form_detalles.fields.cantidad = 0
      this.form_detalles.fields.costo = 0
      this.form_detalles.fields.movimiento_id = row.id
      this.form_detalles.editar = false

      this.form_recepciones.fields.existe_recepcion = row.existe_recepcion
      if (row.existe_recepcion !== 'NO') {
        this.form_recepciones.fields.existe_recepcion = row.existe_recepcion
        this.form_recepciones.fields.razon_social = row.razon_social
        this.form_recepciones.fields.folio_recepcion = row.folio_recepcion
        this.form_recepciones.fields.nickname = row.nickname
        this.form_recepciones.fields.folio_compra = row.folio_compra
        this.form_recepciones.fields.subtotal = row.subtotal
        this.form_recepciones.fields.total = row.total
      }

      await this.getDetallesByMovimiento()
      //
      this.$v.form_detalles.$reset()
      if (this.form.fields.tipo_id === 1 || this.form.fields.tipo_id === 3) {
        this.form.fields.empresa_id_2 = 1
        this.form.fields.tipo_id_2 = 1
        this.form.fields.almacen_id_2 = 1
        this.form_detalles.fields.costo = 0
        this.setView('update')
        console.log('editar')
      }
      if (this.form.fields.tipo_id === 2) {
        this.form.fields.empresa_id_2 = 1
        this.form.fields.tipo_id_2 = 1
        this.form.fields.almacen_id_2 = 1
        this.form_detalles.fields.costo = 1
        this.setView('updateSalida')
        console.log('editarSalida')
      }
      if (this.form.fields.tipo_id === 5) {
        this.form.fields.empresa_id_2 = row.empresa_id_2
        this.cargaAlmacenesDestino()
        this.form.fields.folio_2 = row.folio_2
        this.form.fields.almacen_id_2 = row.almacen_id_2
        this.form.fields.tipo_id_2 = row.tipo_id_2
        this.form.fields.id2 = row.id2
        this.setView('updateTraspaso')
      }
    },
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este movimiento?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete(id)
      }).catch(() => {})
    },
    delete (id) {
      let params = {id: id}
      this.deleteMovimientos(params).then(({data}) => {
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
    async getFolioNuevo () {
      this.form.fields.folio = ''
      await this.getFolioConsecutivo({tipo_movimiento: this.form.fields.tipo_id}).then(({data}) => {
        if (data.result === 'success') {
          this.form.fields.folio = data.folio
        }
      }).catch(error => {
        console.error(error)
      })
    },
    newRow () {
      this.limpiar()
      this.form.fields.tipo_id = 1
      this.form_detalles.fields.costo = 0
      this.form.fields.empresa_id_2 = 1
      this.form.fields.tipo_id_2 = 1
      this.form.fields.almacen_id_2 = 1
      this.form.fields.destino = 0
      this.form.fields.observaciones = ''
      this.getFolioNuevo()
      this.setView('create')
    },
    newRowSalida () {
      this.limpiar()
      this.form.fields.tipo_id = 2
      this.form_detalles.fields.costo = 1
      this.form.fields.empresa_id_2 = 1
      this.form.fields.tipo_id_2 = 1
      this.form.fields.almacen_id_2 = 1
      this.form.fields.destino = 0
      this.form.fields.observaciones = ''
      this.getFolioNuevo()
      this.setView('createSalida')
    },
    newInventario () {
      this.limpiar()
      this.form.fields.tipo_id = 3
      this.form_detalles.fields.costo = 0
      this.form.fields.empresa_id_2 = 1
      this.form.fields.tipo_id_2 = 1
      this.form.fields.almacen_id_2 = 1
      this.form.fields.destino = 0
      this.form.fields.observaciones = ''
      this.getFolioNuevo()
      this.setView('create')
    },
    newTraspaso () {
      this.limpiar()
      this.form.fields.tipo_id_2 = 4
      this.form.fields.empresa_id_2 = 0
      this.form.fields.almacen_id_2 = 0
      this.form.fields.tipo_id = 5
      this.form.fields.destino = 0
      this.form.fields.observaciones = ''
      this.getFolioNuevo()
      this.setView('createTraspaso')
    },
    async atras () {
      this.form.fields.status = ''
      await this.getMovimientos()
      this.setView('grid')
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
      await this.getDetalles({movimiento_id: this.form_detalles.fields.movimiento_id}).then(({data}) => {
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
      this.form_detalles.fields.movimiento_id = this.form.fields.id
      this.form_detalles.editar = true
      //
      this.$v.form_detalles.$reset()
      console.log(this.form_detalles.fields.costo)
    },
    limpiar () {
      this.$v.form.$reset()
      this.form.fields.id = 0
      this.form.fields.empresa_id = 0
      this.form.fields.folio = ''
      this.form.fields.almacen_id = 0
      this.form.fields.status = 'NUEVO'
      this.form.fields.fecha = ''
      this.form.fields.proveedor_id = 0
      // this.almacenesOptions = []
      // this.almacenesOptions.push({label: '---Seleccione---', value: 0})
      //
      this.form_detalles.fields.id = 0
      this.form_detalles.fields.producto_id = 0
      this.form_detalles.fields.presentacion_id = 0
      this.form_detalles.fields.cantidad = 0
      this.form_detalles.fields.movimiento_id = 0
      this.form_detalles.editar = false
      //
    },
    async limpiarDetalles () {
      //
      this.form_detalles.fields.id = 0
      this.form_detalles.fields.producto_id = 0
      this.form_detalles.fields.presentacion_id = 0
      this.form_detalles.fields.cantidad = 0
      this.form_detalles.fields.costo = 0
      this.form_detalles.fields.movimiento_id = this.form.fields.id
      this.form_detalles.editar = false
      //
      if (this.form.fields.tipo_id === 1) {
        this.form_detalles.fields.costo = 0
      } else {
        this.form_detalles.fields.costo = 1
      }
      this.$v.form_detalles.$reset()
    },
    deleteSingleRowDetalle (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este detalle de movimiento?',
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
    ejecutar () {
      this.form.fields.status = 'EJECUTADO'
      this.update()
    },
    cancelar () {
      this.form.fields.status = 'CANCELADO'
      this.update()
    },
    async getPresentacion () {
      await axios.get(`/productos/getPresentacionByProducto/${this.form_detalles.fields.producto_id}`).then(({data}) => {
        this.form_detalles.fields.presentacion_id = data.productos[0].presentacion_id
        // this.form.loading = false
      }).catch(error => {
        console.error(error)
      })
    },
    exportPDF () {
      let uri
      uri = process.env.API + `reportesPDF/exportPDF_movimiento/${this.form.fields.id}`
      window.open(uri, '_blank')
    }
  },
  validations: {
    form: {
      fields: {
        tipo_id: { required, minValue: minValue(1) },
        almacen_id: { required, minValue: minValue(1) },
        tipo_id_2: { required, minValue: minValue(1) },
        almacen_id_2: { required, minValue: minValue(1) },
        folio: {required, minLength: minLength(1), maxLength: maxLength(20)},
        fecha: {required}
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

<style lang="stylus" scoped>
</style>
