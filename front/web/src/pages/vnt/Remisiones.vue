<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Remisiones" />
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right" style="margin-top: 10px;">
            <q-btn @click="newRow()" class="btn_pagar" icon="add">Nueva</q-btn>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md" style="padding: 0;">
            <div class="row q-mt-sm q-pa-md">
              <div class="col-sm-3">
                <q-field icon="fas fa-building" icon-color="dark">
                  <q-select v-model="form_filtros.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-user" icon-color="dark">
                  <q-select v-model="form_filtros.fields.cliente_id" inverted color="dark" float-label="Cliente" :options="clientesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-info" icon-color="dark">
                  <q-select v-model="form_filtros.fields.status" inverted color="dark" float-label="Status" :options="statusCobranzaOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3 pull-right">
                <q-btn @click="filtrar()" inverted color="orange" icon-right="fas fa-filter" :loading="loadingButton">Filtrar</q-btn>
                <q-btn @click="borrar()" color="red" icon="loop"><q-tooltip>Limpiar</q-tooltip></q-btn>
              </div>
            </div>
            <div class="col q-pa-md " style="padding: 0;">
              <div class="col q-pa-md">
              <div class="col-sm-12">
                <q-search color="primary" v-model="form.filter"/>
              </div>
              <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                <q-table id="sticky-table-newstyle"
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
                      <q-td key="folio" :props="props">{{ props.row.folio }}</q-td>
                      <q-td key="folio_interno" :props="props">{{ props.row.folio_interno }}</q-td>
                      <q-td key="folio_fiscal" :props="props">{{ props.row.folio_fiscal }}</q-td>
                      <q-td key="empresa" :props="props">{{ props.row.empresa }}</q-td>
                      <q-td key="cliente" :props="props">{{ props.row.cliente }}</q-td>
                      <q-td key="created" :props="props">{{ props.row.created }}</q-td>
                      <q-td key="fecha_venta" :props="props">{{ props.row.fecha_venta }}</q-td>
                      <q-td key="status_cobranza" :props="props"><q-chip style="border-radius: 0.25rem;" dense :icon="props.row.icon" :color="props.row.color" text-color="white">{{ props.row.status_cobranza }}</q-chip></q-td>
                      <q-td key="status" :props="props"><q-chip style="border-radius: 0.25rem;" dense :icon="statusIcon[props.row.status]" :color="statusColor[props.row.status]" text-color="white">{{ props.row.status.toUpperCase()  }}</q-chip></q-td>
                      <q-td key="status_timbrado" :props="props"><q-chip style="border-radius: 0.25rem;" dense :icon="iconTimbrado[props.row.status_timbrado]" :color="colorTimbrado[props.row.status_timbrado]" text-color="white">{{ statusTimbrado[props.row.status_timbrado].toUpperCase() }}</q-chip></q-td>
                      <q-td key="tipo" :props="props">{{ props.row.tipo }}</q-td>
                      <q-td key="monto_total" style="text-align: right;" :props="props">${{ props.row.monto_total }}</q-td>
                      <q-td key="cancelado" :props="props"><q-checkbox v-model="props.row.cancelado" color="green-10" @click.native="cancelRemision(props.row)"/></q-td>
                      <q-td key="acciones"  style="text-align: left;"  :props="props">
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
    </div>

    <!--Crear un municipio-->

    <div v-if="views.create">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Remisiones" to="" @click.native="atras()"/>
                <q-breadcrumbs-el label="Nueva remisión"/><label style="padding-top: 10px;">: {{ form.fields.folio }} </label>
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
              <div class="col-sm-4">
                <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fields.empresa_id.$error" error-label="Elija una empresa">
                  <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="fas fa-user" icon-color="dark" :error="$v.form.fields.cliente_id.$error" error-label="Elija un cliente">
                  <q-select v-model="form.fields.cliente_id" inverted color="dark" float-label="Cliente" :options="clientesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha_venta.$error" error-label="Seleccione la fecha">
                  <q-datetime v-model="form.fields.fecha_venta" type="date" inverted color="dark" float-label="Fecha de venta" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="fas fa-user" icon-color="dark">
                  <q-select v-model="form.fields.tipo" inverted color="dark" float-label="Tipo" :options="tipoOptions" filter @input="getFolioNuevo()"/>
                </q-field>
              </div>
              <div class="col-sm-4" v-if="form.fields.tipo === 'HISTÓRICO'">
                <q-field icon="description" icon-color="dark" :error="$v.form_fiscal.fields.metodo_pago.$error" error-label="Por favor seleccione un método de pago">
                  <q-select v-model="form_fiscal.fields.metodo_pago" inverted color="dark" float-label="Método de pago" :options="metodoPagoOptions" filter/>
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
                <q-breadcrumbs-el label="Remisiones" to="" @click.native="atras()"/>
                <q-breadcrumbs-el label="Modificar remisión"/><label style="padding-top: 10px;">: {{ form.fields.folio }} </label>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn @click="atras()" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
              <q-btn v-if="detalles.length > 0 && form.fields.status === 'NUEVO'" @click="cambiarStatus('REMISIONADO')" class="btn_guardar pull-right" icon-right="fas fa-truck" :loading="loadingButton">Remisionar</q-btn>
              <q-btn v-if="!isDone && !isChecking" @click="update()" class="btn_actualizar pull-right" icon-right="save" :loading="loadingButton">Actualizar</q-btn>
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
                  <q-btn v-if="detalles.length > 0 && form.fields.status === 'NUEVO'" @click="cambiarStatus('REMISIONADO')" class="btn_guardar pull-right" icon-right="fas fa-truck" :loading="loadingButton">Remisionar</q-btn>
                  <q-btn v-if="!isDone && !isChecking" @click="update()" class="btn_actualizar pull-right" icon-right="save" :loading="loadingButton">Actualizar</q-btn>
                </div>
              </div>
            </div>
          </div> -->
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fields.empresa_id.$error" error-label="Elija una empresa">
                  <q-select :readonly="isRemisionado" v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-user" icon-color="dark" :error="$v.form.fields.cliente_id.$error" error-label="Elija un cliente">
                  <q-select :readonly="isRemisionado" v-model="form.fields.cliente_id" inverted color="dark" float-label="Cliente" :options="clientesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha_venta.$error" error-label="Seleccione la fecha">
                  <q-datetime :readonly="isRemisionado" v-model="form.fields.fecha_venta" type="date" inverted color="dark" float-label="Fecha de venta" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="fas fa-info" icon-color="dark">
                  <q-input readonly upper-case v-model="form.fields.status" type="text" inverted color="dark" float-label="Status" maxlength="50"><q-tooltip v-if="form.fields.status_timbrado === 7">{{form.fields.message_cancelacion}}</q-tooltip></q-input>
                </q-field>
                <!-- <q-btn v-if="form.fields.status_timbrado === 0" size="15px" icon="" disable class="btn_categoria">{{ form.fields.status }}</q-btn>
                <q-btn v-if="form.fields.status_timbrado !== 0" size="15px" :icon-right="iconTimbrado[form.fields.status_timbrado]" disable :color="colorTimbrado[form.fields.status_timbrado]">{{ statusTimbrado[form.fields.status_timbrado] }}<q-tooltip v-if="form.fields.status_timbrado === 6">{{form.fields.message}}</q-tooltip><q-tooltip v-if="form.fields.status_timbrado === 7">{{form.fields.message_cancelacion}}</q-tooltip></q-btn> -->
              </div>
              <div class="col-sm-2">
                <q-field icon="fas fa-user" icon-color="dark">
                  <q-select v-model="form.fields.tipo" inverted color="dark" float-label="Tipo" :options="tipoOptions" filter @input="getFolioNuevo()"/>
                </q-field>
              </div>
              <div class="col-sm-3" v-if="form.fields.tipo === 'HISTÓRICO'">
                <q-field icon="description" icon-color="dark" :error="$v.form_fiscal.fields.metodo_pago.$error" error-label="Por favor seleccione un método de pago">
                  <q-select v-model="form_fiscal.fields.metodo_pago" inverted color="dark" float-label="Método de pago" :options="metodoPagoOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-5" v-if="form.fields.tipo === 'HISTÓRICO'">
                <q-field icon="description" icon-color="dark">
                  <q-input upper-case inverted color="dark" float-label="Folio fiscal" v-model="form.fields.folio_fiscal" readonly></q-input>
                </q-field>
              </div>
              <div class="col-sm-2" v-if="form.fields.tipo === 'HISTÓRICO'">
                <q-field icon="description" icon-color="dark">
                  <q-input upper-case inverted color="dark" float-label="Folio interno" v-model="form.fields.folio_interno" readonly></q-input>
                </q-field>
              </div>
            </div>
            <br/><br/><br/>
            <q-tabs v-model="tabPrincipal" class="shadow-1" inverted animated swipeable color="teal" align="justify">
              <q-tab default name="generales" slot="title" icon="fas fa-box" label="Detalles generales" v-if="form.fields.tipo === 'VENTA'"/>
              <q-tab v-if="form.fields.status === 'REMISIONADO' && form.fields.tipo === 'VENTA'" name="fiscales" slot="title" icon="fas fa-file" label="Detalles fiscales"/>
              <q-tab v-if="isDone && form_fiscal.fields.metodo_pago === 'PPD' && form.fields.tipo === 'VENTA'" name="pagos" slot="title" icon="fas fa-file" label="Pagos"/>
              <q-tab name="facturas" slot="title" icon="fas fa-donate" label="Facturas" v-if="form.fields.tipo === 'HISTÓRICO'"/>

              <q-tab-pane name="generales" v-if="form.fields.tipo === 'VENTA'">
                <div class="row q-mt-lg" v-if="!isDone && !isChecking">
                  <div class="col-sm-8 pull-left">
                    <h6 style="margin: 0; font-weight: bold">Agregar productos</h6>
                  </div>
                  <div v-if="form_productos.typeButton === 'save'" class="col-sm-4 pull-right">
                    <q-btn @click="addProduct()" class="btn_guardar pull-right" icon-right="add" :loading="loadingButton">Agregar</q-btn>
                  </div>
                  <div v-if="form_productos.typeButton === 'update'" class="col-sm-4 pull-right">
                    <q-btn @click="actualizarProducto()" class="btn_actualizar pull-right" icon-right="save" :loading="loadingButton">Actualizar</q-btn>
                  </div>
                  <!-- <q-btn @click="getDetalles({ remision_id: form_productos.fields.remision_id })" class="btn_guardar pull-right" icon-right="add" :loading="loadingButton">Agregar</q-btn> -->
                </div>
                <div class="row q-mt-lg" v-if="!isDone && !isChecking">
                  <div class="col-sm-3">
                    <q-field icon="fas fa-box" icon-color="dark" :error="$v.form_productos.fields.producto_id.$error" error-label="Elija un producto">
                      <q-select ref="select_product" :readonly="form_productos.typeButton === 'update'" v-model="form_productos.fields.producto_id" inverted color="dark" float-label="Producto" :options="productosOptions" @input="onSelectedProduct()" filter/>
                    </q-field>
                  </div>
                  <div class="col-sm-2">
                    <q-field icon="fas fa-hashtag" icon-color="dark" :error="$v.form_productos.fields.cantidad.$error" error-label="Ingrese una cantidad">
                      <q-input @keyup="isNumber($event,'cantidad')" v-model="form_productos.fields.cantidad" inverted color="dark" float-label="Cantidad" maxlength="100"/>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="fas fa-info" icon-color="dark">
                      <q-input v-model="form_productos.fields.descripcion" inverted color="dark" float-label="Descripcion" maxlength="100"/>
                    </q-field>
                  </div>
                  <div class="col-sm-1 pull-right">
                    <q-checkbox v-model="form_productos.fields.impuesto"/> Agregar IVA
                  </div>
                </div>
                <div class="row q-mt-lg">
                  <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                    <q-table id="sticky-table-newstyle"
                      :data="detalles"
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
                          <q-td key="producto_nombre" :props="props">{{ props.row.producto_nombre }}</q-td>
                          <q-td key="descripcion" :props="props">{{ props.row.descripcion }}</q-td>
                          <q-td key="tipo_unidad" :props="props">{{ props.row.tipo_unidad }}</q-td>
                          <q-td key="cantidad" :props="props">{{ props.row.cantidad }}</q-td>
                          <q-td key="precio_unitario" :props="props">{{ props.row.precio_unitario }}</q-td>
                          <q-td key="subtotal" :props="props">{{ props.row.subtotal }}</q-td>
                          <q-td key="impuesto_importe" :props="props">{{ props.row.impuesto_importe }}</q-td>
                          <q-td key="total" :props="props">{{ props.row.total }}</q-td>
                          <q-td key="acciones" :props="props">
                            <q-btn v-if="!isDone && !isChecking" small flat @click="deleteSingleRowDetalle(props.row.id)" color="negative" icon="delete">
                              <q-tooltip>Eliminar</q-tooltip>
                            </q-btn>
                          </q-td>
                        </q-tr>
                      </template>
                    </q-table>
                    <q-inner-loading :visible="form_productos.loading">
                      <q-spinner-dots size="64px" color="primary" />
                    </q-inner-loading>
                  </div>
                </div>
              </q-tab-pane>
              <q-tab-pane name="fiscales" v-if="form.fields.tipo === 'VENTA'">
                <div class="row q-mt-lg">
                  <div class="col-sm-8 offset-sm-4 pull-right">
                    <q-btn v-if="detalles.length > 0 && form.fields.status === 'REMISIONADO' && !isChecking && !isDone" @click="timbrarRemision()" class="btn_guardar pull-right" icon-right="fas fa-certificate" :loading="loadingButton">Timbrar</q-btn>
                    <q-btn disabled v-if="form.fields.status_timbrado === 1" color="orange" class="pull-right">{{this.form.fields.uuid}}</q-btn>
                    <q-btn v-if="form.fields.status_timbrado === 1" @click="getCFDIInvoice(form.fields.id_request)" color="green-8" class="pull-right" icon-right="fas fa-file-excel" :loading="loadingButton">XML</q-btn>
                    <q-btn v-if="form.fields.status_timbrado === 1" @click="getPdfInvoice(form.fields.id_request)" color="brown" class="pull-right" icon-right="fas fa-file-pdf" :loading="loadingButton">PDF</q-btn>
                    <q-btn v-if="form.fields.status_timbrado === 1" @click="cancelarFactura()" class="pull-right" color="red-9" icon-right="cancel" :loading="loadingButton">Cancelar</q-btn>
                    <q-btn v-if="!isDone && !isChecking" @click="actualizarFiscal()" class="btn_actualizar pull-right" icon-right="save" :loading="loadingButton">Actualizar</q-btn>
                  </div>
                </div>
                <div class="row q-mt-lg">
                  <div class="col-sm-3">
                    <q-field icon="description" icon-color="dark" :error="$v.form_fiscal.fields.razon_social.$error" error-label="El cliente debe tener una razon social registrada">
                      <q-input readonly inverted color="dark" float-label="Razon social del receptor" v-model="form_fiscal.fields.razon_social"></q-input>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="description" icon-color="dark" :error="$v.form_fiscal.fields.rfc.$error" error-label="El cliente debe tener un RFC registrado">
                      <q-input readonly inverted color="dark" float-label="RFC del receptor" v-model="form_fiscal.fields.rfc"></q-input>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="description" icon-color="dark" :error="$v.form_fiscal.fields.serie.$error" error-label="Por favor registre una serie">
                      <q-input @blur="siguienteFolio()" :readonly="isDone || isChecking" upper-case inverted color="dark" float-label="Serie" v-model="form_fiscal.fields.serie"></q-input>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="description" icon-color="dark" :error="$v.form_fiscal.fields.folio_fiscal.$error" error-label="Por favor registre un folio fiscal">
                      <q-input readonly inverted color="dark" float-label="Folio fiscal" v-model="form_fiscal.fields.folio_fiscal"></q-input>
                    </q-field>
                  </div>
                </div>
                <div class="row q-mt-lg">
                  <div class="col-sm-3">
                    <q-field icon="date_range" icon-color="dark" :error="$v.form_fiscal.fields.fecha_factura.$error" error-label="Por favor registre una fecha">
                      <q-datetime :readonly="isDone || isChecking" v-model="form_fiscal.fields.fecha_factura" type="datetime" inverted color="dark" float-label="Fecha de emisión" align="center"></q-datetime>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="description" icon-color="dark" :error="$v.form_fiscal.fields.lugar_expedicion.$error" error-label="Por favor registre un código postal">
                      <q-input :readonly="isDone || isChecking" inverted color="dark" float-label="Lugar de expedición" v-model="form_fiscal.fields.lugar_expedicion" @keyup="isNumber($event,'codigo_postal')" maxlength="5"></q-input>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="description" icon-color="dark" :error="$v.form_fiscal.fields.tipo_comprobante.$error" error-label="Por favor seleccione un tipo de comprobante">
                      <q-select :readonly="isDone || isChecking" v-model="form_fiscal.fields.tipo_comprobante" inverted color="dark" float-label="Tipo de comprobante" :options="tiposComprobanteOptions" filter/>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="description" icon-color="dark" :error="$v.form_fiscal.fields.metodo_pago.$error" error-label="Por favor seleccione un método de pago">
                      <q-select :readonly="isDone || isChecking" v-model="form_fiscal.fields.metodo_pago" inverted color="dark" float-label="Método de pago" :options="metodoPagoOptions" filter/>
                    </q-field>
                  </div>
                </div>
                <div class="row q-mt-lg">
                  <div class="col-sm-3">
                    <q-field icon="description" icon-color="dark" :error="$v.form_fiscal.fields.forma_pago.$error" error-label="Por favor seleccione una forma de pago">
                      <q-select :readonly="isDone || isChecking" v-model="form_fiscal.fields.forma_pago" inverted color="dark" float-label="Forma de pago" :options="formaPagoOptions" filter/>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="description" icon-color="dark" :error="$v.form_fiscal.fields.uso_cfdi.$error" error-label="Por favor seleccione un uso CFDI">
                      <q-select :readonly="isDone || isChecking" v-model="form_fiscal.fields.uso_cfdi" inverted color="dark" float-label="Uso CFDI" :options="usoCFDIOptions" filter/>
                    </q-field>
                  </div>
                  <div class="col-sm-6"></div>
                </div>
              </q-tab-pane>
              <q-tab-pane name="pagos" v-if="form.fields.tipo === 'VENTA'">
                <div class="row q-mt-lg">
                  <div class="col-sm-8 offset-sm-4 pull-right">
                    <q-btn @click="agregarPago()" class="btn_guardar pull-right" icon-right="add" :loading="loadingButton">Agregar</q-btn>
                  </div>
                </div>
                <div class="row q-mt-lg">
                  <div class="col-sm-3">
                    <q-field icon="date_range" icon-color="dark" :error="$v.form_pagos.fields.fecha_pago.$error" error-label="Por favor registre una fecha">
                      <q-datetime v-model="form_pagos.fields.fecha_pago" type="datetime" inverted color="dark" float-label="Fecha del pago" align="center"></q-datetime>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="description" icon-color="dark" :error="$v.form_pagos.fields.total.$error" error-label="Por favor introduzca un total">
                      <q-input inverted color="dark" float-label="Total del pago" v-model="form_pagos.fields.total" @keyup="isNumber($event,'total')"></q-input>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="description" icon-color="dark" :error="$v.form_pagos.fields.forma_pago.$error" error-label="Por favor seleccione una forma de pago">
                      <q-select v-model="form_pagos.fields.forma_pago" inverted color="dark" float-label="Forma de pago" :options="formaPagoOptions" filter/>
                    </q-field>
                  </div>
                  <div class="col-sm-3"></div>
                </div>
                <div class="row q-mt-lg">
                  <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                    <q-table id="sticky-table"
                      :data="pagos"
                      :columns="form_pagos.columns"
                      :selected.sync="form_pagos.selected"
                      :filter="form_pagos.filter"
                      color="positive"
                      title=""
                      :dense="true"
                      :pagination.sync="form_pagos.pagination"
                      :loading="form_pagos.loading"
                      :rows-per-page-options="form_pagos.rowsOptions">
                      <template slot="body" slot-scope="props">
                        <q-tr :props="props">
                          <q-td key="num_parcialidad" :props="props">{{ props.row.num_parcialidad }}</q-td>
                          <q-td key="fecha_pago" :props="props">{{ props.row.fecha_pago }}</q-td>
                          <q-td key="total" :props="props">{{ props.row.total }}</q-td>
                          <q-td key="status_timbrado" :props="props"><q-chip dense :icon="iconTimbrado[props.row.status_timbrado]" :color="colorTimbrado[props.row.status_timbrado]" text-color="white">{{ statusTimbrado[props.row.status_timbrado] }}<q-tooltip v-if="props.row.status_timbrado === 6">{{props.row.message}}</q-tooltip><q-tooltip v-if="props.row.status_timbrado === 7">{{props.row.message_cancelacion}}</q-tooltip></q-chip></q-td>
                          <q-td key="acciones" :props="props">
                            <q-btn v-if="props.row.status_timbrado === 0 || props.row.status_timbrado === 6" small flat @click="timbrarPago(props.row)" color="green-6" icon="fas fa-certificate">
                              <q-tooltip>Timbrar</q-tooltip>
                            </q-btn>
                            <q-btn v-if="props.row.status_timbrado === 0 || props.row.status_timbrado === 6" small flat @click="deleteSingleRowPago(props.row)" color="red-6" icon="delete">
                              <q-tooltip>Eliminar</q-tooltip>
                            </q-btn>
                            <q-btn v-if="props.row.status_timbrado === 1" small flat @click="getCFDIInvoice(props.row.id_request)" color="green-8" class="pull-right" icon-right="fas fa-file-excel" :loading="loadingButton">
                              <q-tooltip>XML</q-tooltip>
                            </q-btn>
                            <q-btn v-if="props.row.status_timbrado === 1" small flat @click="getPdfInvoice(props.row.id_request)" color="brown" class="pull-right" icon-right="fas fa-file-pdf" :loading="loadingButton">
                              <q-tooltip>PDF</q-tooltip>
                            </q-btn>
                            <q-btn v-if="props.row.status_timbrado === 1" small flat @click="cancelarPago(props.row.id)" class="pull-right" color="red-9" icon-right="cancel" :loading="loadingButton">
                              <q-tooltip>Cancelar</q-tooltip>
                            </q-btn>
                          </q-td>
                        </q-tr>
                      </template>
                    </q-table>
                    <q-inner-loading :visible="form_productos.loading">
                      <q-spinner-dots size="64px" color="primary" />
                    </q-inner-loading>
                  </div>
                </div>
              </q-tab-pane>
              <q-tab-pane name="facturas" v-if="form.fields.tipo === 'HISTÓRICO'">
                <div class="row q-col-gutter-xs">
                        <div class="col-sm-3">
                          <q-field icon="description" icon-color="dark" :error="$v.form_facturas.file.nombre.$error" error-label="Ingrese el concepto de la factura">
                            <q-input upper-case inverted color="dark" float-label="Concepto factura" v-model="form_facturas.file.nombre"></q-input>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="date_range" icon-color="dark" :error="$v.form_facturas.file.fecha_factura.$error" error-label="Ingrese la fecha de factura">
                            <q-datetime v-model="form_facturas.file.fecha_factura" type="date" inverted color="dark" float-label="Fecha de emisión" align="center"></q-datetime>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="date_range" icon-color="dark" :error="$v.form_facturas.file.fecha_pago.$error" error-label="Ingrese la fecha de pago">
                            <q-datetime  v-model="form_facturas.file.fecha_pago" type="date" inverted color="dark" float-label="Fecha de pago" align="center"></q-datetime>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="date_range" icon-color="dark" :error="$v.form_facturas.file.fecha_vencimiento.$error" error-label="Ingrese la fecha de vencimiento">
                            <q-datetime  v-model="form_facturas.file.fecha_vencimiento" type="date" inverted color="dark" float-label="Fecha de vencimiento" align="center"></q-datetime>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="description" icon-color="dark">
                            <q-input upper-case inverted color="dark" float-label="Folio fiscal" v-model="form_facturas.file.folio_fiscal"></q-input>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field  icon="attach_file" icon-color="dark" >
                            <input style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="fileInputFormato" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf, .PNG, .JPG, .JPEG, .XML, .DOCX, .00, .PDF" @input="checkContrato()"/>
                            <q-btn @click="$refs.fileInputFormato.click()" class="btn_atach" v-if="show_file_contrato !== true" :loading="loadingButton">Adjuntar Factura</q-btn>
                            <q-btn @click="$refs.fileInputFormato.click()" class="btn_guardar" v-else :loading="loadingButton">Adjuntar Factura</q-btn>
                          </q-field>
                        </div>
                        <div class="col-sm-6 pull-right">
                          <q-btn  @click="uploadFormato()" class="btn_guardar" :loading="loadingButton">Guardar Factura</q-btn>
                        </div>
                      </div>
                <div class="col q-pa-md border-panel">
                        <div class="col-sm-12" style="padding-bottom: 10px;">
                          <q-search color="primary" v-model="form_facturas.filter"/>
                        </div>
                          <q-table id="sticky-table-newstyle"
                            :data="form_facturas.data"
                            :columns="form_facturas.columns"
                            :selected.sync="form_facturas.selected"
                            :filter="form_facturas.filter"
                            color="positive"
                            title=""
                            :dense="true"
                            :pagination.sync="form_facturas.pagination"
                            :loading="form_facturas.loading"
                            :rows-per-page-options="form_facturas.rowsOptions">
                            <template slot="body" slot-scope="props">
                              <q-tr :props="props">
                                <q-td key="id" :props="props"  style="cursor: pointer;">{{ props.row.id }}</q-td>
                                <q-td key="nombre" :props="props"  style="cursor: pointer;">{{ props.row.name }}</q-td>
                                <q-td key="extension" :props="props">{{ props.row.doc_type }}</q-td>
                                <q-td key="fecha_factura" :props="props">{{ props.row.fecha_factura }}</q-td>
                                <q-td key="fecha_pago" :props="props">{{ props.row.fecha_pago }}</q-td>
                                <q-td key="fecha_vencimiento" :props="props">{{ props.row.fecha_vencimiento }}</q-td>
                                <q-td key="monto_factura" :props="props">${{ currencyFormat(props.row.monto_factura) }}</q-td>
                                <q-td key="folio_fiscal" :props="props">{{ props.row.folio_fiscal }}</q-td>
                                <q-td key="acciones" :props="props">
                                  <q-btn small flat @click="abrirDocumento(props.row.id,props.row.doc_type)" color="blue-6" icon="get_app">
                                    <q-tooltip>Ver</q-tooltip>
                                  </q-btn>
                                  <q-btn small flat @click="deleteDoc(props.row.id)" color="negative" icon="delete">
                                    <q-tooltip>Eliminar</q-tooltip>
                                  </q-btn>
                                </q-td>
                              </q-tr>
                            </template>
                          </q-table>
                          <q-inner-loading :visible="form_facturas.loading">
                            <q-spinner-dots size="64px" color="primary" />
                          </q-inner-loading>
                      </div>
              </q-tab-pane>
            </q-tabs>
          </div>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import moment from 'moment'
import axios from 'axios'
import { required, minValue, minLength } from 'vuelidate/lib/validators'

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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'INVENTARIOS'.toUpperCase() || propiedades[i].toUpperCase() === 'COBRANZA'.toUpperCase() || propiedades[i].toUpperCase() === 'AUXILIAR'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR GDP'.toUpperCase()) {
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
      tabPrincipal: 'generales',
      interval: null,
      loadingButton: false,
      statusColor: {
        'NUEVO': 'amber',
        'REMISIONADO': 'purple'
      },
      statusIcon: {
        'NUEVO': 'add',
        'REMISIONADO': 'done'
      },
      statusCobranzaOptions: [{ 'label': 'EMITIDO', 'value': 'EMITIDO' }, { 'label': 'ABONADO', 'value': 'ABONADO' }, { 'label': 'PAGADO', 'value': 'PAGADO' }, { 'label': 'DESCONTADO', 'value': 'DESCONTADO' }, { 'label': 'Todos', 'value': 'all' }],
      tiposComprobanteOptions: [ { 'label': 'Ingreso', 'value': 'I' } ], /* , { 'label': 'Egreso', 'value': 'E' }, { 'label': 'Pago', 'value': 'P' } */
      metodoPagoOptions: [ { 'label': 'Pago en una sola exhibición', 'value': 'PUE' }, { 'label': 'Pago en parcialidades', 'value': 'PPD' } ],
      statusTimbrado: ['Nuevo', 'Timbrado', 'Cancelado', 'Cancelando', 'Timbrando', 'Cancelando', 'Error', 'Error al cancelar'],
      colorTimbrado: ['blue-6', 'green-6', 'purple-6', 'warning', 'warning', 'warning', 'red-6', 'red-6'],
      iconTimbrado: ['add', 'done', 'cancel', 'fas fa-ellipsis-h', 'fas fa-ellipsis-h', 'fas fa-ellipsis-h', 'cancel', 'cancel'],
      tipoOptions: [ { 'label': 'VENTA', 'value': 'VENTA' }, { 'label': 'HISTÓRICO', 'value': 'HISTÓRICO' } ],
      views: {
        grid: true,
        create: false,
        update: false,
        createSalida: false,
        updateSalida: false
      },
      form_filtros: {
        fields: {
          empresa_id: 0,
          cliente_id: 0,
          status: 'all'
        }
      },
      form: {
        fields: {
          id: 0,
          tipo: 'VENTA',
          empresa_id: 0,
          cliente_id: 0,
          fecha_venta: '',
          status: 'NUEVO',
          status_timbrado: 0,
          message: '',
          id_request: '',
          uuid: '',
          folio: '',
          folio_interno: '',
          folio_fiscal: ''
        },
        data: [],
        columns: [
          { name: 'folio', label: 'Folio', field: 'folio', sortable: true, type: 'string', align: 'left' },
          { name: 'folio_interno', label: 'Folio interno', field: 'folio_interno', sortable: true, type: 'string', align: 'left' },
          { name: 'folio_fiscal', label: 'Folio fiscal', field: 'folio_fiscal', sortable: true, type: 'string', align: 'left' },
          { name: 'empresa', label: 'Empresa', field: 'empresa', sortable: true, type: 'string', align: 'left' },
          { name: 'cliente', label: 'Cliente', field: 'cliente', sortable: true, type: 'string', align: 'left' },
          { name: 'created', label: 'Creado', field: 'created', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha_venta', label: 'Fecha de venta', field: 'fecha_venta', sortable: true, type: 'string', align: 'left' },
          { name: 'status_cobranza', label: 'Status cobranza', field: 'status_cobranza', sortable: true, type: 'string', align: 'left' },
          { name: 'status', label: 'Status', field: 'status', sortable: true, type: 'string', align: 'left' },
          { name: 'status_timbrado', label: 'Status del timbrado', field: 'status_timbrado', sortable: true, type: 'string', align: 'left' },
          { name: 'tipo', label: 'Tipo', field: 'tipo', sortable: true, type: 'string', align: 'left' },
          { name: 'monto_total', label: 'Monto total', field: 'monto_total', sortable: true, type: 'string', align: 'left' },
          { name: 'cancelado', label: 'Cancelada', field: 'cancelado', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_fiscal: {
        fields: {
          razon_social: '',
          rfc: '',
          folio_fiscal: '',
          serie: '',
          fecha_factura: '',
          lugar_expedicion: '',
          tipo_comprobante: 'I',
          metodo_pago: 'PUE',
          forma_pago: 0,
          uso_cfdi: 0
        }
      },
      form_pagos: {
        fields: {
          folio_fiscal: '',
          serie: '',
          fecha_pago: '',
          total: '',
          forma_pago: 0,
          num_parcialidad: 1
        },
        columns: [
          { name: 'num_parcialidad', label: 'Num. Parcialidad', field: 'num_parcialidad', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha_pago', label: 'Fecha del pago', field: 'fecha_pago', sortable: true, type: 'string', align: 'left' },
          { name: 'total', label: 'Total', field: 'total', sortable: true, type: 'string', align: 'left' },
          { name: 'status_timbrado', label: 'Status', field: 'status_timbrado', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_detalles: {
        fields: {
          id: 0,
          empresa_id: 0,
          cliente_id: 0,
          fecha_venta: '',
          status: 'NUEVO'
        },
        // data: [],
        columns: [
          { name: 'producto_nombre', label: 'Producto', field: 'producto_nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'descripcion', label: 'Descripcion', field: 'descripcion', sortable: true, type: 'string', align: 'left' },
          { name: 'tipo_unidad', label: 'Unidad', field: 'tipo_unidad', sortable: true, type: 'string', align: 'left' },
          { name: 'cantidad', label: 'Cantidad', field: 'cantidad', sortable: true, type: 'string', align: 'left' },
          { name: 'precio_unitario', label: 'Precio unitario', field: 'precio_unitario', sortable: true, type: 'string', align: 'left' },
          { name: 'subtotal', label: 'Subtotal', field: 'subtotal', sortable: true, type: 'string', align: 'left' },
          { name: 'impuesto_importe', label: 'Impuesto', field: 'impuesto_importe', sortable: true, type: 'string', align: 'left' },
          { name: 'total', label: 'Total', field: 'total', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_productos: {
        fields: {
          id: 0,
          producto_id: 0,
          remision_id: 0,
          cantidad: 1,
          impuesto: false
        },
        typeButton: 'save'
      },
      show_file_contrato: false,
      form_facturas: {
        data: [],
        filter: '',
        file: {
          nombre: '',
          fecha_pago: '',
          fecha_factura: '',
          fecha_vencimiento: '',
          folio_fiscal: ''
        },
        columns: [
          { name: 'id', label: 'Factura', field: 'factura', sortable: true, type: 'string', align: 'center' },
          { name: 'nombre', label: 'Nombre Factura', field: 'nombre', sortable: true, type: 'string', align: 'center' },
          { name: 'extension', label: 'Extensión', field: 'extension', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_factura', label: 'Fecha de Factura', field: 'fecha_factura', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_pago', label: 'Fecha de pago', field: 'fecha_pago', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_vencimiento', label: 'Fecha de vencimiento', field: 'fecha_vencimiento', sortable: true, type: 'string', align: 'center' },
          { name: 'monto_factura', label: 'Monto', field: 'monto_factura', sortable: true, type: 'string', align: 'right' },
          { name: 'folio_fiscal', label: 'Folio fiscal', field: 'folio_fiscal', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
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
      remisiones: 'vnt/remisiones/remisiones',
      detalles: 'vnt/remisiones/detalles',
      pagos: 'vnt/remisiones/pagos',
      productosOptions: 'inv/productos/selectOptions',
      formaPagoOptions: 'vnt/remisiones/selectPaymentFormsOptions',
      usoCFDIOptions: 'vnt/remisiones/selectUsoCFDIOptions'
    }),
    isDone () {
      return this.form.fields.status_timbrado === 1 || this.form.fields.status_timbrado === 2
    },
    isRemisionado () {
      return this.form.fields.status === 'REMISIONADO'
    },
    isChecking () {
      return this.form.fields.status_timbrado === 3 || this.form.fields.status_timbrado === 4 || this.form.fields.status_timbrado === 5
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
  methods: {
    ...mapActions({
      getRemisiones: 'vnt/remisiones/getBy',
      getRemision: 'vnt/remisiones/get',
      saveRemisiones: 'vnt/remisiones/save',
      updateRemisiones: 'vnt/remisiones/update',
      deleteRemisiones: 'vnt/remisiones/delete',
      getEmpresas: 'vnt/empresa/refresh',
      getClientes: 'ventas/clientes/refresh',
      getProductos: 'inv/productos/refresh',
      saveProduct: 'vnt/remisiones/saveProduct',
      updateProduct: 'vnt/remisiones/updateProduct',
      deleteProduct: 'vnt/remisiones/deleteProduct',
      getDetalles: 'vnt/remisiones/refreshDetails',
      changeStatus: 'vnt/remisiones/changeStatus',
      getFormasDePago: 'vnt/remisiones/getFormasDePago',
      getUsoCFDIs: 'vnt/remisiones/getUsoCFDIs',
      getDatosFiscales: 'vnt/remisiones/getDatosFiscales',
      updateFiscal: 'vnt/remisiones/updateFiscal',
      checkInvoice: 'vnt/remisiones/checkInvoice',
      checkPagos: 'vnt/remisiones/checkPagos',
      timbrar: 'vnt/remisiones/timbrar',
      cancelInvoice: 'vnt/remisiones/cancelInvoice',
      cancelPago: 'vnt/remisiones/cancelPago',
      savePago: 'vnt/remisiones/savePago',
      deletePayment: 'vnt/remisiones/deletePayment',
      getPagos: 'vnt/remisiones/getPagos',
      keepChecking: 'vnt/remisiones/keepChecking',
      timbrarPayment: 'vnt/remisiones/timbrarPayment',
      getNextFolio: 'vnt/remisiones/getNextFolio',
      getFacturasByRemision: 'vnt/remisionesFactura/getFacturas',
      deleteDocFacturas: 'vnt/remisionesFactura/delete',
      getFolioConsecutivo: 'vnt/remisiones/getFolioConsecutivo',
      cancelarRemision: 'vnt/remisiones/cancelar'
    }),
    isNumber (evt, input) {
      switch (input) {
        case 'cantidad':
          this.form_productos.fields.cantidad = this.form_productos.fields.cantidad.replace(/[^0-9.]/g, '')
          this.$v.form_productos.fields.cantidad.$touch()
          break
        case 'total':
          this.form_pagos.fields.total = this.form_pagos.fields.total.replace(/[^0-9.]/g, '')
          this.$v.form_pagos.fields.total.$touch()
          break
        case 'numero_interior':
          this.form.fields.numero_interior = this.form.fields.numero_interior.replace(/[^0-9.]/g, '')
          this.$v.form.fields.numero_interior.$touch()
          break
        case 'codigo_postal':
          this.form_fiscal.fields.lugar_expedicion = this.form_fiscal.fields.lugar_expedicion.replace(/[^0-9.]/g, '')
          this.$v.form_fiscal.fields.lugar_expedicion.$touch()
          break
        default:
          break
      }
    },
    async loadAll () {
      this.form.loading = true
      await this.getEmpresas()
      await this.getClientes()
      await this.filtrar()
      await this.getProductos()
      await this.getFormasDePago()
      await this.getUsoCFDIs()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    filtrar () {
      console.log(this.form_filtros.fields.empresa_id)
      this.getRemisiones({empresa_id: this.form_filtros.fields.empresa_id, cliente_id: this.form_filtros.fields.cliente_id, status: this.form_filtros.fields.status}).then(({data}) => {
        this.form.data = data.remisiones
      }).catch(error => {
        console.error(error)
      })
    },
    async getFolioNuevo () {
      this.form.fields.folio = ''
      await this.getFolioConsecutivo({tipo: this.form.fields.tipo}).then(({data}) => {
        if (data.result === 'success') {
          this.form.fields.folio = data.folio
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async save () {
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        params.metodo_pago = this.form_fiscal.fields.metodo_pago
        this.saveRemisiones(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              color: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'check',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.editRow(data.insert)
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
        this.updateRemisiones(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              color: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'check',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
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
    async editRow (row) {
      this.form.fields.id = row.id
      this.form_productos.fields.remision_id = row.id
      this.form.fields.empresa_id = row.empresa_id
      this.form.fields.cliente_id = row.cliente_id
      this.form.fields.fecha = row.created
      this.form.fields.fecha_venta = row.fecha_venta
      this.form.fields.status = row.status
      this.form.fields.status_timbrado = row.status_timbrado
      this.form.fields.message = row.message
      this.form.fields.id_request = row.id_request
      this.form.fields.uuid = row.uuid
      this.form.fields.tipo = row.tipo
      this.form.fields.folio = row.folio
      this.form.fields.folio_interno = row.folio_interno
      this.form.fields.folio_fiscal = row.folio_fiscal
      // await this.getProductos()
      if (this.form.fields.tipo === 'HISTÓRICO') {
        this.tabPrincipal = 'facturas'
      }
      this.limpiarDetalles()
      if ((this.form.fields.status_timbrado === 4 || this.form.fields.status_timbrado === 3 || this.form.fields.status_timbrado === 5) && this.interval === null) {
        this.interval = setInterval(() => {
          this.revisarTimbrado()
        }, 10000)
      }
      await this.cargarFacturas()
      this.setView('update')
    },
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar esta remision?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete(id)
      }).catch(() => {})
    },
    delete (id) {
      let params = {id: id}
      this.deleteRemisiones(params).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            // only required parameter is the message:
            message: data.message.content,
            timeout: 3000,
            color: 'green',
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
    deleteSingleRowDetalle (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar el concepto?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.borrarDetalle(id)
      }).catch(() => {})
    },
    borrarDetalle (id) {
      let params = {id: id}
      this.deleteProduct(params).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            // only required parameter is the message:
            message: data.message.content,
            timeout: 3000,
            color: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'delete',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
          this.limpiarDetalles()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async addProduct () {
      this.$v.form_productos.$touch()
      if (!this.$v.form_productos.$error) {
        this.loadingButton = true
        let params = this.form_productos.fields
        this.saveProduct(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              color: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'check',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
          this.limpiarDetalles()
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    actualizarProducto () {
      this.$v.form_productos.$touch()
      if (!this.$v.form_productos.$error) {
        this.loadingButton = true
        let params = this.form_productos.fields
        this.updateProduct(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              color: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'check',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
          this.limpiarDetalles()
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    actualizarFiscal () {
      this.loadingButton = true
      let params = this.form_fiscal.fields
      params.id = this.form.fields.id
      this.updateFiscal(params).then(({data}) => {
        this.loadingButton = false
        if (data.result === 'success') {
          this.$q.notify({
            // only required parameter is the message:
            message: data.message.content,
            timeout: 3000,
            color: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'check',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
        this.limpiarDetalles()
      }).catch(error => {
        console.error(error)
      })
    },
    cambiarStatus (status) {
      this.loadingButton = true
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea cambiar el status a ' + status + '?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        let params = { status: status, remision_id: this.form.fields.id, tipo: this.form.fields.tipo }
        this.changeStatus(params).then(({data}) => {
          if (data.result === 'success') {
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              color: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'check',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.form.fields.status = status
            this.limpiarDetalles()
          } else {
            this.$showMessage(data.message.title, data.message.content)
            this.form.fields.status = 'NUEVO'
          }
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
      this.loadingButton = false
    },
    revisarTimbrado () {
      this.loadingButton = true
      let params = { id: this.form.fields.id }
      this.checkInvoice(params).then(({data}) => {
        if (data.response.status === 'done' || data.response.status === 'error' || data.response.status === true) {
          this.limpiarDetalles()
        }
      }).catch(error => {
        console.error(error)
      })
      this.loadingButton = false
    },
    revisarPagos () {
      this.loadingButton = true
      let params = { remision_id: this.form.fields.id }
      this.checkPagos(params).then(({data}) => {
        this.limpiarPagos()
      }).catch(error => {
        console.error(error)
      })
      this.loadingButton = false
    },
    async editRowDetalle (row) {
      this.form_productos.fields.id = row.id
      this.form_productos.fields.remision_id = row.remision_id
      this.form_productos.fields.producto_id = row.producto_id
      this.form_productos.fields.cantidad = row.cantidad
      this.form_productos.fields.descripcion = row.descripcion
      this.form_productos.fields.impuesto = row.impuesto
      this.form_productos.typeButton = 'update'
    },
    newRow () {
      this.limpiar()
      this.setView('create')
    },
    atras () {
      this.loadAll()
      this.setView('grid')
    },
    limpiar () {
      this.$v.form.$reset()
      this.form.fields.id = 0
      this.form.fields.cliente_id = 0
      this.form.fields.empresa_id = 0
      this.form.fields.status = 'NUEVO'
      this.form.fields.fecha_venta = ''
      this.form.fields.tipo = 'VENTA'
      this.getFolioNuevo()
    },
    limpiarDetalles () {
      this.$v.form_productos.$reset()
      this.$v.form_fiscal.$reset()
      this.form_productos.fields.producto_id = 0
      this.form_productos.fields.cantidad = 1
      this.form_productos.fields.descripcion = ''
      this.form_productos.fields.impuesto = false
      this.form_productos.typeButton = 'save'
      this.form_productos.loading = true
      this.getRemision({ id: this.form.fields.id }).then(({data}) => {
        if (data.result === 'success') {
          let remision = data.remision[0]
          this.form.fields.id = remision.id
          this.form_productos.fields.remision_id = remision.id
          this.form.fields.empresa_id = remision.empresa_id
          this.form.fields.cliente_id = remision.cliente_id
          this.form.fields.fecha = remision.created
          this.form.fields.fecha_venta = remision.fecha_venta
          this.form.fields.status = remision.status
          this.form.fields.status_timbrado = remision.status_timbrado
          this.form.fields.message = remision.message
          this.form.fields.id_request = remision.id_request
          this.form.fields.uuid = remision.uuid
          this.form.fields.tipo = remision.tipo
          this.form.fields.folio = remision.folio
          this.getDetalles({ remision_id: this.form_productos.fields.remision_id }).then(() => {
            if (this.form.fields.status === 'REMISIONADO') {
              this.getDatosFiscales({ remision_id: this.form_productos.fields.remision_id }).then(({data}) => {
                let remision = data.remision[0]
                this.form_fiscal.fields.razon_social = remision.razon_social
                this.form_fiscal.fields.rfc = remision.rfc
                this.form_fiscal.fields.folio_fiscal = remision.folio_fiscal
                this.form_fiscal.fields.serie = remision.serie
                this.form_fiscal.fields.fecha_factura = remision.fecha_factura
                this.form_fiscal.fields.lugar_expedicion = remision.lugar_expedicion
                this.form_fiscal.fields.tipo_comprobante = remision.tipo_comprobante
                this.form_fiscal.fields.metodo_pago = remision.metodo_pago
                this.form_fiscal.fields.forma_pago = remision.forma_pago
                this.form_fiscal.fields.uso_cfdi = remision.uso_cfdi
                if ((this.form.fields.status_timbrado !== 3 && this.form.fields.status_timbrado !== 4 && this.form.fields.status_timbrado !== 5) && this.interval !== null) {
                  clearInterval(this.interval)
                }
                if (this.form_fiscal.fields.metodo_pago === 'PPD' && this.form.fields.status_timbrado === 1) {
                  this.form_pagos.fields.serie = this.form_fiscal.fields.serie
                  this.form_pagos.fields.folio_fiscal = this.form_fiscal.fields.folio_fiscal
                  this.getPagos({ remision_id: this.form.fields.id }).then(() => {
                    this.keepChecking({ remision_id: this.form.fields.id }).then(({data}) => {
                      if (!data.result) {
                        clearInterval(this.interval)
                      } else if (this.interval === null) {
                        this.interval = setInterval(() => {
                          this.revisarPagos()
                        }, 10000)
                      }
                    })
                  })
                }
              })
            }
          })
        }
      })
      this.form_productos.loading = false
    },
    getRemisionData () {
      this.getRemision({ id: this.form.fields.id }).then(({data}) => {
        if (data.result === 'success') {
          let remision = data.remision[0]
          this.form.fields.id = remision.id
          this.form_productos.fields.remision_id = remision.id
          this.form.fields.empresa_id = remision.empresa_id
          this.form.fields.cliente_id = remision.cliente_id
          this.form.fields.fecha = remision.created
          this.form.fields.fecha_venta = remision.fecha_venta
          this.form.fields.status = remision.status
          this.form.fields.status_timbrado = remision.status_timbrado
          this.form.fields.message = remision.message
          this.form.fields.id_request = remision.id_request
          this.form.fields.uuid = remision.uuid
          this.form.fields.tipo = remision.tipo
          this.form.fields.folio = remision.folio
        }
      }).catch(error => {
        console.error(error)
      })
    },
    timbrarRemision () {
      this.$v.form_fiscal.$touch()
      if (!this.$v.form_fiscal.$error) {
        this.$q.dialog({
          title: 'Confirmar',
          message: '¿Desea timbrar la factura?',
          ok: 'Aceptar',
          cancel: 'Cancelar'
        }).then(() => {
          this.loadingButton = true
          let params = this.form_fiscal.fields
          params.remision_id = this.form.fields.id
          this.timbrar(params).then(({data}) => {
            this.loadingButton = false
            if (data.result === 'error') {
              this.$showMessage(data.message.title, data.message.content)
            } else {
              this.interval = setInterval(() => {
                this.revisarTimbrado()
              }, 10000)
            }
            this.getRemisionData()
          }).catch(error => {
            console.error(error)
          })
        }).catch(() => {})
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    cancelarFactura () {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea cancelar la factura?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.loadingButton = true
        let params = { id: this.form.fields.id }
        this.cancelInvoice(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'error') {
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              color: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'check',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
          } else {
            this.interval = setInterval(() => {
              this.revisarTimbrado()
            }, 10000)
          }
          this.getRemisionData()
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
    },
    cancelarPago (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea cancelar la factura?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.loadingButton = true
        let params = { id: id }
        this.cancelPago(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'error') {
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              color: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'check',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
          } else if (this.interval === null) {
            this.interval = setInterval(() => {
              this.revisarPagos()
            }, 10000)
          }
          this.limpiarPagos()
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
    },
    async agregarPago () {
      this.$v.form_pagos.$touch()
      if (!this.$v.form_pagos.$error) {
        this.loadingButton = true
        let params = this.form_pagos.fields
        params.remision_id = this.form.fields.id
        this.savePago(params).then(({data}) => {
          this.loadingButton = false
          console.log(data)
          if (data.result === 'success') {
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              color: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'check',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.limpiarPagos()
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
    deleteSingleRowPago (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar el pago?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.borrarPago(id)
      }).catch(() => {})
    },
    borrarPago (id) {
      let params = {id: id}
      this.deletePayment(params).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            // only required parameter is the message:
            message: data.message.content,
            timeout: 3000,
            color: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'delete',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
          this.limpiarPagos()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    limpiarPagos () {
      this.$v.form_pagos.$reset()
      this.form_pagos.fields.fecha_pago = ''
      this.form_pagos.fields.total = ''
      this.form_pagos.fields.forma_pago = 0
      this.getPagos({ remision_id: this.form.fields.id }).then(() => {
        this.keepChecking({ remision_id: this.form.fields.id }).then(({data}) => {
          if (!data.result) {
            clearInterval(this.interval)
          } else if (this.interval === null) {
            this.interval = setInterval(() => {
              this.revisarPagos()
            }, 10000)
          }
        })
      })
    },
    timbrarPago (row) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea timbrar el pago?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.loadingButton = true
        let params = { id: row.id }
        this.timbrarPayment(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'error') {
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              color: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'check',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
          } else if (this.interval === null) {
            this.interval = setInterval(() => {
              this.revisarPagos()
            }, 10000)
          }
          console.log(this.interval)
          this.limpiarPagos()
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
    },
    siguienteFolio () {
      this.getNextFolio({ remision_id: this.form.fields.id, serie: this.form_fiscal.fields.serie }).then(({data}) => {
        this.form_fiscal.fields.folio_fiscal = data.folio_fiscal
      })
    },
    onSelectedProduct () {
      this.form_productos.fields.descripcion = this.productosOptions.find(opt => opt.value === this.form_productos.fields.producto_id).label
    },
    getCFDIInvoice (idRequest) {
      var url = process.env.API === 'http://api.impact.antfarm.mx/' ? 'batuta.antfarm.mx' : 'batuta.beta.antfarm.mx'
      window.open('http://' + url + '/api/download_xml/' + idRequest, '_blank')
    },
    getPdfInvoice (idRequest) {
      var url = process.env.API === 'http://api.impact.antfarm.mx/' ? 'batuta.antfarm.mx' : 'batuta.beta.antfarm.mx'
      window.open('http://' + url + '/api/get_pdf/' + idRequest + '/0', '_blank')
    },
    // todo lo de facturas
    uploadFormato () {
      this.$v.form_facturas.file.$touch()
      if (!this.$v.form_facturas.file.$error) {
        if (this.form_facturas.file.fecha_vencimiento >= this.form_facturas.file.fecha_factura) {
          try {
            this.loadingButton = true
            let file = this.$refs.fileInputFormato.files[0]
            let formData = new FormData()
            formData.append('file', file)
            formData.append('nombre', this.form_facturas.file.nombre)
            formData.append('nombre_archivo', file.name)
            formData.append('remision_id', this.form.fields.id)
            formData.append('folio_fiscal', this.form_facturas.file.folio_fiscal)
            formData.append('fecha_factura', moment(String(this.form_facturas.file.fecha_factura)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss'))
            formData.append('fecha_pago', moment(String(this.form_facturas.file.fecha_pago)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss'))
            formData.append('fecha_vencimiento', moment(String(this.form_facturas.file.fecha_vencimiento)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss'))
            console.log('aquillega')
            if (file === null || file === undefined || file === '') {
              this.$showMessage('Error', 'Seleccione un archivo')
            } else if (file.name) {
              if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
                this.$q.loading.show({message: 'Subiendo archivo...'})
                axios.post('remisionesFacturas/uploadArchivo', formData, {
                  headers: {
                    'Content-Type': 'multipart/form-data'
                  }
                }).then(response => {
                  if (response.data.result === 'success') {
                    this.$showMessage(response.data.message.title, response.data.message.content)
                    this.cargarFacturas()
                    this.limpiarFactura()
                  } else {
                    if (response.data.err !== '') {
                      this.$showMessage(response.data.message.title, response.data.message.content)
                      // this.$showMessage('Errores en archivo', response.data.err)
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
            this.$showMessage('Error', 'Seleccione un archivo')
          }
        } else {
          this.$showMessage('Error', 'La fecha de vencimiento debe ser igual o posterior a la fecha de emisión')
        }
      } else {
        this.$showMessage('Error', 'llene los campos obligatorios')
      }
    },
    checkContrato () {
      if (this.$refs.fileInputFormato.files !== null) {
        this.show_file_contrato = true
      }
    },
    async cargarFacturas () {
      this.form_facturas.data = []
      await this.getFacturasByRemision({remision: this.form.fields.id}).then(({data}) => {
        if (data.result === 'success') {
          this.form_facturas.data = data.facturas
        }
      }).catch(error => {
        console.error(error)
      })
    },
    currencyFormat (num) {
      return Number.parseFloat(num).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
    },
    limpiarFactura () {
      this.$v.form_facturas.file.$reset()
      this.form_facturas.file.nombre = ''
      this.form_facturas.file.fecha_pago = ''
      this.form_facturas.file.fecha_factura = ''
      this.form_facturas.file.fecha_vencimiento = ''
      this.form_facturas.file.folio_fiscal = ''
      this.show_file_contrato = false
    },
    abrirDocumento (name, ext) {
      let nombre = name
      let uri = process.env.API + `remisionesFacturas/getFile/${nombre}/${ext}`
      window.open(uri, '_blank')
    },
    deleteDoc (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este archivo?',
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
    borrar () {
      this.form_filtros.fields.empresa_id = 0
      this.form_filtros.fields.cliente_id = 0
      this.form_filtros.fields.status = 'all'
    },
    cancelRemision (row) {
      this.loadingButton = true
      let params = row
      this.cancelarRemision(params).then(({data}) => {
        this.loadingButton = false
        if (data.result === 'success') {
          this.$q.notify({
            // only required parameter is the message:
            message: data.message.content,
            timeout: 3000,
            color: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'check',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    }
  },
  validations: {
    form: {
      fields: {
        empresa_id: {required, minValue: minValue(1)},
        cliente_id: {required, minValue: minValue(1)},
        fecha_venta: {required}
      }
    },
    form_productos: {
      fields: {
        producto_id: {required, minValue: minValue(1)},
        cantidad: {required}
      }
    },
    form_fiscal: {
      fields: {
        razon_social: {required},
        rfc: {required},
        folio_fiscal: {required},
        serie: {required},
        fecha_factura: {required},
        lugar_expedicion: {required},
        tipo_comprobante: {required},
        metodo_pago: {required},
        forma_pago: {required},
        uso_cfdi: {required}
      }
    },
    form_pagos: {
      fields: {
        fecha_pago: {required},
        forma_pago: {required},
        total: {required}
      }
    },
    form_facturas: {
      file: {
        nombre: {required, minLength: minLength(3)},
        fecha_pago: {required},
        fecha_factura: {required},
        fecha_vencimiento: {required}
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
</style>
