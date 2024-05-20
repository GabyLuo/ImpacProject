<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-4">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Abonos facturas" />
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-4 pull-right" style="margin-top: 10px;">
            <q-btn-toggle color="teal" v-model="tipo" toggle-color="primary" :options="tipoFacturas" @input="changeFactura()"/>
          </div>
          <div class="col-sm-4 pull-right" style="margin-top: 10px;">
            <q-btn-toggle color="teal" v-model="year" toggle-color="primary" :options="selectYear" @input="changeFactura()"/>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs" v-if="tipo === 'GDP'">
              <div class="col-sm-3">
                <q-field icon="work" icon-color="dark">
                  <q-select v-model="form.fields.proyecto_id" inverted color="dark" float-label="Proyecto" :options="recursosOptions" filter @input="getContratosOpt()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="work" icon-color="dark">
                  <q-select v-model="form.fields.cliente_id" inverted color="dark" float-label="Cliente" :options="clientesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="work" icon-color="dark">
                  <q-select v-model="form.fields.contrato_id" inverted color="dark" float-label="Contratos" :options="contratosOptions" filter @input="getFacturasOpt()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="work" icon-color="dark">
                  <q-select v-model="form.fields.factura_id_filtro" inverted color="dark" float-label="Facturas" :options="facturasOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="work" icon-color="dark">
                  <q-select v-model="form.fields.empresa_id_filtro" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-info" icon-color="dark">
                  <q-select v-model="form.fields.status" inverted color="dark" float-label="Status" :options="statusOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-6 pull-right">
                <q-btn @click="filtrar()" inverted color="orange" icon-right="fas fa-filter" :loading="loadingButton">Filtrar</q-btn>
                <q-btn @click="borrar()" color="red" icon="loop"><q-tooltip>Limpiar</q-tooltip></q-btn>
              </div>
            </div>
            <div class="row q-col-gutter-xs" v-else>
              <div class="col-sm-3">
                <q-field icon="work" icon-color="dark">
                  <q-select v-model="form_retail.cliente_id" inverted color="dark" float-label="Cliente" :options="clientesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="work" icon-color="dark">
                  <q-select v-model="form_retail.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-info" icon-color="dark">
                  <q-select v-model="form_retail.status" inverted color="dark" float-label="Status" :options="statusOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3 pull-right">
                <q-btn @click="filtrarRetail()" inverted color="orange" icon-right="fas fa-filter" :loading="loadingButton">Filtrar</q-btn>
                <q-btn @click="filtrarDuplicadas()" inverted color="purple" icon-right="fas fa-filter" :loading="loadingButton"></q-btn>
                <q-btn @click="borrarRetail()" color="red" icon="loop"><q-tooltip>Limpiar</q-tooltip></q-btn>
                <q-btn @click="reporteRetail()" color="green" icon="fas fa-file-excel"><q-tooltip>Generar CSV</q-tooltip></q-btn>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white">
          <div class="col q-pa-md"  style="padding: 0;">
            <div class="col q-pa-md border-panel" v-if="tipo === 'GDP'">
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
                      <q-td key="codigo" :props="props" style="cursor: pointer;">{{ props.row.codigo }}</q-td>
                      <q-td key="cliente" :props="props" style="cursor: pointer;">{{ props.row.cliente }}</q-td>
                      <q-td key="name_factura" :props="props" style="cursor: pointer;">{{ props.row.name_factura }}</q-td>
                      <q-td key="empresa" :props="props" style="cursor: pointer;">{{ props.row.empresa }}</q-td>
                      <q-td key="folio_xml" :props="props" style="cursor: pointer;">{{ props.row.folio_xml }}</q-td>
                      <q-td key="fecha_factura" :props="props">{{ props.row.fecha_factura }}</q-td>
                      <!-- <q-td key="fecha_pago" :props="props">{{ props.row.fecha_pago }}</q-td> -->
                      <q-td key="fecha_vencimiento" :props="props">{{ props.row.fecha_vencimiento }}</q-td>
                      <q-td key="monto_factura" :props="props">${{ currencyFormat(props.row.monto_factura) }}</q-td>
                      <!-- <q-td key="folio_fiscal" :props="props">{{ props.row.folio_fiscal }}</q-td> -->
                      <q-td key="monto_total_abonado" :props="props">${{ currencyFormat(props.row.monto_total_abonado) }}</q-td>
                      <q-td key="monto_restante" :props="props">${{ currencyFormat(props.row.monto_restante) }}</q-td>
                      <q-td key="status" :props="props">
                        <q-chip dense :icon="props.row.icon" :color="props.row.color" text-color="white">{{ props.row.status }}</q-chip>
                        <q-chip dense :icon="props.row.icon" color="green" text-color="white" v-if="props.row.status === 'DESCONTADO'">PAGADO</q-chip>
                      </q-td>
                      <q-td key="cancelada" :props="props"><q-checkbox readonly v-model="props.row.cancelada" color="green-10"/></q-td>
                      <q-td key="acciones" :props="props">
                        <q-btn small flat @click="abrirDocumento(props.row.factura_id,props.row.doc_type)" color="blue-6" icon="get_app">
                          <q-tooltip>Ver</q-tooltip>
                        </q-btn>
                        <q-btn small flat @click="abonar(props.row)" color="teal" icon="fas fa-dollar-sign" v-if="props.row.status !== 'PAGADO' && props.row.status !== 'DESCONTADO' && props.row.cancelada===false" >
                          <q-tooltip>Abonar</q-tooltip>
                        </q-btn>
                        <q-btn small flat @click="getHistorial(props.row)" color="orange" icon="fas fa-calendar-alt">
                          <q-tooltip>Ver historial de abonos</q-tooltip>
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
            <div class="col q-pa-md border-panel" v-else>
              <div class="col-sm-12">
                <q-search color="primary" v-model="form_retail.filter"/>
              </div>
              <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                <q-table id="sticky-table-newstyle"
                  :data="form_retail.data"
                  :columns="form_retail.columns"
                  :selected.sync="form_retail.selected"
                  :filter="form_retail.filter"
                  color="positive"
                  title=""
                  :dense="true"
                  :pagination.sync="form_retail.pagination"
                  :loading="form_retail.loading"
                  :rows-per-page-options="form_retail.rowsOptions">
                  <template slot="body" slot-scope="props">
                    <q-tr :props="props">
                      <!-- <q-td key="codigo" :props="props" style="cursor: pointer;">{{ props.row.codigo }}</q-td> -->
                      <q-td key="folio" :props="props" style="cursor: pointer;">{{ props.row.folio }}</q-td>
                      <q-td key="empresa" :props="props" style="cursor: pointer;">{{ props.row.empresa }}</q-td>
                      <q-td key="cliente" :props="props" style="cursor: pointer;">{{ props.row.cliente }}</q-td>
                      <q-td key="name" :props="props">{{ props.row.name }}</q-td>
                      <q-td key="folio_fiscal" :props="props">{{ props.row.folio_fiscal }}</q-td>
                      <q-td key="created" :props="props">{{ props.row.created }}</q-td>
                      <q-td key="fecha_venta" :props="props">{{ props.row.fecha_venta }}</q-td>
                      <q-td key="tipo" :props="props">{{ props.row.tipo }}</q-td>
                      <!-- <q-td key="fecha_vencimiento" :props="props">{{ props.row.fecha_vencimiento }}</q-td> -->
                      <q-td key="monto_factura" :props="props">${{ currencyFormat(props.row.monto_factura) }}</q-td>
                      <!-- <q-td key="folio_fiscal" :props="props">{{ props.row.folio_fiscal }}</q-td> -->
                      <q-td key="monto_total_abonado" :props="props">${{ currencyFormat(props.row.monto_total_abonado) }}</q-td>
                      <q-td key="monto_restante" :props="props">${{ currencyFormat(props.row.monto_restante) }}</q-td>
                      <q-td key="status_cobranza" :props="props"><q-chip dense :icon="props.row.icon" :color="props.row.color" text-color="white">{{ props.row.status_cobranza }}</q-chip></q-td>
                      <q-td key="cancelado" :props="props"><q-checkbox readonly v-model="props.row.cancelado" color="green-10"/></q-td>
                      <q-td key="acciones" :props="props">
                        <q-btn small flat @click="abrirDocumentoRetail(props.row.factura_id,props.row.doc_type)" color="blue-6" icon="get_app">
                          <q-tooltip>Ver</q-tooltip>
                        </q-btn>
                        <q-btn small flat @click="abonarRetail(props.row)" color="teal" icon="fas fa-dollar-sign" v-if="(props.row.status_cobranza==='EMITIDO' || props.row.status_cobranza==='ABONADO' || (props.row.status_cobranza==='DESCONTADO' && props.row.monto_restante > 0)) && props.row.cancelado === false">
                          <q-tooltip>Abonar</q-tooltip>
                        </q-btn>
                        <q-btn small flat @click="getHistorialRetail(props.row)" color="orange" icon="fas fa-calendar-alt">
                          <q-tooltip>Ver historial de abonos</q-tooltip>
                        </q-btn>
                      </q-td>
                    </q-tr>
                  </template>
                </q-table>
                <q-inner-loading :visible="form_retail.loading">
                  <q-spinner-dots size="64px" color="primary" />
                </q-inner-loading>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <q-modal no-backdrop-dismiss v-if="modal_abono" style="background-color: rgba(0,0,0,0.7);" v-model="modal_abono" :content-css="{width: '65vw', height: '800px'}">
      <q-modal-layout>
        <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
          <div class="col-sm-10">
            <q-toolbar-title>
              Factura
            </q-toolbar-title>
          </div>
          <div class="col-sm-2 pull-right">
            <q-btn
              flat
              round
              dense
              color="white"
              @click="modal_abono = false"
              icon="fas fa-times-circle"
            />
          </div>
        </q-toolbar>
      </q-modal-layout>
      <div class="row q-mt-lg subtitulos" style="margin-top:70px;margin-left:20px;">
          Información del contrato
      </div>
      <div class="row q-mt-lg" style="margin-right:20px;">
          <div class="col-sm-4">
            <q-field icon="fas fa-file" icon-color="dark">
              <q-input readonly upper-case v-model="form.fields.num_contrato" inverted-color="dark" float-label="Número de contrato"/>
            </q-field>
          </div>
          <div class="col-sm-4">
            <q-field icon="fas fa-building" icon-color="dark">
              <q-select readonly v-model="form.fields.empresa_id" inverted-color="dark" float-label="Empresa" :options="empresasOptions" filter/>
            </q-field>
          </div>
          <div class="col-sm-4">
            <q-field icon="fas fa-book" icon-color="dark">
              <q-input readonly upper-case v-model="form.fields.licitacion" inverted-color="dark" float-label="Licitación"/>
            </q-field>
          </div>
          <div class="col-sm-4">
            <q-field icon="fas fa-user" icon-color="dark">
              <q-input readonly upper-case v-model="form.fields.cliente" inverted-color="dark" float-label="Cliente"></q-input>
            </q-field>
          </div>
          <div class="col-sm-4">
            <q-field icon="fas fa-user" icon-color="dark">
              <q-input readonly upper-case inverted-color="dark" float-label="Representante legal del contrato" v-model="form.fields.rep_legal_contrato"></q-input>
            </q-field>
          </div>
          <div class="col-sm-4">
            <div class="icono_field">
              <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                <q-input readonly v-model.lazy="form.fields.monto_total_validar" v-money="money" inverted-color="dark" float-label="Monto" suffix="MXN"></q-input>
              </q-field>
            </div>
          </div>
        </div>
        <div class="row q-mt-lg subtitulos" style="margin-left:20px;">
          Información de la factura
        </div>
        <div class="row q-mt-lg" style="margin-right:20px;">
          <div class="col-sm-9">
            <q-field icon="fas fa-file" icon-color="dark">
              <q-input readonly upper-case v-model="form.fields.name" inverted-color="dark" float-label="Nombre de factura"/>
            </q-field>
          </div>
          <div class="col-sm-3">
            <q-field icon="fas fa-dollar-sign" icon-color="dark">
              <q-input readonly upper-case inverted-color="dark" float-label="Monto factura" v-model="form.fields.monto_factura_total"></q-input>
            </q-field>
          </div>
        </div>
        <div class="row q-mt-lg subtitulos" style="margin-left:20px;">
          Cantidad a abonar
        </div>
        <div class="row q-mt-lg" style="margin-right:20px;">
          <div class="col-sm-4">
            <div class="icono_field">
              <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                <q-input v-model.lazy="form.fields.monto_abonar" v-money="money" inverted color="dark" float-label="Monto a pagar" suffix="MXN" @keyup="quitaError()"></q-input>
              </q-field>
            </div>
          </div>
          <div class="col-sm-4">
            <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha_abono.$error" error-label="Ingrese una fecha">
              <q-datetime v-model="form.fields.fecha_abono" type="date" inverted color="dark" float-label="Fecha abono" align="center"></q-datetime>
            </q-field>
          </div>
          <div class="col-sm-4">
            <q-field icon="label" icon-color="dark">
              <q-input upper-case v-model="form.fields.transaccion" type="text" inverted color="dark" float-label="Transaccion" maxlenght=100></q-input>
            </q-field>
          </div>
          <div class="col-sm-4" style="padding-left: 40px;">
            <input multiple style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="fileInputFormatoGdp" accept=".xml, .XML" @input="uploadXMLGDP()"/>
            <q-btn @click="$refs.fileInputFormatoGdp.click()" class="btn_atach" :loading="loadingButton">Adjuntar Factura(s)</q-btn>
          </div>
        </div>
        <div class="row q-mt-lg pull-right" style="margin-right: 20px;">
          <div class="col-sm-12">
              <q-btn @click="guardarAbono()" inverted color="green" icon-right="fas fa-dollar-sign" :loading="loadingButton">Abonar</q-btn>
          </div>
        </div>
        <div class="row q-mt-lg" style="margin-right:20px;">
        </div>
    </q-modal>

    <q-modal no-backdrop-dismiss v-if="modal_abono_retail" style="background-color: rgba(0,0,0,0.7);" v-model="modal_abono_retail" :content-css="{width: '75vw', height: '800px'}">
      <q-modal-layout>
        <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
          <div class="col-sm-10">
            <q-toolbar-title>
              Factura
            </q-toolbar-title>
          </div>
          <div class="col-sm-2 pull-right">
            <q-btn
              flat
              round
              dense
              color="white"
              @click="modal_abono_retail = false"
              icon="fas fa-times-circle"
            />
          </div>
        </q-toolbar>
      </q-modal-layout>
      <div class="row q-mt-lg subtitulos" style="margin-top:70px;margin-left:20px;">
          Información de la factura
      </div>
      <div class="row q-mt-lg" style="margin-right:20px;">
          <div class="col-sm-3">
            <q-field icon="fas fa-building" icon-color="dark">
              <q-select readonly v-model="form_retail.fields.empresa_id" inverted-color="dark" float-label="Empresa" :options="empresasOptions" filter/>
            </q-field>
          </div>
          <div class="col-sm-3">
            <q-field icon="fas fa-building" icon-color="dark">
              <q-select readonly v-model="form_retail.fields.cliente_id" inverted-color="dark" float-label="Cliente" :options="clientesOptions" filter/>
            </q-field>
          </div>
          <div class="col-sm-3">
            <q-field icon="date_range" icon-color="dark">
              <q-datetime v-model="form_retail.fields.fecha_venta" type="date" inverted-color="dark" float-label="Fecha factura" align="center"></q-datetime>
            </q-field>
          </div>
          <div class="col-sm-3">
            <div class="icono_field">
              <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                <q-input readonly v-model.lazy="form_retail.fields.monto_total_validar" v-money="money" inverted-color="dark" float-label="Monto" suffix="MXN"></q-input>
              </q-field>
            </div>
          </div>
          <div class="col-sm-6">
            <q-field icon="label" icon-color="dark">
              <q-input upper-case v-model="form_retail.fields.name" type="text" inverted-color="dark" float-label="Concepto" maxlenght=100></q-input>
            </q-field>
          </div>
          <div class="col-sm-6">
            <q-field icon="label" icon-color="dark">
              <q-input upper-case v-model="form_retail.fields.folio_fiscal" type="text" inverted-color="dark" float-label="Folio fiscal" maxlenght=100></q-input>
            </q-field>
          </div>
        </div>
        <div class="row q-mt-lg subtitulos" style="margin-left:20px;">
          Cantidad a abonar
        </div>
        <div class="row q-mt-lg" style="margin-right:20px;">
          <div class="col-sm-4">
            <div class="icono_field">
              <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                <q-input v-model.lazy="form_retail.fields.monto_abonar" v-money="money" inverted color="dark" float-label="Monto a pagar" suffix="MXN" @keyup="quitaError()"></q-input>
              </q-field>
            </div>
          </div>
          <div class="col-sm-4">
            <q-field icon="date_range" icon-color="dark" :error="$v.form_retail.fields.fecha_abono.$error" error-label="Ingrese una fecha">
              <q-datetime v-model="form_retail.fields.fecha_abono" type="date" inverted color="dark" float-label="Fecha abono" align="center"></q-datetime>
            </q-field>
          </div>
          <div class="col-sm-4">
            <q-field icon="label" icon-color="dark">
              <q-input upper-case v-model="form_retail.fields.transaccion" type="text" inverted color="dark" float-label="Transaccion" maxlenght=100></q-input>
            </q-field>
          </div>
          <div class="col-sm-4" style="padding-left: 40px;">
            <input multiple style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="fileInputFormato" accept=".xml, .XML" @input="uploadXML()"/>
            <q-btn @click="$refs.fileInputFormato.click()" class="btn_atach" :loading="loadingButton">Adjuntar Factura(s)</q-btn>
          </div>
        </div>
        <div class="row q-mt-lg pull-right" style="margin-right: 20px;">
          <div class="col-sm-12">
              <q-btn @click="guardarAbonoRetail()" inverted color="green" icon-right="fas fa-dollar-sign" :loading="loadingButton">Abonar</q-btn>
          </div>
        </div>
        <div class="row q-mt-lg" style="margin-right:20px;">
        </div>
    </q-modal>

    <q-modal no-backdrop-dismiss v-if="modal_historial_abonos" style="background-color: rgba(0,0,0,0.7);" v-model="modal_historial_abonos" :content-css="{width: '50vw', height: '800px'}">
      <q-modal-layout>
        <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
          <div class="col-sm-10">
            <q-toolbar-title>
              Historial de abonos
            </q-toolbar-title>
          </div>
          <div class="col-sm-2 pull-right">
            <q-btn
              flat
              round
              dense
              color="white"
              @click="modal_historial_abonos = false"
              icon="fas fa-times-circle"
            />
          </div>
        </q-toolbar>
      </q-modal-layout>
      <!-- <div class="row q-mt-lg subtitulos" style="margin-top:70px;margin-left:20px;">
          Abonos realizados
      </div> -->
      <div class="row q-mt-sm" style="margin-top:70px;margin-left:20px;margin-right:20px;margin-bottom:20px;">
          <div  class="col-sm-12 q-mt-sm" id="sticky-table-scroll" >
            <q-table id="sticky-table"
              :data="form_historial.data"
              :columns="form_historial.columns"
              :selected.sync="form_historial.selected"
              color="positive"
              title=""
              :dense="true"
              :pagination.sync="form_historial.pagination"
              :loading="form_historial.loading"
              :rows-per-page-options="form_historial.rowsOptions">
              <template slot="body" slot-scope="props">
                <q-tr :props="props">
                  <q-td key="nickname" :props="props" style="cursor: pointer;">{{ props.row.nickname }}</q-td>
                  <q-td key="fecha" :props="props"  style="cursor: pointer;">{{ props.row.fecha }}</q-td>
                  <q-td key="transaccion" :props="props">{{ props.row.transaccion }}</q-td>
                  <q-td key="monto" :props="props">${{ currencyFormat(props.row.monto) }}</q-td>
                  <q-td key="acciones" :props="props">
                    <q-btn small flat @click="deleteSingleRow(props.row.id)" color="negative" icon="delete" v-if="props.row.xml === false">
                      <q-tooltip>Eliminar</q-tooltip>
                    </q-btn>
                    <q-btn small flat @click="abrirDocumento(props.row.factura_id,props.row.doc_type)" color="blue-6" icon="get_app" v-if="props.row.xml === true">
                          <q-tooltip>Ver</q-tooltip>
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

        <div class="row q-mt-lg" style="margin-right:20px;">
        </div>
    </q-modal>

    <q-modal no-backdrop-dismiss v-if="modal_historial_abonos_retail" style="background-color: rgba(0,0,0,0.7);" v-model="modal_historial_abonos_retail" :content-css="{width: '50vw', height: '800px'}">
      <q-modal-layout>
        <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
          <div class="col-sm-10">
            <q-toolbar-title>
              Historial de abonos
            </q-toolbar-title>
          </div>
          <div class="col-sm-2 pull-right">
            <q-btn
              flat
              round
              dense
              color="white"
              @click="modal_historial_abonos_retail = false"
              icon="fas fa-times-circle"
            />
          </div>
        </q-toolbar>
      </q-modal-layout>
      <!-- <div class="row q-mt-lg subtitulos" style="margin-top:70px;margin-left:20px;">
          Abonos realizados
      </div> -->
      <div class="row q-mt-sm" style="margin-top:70px;margin-left:20px;margin-right:20px;margin-bottom:20px;">
          <div  class="col-sm-12 q-mt-sm" id="sticky-table-scroll" >
            <q-table id="sticky-table"
              :data="form_historial.data"
              :columns="form_historial.columns"
              :selected.sync="form_historial.selected"
              color="positive"
              title=""
              :dense="true"
              :pagination.sync="form_historial.pagination"
              :loading="form_historial.loading"
              :rows-per-page-options="form_historial.rowsOptions">
              <template slot="body" slot-scope="props">
                <q-tr :props="props">
                  <q-td key="nickname" :props="props" style="cursor: pointer;">{{ props.row.nickname }}</q-td>
                  <q-td key="fecha" :props="props"  style="cursor: pointer;">{{ props.row.fecha }}</q-td>
                  <q-td key="transaccion" :props="props">{{ props.row.transaccion }}</q-td>
                  <q-td key="monto" :props="props">${{ currencyFormat(props.row.monto) }}</q-td>
                  <q-td key="acciones" :props="props">
                    <q-btn small flat @click="deleteSingleRowRetail(props.row.id)" color="negative" icon="delete" v-if="props.row.xml === false">
                      <q-tooltip>Eliminar</q-tooltip>
                    </q-btn>
                    <q-btn small flat @click="abrirDocumentoRetail(props.row.factura_id,props.row.doc_type)" color="blue-6" icon="get_app" v-if="props.row.xml === true">
                          <q-tooltip>Ver</q-tooltip>
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

        <div class="row q-mt-lg" style="margin-right:20px;">
        </div>
    </q-modal>
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { required } from 'vuelidate/lib/validators'
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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'COBRANZA'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'AUXILIAR'.toUpperCase()) {
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
      selectYear: [ { label: '' + (new Date().getFullYear() - 3), value: '' + (new Date().getFullYear() - 3) }, { label: '' + (new Date().getFullYear() - 2), value: '' + (new Date().getFullYear() - 2) }, { label: '' + (new Date().getFullYear() - 1), value: '' + (new Date().getFullYear() - 1) }, { label: '' + (new Date().getFullYear()), value: '' + (new Date().getFullYear()) }, { label: '' + (new Date().getFullYear() + 1), value: '' + (new Date().getFullYear() + 1) } ],
      year: '' + (new Date().getFullYear()),
      loadingButton: false,
      contratosOptions: [{ 'label': '---Seleccione---', 'value': 0 }],
      modal_abono: false,
      modal_abono_retail: false,
      modal_historial_abonos: false,
      modal_historial_abonos_retail: false,
      historial: [],
      facturasOptions: [{ 'label': '---Seleccione---', 'value': 0 }],
      statusOptions: [{ 'label': 'EMITIDO', 'value': 'EMITIDO' }, { 'label': 'ABONADO', 'value': 'ABONADO' }, { 'label': 'PAGADO', 'value': 'PAGADO' }, { 'label': 'DESCONTADO', 'value': 'DESCONTADO' }, { 'label': 'Todos', 'value': 'all' }],
      tipoFacturas: [{ 'label': 'GDP', 'value': 'GDP' }, { 'label': 'RETAIL', 'value': 'RETAIL' }],
      tipo: 'GDP',
      data_retail: [],
      data_gdp: [],
      money: {
        decimal: '.',
        thousands: ',',
        precision: 2,
        masked: false
      },
      views: {
        grid: true,
        create: false,
        update: false
      },
      form: {
        fields: {
          id: 0,
          nombre: '',
          proyecto_id: 0,
          cliente_id: 0,
          contrato_id: 0,
          num_contrato: 0,
          empresa_id: 0,
          empresa_id_filtro: 0,
          fecha_inicio: '',
          fecha_fin: '',
          licitacion: 0,
          cliente: '',
          rep_legal_contrato: '',
          monto_total_validar: 0,
          name: '',
          fecha_factura: '',
          fecha_pago: '',
          fecha_vencimiento: '',
          monto_abonar: 0,
          monto_abonar2: 0,
          factura_id: 0,
          factura_id_filtro: 0,
          fecha_abono: '',
          monto_factura: 0,
          monto_total_abonado: 0,
          monto_restante: 0,
          monto_factura_total: 0,
          status: 'all'
        },
        // data: [],
        columns: [
          { name: 'codigo', label: 'Proyecto', field: 'codigo', sortable: true, type: 'string', align: 'left' },
          { name: 'cliente', label: 'Cliente', field: 'cliente', sortable: true, type: 'string', align: 'left' },
          { name: 'name_factura', label: 'Nombre factura', field: 'name_factura', sortable: true, type: 'string', align: 'left' },
          { name: 'empresa', label: 'Empresa', field: 'empresa', sortable: true, type: 'string', align: 'left' },
          { name: 'folio_xml', label: 'Folio', field: 'folio_xml', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_factura', label: 'Fecha factura', field: 'fecha_factura', sortable: true, type: 'string', align: 'center' },
          // { name: 'fecha_pago', label: 'Fecha compromiso', field: 'fecha_pago', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_vencimiento', label: 'Fecha vencimiento', field: 'fecha_vencimiento', sortable: true, type: 'string', align: 'center' },
          { name: 'monto_factura', label: 'Monto factura', field: 'monto_factura', sortable: true, type: 'string', align: 'right' },
          // { name: 'folio_fiscal', label: 'Folio fiscal', field: 'folio_fiscal', sortable: true, type: 'string', align: 'center' },
          { name: 'monto_total_abonado', label: 'Monto abonado', field: 'monto_total_abonado', sortable: true, type: 'string', align: 'right' },
          { name: 'monto_restante', label: 'Monto restante', field: 'monto_restante', sortable: true, type: 'string', align: 'right' },
          { name: 'status', label: 'Status', field: 'status', sortable: true, type: 'string', align: 'left' },
          { name: 'cancelada', label: 'Cancelada', field: 'cancelada', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        data: [],
        selected: [],
        filter: '',
        loading: false
      },
      form_retail: {
        cliente_id: 0,
        empresa_id: 0,
        status: 'all',
        fields: {
          id: 0,
          nombre: '',
          proyecto_id: 0,
          cliente_id: 0,
          contrato_id: 0,
          num_contrato: 0,
          empresa_id: 0,
          fecha_inicio: '',
          fecha_fin: '',
          licitacion: 0,
          cliente: '',
          rep_legal_contrato: '',
          monto_total_validar: 0,
          name: '',
          fecha_factura: '',
          fecha_pago: '',
          fecha_vencimiento: '',
          monto_abonar: 0,
          monto_abonar2: 0,
          factura_id: 0,
          factura_id_filtro: 0,
          fecha_abono: '',
          monto_factura: 0,
          monto_total_abonado: 0,
          monto_restante: 0,
          fecha_venta: '',
          folio_fiscal: ''
        },
        // data: [],
        columns: [
          { name: 'folio', label: 'Folio', field: 'folio', sortable: true, type: 'string', align: 'left' },
          { name: 'empresa', label: 'Empresa', field: 'empresa', sortable: true, type: 'string', align: 'left' },
          { name: 'cliente', label: 'Cliente', field: 'cliente', sortable: true, type: 'string', align: 'left' },
          { name: 'name', label: 'Concepto', field: 'name', sortable: true, type: 'string', align: 'left' },
          { name: 'folio_fiscal', label: 'Folio fiscal', field: 'folio_fiscal', sortable: true, type: 'string', align: 'left' },
          { name: 'created', label: 'Creado', field: 'created', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_venta', label: 'Fecha venta', field: 'fecha_venta', sortable: true, type: 'string', align: 'center' },
          { name: 'tipo', label: 'Tipo', field: 'tipo', sortable: true, type: 'string', align: 'center' },
          // { name: 'fecha_vencimiento', label: 'Fecha vencimiento', field: 'fecha_vencimiento', sortable: true, type: 'string', align: 'center' },
          { name: 'monto_factura', label: 'Total', field: 'monto_factura', sortable: true, type: 'string', align: 'right' },
          // { name: 'folio_fiscal', label: 'Folio fiscal', field: 'folio_fiscal', sortable: true, type: 'string', align: 'center' },
          { name: 'monto_total_abonado', label: 'Abonado', field: 'monto_total_abonado', sortable: true, type: 'string', align: 'right' },
          { name: 'monto_restante', label: 'Restante', field: 'monto_restante', sortable: true, type: 'string', align: 'right' },
          { name: 'status_cobranza', label: 'Status', field: 'status_cobranza', sortable: true, type: 'string', align: 'left' },
          { name: 'cancelado', label: 'Cancelado', field: 'cancelado', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        data: [],
        selected: [],
        filter: '',
        loading: false
      },
      form_historial: {
        fields: {
          nickname: '',
          fecha: '',
          transaccion: '',
          monto: ''
        },
        // data: [],
        columns: [
          { name: 'nickname', label: 'Abonó', field: 'nickname', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha', label: 'Fecha de abono', field: 'fecha', sortable: true, type: 'string', align: 'center' },
          { name: 'transaccion', label: 'Transaccion', field: 'transaccion', sortable: true, type: 'string', align: 'center' },
          { name: 'monto', label: 'Monto', field: 'monto', sortable: true, type: 'string', align: 'right' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'center' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        data: [],
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
      estados: 'vnt/estado/estados'// ,
      // clientesOptions: 'ventas/clientes/selectOptions'
    }),
    recursosOptions () {
      let recurso = this.$store.getters['vnt/recurso/selectOptionsName']
      recurso.splice(0, 0, {label: '---Seleccione---', value: 0})
      recurso.sort(function (a, b) {
        if (a.codigo > b.codigo) {
          return 1
        }
        if (a.codigo < b.codigo) {
          return -1
        }
        return 0
      })
      return recurso
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
      getRecursos: 'vnt/recurso/refresh',
      getClientes: 'ventas/clientes/refresh',
      getEmpresas: 'vnt/empresa/refresh',
      getContratosbyID: 'vnt/contrato/getByID',
      getFacturas: 'vnt/contratoFactura/getFacturasByContratoandId',
      getContratos: 'vnt/contrato/getOptions',
      saveAbono: 'vnt/abonosFacturas/save',
      historialAbonos: 'vnt/abonosFacturas/historialAbonos',
      deleteAbono: 'vnt/abonosFacturas/delete',
      getFacturasOpt: 'vnt/contratoFactura/getFacturasByContrato',
      // facturas retail
      getFacturasRetail: 'vnt/remisionesFactura/getFacturasByContratoandId',
      getFacturasRetailDuplicadas: 'vnt/remisionesFactura/getFacturasDuplicadas',
      saveAbonoRetail: 'vnt/abonosRemisiones/save',
      historialAbonosRetail: 'vnt/abonosRemisiones/historialAbonos',
      deleteAbonoRetail: 'vnt/abonosRemisiones/delete'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getClientes()
      await this.getEmpresas()
      await this.getRecursos()
      await this.changeFactura()
      // await this.getFacturasOpt()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este abono?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete(id)
      }).catch(() => {})
    },
    delete (id) {
      let params = {id: id}
      this.deleteAbono(params).then(({data}) => {
        // this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          // ***
          this.$q.notify({
          // only required parameter is the message:
            message: data.message.content,
            timeout: 3000,
            type: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'delete',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
          // ***
          this.getHistorial(this.form.fields.factura_id)
          this.modal_historial_abonos = false
          this.loadAll()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    getContratosOpt () {
      this.getContratos({id: this.form.fields.proyecto_id}).then(({data}) => {
        if (data.result === 'success') {
          if (data.contratosOptions.length > 0) {
            this.contratosOptions = data.contratosOptions
            this.form.fields.contrato_id = 0
            this.contratosOptions.push({ 'label': '---Seleccione---', 'value': 0 })
          } else {
            this.contratosOptions = []
            this.form.fields.contrato_id = 0
            this.contratosOptions.push({ 'label': '---No hay contratos---', 'value': 0 })
            this.facturasOptions = []
            this.form.fields.factura_id = 0
            this.facturasOptions.push({ 'label': '---No hay facturas---', 'value': 0 })
          }
        }
      }).catch(error => {
        console.error(error)
      })
    },
    getFacturasOpt () {
      this.getFacturasOpt({contrato: this.form.fields.contrato_id}).then(({data}) => {
        if (data.result === 'success') {
          if (data.facturasOptions.length > 0) {
            this.facturasOptions = data.facturasOptions
            this.form.fields.factura_id = 0
            this.facturasOptions.push({ 'label': '---Seleccione---', 'value': 0 })
          } else {
            this.facturasOptions = []
            this.form.fields.factura_id = 0
            this.facturasOptions.push({ 'label': '---No hay facturas---', 'value': 0 })
          }
        }
      }).catch(error => {
        console.error(error)
      })
    },
    filtrar () {
      this.form.loading = true
      this.getFacturas({proyecto: this.form.fields.proyecto_id, cliente: this.form.fields.cliente_id, contrato: this.form.fields.contrato_id, factura: this.form.fields.factura_id_filtro, year: this.year, status: this.form.fields.status, empresa_id: this.form.fields.empresa_id_filtro}).then(({data}) => {
        if (data.facturas.length > 0) {
          data.facturas.forEach(function (element) {
            if (element.status === 'EMITIDO') {
              element.color = 'amber'
              element.icon = 'fas fa-arrow-circle-right'
            } else if (element.status === 'ABONADO') {
              element.color = 'blue'
              element.icon = 'fas fa-check-circle'
            } else if (element.status === 'PAGADO') {
              element.color = 'green'
              element.icon = 'done_all'
            } else if (element.status === 'DESCONTADO') {
              element.color = 'lime'
              element.icon = 'fas fa-file-invoice-dollar'
            }
          })
          this.form.data = data.facturas
        } else {
          this.form.data = data.facturas
          this.$q.notify({
            message: 'No existen facturas para este proyecto/cliente/contrato', timeout: 3000, type: 'orange', textColor: 'white', icon: 'warning', position: 'top-right'
          })
        }
      }).catch(error => {
        console.error(error)
      })
      this.form.loading = false
    },
    abrirDocumento (name, ext) {
      console.log(name)
      let nombre = name
      let uri = process.env.API + `facturasContratos/getFile/${nombre}/${ext}`
      window.open(uri, '_blank')
    },
    abonar (row) {
      this.data_gdp = row
      this.form.fields.num_contrato = row.num_contrato
      this.form.fields.empresa_id = parseInt(row.empresa_id)
      this.form.fields.fecha_inicio = row.fecha_inicio
      this.form.fields.fecha_fin = row.fecha_fin
      this.form.fields.licitacion = row.licitacion
      if (row.cliente === null) {
        this.form.fields.cliente = ''
      } else {
        this.form.fields.cliente = row.cliente
      }
      this.form.fields.rep_legal_contrato = row.rep_legal_contrato
      this.form.fields.monto_total_validar = row.monto_total
      this.form.fields.fecha_factura = row.fecha_factura
      this.form.fields.fecha_pago = row.fecha_pago
      this.form.fields.fecha_vencimiento = row.fecha_vencimiento
      this.form.fields.name = row.name
      this.form.fields.monto_abonar = 0
      this.form.fields.monto_abonar2 = 0
      this.form.fields.factura_id = row.factura_id
      this.form.fields.fecha_abono = ''
      this.form.fields.transaccion = ''
      this.form.fields.monto_total_abonado = row.monto_total_abonado
      this.form.fields.monto_factura = row.monto_factura
      this.form.fields.monto_factura_total = row.monto_factura_total
      this.form.fields.monto_restante = row.monto_restante
      this.$v.form.$reset()
      this.modal_abono = true
    },
    guardarAbono () {
      this.form.fields.monto_abonar2 = this.evaluaMonto(this.form.fields.monto_abonar)
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        if (parseFloat(this.form.fields.monto_restante) < this.form.fields.monto_abonar2) {
          this.$showMessage('¡Advertencia!', 'No se puede abonar una cantidad mayor al monto restante.')
        } else {
          this.saveAbono({factura_id: this.form.fields.factura_id, monto_abonar: this.form.fields.monto_abonar2, fecha_abono: this.form.fields.fecha_abono, transaccion: this.form.fields.transaccion, monto_total_abonado: this.form.fields.monto_total_abonado}).then(({data}) => {
            this.$q.notify({
              message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'done', position: 'top-right'
            })
            // this.$showMessage(data.message.title, data.message.content)
            this.modal_abono = false
            this.filtrar()
          }).catch(error => {
            console.error(error)
          })
        }
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
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
    currencyFormat (num) {
      return Number.parseFloat(num).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
    },
    getHistorial (row) {
      this.form.fields.factura_id = row.factura_id
      this.historialAbonos({factura_id: this.form.fields.factura_id}).then(({data}) => {
        if (data.abonos.length > 0) {
          this.historial = data.abonos
          this.form_historial.loading = true
          this.form_historial.data = data.abonos
          this.form_historial.loading = false
          this.modal_historial_abonos = true
        } else {
          this.modal_historial_abonos = false
          this.$showMessage('Advertencia', 'No hay abonos registrados para esta factura.')
        }
      }).catch(error => {
        console.error(error)
      })
    },
    quitaError () {
      this.$v.form.$reset()
    },
    borrar () {
      this.form.fields.proyecto_id = 0
      this.form.fields.contrato_id = 0
      this.form.fields.cliente_id = 0
      this.form.fields.factura_id = 0
      this.form.fields.factura_id_filtro = 0
      this.form.fields.empresa_id_filtro = 0
      this.form.fields.status = 'all'
    },
    changeFactura () {
      if (this.tipo === 'GDP') {
        this.filtrar()
      } else {
        this.filtrarRetail()
      }
    },
    //
    filtrarRetail () {
      this.form.loading = true
      this.getFacturasRetail({cliente: this.form_retail.cliente_id, empresa: this.form_retail.empresa_id, year: this.year, status: this.form_retail.status}).then(({data}) => {
        if (data.facturas.length > 0) {
          data.facturas.forEach(function (element) {
            if (element.status_cobranza === 'EMITIDO') {
              element.color = 'amber'
              element.icon = 'fas fa-arrow-circle-right'
            } else if (element.status_cobranza === 'ABONADO') {
              element.color = 'blue'
              element.icon = 'fas fa-check-circle'
            } else if (element.status_cobranza === 'PAGADO') {
              element.color = 'green'
              element.icon = 'done_all'
            } else if (element.status_cobranza === 'DESCONTADO') {
              element.color = 'lime'
              element.icon = 'fas fa-dollar-sign'
            }
          })
          this.form_retail.data = data.facturas
        } else {
          this.form_retail.data = data.facturas
          this.$q.notify({
            message: 'No existen facturas para este cliente/empresa', timeout: 3000, type: 'orange', textColor: 'white', icon: 'warning', position: 'top-right'
          })
        }
      }).catch(error => {
        console.error(error)
      })
      this.form.loading = false
    },
    filtrarDuplicadas () {
      this.form.loading = true
      this.getFacturasRetailDuplicadas({cliente: this.form_retail.cliente_id, empresa: this.form_retail.empresa_id, year: this.year, status: this.form_retail.status}).then(({data}) => {
        if (data.facturas.length > 0) {
          data.facturas.forEach(function (element) {
            if (element.status_cobranza === 'EMITIDO') {
              element.color = 'amber'
              element.icon = 'fas fa-arrow-circle-right'
            } else if (element.status_cobranza === 'ABONADO') {
              element.color = 'blue'
              element.icon = 'fas fa-check-circle'
            } else if (element.status_cobranza === 'PAGADO') {
              element.color = 'green'
              element.icon = 'done_all'
            }
          })
          this.form_retail.data = data.facturas
        } else {
          this.form_retail.data = data.facturas
          this.$q.notify({
            message: 'No existen facturas para este cliente/empresa', timeout: 3000, type: 'orange', textColor: 'white', icon: 'warning', position: 'top-right'
          })
        }
      }).catch(error => {
        console.error(error)
      })
      this.form.loading = false
    },
    borrarRetail () {
      this.form_retail.empresa_id = 0
      this.form_retail.cliente_id = 0
      this.form_retail.status = 'all'
    },
    abrirDocumentoRetail (name, ext) {
      let nombre = name
      let uri = process.env.API + `remisionesFacturas/getFile/${nombre}/${ext}`
      window.open(uri, '_blank')
    },
    reporteRetail () {
      let uri = process.env.API + `remisionesFacturas/getFacturasDuplicadasCsv/${this.form_retail.cliente_id}/${this.form_retail.empresa_id}/${this.year}/${this.form_retail.status}`
      window.open(uri, '_blank')
    },
    abonarRetail (row) {
      this.data_retail = row
      this.form_retail.fields.empresa_id = parseInt(row.empresa_id)
      this.form_retail.fields.cliente_id = parseInt(row.cliente_id)
      // this.form_retail.fields.fecha_inicio = row.fecha_inicio
      // this.form_retail.fields.fecha_fin = row.fecha_fin
      if (row.cliente === null) {
        this.form_retail.fields.cliente = ''
      } else {
        this.form_retail.fields.cliente = row.cliente
      }
      this.form_retail.fields.factura_id = row.id
      this.form_retail.fields.monto_total_validar = row.monto_factura
      // this.form_retail.fields.fecha_factura = row.fecha_factura
      // this.form_retail.fields.fecha_pago = row.fecha_pago
      // this.form_retail.fields.fecha_vencimiento = row.fecha_vencimiento
      // this.form_retail.fields.name = row.name
      this.form_retail.fields.monto_abonar = 0
      this.form_retail.fields.monto_abonar2 = 0
      this.form_retail.fields.fecha_abono = ''
      this.form_retail.fields.transaccion = ''
      this.form_retail.fields.monto_total_abonado = row.monto_total_abonado
      this.form_retail.fields.monto_factura = row.monto_factura
      this.form_retail.fields.monto_restante = row.monto_restante
      this.form_retail.fields.name = row.name
      this.form_retail.fields.fecha_venta = row.fecha_venta
      this.form_retail.fields.folio_fiscal = row.folio_fiscal
      this.$v.form_retail.$reset()
      this.modal_abono_retail = true
    },
    guardarAbonoRetail () {
      this.form_retail.fields.monto_abonar2 = this.evaluaMonto(this.form_retail.fields.monto_abonar)
      this.$v.form_retail.$touch()
      if (!this.$v.form_retail.$error) {
        if (parseFloat(this.form_retail.fields.monto_restante) < this.form_retail.fields.monto_abonar2) {
          this.$showMessage('¡Advertencia!', 'No se puede abonar una cantidad mayor al monto restante.')
        } else {
          this.saveAbonoRetail({factura_id: this.form_retail.fields.factura_id, monto_abonar: this.form_retail.fields.monto_abonar2, fecha_abono: this.form_retail.fields.fecha_abono, transaccion: this.form_retail.fields.transaccion, monto_total_abonado: this.form_retail.fields.monto_total_abonado, monto_factura: this.form_retail.fields.monto_factura}).then(({data}) => {
            this.$q.notify({
              message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'done', position: 'top-right'
            })
            // this.$showMessage(data.message.title, data.message.content)
            this.modal_abono_retail = false
            this.filtrarRetail()
          }).catch(error => {
            console.error(error)
          })
        }
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    getHistorialRetail (row) {
      this.form_retail.fields.factura_id = row.id
      this.historialAbonosRetail({factura_id: this.form_retail.fields.factura_id}).then(({data}) => {
        if (data.abonos.length > 0) {
          this.historial = data.abonos
          this.form_historial.loading = true
          this.form_historial.data = data.abonos
          this.form_historial.loading = false
          this.modal_historial_abonos_retail = true
        } else {
          this.modal_historial_abonos_retail = false
          this.$showMessage('Advertencia', 'No hay abonos registrados para esta factura.')
        }
      }).catch(error => {
        console.error(error)
      })
    },
    deleteSingleRowRetail (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este abono?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.deleteRetail(id)
      }).catch(() => {})
    },
    deleteRetail (id) {
      let params = {id: id}
      this.deleteAbonoRetail(params).then(({data}) => {
        // this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          // ***
          this.$q.notify({
          // only required parameter is the message:
            message: data.message.content,
            timeout: 3000,
            type: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'delete',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
          // ***
          // this.getHistorialRetail(id)
          this.modal_historial_abonos_retail = false
          this.changeFactura()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    uploadXML () {
      this.terminado = true
      this.loadingButton = false
      if (this.form.files_length !== 0) {
        let file = this.$refs.fileInputFormato.files[0]
        let formData = new FormData()
        formData.append('nombre', file.name)
        formData.append('file', file)
        formData.append('folio_fiscal', this.data_retail.folio_fiscal)
        axios.post('remisionesFacturas/uploadAmortizacion', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        }).then(response => {
          if (response.data.result === 'success') {
            this.terminado = false
            if (response.data.message.title === '¡Error!') {
              this.$showMessage(response.data.message.title, response.data.message.content)
            } else {
              this.$q.notify({
                message: response.data.message.content,
                timeout: 3000,
                type: 'green',
                textColor: 'white', // if default 'white' doesn't fits
                icon: 'done',
                position: 'top-right' // 'top', 'left', 'bottom-left' etc
              })
            }
          } else {
            if (response.data.err !== '') {
              this.$showMessage(response.data.message.title, response.data.message.content)
            }
          }
        }).catch(error => {
          this.terminado = false
          this.loadingButton = false
          console.error(error)
        })
      } else {
        this.terminado = false
        this.loadingButton = false
        this.$showMessage('Error', 'No ha seleccionado un archivo con la extensión .xml')
      }
    },
    uploadXMLGDP () {
      try {
        this.loadingButton = true
        let file = this.$refs.fileInputFormatoGdp.files[0]
        let formData = new FormData()
        formData.append('file', file)
        formData.append('nombre', file.name)
        formData.append('nombre_archivo', file.name)
        formData.append('contrato_id', this.data_gdp.contrato_id)
        formData.append('folio_fiscal', '')
        formData.append('fecha_factura', '')
        formData.append('fecha_pago', '')
        formData.append('fecha_vencimiento', '')
        // console.log('aquillega')
        if (file === null || file === undefined || file === '') {
          this.$showMessage('Error', 'Seleccione un archivo')
        } else if (file.name) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('facturasContratos/uploadArchivo', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              if (response.data.result === 'success') {
                this.$q.notify({
                  message: response.data.message.content,
                  timeout: 3000,
                  type: 'green',
                  textColor: 'white', // if default 'white' doesn't fits
                  icon: 'done',
                  position: 'top-right' // 'top', 'left', 'bottom-left' etc
                })
                this.modal_abono = false
                this.filtrar()
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
    }
  },
  validations: {
    form: {
      fields: {
        fecha_abono: {required}
      }
    },
    form_retail: {
      fields: {
        fecha_abono: {required}
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
</style>
