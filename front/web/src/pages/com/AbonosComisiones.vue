<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Comisiones" />
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
          <div class="q-pa-sm q-gutter-sm">
            <q-btn @click="exportCSV()" color="green" icon="fas fa-file-excel"><q-tooltip>Generar CSV</q-tooltip></q-btn>
            <q-btn @click="exportCSV_aliados()" color="teal" icon="fas fa-file-excel" :disable="form.fields.fecha_inicio === null && form.fields.fecha_fin === null"><q-tooltip>Generar CSV pagos</q-tooltip></q-btn>
          </div>
        </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="work" icon-color="dark">
                  <q-select v-model="form.fields.proyecto_id_filtro" inverted color="dark" float-label="Proyecto" :options="recursosOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="person" icon-color="dark">
                  <q-select v-model="form.fields.aliado_id" inverted color="dark" float-label="Aliados" :options="aliadosOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="person" icon-color="dark">
                  <q-select v-model="form.fields.vendedor_id" inverted color="dark" float-label="Vendedores" :options="vendedoresOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="date_range" icon-color="dark">
                  <q-datetime v-model="form.fields.fecha_inicio" type="date" inverted color="dark" float-label="Fecha inicio" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="date_range" icon-color="dark">
                  <q-datetime v-model="form.fields.fecha_fin" type="date" inverted color="dark" float-label="Fecha fin" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-12 pull-right">
                <q-btn @click="filtrar()" inverted color="orange" icon="fas fa-filter" label="Filtrar" :loading="loadingButton">
                  <q-tooltip class="bg-orange">Filtrar</q-tooltip>
                </q-btn>
                <q-btn @click="loadAll()" inverted color="blue" icon="refresh" :loading="loadingButton">
                  <q-tooltip class="bg-blue">Recargar</q-tooltip>
                </q-btn>
                <q-btn @click="borrar()" inverted color="red" icon="loop" :loading="loadingButton">
                  <q-tooltip class="bg-red">Borrar</q-tooltip>
                </q-btn>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md" style="padding: 0">
            <div class="col q-pa-md ">
              <div class="col-sm-12">
                <q-search color="primary" v-model="form.filter"/>
              </div>
              <div  class="col-sm-12 q-mt-sm" id="sticky-table-scroll" >
                <q-table id="sticky-table-newstyle"
                  v-if="filtro_fecha === false"
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
                      <q-td key="proyecto" :props="props" style="cursor: pointer;">{{ props.row.proyecto }}</q-td>
                      <!-- <q-td key="proyecto" :props="props">{{ props.row.proyecto }}</q-td> -->
                      <q-td key="contrato" :props="props">{{ props.row.contrato }}</q-td>
                      <q-td key="asignado" :props="props" style="cursor: pointer;">{{ props.row.asignado }}</q-td>
                      <q-td key="aliado" :props="props" style="cursor: pointer;">{{ props.row.aliado }}</q-td>
                      <q-td key="metodo_pago" :props="props" style="cursor: pointer;">{{ props.row.metodo_pago }}</q-td>
                      <q-td key="tipo" :props="props">{{ props.row.tipo }}</q-td>
                      <q-td key="cantidad_com" :props="props" v-if="props.row.tipo === 'PORCENTAJE'">{{ props.row.cantidad_com }}%</q-td>
                      <q-td key="cantidad_com" :props="props" v-else>${{ currencyFormat(props.row.cantidad_com) }}</q-td>
                      <!-- <q-td key="monto_subtotal_comision" :props="props">${{ currencyFormat(props.row.monto_subtotal_comision) }}</q-td>
                      <q-td key="iva" :props="props">${{ currencyFormat(props.row.iva) }}</q-td> -->
                      <q-td key="monto_total_comision" :props="props">${{ currencyFormat(props.row.monto_total_comision) }}</q-td>
                      <q-td key="monto_total_abonado" :props="props">${{ currencyFormat(props.row.monto_total_abonado) }}</q-td>
                      <q-td key="monto_restante" :props="props">${{ currencyFormat(props.row.monto_restante) }}</q-td>
                      <q-td key="status" :props="props"><q-chip dense :icon="props.row.icon" :color="props.row.color" text-color="white">{{ props.row.status }}</q-chip></q-td>
                      <q-td key="observaciones" :props="props">{{ props.row.observaciones }}</q-td>
                      <q-td key="acciones" :props="props">
                        <q-btn small flat @click="abonar(props.row)" color="teal" icon="fas fa-dollar-sign" v-if="props.row.status!=='PAGADO'">
                          <q-tooltip>Abonar</q-tooltip>
                        </q-btn>
                        <q-btn small flat @click="getHistorial(props.row)" color="orange" icon="fas fa-calendar-alt">
                          <q-tooltip>Ver historial de abonos</q-tooltip>
                        </q-btn>
                      </q-td>
                    </q-tr>
                  </template>
                </q-table>
                <q-table id="sticky-table-newstyle"
                  v-else
                  :data="form.data"
                  :columns="form.columnsFecha"
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
                      <q-td key="proyecto" :props="props" style="cursor: pointer;">{{ props.row.proyecto }}</q-td>
                      <!-- <q-td key="proyecto" :props="props">{{ props.row.proyecto }}</q-td> -->
                      <q-td key="contrato" :props="props">{{ props.row.contrato }}</q-td>
                      <q-td key="aliado" :props="props" style="cursor: pointer;">{{ props.row.aliado }}</q-td>
                      <q-td key="metodo_pago" :props="props" style="cursor: pointer;">{{ props.row.metodo_pago }}</q-td>
                      <q-td key="tipo" :props="props">{{ props.row.tipo }}</q-td>
                      <q-td key="cantidad_com" :props="props" v-if="props.row.tipo === 'PORCENTAJE'">{{ props.row.cantidad_com }}%</q-td>
                      <q-td key="cantidad_com" :props="props" v-else>${{ currencyFormat(props.row.cantidad_com) }}</q-td>
                      <!-- <q-td key="monto_subtotal_comision" :props="props">${{ currencyFormat(props.row.monto_subtotal_comision) }}</q-td>
                      <q-td key="iva" :props="props">${{ currencyFormat(props.row.iva) }}</q-td> -->
                      <q-td key="monto_total_comision" :props="props">${{ currencyFormat(props.row.monto_total_comision) }}</q-td>
                      <q-td key="monto_total_abonado" :props="props">${{ currencyFormat(props.row.monto_total_abonado) }}</q-td>
                      <q-td key="single_abonado" :props="props">${{ currencyFormat(props.row.single_abonado) }}</q-td>
                      <q-td key="fecha" :props="props">{{ props.row.fecha }}</q-td>
                      <q-td key="monto_restante" :props="props">${{ currencyFormat(props.row.monto_restante) }}</q-td>
                      <q-td key="status" :props="props"><q-chip dense :icon="props.row.icon" :color="props.row.color" text-color="white">{{ props.row.status }}</q-chip></q-td>
                      <q-td key="observaciones" :props="props">{{ props.row.observaciones }}</q-td>
                      <q-td key="acciones" :props="props">
                        <q-btn small flat @click="abonar(props.row)" color="teal" icon="fas fa-dollar-sign" v-if="props.row.status!=='PAGADO'">
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
          </div>
        </div>
      </div>

      <q-modal no-backdrop-dismiss v-if="modal_abono" style="background-color: rgba(0,0,0,0.7);" v-model="modal_abono" :content-css="{width: '90vw', height: '480px'}">
        <q-modal-layout>
          <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
            <div class="col-sm-10">
              <q-toolbar-title>
                Comisión
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
            Información de la comisión
        </div>
        <div class="row q-mt-lg" style="margin-right:20px;">
          <div class="col-sm-2">
            <q-field icon="folder" icon-color="dark">
              <q-input readonly upper-case v-model="form.fields.codigo" inverted-color="dark" float-label="Código"/>
            </q-field>
          </div>
          <div class="col-sm-2">
            <q-field icon="work" icon-color="dark">
              <q-input readonly upper-case v-model="form.fields.proyecto" inverted-color="dark" float-label="Proyecto"/>
            </q-field>
          </div>
          <div class="col-sm-2">
            <q-field icon="fas fa-file" icon-color="dark">
              <q-input readonly upper-case v-model="form.fields.contrato" inverted-color="dark" float-label="Contrato"/>
            </q-field>
          </div>
          <div class="col-sm-2">
            <q-field  icon="fas fa-dollar-sign" icon-color="dark">
              <q-input readonly v-model.lazy="form.fields.subtotal_monto_bolsa" v-money="money" inverted-color="dark" float-label="Subtotal de la bolsa" suffix="MXN"></q-input>
            </q-field>
          </div>
          <div class="col-sm-2">
            <q-field icon="check" icon-color="dark">
              <q-input readonly v-model.lazy="form.fields.iva" v-money="money" inverted-color="dark" float-label="Iva" suffix="MXN"/>
            </q-field>
          </div>
          <div class="col-sm-2">
            <q-field  icon="fas fa-dollar-sign" icon-color="dark">
              <q-input readonly v-model.lazy="form.fields.monto_bolsa" v-money="money" inverted-color="dark" float-label="Monto de la bolsa(neto)" suffix="MXN"></q-input>
            </q-field>
          </div>
          <div class="col-sm-6">
            <q-field icon="fas fa-user" icon-color="dark">
              <q-input readonly upper-case v-model="form.fields.aliado" inverted-color="dark" float-label="Aliado"></q-input>
            </q-field>
          </div>
          <div class="col-sm-2">
            <q-field icon="fas fa-donate" icon-color="dark">
              <q-input readonly upper-case v-model="form.fields.tipo" inverted-color="dark" float-label="Tipo"></q-input>
            </q-field>
          </div>
          <div class="col-sm-2" v-if="form.fields.tipo === 'CANTIDAD FIJA'">
            <q-field icon="fas fa-dollar-sign" icon-color="dark">
              <q-input readonly v-model.lazy="form.fields.cantidad_com" v-money="money" inverted-color="dark" float-label="Monto comisión" suffix="MXN"></q-input>
            </q-field>
          </div>
          <div class="col-sm-2" v-else>
            <q-field icon="label" icon-color="dark">
              <q-input readonly upper-case v-model="form.fields.cantidad_com" type="text" inverted-color="dark" float-label="Porcentaje" suffix="%" maxlength="50"/>
            </q-field>
          </div>
          <div class="col-sm-2">
            <q-field icon="fas fa-dollar-sign" icon-color="dark">
              <q-input readonly v-model="form.fields.monto_restante_mostrar" type="text" inverted-color="dark" float-label="Monto restante a pagar" suffix="MXN"></q-input>
            </q-field>
          </div>
          <div class="col-sm-2">
            <q-field icon="request_page">
            <q-input readonly upper-case v-model="form.fields.metodo_pago" type="text" inverted-color="dark" float-label="Método de pago"/>
            </q-field>
          </div>
          <div class="col-sm-2">
            <q-field icon="style" icon-color="dark" error-label="Seleccione una opción">
              <q-input readonly upper-case v-model="form.fields.aplicado" type="text" inverted-color="dark" float-label="Aplicado"/>
            </q-field>
          </div>
          <div class="col-sm-2">
            <q-field icon="dns" icon-color="dark">
              <q-input readonly upper-case v-model="form.fields.tiempo_pago" type="text" inverted-color="dark" float-label="Tiempo pago"/>
            </q-field>
          </div>
          <div class="col-sm-6">
            <q-field icon="fas fa-pen-square" icon-color="dark">
              <q-input readonly upper-case v-model="form.fields.observaciones" inverted-color="dark" float-label="Observaciones"/>
            </q-field>
          </div>
        </div>
        <div class="row q-mt-lg subtitulos" style="margin-left:20px;">
          Cantidad a abonar
        </div>
        <div class="row q-mt-lg" style="margin-right:20px;">
          <div class="col-sm-3">
            <div class="icono_field">
              <q-field  icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form.fields.monto_abonar2.$error" error-label="Ingrese el monto ($1.00 - $1,000,000,000.00)">
                <q-input v-model.lazy="form.fields.monto_abonar" v-money="money" inverted color="dark" float-label="Monto a pagar" suffix="MXN" @keyup="quitaError()"></q-input>
              </q-field>
            </div>
          </div>
          <div class="col-sm-3">
            <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha_abono.$error" error-label="Ingrese una fecha">
              <q-datetime v-model="form.fields.fecha_abono" type="date" inverted color="dark" float-label="Fecha abono" align="center"></q-datetime>
            </q-field>
          </div>
          <div class="col-sm-3">
            <q-field icon="fas fa-donate" icon-color="dark" :error="$v.form.fields.metodo_pago_comision.$error" error-label="Elija un método de pago">
              <q-select v-model="form.fields.metodo_pago_comision" inverted color="dark" float-label="Método de pago" :options="metodoPagoComisionOptions" filter/>
            </q-field>
          </div>
          <div class="col-sm-3">
            <q-field icon="label" icon-color="dark">
              <q-input upper-case v-model="form.fields.transaccion" type="text" inverted color="dark" float-label="Transaccion" maxlenght=100></q-input>
            </q-field>
          </div>
          <div class="col-sm-12">
            <q-field icon="label" icon-color="dark">
              <q-input upper-case v-model="form.fields.observaciones_abono" type="text" inverted color="dark" float-label="Observaciones" maxlenght=1000></q-input>
            </q-field>
          </div>
        </div>
        <div class="row q-mt-lg" style="margin-right: 20px;">
          <div class="col-sm-12 pull-right">
              <q-btn @click="guardarAbono()" inverted color="green" icon-right="fas fa-dollar-sign" :loading="loadingButton">Abonar</q-btn>
          </div>
        </div>
        <div class="row q-mt-lg" style="margin-right:20px;">
        </div>
      </q-modal>

      <q-modal no-backdrop-dismiss v-if="modal_historial_abonos" style="background-color: rgba(0,0,0,0.7);" v-model="modal_historial_abonos" :content-css="{width: '80vw', height: '800px'}">
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
        <div class="row q-mt-sm" style="margin-right:20px;margin-top: 80px;">
        </div>
        <div class="row q-mt-sm" style="margin-right:20px;" v-if="editar_abono === true">
          <div class="col-sm-3">
            <div class="icono_field">
              <q-field  icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form.fields.monto_abonar2.$error" error-label="Ingrese el monto ($1.00 - $1,000,000,000.00)">
                <q-input v-model.lazy="form.fields.monto_abonar" v-money="money" inverted color="dark" float-label="Monto a pagar" suffix="MXN" @keyup="quitaError()"></q-input>
              </q-field>
            </div>
          </div>
          <div class="col-sm-3">
            <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha_abono.$error" error-label="Ingrese una fecha">
              <q-datetime v-model="form.fields.fecha_abono" type="date" inverted color="dark" float-label="Fecha abono" align="center"></q-datetime>
            </q-field>
          </div>
          <div class="col-sm-3">
            <q-field icon="fas fa-donate" icon-color="dark" :error="$v.form.fields.metodo_pago_comision.$error" error-label="Elija un método de pago">
              <q-select v-model="form.fields.metodo_pago_comision" inverted color="dark" float-label="Método de pago" :options="metodoPagoComisionOptions" filter/>
            </q-field>
          </div>
          <div class="col-sm-3">
            <q-field icon="label" icon-color="dark">
              <q-input upper-case v-model="form.fields.transaccion" type="text" inverted color="dark" float-label="Transaccion" maxlenght=100></q-input>
            </q-field>
          </div>
          <div class="col-sm-9">
            <q-field icon="label" icon-color="dark">
              <q-input upper-case v-model="form.fields.observaciones_abono" type="text" inverted color="dark" float-label="Observaciones" maxlenght=1000></q-input>
            </q-field>
          </div>
          <div class="col-sm-3 pull-right">
            <q-btn @click="guardarAbono()" inverted color="green" icon-right="fas fa-dollar-sign" :loading="loadingButton" v-if="editar_abono === false">Abonar</q-btn>
            <q-btn @click="actualizarAbono()" inverted color="orange" icon-right="fas fa-dollar-sign" :loading="loadingButton" v-if="editar_abono === true">Actualizar</q-btn>
          </div>
        </div>
        <div class="row q-mt-lg" style="margin-right:20px;">
        </div>
        <div class="row q-mt-sm" style="margin-left:20px;margin-right:20px;margin-bottom:20px;">
            <div  class="col-sm-12 q-mt-sm" id="sticky-table-scroll" >
              <q-table id="sticky-table"
                :data="form_historial.data"
                :columns="form_historial.columns"
                :selected.sync="form_historial.selected"
                :filter="form_historial.filter"
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
                    <q-td key="metodo_pago" :props="props">{{ props.row.metodo_pago }}</q-td>
                    <q-td key="observaciones" :props="props">{{ props.row.observaciones }}</q-td>
                    <q-td key="monto" :props="props">${{ currencyFormat(props.row.monto) }}</q-td>
                    <q-td key="documentos" :props="props">
                      <input id="archivo_com" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="comisionInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf, .txt, .TXT" @change="uploadComisionFile()" />
                      <q-btn small flat @click="selectedCom(props.row)" color="green-9" :loading="loadingButton">
                        <q-icon name="cloud_upload" />&nbsp;
                        <q-tooltip>Subir Archivo</q-tooltip>
                      </q-btn>
                      <div style="display: inline;" v-for="doc in props.row.documentos" :key="doc.id">
                        <q-btn small flat :icon="doc.icon" :color="doc.color">
                          <q-tooltip>{{ doc.nombre }}</q-tooltip>
                          <q-popover>
                            <q-list link separator class="scroll" style="min-width: 100px">
                              <q-item v-close-overlay @click.native="verDocumentosCom(doc.id, doc.extension)" v-if="doc.extension !== 'docx' && doc.extension !== 'xml' && doc.extension !== 'xlsx'">
                                <q-item-main label="Ver"/>
                              </q-item>
                              <q-item v-close-overlay @click.native="descargarDocumentoCom(doc.id, doc.extension)">
                                <q-item-main label="Descargar"/>
                              </q-item>
                              <q-item v-close-overlay @click.native="deleteDocumentoCom(doc.id)">
                                <q-item-main label="Eliminar"/>
                              </q-item>
                            </q-list>
                          </q-popover>
                        </q-btn>
                      </div>
                    </q-td>
                    <q-td key="acciones" :props="props">
                      <q-btn small flat @click="editRow(props.row)" color="blue" icon="edit">
                        <q-tooltip>Actualizar</q-tooltip>
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

          <div class="row q-mt-lg" style="margin-right:20px;">
          </div>
      </q-modal>
    </div>
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { required, maxValue, minValue, minLength } from 'vuelidate/lib/validators'
import {VMoney} from 'v-money'
import moment from 'moment'
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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'COBRANZA'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS/COMISIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'AUXILIAR TESORERIA'.toUpperCase()) {
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
      registro_comision: '',
      filtro_fecha: false,
      data_row: [],
      editar_abono: false,
      loadingButton: false,
      contratosOptions: [],
      modal_abono: false,
      modal_historial_abonos: false,
      historial: [],
      metodoPagoComisionOptions: [ { 'label': 'EFECTIVO', 'value': 'EFECTIVO' }, { 'label': 'TRANSFERENCIA', 'value': 'TRANSFERENCIA' }, { 'label': 'CHEQUE', 'value': 'CHEQUE' }, { 'label': '--Seleccione--', 'value': 'all' } ],
      facturasOptions: [],
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
          fecha_inicio: null,
          fecha_fin: null,
          id: 0,
          proyecto_id: 0,
          proyecto_id_filtro: 0,
          contrato: 0,
          porcentaje: 0,
          monto: 0,
          monto_bolsa: 0,
          metodo_pago: '',
          metodo_pago_comision: 'all',
          iva: '',
          aplicado: '',
          tipo: '',
          cantidad_com: '',
          tiempo_pago: '',
          observaciones: 0,
          observaciones_abono: '',
          aliado_id: 0,
          vendedor_id: 0,
          status: '',
          aliado: '',
          monto_total_comision: 0,
          utilidad: 0,
          codigo: '',
          proyecto: '',
          monto_abonar: 0,
          monto_abonar2: 0,
          comision_id: 0,
          // factura_id: 0,
          fecha_abono: '',
          // monto_factura: 0,
          transaccion: '',
          monto_total_abonado: 0,
          monto_restante: 0,
          monto_restante_mostrar: 0,
          subtota_monto_bolsa: 0,
          id_abono: 0,
          monto_auxiliar: 0
        },
        // data: [],
        columns: [
          { name: 'codigo', label: 'Código', field: 'codigo', sortable: true, type: 'string', align: 'left' },
          { name: 'proyecto', label: 'Proyecto', field: 'proyecto', sortable: true, type: 'string', align: 'center' },
          { name: 'contrato', label: 'Contrato', field: 'contrato', sortable: true, type: 'string', align: 'left' },
          { name: 'asignado', label: 'Asignado', field: 'asignado', sortable: true, type: 'string', align: 'left' },
          { name: 'aliado', label: 'Aliado/Vendedor', field: 'aliado', sortable: true, type: 'string', align: 'left' },
          { name: 'metodo_pago', label: 'Método de pago', field: 'metodo_pago', sortable: true, type: 'string', align: 'left' },
          { name: 'tipo', label: 'Tipo', field: 'tipo', sortable: true, type: 'string', align: 'left' },
          { name: 'cantidad_com', label: 'Cantidad / porcentaje', field: 'cantidad_com', sortable: true, type: 'string', align: 'right' },
          // { name: 'monto_subtotal_comision', label: 'Subtotal', field: 'monto_subtotal_comision', sortable: true, type: 'string', align: 'right' },
          // { name: 'iva', label: 'IVA', field: 'iva', sortable: true, type: 'string', align: 'right' },
          { name: 'monto_total_comision', label: 'Monto total', field: 'monto_total_comision', sortable: true, type: 'string', align: 'right' },
          { name: 'monto_total_abonado', label: 'Monto abonado', field: 'monto_total_abonado', sortable: true, type: 'string', align: 'right' },
          { name: 'monto_restante', label: 'Monto restante', field: 'monto_restante', sortable: true, type: 'string', align: 'right' },
          { name: 'status', label: 'Status', field: 'status', sortable: true, type: 'string', align: 'left' },
          { name: 'observaciones', label: 'Observaciones', field: 'observaciones', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'center' }
        ],
        columnsFecha: [
          { name: 'codigo', label: 'Código', field: 'codigo', sortable: true, type: 'string', align: 'left' },
          { name: 'proyecto', label: 'Proyecto', field: 'proyecto', sortable: true, type: 'string', align: 'center' },
          { name: 'contrato', label: 'Contrato', field: 'contrato', sortable: true, type: 'string', align: 'left' },
          { name: 'aliado', label: 'Aliado', field: 'aliado', sortable: true, type: 'string', align: 'left' },
          { name: 'metodo_pago', label: 'Método de pago', field: 'metodo_pago', sortable: true, type: 'string', align: 'left' },
          { name: 'tipo', label: 'Tipo', field: 'tipo', sortable: true, type: 'string', align: 'left' },
          { name: 'cantidad_com', label: 'Cantidad / porcentaje', field: 'cantidad_com', sortable: true, type: 'string', align: 'right' },
          // { name: 'monto_subtotal_comision', label: 'Subtotal', field: 'monto_subtotal_comision', sortable: true, type: 'string', align: 'right' },
          // { name: 'iva', label: 'IVA', field: 'iva', sortable: true, type: 'string', align: 'right' },
          { name: 'monto_total_comision', label: 'Monto total', field: 'monto_total_comision', sortable: true, type: 'string', align: 'right' },
          { name: 'monto_total_abonado', label: 'Total abonado', field: 'monto_total_abonado', sortable: true, type: 'string', align: 'right' },
          { name: 'single_abonado', label: 'Monto abonado', field: 'single_abonado', sortable: true, type: 'string', align: 'right' },
          { name: 'fecha', label: 'Fecha del abono', field: 'fecha', sortable: true, type: 'string', align: 'right' },
          { name: 'monto_restante', label: 'Monto restante', field: 'monto_restante', sortable: true, type: 'string', align: 'right' },
          { name: 'status', label: 'Status', field: 'status', sortable: true, type: 'string', align: 'left' },
          { name: 'observaciones', label: 'Observaciones', field: 'observaciones', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'center' }
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
          { name: 'transaccion', label: 'Transacción', field: 'transaccion', sortable: true, type: 'string', align: 'center' },
          { name: 'metodo_pago', label: 'Método pago', field: 'metodo_pago', sortable: true, type: 'string', align: 'left' },
          { name: 'observaciones', label: 'Observaciones', field: 'observaciones', sortable: true, type: 'string', align: 'left' },
          { name: 'monto', label: 'Monto', field: 'monto', sortable: true, type: 'string', align: 'right' },
          { name: 'documentos', label: 'Documentos', field: 'documentos', sortable: true, type: 'string', align: 'right' },
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
      estados: 'vnt/estado/estados'
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
    aliadosOptions () {
      let aliados = this.$store.getters['com/aliados/selectOptions']
      aliados.splice(0, 0, {label: '---Seleccione---', value: 0})
      return aliados
    },
    vendedoresOptions () {
      let aliados = this.$store.getters['crm/vendedores/selectOptions']
      aliados.splice(0, 0, {label: '---Seleccione---', value: 0})
      return aliados
    }
  },
  created () {
    this.loadAll()
  },
  methods: {
    ...mapActions({
      getRecursos: 'vnt/recurso/refresh',
      getAliados: 'com/aliados/refresh',
      getComisiones: 'com/abonosComisiones/filtrar',
      saveAbono: 'com/abonosComisiones/save',
      updateAbono: 'com/abonosComisiones/update',
      historialAbonos: 'com/abonosComisiones/historialAbonos',
      deleteAbono: 'com/abonosComisiones/delete',
      deleteDocumentoComision: 'com/documentos_abonos/deleteFormato',
      getVendedores: 'crm/vendedores/refresh'
    }),
    async loadAll () {
      this.form.loading = true
      this.fecha_filtro = false
      await this.getVendedores()
      await this.getRecursos()
      await this.getAliados()
      await this.filtrar()
      // await this.getFacturasOpt()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    editRow (row) {
      this.form.fields.id_abono = row.id
      this.form.fields.monto_abonar = row.monto
      this.form.fields.fecha_abono = moment(String(row.fecha)).format('YYYY-MM-DD HH:mm:ss')// row.fecha
      this.form.fields.metodo_pago_comision = row.metodo_pago
      this.form.fields.observaciones_abono = row.observaciones
      this.editar_abono = true
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
          this.filtrar()
          // this.getHistorial(this.form.fields.comision_id)
          this.modal_historial_abonos = false
          this.loadAll()
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
      this.setView('create')
    },
    filtrar () {
      if (this.form.fields.fecha_inicio !== null && this.form.fields.fecha_fin !== null) {
        this.filtro_fecha = true
      }
      this.getComisiones({proyecto_id: this.form.fields.proyecto_id_filtro, aliado_id: this.form.fields.aliado_id, fecha_inicio: this.form.fields.fecha_inicio, fecha_fin: this.form.fields.fecha_fin, vendedor_id: this.form.fields.vendedor_id}).then(({data}) => {
        if (data.comisiones.length > 0) {
          data.comisiones.forEach(function (element) {
            if (element.status === 'NUEVO') {
              element.color = 'amber'
              element.icon = 'fas fa-arrow-circle-right'
            } else if (element.status === 'ABONADO') {
              element.color = 'blue'
              element.icon = 'fas fa-check-circle'
            } else if (element.status === 'PAGADO') {
              element.color = 'green'
              element.icon = 'done_all'
            }
          })
          this.form.data = data.comisiones
        } else {
          this.form.data = []
          this.$showMessage('Advertencia', 'No existen comisiones para este proyecto')
        }
      }).catch(error => {
        console.error(error)
      })
    },
    abonar (row) {
      this.form.fields.id = row.id
      this.form.fields.comision_id = row.id
      this.form.fields.proyecto_id = row.proyecto_id
      this.form.fields.contrato = row.contrato
      this.form.fields.porcentaje = row.porcentaje
      this.form.fields.monto = row.monto
      this.form.fields.monto_bolsa = row.monto_bolsa
      this.form.fields.metodo_pago = row.metodo_pago
      this.form.fields.iva = row.iva
      this.form.fields.aplicado = row.aplicado
      this.form.fields.tipo = row.tipo
      this.form.fields.cantidad_com = row.cantidad_com
      this.form.fields.tiempo_pago = row.tiempo_pago
      this.form.fields.observaciones = row.observaciones
      this.form.fields.status = row.status
      this.form.fields.aliado = row.aliado
      this.form.fields.monto_total_comision = row.monto_total_comision
      this.form.fields.utilidad = row.utilidad
      this.form.fields.codigo = row.codigo
      this.form.fields.proyecto = row.proyecto
      this.form.fields.monto_abonar = 0
      this.form.fields.monto_abonar2 = 0
      this.form.fields.subtotal_monto_bolsa = row.subtotal_monto_bolsa
      // this.form.fields.factura_id = row.factura_id
      this.form.fields.fecha_abono = ''
      this.form.fields.transaccion = ''
      this.form.fields.monto_total_abonado = row.monto_total_abonado
      this.form.fields.monto_restante = row.monto_restante
      this.form.fields.monto_restante_mostrar = row.monto_restante_mostrar
      this.form.fields.metodo_pago_comision = 'all'
      this.form.fields.observaciones_abono = ''
      this.$v.form.$reset()
      this.modal_abono = true
    },
    abonarReadonly (row) {
      this.form.fields.id = row.id
      this.form.fields.comision_id = row.id
      this.form.fields.proyecto_id = row.proyecto_id
      this.form.fields.contrato = row.contrato
      this.form.fields.porcentaje = row.porcentaje
      this.form.fields.monto = row.monto
      this.form.fields.monto_bolsa = row.monto_bolsa
      this.form.fields.metodo_pago = row.metodo_pago
      this.form.fields.iva = row.iva
      this.form.fields.aplicado = row.aplicado
      this.form.fields.tipo = row.tipo
      this.form.fields.cantidad_com = row.cantidad_com
      this.form.fields.tiempo_pago = row.tiempo_pago
      this.form.fields.observaciones = row.observaciones
      this.form.fields.status = row.status
      this.form.fields.aliado = row.aliado
      this.form.fields.monto_total_comision = row.monto_total_comision
      this.form.fields.utilidad = row.utilidad
      this.form.fields.codigo = row.codigo
      this.form.fields.proyecto = row.proyecto
      this.form.fields.monto_abonar = 0
      this.form.fields.monto_abonar2 = 0
      this.form.fields.subtotal_monto_bolsa = row.subtotal_monto_bolsa
      // this.form.fields.factura_id = row.factura_id
      this.form.fields.fecha_abono = ''
      this.form.fields.transaccion = ''
      this.form.fields.monto_total_abonado = row.monto_total_abonado
      this.form.fields.monto_restante = row.monto_restante
      this.form.fields.monto_restante_mostrar = row.monto_restante_mostrar
      this.form.fields.metodo_pago_comision = 'all'
      this.$v.form.$reset()
    },
    guardarAbono () {
      this.form.fields.monto_abonar2 = this.evaluaMonto(this.form.fields.monto_abonar)
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        if (parseFloat(this.form.fields.monto_restante + 1) < this.form.fields.monto_abonar2) {
          this.$showMessage('¡Advertencia!', 'No se puede abonar una cantidad mayor al monto restante.')
        } else {
          let params = this.form.fields
          params.monto_abonar_validado = this.form.fields.monto_abonar2
          this.saveAbono(params).then(({data}) => {
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'check',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
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
    actualizarAbono () {
      this.form.fields.monto_abonar2 = this.evaluaMonto(this.form.fields.monto_abonar)
      this.form.fields.monto_auxiliar = this.form.fields.monto_abonar2
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        if (parseFloat((this.form.fields.monto_restante + 1) + this.form.fields.monto_auxiliar) < this.form.fields.monto_abonar2) {
          this.$showMessage('¡Advertencia!', 'No se puede abonar una cantidad mayor al monto restante.')
        } else {
          let params = this.form.fields
          params.monto_abonar_validado = this.form.fields.monto_abonar2
          this.updateAbono(params).then(({data}) => {
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'check',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.filtrar()
            this.modal_abono = false
            this.getHistorial(this.data_row)
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
      this.data_row = row
      this.editar_abono = false
      this.abonarReadonly(row)
      this.form.fields.id = row.id
      this.historialAbonos({comision_id: row.id}).then(({data}) => {
        if (data.abonos.length > 0) {
          data.abonos.forEach(function (element) {
            element.fecha = moment(String(element.fecha)).utcOffset('-05:00:00', false).format('YYYY-MM-DD')
          })
          this.historial = data.abonos
          this.form_historial.loading = true
          this.form_historial.data = data.abonos
          this.form_historial.loading = false
          this.modal_historial_abonos = true
        } else {
          this.form_historial.data = []
          this.modal_historial_abonos = true
          this.$q.notify({
            // only required parameter is the message:
            message: 'No hay abonos registrados para esta comisión.',
            timeout: 3000,
            type: 'orange',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'warning',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
        }
        // this.$showMessage('Advertencia', 'No hay abonos registrados para esta comisión.')
      }).catch(error => {
        console.error(error)
      })
    },
    quitaError () {
      this.$v.form.$reset()
    },
    exportCSV () {
      let uri = process.env.API + `abonosComisiones/exportCSV_comisiones/${this.form.fields.proyecto_id_filtro}/${this.form.fields.aliado_id}/${this.form.fields.fecha_inicio}/${this.form.fields.fecha_fin}/${this.form.fields.vendedor_id}`
      window.open(uri, '_blank')
    },
    exportCSV_aliados () {
      let uri = process.env.API + `abonosComisiones/exportCSV_pagos/${this.form.fields.proyecto_id_filtro}/${this.form.fields.aliado_id}/${this.form.fields.fecha_inicio}/${this.form.fields.fecha_fin}/${this.form.fields.vendedor_id}`
      window.open(uri, '_blank')
    },
    borrar () {
      this.form.fields.proyecto_id_filtro = 0
      this.form.fields.aliado_id = 0
      this.form.fields.vendedor_id = 0
      this.form.fields.fecha_inicio = null
      this.form.fields.fecha_fin = null
      this.form.filter = ''
    },
    selectedCom (row) {
      this.$refs.comisionInput.value = ''
      this.registro_comision = row
      this.$refs.comisionInput.click()
    },
    uploadComisionFile () {
      try {
        let file = this.$refs.comisionInput.files[0]
        let formData = new FormData()
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('abono_id', this.registro_comision.id)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('documentos_abonos/uploadDocumento', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              console.log(response)
              if (response.data.result === 'success') {
                this.$q.notify({
                  // only required parameter is the message:
                  message: 'Se ha cargado el archivo',
                  timeout: 3000,
                  type: 'green',
                  textColor: 'white', // if default 'white' doesn't fits
                  icon: 'done',
                  position: 'top-right' // 'top', 'left', 'bottom-left' etc
                })
                this.historialAbonos({comision_id: this.data_row.id}).then(({data}) => {
                  if (data.abonos.length > 0) {
                    this.historial = data.abonos
                    this.form_historial.loading = true
                    this.form_historial.data = data.abonos
                    this.form_historial.loading = false
                    this.modal_historial_abonos = true
                  } else {
                    this.form_historial.data = []
                    this.modal_historial_abonos = true
                    this.$q.notify({
                      // only required parameter is the message:
                      message: 'No hay abonos registrados para esta comisión.',
                      timeout: 3000,
                      type: 'orange',
                      textColor: 'white', // if default 'white' doesn't fits
                      icon: 'warning',
                      position: 'top-right' // 'top', 'left', 'bottom-left' etc
                    })
                  }
                  // this.$showMessage('Advertencia', 'No hay abonos registrados para esta comisión.')
                }).catch(error => {
                  console.error(error)
                })
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
    verDocumentosCom (id, ext) {
      let uri = process.env.API + '/public/assets/abonos_comisiones/' + id + '.' + ext
      window.open(uri, '_blank')
    },
    descargarDocumentoCom (id, ext) {
      let uri = process.env.API + `documentos_abonos/getFile/${id}/${ext}`
      window.open(uri, '_blank')
    },
    deleteDocumentoCom (id) {
      this.$q.dialog({
        title: 'Confirmar acción',
        message: '¿Desea eliminar el archivo?',
        ok: 'Eliminar',
        cancel: 'Cancelar',
        preventClose: true
      }).then(() => {
        this.delete_documento_com(id)
      }).catch(() => {
      })
    },
    delete_documento_com (id) {
      this.deleteDocumentoComision({id: id}).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'delete', position: 'top-right'
          })
          this.historialAbonos({comision_id: this.data_row.id}).then(({data}) => {
            if (data.abonos.length > 0) {
              this.historial = data.abonos
              this.form_historial.loading = true
              this.form_historial.data = data.abonos
              this.form_historial.loading = false
              this.modal_historial_abonos = true
            } else {
              this.form_historial.data = []
              this.modal_historial_abonos = true
              this.$q.notify({
                // only required parameter is the message:
                message: 'No hay abonos registrados para esta comisión.',
                timeout: 3000,
                type: 'orange',
                textColor: 'white', // if default 'white' doesn't fits
                icon: 'warning',
                position: 'top-right' // 'top', 'left', 'bottom-left' etc
              })
            }
            // this.$showMessage('Advertencia', 'No hay abonos registrados para esta comisión.')
          }).catch(error => {
            console.error(error)
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
        monto_abonar2: {required, minValue: minValue(1), maxValue: maxValue(1000000000)},
        metodo_pago_comision: {required, minLength: minLength(4)},
        fecha_abono: {required}
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
</style>
