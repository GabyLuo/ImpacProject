<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Proveedores"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn class="btn_nuevo" icon="add" label="Nuevo" @click="newRow()" />
              <q-btn @click="exportCSV()" color="green" icon="fas fa-file-excel"><q-tooltip>Generar reporte CSV</q-tooltip></q-btn>
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
                  :data="proveedor"
                  :columns="form.columns"
                  :selected.sync="form.selected"
                  :filter="form.filter"
                  color="positive"
                  title=""
                  :pagination.sync="form.pagination"
                  :loading="form.loading"
                  :rows-per-page-options="form.rowsOptions"
                  >
                  <template slot="body" slot-scope="props">
                    <q-tr :props="props">
                      <q-td key="id" :props="props">{{ props.row.id }}</q-td>
                      <q-td key="nombre_comercial" :props="props" @click.native="clickFila(props.row)" style="cursor: pointer;">{{ props.row.nombre_comercial }}</q-td>
                      <q-td key="razon_social" :props="props">{{ props.row.razon_social }}</q-td>
                      <q-td key="rfc" :props="props">{{ props.row.rfc }}</q-td>
                      <q-td key="curp" :props="props">{{ props.row.curp }}</q-td>
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

      <q-modal v-if="informacion" style="background-color: rgba(0,0,0,0.7);" v-model="informacion" :content-css="{width: '90vw', height: '600px'}">
        <q-modal-layout>
          <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
            <div class="col-sm-6">
              <q-toolbar-title>
                Información
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
          <div class="row q-mt-lg subtitulos" style="margin-left: 15px;">
            Datos generales
          </div>
          <div class="row q-mt-lg" style="margin-right: 15px;">
            <div class="col-sm-6">
              <q-field icon="fas fa-building" icon-color="dark">
                <q-input readonly upper-case v-model="form.fields.nombre_comercial" inverted-color="dark" float-label="Nombre Comercial"/>
              </q-field>
            </div>
            <div class="col-sm-6">
              <q-field icon="fas fa-building" icon-color="dark">
                <q-input readonly upper-case v-model="form.fields.razon_social" inverted-color="dark" float-label="Razón social"/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right: 15px;">
            <div class="col-sm-6">
              <q-field icon="fas fa-id-card" icon-color="dark">
                <q-input readonly upper-case v-model="form.fields.rfc" inverted-color="dark" float-label="RFC"/>
              </q-field>
            </div>
            <div class="col-sm-6">
              <q-field icon="fas fa-id-badge" icon-color="dark">
                <q-input readonly upper-case v-model="form.fields.curp" inverted-color="dark" float-label="CURP"/>
              </q-field>
            </div>
          </div>
           <div class="row q-mt-lg" style="margin-right: 15px;">
            <div class="col-sm-12">
              <q-field icon="fas fa-address-book" icon-color="dark">
                <q-input readonly upper-case v-model="form.fields.direccion" inverted-color="dark" float-label="Dirección"/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right: 15px;">
            <div class="col-sm-6">
              <q-field icon="contact_phone" icon-color="dark">
                <q-input readonly @keyup="isNumber($event,'telefono')" upper-case v-model="form.fields.telefono" inverted-color="dark" float-label="Telefono"/>
              </q-field>
            </div>
            <div class="col-sm-6">
              <q-field icon="person" icon-color="dark">
                <q-input readonly upper-case v-model="form.fields.contacto" inverted-color="dark" float-label="Contacto"/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg subtitulos" style="margin-left: 15px;">
            Datos bancarios
          </div>
          <div class="row q-mt-lg" style="margin-right: 15px;">
            <div class="col-sm-6">
              <q-field icon="account_balance" icon-color="dark">
                <q-input readonly upper-case v-model="form.fields.banco" inverted-color="dark" float-label="Banco"/>
              </q-field>
            </div>
            <div class="col-sm-6">
              <q-field icon="vpn_key" icon-color="dark">
                <q-input readonly @keyup="isNumber($event,'clabe1')" upper-case v-model="form.fields.clabe" inverted-color="dark" float-label="CLABE"/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right: 15px;">
            <div class="col-sm-6">
              <q-field icon="account_balance" icon-color="dark">
                <q-input readonly upper-case v-model="form.fields.banco2" inverted-color="dark" float-label="Banco 2"/>
              </q-field>
            </div>
            <div class="col-sm-6">
              <q-field icon="vpn_key" icon-color="dark">
                <q-input readonly @keyup="isNumber($event,'clabe2')" upper-case v-model="form.fields.clabe2" inverted-color="dark" float-label="CLABE 2"/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right: 15px;">
            <div class="col-sm-6">
              <q-field icon="account_balance" icon-color="dark">
                <q-input readonly upper-case v-model="form.fields.banco3" inverted-color="dark" float-label="Banco"/>
              </q-field>
            </div>
            <div class="col-sm-6">
              <q-field icon="vpn_key" icon-color="dark">
                <q-input readonly @keyup="isNumber($event,'clabe3')" upper-case v-model="form.fields.clabe3" inverted-color="dark" float-label="CLABE"/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right: 15px;">
            <div class="col-sm-6">
              <q-field icon="account_balance" icon-color="dark">
                <q-input readonly upper-case v-model="form.fields.banco4" inverted-color="dark" float-label="Banco"/>
              </q-field>
            </div>
            <div class="col-sm-6">
              <q-field icon="vpn_key" icon-color="dark">
                <q-input readonly @keyup="isNumber($event,'clabe4')" upper-case v-model="form.fields.clabe4" inverted-color="dark" float-label="CLABE"/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right: 15px;">
            <div class="col-sm-6">
              <q-field icon="account_balance" icon-color="dark">
                <q-input readonly upper-case v-model="form.fields.banco" inverted-color="dark" float-label="Banco"/>
              </q-field>
            </div>
            <div class="col-sm-6">
              <q-field icon="vpn_key" icon-color="dark">
                <q-input readonly @keyup="isNumber($event,'clabe1')" upper-case v-model="form.fields.clabe" inverted-color="dark" float-label="CLABE"/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right: 15px;">
            <div class="col-sm-6">
              <q-field icon="vpn_key" icon-color="dark">
                <q-input readonly @keyup="isNumber($event,'toka')" upper-case v-model="form.fields.toka" inverted-color="dark" float-label="TOKA"/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg subtitulos" style="margin-left: 15px;">
            Expediente
          </div>
          <div class="row q-mt-lg" style="margin-right: 15px; margin-bottom: 20px;">
            <q-field icon="fas fa-id-badge" icon-color="white" >
              <q-btn @click="abrirDocumento()" class="btn_guardar" icon-right="visibility" :loading="loadingButton">Ver Expediente</q-btn>
            </q-field>
          </div>
        </q-modal-layout>
      </q-modal>

    <!--Crear un proveedor-->

    <div v-if="views.create">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Proveedores" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el label="Nuevo proveedor"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
                <div class="q-pa-sm q-gutter-sm">
                  <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" />
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
                  <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
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
            <div class="row q-pa-md">
                Datos generales
            </div>
            <div class="row q-col-gutter-xs">
                <div class="col-sm-6">
                  <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fields.nombre_comercial.$error" error-label="Escriba un nombre">
                    <q-input upper-case v-model="form.fields.nombre_comercial" inverted color="dark" float-label="Nombre Comercial"/>
                  </q-field>
                </div>
                <div class="col-sm-6">
                  <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fields.razon_social.$error" error-label="Ingrese la razón social">
                    <q-input upper-case v-model="form.fields.razon_social" inverted color="dark" float-label="Razón social"/>
                  </q-field>
                </div>
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-4">
                  <q-field icon="fas fa-id-card" icon-color="dark" :error="$v.form.fields.rfc.$error" error-label="Escriba un RFC">
                    <q-input upper-case v-model="form.fields.rfc" inverted color="dark" float-label="RFC"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="fas fa-id-badge" icon-color="dark">
                    <q-input upper-case v-model="form.fields.curp" inverted color="dark" float-label="CURP"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="fas fa-user" icon-color="dark">
                    <q-select v-model="form.fields.tipo" inverted color="dark" float-label="Tipo" :options="tipoOptions" filter/>
                  </q-field>
                </div>
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-12">
                  <q-field icon="fas fa-address-book" icon-color="dark" :error="$v.form.fields.direccion.$error" error-label="Escriba una dirección">
                    <q-input upper-case v-model="form.fields.direccion" inverted color="dark" float-label="Dirección"/>
                  </q-field>
                </div>
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-4">
                  <q-field icon="contact_phone" icon-color="dark" :error="$v.form.fields.telefono.$error" error-label="Verifique el número telefónico">
                    <q-input  @keyup="isNumber($event,'telefono')" upper-case v-model="form.fields.telefono" inverted color="dark" float-label="Telefono"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="person" icon-color="dark" :error="$v.form.fields.contacto.$error" error-label="Ingrese el Contacto">
                    <q-input upper-case v-model="form.fields.contacto" inverted color="dark" float-label="Contacto"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="email" icon-color="dark" :error="$v.form.fields.email.$error" error-label="Por favor ingrese un email válido">
                    <q-input v-model="form.fields.email" type="email" inverted color="dark" float-label="Correo electrónico" maxlength="100" />
                  </q-field>
                </div>
              </div>
              <div class="row q-pa-md">
                Datos bancarios
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-4">
                  <q-field icon="account_balance" icon-color="dark" :error="$v.form.fields.banco.$error" error-label="Escriba el nombre del banco">
                    <q-input upper-case v-model="form.fields.banco" inverted color="dark" float-label="Banco"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="vpn_key" icon-color="dark">
                    <q-input  @keyup="isNumber($event,'clabe1')" upper-case v-model="form.fields.clabe" inverted color="dark" float-label="CLABE"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="vpn_key" icon-color="dark" :error="$v.form.fields.tarjeta1.$error" error-label="Verifique el número de tarjeta">
                    <q-input  @keyup="isNumber($event,'tarjeta1')" upper-case v-model="form.fields.tarjeta1" inverted color="dark" float-label="Tarjeta"/>
                  </q-field>
                </div>
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-4">
                  <q-field icon="account_balance" icon-color="dark" :error="$v.form.fields.banco2.$error" error-label="Escriba el nombre del Banco">
                    <q-input upper-case v-model="form.fields.banco2" inverted color="dark" float-label="Banco 2"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="vpn_key" icon-color="dark" :error="$v.form.fields.clabe2.$error" error-label="Verifique el número clabe">
                    <q-input  @keyup="isNumber($event,'clabe2')" upper-case v-model="form.fields.clabe2" inverted color="dark" float-label="CLABE 2"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="vpn_key" icon-color="dark" :error="$v.form.fields.tarjeta2.$error" error-label="Verifique el número de tarjeta">
                    <q-input  @keyup="isNumber($event,'tarjeta2')" upper-case v-model="form.fields.tarjeta2" inverted color="dark" float-label="Tarjeta"/>
                  </q-field>
                </div>
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-4">
                  <q-field icon="account_balance" icon-color="dark" :error="$v.form.fields.banco3.$error" error-label="Escriba el nombre del Banco">
                    <q-input upper-case v-model="form.fields.banco3" inverted color="dark" float-label="Banco 3"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="vpn_key" icon-color="dark" :error="$v.form.fields.clabe3.$error" error-label="Verifique el número clabe">
                    <q-input  @keyup="isNumber($event,'clabe3')" upper-case v-model="form.fields.clabe3" inverted color="dark" float-label="CLABE 3"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="vpn_key" icon-color="dark" :error="$v.form.fields.tarjeta3.$error" error-label="Verifique el número de tarjeta">
                    <q-input  @keyup="isNumber($event,'tarjeta3')" upper-case v-model="form.fields.tarjeta3" inverted color="dark" float-label="Tarjeta"/>
                  </q-field>
                </div>
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-4">
                  <q-field icon="account_balance" icon-color="dark" :error="$v.form.fields.banco4.$error" error-label="Escriba el nombre del Banco">
                    <q-input upper-case v-model="form.fields.banco4" inverted color="dark" float-label="Banco 4"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="vpn_key" icon-color="dark" :error="$v.form.fields.clabe4.$error" error-label="Verifique el número clabe">
                    <q-input  @keyup="isNumber($event,'clabe4')" upper-case v-model="form.fields.clabe4" inverted color="dark" float-label="CLABE 4"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="vpn_key" icon-color="dark" :error="$v.form.fields.tarjeta4.$error" error-label="Verifique el número de tarjeta">
                    <q-input  @keyup="isNumber($event,'tarjeta4')" upper-case v-model="form.fields.tarjeta4" inverted color="dark" float-label="Tarjeta"/>
                  </q-field>
                </div>
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-6">
                  <q-field icon="vpn_key" icon-color="dark" :error="$v.form.fields.toka.$error" error-label="Verifique el número de la tarjeta toka">
                    <q-input @keyup="isNumber($event,'toka')" upper-case v-model="form.fields.toka" inverted color="dark" float-label="TOKA"/>
                  </q-field>
                </div>
              </div>
              <div class="row q-pa-md">
                Expediente
              </div>
              <div class="row q-col-gutter-xs">
                <q-field icon="fas fa-id-badge" icon-color="white" >
                  <input style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="fileInputFormato" accept=".pdf,.docx" @change="uploadFormato()" />
                  <q-btn @click="$refs.fileInputFormato.click()" class="btn_atach" icon-right="attach_file" :loading="loadingButton">Adjuntar Expediente</q-btn>
                </q-field>
                <q-field icon="fas fa-id-badge" icon-color="white" >
                  <q-btn @click="abrirDocumento()" class="btn_guardar" icon-right="visibility" :loading="loadingButton">Ver Expediente</q-btn>
                </q-field>
              </div>
            <!-- </div> -->
          </div>
        </div>
      </div>
    </div>

      <!--Editar un proveedor-->

    <div v-if="views.update">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Proveedores" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el :label="form.fields.rfc"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
                <div class="q-pa-sm q-gutter-sm">
                  <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" />
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
                  <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
                </div>
              </div>
              <div class="col-sm-6 pull-right">
                <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
                  <q-btn @click="update()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
                </div>
              </div>
            </div>
          </div> -->
          <div class="col q-pa-md">
              <div class="row q-pa-md">
                Datos generales
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-6">
                  <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fields.nombre_comercial.$error" error-label="Escriba un nombre">
                    <q-input upper-case v-model="form.fields.nombre_comercial" inverted color="dark" float-label="Nombre Comercial"/>
                  </q-field>
                </div>
                <div class="col-sm-6">
                  <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fields.razon_social.$error" error-label="Ingrese la razón social">
                    <q-input upper-case v-model="form.fields.razon_social" inverted color="dark" float-label="Razón social"/>
                  </q-field>
                </div>
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-4">
                  <q-field icon="fas fa-id-card" icon-color="dark" :error="$v.form.fields.rfc.$error" error-label="Escriba un RFC">
                    <q-input upper-case v-model="form.fields.rfc" inverted color="dark" float-label="RFC"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="fas fa-id-badge" icon-color="dark">
                    <q-input upper-case v-model="form.fields.curp" inverted color="dark" float-label="CURP"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="fas fa-user" icon-color="dark">
                    <q-select v-model="form.fields.tipo" inverted color="dark" float-label="Tipo" :options="tipoOptions" filter/>
                  </q-field>
                </div>
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-12">
                  <q-field icon="fas fa-address-book" icon-color="dark" :error="$v.form.fields.direccion.$error" error-label="Escriba una dirección">
                    <q-input upper-case v-model="form.fields.direccion" inverted color="dark" float-label="Dirección"/>
                  </q-field>
                </div>
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-4">
                  <q-field icon="contact_phone" icon-color="dark" :error="$v.form.fields.telefono.$error" error-label="Verifique el número Telefónico">
                    <q-input  @keyup="isNumber($event,'telefono')" upper-case v-model="form.fields.telefono" inverted color="dark" float-label="Telefono"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="person" icon-color="dark" :error="$v.form.fields.contacto.$error" error-label="Ingrese el Contacto">
                    <q-input upper-case v-model="form.fields.contacto" inverted color="dark" float-label="Contacto"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="email" icon-color="dark" :error="$v.form.fields.email.$error" error-label="Por favor ingrese un email válido">
                    <q-input v-model="form.fields.email" type="email" inverted color="dark" float-label="Correo electrónico" maxlength="100" />
                  </q-field>
                </div>
              </div>
              <div class="row q-pa-md">
                Datos bancarios
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-4">
                  <q-field icon="account_balance" icon-color="dark" :error="$v.form.fields.banco.$error" error-label="Escriba el nombre del Banco">
                    <q-input upper-case v-model="form.fields.banco" inverted color="dark" float-label="Banco"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="vpn_key" icon-color="dark">
                    <q-input  @keyup="isNumber($event,'clabe1')" upper-case v-model="form.fields.clabe" inverted color="dark" float-label="CLABE"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="vpn_key" icon-color="dark" :error="$v.form.fields.tarjeta1.$error" error-label="Verifique el número de tarjeta">
                    <q-input  @keyup="isNumber($event,'tarjeta1')" upper-case v-model="form.fields.tarjeta1" inverted color="dark" float-label="Tarjeta"/>
                  </q-field>
                </div>
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-4">
                  <q-field icon="account_balance" icon-color="dark" :error="$v.form.fields.banco2.$error" error-label="Escriba el nombre del Banco">
                    <q-input upper-case v-model="form.fields.banco2" inverted color="dark" float-label="Banco 2"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="vpn_key" icon-color="dark"  :error="$v.form.fields.clabe2.$error" error-label="Verifique el número clabe">
                    <q-input  @keyup="isNumber($event,'clabe2')" upper-case v-model="form.fields.clabe2" inverted color="dark" float-label="CLABE 2"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="vpn_key" icon-color="dark" :error="$v.form.fields.tarjeta2.$error" error-label="Verifique el número de tarjeta">
                    <q-input  @keyup="isNumber($event,'tarjeta2')" upper-case v-model="form.fields.tarjeta2" inverted color="dark" float-label="Tarjeta"/>
                  </q-field>
                </div>
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-4">
                  <q-field icon="account_balance" icon-color="dark" :error="$v.form.fields.banco3.$error" error-label="Escriba el nombre del Banco">
                    <q-input upper-case v-model="form.fields.banco3" inverted color="dark" float-label="Banco 3"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="vpn_key" icon-color="dark" :error="$v.form.fields.clabe3.$error" error-label="Verifique el número clabe">
                    <q-input  @keyup="isNumber($event,'clabe3')" upper-case v-model="form.fields.clabe3" inverted color="dark" float-label="CLABE 3"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="vpn_key" icon-color="dark" :error="$v.form.fields.tarjeta3.$error" error-label="Verifique el número de tarjeta">
                    <q-input  @keyup="isNumber($event,'tarjeta3')" upper-case v-model="form.fields.tarjeta3" inverted color="dark" float-label="Tarjeta"/>
                  </q-field>
                </div>
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-4">
                  <q-field icon="account_balance" icon-color="dark" :error="$v.form.fields.banco4.$error" error-label="Escriba el nombre del Banco">
                    <q-input upper-case v-model="form.fields.banco4" inverted color="dark" float-label="Banco 4"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="vpn_key" icon-color="dark" :error="$v.form.fields.clabe4.$error" error-label="Verifique el número clabe">
                    <q-input  @keyup="isNumber($event,'clabe4')" upper-case v-model="form.fields.clabe4" inverted color="dark" float-label="CLABE 4"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="vpn_key" icon-color="dark" :error="$v.form.fields.tarjeta4.$error" error-label="Verifique el número de tarjeta">
                    <q-input  @keyup="isNumber($event,'tarjeta4')" upper-case v-model="form.fields.tarjeta4" inverted color="dark" float-label="Tarjeta"/>
                  </q-field>
                </div>
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-6">
                  <q-field icon="vpn_key" icon-color="dark" :error="$v.form.fields.toka.$error" error-label="Verifique el número de la tarjeta toka">
                    <q-input @keyup="isNumber($event,'toka')" upper-case v-model="form.fields.toka" inverted color="dark" float-label="TOKA"/>
                  </q-field>
                </div>
              </div>
              <div class="row q-pa-md">
                Expediente
              </div>
              <div class="row q-col-gutter-xs">
                <q-field icon="fas fa-id-badge" icon-color="white" >
                  <input style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="fileInputFormato" accept=".pdf,.docx" @change="uploadFormato()" />
                  <q-btn @click="$refs.fileInputFormato.click()" class="btn_atach" icon-right="attach_file" :loading="loadingButton">Actualizar Expediente</q-btn>
                </q-field>
                <q-field icon="fas fa-id-badge" icon-color="white" >
                  <q-btn @click="abrirDocumento()" class="btn_guardar" icon-right="visibility" :loading="loadingButton">Ver Expediente</q-btn>
                </q-field>
              </div>
          </div>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { required, maxLength, email, minLength, helpers } from 'vuelidate/lib/validators'
import axios from 'axios'

// const rfc = helpers.regex('rfc', /[A-ZÑ&]{3,4}\d{6}[A-V1-9][A-Z1-9][0-9A]/)
// const curp = helpers.regex('curp', /^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$/)
const clabe = helpers.regex('clabe', /[0-9]{18}/)
export default {
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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'COBRANZA'.toUpperCase() || propiedades[i].toUpperCase() === 'AUXILIAR'.toUpperCase() || propiedades[i].toUpperCase() === 'VENTAS'.toUpperCase() || propiedades[i].toUpperCase() === 'PLANEACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'Coordinador GDP'.toUpperCase() || propiedades[i].toUpperCase() === 'COMPRAS'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS/COMISIONES'.toUpperCase()) {
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
      views: {
        grid: true,
        create: false,
        update: false
      },
      tipoOptions: [ { 'label': 'NÓMINA', 'value': 'NÓMINA' }, { 'label': 'PROVEEEDOR', 'value': 'PROVEEDOR' }, { 'label': 'REEMBOLSO', 'value': 'REEMBOLSO' } ],
      form: {
        fields: {
          id: 0,
          nombre_comercial: '',
          razon_social: '',
          rfc: '',
          curp: '',
          direccion: '',
          banco: '',
          banco2: '',
          banco3: '',
          banco4: '',
          clabe: '',
          clabe2: '',
          clabe3: '',
          clabe4: '',
          telefono: '',
          contacto: '',
          toka: '',
          email: '',
          tarjeta1: '',
          tarjeta2: '',
          tarjeta3: '',
          tarjeta4: '',
          tipo: ''
        },
        // data: [],
        columns: [
          {name: 'nombre_comercial', label: 'Nombre comercial', field: 'nombre_comercial', sortable: true, type: 'string', align: 'left'},
          { name: 'razon_social', label: 'Razon Social', field: 'razon_social', sortable: true, type: 'string', align: 'left' },
          { name: 'rfc', label: 'RFC', field: 'rfc', sortable: true, type: 'string', align: 'left' },
          { name: 'curp', label: 'CURP', field: 'curp', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
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
      proveedor: 'pmo/proveedor/proveedor'
    })
  },
  created () {
    this.loadAll()
  },
  methods: {
    ...mapActions({
      getProveedores: 'pmo/proveedor/refresh',
      saveProveedores: 'pmo/proveedor/save',
      updateProveedor: 'pmo/proveedor/update',
      deleteProveedor: 'pmo/proveedor/delete'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getProveedores()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    async clickFila (row) {
      this.informacion = true
      this.$q.loading.show({message: 'Cargando...'})
      this.form.fields.id = row.id
      this.form.fields.nombre_comercial = row.nombre_comercial
      this.form.fields.razon_social = row.razon_social
      if (row.rfc === '(SIN RFC)') {
        this.form.fields.rfc = ''
      } else {
        this.form.fields.rfc = row.rfc
      }
      this.form.fields.curp = row.curp
      this.form.fields.direccion = row.direccion
      this.form.fields.banco = row.banco
      this.form.fields.clabe = row.clabe
      this.form.fields.banco2 = row.banco2
      this.form.fields.clabe2 = row.clabe2
      this.form.fields.telefono = row.telefono
      this.form.fields.contacto = row.contacto
      this.form.fields.toka = row.toka
      this.form.fields.email = row.email
      this.form.fields.banco3 = row.banco3
      this.form.fields.clabe3 = row.clabe3
      this.form.fields.banco4 = row.banco4
      this.form.fields.clabe4 = row.clabe4
      this.form.fields.tarjeta1 = row.tarjeta1
      this.form.fields.tarjeta2 = row.tarjeta2
      this.form.fields.tarjeta3 = row.tarjeta3
      this.form.fields.tarjeta4 = row.tarjeta4
      this.form.fields.tipo = row.tipo
      this.$q.loading.hide()
    },
    newRow () {
      this.$v.form.$reset()
      this.form.fields.nombre_comercial = ''
      this.form.fields.razon_social = ''
      this.form.fields.rfc = ''
      this.form.fields.curp = ''
      this.form.fields.direccion = ''
      this.form.fields.banco = ''
      this.form.fields.clabe = ''
      this.form.fields.banco2 = ''
      this.form.fields.clabe2 = ''
      this.form.fields.telefono = ''
      this.form.fields.contacto = ''
      this.form.fields.toka = ''
      this.form.fields.email = ''
      this.form.fields.banco3 = ''
      this.form.fields.clabe3 = ''
      this.form.fields.banco4 = ''
      this.form.fields.clabe4 = ''
      this.form.fields.tarjeta1 = ''
      this.form.fields.tarjeta2 = ''
      this.form.fields.tarjeta3 = ''
      this.form.fields.tarjeta4 = ''
      this.form.fields.tipo = ''
      this.setView('create')
    },
    save () {
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        this.saveProveedores(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
            // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'done',
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
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    editRow (row) {
      let proveedor = { ...row }
      this.form.fields.id = proveedor.id
      this.form.fields.nombre_comercial = proveedor.nombre_comercial
      this.form.fields.razon_social = proveedor.razon_social
      if (proveedor.rfc === '(SIN RFC)') {
        this.form.fields.rfc = ''
      } else {
        this.form.fields.rfc = proveedor.rfc
      }
      this.form.fields.curp = proveedor.curp
      this.form.fields.direccion = proveedor.direccion
      this.form.fields.banco = proveedor.banco
      this.form.fields.clabe = proveedor.clabe
      this.form.fields.banco2 = proveedor.banco2
      this.form.fields.clabe2 = proveedor.clabe2
      this.form.fields.banco3 = proveedor.banco3
      this.form.fields.clabe3 = proveedor.clabe3
      this.form.fields.banco4 = proveedor.banco4
      this.form.fields.clabe4 = proveedor.clabe4
      this.form.fields.telefono = proveedor.telefono
      this.form.fields.contacto = proveedor.contacto
      this.form.fields.toka = proveedor.toka
      this.form.fields.email = proveedor.email
      this.form.fields.tarjeta1 = row.tarjeta1
      this.form.fields.tarjeta2 = row.tarjeta2
      this.form.fields.tarjeta3 = row.tarjeta3
      this.form.fields.tarjeta4 = row.tarjeta4
      this.form.fields.tipo = row.tipo
      this.setView('update')
    },
    update () {
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        this.updateProveedor(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
            // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'done',
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
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este proveedor?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete(id)
      }).catch(() => {})
    },
    delete (id) {
      let params = {id: id}
      this.deleteProveedor(params).then(({data}) => {
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
    isNumber (evt, input) {
      switch (input) {
        case 'telefono':
          this.form.fields.telefono = this.form.fields.telefono.replace(/[^0-9.]/g, '')
          this.$v.form.fields.telefono.$touch()
          break
        case 'clabe1':
          this.form.fields.clabe = this.form.fields.clabe.replace(/[^0-9.]/g, '')
          this.$v.form.fields.clabe.$touch()
          break
        case 'clabe2':
          this.form.fields.clabe2 = this.form.fields.clabe2.replace(/[^0-9.]/g, '')
          this.$v.form.fields.clabe2.$touch()
          break
        case 'clabe3':
          this.form.fields.clabe3 = this.form.fields.clabe3.replace(/[^0-9.]/g, '')
          this.$v.form.fields.clabe3.$touch()
          break
        case 'clabe4':
          this.form.fields.clabe4 = this.form.fields.clabe4.replace(/[^0-9.]/g, '')
          this.$v.form.fields.clabe4.$touch()
          break
        case 'toka':
          this.form.fields.toka = this.form.fields.toka.replace(/[^0-9.]/g, '')
          this.$v.form.fields.toka.$touch()
          break
        case 'tarjeta1':
          this.form.fields.tarjeta1 = this.form.fields.tarjeta1.replace(/[^0-9.]/g, '')
          this.$v.form.fields.tarjeta1.$touch()
          break
        case 'tarjeta2':
          this.form.fields.tarjeta2 = this.form.fields.tarjeta2.replace(/[^0-9.]/g, '')
          this.$v.form.fields.tarjeta2.$touch()
          break
        case 'tarjeta3':
          this.form.fields.tarjeta3 = this.form.fields.tarjeta3.replace(/[^0-9.]/g, '')
          this.$v.form.fields.tarjeta3.$touch()
          break
        case 'tarjeta4':
          this.form.fields.tarjeta4 = this.form.fields.tarjeta4.replace(/[^0-9.]/g, '')
          this.$v.form.fields.tarjeta4.$touch()
          break
        default:
          break
      }
    },
    uploadFormato () {
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        try {
          this.loadingButton = true
          let file = this.$refs.fileInputFormato.files[0]
          let formData = new FormData()
          formData.append('file', file)
          formData.append('nombre', this.form.fields.nombre_comercial)
          formData.append('curp', this.form.fields.curp)
          if (file === null || file === undefined) {
            this.loadingButton = false
          } else if (file.type) {
            if (file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document' || file.type.split('/')[1].toLowerCase() === 'pdf') {
              this.$q.loading.show({message: 'Subiendo archivo...'})
              axios.post('proveedor/uploadArchivo', formData, {
                headers: {
                  'Content-Type': 'multipart/form-data'
                }
              }).then(response => {
                if (response.data.result === 'success') {
                  this.$q.notify({
                    // only required parameter is the message:
                    message: response.data.message.content,
                    timeout: 3000,
                    type: 'green',
                    textColor: 'white', // if default 'white' doesn't fits
                    icon: 'delete',
                    position: 'top-right' // 'top', 'left', 'bottom-left' etc
                  })
                  this.getFormatoByProveedor()
                  this.loadAll()
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
    abrirDocumento () {
      let nombre = this.form.fields.nombre_comercial + '_' + this.form.fields.curp
      let uri = process.env.API + `proveedor/getFile/${nombre}`
      window.open(uri, '_blank')
    },
    exportCSV () {
      let uri = process.env.API + 'proveedor/exportCSV'
      window.open(uri, '_blank')
    }
  },
  validations: {
    form: {
      fields: {
        // curp: {maxLength: maxLength(18), minLength: minLength(18), curp},
        nombre_comercial: {required, maxLength: maxLength(100)},
        razon_social: {required, maxLength: maxLength(100)},
        rfc: {required, minLength: minLength(12), maxLength: maxLength(13)},
        direccion: {maxLength: maxLength(100)},
        clabe: {maxLength: maxLength(18), minLength: minLength(18), clabe},
        clabe2: {maxLength: maxLength(18), minLength: minLength(18), clabe},
        clabe3: {maxLength: maxLength(18), minLength: minLength(18), clabe},
        clabe4: {maxLength: maxLength(18), minLength: minLength(18), clabe},
        banco: {required, maxLength: maxLength(20), minLength: minLength(1)},
        banco2: {maxLength: maxLength(20), minLength: minLength(1)},
        banco3: {maxLength: maxLength(20), minLength: minLength(1)},
        banco4: {maxLength: maxLength(20), minLength: minLength(1)},
        telefono: {maxLength: maxLength(13)},
        contacto: {maxLength: maxLength(50)},
        toka: {maxLength: maxLength(16), minLength: minLength(16)},
        email: { maxLength: maxLength(100), email },
        tarjeta1: {maxLength: maxLength(16), minLength: minLength(16)},
        tarjeta2: {maxLength: maxLength(16), minLength: minLength(16)},
        tarjeta3: {maxLength: maxLength(16), minLength: minLength(16)},
        tarjeta4: {maxLength: maxLength(16), minLength: minLength(16)}
      }
    }
  }
}
</script>

<style lang="stylus" scoped>

</style>
