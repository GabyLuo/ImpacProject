<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Padrón de proveedores"/>
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
                  :data="proveedores"
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
                      <q-td key="folio" :props="props" @click.native="clickFila(props.row)" style="cursor: pointer;">{{ props.row.folio }}</q-td>
                      <q-td style="text-align: left;" key="estado" :props="props">{{ props.row.estado }}, {{ props.row.municipio }}</q-td>
                      <q-td style="text-align: left;" key="empresa" :props="props">{{ props.row.empresa }}</q-td>
                      <q-td key="fecha_renovacion" :props="props">{{ props.row.fecha_renovacion }}</q-td>
                      <q-td key="status" :props="props">
                        <q-chip dense icon="fas fa-arrow-circle-right" color="amber" text-color="white" v-if="props.row.status==='EN PROCESO'">{{ props.row.status }}</q-chip>
                        <q-chip dense icon="highlight_off" color="red-14" text-color="white" v-if="props.row.status==='NEGADA'">{{ props.row.status }}</q-chip>
                        <q-chip dense icon="done_all" color="green" text-color="white" v-if="props.row.status==='CONCLUIDA'">{{ props.row.status }}</q-chip>
                      </q-td>
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

    <!--Crear un proveedor-->

    <div v-if="views.create">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Padrón de proveedores" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el label="Nuevo proveedor"/>
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
              <div class="col-sm-3">
                <q-field icon="fas fa-globe" icon-color="dark" :error="$v.form.fields.estado_id.$error" error-label="Elija un estado">
                  <q-select v-model="form.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()"></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-map-pin" icon-color="dark" :error="$v.form.fields.municipio_id.$error" error-label="Elija un municipio">
                  <q-select v-model="form.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="municipiosOptions" filter></q-select>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fields.empresa_id.$error" error-label="Elija una empresa">
                  <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="label" icon-color="dark">
                  <q-select v-model="form.fields.status" inverted color="dark" float-label="Estatus" :options="selectStatus" filter/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-12">
                <q-field icon="fas fa-building" icon-color="dark">
                  <q-input upper-case v-model="form.fields.observaciones_proceso" inverted color="dark" float-label="Observaciones estatus" maxlength="100" v-if="form.fields.status  === 'EN PROCESO'"/>
                  <q-input upper-case v-model="form.fields.observaciones_negada" inverted color="dark" float-label="Observaciones estatus" maxlength="100" v-if="form.fields.status  === 'NEGADA'"/>
                  <q-input upper-case v-model="form.fields.observaciones_concluida" inverted color="dark" float-label="Observaciones estatus" maxlength="100" v-if="form.fields.status  === 'CONCLUIDA'"/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="fas fa-thumbtack" icon-color="dark">
                  <q-input upper-case inverted color="dark" v-model="form.fields.folio" type="text" float-label="Número de proveedor" maxlength="20" />
                </q-field>
              </div>
              <div class="col-sm-5">
                <q-field icon="fas fa-building" icon-color="dark">
                  <q-input upper-case v-model="form.fields.institucion" inverted color="dark" float-label="Institución" maxlength="100"/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="date_range" icon-color="dark">
                  <q-datetime v-model="form.fields.fecha_registro" type="date" inverted color="dark" float-label="Fecha registro" align="center" @input="aumentoFechaRenovacion"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-2" v-if="!fecha_renovacion_indefinida">
                <q-field icon="date_range" icon-color="dark">
                  <q-datetime v-model="form.fields.fecha_renovacion" type="date" inverted color="dark" float-label="Fecha renovación" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-2" v-else>
                <q-field icon="date_range" icon-color="dark">
                  <q-input inverted color="dark" float-label="Fecha renovacion" readonly value="-----Indefinida-----"></q-input>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs" style="margin-top:5px;">
              <div class="col-sm-3 offset-sm-9 pull-right">
                <q-checkbox v-model="fecha_renovacion_indefinida" label="Fecha indefinida" color="amber"/>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

      <!-- Modal editar PRROVEEDOR -->
    <div v-if="views.update">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Padrón de proveedores" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el :label="form.fields.folio"/>
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
              <div class="col-sm-3">
                <q-field icon="fas fa-globe" icon-color="dark" :error="$v.form.fields.estado_id.$error" error-label="Elija un estado">
                  <q-select v-model="form.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()"></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-map-pin" icon-color="dark" :error="$v.form.fields.municipio_id.$error" error-label="Elija un municipio">
                  <q-select v-model="form.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="municipiosOptions" filter></q-select>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fields.empresa_id.$error" error-label="Elija una empresa">
                  <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="label" icon-color="dark">
                  <q-select v-model="form.fields.status" inverted color="dark" float-label="Estatus" :options="selectStatus" filter/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-12">
                <q-field icon="fas fa-building" icon-color="dark">
                  <q-input upper-case v-model="form.fields.observaciones_proceso" inverted color="dark" float-label="Observaciones estatus" maxlength="100" v-if="form.fields.status  === 'EN PROCESO'"/>
                  <q-input upper-case v-model="form.fields.observaciones_negada" inverted color="dark" float-label="Observaciones estatus" maxlength="100" v-if="form.fields.status  === 'NEGADA'"/>
                  <q-input upper-case v-model="form.fields.observaciones_concluida" inverted color="dark" float-label="Observaciones estatus" maxlength="100" v-if="form.fields.status  === 'CONCLUIDA'"/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="fas fa-thumbtack" icon-color="dark">
                  <q-input upper-case inverted color="dark" v-model="form.fields.folio" type="text" float-label="Número de proveedor" maxlength="20" />
                </q-field>
              </div>
              <div class="col-sm-5">
                <q-field icon="fas fa-building" icon-color="dark">
                  <q-input upper-case v-model="form.fields.institucion" inverted color="dark" float-label="Institución" maxlength="100"/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="date_range" icon-color="dark">
                  <q-datetime v-model="form.fields.fecha_registro" type="date" inverted color="dark" float-label="Fecha registro" align="center" @input="aumentoFechaRenovacion"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-2" v-if="!fecha_renovacion_indefinida">
                <q-field icon="date_range" icon-color="dark">
                  <q-datetime v-model="form.fields.fecha_renovacion" type="date" inverted color="dark" float-label="Fecha renovación" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-2" v-else>
                <q-field icon="date_range" icon-color="dark">
                  <q-input inverted color="dark" float-label="Fecha renovacion" readonly value="-----Indefinida-----"></q-input>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs" style="margin-top:5px;">
              <div class="col-sm-3 offset-sm-9 pull-right">
                <q-checkbox v-model="fecha_renovacion_indefinida" label="Fecha indefinida" color="amber"/>
              </div>
            </div>
            <div class="row q-pa-md">
              CONSTANCIA
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <input style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="fileInputFormato" accept=".jpg,.jpeg,.pdf,.png,.docx" @change="uploadFormato()" />
                <q-btn small flat @click="$refs.fileInputFormato.click()" color="green" :loading="loadingButton">
                  <i class="fa fa-upload" aria-hidden="true"></i>&nbsp;Examinar
                </q-btn>
                <q-btn small flat @click="descargarFormato()" color="black" :loading="loadingButton" v-if="!(form.fields.formato_requisito_id===null)">
                  <i class="fa fa-download" aria-hidden="true"></i>&nbsp;Descargar
                </q-btn>
                <q-btn small flat @click="deleteFormatoByProveedor()" color="negative" icon="delete" v-if="!(form.fields.formato_requisito_id===null)" :loading="loadingButton"/>
              </div>
            </div>
            <div class="row q-pa-md">
              NUEVO REQUISITO
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="fas fa-thumbtack" icon-color="dark" :error="$v.form_requisitos.fields.nombre.$error" error-label="Escriba un nombre">
                  <q-input upper-case inverted color="dark" v-model="form_requisitos.fields.nombre" type="text" float-label="Nombre del requisito" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-2 pull-right">
                <q-btn @click="save_requisito()" class="btn_guardar" icon-right="add" :loading="loadingButton" v-if="this.form_requisitos.editar_requisito===false">Agregar</q-btn>
                <q-btn @click="editar_requisito()" style="background-color: orange;" icon-right="save" :loading="loadingButton" v-else>Guardar</q-btn>
              </div>
            </div>
            <div class="row q-pa-md">
              LISTA DE REQUISITOS
            </div>
            <div class="col q-pa-md border-panel" style="margin-top:5px;">
                <q-table id="sticky-table-newstyle"
                  :data="form_requisitos.data"
                  :columns="form_requisitos.columns"
                  :selected.sync="form_requisitos.selected"
                  color="positive"
                  title=""
                  :dense="true"
                  :pagination.sync="form_requisitos.pagination"
                  :loading="form_requisitos.loading"
                  :rows-per-page-options="form_requisitos.rowsOptions">
                  <template slot="body" slot-scope="props">
                    <q-tr :props="props">
                      <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                      <q-td key="documentos" :props="props">
                        <div style="display: inline;" v-for="doc in props.row.documentos" :key="doc.id">
                          <q-btn small flat :icon="doc.icon" :color="doc.color">
                            <q-tooltip>{{ doc.name }}</q-tooltip>
                            <q-popover>
                              <q-list link separator class="scroll" style="min-width: 100px">
                                <q-item v-close-overlay @click.native="verDocumento(doc)" v-if="doc.doc_type !== 'docx' && doc.doc_type !== 'DOCX'">
                                  <q-item-main label="Ver"/>
                                </q-item>
                                <q-item v-close-overlay @click.native="descargarDocumento(doc.id, doc.doc_type)">
                                  <q-item-main label="Descargar"/>
                                </q-item>
                                <q-item v-close-overlay @click.native="documentoAccion(doc.id)">
                                  <q-item-main label="Eliminar"/>
                                </q-item>
                              </q-list>
                            </q-popover>
                          </q-btn>
                        </div>
                      </q-td>
                      <q-td key="acciones" :props="props">
                        <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="fileInput" accept=".jpg,.jpeg,.pdf,.png,.docx" @change="uploadImageFile()" />
                        <q-btn small flat @click="selectedFile(props.row)" color="green" :loading="loadingButton">
                          <q-icon name="cloud_upload" />&nbsp;
                          <q-tooltip>Subir archivo</q-tooltip>
                        </q-btn>
                        <q-btn small flat @click="editRow_requisito(props.row)" color="blue-6" icon="edit">
                          <q-tooltip>Editar</q-tooltip>
                        </q-btn>
                        <q-btn small flat @click="deleteSingleRow_requisito(props.row.id)" color="negative" icon="delete" v-if="props.row.padron_id !== null">
                          <q-tooltip>Eliminar</q-tooltip>
                        </q-btn>
                      </q-td>
                    </q-tr>
                  </template>
                </q-table>
                <q-inner-loading :visible="form_requisitos.loading">
                  <q-spinner-dots size="64px" color="primary" />
                </q-inner-loading>
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
            INFORMACIÓN GENERAL
          </div>
          <div class="row q-mt-lg" style="margin-right: 15px;">
            <div class="col-sm-6">
              <q-field icon="fas fa-user" icon-color="dark">
                <q-select readonly v-model="form.fields.cliente_id" inverted-color="dark" float-label="Cliente" :options="clientesOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-6">
              <q-field icon="fas fa-building" icon-color="dark">
                <q-select readonly v-model="form.fields.empresa_id" inverted-color="dark" float-label="Empresa" :options="empresasOptions" filter/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right: 15px;">
            <div class="col-sm-6">
              <q-field icon="fas fa-thumbtack" icon-color="dark">
                <q-input readonly upper-case inverted-color="dark" v-model="form.fields.folio" type="text" float-label="Número de proveedor" maxlength="20" />
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="date_range" icon-color="dark">
                <q-datetime readonly v-model="form.fields.fecha_registro" type="date" inverted-color="dark" float-label="Fecha registro" align="center" @input="aumentoFechaRenovacion"></q-datetime>
              </q-field>
            </div>
            <div class="col-sm-3" v-if="!fecha_renovacion_indefinida">
              <q-field icon="date_range" icon-color="dark">
                <q-datetime readonly v-model="form.fields.fecha_renovacion" type="date" inverted-color="dark" float-label="Fecha renovación" align="center"></q-datetime>
              </q-field>
            </div>
            <div class="col-sm-3" v-else>
              <q-field icon="date_range" icon-color="dark">
                <q-input inverted-color="dark" float-label="Fecha renovacion" readonly value="-----Indefinida-----"></q-input>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-top:5px;margin-right: 15px;">
            <div class="col-sm-3 offset-sm-9 pull-right">
              <q-checkbox readonly v-model="fecha_renovacion_indefinida" label="Fecha indefinida" color="amber"/>
            </div>
          </div>
          <div class="row q-mt-lg subtitulos" style="margin-left: 15px;">
            FORMATO REQUISITO
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-6">
              <q-btn disable small flat color="teal" v-if="form.fields.formato_requisito_id===null" style="margin-left: 15px;">
                NO HAY ARCHIVOS
              </q-btn>
              <q-btn small flat @click="descargarFormato()" color="black" :loading="loadingButton" v-if="!(form.fields.formato_requisito_id===null)">
                <i class="fa fa-download" aria-hidden="true"></i>&nbsp;Descargar
              </q-btn>
            </div>
          </div>
          <div class="row q-mt-lg subtitulos" style="margin-left: 15px;">
            LISTA DE REQUISITOS
          </div>
          <div class="row q-mt-lg" style="margin-top:5px; margin-left: 15px; margin-right: 15px; margin-bottom: 15px;">
            <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
              <q-table id="sticky-table"
                :data="form_requisitos.data"
                :columns="form_requisitos.columns"
                :selected.sync="form_requisitos.selected"
                color="positive"
                title=""
                :dense="true"
                :pagination.sync="form_requisitos.pagination"
                :loading="form_requisitos.loading"
                :rows-per-page-options="form_requisitos.rowsOptions">
                <template slot="body" slot-scope="props">
                  <q-tr :props="props">
                    <q-td key="requisito_nombre" :props="props">{{ props.row.requisito_nombre }}</q-td>
                    <q-td key="requisito_tipo" :props="props">{{ props.row.requisito_tipo }}</q-td>
                    <q-td key="status" :props="props"><q-checkbox v-model="props.row.status" readonly color="teal"></q-checkbox></q-td>
                    <q-td key="acciones" :props="props">
                      <q-btn small flat @click="descargarArchivo(props.row)" color="gray" icon="cloud_download" v-if="!(props.row.documento_id === null)">
                        <q-tooltip>Descargar</q-tooltip>
                      </q-btn>
                    </q-td>
                  </q-tr>
                </template>
              </q-table>
              <q-inner-loading :visible="form_requisitos.loading">
                <q-spinner-dots size="64px" color="primary" />
              </q-inner-loading>
            </div>
          </div>
        </q-modal-layout>
      </q-modal>
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { required, minValue } from 'vuelidate/lib/validators'
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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'COBRANZA'.toUpperCase() || propiedades[i].toUpperCase() === 'PLANEACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'COMPRAS'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS/COMISIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'AUXILIAR TESORERIA'.toUpperCase()) {
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
      municipiosOptions: [],
      selectStatus: [ { 'label': 'EN PROCESO', 'value': 'EN PROCESO' }, { 'label': 'NEGADA', 'value': 'NEGADA' }, { 'label': 'CONCLUIDA', 'value': 'CONCLUIDA' } ],
      registro: null,
      loadingButton: false,
      informacion: false,
      objetoInformacion: null,
      fecha_renovacion_indefinida: false,
      views: {
        grid: true,
        create: false,
        update: false
      },
      form: {
        fields: {
          id: 0,
          cliente_id: 0,
          empresa_id: 0,
          folio: '',
          fecha_registro: '',
          fecha_renovacion: '',
          formato_requisito_id: 0,
          nombre_documento: '',
          nombre_requisito: '',
          estado_id: 0,
          municipio_id: -1,

          cliente: '',
          empresa: '',
          institucion: '',
          status: '',
          observaciones: ''
        },
        // data: [],
        columns: [
          { name: 'folio', label: 'Núm. de proveedor', field: 'folio', sortable: true, type: 'string', align: 'left' },
          { name: 'estado', label: 'Estado-municipio', field: 'estado', sortable: true, type: 'string', align: 'left' },
          { name: 'empresa', label: 'Empresa', field: 'empresa', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha_renovacion', label: 'Fecha de vencimiento', field: 'fecha_renovacion', sortable: true, type: 'string', align: 'center' },
          { name: 'status', label: 'Estatus', field: 'status', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'center' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_requisitos: {
        fields: {
          id: 0,
          nombre: '',
          padron_id: ''
        },
        editar_requisito: false,
        requisitos: [],
        columns: [
          { name: 'nombre', label: 'Requisito', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'documentos', label: 'Archivos', field: 'documentos', sortable: true, type: 'string', align: 'center' },
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
      clientesOptions: 'ventas/clientes/selectOptions',
      empresasOptions: 'vnt/empresa/selectOptions',
      proveedores: 'vnt/proveedor/proveedores',
      estadosOptions: 'vnt/estado/selectOptions'
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
    fecha_renovacion_indefinida (newValue, old) {
      if (newValue === true) {
        this.form.fields.fecha_renovacion = ''
      }
    }
  },
  methods: {
    ...mapActions({
      getProveedores: 'vnt/proveedor/refresh',
      saveProveedor: 'vnt/proveedor/save',
      updateProveedor: 'vnt/proveedor/update',
      deleteProveedor: 'vnt/proveedor/delete',
      getClientes: 'ventas/clientes/refresh',
      getEmpresas: 'vnt/empresa/refresh',

      getRequisitosByProveedor: 'vnt/padronRequisitos/getByProveedor',
      getFormato: 'vnt/proveedor/getFormato',
      deleteFormato: 'vnt/proveedor/deleteFormato',
      getEstados: 'vnt/estado/refresh',
      getMunicipiosByEstado: 'vnt/municipio/getByEstado',
      newRequisito: 'vnt/proveedor/save_requisito',
      editRequisito: 'vnt/proveedor/update_requisito',
      deleteRequisito: 'vnt/proveedor/delete_requisito',
      deleteFile: 'vnt/padronRequisitos/deleteFile'
    }),
    aumentoFechaRenovacion () {
      let fechaRenovacion = moment(String(this.form.fields.fecha_registro)).add(1, 'years').utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      this.form.fields.fecha_renovacion = fechaRenovacion
    },
    async loadAll () {
      this.form.loading = true
      this.municipiosOptions.push({label: '---Ninguno---', value: -1})
      this.municipiosOptions.push({label: 'ESTATAL', value: 0})
      await this.getEstados()
      await this.getEmpresas()
      await this.getProveedores()
      this.estadosOptions.push({label: '---Seleccione---', value: 0})
      this.empresasOptions.push({label: '---Seleccione---', value: 0})
      this.form.loading = false
    },
    cargaMunicipios () {
      this.municipiosOptions = []
      this.form.fields.municipio_id = -1
      this.getMunicipiosByEstado({estado_id: this.form.fields.estado_id}).then(({data}) => {
        this.municipiosOptions.push({label: '---Ninguno---', value: -1})
        this.municipiosOptions.push({label: 'ESTATAL', value: 0})
        if (data.municipios.length > 0) {
          data.municipios.forEach(municipio => {
            this.municipiosOptions.push({label: municipio.nombre, value: municipio.id})
          })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    validarFechas () {
      if (this.form.fields.fecha_renovacion === '' || this.form.fields.fecha_renovacion === null) {
        return true
      } else {
        if (moment(String(this.form.fields.fecha_registro)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss') <= moment(String(this.form.fields.fecha_renovacion)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')) {
          return true
        }
      }
      return false
    },
    mensajeNotify (message, time, color, icon, detail, position) {
      this.$q.notify({
        message: message,
        timeout: time,
        color: color,
        icon: icon,
        detail: detail,
        position: position
      })
    },
    save () {
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        if (this.validarFechas()) {
          this.loadingButton = true
          let params = this.form.fields
          this.saveProveedor(params).then(({data}) => {
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
          this.mensajeNotify('Revisa las fechas', 3000, 'negative', 'fas fa-exclamation-triangle', 'La fecha de registro no debe ser mayor a la fecha de renovación.', 'top-right')
        }
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    update () {
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        if (this.validarFechas()) {
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
          this.mensajeNotify('Revisa las fechas', 3000, 'negative', 'fas fa-exclamation-triangle', 'La fecha de registro no debe ser mayor a la fecha de renovación.', 'top-right')
        }
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    async editRow (row) {
      this.$q.loading.show({message: 'Cargando...'})
      let proveedor = { ...row }
      this.form.fields.id = row.id
      this.form_requisitos.fields.padron_id = row.id
      this.form_requisitos.fields.nombre = ''
      this.form.fields.cliente_id = proveedor.cliente_id
      this.form.fields.empresa_id = proveedor.empresa_id
      this.form.fields.folio = proveedor.folio
      this.form.fields.fecha_registro = moment(String(proveedor.fecha_registro)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      if (proveedor.fecha_renovacion === null) {
        this.form.fields.fecha_renovacion = ''
        this.fecha_renovacion_indefinida = true
      } else {
        this.form.fields.fecha_renovacion = moment(String(proveedor.fecha_renovacion)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
        this.fecha_renovacion_indefinida = false
      }
      this.form.fields.cliente = proveedor.cliente
      this.form.fields.empresa = proveedor.empresa
      this.form.fields.formato_requisito_id = proveedor.formato_requisito_id
      this.form.fields.nombre_documento = proveedor.nombre_documento
      this.form.fields.estado_id = proveedor.estado_id
      this.form.fields.municipio_id = proveedor.municipio_id
      this.form.fields.empresa_id = proveedor.empresa_id
      this.form.fields.status = proveedor.status
      this.form.fields.observaciones_proceso = proveedor.observaciones_proceso
      this.form.fields.observaciones_negada = proveedor.observaciones_negada
      this.form.fields.observaciones_concluida = proveedor.observaciones_concluida
      this.form.fields.institucion = proveedor.institucion
      this.form_requisitos.editar_requisito = false
      await this.cargarDocumentos()
      this.$q.loading.hide()
      this.setView('update')
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
    newRow () {
      this.$v.form.$reset()
      this.form.fields.id = 0
      this.form.fields.cliente_id = 0
      this.form.fields.empresa_id = 0
      this.form.fields.folio = ''
      this.form.fields.fecha_registro = ''
      this.form.fields.fecha_renovacion = ''
      this.fecha_renovacion_indefinida = false
      this.form.fields.cliente = ''
      this.form.fields.empresa = ''
      this.form.fields.formato_requisito_id = ''
      this.form.fields.nombre_documento = ''
      this.form.fields.estado_id = 0
      this.form.fields.municipio_id = -1
      this.form.fields.empresa_id = 0
      this.form.fields.status = 'EN PROCESO'
      this.form.fields.observaciones_proceso = ''
      this.form.fields.observaciones_negada = ''
      this.form.fields.observaciones_concluida = ''
      this.form.fields.institucion = ''
      this.form.fields.nombre_requisito = ''
      this.form_requisitos.editar_requisito = false
      this.setView('create')
    },
    async clickFila (row) {
      this.informacion = true
      this.$q.loading.show({message: 'Cargando...'})
      this.form.fields.id = row.id
      this.form.fields.cliente_id = row.cliente_id
      this.form.fields.empresa_id = row.empresa_id
      this.form.fields.folio = row.folio
      this.form.fields.fecha_registro = moment(String(row.fecha_registro)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      if (row.fecha_renovacion === null) {
        this.form.fields.fecha_renovacion = ''
        this.fecha_renovacion_indefinida = true
      } else {
        this.form.fields.fecha_renovacion = moment(String(row.fecha_renovacion)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
        this.fecha_renovacion_indefinida = false
      }
      this.form.fields.cliente = row.cliente
      this.form.fields.empresa = row.empresa
      this.form.fields.formato_requisito_id = row.formato_requisito_id
      this.form.fields.nombre_documento = row.nombre_documento
      await this.cargarDocumentos()
      this.$q.loading.hide()
    },
    async cargarDocumentos () {
      this.form_requisitos.loading = true
      this.form_requisitos.data = []
      await this.getRequisitosByProveedor({proveedor_id: this.form.fields.id, cliente_id: 0}).then(({data}) => {
        if (data.padron_requisitos.length > 0) {
          data.padron_requisitos.forEach(function (element) {
            element.documentos.forEach(function (documento) {
              if (documento.doc_type === 'docx') {
                documento.color = 'blue-9'
                documento.icon = 'fas fa-file-word'
              } else if (documento.doc_type === 'pdf' || documento.doc_type === 'PDF') {
                documento.color = 'red-10'
                documento.icon = 'fas fa-file-pdf'
              } else if (documento.doc_type === 'xml' || documento.doc_type === 'XML') {
                documento.color = 'light-green'
                documento.icon = 'fas fa-file-code'
              } else if (documento.doc_type === 'jpg' || documento.doc_type === 'jpeg' || documento.doc_type === 'png' || documento.doc_type === 'JPG' || documento.doc_type === 'JPEG' || documento.doc_type === 'PNG') {
                documento.color = 'amber'
                documento.icon = 'fas fa-file-image'
              }
            })
          })
          this.form_requisitos.data = data.padron_requisitos
        }
        this.form_requisitos.loading = false
      }).catch(error => {
        this.form_requisitos.loading = false
        console.error(error)
      })
    },
    async getFormatoByProveedor () {
      await this.getFormato({proveedor_id: this.form.fields.id}).then(({data}) => {
        if (data.formato.length > 0) {
          this.form.fields.formato_requisito_id = data.formato[0].formato_requisito_id
          this.form.fields.nombre_documento = data.formato[0].nombre_documento
        }
      }).catch(error => {
        console.error(error)
      })
    },
    deleteFormatoByProveedor () {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar la constancia de este proveedor?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.loadingButton = true
        let params = {id: this.form.fields.id, formato_requisito_id: this.form.fields.formato_requisito_id}
        this.deleteFormato(params).then(({data}) => {
          if (data.result === 'success') {
            this.form.fields.formato_requisito_id = null
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'done',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.getFormatoByProveedor()
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
          this.loadingButton = false
        }).catch(error => {
          console.error(error)
          this.loadingButton = false
        })
      }).catch(() => {})
    },
    descargarArchivo (row) {
      window.open(process.env.API + '/public/assets/expedientes/' + row.nombre_archivo)
    },
    selectedFile (row) {
      this.$refs.fileInput.value = ''
      this.registro = row
      this.$refs.fileInput.click()
    },
    uploadImageFile () {
      try {
        this.loadingButton = true
        let file = this.$refs.fileInput.files[0]
        let formData = new FormData()
        formData.append('file', file)
        formData.append('nombre', file.name)
        formData.append('id', this.registro.id)
        formData.append('proveedor_id', this.form.fields.id)
        if (file === null || file === undefined) {
          this.loadingButton = false
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('padron_requisitos/uploadArchivo', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              if (response.data.result === 'success') {
                this.$q.notify({
                // only required parameter is the message:
                  message: 'Se ha subido el archivo',
                  timeout: 3000,
                  type: 'green',
                  textColor: 'white', // if default 'white' doesn't fits
                  icon: 'done',
                  position: 'top-right' // 'top', 'left', 'bottom-left' etc
                })
                this.cargarDocumentos()
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
          this.$showMessage('Error', 'No ha seleccionado un archivo con las extensión .jpg, .jpeg, .png o .pdf')
        }
      } catch (error) {
        this.loadingButton = false
      }
    },
    uploadFormato () {
      try {
        this.loadingButton = true
        let file = this.$refs.fileInputFormato.files[0]
        let formData = new FormData()
        formData.append('file', file)
        formData.append('proveedor_id', this.form.fields.id)
        formData.append('formato_requisito_id', this.form.fields.formato_requisito_id)
        if (file === null || file === undefined) {
          this.loadingButton = false
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('proveedores/uploadArchivo', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              if (response.data.result === 'success') {
                this.$q.notify({
                  // only required parameter is the message:
                  message: 'Se ha subido el archivo',
                  timeout: 3000,
                  type: 'green',
                  textColor: 'white', // if default 'white' doesn't fits
                  icon: 'done',
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
          this.$showMessage('Error', 'No ha seleccionado un archivo con las extensión .jpg, .jpeg, .png o .pdf')
        }
      } catch (error) {
        this.loadingButton = false
      }
    },
    descargarFormato () {
      window.open(process.env.API + '/public/assets/expedientes/' + this.form.fields.nombre_documento)
    },
    save_requisito () {
      this.$v.form_requisitos.$touch()
      if (!this.$v.form_requisitos.$error) {
        this.loadingButton = true
        let params = this.form_requisitos.fields
        this.newRequisito(params).then(({data}) => {
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
            this.form_requisitos.fields.nombre = ''
            this.$v.form_requisitos.$reset()
            this.cargarDocumentos()
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
    editRow_requisito (row) {
      this.form_requisitos.fields.id = row.id
      this.form_requisitos.fields.nombre = row.nombre
      this.form_requisitos.editar_requisito = true
    },
    editar_requisito () {
      this.$v.form_requisitos.$touch()
      if (!this.$v.form_requisitos.$error) {
        this.loadingButton = true
        let params = this.form_requisitos.fields
        this.editRequisito(params).then(({data}) => {
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
            this.form_requisitos.fields.nombre = ''
            this.$v.form_requisitos.$reset()
            this.form_requisitos.editar_requisito = false
            this.cargarDocumentos()
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
    deleteSingleRow_requisito (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este requisito?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.loadingButton = true
        let params = {id: id}
        this.deleteRequisito(params).then(({data}) => {
          if (data.result === 'success') {
            this.form.fields.formato_requisito_id = null
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'done',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.cargarDocumentos()
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
          this.loadingButton = false
        }).catch(error => {
          console.error(error)
          this.loadingButton = false
        })
      }).catch(() => {})
    },
    verDocumento (documento) {
      var uri = process.env.API + '/public/assets/documentos_padron/' + documento.id + '.' + documento.doc_type
      window.open(uri, '_blank')
    },
    documentoAccion (id) {
      this.$q.dialog({
        title: 'Confirmar acción',
        message: 'Se eliminará el documento',
        ok: 'Eliminar',
        cancel: 'Cancelar',
        preventClose: true
      }).then(() => {
        let params = {id: id}
        this.deleteFile(params).then(({data}) => {
          if (data.result === 'success') {
            this.form.fields.formato_requisito_id = null
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'done',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.cargarDocumentos()
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
          this.loadingButton = false
        }).catch(error => {
          console.error(error)
          this.loadingButton = false
        })
      }).catch(() => {})
    },
    descargarDocumento (id, ext) {
      let uri = process.env.API + `padron_requisitos/getFile/${id}/${ext}`
      window.open(uri, '_blank')
    }
  },
  validations: {
    form: {
      fields: {
        estado_id: { required, minValue: minValue(1) },
        municipio_id: { required, minValue: minValue(0) },
        empresa_id: {required, minValue: minValue(1)}
      }
    },
    form_requisitos: {
      fields: {
        nombre: { required }
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
</style>
