<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Empresas"/>
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
                  :data="empresas"
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
                      <q-td  key="razon_social" :props="props" @click.native="clickFila(props.row)" style="text-align: left;cursor: pointer;">{{ props.row.razon_social }}</q-td>
                      <q-td style="text-align: left;" key="rfc" :props="props">{{ props.row.rfc }}</q-td>
                      <q-td style="text-align: left;" key="rep_legal" :props="props">{{ props.row.rep_legal }}</q-td>
                      <q-td key="acciones" :props="props">
                        <q-btn small flat @click="editRow(props.row)" color="blue-6" icon="edit">
                          <q-tooltip>Editar</q-tooltip>
                        </q-btn>
                        <q-btn small flat @click="direccionRow(props.row)" color="blue-grey-6" icon="fas fa-home">
                          <q-tooltip>Dirección</q-tooltip>
                        </q-btn>
                        <q-btn small flat @click="sucursalesRow(props.row, true)" color="blue-grey-6" icon="location_city">
                          <q-tooltip>Sucursales</q-tooltip>
                        </q-btn>
                        <q-btn small flat @click="reporteClientes(props.row.id)" color="amber" icon="fas fa-users">
                          <q-tooltip>Clientes</q-tooltip>
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

    <!--Crear una empresa-->

    <div v-if="views.create">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Empresas" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el label="Nueva empresa"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
               <q-btn @click="save()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
            </div>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md col-sm-12">
            <!-- <div class="row q-col-gutter-xs">
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
            </div> -->
          </div>
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-12">
                <q-field icon="fas fa-gavel" icon-color="dark" :error="$v.form.fields.regimen.$error" error-label="Elija el régimen de la empresa">
                  <q-select v-model="form.fields.regimen" inverted color="dark" float-label="Régimen" :options="form.selectRegimen"/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-12">
                <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fields.razon_social.$error" error-label="Ingrese la razón social">
                  <q-input upper-case v-model="form.fields.razon_social" type="text" inverted color="dark" float-label="Razón social" maxlength="100" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fields.nombre_corto.$error" error-label="Nombre corto de la empresa">
                  <q-input upper-case inverted color="dark" v-model="form.fields.nombre_corto" type="text" float-label="Nombre corto de la empresa" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="fas fa-id-card" icon-color="dark" :error="$v.form.fields.rfc.$error" error-label="Revise el RFC">
                  <q-input upper-case v-model="form.fields.rfc" type="text" inverted color="dark" float-label="RFC" maxlength="13" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="call" icon-color="dark">
                  <q-input upper-case inverted color="dark" v-model="form.fields.telefono" type="text" float-label="Teléfono" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="mail" icon-color="dark">
                  <q-input v-model="form.fields.correo" type="text" inverted color="dark" float-label="Correo electrónico" maxlength="100" />
                </q-field>
              </div>
            </div>
            <div class="row q-pa-md">
              Información del representante
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-12">
                <q-field icon="fas fa-user" icon-color="dark" :error="$v.form.fields.rep_legal.$error" error-label="Ingrese el nombre del representante legal">
                  <q-input upper-case v-model="form.fields.rep_legal" type="text" inverted color="dark" float-label="Nombre del representante legal" maxlength="100" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="fas fa-id-card" icon-color="dark" :error="$v.form.fields.rfc_representante.$error" error-label="Revise el RFC">
                  <q-input upper-case v-model="form.fields.rfc_representante" type="text" inverted color="dark" float-label="RFC del representante" maxlength="13" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="fas fa-id-badge" icon-color="dark" :error="$v.form.fields.curp_representante.$error" error-label="Revise la CURP">
                  <q-input upper-case v-model="form.fields.curp_representante" type="text" inverted color="dark" float-label="CURP del representante" maxlength="18" />
                </q-field>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

      <!-- Modal editar EMPRESA -->
    <div v-if="views.update">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Empresas" to="" @click.native="setView('grid')"/>
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
          <div class="col q-pa-md col-sm-12">
            <!-- <div class="row q-col-gutter-xs">
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
            </div> -->
          </div>
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-12">
                <q-field icon="fas fa-gavel" icon-color="dark" :error="$v.form.fields.regimen.$error" error-label="Elija el régimen de la empresa">
                  <q-select v-model="form.fields.regimen" inverted color="dark" float-label="Régimen" :options="form.selectRegimen"/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-12">
                <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fields.razon_social.$error" error-label="Ingrese la razón social">
                  <q-input upper-case v-model="form.fields.razon_social" type="text" inverted color="dark" float-label="Razón social" maxlength="100" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fields.nombre_corto.$error" error-label="Nombre corto de la empresa">
                  <q-input upper-case inverted color="dark" v-model="form.fields.nombre_corto" type="text" float-label="Nombre corto de la empresa" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="fas fa-id-card" icon-color="dark" :error="$v.form.fields.rfc.$error" error-label="Revise el RFC">
                  <q-input upper-case v-model="form.fields.rfc" type="text" inverted color="dark" float-label="RFC" maxlength="13" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="call" icon-color="dark">
                  <q-input upper-case inverted color="dark" v-model="form.fields.telefono" type="text" float-label="Teléfono" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="mail" icon-color="dark">
                  <q-input v-model="form.fields.correo" type="text" inverted color="dark" float-label="Correo electrónico" maxlength="100" />
                </q-field>
              </div>
            </div>
            <div class="row q-pa-md">
              Información del representante
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-12">
                <q-field icon="fas fa-user" icon-color="dark" :error="$v.form.fields.rep_legal.$error" error-label="Ingrese el nombre del representante legal">
                  <q-input upper-case v-model="form.fields.rep_legal" type="text" inverted color="dark" float-label="Nombre del representante legal" maxlength="100" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="fas fa-id-card" icon-color="dark" :error="$v.form.fields.rfc_representante.$error" error-label="Revise el RFC">
                  <q-input upper-case v-model="form.fields.rfc_representante" type="text" inverted color="dark" float-label="RFC del representante" maxlength="13" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="fas fa-id-badge" icon-color="dark" :error="$v.form.fields.curp_representante.$error" error-label="Revise la CURP">
                  <q-input upper-case v-model="form.fields.curp_representante" type="text" inverted color="dark" float-label="CURP del representante" maxlength="18" />
                </q-field>
              </div>
            </div>
            <div class="row q-pa-md">
              Subempresa (opcional)
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-12">
                <q-field icon="fas fa-building" icon-color="dark">
                  <q-select v-model="form.fields.subempresa_id" inverted color="dark" float-label="Subempresa" :options="subempresasOptions" filter/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs border-panel" style="padding-left: 20px;padding-right: 20px;padding-top: 20px;margin-top: 20px;padding-bottom: 20px;" v-if="vista_empresas==='grid'">
                <div class="col-sm-12">
                  <div class="row justify-between">
                    <div class="col-sm-6">
                      <q-btn size="15px" icon="business" disable class="btn_categoria" label="SUBEMPRESAS" />
                    </div>
                    <div class="col-sm-6">
                      <div class="row justify-end">
                        <div class="col-sm-4" style="text-align: right">
                          <q-btn @click="newSubRow()" class="btn_nuevo" icon="add">
                            Nuevo
                          </q-btn>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row q-mt-sm" style="margin-top:20px;">
                <div class="row" style="width:60vw;">
                  <q-search hide-underline color="secondary" v-model="form.filter"/>
                </div>
                <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                  <q-table id="sticky-table"
                    :data="subempresas"
                    :columns="form.columns"
                    :selected.sync="form_subempresa.selected"
                    :filter="form_subempresa.filter"
                    color="positive"
                    title=""
                    :dense="true"
                    :pagination.sync="form_subempresa.pagination"
                    :loading="form_subempresa.loading"
                    :rows-per-page-options="form_subempresa.rowsOptions">
                    <template slot="body" slot-scope="props">
                      <q-tr :props="props">
                        <q-td  key="razon_social" :props="props" @click.native="clickFila(props.row)" style="text-align: left;cursor: pointer;">{{ props.row.razon_social }}</q-td>
                        <q-td style="text-align: left;" key="rfc" :props="props">{{ props.row.rfc }}</q-td>
                        <q-td style="text-align: left;" key="rep_legal" :props="props">{{ props.row.rep_legal }}</q-td>
                        <q-td key="acciones" :props="props">
                          <q-btn small flat @click="editSubRow(props.row)" color="blue-6" icon="edit">
                            <q-tooltip>Editar</q-tooltip>
                          </q-btn>
                          <q-btn small flat @click="deleteSingleSubRow(props.row.id)" color="negative" icon="delete">
                            <q-tooltip>Eliminar</q-tooltip>
                          </q-btn>
                        </q-td>
                      </q-tr>
                    </template>
                  </q-table>
                  <q-inner-loading :visible="form_subempresa.loading">
                    <q-spinner-dots size="64px" color="primary" />
                  </q-inner-loading>
                </div>
                </div>
            </div>
            <div class="row q-col-gutter-xs border-panel" style="padding-left: 20px;padding-right: 20px;padding-top: 20px;margin-top: 20px;padding-bottom: 20px;" v-if="vista_empresas==='create' || vista_empresas==='update'">
              <div class="col-sm-12">
                <div class="row justify-between">
                  <div class="col-sm-6" v-if="vista_empresas==='create'">
                    <h5 style="margin: 7px 0; font-weight: bold">NUEVA EMPRESA</h5>
                  </div>
                  <div class="col-sm-6" v-if="vista_empresas==='update'">
                    <h5 style="margin: 7px 0; font-weight: bold">EDITAR EMPRESA - {{ form_subempresa.fields.razon_social }}</h5>
                  </div>
                  <div class="col-sm-6">
                    <div class="row justify-end">
                      <div class="col-sm-4" style="text-align: right">
                        <q-btn @click="vista_empresas='grid'" class="btn_regresar" icon="fa-arrow-left" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- -->
              <div class="col-sm-12">
                    <div class="row q-col-gutter-xs">
                      <div class="col-sm-6">
                        <q-field icon="fas fa-gavel" icon-color="dark" :error="$v.form_subempresa.fields.regimen.$error" error-label="Elija el régimen de la empresa">
                          <q-select v-model="form_subempresa.fields.regimen" inverted color="dark" float-label="Régimen" :options="form.selectRegimen"/>
                        </q-field>
                      </div>
                      <div class="col-sm-6">
                        <q-field icon="fas fa-building" icon-color="dark" :error="$v.form_subempresa.fields.razon_social.$error" error-label="Ingrese la razón social">
                          <q-input upper-case v-model="form_subempresa.fields.razon_social" type="text" inverted color="dark" float-label="Razón social" maxlength="100" />
                        </q-field>
                      </div>
                    </div>
                    <div class="row q-col-gutter-xs">
                      <div class="col-sm-6">
                        <q-field icon="fas fa-building" icon-color="dark" :error="$v.form_subempresa.fields.nombre_corto.$error" error-label="Nombre corto de la empresa">
                          <q-input upper-case inverted color="dark" v-model="form_subempresa.fields.nombre_corto" type="text" float-label="Nombre corto de la empresa" maxlength="100" />
                        </q-field>
                      </div>
                      <div class="col-sm-6">
                        <q-field icon="fas fa-id-card" icon-color="dark" :error="$v.form_subempresa.fields.rfc.$error" error-label="Revise el RFC">
                          <q-input upper-case v-model="form_subempresa.fields.rfc" type="text" inverted color="dark" float-label="RFC" maxlength="13" />
                        </q-field>
                      </div>
                    </div>
                    <div class="row q-pa-md">
                      Información del representante
                    </div>
                    <div class="row q-col-gutter-xs">
                      <div class="col-sm-12">
                        <q-field icon="fas fa-user" icon-color="dark" :error="$v.form_subempresa.fields.rep_legal.$error" error-label="Ingrese el nombre del representante legal">
                          <q-input upper-case v-model="form_subempresa.fields.rep_legal" type="text" inverted color="dark" float-label="Nombre del representante legal" maxlength="100" />
                        </q-field>
                      </div>
                    </div>
                    <div class="row q-col-gutter-xs">
                      <div class="col-sm-6">
                        <q-field icon="fas fa-id-card" icon-color="dark" :error="$v.form_subempresa.fields.rfc_representante.$error" error-label="Revise el RFC">
                          <q-input upper-case v-model="form_subempresa.fields.rfc_representante" type="text" inverted color="dark" float-label="RFC del representante" maxlength="13" />
                        </q-field>
                      </div>
                      <div class="col-sm-6">
                        <q-field icon="fas fa-id-badge" icon-color="dark" :error="$v.form_subempresa.fields.curp_representante.$error" error-label="Revise la CURP">
                          <q-input upper-case v-model="form_subempresa.fields.curp_representante" type="text" inverted color="dark" float-label="CURP del representante" maxlength="18" />
                        </q-field>
                      </div>
                    </div>
                    <div class="row q-col-gutter-xs">
                      <div class="col-sm-2 offset-sm-10 pull-right">
                        <q-btn @click="saveSub()" class="btn_guardar" icon-right="save" :loading="loadingButton" v-if="vista_empresas === 'create'">Guardar</q-btn>
                        <q-btn @click="updateSub()" class="btn_guardar" icon-right="save" :loading="loadingButton" v-if="vista_empresas === 'update'">Guardar</q-btn>
                      </div>
                    </div>
                  </div>
              <!-- -->
                <!-- </div> -->
            </div>
          </div>
        </div>
      </div>
    </div>
      <!-- Modal de información de la empresa -->
      <q-modal v-if="informacion" style="background-color: rgba(0,0,0,0.7);" v-model="informacion" :content-css="{width: '90vw', height: '600px'}">
        <q-modal-layout>
          <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
            <div class="col-sm-6">
              <q-toolbar-title>
                {{ form.fields.razon_social }}
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
          <div class="row q-mt-lg subtitulos" style="margin-left:15px;margin-right:15px;">
            Información general
          </div>
          <div class="row q-mt-lg" style="margin-right:15px;">
            <div class="col-sm-12">
              <q-field icon="fas fa-gavel" icon-color="dark">
                <q-select readonly v-model="form.fields.regimen" inverted-color="dark" float-label="Régimen" :options="form.selectRegimen"/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right:15px;">
            <div class="col-sm-12">
              <q-field icon="fas fa-building" icon-color="dark">
                <q-input readonly upper-case v-model="form.fields.razon_social" type="text" inverted-color="dark" float-label="Razón social" maxlength="100" />
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right:15px;">
            <div class="col-sm-6">
              <q-field icon="fas fa-building" icon-color="dark">
                <q-input readonly upper-case inverted-color="dark" v-model="form.fields.nombre_corto" type="text" float-label="Nombre corto de la empresa" maxlength="100" />
              </q-field>
            </div>
            <div class="col-sm-6">
              <q-field icon="fas fa-id-card" icon-color="dark">
                <q-input readonly upper-case v-model="form.fields.rfc" type="text" inverted-color="dark" float-label="RFC" maxlength="13" />
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg subtitulos" style="margin-left:15px;">
            Información del representante
          </div>
          <div class="row q-mt-lg" style="margin-right:15px;">
            <div class="col-sm-12">
              <q-field icon="fas fa-user" icon-color="dark">
                <q-input readonly upper-case v-model="form.fields.rep_legal" type="text" inverted-color="dark" float-label="Nombre del representante legal" maxlength="100" />
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right:15px; margin-bottom: 10px;">
            <div class="col-sm-6">
              <q-field icon="fas fa-id-card" icon-color="dark">
                <q-input readonly upper-case v-model="form.fields.rfc_representante" type="text" inverted-color="dark" float-label="RFC del representante" maxlength="13" />
              </q-field>
            </div>
            <div class="col-sm-6">
              <q-field icon="fas fa-id-badge" icon-color="dark">
                <q-input readonly upper-case v-model="form.fields.curp_representante" type="text" inverted-color="dark" float-label="CURP del representante" maxlength="18" />
              </q-field>
            </div>
          </div>
        </q-modal-layout>
      </q-modal>

      <!-- Modal direcciones -->
    <div v-if="views.grid_direcciones">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Empresas" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el label="Direcciones"/>
              </q-breadcrumbs>
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
                  <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
                </div>
              </div>
              <div class="col-sm-6 pull-right">
                <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
                  <q-btn @click="newDireccionRow()" class="btn_nuevo" icon="add">Nuevo</q-btn>
                </div>
              </div>
            </div>
          </div>
          <div class="col q-pa-md">
            <div class="col q-pa-md border-panel">
              <div class="col-sm-12" style="padding-bottom: 10px;">
                <q-search color="primary" v-model="form.filter"/>
              </div>
              <q-table id="sticky-table-newstyle"
                :data="form_direcciones.data"
                :columns="form_direcciones.columns"
                :selected.sync="form_direcciones.selected"
                :filter="form_direcciones.filter"
                color="positive"
                title=""
                :dense="true"
                :pagination.sync="form_direcciones.pagination"
                :loading="form_direcciones.loading"
                :rows-per-page-options="form_direcciones.rowsOptions">
                <template slot="body" slot-scope="props">
                  <q-tr :props="props">
                    <q-td key="tipo" :props="props" @click.native="clickDireccion(props.row)" style="cursor: pointer;">{{ props.row.tipo }}</q-td>
                    <q-td key="tipo_oficina" :props="props">{{ props.row.tipo_oficina }}</q-td>
                    <q-td key="tipo_propiedad" :props="props">{{ props.row.tipo_propiedad }}</q-td>
                    <q-td key="poblacion" :props="props">{{ props.row.poblacion }}</q-td>
                    <q-td key="cp" :props="props">{{ props.row.cp }}</q-td>
                    <q-td key="acciones" :props="props">
                      <q-btn small flat @click="editDireccionRow(props.row)" color="blue-6" icon="edit">
                        <q-tooltip>Editar</q-tooltip>
                      </q-btn>
                      <q-btn small flat @click="deleteDireccionSingleRow(props.row.id)" color="negative" icon="delete">
                        <q-tooltip>Eliminar</q-tooltip>
                      </q-btn>
                    </q-td>
                  </q-tr>
                </template>
              </q-table>
              <q-inner-loading :visible="form_direcciones.loading">
                <q-spinner-dots size="64px" color="primary" />
              </q-inner-loading>
            </div>
          </div>
        </div>
      </div>
    </div>

      <!--Crear una direccion-->

    <div v-if="views.create_direccion">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Empresas" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el label="Direcciones" to="" @click.native="setView('grid_direcciones')"/>
                <q-breadcrumbs-el label="Nueva dirección"/>
              </q-breadcrumbs>
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
                  <q-btn @click="setView('grid_direcciones')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
                </div>
              </div>
              <div class="col-sm-6 pull-right">
                <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
                  <q-btn @click="save_direccion()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
                </div>
              </div>
            </div>
          </div>
          <div class="col q-pa-md col-sm-12">
            {{ form_direcciones.fields.empresa }}
          </div>
          <div class="col q-pa-md col-sm-12">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-4">
                <q-field icon="fas fa-home" icon-color="dark" :error="$v.form_direcciones.fields.tipo.$error" error-label="Elija el tipo">
                  <q-select v-model="form_direcciones.fields.tipo" inverted color="dark" float-label="Tipo" :options="form_direcciones.selectTipos"></q-select>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="fas fa-home" icon-color="dark" :error="$v.form_direcciones.fields.tipo_oficina.$error" error-label="Elija el tipo de oficina">
                  <q-select v-model="form_direcciones.fields.tipo_oficina" inverted color="dark" float-label="Tipo de oficina" :options="form_direcciones.selectTipoOficina"></q-select>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="fas fa-home" icon-color="dark" :error="$v.form_direcciones.fields.tipo_propiedad.$error" error-label="Elija el tipo de propiedad">
                  <q-select v-model="form_direcciones.fields.tipo_propiedad" inverted color="dark" float-label="Tipo de propiedad" :options="form_direcciones.selectTipoPropiedad"></q-select>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="fas fa-road" icon-color="dark">
                  <q-input upper-case v-model="form_direcciones.fields.calle" type="text" inverted color="dark" float-label="Calle" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-hashtag" icon-color="dark">
                  <q-input upper-case v-model="form_direcciones.fields.num_ext" type="text" inverted color="dark" float-label="Núm. Ext." maxlength="20" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-hashtag" icon-color="dark">
                  <q-input upper-case v-model="form_direcciones.fields.num_int" type="text" inverted color="dark" float-label="Núm. Int." maxlength="20" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="business" icon-color="dark">
                  <q-input upper-case v-model="form_direcciones.fields.colonia" type="text" inverted color="dark" float-label="Colonia" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="fas fa-map-signs" icon-color="dark">
                  <q-input upper-case v-model="form_direcciones.fields.poblacion" type="text" inverted color="dark" float-label="Población" maxlength="100" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-4">
                <q-field icon="fas fa-globe" icon-color="dark">
                  <q-select v-model="form_direcciones.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()"></q-select>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="fas fa-map-pin" icon-color="dark">
                  <q-select v-model="form_direcciones.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="form_direcciones.municipiosOptions" filter></q-select>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="location_on" icon-color="dark" :error="$v.form_direcciones.fields.cp.$error" error-label="Ingrese un código postal válido">
                  <q-input upper-case v-model="form_direcciones.fields.cp" type="text" inverted color="dark" float-label="Código postal" maxlength="5" @keydown="numberOnly"/>
                </q-field>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

      <!--Editar una direccion-->

    <div v-if="views.update_direccion">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Empresas" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el label="Direcciones" to="" @click.native="setView('grid_direcciones')"/>
                <q-breadcrumbs-el label="Editar dirección"/>
              </q-breadcrumbs>
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
                  <q-btn @click="setView('grid_direcciones')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
                </div>
              </div>
              <div class="col-sm-6 pull-right">
                <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
                  <q-btn @click="update_direccion()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
                </div>
              </div>
            </div>
          </div>
          <div class="col q-pa-md col-sm-12">
            {{ form_direcciones.fields.empresa }}
          </div>
          <div class="col q-pa-md col-sm-12">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-4">
                <q-field icon="fas fa-home" icon-color="dark" :error="$v.form_direcciones.fields.tipo.$error" error-label="Elija el tipo">
                  <q-select v-model="form_direcciones.fields.tipo" inverted color="dark" float-label="Tipo" :options="form_direcciones.selectTipos"></q-select>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="fas fa-home" icon-color="dark" :error="$v.form_direcciones.fields.tipo_oficina.$error" error-label="Elija el tipo de oficina">
                  <q-select v-model="form_direcciones.fields.tipo_oficina" inverted color="dark" float-label="Tipo de oficina" :options="form_direcciones.selectTipoOficina"></q-select>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="fas fa-home" icon-color="dark" :error="$v.form_direcciones.fields.tipo_propiedad.$error" error-label="Elija el tipo de propiedad">
                  <q-select v-model="form_direcciones.fields.tipo_propiedad" inverted color="dark" float-label="Tipo de propiedad" :options="form_direcciones.selectTipoPropiedad"></q-select>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="fas fa-road" icon-color="dark">
                  <q-input upper-case v-model="form_direcciones.fields.calle" type="text" inverted color="dark" float-label="Calle" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-hashtag" icon-color="dark">
                  <q-input upper-case v-model="form_direcciones.fields.num_ext" type="text" inverted color="dark" float-label="Núm. Ext." maxlength="20" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-hashtag" icon-color="dark">
                  <q-input upper-case v-model="form_direcciones.fields.num_int" type="text" inverted color="dark" float-label="Núm. Int." maxlength="20" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="business" icon-color="dark">
                  <q-input upper-case v-model="form_direcciones.fields.colonia" type="text" inverted color="dark" float-label="Colonia" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="fas fa-map-signs" icon-color="dark">
                  <q-input upper-case v-model="form_direcciones.fields.poblacion" type="text" inverted color="dark" float-label="Población" maxlength="100" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-4">
                <q-field icon="fas fa-globe" icon-color="dark">
                  <q-select v-model="form_direcciones.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()"></q-select>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="fas fa-map-pin" icon-color="dark">
                  <q-select v-model="form_direcciones.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="form_direcciones.municipiosOptions" filter></q-select>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="location_on" icon-color="dark" :error="$v.form_direcciones.fields.cp.$error" error-label="Ingrese un código postal válido">
                  <q-input upper-case v-model="form_direcciones.fields.cp" type="text" inverted color="dark" float-label="Código postal" maxlength="5" @keydown="numberOnly"/>
                </q-field>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="views.grid_sucursales">
      <sucursales v-if="grid_sucursales" :show="grid_sucursales" :empresa_id="empresa_id" :empresa="razon_social" @closeSucursales="modalSucursal($event)"/>
    </div>

      <!-- Modal de información de la empresa -->
      <q-modal v-if="form_direcciones.informacion_direccion" style="background-color: rgba(0,0,0,0.7);" v-model="form_direcciones.informacion_direccion" :content-css="{width: '40vw', height: '310px'}">
        <q-modal-layout>
          <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
            <div class="col-sm-6">
              <q-toolbar-title>
                Dirección
              </q-toolbar-title>
            </div>
            <div class="col-sm-6 pull-right">
              <q-btn
                flat
                round
                dense
                color="white"
                @click="form_direcciones.informacion_direccion = false"
                icon="fas fa-times-circle"
              />
            </div>
          </q-toolbar>
          <div class="row q-mt-lg">
            <div class="col-sm-12" v-if="form_direcciones.objetoInformacion_direccion!==null">
              <div style="font-weight:bold;font-size:1.2em;text-align:center;">{{form_direcciones.objetoInformacion_direccion.tipo}}</div>
              <ul style="list-style:none;padding-left:15px;">
                <li><label style="font-weight:bold;">Tipo de oficina: </label><label style="margin-left:5px;">{{form_direcciones.objetoInformacion_direccion.tipo_oficina}}</label></li>
                <li><label style="font-weight:bold;">Tipo de propiedad: </label><label style="margin-left:5px;">{{form_direcciones.objetoInformacion_direccion.tipo_propiedad}}</label></li>
                <li><label style="font-weight:bold;">Calle: </label><label style="margin-left:5px;">{{form_direcciones.objetoInformacion_direccion.calle}}</label></li>
                <li><label style="font-weight:bold;">Num. Ext.: </label><label style="margin-left:5px;">{{form_direcciones.objetoInformacion_direccion.num_ext}}</label></li>
                <li><label style="font-weight:bold;">Num. Int.: </label><label style="margin-left:5px;">{{form_direcciones.objetoInformacion_direccion.num_int}}</label></li>
                <li><label style="font-weight:bold;">Colonia: </label><label style="margin-left:5px;">{{form_direcciones.objetoInformacion_direccion.colonia}}</label></li>
                <li><label style="font-weight:bold;">Población: </label><label style="margin-left:5px;">{{form_direcciones.objetoInformacion_direccion.poblacion}}</label></li>
                <li><label style="font-weight:bold;">Estado: </label><label style="margin-left:5px;">{{form_direcciones.objetoInformacion_direccion.estado_nombre}}</label></li>
                <li><label style="font-weight:bold;">Municipio: </label><label style="margin-left:5px;">{{form_direcciones.objetoInformacion_direccion.municipio_nombre}}</label></li>
                <li><label style="font-weight:bold;">Código postal: </label><label style="margin-left:5px;">{{form_direcciones.objetoInformacion_direccion.cp}}</label></li>
              </ul>
            </div>
          </div>
        </q-modal-layout>
      </q-modal>

      <!-- Modal reporte de clientes -->
      <q-modal v-if="reporte_clientes" style="background-color: rgba(0,0,0,0.7);" v-model="reporte_clientes" :content-css="{width: '40vw', height: '40vh'}">
        <q-modal-layout>
          <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
            <div class="col-sm-6">
              <q-toolbar-title>
                Reporte de clientes
              </q-toolbar-title>
            </div>
            <div class="col-sm-6 pull-right">
              <q-btn
                flat
                round
                dense
                color="white"
                @click="reporte_clientes = false"
                icon="fas fa-times-circle"
              />
            </div>
          </q-toolbar>
          <div class="row q-mt-lg" style="margin-lef:10px;">
            <q-tree
              :nodes="props"
              :expanded.sync="propsExpanded"
              color="red"
              node-key="label"
            />
          </div>
        </q-modal-layout>
      </q-modal>
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { required, maxLength, minLength, numeric, helpers } from 'vuelidate/lib/validators'
import Sucursales from '../../components/layout/vnt/Sucursales'

const rfc = helpers.regex('rfc', /[A-ZÑ&]{3,4}\d{6}[A-V1-9][A-Z1-9][0-9A]/)
const curp = helpers.regex('curp', /^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$/)
const nombreRep = helpers.regex('nombreRep', /^[A-ZÁÉÍÓÚÑ]+(\s{1}[A-ZÁÉÍÓÚÑ]+)*$/)

export default {
  components: {
    Sucursales
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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'COBRANZA'.toUpperCase() || propiedades[i].toUpperCase() === 'PLANEACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS/COMISIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'AUXILIAR TESORERIA'.toUpperCase()) {
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
      grid_sucursales: false,
      razon_social: '',
      empresa_id: 0,
      vista_empresas: 'grid',
      loadingButton: false,
      informacion: false,
      reporte_clientes: false,
      objetoInformacion: null,
      props: [],
      propsExpanded: ['Clientes'],
      views: {
        grid: true,
        create: false,
        update: false,
        grid_direcciones: false,
        create_direccion: false,
        update_direccion: false,
        grid_sucursales: false
      },
      form_subempresa: {
        fields: {
          id: 0,
          razon_social: '',
          rfc: '',
          rep_legal: '',
          curp_representante: '',
          regimen: '',
          nombre_corto: '',
          rfc_representante: '',
          padre_id: 0
        },
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form: {
        fields: {
          id: 0,
          razon_social: '',
          rfc: '',
          rep_legal: '',
          curp_representante: '',
          regimen: '',
          nombre_corto: '',
          rfc_representante: '',
          subempresa_id: 0,
          padre_id: 0,
          telefono: '',
          correo: ''
        },
        // data: [],
        selectRegimen: [ { 'label': 'RÉGIMEN DE ASALARIADOS', 'value': 'RÉGIMEN DE ASALARIADOS' }, { 'label': 'RÉGIMEN DE ACTIVIDADES PROFESIONALES (HONORARIOS)', 'value': 'RÉGIMEN DE ACTIVIDADES PROFESIONALES (HONORARIOS)' }, { 'label': 'RÉGIMEN DE ARRENDAMIENTO DE INMUEBLES', 'value': 'RÉGIMEN DE ARRENDAMIENTO DE INMUEBLES' }, { 'label': 'RÉGIMEN DE ACTIVIDAD EMPRESARIAL', 'value': 'RÉGIMEN DE ACTIVIDAD EMPRESARIAL' }, {'label': 'RÉGIMEN DE INCORPORACIÓN FISCAL', 'value': 'RÉGIMEN DE INCORPORACIÓN FISCAL'}, {'label': 'PERSONAS MORALES DEL RÉGIMEN GENERAL', 'value': 'PERSONAS MORALES DEL RÉGIMEN GENERAL'}, {'label': 'PERSONAS MORALES CON FINES NO LUCRATIVOS', 'value': 'PERSONAS MORALES CON FINES NO LUCRATIVOS'} ],
        columns: [
          { name: 'razon_social', label: 'Razón social', field: 'razon_social', sortable: true, type: 'string', align: 'left' },
          { name: 'rfc', label: 'RFC', field: 'rfc', sortable: true, type: 'string', align: 'left' },
          { name: 'rep_legal', label: 'Representante legal', field: 'rep_legal', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_direcciones: {
        informacion_direccion: false,
        objetoInformacion_direccion: null,
        fields: {
          id: 0,
          empresa_id: 0,
          empresa: '',
          tipo: '',
          calle: '',
          num_ext: '',
          num_int: '',
          colonia: '',
          poblacion: '',
          municipio_id: 0,
          estado_id: 0,
          cp: '',
          tipo_oficina: '',
          tipo_propiedad: ''
        },
        selectTipos: [ { 'label': 'DOMICILIO FISCAL', 'value': 'DOMICILIO FISCAL' }, { 'label': 'SUCURSAL FISCAL', 'value': 'SUCURSAL FISCAL' } ],
        selectTipoOficina: [ { 'label': 'FÍSICA', 'value': 'FÍSICA' }, { 'label': 'VIRTUAL', 'value': 'VIRTUAL' } ],
        selectTipoPropiedad: [ { 'label': 'PROPIA', 'value': 'PROPIA' }, { 'label': 'RENTADA', 'value': 'RENTADA' } ],
        municipiosOptions: [],
        // data: [],
        columns: [
          { name: 'tipo', label: 'Tipo', field: 'tipo', sortable: true, type: 'string', align: 'left' },
          { name: 'tipo_oficina', label: 'Tipo de oficina', field: 'tipo_oficina', sortable: true, type: 'string', align: 'left' },
          { name: 'tipo_propiedad', label: 'Tipo de propiedad', field: 'tipo_propiedad', sortable: true, type: 'string', align: 'left' },
          { name: 'poblacion', label: 'Población', field: 'poblacion', sortable: true, type: 'string', align: 'left' },
          { name: 'cp', label: 'C. P.', field: 'cp', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'center' }
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
      subempresas: 'vnt/empresa/subempresas',
      empresas: 'vnt/empresa/empresas',
      direcciones: 'exp/direccion/direcciones',
      estadosOptions: 'vnt/estado/selectOptions',
      empresasOptions: 'vnt/empresa/selectOptions',
      subempresasOptions: 'vnt/empresa/selectOptionsSub'
    })
  },
  created () {
    this.loadAll()
  },
  watch: {
    informacion (newValue, old) {
      if (newValue === false) {
        this.objetoInformacion = null
      }
    },
    'form_direcciones.informacion_direccion' (newValue, old) {
      if (newValue === false) {
        this.form_direcciones.objetoInformacion_direccion = null
      }
    }
  },
  methods: {
    ...mapActions({
      getEmpresas: 'vnt/empresa/refresh',
      saveEmpresa: 'vnt/empresa/save',
      updateEmpresa: 'vnt/empresa/update',
      deleteEmpresa: 'vnt/empresa/delete',

      getDireccionesByEmpresa: 'exp/direccion/getByEmpresa',
      saveDireccion: 'exp/direccion/save',
      updateDireccion: 'exp/direccion/update',
      deleteDireccion: 'exp/direccion/delete',
      getEstados: 'vnt/estado/refresh',
      getMunicipiosByEstado: 'vnt/municipio/getByEstado',
      getClientesByEmpresa: 'vnt/proveedor/getByEmpresa',
      getEmpresasByPadre: 'vnt/empresa/getByPadre'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getEmpresas()
      this.form.loading = false
    },
    async loadAllSub () {
      this.form_subempresa.loading = true
      await this.getSubempresas()
      this.form_subempresa.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views.grid_direcciones = false
      this.views.create_direccion = false
      this.views.update_direccion = false
      this.views.grid_direcciones = false
      this.views[view] = true
    },
    quitarEspacios () {
      this.form.fields.razon_social = this.form.fields.razon_social.trim()
      this.form.fields.rfc = this.form.fields.rfc.trim()
      this.form.fields.rep_legal = this.form.fields.rep_legal.trim()
      this.form.fields.nombre_corto = this.form.fields.nombre_corto.trim()

      this.form_subempresa.fields.razon_social = this.form_subempresa.fields.razon_social.trim()
      this.form_subempresa.fields.rfc = this.form_subempresa.fields.rfc.trim()
      this.form_subempresa.fields.rep_legal = this.form_subempresa.fields.rep_legal.trim()
      this.form_subempresa.fields.nombre_corto = this.form_subempresa.fields.nombre_corto.trim()
    },
    save () {
      this.quitarEspacios()
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        this.saveEmpresa(params).then(({data}) => {
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
    saveSub () {
      this.quitarEspacios()
      this.$v.form_subempresa.$touch()
      if (!this.$v.form_subempresa.$error) {
        this.loadingButton = true
        let params = this.form_subempresa.fields
        this.saveEmpresa(params).then(({data}) => {
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
            this.loadAllSub()
            this.vista_empresas = 'grid'
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
      this.quitarEspacios()
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        this.updateEmpresa(params).then(({data}) => {
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
    updateSub () {
      this.quitarEspacios()
      this.$v.form_subempresa.$touch()
      if (!this.$v.form_subempresa.$error) {
        this.loadingButton = true
        let params = this.form_subempresa.fields
        this.updateEmpresa(params).then(({data}) => {
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
            this.loadAllSub()
            this.vista_empresas = 'grid'
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
      let empresa = { ...row }
      this.form.fields.id = row.id
      this.form.fields.razon_social = empresa.razon_social
      this.form.fields.rep_legal = empresa.rep_legal
      this.form.fields.regimen = empresa.regimen
      if (empresa.curp_representante === null) {
        this.form.fields.curp_representante = ''
      } else {
        this.form.fields.curp_representante = empresa.curp_representante
      }
      if (empresa.rfc === null) {
        this.form.fields.rfc = ''
      } else {
        this.form.fields.rfc = empresa.rfc
      }
      if (empresa.nombre_corto === null) {
        this.form.fields.nombre_corto = ''
      } else {
        this.form.fields.nombre_corto = empresa.nombre_corto
      }
      if (empresa.rfc_representante === null) {
        this.form.fields.rfc_representante = ''
      } else {
        this.form.fields.rfc_representante = empresa.rfc_representante
      }
      if (empresa.telefono === null) {
        this.form.fields.telefono = ''
      } else {
        this.form.fields.telefono = empresa.telefono
      }
      if (empresa.correo === null) {
        this.form.fields.correo = ''
      } else {
        this.form.fields.correo = empresa.correo
      }
      if (empresa.subempresa_id === null) {
        this.form.fields.subempresa_id = 0
      } else {
        this.form.fields.subempresa_id = empresa.subempresa_id
      }
      if (empresa.padre_id === null) {
        this.form.fields.padre_id = 0
      } else {
        this.form.fields.padre_id = empresa.padre_id
      }
      this.subempresas = []
      this.getSubempresas()
      this.vista_empresas = 'grid'
      this.setView('update')
    },
    editSubRow (row) {
      let empresa = { ...row }
      this.form_subempresa.fields.id = row.id
      this.form_subempresa.fields.razon_social = empresa.razon_social
      this.form_subempresa.fields.rep_legal = empresa.rep_legal
      this.form_subempresa.fields.regimen = empresa.regimen
      if (empresa.curp_representante === null) {
        this.form_subempresa.fields.curp_representante = ''
      } else {
        this.form_subempresa.fields.curp_representante = empresa.curp_representante
      }
      if (empresa.rfc === null) {
        this.form_subempresa.fields.rfc = ''
      } else {
        this.form_subempresa.fields.rfc = empresa.rfc
      }
      if (empresa.nombre_corto === null) {
        this.form_subempresa.fields.nombre_corto = ''
      } else {
        this.form_subempresa.fields.nombre_corto = empresa.nombre_corto
      }
      if (empresa.rfc_representante === null) {
        this.form_subempresa.fields.rfc_representante = ''
      } else {
        this.form_subempresa.fields.rfc_representante = empresa.rfc_representante
      }
      if (empresa.subempresa_id === null) {
        this.form_subempresa.fields.subempresa_id = 0
      } else {
        this.form_subempresa.fields.subempresa_id = empresa.subempresa_id
      }
      if (empresa.padre_id === null) {
        this.form_subempresa.fields.padre_id = 0
      } else {
        this.form_subempresa.fields.padre_id = empresa.padre_id
      }
      this.vista_empresas = 'update'
    },
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar esta empresa?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete(id)
      }).catch(() => {})
    },
    deleteSingleSubRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar esta subempresa?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.deleteSub(id)
      }).catch(() => {})
    },
    delete (id) {
      let params = {id: id}
      this.deleteEmpresa(params).then(({data}) => {
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
    deleteSub (id) {
      let params = {id: id}
      this.deleteEmpresa(params).then(({data}) => {
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
          this.loadAllSub()
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
      this.form.fields.razon_social = ''
      this.form.fields.rfc = ''
      this.form.fields.rep_legal = ''
      this.form.fields.regimen = ''
      this.form.fields.curp_representante = ''
      this.form.fields.nombre_corto = ''
      this.form.fields.rfc_representante = ''
      this.form.fields.subempresa_id = 0
      this.form.fields.padre_id = 0
      this.form.fields.telefono = ''
      this.form.fields.correo = ''
      this.setView('create')
    },
    newSubRow () {
      this.$v.form_subempresa.$reset()
      this.form_subempresa.fields.id = 0
      this.form_subempresa.fields.razon_social = ''
      this.form_subempresa.fields.rfc = ''
      this.form_subempresa.fields.rep_legal = ''
      this.form_subempresa.fields.regimen = ''
      this.form_subempresa.fields.curp_representante = ''
      this.form_subempresa.fields.nombre_corto = ''
      this.form_subempresa.fields.rfc_representante = ''
      this.form_subempresa.fields.subempresa_id = 0
      this.form_subempresa.fields.padre_id = this.form.fields.id
      this.vista_empresas = 'create'
    },
    clickFila (row) {
      this.informacion = true
      this.form.fields.id = row.id
      this.form.fields.razon_social = row.razon_social
      this.form.fields.rep_legal = row.rep_legal
      this.form.fields.regimen = row.regimen
      if (row.curp_representante === null) {
        this.form.fields.curp_representante = ''
      } else {
        this.form.fields.curp_representante = row.curp_representante
      }
      if (row.rfc === null) {
        this.form.fields.rfc = ''
      } else {
        this.form.fields.rfc = row.rfc
      }
      if (row.nombre_corto === null) {
        this.form.fields.nombre_corto = ''
      } else {
        this.form.fields.nombre_corto = row.nombre_corto
      }
      if (row.rfc_representante === null) {
        this.form.fields.rfc_representante = ''
      } else {
        this.form.fields.rfc_representante = row.rfc_representante
      }
      if (row.subempresa_id === 0) {
        this.form.fields.subempresa_id = ''
      } else {
        this.form.fields.subempresa_id = row.subempresa_id
      }
    },
    async cargarDirecciones () {
      this.form_direcciones.loading = true
      this.form_direcciones.data = []
      this.getDireccionesByEmpresa({empresa_id: this.form_direcciones.fields.empresa_id}).then(({data}) => {
        this.form_direcciones.loading = false
        if (data.direcciones.length > 0) {
          this.form_direcciones.data = data.direcciones
        }
      }).catch(error => {
        this.form_direcciones.loading = false
        console.error(error)
      })
    },
    async direccionRow (row) {
      this.form_direcciones.fields.empresa = row.razon_social
      this.form_direcciones.fields.empresa_id = row.id
      await this.getEstados()
      await this.cargarDirecciones()
      this.setView('grid_direcciones')
    },
    newDireccionRow () {
      this.$v.form_direcciones.$reset()
      this.form_direcciones.fields.id = 0
      this.form_direcciones.fields.tipo = ''
      this.form_direcciones.fields.calle = ''
      this.form_direcciones.fields.num_ext = ''
      this.form_direcciones.fields.num_int = ''
      this.form_direcciones.fields.colonia = ''
      this.form_direcciones.fields.poblacion = ''
      this.form_direcciones.fields.municipio_id = 0
      this.form_direcciones.fields.estado_id = 0
      this.form_direcciones.fields.cp = ''
      this.form_direcciones.fields.tipo_oficina = ''
      this.form_direcciones.fields.tipo_propiedad = ''
      this.form_direcciones.municipiosOptions = []
      this.setView('create_direccion')
    },
    async editDireccionRow (row) {
      this.form_direcciones.municipiosOptions = []
      let direccion = { ...row }
      this.form_direcciones.fields.id = row.id
      this.form_direcciones.fields.tipo = direccion.tipo
      this.form_direcciones.fields.calle = direccion.calle
      this.form_direcciones.fields.num_ext = direccion.num_ext
      this.form_direcciones.fields.num_int = direccion.num_int
      this.form_direcciones.fields.colonia = direccion.colonia
      this.form_direcciones.fields.poblacion = direccion.poblacion
      if (direccion.estado_id === null) {
        this.form_direcciones.fields.estado_id = 0
        this.form_direcciones.fields.municipio_id = 0
      } else {
        this.form_direcciones.fields.estado_id = direccion.estado_id
        await this.cargaMunicipios()
        if (direccion.municipio_id === null) {
          this.form_direcciones.fields.municipio_id = 0
        } else {
          this.form_direcciones.fields.municipio_id = direccion.municipio_id
        }
      }
      this.form_direcciones.fields.cp = direccion.cp
      this.form_direcciones.fields.tipo_oficina = direccion.tipo_oficina
      this.form_direcciones.fields.tipo_propiedad = direccion.tipo_propiedad
      this.setView('update_direccion')
    },
    deleteDireccionSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar esta dirección?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.deleteDireccion({id: id}).then(({data}) => {
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
            this.cargarDirecciones()
            this.setView('grid_direcciones')
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
    },
    save_direccion () {
      this.$v.form_direcciones.$touch()
      if (!this.$v.form_direcciones.$error) {
        this.loadingButton = true
        let params = this.form_direcciones.fields
        this.saveDireccion(params).then(({data}) => {
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
            this.cargarDirecciones()
            this.setView('grid_direcciones')
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    update_direccion () {
      this.$v.form_direcciones.$touch()
      if (!this.$v.form_direcciones.$error) {
        this.loadingButton = true
        let params = this.form_direcciones.fields
        this.updateDireccion(params).then(({data}) => {
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
            this.cargarDirecciones()
            this.setView('grid_direcciones')
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
    reporteClientes (id) {
      this.props = []
      this.reporte_clientes = true
      this.getClientesByEmpresa({empresa_id: id}).then(({data}) => {
        this.props = data.resultado
        this.propsExpanded = ['Clientes']
      }).catch(error => {
        console.error(error)
      })
    },
    cargaMunicipios () {
      this.form_direcciones.municipiosOptions = []
      this.form_direcciones.fields.municipio_id = 0
      this.getMunicipiosByEstado({estado_id: this.form_direcciones.fields.estado_id}).then(({data}) => {
        this.form_direcciones.municipiosOptions.push({label: '---Ninguno---', value: 0})
        if (data.municipios.length > 0) {
          data.municipios.forEach(municipio => {
            this.form_direcciones.municipiosOptions.push({label: municipio.nombre, value: municipio.id})
          })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    numberOnly (event) {
      let allowedKeys = [8]
      let numeros = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105]
      let key = event.keyCode
      if (!numeros.includes(key) && !allowedKeys.includes(key)) {
        event.preventDefault()
      }
    },
    clickDireccion (row) {
      this.form_direcciones.informacion_direccion = true
      this.form_direcciones.objetoInformacion_direccion = row
    },
    getSubempresas () {
      this.form_subempresa.loading = true
      this.getEmpresasByPadre({padre: this.form.fields.id}).then(({data}) => {
      }).catch(error => {
        console.error(error)
      })
      this.form_subempresa.loading = false
    },
    modalSucursal (show) {
      this.grid_sucursales = show
      if (show === false) {
        this.setView('grid')
      }
    },
    sucursalesRow (row, show) {
      this.setView('grid_sucursales')
      this.empresa_id = row.id
      this.razon_social = row.razon_social
      this.grid_sucursales = show
    }
  },
  validations: {
    form: {
      fields: {
        razon_social: { required, maxLength: maxLength(100) },
        rfc: {required, maxLength: maxLength(13), minLength: minLength(12), rfc},
        rep_legal: {required, maxLength: maxLength(100), nombreRep},
        curp_representante: {maxLength: maxLength(18), minLength: minLength(18), curp},
        regimen: {required, maxLength: maxLength(100)},
        nombre_corto: {required, maxLength: maxLength(100)},
        rfc_representante: {maxLength: maxLength(13), minLength: minLength(12), rfc}
      }
    },
    form_subempresa: {
      fields: {
        razon_social: { required, maxLength: maxLength(100) },
        rfc: {required, maxLength: maxLength(13), minLength: minLength(12), rfc},
        rep_legal: {required, maxLength: maxLength(100), nombreRep},
        curp_representante: {maxLength: maxLength(18), minLength: minLength(18), curp},
        regimen: {required, maxLength: maxLength(100)},
        nombre_corto: {required, maxLength: maxLength(100)},
        rfc_representante: {maxLength: maxLength(13), minLength: minLength(12), rfc}
      }
    },
    form_direcciones: {
      fields: {
        tipo: {required},
        tipo_oficina: {required},
        tipo_propiedad: {required},
        cp: {numeric, minLength: minLength(5), maxLength: maxLength(5)}
      }
    }
  }
}
</script>

<style scoped>
#sticky-table-scroll .q-table th{
  text-align: center;
}
</style>
