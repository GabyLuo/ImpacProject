<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header bg-white">
        <div class="row">
            <div class=" row col">
          <div class="col-sm-4">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el  style="font-weight: normal;margin-left: 0;" class="grey-8 fs28 page-title"  label="Proyectos" />
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-4">
            <center>
             <div class="q-pa-sm q-gutter-sm">
            <q-btn-toggle color="teal" v-model="year" toggle-color="primary" :options="selectYear" @input="filterByYear()"/>
          </div>
           </center>
          </div>
          <div class="col-sm-4 pull-right">
             <div class="q-pa-sm q-gutter-sm">
            <q-btn class="btn_nuevo" icon="add" label="Nuevo" @click="newRow()" v-if="role !== 'Cobranza' && role !== 'Operación'"/>
          </div>
          </div>
        </div>
      </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel" style="padding:0;">
          <div class="col">
            <div class="col q-pa-md border-panel">
              <div class="row q-col-gutter-xs">
                <div class="col-sm-3">
                  <q-field icon="work" icon-color="dark">
                    <q-select v-model="form.sucursal" inverted color="dark" float-label="Sucursal" :options="sucursalesOptions" filter @input="busca()"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="work" icon-color="dark">
                    <q-select v-model="form.busqueda" inverted color="dark" float-label="Búsqueda" :options="busquedaOptions" filter @input="busca()"/>
                  </q-field>
                </div>
                <div class="col-sm-6">
                  <q-field icon="search" icon-color="dark">
                    <q-input v-model="form.filter" inverted color="dark" float-label="Proyecto" v-if="form.busqueda === 'PROYECTO'" />
                    <q-input upper-case v-model="form.filter_presupuesto" inverted color="dark" float-label="Presupuesto" v-if="form.busqueda === 'PRESUPUESTO'" @input="filterByYear"/>
                  </q-field>
                </div>
              </div>
              <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                <q-table id="sticky-table-newstyle"
                  :data="form.data_recursos"
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
                      <q-td key="codigo" :props="props" @click.native="clickFila(props.row)" style="cursor: pointer;">{{ props.row.codigo }}</q-td>
                      <q-td style="text-align: left;" key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                      <q-td style="text-align: left;" key="razon_social" :props="props">{{ props.row.razon_social }}</q-td>
                      <q-td key="programa" :props="props">{{ props.row.programa_nombre }}</q-td>
                      <q-td key="subprograma" :props="props">{{ props.row.subprograma_nombre }}</q-td>
                      <q-td key="sucursal" :props="props">{{ props.row.sucursal }}</q-td>
                      <q-td key="creador" :props="props">{{ props.row.creador }}</q-td>
                      <q-td key="fecha_creacion" style="text-align: center;" :props="props">{{ props.row.fecha_creacion }}</q-td>
                      <q-td key="monto" :props="props">${{ currencyFormat(props.row.monto) }}</q-td>
                      <q-td key="rubro" :props="props">{{ props.row.rubro }}</q-td>
                      <q-td key="year" style="text-align: center;" :props="props">{{ props.row.year }}</q-td>
                      <q-td key="acciones" :props="props">
                        <q-btn small flat @click="editRow(props.row)" color="blue-6" icon="fas fa-eye" v-if="role === 'Lider'">
                          <q-tooltip>Ver</q-tooltip>
                        </q-btn>
                        <q-btn small flat @click="editRow(props.row)" color="blue-6" icon="edit" v-if="role !== 'Lider'">
                          <q-tooltip>Editar</q-tooltip>
                        </q-btn>
                        <q-btn small flat @click="deleteSingleRow(props.row.id)" color="negative" icon="delete" v-if="role !== 'Lider' && role !== 'Finanzas/Comisiones'">
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
    <!--Crear un recurso-->
    <div v-if="views.create">
      <div class="q-pa-sm panel-header">
        <div class="row col">
          <div class="col-sm-4">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Proyectos" class="q-ml-sm grey-8 fs28 page-title" to="" style="font-weight: normal;margin-left: 0;" @click.native="setView('grid')"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title"  style="font-weight: normal;margin-left: 0;padding-left: 0;" label="Nuevo proyecto"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-4 pull-right">
            <div class="q-pa-sm q-gutter-sm">
            <q-btn-toggle color="teal" v-model="year" toggle-color="primary" :options="selectYear"/>
          </div>
          </div>
           <div class="col-sm-4 pull-right">
            <div class="q-pa-sm q-gutter-sm">
            <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
            <q-btn @click="save()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
          </div>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel" style="padding:0;">
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-4">
                <q-field icon="fas fa-user" icon-color="dark" :error="$v.form.fields.cliente_id.$error" error-label="Elija un cliente">
                  <q-select v-model="form.fields.cliente_id" inverted color="dark" float-label="Cliente" :options="clientesOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="fas fa-pen-square" icon-color="dark" :error="$v.form.fields.tipo.$error" error-label="Elija un programa general">
                  <q-select v-model="form.fields.tipo" inverted color="dark" float-label="Programa general" :options="programageneralOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-pen-square" icon-color="dark" :error="$v.form.fields.programa_id.$error" error-label="Elija un programa">
                  <q-select v-model="form.fields.programa_id" inverted color="dark" float-label="Programa" :options="programasOptions" filter @input="selectSubprograma()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="inbox" icon-color="dark" :error="$v.form.fields.subprograma_id.$error" error-label="Elija un subprograma">
                  <q-select v-model="form.fields.subprograma_id" inverted color="dark" float-label="Subprograma" :options="subprogramasOptions" filter/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-8">
                <q-field icon="inbox" icon-color="dark" :error="$v.form.fields.nombre.$error" error-label="Escriba un nombre">
                  <q-input upper-case v-model="form.fields.nombre" inverted color="dark" float-label="Nombre" maxlength="100"/>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="inbox" icon-color="dark">
                  <q-input upper-case v-model="form.fields.rubro" inverted color="dark" float-label="Rubro" maxlength="100"/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="inbox" icon-color="dark">
                  <q-input upper-case v-model="form.fields.aliado" inverted color="dark" float-label="Aliado principal" maxlength="1000"/>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="inbox" icon-color="dark">
                  <q-input upper-case v-model="form.fields.aliado1" inverted color="dark" float-label="Aliado" maxlength="100"/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="inbox" icon-color="dark">
                  <q-input upper-case v-model="form.fields.aliado2" inverted color="dark" float-label="Aliado" maxlength="100"/>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="inbox" icon-color="dark">
                  <q-input upper-case v-model="form.fields.aliado3" inverted color="dark" float-label="Aliado" maxlength="100"/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="inbox" icon-color="dark" :error="$v.form.fields.fuente_financiamiento.$error" error-label="Ingrese la fuente de financiamiento">
                  <q-input upper-case v-model="form.fields.fuente_financiamiento" inverted color="dark" float-label="Fuente de financiamiento" maxlength="100"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                  <q-input v-model.lazy="form.fields.montovalidar" v-money="money" inverted color="dark" float-label="Monto de la bolsa" suffix="MXN"></q-input>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                  <q-input v-model.lazy="form.fields.montovalidariva" v-money="money" inverted color="dark" float-label="Monto de la bolsa sin iva" suffix="MXN"></q-input>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="inbox" icon-color="dark">
                  <q-select v-model="form.fields.adjudicacion" inverted color="dark" float-label="Tipo de venta" :options="adjudicacionOptions" filter/>
                </q-field>
              </div>
            </div>
          </div>
          <!-- <div class="col q-pa-md col-sm-12">
            <div class="row q-col-gutter-xs">
                <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
                  <q-btn @click="save()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
                </div>
            </div>
          </div> -->
        </div>
      </div>
    </div>
    <!-- Modal editar Recurso -->
    <div v-if="views.update">
      <div class="q-pa-sm panel-header">
        <div class="row col">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left" style="font-weight: normal;">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el style="font-weight: normal;" class="grey-8 fs28 page-title"   label="Proyectos" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el  style="font-weight: normal;" class="grey-8 fs28 page-title"  label=" "/><label style="padding-top: 10px;">{{form.fields.codigo}} </label>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
            <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
            <q-btn @click="update()" class="btn_guardar" icon-right="save" :loading="loadingButton" v-if="role !== 'Operación'">Guardar</q-btn>
          </div>
        </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel" style="padding:0;">
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-2">
                <q-field icon="fas fa-key" icon-color="dark">
                  <q-input upper-case v-model="form.fields.codigo" inverted color="dark" float-label="Código" readonly/>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="fas fa-user" icon-color="dark" :error="$v.form.fields.cliente_id.$error" error-label="Elija un cliente">
                  <q-select v-model="form.fields.cliente_id" inverted color="dark" float-label="Cliente" :options="clientesOptions" filter v-if="role === 'Admin' || (role === 'Cobranza' && year === '2020') || role === 'Root' || role === 'Planeación'"/>
                  <q-select v-model="form.fields.cliente_id" inverted color="dark" float-label="Cliente" :options="clientesOptions" filter readonly v-else/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="fas fa-pen-square" icon-color="dark" :error="$v.form.fields.tipo.$error" error-label="Elija un programa general">
                  <q-select v-model="form.fields.tipo" inverted color="dark" float-label="Programa general" :options="programageneralOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="fas fa-pen-square" icon-color="dark" :error="$v.form.fields.programa_id.$error" error-label="Elija un programa">
                  <q-select v-model="form.fields.programa_id" inverted color="dark" float-label="Programa" :options="programasOptions" filter @input="selectSubprograma()" :readonly="role !== 'Admin' && role !== 'Planeación' && role !== 'Root'"/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="inbox" icon-color="dark" :error="$v.form.fields.subprograma_id.$error" error-label="Elija un subprograma">
                  <q-select v-model="form.fields.subprograma_id" inverted color="dark" float-label="Subprograma" :options="subprogramasOptions" filter :readonly="role !== 'Admin' && role !== 'Planeación' && role !== 'Root'"/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="inbox" icon-color="dark" :error="$v.form.fields.nombre.$error" error-label="Escriba el nombre del proyecto">
                  <q-input upper-case v-model="form.fields.nombre" inverted color="dark" float-label="Nombre" maxlength="100"/>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="inbox" icon-color="dark" :error="$v.form.fields.fuente_financiamiento.$error" error-label="Ingrese la fuente de financiamiento">
                  <q-input upper-case v-model="form.fields.fuente_financiamiento" inverted color="dark" float-label="Fuente de financiamiento" maxlength="100"/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="inbox" icon-color="dark">
                  <q-input upper-case v-model="form.fields.aliado" inverted color="dark" float-label="Aliado principal" maxlength="100"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="inbox" icon-color="dark">
                  <q-input upper-case v-model="form.fields.aliado1" inverted color="dark" float-label="Aliado" maxlength="100"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="inbox" icon-color="dark">
                  <q-input upper-case v-model="form.fields.aliado2" inverted color="dark" float-label="Aliado" maxlength="100"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="inbox" icon-color="dark">
                  <q-input upper-case v-model="form.fields.aliado3" inverted color="dark" float-label="Aliado" maxlength="100"/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="inbox" icon-color="dark">
                  <q-input upper-case v-model="form.fields.rubro" inverted color="dark" float-label="Rubro" maxlength="100"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                  <q-input v-model.lazy="form.fields.montovalidar" v-money="money" inverted color="dark" float-label="Monto de la bolsa" suffix="MXN"></q-input>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                  <q-input v-model.lazy="form.fields.montovalidariva" v-money="money" inverted color="dark" float-label="Monto de la bolsa sin iva" suffix="MXN"></q-input>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                  <q-input readonly v-model.lazy="form.fields.monto_licitado" v-money="money" inverted color="dark" float-label="Monto licitado" suffix="MXN"></q-input>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                  <q-input eadonly v-model.lazy="form.fields.monto_adjudicado" v-money="money" inverted color="dark" float-label="Monto adjudicado" suffix="MXN"></q-input>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                  <q-input readonly v-model="form.fields.suma_contratos" inverted color="dark" float-label="Monto adjudicado(contratos)" suffix="MXN"></q-input>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="inbox" icon-color="dark">
                  <q-select v-model="form.fields.adjudicacion" inverted color="dark" float-label="Tipo de venta" :options="adjudicacionOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="store" icon-color="dark">
                  <q-select v-model="form.fields.sucursal_id" inverted color="dark" float-label="Sucursal" :options="sucursalesOptions" filter/>
                </q-field>
              </div>
            </div>
          </div>
        </div>

          <q-tabs v-model="tabPrincipal" class="shadow-1 bg-white" inverted animated swipeable color="teal" align="justify" style="margin-top: 25px;padding-top: 1%;">
            <q-tab default name="contratos" slot="title" icon="insert_drive_file" label="Contratos"/>
            <q-tab name="projects" slot="title" icon="work" label="Presupuesto"/>
            <q-tab name="organismos" slot="title" icon="folder" label="Organismos de apoyo"/>
            <q-tab name="comisiones" slot="title" icon="fas fa-donate" label="C.C." v-if="role === 'Admin' || role === 'Root' || role === 'Finanzas/Comisiones'"/>

            <q-tab-pane name="contratos" class="bg-white" style="padding: 0;">
              <div v-if="viewsContrato.grid">
                <div class="q-pa-sm panel-header">
                  <div class="row">
                    <div class="col-sm-12 pull-right">
                      <div class="q-pa-sm q-gutter-sm">
                        <q-btn class="btn_nuevo" icon="add" label="Nuevo" @click="newRowContrato()" v-if="role !== 'Lider' && role !== 'Finanzas/Comisiones'"/>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col q-pa-md border-panel" style="border: none;margin-top: -2%;">
                  <div class="col-sm-12" style="padding-bottom: 10px;">
                    <q-search color="primary" v-model="form.fieldsContrato.filter"/>
                  </div>
                  <q-table id="sticky-table-newstyle"
                    :data="form_presupuestos.data_contratos"
                    :columns="form.columnsContrato"
                    :selected.sync="form.selected"
                    :filter="form.fieldsContrato.filter"
                    color="positive"
                    title=""
                    :dense="true"
                    :pagination.sync="form.pagination"
                    :loading="form.loading"
                    :rows-per-page-options="form.rowsOptions">
                    <template slot="body" slot-scope="props">
                      <q-tr :props="props">
                        <q-td key="num_contrato" :props="props" @click.native="clicFilaContrato(props.row)" style="cursor: pointer;">{{ props.row.num_contrato }}</q-td>
                        <q-td key="nombre" style="text-align: left;" :props="props">{{ props.row.nombre }}</q-td>
                        <q-td key="monto_total" style="text-align: right;" :props="props">${{ currencyFormat(props.row.monto_total) }}</q-td>
                        <q-td key="recurso" :props="props" @click.native="clicFilaContrato(props.row)" style="cursor: pointer;">{{ props.row.recurso }}</q-td>
                        <q-td key="licitacion" :props="props">{{ props.row.licitacion }}</q-td>
                        <q-td style="text-align: left;" key="razon_social" :props="props">{{ props.row.razon_social }}</q-td>
                        <q-td key="fecha_inicio" :props="props">{{ props.row.f_inicio }}</q-td>
                        <q-td key="fecha_fin" :props="props">{{ props.row.f_fin }}</q-td>
                        <q-td key="documento_final" :props="props">
                          <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="contratoInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadContratoFile()" />
                          <q-btn small flat @click="selectedRowContrato(props.row)" color="green-9" :loading="loadingButton" v-if="role !== 'Lider'">
                            <q-icon name="cloud_upload" />&nbsp;
                            <q-tooltip>Subir carta de entera satisfacción</q-tooltip>
                          </q-btn>
                          <q-btn small flat :icon="props.row.icon" :color="props.row.color" v-if="props.row.documento_final !== null">
                            <q-tooltip>{{ props.row.documento_final }}</q-tooltip>
                            <q-popover>
                              <q-list link separator class="scroll" style="min-width: 100px">
                                <q-item v-close-overlay @click.native="verDocumentoContrato(props.row.id, props.row.extension)" v-if="props.row.extension !== 'docx' && props.row.extension !== 'xml' && props.row.extension !== 'xlsx'">
                                  <q-item-main label="Ver"/>
                                </q-item>
                                <q-item v-close-overlay @click.native="descargarDocumentoContrato(props.row.id, props.row.extension)">
                                  <q-item-main label="Descargar"/>
                                </q-item>
                                <q-item v-close-overlay @click.native="eliminarDocumentoContrato(props.row.id)">
                                  <q-item-main label="Eliminar"/>
                                </q-item>
                              </q-list>
                            </q-popover>
                          </q-btn>
                        </q-td>
                        <q-td key="acciones" style="text-align: center;" :props="props">
                          <q-btn small flat @click="seeRowContrato(props.row)" color="blue-6" icon="visibility" v-if="role !== 'Lider' && role !== 'Finanzas/Comisiones'">
                            <q-tooltip>Ver detalles</q-tooltip>
                          </q-btn>
                          <q-btn small flat @click="editRowContrato(props.row)" color="blue-6" icon="edit" v-if="role !== 'Lider' && role !== 'Finanzas/Comisiones'">
                            <q-tooltip>Editar</q-tooltip>
                          </q-btn>
                          <q-btn small flat @click="deleteSingleContrato(props.row.id)" color="negative" icon="delete" v-if="role !== 'Lider' && role !== 'Finanzas/Comisiones'">
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

                <div class="col q-pa-md border-panel" v-if="modal_ver_contrato" style="margin-top: 20px;">
                  <div class="row q-col-gutter-xs">
                    <div class="col-sm-12">
                      <q-btn @click="modal_ver_contrato=false" color="negative" icon="visibility_off">
                        <q-tooltip>Ocultar información</q-tooltip>
                      </q-btn>
                    </div>
                  </div>
                      <div class="row q-col-gutter-xs" style="margin-top: 10px;">
                        <div class="col-sm-3">
                          <q-field icon="fas fa-file" icon-color="dark" :error="$v.form.fieldsContrato.num_contrato.$error" error-label="Ingrese el número de contrato">
                            <q-input upper-case v-model="form.fieldsContrato.num_contrato" inverted-color="dark" float-label="Número de contrato"/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fieldsContrato.empresa_id.$error" error-label="Elija una empresa">
                            <q-select v-model="form.fieldsContrato.empresa_id" inverted-color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                          </q-field>
                        </div>
                        <div class="col-sm-6">
                          <q-field icon="fas fa-user" icon-color="dark">
                            <q-input readonly inverted-color="dark" float-label="Cliente" v-model="cliente"></q-input>
                          </q-field>
                        </div>
                      </div>
                      <div class="row q-col-gutter-xs">
                        <div class="col-sm-6">
                          <q-field icon="inbox" icon-color="dark">
                            <q-input upper-case v-model="form.fieldsContrato.nombre" inverted-color="dark" float-label="Nombre" maxlength="100"/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="date_range" icon-color="dark" :error="$v.form.fieldsContrato.fecha_inicio.$error" error-label="Ingrese la fecha inicio">
                            <q-datetime v-model="form.fieldsContrato.fecha_inicio" type="date" inverted-color="dark" float-label="Fecha inicio" align="center"></q-datetime>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="date_range" icon-color="dark" :error="$v.form.fieldsContrato.fecha_fin.$error" error-label="Ingrese la fecha fin">
                            <q-datetime v-model="form.fieldsContrato.fecha_fin" type="date" inverted-color="dark" float-label="Fecha fin" align="center"></q-datetime>
                          </q-field>
                        </div>
                      </div>
                      <div class="row q-col-gutter-xs">
                        <div class="col-sm-6">
                          <q-field icon="fas fa-book" icon-color="dark">
                            <q-select v-model="form.fieldsContrato.organismo_id" inverted-color="dark" float-label="Organismo de apoyo" :options="organismosOptions" filter clearable/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-dollar-sign" icon-color="dark">
                            <q-input :decimals="0" inverted-color="dark" float-label="Porcentaje" numeric-keyboard-toggle v-model="form.fieldsContrato.porcentaje" type="number" suffix="%"  :readonly="parseInt(form.fieldsContrato.organismo_id)===0"/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-book" icon-color="dark">
                            <q-select v-model="form.fieldsContrato.licitacion_id" inverted-color="dark" float-label="Licitación" :options="licitacionesOptions" filter clearable/>
                          </q-field>
                        </div>
                      </div>
                      <div class="row q-col-gutter-xs">
                        <div class="col-sm-6">
                          <q-field icon="fas fa-user" icon-color="dark" :error="$v.form.fieldsContrato.rep_legal_contrato.$error" error-label="Ingrese el nombre del representante">
                            <q-input upper-case inverted-color="dark" float-label="Representante legal del contrato" v-model="form.fieldsContrato.rep_legal_contrato"></q-input>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <div class="icono_field">
                            <q-field  icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form.fieldsContrato.monto_total.$error" error-label="Ingrese el monto ($1.00 - $1,000,000,000.00)">
                              <q-input v-model.lazy="form.fieldsContrato.monto_total_validar" v-money="money" inverted-color="dark" float-label="Monto" suffix="MXN"></q-input>
                            </q-field>
                          </div>
                        </div>
                        <div class="col-sm-12">
                          <q-field icon="label" icon-color="dark">
                            <q-input upper-case inverted-color="dark" float-label="Observaciones" v-model="form.fieldsContrato.observaciones"></q-input>
                          </q-field>
                        </div>
                      </div>
                      <div class="col q-pa-md col-sm-12">
                        Facturas
                      </div>
                      <div v-if="Datofactura" class="col q-pa-md border-panel" style="border: none;">
                        <div class="col-sm-12" style="padding-bottom: 10px;">
                          <q-search color="primary" v-model="form.fieldsContrato.filter"/>
                        </div>
                          <q-table id="sticky-table-newstyle"
                            :data="facturas"
                            :columns="form.fileColumnContrato"
                            :selected.sync="form.selected"
                            :filter="form.fieldsContrato.filter"
                            color="positive"
                            title=""
                            :dense="true"
                            :pagination.sync="form.pagination"
                            :loading="form.loading"
                            :rows-per-page-options="form.rowsOptions">
                            <template slot="body" slot-scope="props">
                              <q-tr :props="props">
                                <q-td key="id" :props="props"  style="cursor: pointer;">{{ props.row.factura_id }}</q-td>
                                <q-td key="nombre" :props="props"  style="cursor: pointer;">{{ props.row.name }}</q-td>
                                <q-td key="extension" :props="props">{{ props.row.doc_type }}</q-td>
                                <q-td key="fecha_factura" :props="props">{{ props.row.fecha_factura }}</q-td>
                                <q-td key="fecha_pago" :props="props">{{ props.row.fecha_pago }}</q-td>
                                <q-td key="fecha_vencimiento" :props="props">{{ props.row.fecha_vencimiento }}</q-td>
                                <q-td key="monto_factura" :props="props">${{ currencyFormat(props.row.monto_factura) }}</q-td>
                                <q-td key="folio_fiscal" :props="props">{{ props.row.folio_fiscal }}</q-td>
                                <q-td key="cancelada" :props="props"><q-checkbox v-model="props.row.cancelada" color="green-10" @click.native="cancelFactura(props.row)"/></q-td>
                                <q-td key="nota_credito" :props="props"><q-checkbox v-model="props.row.nota_credito" color="green-10" @click.native="updateFactura(props.row)"/></q-td>
                                <q-td key="acciones" :props="props">
                                  <q-btn small flat @click="abrirDocumento(props.row.factura_id,props.row.doc_type)" color="blue-6" icon="get_app">
                                    <q-tooltip>Ver</q-tooltip>
                                  </q-btn>
                                  <q-btn small flat @click="deleteDoc(props.row.factura_id)" color="negative" icon="delete">
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
              <!--Crear un contrato-->
              <div v-if="viewsContrato.create">
                <div class="q-pa-sm panel-header">
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="q-pa-sm q-gutter-sm">
                        <q-breadcrumbs align="left">
                          <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                          <q-breadcrumbs-el label="Contratos" to="" @click.native="returnToView('contrato','grid')"/>
                          <q-breadcrumbs-el label="Nuevo contrato"/>
                        </q-breadcrumbs>
                      </div>
                    </div>
                    <div class="col-sm-6 pull-right">
                    <div class="q-pa-sm q-gutter-sm">
                 <q-btn @click="returnToView('contrato','grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
                  <q-btn @click="saveContratos()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
                </div>
              </div>
                  </div>
                </div>
                <div class="q-pa-md">
                  <div class="row bg-white border-panel" style="padding:0;">
                    <div class="col q-pa-md">
                      <div class="row q-col-gutter-xs">
                        <div class="col-sm-3">
                          <q-field icon="fas fa-file" icon-color="dark" :error="$v.form.fieldsContrato.num_contrato.$error" error-label="Ingrese el número de contrato">
                            <q-input upper-case v-model="form.fieldsContrato.num_contrato" inverted color="dark" float-label="Número de contrato"/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fieldsContrato.empresa_id.$error" error-label="Elija una empresa">
                            <q-select v-model="form.fieldsContrato.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                          </q-field>
                        </div>
                        <div class="col-sm-6">
                          <q-field icon="fas fa-user" icon-color="dark">
                            <q-input readonly inverted color="dark" float-label="Cliente" v-model="cliente"></q-input>
                          </q-field>
                        </div>
                      </div>
                      <div class="row q-col-gutter-xs">
                        <div class="col-sm-6">
                          <q-field icon="inbox" icon-color="dark">
                            <q-input upper-case v-model="form.fieldsContrato.nombre" inverted color="dark" float-label="Nombre" maxlength="100"/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="date_range" icon-color="dark" :error="$v.form.fieldsContrato.fecha_inicio.$error" error-label="Ingrese la fecha inicio">
                            <q-datetime v-model="form.fieldsContrato.fecha_inicio" type="date" inverted color="dark" float-label="Fecha inicio" align="center"></q-datetime>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="date_range" icon-color="dark" :error="$v.form.fieldsContrato.fecha_fin.$error" error-label="Ingrese la fecha fin">
                            <q-datetime v-model="form.fieldsContrato.fecha_fin" type="date" inverted color="dark" float-label="Fecha fin" align="center"></q-datetime>
                          </q-field>
                        </div>
                      </div>
                      <div class="row q-col-gutter-xs">
                        <div class="col-sm-6">
                          <q-field icon="fas fa-book" icon-color="dark">
                            <q-select v-model="form.fieldsContrato.organismo_id" inverted color="dark" float-label="Organismo de apoyo" :options="organismosOptions" filter clearable/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-dollar-sign" icon-color="dark">
                            <q-input :decimals="0" inverted color="dark" float-label="Porcentaje" numeric-keyboard-toggle v-model="form.fieldsContrato.porcentaje" type="number" suffix="%" :readonly="parseInt(form.fieldsContrato.organismo_id)===0"/>
                            <!-- </q-input> -->
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-book" icon-color="dark">
                            <q-select v-model="form.fieldsContrato.licitacion_id" inverted color="dark" float-label="Licitación" :options="licitacionesOptions" filter clearable/>
                          </q-field>
                        </div>
                      </div>
                      <div class="row q-col-gutter-xs">
                        <div class="col-sm-6" style="margin-top:2px;">
                          <q-field icon="fas fa-user" icon-color="dark" :error="$v.form.fieldsContrato.rep_legal_contrato.$error" error-label="Ingrese el nombre del representante">
                            <q-input upper-case inverted color="dark" float-label="Representante legal del contrato" v-model="form.fieldsContrato.rep_legal_contrato"></q-input>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <div class="icono_field">
                            <q-field  icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form.fieldsContrato.monto_total.$error" error-label="Ingrese el monto ($1.00 - $1,000,000,000.00)">
                              <q-input v-model.lazy="form.fieldsContrato.monto_total_validar" v-money="money" inverted color="dark" float-label="Monto" suffix="MXN"></q-input>
                            </q-field>
                          </div>
                        </div>
                        <div class="col-sm-12">
                          <q-field icon="label" icon-color="dark">
                            <q-input upper-case inverted color="dark" float-label="Observaciones" v-model="form.fieldsContrato.observaciones"></q-input>
                          </q-field>
                        </div>
                      </div>
                      <div v-if="Datofactura" class="col q-pa-md col-sm-12">
                        Facturas
                      </div>
                      <div v-if="Datofactura" class="row q-col-gutter-xs">
                        <!-- <div class="col-sm-3">
                          <q-field icon="description" icon-color="dark" :error="$v.form.file.nombre.$error" error-label="Ingrese el condepto de la factura">
                            <q-input upper-case inverted color="dark" float-label="Concepto factura" v-model="form.file.nombre"></q-input>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="date_range" icon-color="dark" :error="$v.form.file.fecha_factura.$error" error-label="Ingrese la fecha de emisión">
                            <q-datetime v-model="form.file.fecha_factura" type="date" inverted color="dark" float-label="Fecha de emisión" align="center"></q-datetime>
                          </q-field>
                        </div> -->
                        <div class="col-sm-2">
                          <q-field icon="date_range" icon-color="dark" :error="$v.form.file.fecha_pago.$error" error-label="Ingrese la fecha de pago">
                            <q-datetime  v-model="form.file.fecha_pago" type="date" inverted color="dark" float-label="Fecha de pago" align="center"></q-datetime>
                          </q-field>
                        </div>
                        <div class="col-sm-2">
                          <q-field icon="date_range" icon-color="dark" :error="$v.form.file.fecha_vencimiento.$error" error-label="Ingrese la fecha de vencimiento">
                            <q-datetime  v-model="form.file.fecha_vencimiento" type="date" inverted color="dark" float-label="Fecha de vencimiento" align="center"></q-datetime>
                          </q-field>
                        </div>
                        <!-- <div class="col-sm-2">
                          <q-field icon="description" icon-color="dark">
                            <q-input upper-case inverted color="dark" float-label="Folio fiscal" v-model="form.file.folio_fiscal"></q-input>
                          </q-field>
                        </div> -->
                        <div class="col-sm-3">
                          <q-field  icon="attach_file" icon-color="dark" >
                            <input   style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="fileInputFormato" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf, .PNG, .JPG, .JPEG, .XML, .DOCX, .00, .PDF" @input="checkContrato()"/>
                            <q-btn @click="$refs.fileInputFormato.click()" class="btn_atach" v-if="show_file_contrato !== true" :loading="loadingButton">Adjuntar Factura</q-btn>
                            <q-btn @click="$refs.fileInputFormato.click()" class="btn_guardar" v-else :loading="loadingButton">Adjuntar Factura</q-btn>
                          </q-field>
                        </div>
                        <div class="col-sm-6 pull-right">
                          <q-btn @click="uploadFormato()" class="btn_guardar" :loading="loadingButton">Guardar Factura</q-btn>
                        </div>
                      </div>
                      <div v-if="Datofactura" class="col q-pa-md border-panel" style="border: none;">
                        <div class="col-sm-12" style="padding-bottom: 10px;">
                          <q-search color="primary" v-model="form.fieldsContrato.filter"/>
                        </div>
                        <q-table id="sticky-table-newstyle"
                          :data="facturas"
                          :columns="form.fileColumnContrato"
                          :selected.sync="form.selected"
                          :filter="form.fieldsContrato.filter"
                          color="positive"
                          title=""
                          :dense="true"
                          :pagination.sync="form.pagination"
                          :loading="form.loading"
                          :rows-per-page-options="form.rowsOptions">
                          <template slot="body" slot-scope="props">
                            <q-tr :props="props">
                              <q-td key="id" :props="props" style="cursor: pointer;">{{ props.row.folio_xml }}</q-td>
                              <q-td key="nombre" :props="props"  style="cursor: pointer;">{{ props.row.name }}</q-td>
                              <q-td key="extension" :props="props">{{ props.row.doc_type }}</q-td>
                              <q-td key="fecha_factura" :props="props">{{ props.row.fecha_factura }}</q-td>
                              <q-td key="fecha_pago" :props="props">{{ props.row.fecha_pago }}</q-td>
                              <q-td key="fecha_vencimiento" :props="props">{{ props.row.fecha_vencimiento }}</q-td>
                              <q-td key="monto_factura" :props="props">${{ currencyFormat(props.row.monto_factura) }}</q-td>
                              <q-td key="folio_fiscal" :props="props">{{ props.row.folio_fiscal }}</q-td>
                              <q-td key="cancelada" :props="props"><q-checkbox v-model="props.row.cancelada" color="green-10" @click.native="cancelFactura(props.row)"/></q-td>
                              <q-td key="nota_credito" :props="props"><q-checkbox v-model="props.row.nota_credito" color="green-10" @click.native="updateFactura(props.row)"/></q-td>
                              <q-td key="acciones" :props="props">
                                <q-btn small flat @click="abrirDocumento(props.row.factura_id+'.'+props.row.doc_type)" color="blue-6" icon="get_app">
                                  <q-tooltip>Ver</q-tooltip>
                                </q-btn>
                                <q-btn small flat @click="deleteDoc(props.row.factura_id)" color="negative" icon="delete">
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
              <!-- Modal editar CONTRATO -->
              <div v-if="viewsContrato.update">
                <div class="q-pa-sm panel-header">
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="q-pa-sm q-gutter-sm">
                       <!--  <q-breadcrumbs align="left">
                          <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                          <q-breadcrumbs-el label="Contratos" to="" @click.native="setViewContrato('grid')"/>
                          <q-breadcrumbs-el label="Editar contrato"/>
                        </q-breadcrumbs> -->
                      </div>
                    </div>
                    <div class="col-sm-6 pull-right">
                    <div class="q-pa-sm q-gutter-sm">
                  <q-btn @click="setViewContrato('grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
                  <q-btn @click="updateContratos()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
                </div>
              </div>
                  </div>
                </div>

                <div class="q-pa-md">
                  <div class="row bg-white border-panel" style="padding:0;">
                    <!-- <div class="col q-pa-md col-sm-12">
                      <div class="row q-col-gutter-xs">
                        <div class="col-sm-6 pull-left">
                          <div class="col-xs-12 col-sm-12 col-md-12 pull-left" >
                            <q-btn @click="setViewContrato('grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
                          </div>
                        </div>
                        <div class="col-sm-6 pull-right">
                          <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
                            <q-btn @click="updateContratos()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
                          </div>
                        </div>
                      </div>
                    </div> -->
                    <div class="col q-pa-md">
                      <div class="row q-col-gutter-xs">
                        <div class="col-sm-3">
                          <q-field icon="work" icon-color="dark">
                            <q-select v-model="form.fieldsContrato.recurso_id" inverted color="dark" float-label="Proyecto" :options="recursosOptions" filter @input="updateContratosGrid()" :readonly="this.role.toUpperCase() !== 'admin'.toUpperCase() && this.role.toUpperCase() !== 'root'.toUpperCase()"/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-file" icon-color="dark" :error="$v.form.fieldsContrato.num_contrato.$error" error-label="Ingrese el número de contrato">
                            <q-input upper-case v-model="form.fieldsContrato.num_contrato" inverted color="dark" float-label="Número de contrato"/>
                          </q-field>
                        </div>
                        <div class="col-sm-6">
                          <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fieldsContrato.empresa_id.$error" error-label="Elija una empresa">
                            <q-select v-model="form.fieldsContrato.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                          </q-field>
                        </div>
                        <div class="col-sm-6">
                          <q-field icon="fas fa-user" icon-color="dark">
                            <q-input readonly inverted color="dark" float-label="Cliente" v-model="cliente"></q-input>
                          </q-field>
                        </div>
                        <div class="col-sm-6">
                          <q-field icon="inbox" icon-color="dark">
                            <q-input upper-case v-model="form.fieldsContrato.nombre" inverted color="dark" float-label="Nombre" maxlength="100"/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="date_range" icon-color="dark" :error="$v.form.fieldsContrato.fecha_inicio.$error" error-label="Ingrese la fecha inicio">
                            <q-datetime v-model="form.fieldsContrato.fecha_inicio" type="date" inverted color="dark" float-label="Fecha inicio" align="center"></q-datetime>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="date_range" icon-color="dark" :error="$v.form.fieldsContrato.fecha_fin.$error" error-label="Ingrese la fecha fin">
                            <q-datetime v-model="form.fieldsContrato.fecha_fin" type="date" inverted color="dark" float-label="Fecha fin" align="center"></q-datetime>
                          </q-field>
                        </div>
                        <div class="col-sm-6">
                          <q-field icon="fas fa-book" icon-color="dark">
                            <q-select v-model="form.fieldsContrato.organismo_id" inverted color="dark" float-label="Organismo de apoyo" :options="organismosOptions" filter clearable/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-dollar-sign" icon-color="dark">
                            <q-input :decimals="0" inverted color="dark" float-label="Porcentaje" numeric-keyboard-toggle v-model="form.fieldsContrato.porcentaje" type="number" suffix="%"  :readonly="parseInt(form.fieldsContrato.organismo_id)===0"/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-book" icon-color="dark">
                            <q-select v-model="form.fieldsContrato.licitacion_id" inverted color="dark" float-label="Licitación" :options="licitacionesOptions" filter clearable/>
                          </q-field>
                        </div>
                        <div class="col-sm-6">
                          <q-field icon="fas fa-user" icon-color="dark" :error="$v.form.fieldsContrato.rep_legal_contrato.$error" error-label="Ingrese el nombre del representante">
                            <q-input upper-case inverted color="dark" float-label="Representante legal del contrato" v-model="form.fieldsContrato.rep_legal_contrato"></q-input>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <div class="icono_field">
                            <q-field  icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form.fieldsContrato.monto_total.$error" error-label="Ingrese el monto ($1.00 - $1,000,000,000.00)">
                              <q-input v-model.lazy="form.fieldsContrato.monto_total_validar" v-money="money" inverted color="dark" float-label="Monto" suffix="MXN"></q-input>
                            </q-field>
                          </div>
                        </div>
                        <div class="col-sm-3" v-if="this.form.fieldsContrato.documento_pdf !== 0">
                          <q-field icon="fas fa-file-pdf" icon-color="red">
                            <q-btn color="red" :label="form.fieldsContrato.nombre_pdf" @click="downloadContrato()"/>
                          </q-field>
                        </div>
                        <div class="col-sm-12">
                          <q-field icon="label" icon-color="dark">
                            <q-input upper-case inverted color="dark" float-label="Observaciones" v-model="form.fieldsContrato.observaciones"></q-input>
                          </q-field>
                        </div>
                      </div>
                      <div class="col q-pa-md col-sm-12">
                        Facturas
                      </div>
                      <div v-if="Datofactura" class="row q-col-gutter-xs">
                        <div class="col-sm-3">
                          <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fieldsContrato.empresa_id.$error" error-label="Elija un tipo de factura">
                            <q-select v-model="form.tipo_factura" inverted color="dark" float-label="Tipo de factura" :options="tiposFacturaOptions" filter/>
                          </q-field>
                        </div>
                        <div class="col-sm-9">
                        </div>
                      </div>
                      <div v-if="Datofactura && form.tipo_factura === 'GDP'"  class="row q-col-gutter-xs">
                        <!-- <div class="col-sm-3">
                          <q-field icon="description" icon-color="dark" :error="$v.form.file.nombre.$error" error-label="Ingrese el concepto de la factura">
                            <q-input upper-case inverted color="dark" float-label="Concepto factura" v-model="form.file.nombre"></q-input>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="date_range" icon-color="dark" :error="$v.form.file.fecha_factura.$error" error-label="Ingrese la fecha de factura">
                            <q-datetime v-model="form.file.fecha_factura" type="date" inverted color="dark" float-label="Fecha de emisión" align="center"></q-datetime>
                          </q-field>
                        </div> -->
                        <div class="col-sm-3">
                          <q-field icon="date_range" icon-color="dark" :error="$v.form.file.fecha_pago.$error" error-label="Ingrese la fecha de pago">
                            <q-datetime  v-model="form.file.fecha_pago" type="date" inverted color="dark" float-label="Fecha de pago" align="center"></q-datetime>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="date_range" icon-color="dark" :error="$v.form.file.fecha_vencimiento.$error" error-label="Ingrese la fecha de vencimiento">
                            <q-datetime  v-model="form.file.fecha_vencimiento" type="date" inverted color="dark" float-label="Fecha de vencimiento" align="center"></q-datetime>
                          </q-field>
                        </div>
                        <!-- <div class="col-sm-3">
                          <q-field icon="description" icon-color="dark">
                            <q-input upper-case inverted color="dark" float-label="Folio fiscal" v-model="form.file.folio_fiscal"></q-input>
                          </q-field>
                        </div> -->
                        <div class="col-sm-3" style="padding-left: 10px;">
                            <input   style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="fileInputFormato" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf, .PNG, .JPG, .JPEG, .XML, .DOCX, .00, .PDF" @input="checkContrato()"/>
                            <q-btn @click="$refs.fileInputFormato.click()" class="btn_atach" v-if="show_file_contrato !== true" :loading="loadingButton">Adjuntar Factura</q-btn>
                            <q-btn @click="$refs.fileInputFormato.click()" class="btn_guardar" v-else :loading="loadingButton">Adjuntar Factura</q-btn>
                            <q-btn  @click="uploadFormato()" class="btn_guardar" :loading="loadingButton">Guardar Factura</q-btn>
                        </div>
                        <!-- <div class="col-sm-3 pull-right">
                          <q-btn  @click="uploadFormato()" class="btn_guardar" :loading="loadingButton">Guardar Factura</q-btn>
                        </div> -->
                      </div>
                      <!-- AQUI EMPIEZA LO MIO -->
                      <div v-if="Datofactura && form.tipo_factura === 'retail'"  class="row q-col-gutter-xs">
                        <div class="col-sm-3">
                          <q-field icon="description" icon-color="dark">
                            <q-input upper-case inverted color="dark" float-label="RFC" v-model="form.retail.rfc"></q-input>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="description" icon-color="dark">
                            <q-input upper-case inverted color="dark" float-label="Razon social" v-model="form.retail.razon_social"></q-input>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="description" icon-color="dark">
                            <q-input upper-case inverted color="dark" float-label="Folio fiscal" v-model="form.retail.folio_fiscal"></q-input>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="description" icon-color="dark">
                            <q-input upper-case inverted color="dark" float-label="Serie" v-model="form.retail.serie"></q-input>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="date_range" icon-color="dark" :error="$v.form.retail.fecha_factura.$error" error-label="Ingrese la fecha de factura">
                            <q-datetime v-model="form.retail.fecha_factura" type="date" inverted color="dark" float-label="Fecha de emisión" align="center"></q-datetime>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="date_range" icon-color="dark" :error="$v.form.retail.fecha_pago.$error" error-label="Ingrese la fecha de pago">
                            <q-datetime  v-model="form.retail.fecha_pago" type="date" inverted color="dark" float-label="Fecha de pago" align="center"></q-datetime>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fieldsContrato.empresa_id.$error" error-label="Elija un método de pago">
                            <q-select v-model="form.retail.metodo_pago" inverted color="dark" float-label="Método de pago" :options="metodoPagoOptions" filter/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fieldsContrato.empresa_id.$error" error-label="Elija un tipo de comprobante">
                            <q-select v-model="form.retail.tipo_comprobante" inverted color="dark" float-label="Tipo de comprobante" :options="tiposComprobanteOptions" filter/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fieldsContrato.empresa_id.$error" error-label="Elija una forma de pago">
                            <q-select v-model="form.retail.forma_pago" inverted color="dark" float-label="Forma de pago" :options="formaPagoOptions" filter/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fieldsContrato.empresa_id.$error" error-label="Elija un uso CFDI">
                            <q-select v-model="form.retail.uso_cfdi" inverted color="dark" float-label="Uso CFDI" :options="usoCFDIOptions" filter/>
                          </q-field>
                        </div>
                        <div class="col-sm-6 pull-right">
                          <q-btn  @click="uploadFormato()" class="btn_guardar" :loading="loadingButton">Guardar Factura</q-btn>
                        </div>
                      </div>

                      <div v-if="Datofactura" class="col q-pa-md border-panel" style="border: none;">
                        <div class="col-sm-12" style="padding-bottom: 10px;">
                          <q-search color="primary" v-model="form.fieldsContrato.filter"/>
                        </div>
                          <q-table id="sticky-table-newstyle"
                            :data="facturas"
                            :columns="form.fileColumnContrato"
                            :selected.sync="form.selected"
                            :filter="form.fieldsContrato.filter"
                            color="positive"
                            title=""
                            :dense="true"
                            :pagination.sync="form.pagination"
                            :loading="form.loading"
                            :rows-per-page-options="form.rowsOptions">
                            <q-tr slot="bottom-row" slot-scope="props">
                              <q-td colspan="100%" style="text-align: right;">
                                <strong>Total: ${{ totalContratos }} MXN</strong>
                              </q-td>
                            </q-tr>
                            <template slot="body" slot-scope="props">
                              <q-tr :props="props">
                                <q-td key="id" :props="props"  style="cursor: pointer;">{{ props.row.folio_xml }}</q-td>
                                <q-td key="nombre" :props="props"  style="cursor: pointer;">{{ props.row.name }}</q-td>
                                <q-td key="extension" :props="props">{{ props.row.doc_type }}</q-td>
                                <q-td key="fecha_factura" :props="props">{{ props.row.fecha_factura }}</q-td>
                                <q-td key="fecha_pago" :props="props">{{ props.row.fecha_pago }}</q-td>
                                <q-td key="fecha_vencimiento" :props="props">{{ props.row.fecha_vencimiento }}</q-td>
                                <q-td key="monto_factura" :props="props">${{ currencyFormat(props.row.monto_factura) }}</q-td>
                                <q-td key="folio_fiscal" :props="props">{{ props.row.folio_fiscal }}</q-td>
                                <q-td key="cancelada" :props="props"><q-checkbox v-model="props.row.cancelada" color="green-10" @click.native="cancelFactura(props.row)"/></q-td>
                                <q-td key="nota_credito" :props="props"><q-checkbox v-model="props.row.nota_credito" color="green-10" @click.native="updateFactura(props.row)"/></q-td>
                                <q-td key="acciones" :props="props">
                                  <input id="archivopdf" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="pdfInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadPDFFile()" />
                                  <q-btn small flat @click="uploadArchivoPDF(props.row)" color="green-9" :loading="loadingButton">
                                    <q-icon name="cloud_upload" />&nbsp;
                                    <q-tooltip>Subir pdf</q-tooltip>
                                  </q-btn>
                                  <q-btn small flat @click="abrirDocumento(props.row.document_id,props.row.doc_type)" color="green" icon="fas fa-file-excel">
                                    <q-tooltip>Ver xml</q-tooltip>
                                  </q-btn>
                                  <q-btn small flat @click="abrirDocumento(props.row.pdf_id,props.row.doc_type_pdf)" color="red" icon="fas fa-file-pdf" v-if="props.row.pdf_id !== null">
                                    <q-tooltip>Ver pdf</q-tooltip>
                                  </q-btn>
                                  <q-btn small flat @click="deleteDoc(props.row.factura_id)" color="negative" icon="delete">
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
            </q-tab-pane>

            <q-tab-pane name="projects" class="bg-white" style="padding: 0;">
              <div v-if="viewsPresupuestos.grid">
                <div class="q-pa-sm panel-header">
                  <div class="row">
                    <!-- <div class="col-sm-6">
                      <div class="q-pa-sm q-gutter-sm">
                        <q-breadcrumbs align="left">
                          <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                          <q-breadcrumbs-el label="Projects"/>
                        </q-breadcrumbs>
                      </div>
                    </div> -->
                    <div class="col-sm-12 pull-right">
                      <div class="q-pa-sm q-gutter-sm">
                        <q-btn class="btn_nuevo" icon="add" label="Nuevo" @click="newRowPresupuestos()" v-if="role !== 'Lider' && role !== 'Finanzas/Comisiones'"/>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col q-pa-md border-panel" style="border: none;margin-top: -2%;">
                  <div class="col-sm-12" style="padding-bottom: 10px;">
                    <q-search color="primary" v-model="form.filter"/>
                  </div>
                  <q-table id="sticky-table-newstyle"
                    :data="form_presupuestos.data_projects"
                    :columns="form.columnsPresupuesto"
                    :selected.sync="form_presupuestos.selected"
                    :filter="form_presupuestos.filter"
                    color="positive"
                    title=""
                    :pagination.sync="form_presupuestos.pagination"
                    :loading="form_presupuestos.loading"
                    :rows-per-page-options="form_presupuestos.rowsOptions"
                    >
                    <template slot="body" slot-scope="props">
                      <q-tr :props="props">
                        <q-td key="nombre_proyecto"  :props="props" @click.native="clicFilaPresupuesto(props.row)" style="cursor: pointer;text-align: left;">{{ props.row.nombre_proyecto }}</q-td>
                        <q-td key="nombre_corto"  :props="props" @click.native="clicFilaPresupuesto(props.row)" style="cursor: pointer;text-align: left;">{{ props.row.nombre_corto }}</q-td>
                        <q-td key="recurso" :props="props" @click.native="clicFilaPresupuesto(props.row)" style="cursor: pointer;">{{ props.row.recurso }}</q-td>
                        <q-td key="inicio" :props="props">{{ props.row.f_inicio }}</q-td>
                        <q-td key="fin" :props="props">{{ props.row.f_fin }}</q-td>
                        <q-td key="lider" style="text-align: left;" :props="props">{{ props.row.nickname }}</q-td>
                        <q-td key="status" :props="props">{{ props.row.status }}</q-td>
                        <q-td key="finalizado" :props="props">
                          <q-checkbox v-model="props.row.finalizado" @click.native="finalizarProyecto(props.row)" color="teal"/>
                        </q-td>
                        <q-td key="acciones" style="text-align: left;" :props="props">
                          <q-btn small flat @click="editRowPresupuestos(props.row)" color="blue-6" icon="edit" v-if="role !== 'Finanzas/Comisiones'">
                            <q-tooltip>Editar</q-tooltip>
                          </q-btn>
                          <!-- <q-btn small flat @click="editRowPresupuestos(props.row)" color="blue-6" icon="fas fa-eye" v-if="user_id === props.row.lider_proyecto">
                            <q-tooltip>Ver</q-tooltip>
                          </q-btn> -->
                          <q-btn small flat @click="deleteSingleRowPresupuestos(props.row.id)" color="negative" icon="delete" v-if="role !== 'Finanzas/Comisiones'">
                            <q-tooltip>Eliminar</q-tooltip>
                          </q-btn>
                        </q-td>
                      </q-tr>
                    </template>
                  </q-table>
                  <q-inner-loading :visible="form_presupuestos.loading">
                    <q-spinner-dots size="64px" color="primary" />
                  </q-inner-loading>
                </div>
              </div>
              <!--Crear un proyecto-->
              <div v-if="viewsPresupuestos.create">
                <div class="q-pa-sm panel-header">
                  <div class="row">
                    <!-- <div class="col-sm-6">
                      <div class="q-pa-sm q-gutter-sm">
                        <q-breadcrumbs align="left">
                          <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                          <q-breadcrumbs-el label="Projects" to="" @click.native="returnToView('presupuesto','grid')"/>
                          <q-breadcrumbs-el label="Nuevo project"/>
                        </q-breadcrumbs>
                      </div>
                    </div> -->
                    <div class="col-sm-12 pull-right">
                      <q-btn  @click.native="returnToView('presupuesto','grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
                      <q-btn @click="savePresupuestos()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
                    </div>
                  </div>
                </div>
                <div class="q-pa-md">
                  <div class="row bg-white border-panel" style="padding:0;">
                    <div class="col q-pa-md">
                      <div class="row q-col-gutter-xs">
                        <div class="col-sm-9">
                          <q-field icon="fas fa-pen-square" icon-color="dark" :error="$v.form.fieldsPresupuesto.nombre_proyecto.$error" error-label="Ingrese el nombre del presupuesto">
                            <q-input upper-case v-model="form.fieldsPresupuesto.nombre_proyecto" inverted color="dark" float-label="Nombre del presupuesto"/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="person" icon-color="dark" :error="$v.form.fieldsPresupuesto.lider_proyecto.$error" error-label="Por favor elija un usuario">
                          <q-select v-model="form.fieldsPresupuesto.lider_proyecto" inverted color="dark" float-label="Líder" :options="usuariosOptions" filter/>
                          </q-field>
                        </div>
                      </div>
                      <div class="row q-col-gutter-xs">
                        <div class="col-sm-4">
                          <q-field icon="fas fa-pen-square" icon-color="dark" :error="$v.form.fieldsPresupuesto.nombre_corto.$error" error-label="Ingrese el nombre">
                            <q-input upper-case v-model="form.fieldsPresupuesto.nombre_corto" inverted color="dark" float-label="Nombre corto"/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="date_range" icon-color="dark" :error="$v.form.fieldsPresupuesto.inicio.$error" error-label="Ingrese la fecha de inicio">
                            <q-datetime v-model="form.fieldsPresupuesto.inicio" type="date" inverted color="dark" float-label="Fecha inicio" align="center"></q-datetime>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="date_range" icon-color="dark" :error="$v.form.fieldsPresupuesto.fin.$error" error-label="Ingrese la fecha de término">
                            <q-datetime v-model="form.fieldsPresupuesto.fin" type="date" inverted color="dark" float-label="Fecha término" align="center"></q-datetime>
                          </q-field>
                        </div>
                        <div class="col-sm-2">
                          <q-field icon="fas fa-sun" icon-color="dark">
                            <q-input v-model="form.fieldsPresupuesto.dias" type="number" inverted color="dark" float-label="Días" readonly ></q-input>
                          </q-field>
                        </div>
                      </div>
                      <div class="row q-col-gutter-xs">
                        <div class="col-sm-2">
                          <div class="icono_field">
                            <!-- <q-field icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form.fieldsPresupuesto.presupuesto.$error" error-label="Rango del presupuesto ($0.00 - $1,000,000,000.00)" helper="Presupuesto">
                              <money v-model="form.fieldsPresupuesto.presupuesto" v-bind="money" style="height:53px;width:100%;" v-bind:class="{ moneyBien: !($v.form.fieldsPresupuesto.presupuesto.$error), moneyError: $v.form.fieldsPresupuesto.presupuesto.$error }" @input="$v.form.fieldsPresupuesto.presupuesto.$touch"></money>
                            </q-field> -->
                            <q-field  icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form.fieldsPresupuesto.presupuesto.$error" error-label="Ingrese el monto ($1.00 - $1,000,000,000.00)">
                              <q-input v-model.lazy="form.fieldsPresupuesto.presupuestovalidar" v-money="money" inverted color="dark" float-label="Presupuesto" suffix="MXN"></q-input>
                            </q-field>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="icono_field">
                            <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                              <q-input v-model.lazy="form.fieldsPresupuesto.monto_bolsa" v-money="money" inverted color="dark" float-label="Monto de la bolsa" suffix="MXN"></q-input>
                            </q-field>
                          </div>
                        </div>
                        <div class="col-sm-4">
                          <q-field icon="fas fa-book" icon-color="dark">
                            <q-select v-model="form.fieldsPresupuesto.licitacion_id" inverted color="dark" float-label="Licitación" :options="licitacionesOptions" filter @input="obtenerEmpresa()"/>
                          </q-field>
                        </div>
                        <div class="col-sm-4" v-if="form.fieldsPresupuesto.licitacion_id > 0">
                          <q-field icon="business" icon-color="dark">
                            <q-select readonly v-model="form.fieldsPresupuesto.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                          </q-field>
                        </div>
                        <div class="col-sm-4" v-else>
                          <q-field icon="business" icon-color="dark">
                            <q-select v-model="form.fieldsPresupuesto.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                          </q-field>
                        </div>
                        <!-- <div class="col-sm-4">
                          <q-field icon="store" icon-color="dark">
                            <q-select readonly v-model="form.fieldsPresupuesto.sucursal_id" inverted color="dark" float-label="Sucursal" :options="sucursalesOptions" filter/>
                          </q-field>
                        </div> -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Modal editar PROYECTO -->
              <div v-if="viewsPresupuestos.update">
                <div class="q-pa-sm panel-header">
                  <div class="row">
                    <!-- <div class="col-sm-6">
                      <div class="q-pa-sm q-gutter-sm">
                        <q-breadcrumbs align="left">
                          <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                          <q-breadcrumbs-el label="Projects" to="" @click.native="backProjects()"/>
                          <q-breadcrumbs-el label="Editar project"/>
                        </q-breadcrumbs>
                      </div>
                    </div> -->
                    <div class="col-sm-12 pull-right">
                      <q-btn  @click.native="returnToView('presupuesto','grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
                      <q-btn @click="updateStatusBloqueado()" class="btn_rechazar" v-if="form.fieldsPresupuesto.status === 'ACTIVO' && (role.toUpperCase() === 'admin'.toUpperCase() || role.toUpperCase() === 'root'.toUpperCase())">Bloquear</q-btn>
                      <q-btn @click="updateStatusActivo()" class="btn_guardar" v-if="form.fieldsPresupuesto.status === 'BLOQUEADO' && (role.toUpperCase() === 'admin'.toUpperCase() || role.toUpperCase() === 'root'.toUpperCase())">Activar</q-btn>
                      <q-btn @click="updatePresupuestos()" class="btn_guardar" icon-right="save" :loading="loadingButton" v-if="role !== 'Lider' && role !== 'Cobranza'">Guardar</q-btn>
                    </div>
                  </div>
                </div>
                <div class="q-px-md">
                  <div class="row bg-white border-panel" style="padding:0;">
                    <div class="col q-pa-md">
                      <div class="row q-col-gutter-xs">
                        <div class="col-sm-6" v-if="this.role.toUpperCase() === 'admin'.toUpperCase() || this.role.toUpperCase() === 'root'.toUpperCase()">
                          <q-field icon="fas fa-pen-square" icon-color="dark" :error="$v.form.fieldsPresupuesto.nombre_proyecto.$error" error-label="Ingrese el nombre del presupuesto">
                            <q-input upper-case v-model="form.fieldsPresupuesto.nombre_proyecto" inverted color="dark" float-label="Nombre del presupuesto"/>
                          </q-field>
                        </div>
                        <div class="col-sm-9" v-else>
                          <q-field icon="fas fa-pen-square" icon-color="dark" :error="$v.form.fieldsPresupuesto.nombre_proyecto.$error" error-label="Ingrese el nombre del presupuesto">
                            <q-input upper-case v-model="form.fieldsPresupuesto.nombre_proyecto" inverted color="dark" float-label="Nombre del presupuesto"/>
                          </q-field>
                        </div>
                        <div class="col-sm-3" v-if="this.role.toUpperCase() === 'admin'.toUpperCase() || this.role.toUpperCase() === 'gerente'.toUpperCase() || this.role.toUpperCase() === 'root'.toUpperCase() || this.role.toUpperCase() === 'gerente de operaciones'.toUpperCase() || this.role.toUpperCase() === 'PLANEACIÓN'.toUpperCase()">
                          <q-field icon="person" icon-color="dark" :error="$v.form.fieldsPresupuesto.lider_proyecto.$error" error-label="Por favor elija un usuario">
                          <q-select v-model="form.fieldsPresupuesto.lider_proyecto" inverted color="dark" float-label="Líder" :options="usuariosOptions" filter/>
                          </q-field>
                        </div>
                        <div class="col-sm-3" v-else>
                          <q-field icon="person" icon-color="dark">
                            <q-input v-model="form.fieldsPresupuesto.lider_usuario" inverted color="dark" float-label="Lider de presupuesto" readonly></q-input>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-pen-square" icon-color="dark" :error="$v.form.fieldsPresupuesto.nombre_corto.$error" error-label="Ingrese el nombre">
                            <q-input upper-case v-model="form.fieldsPresupuesto.nombre_corto" inverted color="dark" float-label="Nombre corto"/>
                          </q-field>
                        </div>
                      </div>
                      <div class="row q-col-gutter-xs">
                        <div class="col-sm-3" v-if="this.role.toUpperCase() === 'admin'.toUpperCase() || this.role.toUpperCase() === 'root'.toUpperCase()">
                          <q-field icon="work" icon-color="dark">
                            <q-select v-model="form.fieldsPresupuesto.recurso_id" inverted color="dark" float-label="Proyecto" :options="recursosOptions" filter @input="updatePresupuestos()"/>
                          </q-field>
                        </div>
                        <div class="col-sm-2">
                          <q-field icon="date_range" icon-color="dark" :error="$v.form.fieldsPresupuesto.inicio.$error" error-label="Ingrese la fecha de inicio">
                            <q-datetime v-model="form.fieldsPresupuesto.inicio" type="date" inverted color="dark" float-label="Fecha inicio" align="center"></q-datetime>
                          </q-field>
                        </div>
                        <div class="col-sm-2">
                          <q-field icon="date_range" icon-color="dark" :error="$v.form.fieldsPresupuesto.fin.$error" error-label="Ingrese la fecha de término">
                            <q-datetime v-model="form.fieldsPresupuesto.fin" type="date" inverted color="dark" float-label="Fecha término" align="center"></q-datetime>
                          </q-field>
                        </div>
                        <div class="col-sm-2">
                          <q-field icon="fas fa-sun" icon-color="dark">
                            <q-input v-model="form.fieldsPresupuesto.dias" type="number" inverted color="dark" float-label="Días" readonly ></q-input>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <div class="icono_field">
                            <q-field  icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form.fieldsPresupuesto.presupuesto.$error" error-label="Ingrese el monto ($0.00 - $1,000,000,000.00)">
                              <q-input v-model.lazy="form.fieldsPresupuesto.presupuestovalidar" v-money="money" inverted color="dark" float-label="Presupuesto inicial" suffix="MXN"></q-input>
                            </q-field>
                          </div>
                        </div>
                      </div>
                      <div class="row q-col-gutter-xs">
                        <div class="col-sm-3">
                          <div class="icono_field">
                            <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                              <q-input v-model.lazy="form.fieldsPresupuesto.monto_bolsa" v-money="money" inverted color="dark" float-label="Monto de la bolsa" suffix="MXN"></q-input>
                            </q-field>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="icono_field">
                            <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                              <q-input v-model.lazy="form.fieldsPresupuesto.monto_bolsa_iva" v-money="money" inverted color="dark" float-label="Monto de la bolsa sin iva" suffix="MXN"></q-input>
                            </q-field>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="icono_field">
                            <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                              <!-- <q-input v-model.lazy="form.fieldsPresupuesto.presupuesto_actual" v-money="money" inverted color="dark" float-label="Presupuesto Actual" suffix="MXN"></q-input> -->
                              <q-input readonly v-model="form.fieldsPresupuesto.presupuesto_actual" inverted color="dark" float-label="Presupuesto actual" suffix="MXN"></q-input>
                            </q-field>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-book" icon-color="dark">
                            <q-select v-model="form.fieldsPresupuesto.licitacion_id" inverted color="dark" float-label="Licitación" :options="licitacionesOptions" filter @input="obtenerEmpresa()" @click.native="cargarLicitaciones()"/>
                          </q-field>
                        </div>
                        <div class="col-sm-3" v-if="form.fieldsPresupuesto.licitacion_id > 0">
                          <q-field icon="business" icon-color="dark">
                            <q-select readonly v-model="form.fieldsPresupuesto.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter @click.native="getEmpresas()"/>
                          </q-field>
                        </div>
                        <div class="col-sm-3" v-else>
                          <q-field icon="business" icon-color="dark">
                            <q-select v-model="form.fieldsPresupuesto.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter @click.native="getEmpresas()"/>
                          </q-field>
                        </div>
                        <!-- <div class="col-sm-6">
                          <q-field icon="store" icon-color="dark">
                            <q-select readonly v-model="form.fieldsPresupuesto.sucursal_id" inverted color="dark" float-label="Sucursal" :options="sucursalesOptions" filter/>
                          </q-field>
                        </div> -->
                      </div>
                    </div>
                  </div>
                </div>

                <div class="q-px-md">
                  <div class="row bg-white border-panel" style="padding:0;">
                    <div class="row q-pa-md col-sm-12">
                      <div class="col-sm-6">
                        Detalle de Actividades
                      </div>
                    <!-- <div class="row q-mt-sm"> -->
                      <div class="col-sm-6 pull-right" v-if="role !== 'Cobranza'">
                        <input style="display:none" inverted color="dark" stack-label="Archivo csv" type="file" name="" value="" ref="fileInputCSV" accept=".csv" @change="uploadCsvFile()">
                        <q-btn @click="$refs.fileInputCSV.click()" color="tertiary" :loading="loadingButton">
                          <i class="fa fa-upload" aria-hidden="true"></i>&nbsp;Subir CSV
                        </q-btn>
                        <q-btn @click="borrarActividades()" color="tertiary" icon="delete" :loading="loadingButton" style="margin-left: 20px;">Limpiar</q-btn>
                        <q-btn @click="exportCSVActividades()" color="green" icon="fas fa-file-excel" style="margin-left: 20px;">&nbsp;Descargar actividades</q-btn>
                      </div>
                    </div>

                    <div class="col-sm-12 q-px-md" id="sticky-arbol">
                      <div class="q-table-container q-table-dense" style="margin-top:20px;">
                        <div class="q-table-middle scroll">
                          <table class="q-table">
                            <thead>
                              <tr style="text-align: center;">
                                <th>Acciones</th>
                                <th>Nombre</th>
                                <th>Avance</th>
                                <th>Avance $</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Costo actual</th>
                                <th>Costo inicial</th>
                                <th>Ejercido </th>
                                <th>Disponible </th>
                                <th>Solicitado </th>
                                <th>Fin real</th>
                                <th>EDT</th>
                                <th>Resumen</th>
                                <th>Duración</th>
                                <th>Duración restante</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="(item ,index)  in (arrayTreeObj)" :key="index" v-bind:class="[(item.id != selectedRowID.id) ? 'my-label':'text-green bg-light-green-11','']">
                                <td data-th="" style="text-align: left;">
                                  <q-checkbox v-model="item.finalizado" @click.native="finalizarActividad(item, index)" color="teal" v-if="item.children.length === 0">
                                    <q-tooltip>Finalizar actividad</q-tooltip>
                                  </q-checkbox>
                                  <q-checkbox v-model="item.visible" @click.native="visibleActividad(item, index)" color="teal" v-if="item.children.length === 0">
                                    <q-tooltip>Bloquear actividad</q-tooltip>
                                  </q-checkbox>
                                  <q-btn small flat @click="createActividad(item,index)" color="purple" icon="add_circle" size="sm">
                                    <q-tooltip>Agregar actividad</q-tooltip>
                                  </q-btn>
                                  <q-btn small flat @click="editActividad(item,index)" color="blue-6" icon="fas fa-edit" size="sm">
                                    <q-tooltip>Editar</q-tooltip>
                                  </q-btn>
                                  <q-btn small flat @click="uploadFiles(item,index)" color="teal" icon="folder" size="sm">
                                    <q-tooltip>Documentos</q-tooltip>
                                  </q-btn>
                                  <q-btn small flat @click="delete_single(item,index)" color="negative" icon="delete" size="sm" v-if="item.children.length === 0 && item.cantidad_ejercida === '0.00' && item.cantidad_comprometida === '0.00'">
                                    <q-tooltip>Eliminar</q-tooltip>
                                  </q-btn>
                                </td>
                                <td data-th="Nombre" @click="toggle(item, index)" style="cursor: pointer;text-align:left;">

                                  <span class="q-tree-link q-tree-label" v-bind:style="setPadding(item)">
                                    <q-icon  style="cursor: pointer;font-size:15px;" :name="iconName(item)" :color="item.color"></q-icon>
                                    {{item.nombre}}
                                  </span>

                                </td>
                                <td data-th="Avance" style="text-align: center;">{{item.avance}}%</td>
                                <td data-th="Avance $" style="text-align: center;">{{item.avance_monetario}}%</td>
                                <td data-th="Inicio" style="text-align: center;">{{item.inicio}} </td>
                                <td data-th="Fin" style="text-align: center;">{{item.fin}}</td>
                                <td data-th="Costo" style="text-align: right;">${{item.costoCopia}}</td>
                                <td data-th="Costo inicial" style="text-align: right;">${{item.presupuesto_inicial_copia}}</td>
                                <td data-th="Ejercido" style="text-align: right; color: red;" v-if="item.children.length > 0 && item.cantidad_ejercida !== '-------'">${{item.cantidad_ejercida}}</td>
                                <td data-th="Ejercido" style="text-align: right;" v-else>${{item.cantidad_ejercida}}</td>
                                <td data-th="Disponible" style="text-align: right; color: red;" v-if="(parseFloat(item.cantidad_disponible) < 0 && item.cantidad_disponible !== '-------') || (parseFloat(item.cantidad_disponible) < 0 && item.cantidad_disponible !== '-------' && item.children.length > 0)">${{item.cantidad_disponible}}</td>
                                <td data-th="Disponible" style="text-align: right;" v-else>${{item.cantidad_disponible}}</td>
                                <td data-th="Solicitado" style="text-align: right;">${{item.cantidad_comprometida}} </td>
                                <td data-th="Fin Real" style="text-align: center;">{{item.fin_real}} </td>
                                <td data-th="EDT" style="text-align: left;">{{item.indice}}</td>
                                <td data-th="Resumen" style="text-align: center;">{{item.resumen}}</td>
                                <td data-th="Duración" style="text-align: center;">{{item.duracion}}</td>
                                <td data-th="Duración restante" style="text-align: center;">{{item.duracion_restante}}</td>
                              </tr>
                            </tbody>
                          </table>
                          <q-inner-loading :visible="form_actividades.loading">
                            <q-spinner-dots size="64px" color="primary" />
                          </q-inner-loading>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <q-modal no-backdrop-dismiss v-if="form_actividades.modal_crear" style="background-color: rgba(0,0,0,0.7);" v-model="form_actividades.modal_crear" :content-css="{minWidth: '80vw', minHeight: '580px'}">
                  <q-modal-layout>
                    <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
                      <div class="col-sm-9">
                        <q-toolbar-title>
                          Crear nueva actividad
                        </q-toolbar-title>
                      </div>
                      <div class="col-sm-3 pull-right">
                        <q-btn
                          flat
                          round
                          dense
                          color="white"
                          @click="form_actividades.modal_crear = false"
                          icon="fas fa-times-circle"
                        />
                      </div>
                    </q-toolbar>
                    <div class="row q-mt-lg" style="margin-top:20px;">
                      <div class="col-sm-4" style="margin:auto;" >
                        <q-field icon="icono_prueba" icon-color="dark" helper="Porcentaje de avance">
                          <q-knob
                            v-model="form_actividades.fieldsPresupuesto.avance"
                            size="100px"
                            style="font-size: 1rem; margin-left:30%;"
                            color="light-blue-10"
                            track-color="light-blue-12"
                            line-width="5px"
                            :min="0"
                            :max="100"
                            :step="1"
                          >
                            {{ form_actividades.fieldsPresupuesto.avance }} <q-icon class="q-ml-xs" name="fas fa-percent" />
                          </q-knob>
                          <q-input clearable align="right" :decimals="0" numeric-keyboard-toggle v-model="form_actividades.fieldsPresupuesto.avance" type="number" suffix="%"></q-input>
                        </q-field>
                      </div>
                    </div>
                    <div class="row q-mt-lg" style="margin-right:10px;">
                      <div class="col-sm-12">
                        <q-field icon="fas fa-chart-line" icon-color="dark" :error="$v.form_actividades.fieldsPresupuesto.nombre.$error" error-label="Ingrese el nombre de la actividad">
                          <q-input upper-case v-model="form_actividades.fieldsPresupuesto.nombre" type="text" inverted color="dark" float-label="Nombre de la actividad" maxlength="100"></q-input>
                        </q-field>
                      </div>
                    </div>
                    <div class="row q-mt-lg" style="margin-right:10px;">
                      <div class="col-sm-4">
                        <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                          <q-input v-model.lazy="form_actividades.fieldsPresupuesto.costo" v-money="money" inverted color="dark" float-label="Costo" suffix="MXN"></q-input>
                        </q-field>
                      </div>
                      <div class="col-sm-4">
                        <q-field icon="fas fa-sun" icon-color="dark" :error="$v.form_actividades.fieldsPresupuesto.duracion.$error" error-label="Ingrese la duración de la actividad">
                          <q-input v-model="form_actividades.fieldsPresupuesto.duracion" type="number" inverted color="dark" float-label="Duración (días)" numeric-keyboard-toggle></q-input>
                        </q-field>
                      </div>
                      <div class="col-sm-4">
                        <q-field icon="fas fa-sun" icon-color="dark" :error="$v.form_actividades.fieldsPresupuesto.duracion_restante.$error" error-label="Ingrese la duración restante de la actividad">
                          <q-input v-model="form_actividades.fieldsPresupuesto.duracion_restante" type="number" inverted color="dark" float-label="Duración restante (días)" numeric-keyboard-toggle></q-input>
                        </q-field>
                      </div>
                    </div>
                    <div class="row q-mt-lg" style="margin-right:10px;">
                      <div class="col-sm-4">
                        <q-field icon="date_range" icon-color="dark" :error="$v.form_actividades.fieldsPresupuesto.inicio.$error" error-label="Ingrese la fecha de inicio">
                          <q-datetime v-model="form_actividades.fieldsPresupuesto.inicio" type="date" inverted color="dark" float-label="Fecha inicio" clearable align="center"></q-datetime>
                        </q-field>
                      </div>
                      <div class="col-sm-4">
                        <q-field icon="date_range" icon-color="dark" :error="$v.form_actividades.fieldsPresupuesto.fin.$error" error-label="Ingrese la fecha fin">
                          <q-datetime v-model="form_actividades.fieldsPresupuesto.fin" type="date" inverted color="dark" float-label="Fecha fin" clearable align="center"></q-datetime>
                        </q-field>
                      </div>
                      <div class="col-sm-4">
                        <q-field icon="date_range" icon-color="dark">
                          <q-datetime v-model="form_actividades.fieldsPresupuesto.fin_real" type="date" inverted color="dark" float-label="Fin real" clearable align="center"></q-datetime>
                        </q-field>
                      </div>
                      <!-- <div class="col-sm-3" style="margin-top:5px;">
                        <q-field icon="fas fa-file-alt" icon-color="dark" helper="Resumen">
                          <q-toggle v-model="form_actividades.fieldsPresupuesto.resumen" color="green" checked-icon="fas fa-check" unchecked-icon="fas fa-times"/>
                        </q-field>
                      </div> -->
                    </div>
                    <div class="row q-mt-lg" style="margin-right:10px;margin-bottom:10px;">
                      <div class="col-sm-3 offset-sm-9 pull-right">
                        <q-btn @click="crearActividadDetalle()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
                      </div>
                    </div>
                  </q-modal-layout>
                </q-modal>

                <q-modal no-backdrop-dismiss v-if="form_actividades.modal_editar" style="background-color: rgba(0,0,0,0.7);" v-model="form_actividades.modal_editar" :content-css="{minWidth: '80vw', minHeight: '580px'}">
                  <q-modal-layout>
                    <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
                      <div class="col-sm-9">
                        <q-toolbar-title>
                          Edición de actividad
                        </q-toolbar-title>
                      </div>
                      <div class="col-sm-3 pull-right">
                        <q-btn
                          flat
                          round
                          dense
                          color="white"
                          @click="form_actividades.modal_editar = false"
                          icon="fas fa-times-circle"
                        />
                      </div>
                    </q-toolbar>
                    <div class="row q-mt-lg" style="margin-top:20px;">
                      <div class="col-sm-4" style="margin:auto;" >
                        <q-field icon="icono_prueba" icon-color="dark" helper="Porcentaje de avance">
                          <q-knob
                            v-model="form_actividades.fieldsPresupuesto.avance"
                            size="100px"
                            style="font-size: 1rem; margin-left:30%;"
                            color="light-blue-10"
                            track-color="light-blue-12"
                            line-width="5px"
                            :min="0"
                            :max="100"
                            :step="1"
                          >
                            {{ form_actividades.fieldsPresupuesto.avance }} <q-icon class="q-ml-xs" name="fas fa-percent" />
                          </q-knob>
                          <q-input clearable align="right" :decimals="0" numeric-keyboard-toggle v-model="form_actividades.fieldsPresupuesto.avance" type="number" suffix="%"></q-input>
                        </q-field>
                      </div>
                    </div>
                    <div class="row q-mt-lg" style="margin-right:10px;">
                      <div class="col-sm-10">
                        <q-field icon="fas fa-chart-line" icon-color="dark" :error="$v.form_actividades.fieldsPresupuesto.nombre.$error" error-label="Ingrese el nombre de la actividad">
                          <q-input upper-case v-model="form_actividades.fieldsPresupuesto.nombre" type="text" inverted color="dark" float-label="Nombre de la actividad" maxlength="100"></q-input>
                        </q-field>
                      </div>
                      <div class="col-sm-2">
                        <q-field icon="fas fa-arrow-circle-down" icon-color="dark">
                          <q-input upper-case v-model="form_actividades.fieldsPresupuesto.indice" type="text" inverted color="dark" float-label="EDT" readonly disable></q-input>
                        </q-field>
                      </div>
                    </div>
                    <div class="row q-mt-lg" style="margin-right:10px;">
                      <div class="col-sm-3">
                        <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                          <q-input readonly v-model.lazy="form_actividades.fieldsPresupuesto.presupuesto_inicial" v-money="money" inverted color="dark" float-label="Monto inicial" suffix="MXN"></q-input>
                        </q-field>
                      </div>
                      <div class="col-sm-3">
                        <q-field icon="fas fa-dollar-sign" icon-color="dark">
                          <q-input v-if="this.role.toUpperCase() === 'root'.toUpperCase() || this.role.toUpperCase() === 'admin'.toUpperCase() || this.role.toUpperCase() === 'planeación'.toUpperCase()" v-model.lazy="form_actividades.fieldsPresupuesto.costo" v-money="money" inverted color="dark" float-label="Monto actual" suffix="MXN"></q-input>
                          <q-input v-else readonly v-model.lazy="form_actividades.fieldsPresupuesto.costo" v-money="money" inverted color="dark" float-label="Monto actual" suffix="MXN"></q-input>
                        </q-field>
                      </div>
                      <div class="col-sm-3">
                        <q-field icon="fas fa-sun" icon-color="dark">
                          <q-input v-model="form_actividades.fieldsPresupuesto.duracion" type="number" inverted color="dark" float-label="Duración" readonly numeric-keyboard-toggle></q-input>
                        </q-field>
                      </div>
                      <div class="col-sm-3">
                        <q-field icon="fas fa-sun" icon-color="dark">
                          <q-input readonly v-model="form_actividades.fieldsPresupuesto.duracion_restante" type="number" inverted color="dark" float-label="Duración restante" numeric-keyboard-toggle></q-input>
                        </q-field>
                      </div>
                    </div>
                    <div class="row q-mt-lg" style="margin-right:10px;">
                      <div class="col-sm-3">
                        <q-field icon="date_range" icon-color="dark">
                          <q-datetime v-model="form_actividades.fieldsPresupuesto.inicio" type="date" inverted color="dark" float-label="Inicio" clearable align="center"></q-datetime>
                        </q-field>
                      </div>
                      <div class="col-sm-3">
                        <q-field icon="date_range" icon-color="dark">
                          <q-datetime v-model="form_actividades.fieldsPresupuesto.fin" type="date" inverted color="dark" float-label="Fin" clearable align="center"></q-datetime>
                        </q-field>
                      </div>
                      <div class="col-sm-3">
                        <q-field icon="date_range" icon-color="dark">
                          <q-datetime v-model="form_actividades.fieldsPresupuesto.fin_real" type="date" inverted color="dark" float-label="Fin real" clearable align="center"></q-datetime>
                        </q-field>
                      </div>
                      <!-- <div class="col-sm-3" style="margin-top:5px;">
                        <q-field icon="fas fa-file-alt" icon-color="dark" helper="Resumen">
                          <q-toggle v-model="form_actividades.fieldsPresupuesto.resumen" color="green" checked-icon="fas fa-check" unchecked-icon="fas fa-times"/>
                        </q-field>
                      </div> -->
                    </div>
                    <div class="row q-mt-lg" style="margin-right:10px;margin-bottom:10px;">
                      <div class="col-sm-3 offset-sm-9 pull-right">
                        <q-btn @click="updateActividadDetalle()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
                      </div>
                    </div>
                  </q-modal-layout>
                </q-modal>

                <q-modal no-backdrop-dismiss v-if="form_actividades.modal_documentos" style="background-color: rgba(0,0,0,0.7);" v-model="form_actividades.modal_documentos" :content-css="{minWidth: '80vw', minHeight: '580px'}">
                  <q-modal-layout>
                    <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
                      <div class="col-sm-9">
                        <q-toolbar-title>
                          Documentos de actividad
                        </q-toolbar-title>
                      </div>
                      <div class="col-sm-3 pull-right">
                        <q-btn
                          flat
                          round
                          dense
                          color="white"
                          @click="form_actividades.modal_documentos = false"
                          icon="fas fa-times-circle"
                        />
                      </div>
                    </q-toolbar>
                    <div class="row q-mt-lg subtitulos" style="margin-left:15px;">
                      {{ form_actividades.fieldsPresupuesto.nombre }}
                    </div>
                    <div class="col-sm-3" style="margin-top:30px; margin-right:30px; margin-left: 30px;">
                      <input style="display:none" inverted color="dark" stack-label="Archivo" @change="uploadFormatoActividad()" type="file" name="" value="" ref="fileInputFormato" accept=".jpg,.jpeg,.png,.pdf,.docx" />
                        <q-btn @click="$refs.fileInputFormato.click()" color="tertiary" icon="attach_file" :loading="loadingButton">&nbsp; Cargar Archivo</q-btn>
                    </div>
                    <div class="row q-mt-sm" style="margin-top:30px; margin-right:30px; margin-left: 30px;">
                      <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                        <q-table id="sticky-table"
                          :data="form_archivos.data"
                          :columns="form_archivos.columns"
                          :selected.sync="form_archivos.selected"
                          :filter="form_archivos.filter"
                          color="positive"
                          title=""
                          :pagination.sync="form_archivos.pagination"
                          :loading="form_archivos.loading"
                          :rows-per-page-options="form_archivos.rowsOptions"
                        >
                        <template slot="body" slot-scope="props">
                          <q-tr :props="props">
                            <q-td key="name" :props="props">{{ props.row.name }}</q-td>
                              <q-td key="acciones" :props="props">
                                <q-btn small flat @click="descargarFormatoActividad(props.row.documento_id)" color="blue-8" icon="cloud_download">
                                  <q-tooltip>Descargar</q-tooltip>
                                </q-btn>
                                <q-btn small flat @click="deleteFormatoActividad(props.row.id)" color="negative" icon="delete">
                                  <q-tooltip>Eliminar</q-tooltip>
                                </q-btn>
                              </q-td>
                          </q-tr>
                        </template>
                        </q-table>
                          <q-inner-loading :visible="form_archivos.loading">
                            <q-spinner-dots size="64px" color="primary" />
                          </q-inner-loading>
                      </div>
                    </div>
                  </q-modal-layout>
                </q-modal>

                <q-modal v-if="informacionPresupuestos" style="background-color: rgba(0,0,0,0.7);" v-model="informacionPresupuestos" :content-css="{width: '40vw', height: '300px'}">
                  <q-modal-layout>
                    <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
                      <div class="col-sm-6">
                        <q-toolbar-title>
                          Presupuesto
                        </q-toolbar-title>
                      </div>
                      <div class="col-sm-6 pull-right">
                        <q-btn
                          flat
                          round
                          dense
                          color="white"
                          @click="informacionPresupuestos = false"
                          icon="fas fa-times-circle"
                        />
                      </div>
                    </q-toolbar>
                  </q-modal-layout>
                  <div class="row q-mt-lg" style="margin-top:70px;">
                    <div class="col-sm-12" v-if="objetoInformacionPresupuesto!==null">
                      <div style="font-weight:bold;font-size:1.2em;text-align:center;">{{objetoInformacionPresupuesto.nombre_proyecto}}</div>
                      <ul style="list-style:none;padding-left:15px;">
                        <li><label style="font-weight:bold;">Proyecto: </label><label style="margin-left:5px;">{{objetoInformacionPresupuesto.recurso}}</label></li>
                        <li><label style="font-weight:bold;">Fecha de inicio: </label><label style="margin-left:5px;">{{objetoInformacionPresupuesto.inicio}}</label></li>
                        <li><label style="font-weight:bold;">Fecha de término: </label><label style="margin-left:5px;">{{objetoInformacionPresupuesto.fin}}</label></li>
                        <li><label style="font-weight:bold;">Días: </label><label style="margin-left:5px;">{{objetoInformacionPresupuesto.dias}} dias</label></li>
                        <li><label style="font-weight:bold;">Presupuesto: </label><label style="margin-left:5px;">{{objetoInformacionPresupuesto.presupuesto}}</label></li>
                      </ul>
                    </div>
                  </div>
                </q-modal>

                <div class="q-px-md col-sm-12">
                  <q-tabs v-model="tab" class="shadow-1" inverted animated swipeable color="teal" align="justify">
                    <q-tab default name="colaborador_pane" slot="title" icon="person" label="Colaboradores"/>
                    <q-tab name="autorizadores_pane" slot="title" icon="person" label="Autorizadores" @click.native="cargarAutorizadores()"/>
                    <q-tab name="responsables_pane" slot="title" icon="person" label="Responsable de pagos" @click.native="cargarResponsables()"/>
                    <q-tab name="perfiles_pane" slot="title" icon="person" label="Perfiles" @click.native="getPerfiles()"/>

                    <q-tab-pane name="colaborador_pane">
                      <div class="col q-px-md">
                        <div>
                          <div class="row q-col-gutter-xs" style="padding-bottom: 10px;">
                            <div class="col-sm-1">
                              <q-btn @click="newColaborador()" class="btn_nuevo" icon="add">
                              </q-btn>
                            </div>
                            <div class="col-sm-11">
                              <q-search color="primary" v-model="form_colaboradores.filter"/>
                            </div>
                          </div>
                          <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                            <q-table id="sticky-table-newstyle"
                              :data="form_colaboradores.data"
                              :columns="form_colaboradores.columns"
                              :selected.sync="form_colaboradores.selected"
                              :filter="form_colaboradores.filter"
                              color="positive"
                              title=""
                              :dense="true"
                              :pagination.sync="form_colaboradores.pagination"
                              :loading="form_colaboradores.loading"
                              :rows-per-page-options="form_colaboradores.rowsOptions">
                              <template slot="body" slot-scope="props">
                                <q-tr :props="props">
                                  <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                                  <q-td key="perfil" :props="props">{{ props.row.perfil }}</q-td>
                                  <q-td key="acciones" :props="props">
                                    <q-btn small flat @click="editColaborador(props.row)" color="blue" icon="edit" v-if="role !== 'Lider'">
                                      <q-tooltip>Editar</q-tooltip>
                                    </q-btn>
                                    <q-btn small flat @click="deleteColaboradores(props.row.id)" color="negative" icon="delete" v-if="role !== 'Lider'">
                                      <q-tooltip>Eliminar</q-tooltip>
                                    </q-btn>
                                  </q-td>
                                </q-tr>
                              </template>
                            </q-table>
                            <q-inner-loading :visible="form_colaboradores.loading">
                              <q-spinner-dots size="64px" color="primary" />
                            </q-inner-loading>
                          </div>
                        </div>
                      </div>
                    </q-tab-pane>
                    <q-tab-pane name="solicitantes_pane">
                      <div class="col q-px-md">
                        <div>
                          <div class="row q-col-gutter-xs" style="padding-bottom: 10px;">
                            <div class="col-sm-1">
                              <q-btn @click="newSolicitante()" class="btn_nuevo" icon="add">
                              </q-btn>
                            </div>
                            <div class="col-sm-11">
                              <q-search color="primary" v-model="form_solicitantes.filter"/>
                            </div>
                          </div>
                          <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                            <q-table id="sticky-table-newstyle"
                              :data="form_solicitantes.data"
                              :columns="form_solicitantes.columns"
                              :selected.sync="form_solicitantes.selected"
                              :filter="form_solicitantes.filter"
                              color="positive"
                              title=""
                              :dense="true"
                              :pagination.sync="form_solicitantes.pagination"
                              :loading="form_solicitantes.loading"
                              :rows-per-page-options="form_solicitantes.rowsOptions">
                              <template slot="body" slot-scope="props">
                                <q-tr :props="props">
                                  <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                                  <q-td key="perfil" :props="props">{{ props.row.perfil }}</q-td>
                                  <q-td key="acciones" :props="props">
                                    <q-btn small flat @click="editSolicitante(props.row)" color="blue" icon="edit" v-if="role !== 'Lider'">
                                      <q-tooltip>Editar</q-tooltip>
                                    </q-btn>
                                    <q-btn small flat @click="deleteSolicitantes(props.row.id)" color="negative" icon="delete" v-if="role !== 'Lider'">
                                      <q-tooltip>Eliminar</q-tooltip>
                                    </q-btn>
                                  </q-td>
                                </q-tr>
                              </template>
                            </q-table>
                            <q-inner-loading :visible="form_solicitantes.loading">
                              <q-spinner-dots size="64px" color="primary" />
                            </q-inner-loading>
                          </div>
                        </div>
                      </div>
                    </q-tab-pane>
                    <q-tab-pane name="autorizadores_pane">
                      <div class="col q-px-md">
                        <div>
                          <div class="row q-col-gutter-xs" style="padding-bottom: 10px;">
                            <div class="col-sm-1">
                              <q-btn @click="ejecutaAutorizador()" class="btn_nuevo" icon="add">
                              </q-btn>
                            </div>
                            <div class="col-sm-11">
                              <q-search color="primary" v-model="form_autorizadores.filter"/>
                            </div>
                          </div>
                          <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                            <q-table id="sticky-table-newstyle"
                              :data="form_autorizadores.data"
                              :columns="form_autorizadores.columns"
                              :selected.sync="form_autorizadores.selected"
                              :filter="form_autorizadores.filter"
                              color="positive"
                              title=""
                              :dense="true"
                              :pagination.sync="form_autorizadores.pagination"
                              :loading="form_autorizadores.loading"
                              :rows-per-page-options="form_autorizadores.rowsOptions">
                              <template slot="body" slot-scope="props">
                                <q-tr :props="props">
                                  <q-td key="orden" :props="props">{{ props.row.orden }}</q-td>
                                  <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                                  <q-td key="perfil" :props="props">{{ props.row.perfil }}</q-td>
                                  <q-td key="alteracion" :props="props">{{ props.row.alteracion }}</q-td>
                                  <q-td key="acciones" :props="props">
                                    <q-btn small flat @click="editAutorizador(props.row)" color="blue" icon="edit" v-if="role !== 'Lider'">
                                      <q-tooltip>Editar</q-tooltip>
                                    </q-btn>
                                    <q-btn small flat @click="deleteAutorizadores(props.row.id)" color="negative" icon="delete" v-if="role !== 'Lider'">
                                      <q-tooltip>Eliminar</q-tooltip>
                                    </q-btn>
                                  </q-td>
                                </q-tr>
                              </template>
                            </q-table>
                            <q-inner-loading :visible="form_autorizadores.loading">
                              <q-spinner-dots size="64px" color="primary" />
                            </q-inner-loading>
                          </div>
                        </div>
                      </div>
                    </q-tab-pane>
                    <q-tab-pane name="responsables_pane">
                      <div class="col q-px-md">
                        <div>
                          <div class="row q-col-gutter-xs" style="padding-bottom: 10px;">
                            <div class="col-sm-1">
                              <q-btn @click="newResponsable()" class="btn_nuevo" icon="add">
                              </q-btn>
                            </div>
                            <div class="col-sm-11">
                              <q-search color="primary" v-model="form_responsables.filter"/>
                            </div>
                          </div>
                          <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                            <q-table id="sticky-table-newstyle"
                              :data="form_responsables.data"
                              :columns="form_responsables.columns"
                              :selected.sync="form_responsables.selected"
                              :filter="form_responsables.filter"
                              color="positive"
                              title=""
                              :dense="true"
                              :pagination.sync="form_responsables.pagination"
                              :loading="form_responsables.loading"
                              :rows-per-page-options="form_responsables.rowsOptions">
                              <template slot="body" slot-scope="props">
                                <q-tr :props="props">
                                  <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                                  <q-td key="perfil" :props="props">{{ props.row.perfil }}</q-td>
                                  <q-td key="acciones" :props="props">
                                    <q-btn small flat @click="editResponsable(props.row)" color="blue" icon="edit" v-if="role !== 'Lider'">
                                      <q-tooltip>Editar</q-tooltip>
                                    </q-btn>
                                    <q-btn small flat @click="deleteResponsables(props.row.id)" color="negative" icon="delete" v-if="role !== 'Lider'">
                                      <q-tooltip>Eliminar</q-tooltip>
                                    </q-btn>
                                  </q-td>
                                </q-tr>
                              </template>
                            </q-table>
                            <q-inner-loading :visible="form_responsables.loading">
                              <q-spinner-dots size="64px" color="primary" />
                            </q-inner-loading>
                          </div>
                        </div>
                      </div>
                    </q-tab-pane>
                    <q-tab-pane name="perfiles_pane">
                      <div class="col q-px-md">
                        <div>
                          <div class="row q-col-gutter-xs" style="padding-bottom: 10px;">
                            <div class="col-sm-1">
                              <q-btn @click="newPerfil()" class="btn_nuevo" icon="add">
                              </q-btn>
                            </div>
                            <div class="col-sm-11">
                              <q-search color="primary" v-model="form_perfiles.filter"/>
                            </div>
                          </div>
                          <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                            <q-table id="sticky-table-newstyle"
                              :data="form_perfiles.data"
                              :columns="form_perfiles.columns"
                              :selected.sync="form_perfiles.selected"
                              :filter="form_perfiles.filter"
                              color="positive"
                              title=""
                              :pagination.sync="form_perfiles.pagination"
                              :loading="form_perfiles.loading"
                              :rows-per-page-options="form_perfiles.rowsOptions"
                              >
                              <template slot="body" slot-scope="props">
                                <q-tr :props="props">
                                  <q-td key="nombre" :props="props">{{ props.row.nombre }} {{ props.row.apellido_paterno }} {{ apellido_materno }}</q-td>
                                  <q-td key="participacion" :props="props">{{ props.row.participacion }}</q-td>
                                  <q-td key="acciones" :props="props">
                                    <q-btn small flat @click="deletePerfiles(props.row.id)" color="red" icon="delete" v-if="role !== 'Lider'">
                                      <q-tooltip>Eliminar</q-tooltip>
                                    </q-btn>
                                  </q-td>
                                </q-tr>
                              </template>
                            </q-table>
                            <q-inner-loading :visible="form_perfiles.loading">
                              <q-spinner-dots size="64px" color="primary" />
                            </q-inner-loading>
                          </div>
                        </div>
                      </div>
                    </q-tab-pane>
                  </q-tabs>
                </div>
                <!-- SOLICITANTES -->
                <q-modal no-backdrop-dismiss v-if="form_solicitantes.solicitantes_proyecto" style="background-color: rgba(0,0,0,0.7);" v-model="form_solicitantes.solicitantes_proyecto" :content-css="{width: '40vw', height: '200px'}">
                  <q-modal-layout>
                    <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
                      <div class="col-sm-10">
                        <q-toolbar-title>
                          Nuevo solicitante
                        </q-toolbar-title>
                      </div>
                      <div class="col-sm-2 pull-right">
                        <q-btn
                          flat
                          round
                          dense
                          color="white"
                          @click="form_solicitantes.solicitantes_proyecto = false"
                          icon="fas fa-times-circle"/>
                      </div>
                    </q-toolbar>
                  </q-modal-layout>
                  <div class="row q-mt-lg" style="margin-top:70px;margin-right:20px;">
                    <div class="col-sm-6">
                      <q-field icon="person" icon-color="dark" :error="$v.form_solicitantes.fieldsPresupuesto.usuario_id.$error" error-label="Por favor elija un usuario">
                        <q-select v-model="form_solicitantes.fieldsPresupuesto.usuario_id" inverted color="dark" float-label="Usuario" :options="form_solicitantes.solicitantesOptions" filter/>
                      </q-field>
                    </div>
                    <div class="col-sm-6">
                      <q-field icon="folder_shared" icon-color="dark" :error="$v.form_solicitantes.fieldsPresupuesto.perfil.$error" error-label="Escriba el perfil">
                        <q-input upper-case v-model="form_solicitantes.fieldsPresupuesto.perfil" type="text" inverted color="dark" float-label="Perfil" maxlength="100" />
                      </q-field>
                    </div>
                  </div>
                  <div class="row q-mt-lg" style="margin-right:20px;">
                    <div class="col-sm-4 offset-sm-8 pull-right">
                      <q-btn @click="saveSolicitantes()" class="btn_guardar" icon-right="fas fa-save" :loading="loadingButton">Agregar</q-btn>
                    </div>
                  </div>
                </q-modal>
                <!-- SOLICITANTES -->
                <q-modal no-backdrop-dismiss v-if="form_solicitantes.editar_solicitantes_proyecto" style="background-color: rgba(0,0,0,0.7);" v-model="form_solicitantes.editar_solicitantes_proyecto" :content-css="{width: '40vw', height: '200px'}">
                  <q-modal-layout>
                    <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
                      <div class="col-sm-10">
                        <q-toolbar-title>
                          Editar solicitante
                        </q-toolbar-title>
                      </div>
                      <div class="col-sm-2 pull-right">
                        <q-btn
                          flat
                          round
                          dense
                          color="white"
                          @click="form_solicitantes.editar_solicitantes_proyecto = false"
                          icon="fas fa-times-circle"
                        />
                      </div>
                    </q-toolbar>
                  </q-modal-layout>
                  <div class="row q-mt-lg" style="margin-top:70px;margin-right:20px;">
                    <div class="col-sm-6">
                      <q-field icon="person" icon-color="dark" :error="$v.form_solicitantes.fieldsPresupuesto.usuario_id.$error" error-label="Por favor elija un usuario">
                        <q-select v-model="form_solicitantes.fieldsPresupuesto.usuario_id" inverted color="dark" float-label="Usuario" :options="form_solicitantes.solicitantesOptions" filter/>
                      </q-field>
                    </div>
                    <div class="col-sm-6">
                        <q-field icon="folder_shared" icon-color="dark" :error="$v.form_solicitantes.fieldsPresupuesto.perfil.$error" error-label="Escriba el perfil">
                          <q-input upper-case v-model="form_solicitantes.fieldsPresupuesto.perfil" type="text" inverted color="dark" float-label="Perfil" maxlength="100" />
                        </q-field>
                      </div>
                  </div>
                  <div class="row q-mt-lg" style="margin-right:20px;">
                    <div class="col-sm-4 offset-sm-8 pull-right">
                      <q-btn @click="editarSolicitante()" class="btn_guardar" icon-right="fas fa-save" :loading="loadingButton">Agregar</q-btn>
                    </div>
                  </div>
                </q-modal>
                <!-- COLABORADORES -->
                <q-modal no-backdrop-dismiss v-if="form_colaboradores.colaboradores_proyecto" style="background-color: rgba(0,0,0,0.7);" v-model="form_colaboradores.colaboradores_proyecto" :content-css="{width: '40vw', height: '200px'}">
                  <q-modal-layout>
                    <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
                      <div class="col-sm-10">
                        <q-toolbar-title>
                          Nuevo colaborador
                        </q-toolbar-title>
                      </div>
                      <div class="col-sm-2 pull-right">
                        <q-btn
                          flat
                          round
                          dense
                          color="white"
                          @click="form_colaboradores.colaboradores_proyecto = false"
                          icon="fas fa-times-circle"
                        />
                      </div>
                    </q-toolbar>
                  </q-modal-layout>
                  <div class="row q-mt-lg" style="margin-top:70px;margin-right:20px;">
                    <div class="col-sm-6">
                      <q-field icon="person" icon-color="dark" :error="$v.form_colaboradores.fieldsPresupuesto.usuario_id.$error" error-label="Por favor elija un usuario">
                        <q-select v-model="form_colaboradores.fieldsPresupuesto.usuario_id" inverted color="dark" float-label="Usuario" :options="usuariosOptions" filter/>
                      </q-field>
                    </div>
                    <div class="col-sm-6">
                        <q-field icon="folder_shared" icon-color="dark" :error="$v.form_colaboradores.fieldsPresupuesto.perfil.$error" error-label="Escriba el perfil">
                          <q-input upper-case v-model="form_colaboradores.fieldsPresupuesto.perfil" type="text" inverted color="dark" float-label="Perfil" maxlength="100" />
                        </q-field>
                      </div>
                  </div>
                  <div class="row q-mt-lg" style="margin-right:20px;">
                    <div class="col-sm-4 offset-sm-8 pull-right">
                      <q-btn @click="saveColaboradores()" class="btn_guardar" icon-right="fas fa-save" :loading="loadingButton">Agregar</q-btn>
                    </div>
                  </div>
                </q-modal>
                <!-- COLABORADORES -->
                <q-modal no-backdrop-dismiss v-if="form_colaboradores.editar_colaboradores_proyecto" style="background-color: rgba(0,0,0,0.7);" v-model="form_colaboradores.editar_colaboradores_proyecto" :content-css="{width: '40vw', height: '200px'}">
                  <q-modal-layout>
                    <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
                      <div class="col-sm-10">
                        <q-toolbar-title>
                          Editar colaborador
                        </q-toolbar-title>
                      </div>
                      <div class="col-sm-2 pull-right">
                        <q-btn
                          flat
                          round
                          dense
                          color="white"
                          @click="form_colaboradores.editar_colaboradores_proyecto = false"
                          icon="fas fa-times-circle"
                        />
                      </div>
                    </q-toolbar>
                  </q-modal-layout>
                  <div class="row q-mt-lg" style="margin-top:70px;margin-right:20px;">
                    <div class="col-sm-6">
                      <q-field icon="person" icon-color="dark" :error="$v.form_colaboradores.fieldsPresupuesto.usuario_id.$error" error-label="Por favor elija un usuario">
                        <q-select v-model="form_colaboradores.fieldsPresupuesto.usuario_id" inverted color="dark" float-label="Usuario" :options="usuariosOptions" filter/>
                      </q-field>
                    </div>
                    <div class="col-sm-6">
                        <q-field icon="folder_shared" icon-color="dark" :error="$v.form_colaboradores.fieldsPresupuesto.perfil.$error" error-label="Escriba el perfil">
                          <q-input upper-case v-model="form_colaboradores.fieldsPresupuesto.perfil" type="text" inverted color="dark" float-label="Perfil" maxlength="100" />
                        </q-field>
                      </div>
                  </div>
                  <div class="row q-mt-lg" style="margin-right:20px;">
                    <div class="col-sm-4 offset-sm-8 pull-right">
                      <q-btn @click="editarColaborador()" class="btn_guardar" icon-right="fas fa-save" :loading="loadingButton">Guardar</q-btn>
                    </div>
                  </div>
                </q-modal>
                <!-- AUTORIZADORES -->
                <q-modal no-backdrop-dismiss v-if="form_autorizadores.autorizadores_proyecto" style="background-color: rgba(0,0,0,0.7);" v-model="form_autorizadores.autorizadores_proyecto" :content-css="{width: '55vw', height: '280px'}">
                  <q-modal-layout>
                    <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
                      <div class="col-sm-10">
                        <q-toolbar-title>
                          Nuevo autorizador
                        </q-toolbar-title>
                      </div>
                      <div class="col-sm-2 pull-right">
                        <q-btn
                          flat
                          round
                          dense
                          color="white"
                          @click="form_autorizadores.autorizadores_proyecto = false"
                          icon="fas fa-times-circle"
                        />
                      </div>
                    </q-toolbar>
                  </q-modal-layout>
                  <div class="row q-mt-lg" style="margin-top:70px;margin-right:20px;">
                    <div class="col-sm-6">
                      <q-field icon="person" icon-color="dark" :error="$v.form_autorizadores.fieldsPresupuesto.usuario_id.$error" error-label="Por favor elija un usuario">
                        <q-select v-model="form_autorizadores.fieldsPresupuesto.usuario_id" inverted color="dark" float-label="Usuario" :options="usuariosOptions" filter/>
                      </q-field>
                    </div>
                    <div class="col-sm-4">
                        <q-field icon="folder_shared" icon-color="dark" :error="$v.form_autorizadores.fieldsPresupuesto.perfil.$error" error-label="Escriba el perfil">
                          <q-input upper-case v-model="form_autorizadores.fieldsPresupuesto.perfil" type="text" inverted color="dark" float-label="Perfil" maxlength="100" />
                        </q-field>
                    </div>
                    <div class="col-sm-2">
                        <q-field icon="list" icon-color="dark" :error="$v.form_autorizadores.fieldsPresupuesto.orden.$error" error-label="Escriba el nivel del autorizador">
                          <q-select v-model="form_autorizadores.fieldsPresupuesto.orden" inverted color="dark" float-label="Nivel" :options="form_autorizadores.selectNivel" filter/>
                        </q-field>
                    </div>
                  </div>
                  <div class="row q-mt-lg" style="margin-right:20px;">
                    <div class="col-sm-3" style="margin-left:15px;">
                        <q-checkbox v-model="alterar_presupuesto" label="Ajustar presupuesto" color="amber"/>
                    </div>
                    <div class="col-sm-4 offset-sm-8 pull-right">
                      <q-btn @click="saveAutorizadores()" class="btn_guardar" icon-right="fas fa-save" :loading="loadingButton">Agregar</q-btn>
                    </div>
                  </div>
                </q-modal>
                <!-- AUTORIZADORES -->
                <q-modal no-backdrop-dismiss v-if="form_autorizadores.editar_autorizadores_proyecto" style="background-color: rgba(0,0,0,0.7);" v-model="form_autorizadores.editar_autorizadores_proyecto" :content-css="{width: '55vw', height: '280px'}">
                  <q-modal-layout>
                    <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
                      <div class="col-sm-10">
                        <q-toolbar-title>
                          Editar autorizador
                        </q-toolbar-title>
                      </div>
                      <div class="col-sm-2 pull-right">
                        <q-btn
                          flat
                          round
                          dense
                          color="white"
                          @click="form_autorizadores.editar_autorizadores_proyecto = false"
                          icon="fas fa-times-circle"
                        />
                      </div>
                    </q-toolbar>
                  </q-modal-layout>
                  <div class="row q-mt-lg" style="margin-top:70px;margin-right:20px;">
                    <div class="col-sm-6">
                      <q-field icon="person" icon-color="dark" :error="$v.form_autorizadores.fieldsPresupuesto.usuario_id.$error" error-label="Por favor elija un usuario">
                        <q-select v-model="form_autorizadores.fieldsPresupuesto.usuario_id" inverted color="dark" float-label="Usuario" :options="usuariosOptions" filter/>
                      </q-field>
                    </div>
                    <div class="col-sm-4">
                        <q-field icon="folder_shared" icon-color="dark" :error="$v.form_autorizadores.fieldsPresupuesto.perfil.$error" error-label="Escriba el perfil">
                          <q-input upper-case v-model="form_autorizadores.fieldsPresupuesto.perfil" type="text" inverted color="dark" float-label="Perfil" maxlength="100" />
                        </q-field>
                    </div>
                    <div class="col-sm-2">
                        <q-field icon="list" icon-color="dark" :error="$v.form_autorizadores.fieldsPresupuesto.orden.$error" error-label="Escriba el nivel del autorizador">
                          <q-select v-model="form_autorizadores.fieldsPresupuesto.orden" inverted color="dark" float-label="Nivel" :options="form_autorizadores.selectNivel" filter/>
                        </q-field>
                    </div>
                  </div>
                  <div class="row q-mt-lg" style="margin-right:20px;">
                    <div class="col-sm-3" style="margin-left:15px;">
                        <q-checkbox v-model="alterar_presupuesto" label="Ajustar presupuesto" color="amber"/>
                    </div>
                    <div class="col-sm-4 offset-sm-8 pull-right">
                      <q-btn @click="editarAutorizador()" class="btn_guardar" icon-right="fas fa-save" :loading="loadingButton">Guardar</q-btn>
                    </div>
                  </div>
                </q-modal>
                <!-- RESPONSABLES -->
                <q-modal no-backdrop-dismiss v-if="form_responsables.responsables_proyecto" style="background-color: rgba(0,0,0,0.7);" v-model="form_responsables.responsables_proyecto" :content-css="{width: '40vw', height: '200px'}">
                  <q-modal-layout>
                    <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
                      <div class="col-sm-10">
                        <q-toolbar-title>
                          Nuevo Responsable
                        </q-toolbar-title>
                      </div>
                      <div class="col-sm-2 pull-right">
                        <q-btn
                          flat
                          round
                          dense
                          color="white"
                          @click="form_responsables.responsables_proyecto = false"
                          icon="fas fa-times-circle"
                        />
                      </div>
                    </q-toolbar>
                  </q-modal-layout>
                  <div class="row q-mt-lg" style="margin-top:70px;margin-right:20px;">
                    <div class="col-sm-6">
                      <q-field icon="person" icon-color="dark" :error="$v.form_responsables.fieldsPresupuesto.usuario_id.$error" error-label="Por favor elija un usuario">
                        <q-select v-model="form_responsables.fieldsPresupuesto.usuario_id" inverted color="dark" float-label="Usuario" :options="usuariosOptions" filter/>
                      </q-field>
                    </div>
                    <div class="col-sm-6">
                        <q-field icon="folder_shared" icon-color="dark" :error="$v.form_responsables.fieldsPresupuesto.perfil.$error" error-label="Escriba el perfil">
                          <q-input upper-case v-model="form_responsables.fieldsPresupuesto.perfil" type="text" inverted color="dark" float-label="Perfil" maxlength="100" />
                        </q-field>
                      </div>
                  </div>
                  <div class="row q-mt-lg" style="margin-right:20px;">
                    <div class="col-sm-4 offset-sm-8 pull-right">
                      <q-btn @click="saveResponsables()" class="btn_guardar" icon-right="fas fa-save" :loading="loadingButton">Agregar</q-btn>
                    </div>
                  </div>
                </q-modal>
                <!-- RESPONSABLES -->
                <q-modal no-backdrop-dismiss v-if="form_responsables.editar_responsables_proyecto" style="background-color: rgba(0,0,0,0.7);" v-model="form_responsables.editar_responsables_proyecto" :content-css="{width: '40vw', height: '200px'}">
                  <q-modal-layout>
                    <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
                      <div class="col-sm-10">
                        <q-toolbar-title>
                          Editar Responsable
                        </q-toolbar-title>
                      </div>
                      <div class="col-sm-2 pull-right">
                        <q-btn
                          flat
                          round
                          dense
                          color="white"
                          @click="form_responsables.editar_responsables_proyecto = false"
                          icon="fas fa-times-circle"
                        />
                      </div>
                    </q-toolbar>
                  </q-modal-layout>
                  <div class="row q-mt-lg" style="margin-top:70px;margin-right:20px;">
                    <div class="col-sm-6">
                      <q-field icon="person" icon-color="dark" :error="$v.form_responsables.fieldsPresupuesto.usuario_id.$error" error-label="Por favor elija un usuario">
                        <q-select v-model="form_responsables.fieldsPresupuesto.usuario_id" inverted color="dark" float-label="Usuario" :options="usuariosOptions" filter/>
                      </q-field>
                    </div>
                    <div class="col-sm-6">
                        <q-field icon="folder_shared" icon-color="dark" :error="$v.form_responsables.fieldsPresupuesto.perfil.$error" error-label="Escriba el perfil">
                          <q-input upper-case v-model="form_responsables.fieldsPresupuesto.perfil" type="text" inverted color="dark" float-label="Perfil" maxlength="100" />
                        </q-field>
                      </div>
                  </div>
                  <div class="row q-mt-lg" style="margin-right:20px;">
                    <div class="col-sm-4 offset-sm-8 pull-right">
                      <q-btn @click="editarResponsable()" class="btn_guardar" icon-right="fas fa-save" :loading="loadingButton">Guardar</q-btn>
                    </div>
                  </div>
                </q-modal>
                <!-- PERFILES -->
                <q-modal no-backdrop-dismiss v-if="form_perfiles.perfiles_proyecto" style="background-color: rgba(0,0,0,0.7);" v-model="form_perfiles.perfiles_proyecto" :content-css="{width: '40vw', height: '200px'}">
                  <q-modal-layout>
                    <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
                      <div class="col-sm-10">
                        <q-toolbar-title>
                          Vincular perfil al project
                        </q-toolbar-title>
                      </div>
                      <div class="col-sm-2 pull-right">
                        <q-btn
                          flat
                          round
                          dense
                          color="white"
                          @click="form_perfiles.perfiles_proyecto = false"
                          icon="fas fa-times-circle"
                        />
                      </div>
                    </q-toolbar>
                  </q-modal-layout>
                  <div class="row q-mt-lg" style="margin-top:70px;margin-right:20px;">
                    <div class="col-sm-6">
                      <q-field icon="person" icon-color="dark" :error="$v.form_perfiles.fieldsPresupuesto.perfil_id.$error" error-label="Por favor elija un perfil">
                        <q-select v-model="form_perfiles.fieldsPresupuesto.perfil_id" inverted color="dark" float-label="Perfil" :options="perfilesOptions" filter/>
                      </q-field>
                    </div>
                    <div class="col-sm-6">
                      <q-field icon="folder_shared" icon-color="dark" :error="$v.form_perfiles.fieldsPresupuesto.participacion.$error" error-label="Revise la cantidad de caracteres">
                        <q-input upper-case v-model="form_perfiles.fieldsPresupuesto.participacion" type="text" inverted color="dark" float-label="Participación" maxlength="50" />
                      </q-field>
                    </div>
                  </div>
                  <div class="row q-mt-lg" style="margin-right:20px;">
                    <div class="col-sm-4 offset-sm-8 pull-right">
                      <q-btn @click="savePerfiles()" class="btn_guardar" icon-right="fas fa-save" :loading="loadingButton">Agregar</q-btn>
                    </div>
                  </div>
                </q-modal>
              </div>
            </q-tab-pane>

            <q-tab-pane name="organismos" class="bg-white" style="padding: 0;">
              <div v-if="viewsOrganismos.grid">
                <div class="q-pa-sm panel-header">
                  <div class="row">
                <!--     <div class="col-sm-6">
                      <div class="q-pa-sm q-gutter-sm">
                        <q-breadcrumbs align="left">
                          <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                          <q-breadcrumbs-el label="Organismos de apoyo"/>
                        </q-breadcrumbs>
                      </div>
                    </div> -->
                    <div class="col-sm-12 pull-right">
                      <div class="q-pa-sm q-gutter-sm">
                        <q-btn class="btn_nuevo" icon="add" label="Nuevo" @click="newRowOrganismos()" v-if="role !== 'Lider' && role !== 'Finanzas/Comisiones'"/>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col q-pa-md border-panel" style="border: none;margin-top: -2%;">
                  <div class="col-sm-12" style="padding-bottom: 10px;">
                    <q-search color="primary" v-model="form_organismos.filter"/>
                  </div>
                    <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                      <q-table id="sticky-table-newstyle"
                        :data="form_presupuestos.data_organismos"
                        :columns="form_organismos.columns"
                        :selected.sync="form_organismos.selected"
                        :filter="form_organismos.filter"
                        color="positive"
                        title=""
                        :pagination.sync="form_organismos.pagination"
                        :loading="form_organismos.loading"
                        :rows-per-page-options="form_organismos.rowsOptions"
                        >
                        <template slot="body" slot-scope="props">
                          <q-tr :props="props">
                            <q-td key="nombre" :props="props" style="cursor: pointer;">{{ props.row.nombre }}</q-td>
                            <q-td key="acciones" :props="props">
                              <q-btn small flat @click="editOrganismo(props.row)" color="blue-6" icon="edit" v-if="role !== 'Lider' && role !== 'Finanzas/Comisiones'">
                                <q-tooltip>Editar</q-tooltip>
                              </q-btn>
                              <q-btn small flat @click="deleteOrganismos(props.row.id)" color="red" icon="delete" v-if="role !== 'Lider' && role !== 'Finanzas/Comisiones'">
                                <q-tooltip>Eliminar</q-tooltip>
                              </q-btn>
                            </q-td>
                          </q-tr>
                        </template>
                      </q-table>
                      <q-inner-loading :visible="form_organismos.loading">
                        <q-spinner-dots size="64px" color="primary" />
                      </q-inner-loading>
                    </div>
                </div>
              </div>
              <div v-if="viewsOrganismos.create">
                  <div class="q-pa-sm panel-header">
                    <div class="row">
                      <!-- <div class="col-sm-6">
                        <div class="q-pa-sm q-gutter-sm">
                          <q-breadcrumbs align="left">
                            <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                            <q-breadcrumbs-el label="Organismos de apoyo" to="" @click.native="returnToView('organismo','grid')"/>
                            <q-breadcrumbs-el label="Nuevo"/>
                          </q-breadcrumbs>
                        </div>
                      </div> -->
                      <div class="col-sm-12 pull-right">
                        <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
                         <q-btn @click="returnToView('organismo','grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
                       </div>
                     </div>
                    </div>
                  </div>
                  <div class="q-pa-md">
                    <div class="row bg-white border-panel" style="padding:0;">
                      <div class="col q-pa-md">
                        <div class="row q-col-gutter-xs">
                          <div class="col-sm-9">
                            <q-field icon="fas fa-pen-square" icon-color="dark" :error="$v.form_organismos.fields.nombre.$error" error-label="Ingrese el nombre del organismo de apoyo">
                              <q-input upper-case v-model="form_organismos.fields.nombre" inverted color="dark" float-label="Nombre del organismo de apoyo"/>
                            </q-field>
                          </div>
                        </div>
                        <div class="row q-col-gutter-xs">
                          <div class="col-sm-2 offset-sm-10 pull-right">
                            <q-btn @click="saveOrganismos()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
              </div>
              <div v-if="viewsOrganismos.update">
                  <div class="q-pa-sm panel-header">
                    <div class="row">
                      <div class="col-sm-6">
                        <div class="q-pa-sm q-gutter-sm">
                          <!-- <q-breadcrumbs align="left">
                            <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                            <q-breadcrumbs-el label="Organismos de apoyo" to="" @click.native="returnToView('organismo','grid')"/>
                            <q-breadcrumbs-el label="Editar"/>
                          </q-breadcrumbs> -->
                        </div>
                      </div>
                      <div class="col-sm-6 pull-right">
                        <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
                         <q-btn @click="returnToView('organismo','grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
                       </div>
                     </div>
                    </div>
                  </div>
                  <div class="q-pa-md bg-grey-3">
                    <div class="row bg-white border-panel" style="padding:0;">
                      <div class="col q-pa-md">
                        <div class="row q-col-gutter-xs">
                          <div class="col-sm-9">
                            <q-field icon="fas fa-pen-square" icon-color="dark" :error="$v.form_organismos.fields.nombre.$error" error-label="Ingrese el nombre del organismo de apoyo">
                              <q-input upper-case v-model="form_organismos.fields.nombre" inverted color="dark" float-label="Nombre del organismo de apoyo"/>
                            </q-field>
                          </div>
                        </div>
                        <div class="row q-col-gutter-xs">
                          <div class="col-sm-2 offset-sm-10 pull-right">
                            <q-btn @click="updateOrganismos()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
              </div>
            </q-tab-pane>

            <q-tab-pane name="comisiones" class="bg-white" style="padding: 0;">
              <div v-if="viewsComisiones.grid">
                <div class="q-pa-sm panel-header">
                  <div class="row">
                    <!-- <div class="col-sm-6">
                      <div class="q-pa-sm q-gutter-sm">
                        <q-breadcrumbs align="left">
                          <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                          <q-breadcrumbs-el label="C.C."/>
                        </q-breadcrumbs>
                      </div>
                    </div> -->
                    <div class="col-sm-12 pull-right">
                      <div class="q-pa-sm q-gutter-sm">
                        <q-btn class="btn_nuevo" icon="add" label="Nuevo" @click="newRowComisiones()" v-if="role !== 'Lider'"/>
                        <!-- <q-btn class="btn_nuevo" icon="add" label="Nuevo" @click="getcom()"/> -->
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col q-pa-md border-panel" style="border: none;margin-top: -2%;">
                  <div class="col-sm-12" style="padding-bottom: 10px;">
                    <q-search color="primary" v-model="form.filter"/>
                  </div>
                  <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                    <q-table id="sticky-table-newstyle"
                      :data="form_presupuestos.data_comisiones"
                      :columns="form_comisiones.columns"
                      :selected.sync="form_comisiones.selected"
                      :filter="form_comisiones.filter"
                      color="positive"
                      title=""
                      :pagination.sync="form_comisiones.pagination"
                      :loading="form_comisiones.loading"
                      :rows-per-page-options="form_comisiones.rowsOptions"
                    >
                      <template slot="body" slot-scope="props">
                        <q-tr :props="props">
                          <q-td key="asignado" :props="props">{{ props.row.asignado }}</q-td>
                          <q-td key="aliado" :props="props">{{ props.row.aliado }}</q-td>
                          <q-td key="tipo" :props="props">{{ props.row.tipo }}</q-td>
                          <q-td key="cantidad_com" :props="props" v-if="props.row.tipo === 'PORCENTAJE'">{{ props.row.cantidad_com }}%</q-td>
                          <q-td key="cantidad_com" :props="props" v-else>${{ currencyFormat(props.row.cantidad_com) }}</q-td>
                          <q-td key="contrato" :props="props">{{ props.row.contrato }}</q-td>
                          <q-td key="monto_total_comision" :props="props">${{ props.row.monto_total_comision }}</q-td>
                          <q-td key="nombre_proyecto" :props="props">{{ props.row.nombre_proyecto }}</q-td>
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
                            <q-btn small flat @click="editComisiones(props.row)" color="blue-6" icon="edit" v-if="role !== 'Lider'">
                              <q-tooltip>Editar</q-tooltip>
                            </q-btn>
                            <q-btn small flat @click="deleteComisiones(props.row.id)" color="red" icon="delete" v-if="role !== 'Lider'">
                              <q-tooltip>Eliminar</q-tooltip>
                            </q-btn>
                          </q-td>
                        </q-tr>
                      </template>
                    </q-table>
                    <q-inner-loading :visible="form_comisiones.loading">
                      <q-spinner-dots size="64px" color="primary" />
                    </q-inner-loading>
                  </div>
                </div>
              </div>

              <div v-if="viewsComisiones.create">
                <div class="q-pa-sm panel-header">
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="q-pa-sm q-gutter-sm">
                        <!-- <q-breadcrumbs align="left">
                          <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                          <q-breadcrumbs-el label="Comisiones" to="" @click.native="returnToView('comision','grid')"/>
                          <q-breadcrumbs-el label="Nueva comisión"/>
                        </q-breadcrumbs> -->
                      </div>
                    </div>
                    <div class="col-sm-6 pull-right">
                      <div class="q-pa-sm q-gutter-sm">
                        <q-btn @click="returnToView('comision','grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
                        <q-btn @click="saveComisiones()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="q-pa-md">
                  <div class="row bg-white border-panel" style="padding:0;">
                    <div class="col q-pa-md col-sm-12">
                      <!-- <div class="row q-col-gutter-xs">
                        <div class="col-sm-6 pull-left">
                          <div class="col-xs-12 col-sm-12 col-md-12 pull-left" >
                            <q-btn @click="returnToView('comision','grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
                          </div>
                        </div>
                        <div class="col-sm-6 pull-right">
                          <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
                            <q-btn @click="saveComisiones()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
                          </div>
                        </div>
                      </div> -->
                    </div>
                    <div class="col q-pa-md">
                      <div class="row q-col-gutter-xs">
                        <div class="col-sm-3">
                          <q-field icon="person" icon-color="dark">
                          <q-select v-model="form_comisiones.fields.asignado" inverted color="dark" float-label="Tipo" :options="asignadoComision" filter/>
                          </q-field>
                        </div>
                        <div class="col-sm-6" v-if="form_comisiones.fields.asignado === 'ALIADO'">
                          <q-field icon="person" icon-color="dark">
                          <q-select v-model="form_comisiones.fields.aliado_id" inverted color="dark" float-label="Aliado" :options="aliadosOptions" filter/>
                          </q-field>
                        </div>
                        <div class="col-sm-6" v-if="form_comisiones.fields.asignado === 'VENDEDOR'">
                          <q-field icon="person" icon-color="dark">
                          <q-select v-model="form_comisiones.fields.vendedor_id" inverted color="dark" float-label="Vendedor" :options="vendedoresOptions" filter/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-donate" icon-color="dark" :error="$v.form_comisiones.fields.tipo.$error" error-label="Elija un tipo">
                          <q-select v-model="form_comisiones.fields.tipo" inverted color="dark" float-label="Tipo" :options="tipoComision" filter @input="contrato_utilidad()"/>
                          </q-field>
                        </div>
                        <div class="col-sm-3" v-if="form_comisiones.fields.tipo === 'CANTIDAD FIJA'">
                          <q-field icon="fas fa-dollar-sign" icon-color="dark">
                            <q-input v-model.lazy="form_comisiones.fields.monto" v-money="money" inverted color="dark" float-label="Monto" suffix="MXN" @input="contrato_utilidad()"></q-input>
                          </q-field>
                        </div>
                        <div class="col-sm-3" v-else>
                          <q-field icon="label" icon-color="dark">
                            <q-input upper-case v-model="form_comisiones.fields.porcentaje" type="text" inverted color="dark" float-label="Porcentaje" suffix="%" maxlength="50" @keyup="isNumber($event,'cantidad')" @input="contrato_utilidad()"/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-donate" icon-color="dark" :error="$v.form_comisiones.fields.metodo_pago.$error" error-label="Elija un método de pago">
                          <q-select v-model="form_comisiones.fields.metodo_pago" inverted color="dark" float-label="Método de pago" :options="metodoPagoComisionOptions" filter/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="style" icon-color="dark" :error="$v.form_comisiones.fields.aplicado.$error" error-label="Elija una opción">
                            <q-select v-model="form_comisiones.fields.aplicado" inverted color="dark" float-label="Aplica" :options="aplicadoOptions" filter @input="contrato_utilidad()"/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-file" icon-color="dark" :error="$v.form_comisiones.fields.contrato_id.$error" error-label="Elija un contrato">
                            <q-select v-model="form_comisiones.fields.contrato_id" inverted color="dark" float-label="Contrato" :options="contratosOptions" filter @input="contrato_utilidad()"/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="check" icon-color="dark" :error="$v.form_comisiones.fields.iva.$error" error-label="Seleccione una opción">
                            <q-select v-model="form_comisiones.fields.iva" inverted color="dark" float-label="Base" :options="ivaOptions" filter @input="contrato_utilidad()"/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-dollar-sign" icon-color="dark">
                            <q-input v-model="form_comisiones.monto_bolsa" inverted color="dark" float-label="Monto bolsa" suffix="MXN"></q-input>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-dollar-sign" icon-color="dark">
                            <q-input v-model="form_comisiones.monto_base" inverted color="dark" float-label="Monto base" suffix="MXN"></q-input>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-dollar-sign" icon-color="dark">
                            <q-input v-model="form_comisiones.monto_comision" inverted color="dark" float-label="Monto comisión" suffix="MXN"></q-input>
                          </q-field>
                        </div>
                        <!-- <div class="col-sm-2">
                          <q-field icon="fas fa-dollar-sign" icon-color="dark">
                            <q-input readonly v-model.lazy="form_comisiones.fields.monto_total" v-money="money" inverted color="dark" float-label="Total comisión" suffix="MXN"></q-input>
                          </q-field>
                        </div> -->
                      </div>
                      <div class="row q-col-gutter-xs">
                        <div class="col-sm-3">
                          <q-field icon="dns" icon-color="dark">
                            <q-select v-model="form_comisiones.fields.tiempo_pago" inverted color="dark" float-label="Tiempo pago" :options="tiempoOptions" filter/>
                          </q-field>
                        </div>
                        <div class="col-sm-9">
                          <q-field icon="fas fa-pen-square" icon-color="dark">
                            <q-input upper-case v-model="form_comisiones.fields.observaciones" inverted color="dark" float-label="Observaciones"/>
                          </q-field>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="viewsComisiones.update">
                <div class="q-pa-sm panel-header">
                  <div class="row">
                    <div class="col-sm-6">
                      <!-- <div class="q-pa-sm q-gutter-sm">
                        <q-breadcrumbs align="left">
                          <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                          <q-breadcrumbs-el label="Comisiones" to="" @click.native="returnToView('comision','grid')"/>
                          <q-breadcrumbs-el label="Editar comisión"/>
                        </q-breadcrumbs>
                      </div> -->
                    </div>
                     <div class="col-sm-6 pull-right">
                      <div class="q-pa-sm q-gutter-sm">
                        <q-btn @click="returnToView('comision','grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
                         <q-btn @click="updateComisiones()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="q-pa-md">
                  <div class="row bg-white border-panel" style="padding:0;">
                    <div class="col q-pa-md col-sm-12">
                      <!-- <div class="row q-col-gutter-xs">
                        <div class="col-sm-6 pull-left">
                          <div class="col-xs-12 col-sm-12 col-md-12 pull-left" >
                            <q-btn @click="returnToView('comision','grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
                          </div>
                        </div>
                        <div class="col-sm-6 pull-right">
                          <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
                            <q-btn @click="updateComisiones()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
                          </div>
                        </div>
                      </div> -->
                    </div>
                    <div class="col q-pa-md">
                      <div class="row q-col-gutter-xs">
                        <div class="col-sm-3">
                          <q-field icon="person" icon-color="dark">
                          <q-select v-model="form_comisiones.fields.asignado" inverted color="dark" float-label="Tipo" :options="asignadoComision" filter/>
                          </q-field>
                        </div>
                        <div class="col-sm-6" v-if="form_comisiones.fields.asignado === 'ALIADO'">
                          <q-field icon="person" icon-color="dark">
                          <q-select v-model="form_comisiones.fields.aliado_id" inverted color="dark" float-label="Aliado" :options="aliadosOptions" filter/>
                          </q-field>
                        </div>
                        <div class="col-sm-6" v-if="form_comisiones.fields.asignado === 'VENDEDOR'">
                          <q-field icon="person" icon-color="dark">
                          <q-select v-model="form_comisiones.fields.vendedor_id" inverted color="dark" float-label="Vendedor" :options="vendedoresOptions" filter/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-donate" icon-color="dark" :error="$v.form_comisiones.fields.tipo.$error" error-label="Elija un tipo">
                          <q-select v-model="form_comisiones.fields.tipo" inverted color="dark" float-label="Tipo" :options="tipoComision" filter @input="contrato_utilidad()"/>
                          </q-field>
                        </div>
                        <div class="col-sm-3" v-if="form_comisiones.fields.tipo === 'CANTIDAD FIJA'">
                          <q-field icon="fas fa-dollar-sign" icon-color="dark">
                            <q-input v-model.lazy="form_comisiones.fields.monto" v-money="money" inverted color="dark" float-label="Monto" suffix="MXN" @input="contrato_utilidad()"></q-input>
                          </q-field>
                        </div>
                        <div class="col-sm-3" v-else>
                          <q-field icon="label" icon-color="dark">
                            <q-input upper-case v-model="form_comisiones.fields.porcentaje" type="text" inverted color="dark" float-label="Porcentaje" suffix="%" maxlength="50" @keyup="isNumber($event,'cantidad')" @input="contrato_utilidad()"/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-donate" icon-color="dark" :error="$v.form_comisiones.fields.metodo_pago.$error" error-label="Elija un método de pago">
                          <q-select v-model="form_comisiones.fields.metodo_pago" inverted color="dark" float-label="Método de pago" :options="metodoPagoComisionOptions" filter/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="style" icon-color="dark" :error="$v.form_comisiones.fields.aplicado.$error" error-label="Elija una opción">
                            <q-select v-model="form_comisiones.fields.aplicado" inverted color="dark" float-label="Aplica" :options="aplicadoOptions" filter @input="contrato_utilidad()"/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-file" icon-color="dark" :error="$v.form_comisiones.fields.contrato_id.$error" error-label="Elija un contrato">
                            <q-select v-model="form_comisiones.fields.contrato_id" inverted color="dark" float-label="Contrato" :options="contratosOptions" filter @input="contrato_utilidad()"/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="check" icon-color="dark" :error="$v.form_comisiones.fields.iva.$error" error-label="Seleccione una opción">
                            <q-select v-model="form_comisiones.fields.iva" inverted color="dark" float-label="Base" :options="ivaOptions" filter @input="contrato_utilidad()"/>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-dollar-sign" icon-color="dark">
                            <q-input v-model="form_comisiones.monto_bolsa" inverted color="dark" float-label="Monto bolsa" suffix="MXN"></q-input>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-dollar-sign" icon-color="dark">
                            <q-input v-model="form_comisiones.monto_base" inverted color="dark" float-label="Monto base" suffix="MXN"></q-input>
                          </q-field>
                        </div>
                        <div class="col-sm-3">
                          <q-field icon="fas fa-dollar-sign" icon-color="dark">
                            <q-input v-model="form_comisiones.monto_comision" inverted color="dark" float-label="Monto comisión" suffix="MXN"></q-input>
                          </q-field>
                        </div>
                        <!-- <div class="col-sm-2">
                          <q-field icon="fas fa-dollar-sign" icon-color="dark">
                            <q-input readonly v-model.lazy="form_comisiones.fields.monto_total" v-money="money" inverted color="dark" float-label="Total comisión" suffix="MXN"></q-input>
                          </q-field>
                        </div> -->
                      </div>
                      <div class="row q-col-gutter-xs">
                        <div class="col-sm-3">
                          <q-field icon="dns" icon-color="dark">
                            <q-select v-model="form_comisiones.fields.tiempo_pago" inverted color="dark" float-label="Tiempo pago" :options="tiempoOptions" filter/>
                          </q-field>
                        </div>
                        <div class="col-sm-9">
                          <q-field icon="fas fa-pen-square" icon-color="dark">
                            <q-input upper-case v-model="form_comisiones.fields.observaciones" inverted color="dark" float-label="Observaciones"/>
                          </q-field>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </q-tab-pane>
          </q-tabs>
      </div>
    </div>

      <q-modal v-if="informacion" style="background-color: rgba(0,0,0,0.7);" v-model="informacion" :content-css="{width: '95vw', height: '95vw'}">
        <q-modal-layout>
          <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
            <div class="col-sm-6">
              <q-toolbar-title>
                Proyecto
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
            <div class="col-sm-12">
              <q-field icon="fas fa-user" icon-color="dark">
                <q-select readonly v-model="form.fields.cliente_id" inverted-color="dark" float-label="Cliente" :options="clientesOptions" filter/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right: 15px;">
            <div class="col-sm-6">
              <q-field icon="fas fa-pen-square" icon-color="dark">
                <q-select readonly v-model="form.fields.programa_id" inverted-color="dark" float-label="Programa" :options="programasOptions" filter @input="selectSubprograma()"/>
              </q-field>
            </div>
            <div class="col-sm-6">
              <q-field icon="inbox" icon-color="dark">
                <q-select readonly v-model="form.fields.subprograma_id" inverted-color="dark" float-label="Subprograma" :options="subprogramasOptions" filter/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right: 15px;">
            <div class="col-sm-12">
              <q-field icon="inbox" icon-color="dark">
                <q-input readonly upper-case v-model="form.fields.fuente_financiamiento" inverted-color="dark" float-label="Fuente de financiamiento" maxlength="100"/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right: 15px;">
            <div class="col-sm-4">
              <q-field icon="fas fa-dollar-sign" icon-color="dark">
                <q-input readonly v-model.lazy="form.fields.montovalidar" v-money="money" inverted-color="dark" float-label="Monto de la bolsa" suffix="MXN"></q-input>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="fas fa-dollar-sign" icon-color="dark">
                <q-input readonly v-model.lazy="form.fields.monto_licitado" v-money="money" inverted-color="dark" float-label="Monto licitado" suffix="MXN"></q-input>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="fas fa-dollar-sign" icon-color="dark">
                <q-input readonly v-model.lazy="form.fields.monto_adjudicado" v-money="money" inverted-color="dark" float-label="Monto adjudicado" suffix="MXN"></q-input>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-12">
              <q-collapsible v-model="open_contratos" icon="chrome_reader_mode" label="Contratos">
                <!-- aqui va el contenido -->
      <div class="layout-padding">
      <div class="row" v-if="viewsContrato.grid">
        <div class="col-sm-12">
          <div class="row justify-between">
            <div class="col-sm-6">
              <q-btn size="15px" icon="chrome_reader_mode" disable class="btn_categoria" label="CONTRATOS" />
            </div>
          </div>

          <div class="row q-mt-sm" style="margin-top:50px;">
            <div class="row" style="width:60vw;">
              <q-search hide-underline color="secondary" v-model="form.filter"/>
            </div>
            <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
              <q-table id="sticky-table"
                :data="contratos"
                :columns="form.columnsContrato"
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
                    <q-td key="num_contrato" :props="props">{{ props.row.num_contrato }}</q-td>
                    <q-td key="recurso" :props="props">{{ props.row.recurso }}</q-td>
                    <q-td key="licitacion" :props="props">{{ props.row.licitacion }}</q-td>
                    <q-td key="razon_social" :props="props">{{ props.row.razon_social }}</q-td>
                    <q-td key="fecha_inicio" :props="props"><label style="margin-right:14.2% !important;">{{ props.row.fecha_inicio }}</label></q-td>
                    <q-td key="fecha_fin" :props="props"><label style="margin-right:14.2% !important;">{{ props.row.fecha_fin }}</label></q-td>
                    <q-td key="documento_final" :props="props">{{ props.row.documento_final }}</q-td>
                    <q-td key="acciones" :props="props">
                      <q-btn small flat @click="editRowContrato(props.row)" color="blue-6" icon="visibility">
                        <q-tooltip>Ver información</q-tooltip>
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

      <!-- Modal editar CONTRATO -->
      <div class="row" v-if="viewsContrato.update">
        <div class="col-sm-12">
          <div class="row justify-between">
            <div class="col-sm-9">
              <h5 style="margin: 7px 0; font-weight: bold">CONTRATO</h5>
            </div>
            <div class="col-sm-3">
              <div class="row justify-end">
                <div class="col-sm-4" style="text-align: right">
                  <q-btn @click="setViewContrato('grid')" class="btn_regresar" icon="fa-arrow-left" />
                </div>
              </div>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-4">
              <q-field icon="fas fa-file" icon-color="dark">
                <q-input readonly v-model="form.fieldsContrato.num_contrato" type="number" inverted-color="dark" float-label="Número de contrato" numeric-keyboard-toggle/>
              </q-field>
            </div>
            <div class="col-sm-8">
              <q-field icon="fas fa-building" icon-color="dark">
                <q-select readonly v-model="form.fieldsContrato.empresa_id" inverted-color="dark" float-label="Empresa" :options="empresasOptions" filter/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-4">
              <q-field icon="date_range" icon-color="dark">
                <q-datetime readonly v-model="form.fieldsContrato.fecha_inicio" type="date" inverted-color="dark" float-label="Fecha inicio" align="center"></q-datetime>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="date_range" icon-color="dark">
                <q-datetime readonly v-model="form.fieldsContrato.fecha_fin" type="date" inverted-color="dark" float-label="Fecha fin" align="center"></q-datetime>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="fas fa-book" icon-color="dark">
                <q-select readonly v-model="form.fieldsContrato.licitacion_id" inverted-color="dark" float-label="Licitación" :options="licitacionesOptions" filter clearable/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" v-if="cliente !== ''">
            <div class="col-sm-12">
              <q-field icon="fas fa-user" icon-color="dark">
                <q-input readonly inverted-color="dark" float-label="Cliente" v-model="cliente"></q-input>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-8">
              <q-field icon="fas fa-user" icon-color="dark">
                <q-input readonly upper-case inverted-color="dark" float-label="Representante legal del contrato" v-model="form.fieldsContrato.rep_legal_contrato"></q-input>
              </q-field>
            </div>
            <div class="col-sm-4">
              <div class="icono_field">
                <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                  <q-input readonly v-model.lazy="form.fieldsContrato.monto_total_validar" v-money="money" inverted-color="dark" float-label="Monto" suffix="MXN"></q-input>
                </q-field>
              </div>
            </div>
          </div>

          <div v-if="Datofactura" class="row q-mt-sm" style="margin-top:50px;">
            <div class="row" style="width:60vw;">
              <q-search hide-underline color="secondary" v-model="form.filter"/>
            </div>
            <div  class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
              <q-table id="sticky-table"
                :data="facturas"
                :columns="form.fileColumnContrato"
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
                    <q-td key="id" :props="props">{{ props.row.factura_id }}</q-td>
                    <q-td key="nombre" :props="props">{{ props.row.name }}</q-td>
                    <q-td key="extension" :props="props">{{ props.row.doc_type }}</q-td>
                    <q-td key="fecha_factura" :props="props">{{ props.row.fecha_factura }}</q-td>
                    <q-td key="fecha_pago" :props="props">{{ props.row.fecha_pago }}</q-td>
                    <q-td key="fecha_vencimiento" :props="props">{{ props.row.fecha_vencimiento }}</q-td>
                    <q-td key="monto_factura" :props="props">${{ currencyFormat(props.row.monto_factura) }}</q-td>
                    <q-td key="folio_fiscal" :props="props">{{ props.row.folio_fiscal }}</q-td>
                    <q-td key="acciones" :props="props">
                      <q-btn small flat @click="abrirDocumento(props.row.factura_id,props.row.doc_type)" color="blue-6" icon="get_app">
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
        </div>
      </div> <!-- aqui deberia ir el modal de contratos pero se quitó -->
    </div>
    </q-collapsible>
    <q-collapsible v-model="open_presupuestos" icon="work" label="Presupuestos">
      <div class="layout-padding">
      <div class="row" v-if="viewsPresupuestos.grid">
        <div class="col-sm-12">
          <div class="row justify-between">
            <div class="col-sm-6">
              <q-btn size="15px" icon="work" disable class="btn_categoria" label="PRESUPUESTOS" />
            </div>
          </div>

          <div class="row q-mt-sm" style="margin-top:50px;">
            <div class="row" style="width:60vw;">
              <q-search hide-underline color="secondary" v-model="form.filter" />
            </div>
            <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
              <q-table id="sticky-table"
                :data="proyectos"
                :columns="form.columnsPresupuesto"
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
                    <q-td key="nombre_proyecto" :props="props" @click.native="clicFilaPresupuesto(props.row)" style="cursor: pointer;">{{ props.row.nombre_proyecto }}</q-td>
                    <q-td key="recurso" :props="props" @click.native="clicFilaPresupuesto(props.row)" style="cursor: pointer;">{{ props.row.recurso }}</q-td>
                    <q-td key="inicio" :props="props">{{ props.row.inicio }}</q-td>
                    <q-td key="fin" :props="props">{{ props.row.fin }}</q-td>
                    <q-td key="lider" :props="props">{{ props.row.nickname }}</q-td>
                    <q-td key="acciones" :props="props">
                      <q-btn small flat @click="editRowPresupuestos(props.row)" color="blue-6" icon="visibility">
                        <q-tooltip>Editar</q-tooltip>
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

      <!-- Modal editar PROYECTO -->
      <div class="row" v-if="viewsPresupuestos.update">
        <div class="col-sm-12">
          <div class="row justify-between">
            <div class="col-sm-9">
              <h5 style="margin: 7px 0; font-weight: bold">PRESUPUESTO</h5>
            </div>
            <div class="col-sm-3">
              <div class="row justify-end">
                <div class="col-sm-4" style="text-align: right">
                  <q-btn @click="setViewPresupuestos('grid')" class="btn_regresar" icon="fa-arrow-left" />
                </div>
              </div>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div style="height:53px;width:50%;margin-top:5px;">
              <q-field icon="fas fa-pen-square" icon-color="dark">
                <q-input upper-case v-model="form.fieldsPresupuesto.nombre_proyecto" inverted-color="dark" float-label="Nombre del presupuesto" :readonly="user_id === form.fieldsPresupuesto.lider_proyecto"/>
              </q-field>
            </div>
              <div style="height:53px;width:20%;margin-top:5px;">
              <q-field icon="date_range" icon-color="dark">
                <q-datetime v-model="form.fieldsPresupuesto.inicio" type="date" inverted-color="dark" float-label="Fecha inicio" align="center" :readonly="user_id === form.fieldsPresupuesto.lider_proyecto"></q-datetime>
              </q-field>
            </div>
            <div style="height:53px;width:20%;margin-top:5px;">
              <q-field icon="date_range" icon-color="dark">
                <q-datetime v-model="form.fieldsPresupuesto.fin" type="date" inverted-color="dark" float-label="Fecha término" align="center" :readonly="user_id === form.fieldsPresupuesto.lider_proyecto"></q-datetime>
              </q-field>
            </div>
            <div style="height:53px;width:10%;margin-top:5px;">
              <q-field icon="fas fa-sun" icon-color="dark">
                <q-input v-model="form.fieldsPresupuesto.dias" type="number" inverted-color="dark" float-label="Días" readonly ></q-input>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div style="height:53px;width:35%;">
              <div class="icono_field">
                <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                  <q-input v-model.lazy="form.fieldsPresupuesto.presupuestovalidar" readonly v-money="money" inverted-color="dark" float-label="Presupuesto inicial" suffix="MXN"></q-input>
                </q-field>
              </div>
            </div>
            <div style="height:53px;width:35%;">
              <div class="icono_field">
                <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                  <q-input v-model.lazy="form.fieldsPresupuesto.presupuestoActual" readonly v-money="money" inverted-color="dark" float-label="Presupuesto Actual" suffix="MXN"></q-input>
                </q-field>
              </div>
            </div>
            <div style="height:53px;width:30%;">
              <q-field icon="person" icon-color="dark">
                <q-input v-model="form.fieldsPresupuesto.lider_usuario" inverted-color="dark" float-label="Lider de presupuesto" readonly ></q-input>
              </q-field>
            </div>
          </div>

          <div>
          <div class="row q-mt-lg">
            <div class="col-sm-12">
              <q-collapsible v-model="open_colaboradores" icon="perm_identity" label="Colaboradores">
                <!-- aqui va el contenido -->
                <div class="row q-mt-lg" style="margin-top:30px;">
                  <div class="col-sm-12" style="padding-right:10px;">
                    <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                      <q-table id="sticky-table"
                        :data="form_colaboradores.data"
                        :columns="form_colaboradores.columnsColaboradoresLectura"
                        :selected.sync="form_colaboradores.selected"
                        :filter="form_colaboradores.filter"
                        color="positive"
                        title=""
                        :dense="true"
                        :pagination.sync="form_colaboradores.pagination"
                        :loading="form_colaboradores.loading"
                        :rows-per-page-options="form_colaboradores.rowsOptions">
                        <template slot="body" slot-scope="props">
                          <q-tr :props="props">
                            <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                            <q-td key="perfil" :props="props">{{ props.row.perfil }}</q-td>
                          </q-tr>
                        </template>
                      </q-table>
                      <q-inner-loading :visible="form_colaboradores.loading">
                        <q-spinner-dots size="64px" color="primary" />
                      </q-inner-loading>
                    </div>
                  </div>
                </div>
              </q-collapsible>
            </div>
          </div>

          <div class="row q-mt-lg">
            <div class="col-sm-12">
              <q-collapsible v-model="open_solicitantes" icon="perm_identity" label="Solicitantes">
                <!-- aqui va el contenido -->
                <div class="row q-mt-lg" style="margin-top:30px;">
                  <div class="col-sm-12" style="padding-right:10px;">
                    <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                      <q-table id="sticky-table"
                        :data="form_solicitantes.data"
                        :columns="form_solicitantes.columnsSolicitantesLectura"
                        :selected.sync="form_solicitantes.selected"
                        :filter="form_solicitantes.filter"
                        color="positive"
                        title=""
                        :dense="true"
                        :pagination.sync="form_solicitantes.pagination"
                        :loading="form_solicitantes.loading"
                        :rows-per-page-options="form_solicitantes.rowsOptions">
                        <template slot="body" slot-scope="props">
                          <q-tr :props="props">
                            <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                            <q-td key="perfil" :props="props">{{ props.row.perfil }}</q-td>
                          </q-tr>
                        </template>
                      </q-table>
                      <q-inner-loading :visible="form_solicitantes.loading">
                        <q-spinner-dots size="64px" color="primary" />
                      </q-inner-loading>
                    </div>
                  </div>
                </div>
              </q-collapsible>
            </div>
          </div>

          <div class="row q-mt-lg">
            <div class="col-sm-12">
              <q-collapsible v-model="open_autorizadores" icon="perm_identity" label="Autorizadores">
                <!-- aqui va el contenido -->
                <div class="row q-mt-lg" style="margin-top:30px;">
                  <div class="col-sm-12">
                    <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                      <q-table id="sticky-table"
                        :data="form_autorizadores.data"
                        :columns="form_autorizadores.columnsAutorizadoresLectura"
                        :selected.sync="form_autorizadores.selected"
                        :filter="form_autorizadores.filter"
                        color="positive"
                        title=""
                        :dense="true"
                        :pagination.sync="form_autorizadores.pagination"
                        :loading="form_autorizadores.loading"
                        :rows-per-page-options="form_autorizadores.rowsOptions">
                        <template slot="body" slot-scope="props">
                          <q-tr :props="props">
                            <q-td key="orden" :props="props">{{ props.row.orden }}</q-td>
                            <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                            <q-td key="perfil" :props="props">{{ props.row.perfil }}</q-td>
                            <q-td key="alteracion" :props="props">{{ props.row.alteracion }}</q-td>
                          </q-tr>
                        </template>
                      </q-table>
                      <q-inner-loading :visible="form_autorizadores.loading">
                        <q-spinner-dots size="64px" color="primary" />
                      </q-inner-loading>
                    </div>
                  </div>
                </div>
              </q-collapsible>
            </div>
          </div>

          <div class="row q-mt-lg">
            <div class="col-sm-12">
              <q-collapsible v-model="open_responsables" icon="perm_identity" label="Responsable de pagos">
                <!-- aqui va el contenido -->
                <div class="row q-mt-lg" style="margin-top:30px;">
                  <div class="col-sm-12" style="padding-left:10px;">
                    <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                      <q-table id="sticky-table"
                        :data="form_responsables.data"
                        :columns="form_responsables.columnsResponsablesLectura"
                        :selected.sync="form_responsables.selected"
                        :filter="form_responsables.filter"
                        color="positive"
                        title=""
                        :dense="true"
                        :pagination.sync="form_responsables.pagination"
                        :loading="form_responsables.loading"
                        :rows-per-page-options="form_responsables.rowsOptions">
                        <template slot="body" slot-scope="props">
                          <q-tr :props="props">
                            <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                            <q-td key="perfil" :props="props">{{ props.row.perfil }}</q-td>
                          </q-tr>
                        </template>
                      </q-table>
                      <q-inner-loading :visible="form_responsables.loading">
                        <q-spinner-dots size="64px" color="primary" />
                      </q-inner-loading>
                    </div>
                  </div>
                </div>
              </q-collapsible>
            </div>
          </div>
        </div>

          <div class="row q-mt-lg subtitulos">
            DETALLE DE ACTIVIDADES
          </div>

          <div class="col-sm-12 q-mt-sm" id="sticky-arbol">
            <div class="q-table-container q-table-dense" style="margin-top:20px;">
              <div class="q-table-middle scroll">
                <table class="q-table">
                  <thead>
                    <tr style="text-align: center;">
                      <th>Nombre</th>
                      <th>Avance</th>
                      <th>Inicio</th>
                      <th>Fin</th>
                      <th>Costo</th>
                      <th>Fin real</th>
                      <th>EDT</th>
                      <th>Resumen</th>
                      <th>Duración</th>
                      <th>Duración restante</th>
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
                      <td data-th="Avance" style="text-align: center;">{{item.avance}}%</td>
                      <td data-th="Inicio" style="text-align: center;">{{item.inicio}} </td>
                      <td data-th="Fin" style="text-align: center;">{{item.fin}}</td>
                      <td data-th="Costo" style="text-align: right;">${{item.costoCopia}}</td>
                      <td data-th="Fin Real" style="text-align: center;">{{item.fin_real}} </td>
                      <td data-th="EDT" style="text-align: left;">{{item.indice}}</td>
                      <td data-th="Resumen" style="text-align: center;">{{item.resumen}}</td>
                      <td data-th="Duración" style="text-align: center;">{{item.duracion}}</td>
                      <td data-th="Duración restante" style="text-align: center;">{{item.duracion_restante}}</td>
                    </tr>
                  </tbody>
                </table>
                <q-inner-loading :visible="form_actividades.loading">
                  <q-spinner-dots size="64px" color="primary" />
                </q-inner-loading>
              </div>
            </div>
          </div>

        </div>
      </div>

        <!-- </div>
      </div> -->

      <q-modal no-backdrop-dismiss v-if="form_actividades.modal_editar" style="background-color: rgba(0,0,0,0.7);" v-model="form_actividades.modal_editar" :content-css="{minWidth: '80vw', minHeight: '680px'}">
        <q-modal-layout>
          <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
            <div class="col-sm-9">
              <q-toolbar-title>
                Edición de actividad
              </q-toolbar-title>
            </div>
            <div class="col-sm-3 pull-right">
              <q-btn
                flat
                round
                dense
                color="white"
                @click="form_actividades.modal_editar = false"
                icon="fas fa-times-circle"
              />
            </div>
          </q-toolbar>
          <div class="row q-mt-lg" style="margin-top:20px;">
            <div class="col-sm-4" style="margin:auto;" >
              <q-field icon="icono_prueba" icon-color="dark" helper="Porcentaje de avance">
                <q-knob
                  v-model="form_actividades.fieldsPresupuesto.avance"
                  size="100px"
                  style="font-size: 1rem; margin-left:30%;"
                  color="light-blue-10"
                  track-color="light-blue-12"
                  line-width="5px"
                  :min="0"
                  :max="100"
                  :step="1"
                  :readonly="parseInt(form_actividades.fieldsPresupuesto.children_cantidad)>0"
                >
                  {{ form_actividades.fieldsPresupuesto.avance }} <q-icon class="q-ml-xs" name="fas fa-percent" />
                </q-knob>
                <q-input clearable align="right" :decimals="0" numeric-keyboard-toggle v-model="form_actividades.fieldsPresupuesto.avance" type="number" suffix="%" :readonly="parseInt(form_actividades.fieldsPresupuesto.children_cantidad)>0"></q-input>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right:10px;">
            <div class="col-sm-12">
              <q-field icon="fas fa-chart-line" icon-color="dark" :error="$v.form_actividades.fieldsPresupuesto.nombre.$error" error-label="Ingrese el nombre de la actividad">
                <q-input upper-case v-model="form_actividades.fieldsPresupuesto.nombre" type="text" inverted color="dark" float-label="Nombre de la actividad" maxlength="100"></q-input>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right:10px;">
            <div class="col-sm-4">
              <q-field icon="fas fa-arrow-circle-down" icon-color="dark">
                <q-input upper-case v-model="form_actividades.fieldsPresupuesto.indice" type="text" inverted color="dark" float-label="EDT" readonly disable></q-input>
              </q-field>
            </div>
              <div class="col-sm-4">
              <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                <q-input readonly v-model.lazy="form_actividades.fieldsPresupuesto.presupuesto_inicial" v-money="money" inverted color="dark" float-label="Monto inicial" suffix="MXN"></q-input>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                <q-input readonly v-model.lazy="form_actividades.fieldsPresupuesto.costo" v-money="money" inverted color="dark" float-label="Monto actual" suffix="MXN"></q-input>
              </q-field>
            </div>
            <div class="col-sm-4" style="margin-top:5px;">
              <q-field icon="fas fa-file-alt" icon-color="dark" helper="Resumen">
                <q-toggle v-model="form_actividades.fieldsPresupuesto.resumen" color="green" checked-icon="fas fa-check" unchecked-icon="fas fa-times"/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right:10px;">
            <div class="col-sm-4">
              <q-field icon="date_range" icon-color="dark">
                <q-datetime v-model="form_actividades.fieldsPresupuesto.inicio" type="date" inverted color="dark" float-label="Inicio" clearable align="center"></q-datetime>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="date_range" icon-color="dark">
                <q-datetime v-model="form_actividades.fieldsPresupuesto.fin" type="date" inverted color="dark" float-label="Fin" clearable align="center"></q-datetime>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="date_range" icon-color="dark">
                <q-datetime v-model="form_actividades.fieldsPresupuesto.fin_real" type="date" inverted color="dark" float-label="Fin real" clearable align="center"></q-datetime>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right:10px;">
            <div class="col-sm-4">
              <q-field icon="fas fa-sun" icon-color="dark">
                <q-input v-model="form_actividades.fieldsPresupuesto.duracion" type="number" inverted color="dark" float-label="Duración" readonly numeric-keyboard-toggle></q-input>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="fas fa-sun" icon-color="dark">
                <q-input readonly v-model="form_actividades.fieldsPresupuesto.duracion_restante" type="number" inverted color="dark" float-label="Duración restante" numeric-keyboard-toggle></q-input>
              </q-field>
            </div>
          </div>
        </q-modal-layout>
      </q-modal>

    </div>
</q-collapsible>
<q-collapsible v-model="open_organismos" icon="folder" label="Organismos de apoyo">
</q-collapsible>
  </div>
</div>
        </q-modal-layout>
      </q-modal>
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { required, minValue, maxValue, helpers, minLength, maxLength } from 'vuelidate/lib/validators'
import moment from 'moment'
import {VMoney} from 'v-money'
import axios from 'axios'

const nombreRep = helpers.regex('nombreRep', /^[A-ZÁÉÍÓÚÑ]+(\s{1}[A-ZÁÉÍÓÚÑ]+)*$/)

export default {
  // components: {Money},
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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'COBRANZA'.toUpperCase() || propiedades[i].toUpperCase() === 'PLANEACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS/COMISIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'AUXILIAR TESORERIA'.toUpperCase()) {
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
      selectYear: [ { label: '' + (new Date().getFullYear() - 3), value: '' + (new Date().getFullYear() - 3) }, { label: '' + (new Date().getFullYear() - 2), value: '' + (new Date().getFullYear() - 2) }, { label: '' + (new Date().getFullYear() - 1), value: '' + (new Date().getFullYear() - 1) }, { label: '' + (new Date().getFullYear()), value: '' + (new Date().getFullYear()) }, { label: '' + (new Date().getFullYear() + 1), value: '' + (new Date().getFullYear() + 1) } ],
      adjudicacionOptions: [ { label: 'LICITACIÓN', value: 'LICITACIÓN' }, { label: 'ADJUDICACIÓN DIRECTA', value: 'ADJUDICACIÓN DIRECTA' }, { label: 'INVITACIÓN A 3', value: 'INVITACIÓN A 3' }, { label: 'VENTA DIRECTA', value: 'VENTA DIRECTA' } ],
      year: '' + (new Date().getFullYear()),
      busquedaOptions: [ { label: 'PROYECTO', value: 'PROYECTO' }, { label: 'PRESUPUESTO', value: 'PRESUPUESTO' } ],
      programageneralOptions: [ { label: 'ADMINISTRATIVOS', value: 'ADMINISTRATIVOS' }, { label: 'OPERATIVOS', value: 'OPERATIVOS' } ],
      modal_ver_contrato: false,
      show_file_contrato: false,
      tabPrincipal: 'contratos',
      tab: 'colaborador_pane',
      role: '',
      user_id: 0,
      loadingButton: false,
      informacion: false,
      objetoInformacion: null,
      open_contratos: false,
      open_presupuestos: false,
      open_organismos: false,
      open_perfiles: false,
      InformacionContrato: false,
      objetoInformacionContrato: null,
      licitacionesOptions: [],
      empresas2Options: [],
      contratosOptions: [],
      // presupuestos
      isExpanded: true,
      selectedRowID: {},
      itemId: null,
      actividades: [],
      open_colaboradores: false,
      open_solicitantes: false,
      open_autorizadores: false,
      open_responsables: false,
      alterar_presupuesto: false,
      objetoInformacionPresupuesto: null,
      informacionPresupuestos: false,
      data_projects: [],
      tiposFacturaOptions: [ { 'label': 'GDP', 'value': 'GDP' }, { 'label': 'Retail', 'value': 'retail' } ],
      tiposComprobanteOptions: [ { 'label': 'Ingreso', 'value': 'I' }, { 'label': 'Egreso', 'value': 'E' }, { 'label': 'Pago', 'value': 'P' } ],
      metodoPagoOptions: [ { 'label': 'Pago en una sola exhibición', 'value': 'PUE' }, { 'label': 'Pago en parcialidades', 'value': 'PPD' } ],
      tipoComision: [ { 'label': 'PORCENTAJE', 'value': 'PORCENTAJE' }, { 'label': 'CANTIDAD FIJA', 'value': 'CANTIDAD FIJA' }, { 'label': '--Seleccione--', 'value': 'all' } ],
      metodoPagoComisionOptions: [ { 'label': 'EFECTIVO', 'value': 'EFECTIVO' }, { 'label': 'TRANSFERENCIA', 'value': 'TRANSFERENCIA' }, { 'label': 'CHEQUE', 'value': 'CHEQUE' }, { 'label': '--Seleccione--', 'value': 'all' } ],
      ivaOptions: [ { 'label': 'SUBTOTAL', 'value': 'SUBTOTAL' }, { 'label': 'NETO', 'value': 'NETO' }, { 'label': '--Seleccione--', 'value': 'all' } ],
      aplicadoOptions: [ { 'label': 'AL CONTRATO', 'value': 'AL CONTRATO' }, { 'label': 'A LA UTILIDAD', 'value': 'A LA UTILIDAD' }, { 'label': '--Seleccione--', 'value': 'all' }, { 'label': 'AL PROJECT', 'value': 'AL PROJECT' } ],
      tiempoOptions: [ { 'label': 'FIRMA DE CONTRATO', 'value': 'FIRMA DE CONTRATO' }, { 'label': 'COBRO DE FACTURA', 'value': 'COBRO DE FACTURA' }, { 'label': '--Seleccione--', 'value': 'all' } ],
      asignadoComision: [ { 'label': 'ALIADO', 'value': 'ALIADO' }, { 'label': 'VENDEDOR', 'value': 'VENDEDOR' } ],
      money: {
        decimal: '.',
        thousands: ',',
        precision: 2,
        masked: false
      },
      // //////////////
      factura: false,
      viewsContrato: {
        grid: true,
        create: false,
        update: false
      },
      viewsPresupuestos: {
        grid: true,
        create: false,
        update: false
      },
      viewsOrganismos: {
        grid: true,
        create: false,
        update: false
      },
      viewsComisiones: {
        grid: true,
        create: false,
        update: false
      },
      views: {
        grid: true,
        create: false,
        update: false,
        info: false
      },
      form_presupuestos: {
        data_projects: [],
        data_contratos: [],
        data_comisiones: [],
        data_organismos: [],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form: {
        data_recursos: [],
        tipo_factura: '',
        file: {
          nombre: '',
          fecha_pago: '',
          fecha_factura: '',
          fecha_vencimiento: '',
          folio_fiscal: ''
        },
        retail: {
          nombre: '',
          fecha_pago: '',
          fecha_factura: '',
          fecha_vencimiento: '',
          folio_fiscal: ''
        },
        fieldsContrato: {
          id: 0,
          recurso_id: 0,
          empresa_id: 0,
          licitacion_id: 0,
          fecha_inicio: '',
          fecha_fin: '',
          monto_total: 0,
          monto_total_validar: 0,
          rep_legal_contrato: '',
          num_contrato: 0,
          //
          nombre: '',
          organismo_id: 0,
          porcentaje: 0,
          filter: '',
          documento_pdf: '',
          doc_type_pdf: '',
          nombre_pdf: '',
          observaciones: ''
        },
        fields: {
          id: 0,
          codigo: '',
          cliente_id: 0,
          subprograma_id: 0,
          programa_id: 0,
          fuente_financiamiento: '',
          monto: 0,
          montovalidar: 0,
          montovalidariva: 0,
          total_recursos: 0,
          monto_licitado: 0,
          monto_adjudicado: 0,
          rubro: '',
          nombre: '',
          aliado: '',
          aliado1: '',
          aliado2: '',
          aliado3: '',
          suma_contratos: 0,
          adjudicacion: '',
          sucursal_id: 0,
          tipo: ''
        },
        fieldsPresupuesto: {
          id: 0,
          nombre_proyecto: '',
          nombre_corto: '',
          recurso_id: 0,
          inicio: '',
          fin: '',
          dias: 0,
          presupuesto: 0,
          presupuesto_actual: 0,
          presupuestovalidar: 0,
          monto_bolsa: 0,
          monto_bolsa_iva: 0,
          lider_proyecto: '',
          lider_usuario: '',
          licitacion_id: 0,
          empresa_id: 0,
          status: 'ACTIVO',
          sucursal_id: 0
        },
        // data: [],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        columnsPresupuesto: [
          {name: 'nombre_proyecto', label: 'Nombre', field: 'nombre_proyecto', sortable: true, type: 'string', align: 'center'},
          {name: 'nombre_corto', label: 'Nombre corto', field: 'nombre_corto', sortable: true, type: 'string', align: 'center'},
          { name: 'recurso', label: 'Proyecto', field: 'recurso', sortable: true, type: 'string', align: 'center' },
          { name: 'inicio', label: 'Fecha de inicio', field: 'inicio', sortable: true, type: 'string', align: 'center' },
          { name: 'fin', label: 'Fecha de término', field: 'fin', sortable: true, type: 'string', align: 'center' },
          { name: 'lider', label: 'Lider', field: 'lider', sortable: true, type: 'string', align: 'center' },
          { name: 'status', label: 'Status', field: 'status', sortable: true, type: 'string', align: 'center' },
          { name: 'finalizado', label: 'Finalizado', field: 'finalizado', sortable: true, type: 'string', align: 'center' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'center' }
        ],
        columns: [
          { name: 'codigo', label: 'Código', field: 'codigo', sortable: true, type: 'string', align: 'center' },
          { name: 'nombre', label: 'Nombre', field: 'nombre', sortable: true, type: 'string', align: 'center' },
          { name: 'razon_social', label: 'Cliente', field: 'razon_social', sortable: true, type: 'string', align: 'center' },
          { name: 'programa', label: 'Programa', field: 'programa_nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'subprograma', label: 'Subprograma', field: 'subprograma_nombre', sortable: true, type: 'string', align: 'center' },
          { name: 'sucursal', label: 'Sucursal', field: 'sucursal_nombre', sortable: true, type: 'string', align: 'center' },
          { name: 'creador', label: 'Creador', field: 'creador', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha_creacion', label: 'Fecha creado', field: 'fecha_creacion', sortable: true, type: 'string', align: 'center' },
          { name: 'monto', label: 'Monto bolsa', field: 'monto', sortable: true, type: 'string', align: 'center' },
          { name: 'rubro', label: 'Rubro', field: 'rubro', sortable: true, type: 'string', align: 'center' },
          { name: 'year', label: 'Año', field: 'year', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        columnsContrato: [
          { name: 'num_contrato', label: 'Contrato', field: 'num_contrato', sortable: true, type: 'string', align: 'center' },
          { name: 'nombre', label: 'Nombre', field: 'nombre', sortable: true, type: 'string', align: 'center' },
          { name: 'monto_total', label: 'Importe', field: 'monto_total', sortable: true, type: 'string', align: 'center' },
          { name: 'recurso', label: 'Proyecto', field: 'recurso', sortable: true, type: 'string', align: 'center' },
          { name: 'licitacion', label: 'Licitación', field: 'licitacion', sortable: true, type: 'string', align: 'center' },
          { name: 'razon_social', label: 'Empresa', field: 'razon_social', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_inicio', label: 'Fecha firma', field: 'fecha_inicio', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_fin', label: 'Fecha fin', field: 'fecha_fin', sortable: true, type: 'string', align: 'center' },
          { name: 'documento_final', label: 'Documento final', field: 'documento_final', sortable: true, type: 'string', align: 'center' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        columnsContratoSoloLectura: [
          { name: 'num_contrato', label: 'Contrato', field: 'num_contrato', sortable: true, type: 'string', align: 'center' },
          { name: 'recurso', label: 'Proyecto', field: 'recurso', sortable: true, type: 'string', align: 'center' },
          { name: 'licitacion', label: 'Licitación', field: 'licitacion', sortable: true, type: 'string', align: 'center' },
          { name: 'razon_social', label: 'Empresa', field: 'razon_social', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_inicio', label: 'Fecha firma', field: 'fecha_inicio', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_fin', label: 'Fecha fin', field: 'fecha_fin', sortable: true, type: 'string', align: 'center' }
        ],
        fileColumnContrato: [
          { name: 'id', label: 'Factura', field: 'factura', sortable: true, type: 'string', align: 'center' },
          { name: 'nombre', label: 'Nombre Factura', field: 'nombre', sortable: true, type: 'string', align: 'center' },
          { name: 'extension', label: 'Extensión', field: 'extension', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_factura', label: 'Fecha de Factura', field: 'fecha_factura', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_pago', label: 'Fecha de pago', field: 'fecha_pago', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_vencimiento', label: 'Fecha de vencimiento', field: 'fecha_vencimiento', sortable: true, type: 'string', align: 'center' },
          { name: 'monto_factura', label: 'Monto', field: 'monto_factura', sortable: true, type: 'string', align: 'center' },
          { name: 'folio_fiscal', label: 'Folio fiscal', field: 'folio_fiscal', sortable: true, type: 'string', align: 'center' },
          { name: 'cancelada', label: 'Cancelada', field: 'cancelada', sortable: true, type: 'string', align: 'center' },
          { name: 'nota_credito', label: 'Nota crédito', field: 'nota_credito', sortable: true, type: 'string', align: 'center' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'center' }
        ],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        busqueda: 'PROYECTO',
        filter_presupuesto: '',
        sucursal: 0,
        presupuesto: '',
        loading: false
      },
      form_actividades: {
        loading: false,
        modal_editar: false,
        modal_crear: false,
        modal_documentos: false,
        fieldsPresupuesto: {
          id: 0,
          proyecto_id: 0,
          nombre: '',
          avance: 0,
          inicio: '',
          fin: '',
          costo: 0,
          costovalidar: 0,
          fin_real: '',
          indice: '',
          nivel: 0,
          resumen: false,
          duracion: 0,
          duracion_restante: 0,
          padre_id: 0,
          children_cantidad: 0,
          presupuesto_inicial: 0
        }
      },
      form_solicitantes: {
        solicitantesOptions: [],
        solicitantes_proyecto: false,
        editar_solicitantes_proyecto: false,
        columns: [
          {name: 'nombre', label: 'Usuario', field: 'nombre', sortable: true, type: 'string', align: 'left'},
          {name: 'perfil', label: 'Perfil', field: 'perfil', sortable: true, type: 'string', align: 'left'},
          { name: 'acciones', label: 'Acciones', field: 'acciones', type: 'string', align: 'right' }
        ],
        columnsSolicitantesLectura: [
          {name: 'nombre', label: 'Usuario', field: 'nombre', sortable: true, type: 'string', align: 'left'},
          {name: 'perfil', label: 'Perfil', field: 'perfil', sortable: true, type: 'string', align: 'right'}
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false,
        fieldsPresupuesto: {
          id: 0,
          proyecto_id: 0,
          usuario_id: 0,
          nombre: '',
          perfil: ''
        }
      },
      form_autorizadores: {
        autorizadoresOptions: [],
        autorizadores_proyecto: false,
        editar_autorizadores_proyecto: false,
        selectNivel: [ { 'label': '1', 'value': 1 }, { 'label': '2', 'value': 2 }, { 'label': '3', 'value': 3 }, { 'label': '4', 'value': 4 }, { 'label': '5', 'value': 5 }, { 'label': '6', 'value': 6 }, { 'label': '7', 'value': 7 }, { 'label': '8', 'value': 8 }, { 'label': '9', 'value': 9 }, { 'label': '10', 'value': 10 } ],
        columns: [
          {name: 'orden', label: 'Nivel', field: 'orden', sortable: true, type: 'string', align: 'left'},
          {name: 'nombre', label: 'Usuario', field: 'nombre', sortable: true, type: 'string', align: 'left'},
          {name: 'perfil', label: 'Perfil', field: 'perfil', sortable: true, type: 'string', align: 'left'},
          {name: 'alteracion', label: 'Ajustar presupuesto', field: 'alteracion', sortable: true, type: 'string', align: 'left'},
          { name: 'acciones', label: 'Acciones', field: 'acciones', type: 'string', align: 'right' }
        ],
        columnsAutorizadoresLectura: [
          {name: 'orden', label: 'Nivel', field: 'orden', sortable: true, type: 'string', align: 'left'},
          {name: 'nombre', label: 'Usuario', field: 'nombre', sortable: true, type: 'string', align: 'left'},
          {name: 'perfil', label: 'Perfil', field: 'perfil', sortable: true, type: 'string', align: 'left'},
          {name: 'alteracion', label: 'Ajustar presupuesto', field: 'alteracion', sortable: true, type: 'string', align: 'right'}
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false,
        fieldsPresupuesto: {
          id: 0,
          proyecto_id: 0,
          usuario_id: 0,
          nombre: '',
          perfil: '',
          orden: 0,
          alteracion: 'NO'
        }
      },
      form_responsables: {
        responsablesOptions: [],
        responsables_proyecto: false,
        editar_responsables_proyecto: false,
        columns: [
          {name: 'nombre', label: 'Usuario', field: 'nombre', sortable: true, type: 'string', align: 'left'},
          {name: 'perfil', label: 'Perfil', field: 'perfil', sortable: true, type: 'string', align: 'left'},
          { name: 'acciones', label: 'Acciones', field: 'acciones', type: 'string', align: 'right' }
        ],
        columnsResponsablesLectura: [
          {name: 'nombre', label: 'Usuario', field: 'nombre', sortable: true, type: 'string', align: 'left'},
          {name: 'perfil', label: 'Perfil', field: 'perfil', sortable: true, type: 'string', align: 'right'}
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false,
        fieldsPresupuesto: {
          id: 0,
          proyecto_id: 0,
          usuario_id: 0,
          nombre: '',
          perfil: ''
        }
      },
      form_colaboradores: {
        colaboradoresOptions: [],
        colaboradores_proyecto: false,
        editar_colaboradores_proyecto: false,
        columns: [
          {name: 'nombre', label: 'Usuario', field: 'nombre', sortable: true, type: 'string', align: 'left'},
          {name: 'perfil', label: 'Perfil', field: 'perfil', sortable: true, type: 'string', align: 'left'},
          { name: 'acciones', label: 'Acciones', field: 'acciones', type: 'string', align: 'right' }
        ],
        columnsColaboradoresLectura: [
          {name: 'nombre', label: 'Usuario', field: 'nombre', sortable: true, type: 'string', align: 'left'},
          {name: 'perfil', label: 'Perfil', field: 'perfil', sortable: true, type: 'string', align: 'right'}
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false,
        fieldsPresupuesto: {
          id: 0,
          proyecto_id: 0,
          usuario_id: 0,
          nombre: '',
          perfil: ''
        }
      },
      form_archivos: {
        columns: [
          {name: 'name', label: 'Archivo', field: 'name', sortable: true, type: 'string', align: 'left'},
          {name: 'acciones', label: 'Acciones', field: 'acciones', type: 'string', align: 'right'}
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false,
        fields: {
          id: 0,
          name: '',
          descripcion: '',
          usuario_destino: 0
        }
      },
      form_organismos: {
        columns: [
          {name: 'nombre', label: 'Nombre', field: 'nombre', sortable: true, type: 'string', align: 'left'},
          {name: 'acciones', label: 'Acciones', field: 'acciones', type: 'string', align: 'right'}
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false,
        fields: {
          id: 0,
          nombre: ''
        }
      },
      form_comisiones: {
        columns: [
          {name: 'asignado', label: 'Asignado', field: 'asignado', sortable: true, type: 'string', align: 'left'},
          {name: 'aliado', label: 'Aliado/Vendedor', field: 'aliado', sortable: true, type: 'string', align: 'left'},
          {name: 'tipo', label: 'Tipo', field: 'tipo', sortable: true, type: 'string', align: 'left'},
          {name: 'cantidad_com', label: 'Cantidad / porcentaje', field: 'cantidad_com', sortable: true, type: 'string', align: 'right'},
          {name: 'contrato', label: 'Contrato', field: 'contrato', sortable: true, type: 'string', align: 'right'},
          {name: 'monto_total_comision', label: 'Total comisión', field: 'monto_total_comision', sortable: true, type: 'string', align: 'right'},
          {name: 'nombre_proyecto', label: 'Proyecto', field: 'nombre_proyecto', sortable: true, type: 'string', align: 'left'},
          {name: 'documentos', label: 'Archivos', field: 'documentos', sortable: true, type: 'string', align: 'left'},
          {name: 'acciones', label: 'Acciones', field: 'acciones', type: 'string', align: 'right'}
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false,
        monto_bolsa: 0,
        monto_base: 0,
        monto_comision: 0,
        fields: {
          id: 0,
          proyecto_id: 0,
          aliado_id: 0,
          tipo: '',
          porcentaje: 0.00,
          monto: 0.00,
          monto_base: 0.00,
          metodo_pago: '',
          contrato_id: 0,
          iva: 'si',
          aplicado: '',
          tiempo_pago: '',
          observaciones: '',
          vendedor_id: 0,
          asignado: 'ALIADO'
        }
      },
      form_perfiles: {
        perfilesOptions: [],
        perfiles_proyecto: false,
        columns: [
          {name: 'nombre', label: 'Nombre', field: 'nombre', sortable: true, type: 'string', align: 'left'},
          {name: 'participacion', label: 'Participación', field: 'participacion', sortable: true, type: 'string', align: 'left'},
          {name: 'acciones', label: 'Acciones', field: 'acciones', type: 'string', align: 'right'}
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false,
        data: [],
        fieldsPresupuesto: {
          id: 0,
          proyecto_id: 0,
          perfil_id: 0,
          participacion: ''
        }
      },
      modal: {
        x: 0,
        y: 0,
        transition: null
      },
      totalContratos: 0,
      registroContrato: null
    }
  },
  directives: {money: VMoney},
  computed: {
    ...mapGetters({
      programasOptions: 'vnt/programa/selectOptions',
      clientesOptions: 'ventas/clientes/selectOptions',
      recursos: 'vnt/recurso/recursos',
      recursosOptions: 'vnt/recurso/selectOtherOptions',
      empresasOptions: 'vnt/empresa/selectOptions',
      contratos: 'vnt/contrato/contratos',
      facturas: 'vnt/contratoFactura/facturas',
      usuariosOptions: 'sys/users/selectOptionsName',
      proyectos: 'pmo/proyecto/proyectos',
      organismos: 'vnt/organismo/organismos',
      comisiones: 'com/comisiones/comisiones',
      organismosOptions: 'vnt/organismo/selectOptions',
      formaPagoOptions: 'vnt/contratoFactura/selectPaymentFormsOptions',
      vendedoresOptions: 'crm/vendedores/selectOptions',
      sucursalesOptions: 'pmo/pmosucursales/selectOptions'
    }),
    subprogramasOptions () {
      let subprogramas = this.$store.getters['vnt/subprograma/selectOtherOptions'].filter(p => p.programa_id === this.form.fields.programa_id)
      subprogramas.sort((a, b) => {
        return (a.label < b.label) ? -1 : (a.label > b.label) ? 1 : 0
      })
      return subprogramas
    },
    arrayTreeObj () {
      let newObj = []
      this.recursive(this.actividades, newObj, 0, this.itemId, this.isExpanded)
      return newObj
    },
    cliente () {
      let valor = ''
      if (parseInt(this.form.fieldsContrato.recurso_id) > 0) {
        this.recursosOptions.forEach((element) => {
          if (parseInt(element.value) === parseInt(this.form.fieldsContrato.recurso_id)) {
            valor = element.cliente
          }
        })
      }
      return valor
    },
    Datofactura () {
      return this.form.fieldsContrato.id > 0
    },
    aliadosOptions () {
      let aliados = this.$store.getters['com/aliados/selectOptions']
      aliados.push({label: '---Seleccione---', value: 0})
      return aliados
    }
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
    informacionPresupuestos (newValue, old) {
      if (newValue === false) {
        this.objetoInformacionPresupuesto = null
      }
    },
    alterar_presupuesto (newValue, old) {
      if (newValue === true) {
        this.form_autorizadores.fieldsPresupuesto.alteracion = 'SI'
      } else {
        this.form_autorizadores.fieldsPresupuesto.alteracion = 'NO'
      }
    },
    open_autorizadores (newValue, old) {
      if (newValue === true) {
        this.cargarAutorizadores()
      }
    },
    open_perfiles (newValue, old) {
      if (newValue === true) {
        this.getPerfiles()
      }
    },
    'form_actividades.fieldsPresupuesto.avance' (newValue, old) {
      if (newValue === null) {
        this.form_actividades.fieldsPresupuesto.avance = 0
      }
    },
    'form.fieldsPresupuesto.inicio' (newValue, old) {
      if (this.form.fieldsPresupuesto.fin !== '' && newValue !== '') {
        if (this.validarFechasPresupuestos(this.form.fieldsPresupuesto.inicio, this.form.fieldsPresupuesto.fin)) {
          this.form.fieldsPresupuesto.dias = this.getDiasLaborables(this.form.fieldsPresupuesto.inicio, this.form.fieldsPresupuesto.fin)
        } else {
          this.form.fieldsPresupuesto.dias = 0
          this.mensajeNotify('Revisa las fechas', 3000, 'negative', 'fas fa-exclamation-triangle', 'La fecha de inicio no debe ser mayor a la fecha de término.', 'top-right')
        }
      }
    },
    'form.fieldsPresupuesto.fin' (newValue, old) {
      if (this.form.fieldsPresupuesto.inicio !== '' && newValue !== '') {
        if (this.validarFechasPresupuestos(this.form.fieldsPresupuesto.inicio, this.form.fieldsPresupuesto.fin)) {
          this.form.fieldsPresupuesto.dias = this.getDiasLaborables(this.form.fieldsPresupuesto.inicio, this.form.fieldsPresupuesto.fin)
        } else {
          this.form.fieldsPresupuesto.dias = 0
          this.mensajeNotify('Revisa las fechas', 3000, 'negative', 'fas fa-exclamation-triangle', 'La fecha de inicio no debe ser mayor a la fecha de término.', 'top-right')
        }
      }
    },
    'form_actividades.fieldsPresupuesto.inicio' (newValue, old) {
      if (newValue === null) {
        newValue = ''
        this.form_actividades.fieldsPresupuesto.inicio = ''
        this.form_actividades.fieldsPresupuesto.duracion = 0
      }
      if (this.form_actividades.fieldsPresupuesto.fin !== '' && newValue !== '') {
        if (this.validarFechasPresupuestos(this.form_actividades.fieldsPresupuesto.inicio, this.form_actividades.fieldsPresupuesto.fin)) {
          let diasLab = this.getDiasLaborables(this.form_actividades.fieldsPresupuesto.inicio, this.form_actividades.fieldsPresupuesto.fin)
          this.form_actividades.fieldsPresupuesto.duracion = diasLab
        } else {
          this.form_actividades.fieldsPresupuesto.duracion = 0
          this.mensajeNotify('Revisa las fechas', 3000, 'negative', 'fas fa-exclamation-triangle', 'La fecha de inicio no debe ser mayor a la fecha fin.', 'top-right')
        }
      }
    },
    'form_actividades.fieldsPresupuesto.fin' (newValue, old) {
      if (newValue === null) {
        newValue = ''
        this.form_actividades.fieldsPresupuesto.fin = ''
        this.form_actividades.fieldsPresupuesto.duracion = 0
      }
      if (this.form_actividades.fieldsPresupuesto.inicio !== '' && newValue !== '') {
        if (this.validarFechasPresupuestos(this.form_actividades.fieldsPresupuesto.inicio, this.form_actividades.fieldsPresupuesto.fin)) {
          let diasLab = this.getDiasLaborables(this.form_actividades.fieldsPresupuesto.inicio, this.form_actividades.fieldsPresupuesto.fin)
          this.form_actividades.fieldsPresupuesto.duracion = diasLab
        } else {
          this.form_actividades.fieldsPresupuesto.duracion = 0
          this.mensajeNotify('Revisa las fechas', 3000, 'negative', 'fas fa-exclamation-triangle', 'La fecha de inicio no debe ser mayor a la fecha fin.', 'top-right')
        }
      }
    },
    check_perfiles (newValue, old) {
      if (newValue === true) {
        this.form_actividades.actividades_proyecto = true
      } else {
        this.form_actividades.actividades_proyecto = false
      }
    },
    async 'tabPrincipal' (newValue, old) {
      if (newValue === 'projects') {
        this.cargarDataProject()
      }
      if (newValue === 'comisiones') {
        await this.getContratosOpt()
        await this.getAliados()
        await this.cargarDataComisiones()
        await this.getVendedores()
      }
      if (newValue === 'organismos') {
        await this.cargarDataOrganismos()
      }
      if (newValue === 'contratos') {
        this.modal_ver_contrato = false
        await this.cargarDataContratos()
      }
    }
  },
  methods: {
    ...mapActions({
      getUser: 'user/refresh',
      getRecursos: 'vnt/recurso/refresh',
      getRecursosByYear: 'vnt/recurso/getByYear',
      saveRecurso: 'vnt/recurso/save',
      updateRecurso: 'vnt/recurso/update',
      deleteRecurso: 'vnt/recurso/delete',
      getSubprogramas: 'vnt/subprograma/refresh',
      getProgramas: 'vnt/programa/refresh',
      getClientes: 'ventas/clientes/refresh',
      getContratos: 'vnt/contrato/refresh',
      getContratosbyID: 'vnt/contrato/getByID',
      saveContrato: 'vnt/contrato/save',
      updateContrato: 'vnt/contrato/update',
      deleteContrato: 'vnt/contrato/delete',
      getEmpresas: 'vnt/empresa/refresh',
      getLicitacionesByRecurso: 'lic/licitacion/getByRecurso',
      getFacturas: 'vnt/contratoFactura/getFacturas',
      deleteDocFacturas: 'vnt/contratoFactura/delete',
      // presupuestos
      getProyectos: 'pmo/proyecto/refresh',
      getProyectosbyID: 'pmo/proyecto/getByID',
      saveProyecto: 'pmo/proyecto/save',
      updateProyecto: 'pmo/proyecto/update',
      updateProyectoStatus: 'pmo/proyecto/updateStatus',
      updateFinalizado: 'pmo/proyecto/updateFinalizado',
      deleteProyecto: 'pmo/proyecto/delete',
      getUsuarios: 'sys/users/refresh',

      getActividadesByProyecto: 'pmo/carga/getByProyecto',
      getSolicitantesByProyecto: 'pmo/solicitantes/getByProyecto',
      getAutorizadoresByProyecto: 'pmo/autorizadores/getByProyecto',
      getResponsablesByProyecto: 'pmo/responsable_pagos/getByProyecto',
      getColaboradoresByProyecto: 'pmo/colaboradores/getByProyecto',
      saveActividad: 'pmo/carga/save',
      updateActividad: 'pmo/carga/update',
      deleteActividad: 'pmo/carga/delete',
      saveSolicitante: 'pmo/solicitantes/save',
      deleteSolicitante: 'pmo/solicitantes/delete',
      saveAutorizador: 'pmo/autorizadores/save',
      deleteAutorizador: 'pmo/autorizadores/delete',
      saveResponsable: 'pmo/responsable_pagos/save',
      deleteResponsable: 'pmo/responsable_pagos/delete',
      saveColaborador: 'pmo/colaboradores/save',
      deleteColaborador: 'pmo/colaboradores/delete',
      updateColaborador: 'pmo/colaboradores/update',
      updateSolicitante: 'pmo/solicitantes/update',
      updateAutorizador: 'pmo/autorizadores/update',
      updateResponsable: 'pmo/responsable_pagos/update',
      deleteFormatoAct: 'pmo/carga/deleteFormatoActividad',
      getEmpresaConcursante: 'lic/licitacion/getEmpresaConcursanteByIdLicitacion',
      saveOrganismo: 'vnt/organismo/save',
      updateOrganismo: 'vnt/organismo/update',
      getOrganismos: 'vnt/organismo/refresh',
      getOrganismosbyID: 'vnt/organismo/getByProyecto',
      deleteOrganismo: 'vnt/organismo/delete',
      eliminarDocumento: 'vnt/contrato/deleteFile',
      getPerfilesByProject: 'per/proyectos_perfiles/getByProject',
      savePerfil: 'per/proyectos_perfiles/save',
      getAllPerfiles: 'per/perfiles/refresh',
      deletePerfil: 'per/proyectos_perfiles/delete',
      getFormasDePago: 'vnt/contratoFactura/getFormasDePago',
      saveComision: 'com/comisiones/save',
      updateComision: 'com/comisiones/update',
      getComisionesbyID: 'com/comisiones/getByProyecto',
      deleteComision: 'com/comisiones/delete',
      getAliados: 'com/aliados/refresh',
      getComisiones: 'com/comisiones/refresh',
      getContratosOptions: 'vnt/contrato/getOptions',
      cancelarFactura: 'vnt/contratoFactura/cancelarFactura',
      actualizarFactura: 'vnt/contratoFactura/actualizarFactura',
      getVendedores: 'crm/vendedores/refresh',
      deleteActividadSingle: 'pmo/carga/delete_single',
      updateFinalizadoActividad: 'pmo/carga/updateFinalizado',
      updateVisibleActividad: 'pmo/carga/updateVisible',
      deleteDocumentoComision: 'com/documentos_comisiones/deleteFormato',
      getSucursales: 'pmo/pmosucursales/refresh'
    }),
    async filterByYear () {
      this.form.loading = true
      this.form.presupuesto = 'all'
      if (this.form.filter_presupuesto !== '') {
        this.form.presupuesto = this.form.filter_presupuesto
      }
      await this.getRecursosByYear({year: this.year, presupuesto: this.form.presupuesto, sucursal: this.form.sucursal}).then(({data}) => {
        if (data.recursos.length > 0) {
          this.form.data_recursos = data.recursos
        } else {
          this.form.data_recursos = []
        }
      }).catch(error => {
        console.error(error)
      })
      this.form.loading = false
    },
    async getRole () {
      await this.getUser().then(({data}) => {
        this.role = data.role[0]
        this.user_id = data.user.id
        console.log(data.user.id)
      }).catch(error => {
        console.log(error)
      })
    },
    async returnToView (value, view) {
      if (value === 'contrato') {
        // await this.getContratosbyID({id: this.form.fields.id})
        this.modal_ver_contrato = false
        this.setViewContrato(view)
        await this.cargarDataContratos()
        await this.getContratosOpt()
      }
      if (value === 'presupuesto') {
        this.setViewPresupuestos(view)
        await this.cargarDataProject()
      }
      if (value === 'organismo') {
        this.setViewOrganismos(view)
        await this.cargarDataOrganismos()
      }
      if (value === 'comision') {
        this.setViewComisiones(view)
        await this.cargarDataComisiones()
      }
    },
    recursive (obj, newObj, level, itemId, isExpend) {
      let vm = this
      obj.forEach(function (o) {
        if (o.children && o.children.length !== 0) {
          o.level = level
          newObj.push(o)
          if (o.id === itemId) {
            o.expend = isExpend
          }
          if (o.expend === true) {
            vm.recursive(o.children, newObj, o.level + 1, itemId, isExpend)
          }
        } else {
          o.level = level
          newObj.push(o)
          return false
        }
      })
    },
    iconName (item) {
      if (item.expend === true) {
        return 'fas fa-minus-circle'
      }

      if (item.children && item.children.length > 0) {
        return 'fas fa-plus-circle'
      }

      return 'fas fa-check'
    },
    toggle (item, index) {
      let vm = this
      vm.itemId = item.id
      // show  sub items after click on + (more)
      if (item.expend === undefined && item.children !== undefined) {
        if (item.children.length !== 0) {
          vm.recursive(item.children, [], item.level + 1, item.id, true)
        }
      }

      if (item.expend === true && item.children !== undefined) {
        item.children.forEach(function (o) {
          o.expend = undefined
        })

        // this.item.expend = undefined
        vm.$set(item, 'expend', undefined)
        vm.itemId = null
      }
    },
    setPadding (item) {
      return `padding-left: ${item.level * 30}px;`
    },
    async loadAll () {
      this.form.busqueda = 'PROYECTO'
      this.form.loading = true
      await this.getRole()
      await this.getProgramas()
      await this.getSubprogramas()
      await this.getClientes()
      await this.getRecursos()
      await this.getEmpresas()
      await this.getSucursales()
      this.form.loading = false
      await this.getRecursos()
      await this.getUsuarios()
      await this.filterByYear()
      if (this.$route.params.proyecto > 0 && this.$route.params.project > 0) {
        await this.proyectosss(this.$route.params.proyecto, this.$route.params.project)
        // await this.projects(this.$route.params.project)
      }
    },
    async proyectosss (id, id2) {
      await axios.get('/recursos/getBy/' + id).then(({data}) => {
        this.editRow2P(data.recursos[0], id2)
        // this.projects(id2)
      }).catch(error => {
        console.error(error)
      })
    },
    async projects (id) {
      await axios.get('/proyectos/getBy2/' + id).then(({data}) => {
        this.open_presupuestos = true
        this.editRowPresupuestos(data.proyectos[0])
      }).catch(error => {
        console.error(error)
      })
    },
    setViewPresupuestos (view) {
      this.viewsPresupuestos.grid = false
      this.viewsPresupuestos.create = false
      this.viewsPresupuestos.update = false
      this.viewsPresupuestos[view] = true
    },
    setViewOrganismos (view) {
      this.viewsOrganismos.grid = false
      this.viewsOrganismos.create = false
      this.viewsOrganismos.update = false
      this.viewsOrganismos[view] = true
    },
    setViewComisiones (view) {
      this.viewsComisiones.grid = false
      this.viewsComisiones.create = false
      this.viewsComisiones.update = false
      this.viewsComisiones[view] = true
    },
    currencyFormat (num) {
      return Number.parseFloat(num).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
    },
    validarFechasPresupuestos (fechaInicio, fechaFin) {
      if (moment(String(fechaInicio)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss') <= moment(String(fechaFin)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')) {
        return true
      }
      return false
    },
    async cargarFacturas () {
      await this.getFacturas({factura_id: this.form.fieldsContrato.id}).then(({data}) => {
        this.totalContratos = data.total
      }).catch(error => {
        console.error(error)
      })
      await this.getFormasDePago().then(({data}) => {
      }).catch(error => {
        console.error(error)
      })
    },
    async cargarDataProject () {
      this.form_presupuestos.data_projects = []
      await this.getProyectosbyID({id: this.form.fields.id, year: this.year}).then(({data}) => {
        if (data.result === 'success') {
          this.form_presupuestos.data_projects = data.proyectos
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async cargarDataContratos () {
      this.form_presupuestos.data_contratos = []
      await this.getContratosbyID({id: this.form.fields.id}).then(({data}) => {
        if (data.result === 'success') {
          this.form_presupuestos.data_contratos = data.contratos
          this.form.fields.suma_contratos = data.monto_contratos
          // console.log(this.form_presupuestos.data_contratos)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async cargarDataOrganismos () {
      this.form_presupuestos.data_organismos = []
      await this.getOrganismosbyID({id: this.form.fields.id}).then(({data}) => {
        if (data.result === 'success') {
          this.form_presupuestos.data_organismos = data.organismos
          // console.log(this.form_presupuestos.data_organismos)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async cargarDataComisiones () {
      this.form_presupuestos.data_comisiones = []
      await this.getComisionesbyID({proyecto_id: this.form.fields.id}).then(({data}) => {
        if (data.result === 'success') {
          this.form_presupuestos.data_comisiones = data.comisiones
          // console.log(this.form_presupuestos.data_comisiones)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    setViewContrato (view) {
      this.viewsContrato.grid = false
      this.viewsContrato.create = false
      this.viewsContrato.update = false
      if (view !== 'false') {
        this.viewsContrato[view] = true
      }
    },
    setView (view) {
      this.modal_ver_contrato = false
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    async cargarLicitaciones () {
      this.licitacionesOptions = []
      this.form.fieldsContrato.licitacion_id = 0
      await this.getLicitacionesByRecurso({recurso_id: this.form.fieldsContrato.recurso_id}).then(({data}) => {
        this.licitacionesOptions.push({label: '---Ninguno---', value: 0})
        if (data.licitaciones.length > 0) {
          data.licitaciones.forEach(licitacion => {
            this.licitacionesOptions.push({label: (licitacion.folio + ' - ' + licitacion.titulo), value: licitacion.id})
          })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    saveContratos () {
      this.form.fieldsContrato.monto_total = this.evaluaMonto(this.form.fieldsContrato.monto_total_validar)
      this.$v.form.fieldsContrato.$touch()
      if (!this.$v.form.fieldsContrato.$error) {
        if (this.validarFechas(this.form.fieldsContrato.fecha_inicio, this.form.fieldsContrato.fecha_fin)) {
          this.loadingButton = true
          let params = this.form.fieldsContrato
          params.year = this.year
          this.saveContrato(params).then(({data}) => {
            this.loadingButton = false
            this.$showMessage(data.message.title, data.message.content)
            if (data.result === 'success') {
              this.form.fieldsContrato.id = data.message.id
              this.cargarFacturas()
              this.cargarDataContratos()
              // console.log(this.form.fields.id)
              this.setViewContrato('create')
            }
          }).catch(error => {
            console.error(error)
          })
        } else {
          this.mensajeNotify('Revisa las fechas', 3000, 'negative', 'fas fa-exclamation-triangle', 'La fecha de inicio no debe ser mayor a la fecha fin.', 'top-right')
        }
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    updateContratosGrid () {
      this.form.fieldsContrato.monto_total = this.evaluaMonto(this.form.fieldsContrato.monto_total_validar)
      this.$v.form.fieldsContrato.$touch()
      if (!this.$v.form.fieldsContrato.$error) {
        if (this.validarFechas(this.form.fieldsContrato.fecha_inicio, this.form.fieldsContrato.fecha_fin)) {
          this.loadingButton = true
          let params = this.form.fieldsContrato
          params.organismo_id = this.form.fieldsContrato.organismo_id
          this.updateContrato(params).then(({data}) => {
            this.loadingButton = false
            this.$showMessage(data.message.title, data.message.content)
            if (data.result === 'success') {
              this.returnToView('contrato', 'grid')
            }
          }).catch(error => {
            console.error(error)
          })
        } else {
          this.mensajeNotify('Revisa las fechas', 3000, 'negative', 'fas fa-exclamation-triangle', 'La fecha de inicio no debe ser mayor a la fecha fin.', 'top-right')
        }
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    updateContratos () {
      this.form.fieldsContrato.monto_total = this.evaluaMonto(this.form.fieldsContrato.monto_total_validar)
      this.$v.form.fieldsContrato.$touch()
      if (!this.$v.form.fieldsContrato.$error) {
        if (this.validarFechas(this.form.fieldsContrato.fecha_inicio, this.form.fieldsContrato.fecha_fin)) {
          this.loadingButton = true
          let params = this.form.fieldsContrato
          params.organismo_id = this.form.fieldsContrato.organismo_id
          this.updateContrato(params).then(({data}) => {
            this.loadingButton = false
            this.$showMessage(data.message.title, data.message.content)
            if (data.result === 'success') {
              this.setViewContrato('update')
              this.cargarDataContratos()
              // console.log(this.form.fields.id)
            }
          }).catch(error => {
            console.error(error)
          })
        } else {
          this.mensajeNotify('Revisa las fechas', 3000, 'negative', 'fas fa-exclamation-triangle', 'La fecha de inicio no debe ser mayor a la fecha fin.', 'top-right')
        }
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    async editRowContrato (row) {
      this.licitacionesOptions = []
      let contrato = { ...row }
      this.form.fieldsContrato.id = contrato.id
      this.form.fieldsContrato.recurso_id = contrato.recurso_id
      this.form.fieldsContrato.empresa_id = contrato.empresa_id
      await this.cargarLicitaciones()
      if (contrato.licitacion_id === null) {
        this.form.fieldsContrato.licitacion_id = 0
      } else {
        this.form.fieldsContrato.licitacion_id = contrato.licitacion_id
      }
      if (contrato.fecha_inicio === null) {
        this.form.fieldsContrato.fecha_inicio = ''
      } else {
        this.form.fieldsContrato.fecha_inicio = moment(String(contrato.fecha_inicio)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      }
      this.form.fieldsContrato.num_contrato = contrato.num_contrato
      if (contrato.fecha_fin === null) {
        this.form.fieldsContrato.fecha_fin = ''
      } else {
        this.form.fieldsContrato.fecha_fin = moment(String(contrato.fecha_fin)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      }
      this.form.fieldsContrato.monto_total = contrato.monto_total
      this.form.fieldsContrato.monto_total_validar = contrato.monto_total
      this.form.fieldsContrato.rep_legal_contrato = contrato.rep_legal_contrato
      if (contrato.nombre === null) {
        this.form.fieldsContrato.nombre = ''
      } else {
        this.form.fieldsContrato.nombre = contrato.nombre
      }
      if (contrato.organismo_id === null) {
        this.form.fieldsContrato.organismo_id = 0
      } else {
        this.form.fieldsContrato.organismo_id = contrato.organismo_id
      }
      this.form.fieldsContrato.porcentaje = contrato.porcentaje
      this.form.fieldsContrato.documento_pdf = contrato.documento_pdf
      this.form.fieldsContrato.doc_type_pdf = contrato.doc_type_pdf
      this.form.fieldsContrato.nombre_pdf = contrato.nombre_pdf
      this.form.fieldsContrato.observaciones = contrato.observaciones
      this.cargarFacturas()
      this.setViewContrato('update')
    },
    async seeRowContrato (row) {
      this.modal_ver_contrato = false
      this.licitacionesOptions = []
      let contrato = { ...row }
      this.form.fieldsContrato.id = contrato.id
      this.form.fieldsContrato.recurso_id = contrato.recurso_id
      this.form.fieldsContrato.empresa_id = contrato.empresa_id
      await this.cargarLicitaciones()
      if (contrato.licitacion_id === null) {
        this.form.fieldsContrato.licitacion_id = 0
      } else {
        this.form.fieldsContrato.licitacion_id = contrato.licitacion_id
      }
      if (contrato.fecha_inicio === null) {
        this.form.fieldsContrato.fecha_inicio = ''
      } else {
        this.form.fieldsContrato.fecha_inicio = moment(String(contrato.fecha_inicio)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      }
      this.form.fieldsContrato.num_contrato = contrato.num_contrato
      if (contrato.fecha_fin === null) {
        this.form.fieldsContrato.fecha_fin = ''
      } else {
        this.form.fieldsContrato.fecha_fin = moment(String(contrato.fecha_fin)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      }
      this.form.fieldsContrato.monto_total = contrato.monto_total
      this.form.fieldsContrato.monto_total_validar = contrato.monto_total
      this.form.fieldsContrato.rep_legal_contrato = contrato.rep_legal_contrato
      if (contrato.nombre === null) {
        this.form.fieldsContrato.nombre = ''
      } else {
        this.form.fieldsContrato.nombre = contrato.nombre
      }
      if (contrato.organismo_id === null) {
        this.form.fieldsContrato.organismo_id = 0
      } else {
        this.form.fieldsContrato.organismo_id = contrato.organismo_id
      }
      this.form.fieldsContrato.porcentaje = contrato.porcentaje
      this.form.fieldsContrato.observaciones = contrato.observaciones
      this.cargarFacturas()
      this.modal_ver_contrato = true
    },
    deleteSingleContrato (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este contrato?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.deleteContratos(id)
      }).catch(() => {})
    },
    deleteDoc (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este Archivo?',
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
    deleteContratos (id) {
      let params = {id: id}
      this.deleteContrato(params).then(({data}) => {
        this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          // this.loadAll()
          this.getContratosbyID({id: this.form.fields.id})
          // console.log(this.form.fields.id)
          this.setViewContrato('grid')
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async newRowContrato () {
      this.licitacionesOptions = []
      this.$v.form.$reset()
      this.form.fieldsContrato.id = 0
      this.form.fieldsContrato.recurso_id = this.form.fields.id
      this.form.fieldsContrato.empresa_id = 0
      this.form.fieldsContrato.licitacion_id = 0
      this.form.fieldsContrato.fecha_inicio = ''
      this.form.fieldsContrato.num_contrato = ''
      this.form.fieldsContrato.fecha_fin = ''
      this.form.fieldsContrato.monto_total = 0
      this.form.fieldsContrato.monto_total_validar = 0
      this.form.fieldsContrato.rep_legal_contrato = ''
      this.form.fieldsContrato.nombre = ''
      this.form.fieldsContrato.organismo_id = 0
      this.form.fieldsContrato.porcentaje = 0
      this.form.fieldsContrato.observaciones = ''
      await this.cargarLicitaciones()
      this.setViewContrato('create')
    },
    async clicFilaContrato (row) {
      this.form.fieldsContrato.id = row.id
      this.form.fieldsContrato.recurso_id = row.recurso_id
      this.form.fieldsContrato.empresa_id = row.empresa_id
      await this.cargarLicitaciones()
      if (this.form.fieldsContrato.id > 0) {
        this.cargarFacturas()
      }
      if (row.licitacion_id === null) {
        this.form.fieldsContrato.licitacion_id = 0
      } else {
        this.form.fieldsContrato.licitacion_id = row.licitacion_id
      }
      if (row.fecha_inicio === null) {
        this.form.fieldsContrato.fecha_inicio = ''
      } else {
        this.form.fieldsContrato.fecha_inicio = moment(String(row.fecha_inicio)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      }
      this.form.fieldsContrato.num_contrato = parseInt(row.num_contrato)
      if (row.fecha_fin === null) {
        this.form.fieldsContrato.fecha_fin = ''
      } else {
        this.form.fieldsContrato.fecha_fin = moment(String(row.fecha_fin)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      }
      this.form.fieldsContrato.monto_total = row.monto_total
      this.form.fieldsContrato.monto_total_validar = row.monto_total
      this.form.fieldsContrato.rep_legal_contrato = row.rep_legal_contrato
      if (row.nombre === null) {
        this.form.fieldsContrato.nombre = ''
      } else {
        this.form.fieldsContrato.nombre = row.contrato
      }
      if (row.organismo_id === null) {
        this.form.fieldsContrato.organismo_id = 0
      } else {
        this.form.fieldsContrato.organismo_id = row.organismo_id
      }
      this.form.fieldsContrato.porcentaje = row.porcentaje
      this.form.fieldsContrato.observaciones = row.observaciones
      this.InformacionContrato = true
    },
    validarFechas (fechaInicio, fechaFin) {
      if (moment(String(fechaInicio)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss') <= moment(String(fechaFin)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')) {
        return true
      }
      return false
    },
    uploadFormato () {
      this.$v.form.file.$touch()
      if (!this.$v.form.file.$error) {
        if (this.form.file.fecha_vencimiento >= this.form.file.fecha_factura) {
          try {
            this.loadingButton = true
            let file = this.$refs.fileInputFormato.files[0]
            let formData = new FormData()
            formData.append('file', file)
            formData.append('nombre', file.name)
            formData.append('nombre_archivo', file.name)
            formData.append('contrato_id', this.form.fieldsContrato.id)
            formData.append('folio_fiscal', this.form.file.folio_fiscal)
            formData.append('fecha_factura', moment(String(this.form.file.fecha_factura)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss'))
            formData.append('fecha_pago', moment(String(this.form.file.fecha_pago)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss'))
            formData.append('fecha_vencimiento', moment(String(this.form.file.fecha_vencimiento)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss'))
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
                    this.$showMessage(response.data.message.title, response.data.message.content)
                    this.cargarFacturas()
                    this.$v.form.$reset()
                    this.form.file.nombre = ''
                    this.form.file.fecha_pago = ''
                    this.form.file.fecha_factura = ''
                    this.form.file.fecha_vencimiento = ''
                    this.form.file.folio_fiscal = ''
                    this.show_file_contrato = false
                    if (this.viewsContrato.create) {
                      this.setViewContrato('create')
                    } else {
                      this.setViewContrato('update')
                    }
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
    abrirDocumento (name, ext) {
      console.log(name)
      let nombre = name
      let uri = process.env.API + `facturasContratos/getFile/${nombre}/${ext}`
      window.open(uri, '_blank')
    },
    mensajeNotify (message, color, icon, detail, position) {
      this.$q.notify({
        message: message,
        timeout: 0,
        color: color,
        icon: icon,
        detail: detail,
        position: position,
        closeBtn: true
      })
    },
    save () {
      this.form.fields.monto = this.evaluaMonto(this.form.fields.montovalidar)
      this.form.fields.fuente_financiamiento = this.form.fields.fuente_financiamiento.trim()
      this.$v.form.fields.$touch()
      if (!this.$v.form.fields.$error) {
        this.loadingButton = true
        let params = this.form.fields
        params.year = this.year
        params.monto_sin_iva = this.evaluaMonto(this.form.fields.montovalidariva)
        this.saveRecurso(params).then(({data}) => {
          this.loadingButton = false
          // this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            this.mensajeNotify('Éxito', 'green', 'fas fa-check', data.codigo, 'top')
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
    update () {
      this.form.fields.monto = this.evaluaMonto(this.form.fields.montovalidar)
      this.form.fields.fuente_financiamiento = this.form.fields.fuente_financiamiento.trim()
      this.$v.form.fields.$touch()
      if (!this.$v.form.fields.$error) {
        this.loadingButton = true
        let params = this.form.fields
        params.year = this.year
        params.monto_sin_iva = this.evaluaMonto(this.form.fields.montovalidariva)
        this.updateRecurso(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.mensajeNotify('Éxito', 'blue', 'fas fa-check', data.codigo, 'top')
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
    async editRow (row) {
      this.loadingMessage('Cargando ...')
      let recurso = { ...row }
      this.form.fields.id = row.id
      this.form.fields.cliente_id = recurso.cliente_id
      this.form.fields.subprograma_id = recurso.subprograma_id
      this.form.fields.monto = recurso.monto
      this.form.fields.montovalidar = recurso.monto
      this.form.fields.montovalidariva = recurso.monto_sin_iva
      this.form.fields.monto_licitado = recurso.monto_licitado
      this.form.fields.monto_adjudicado = recurso.monto_adjudicado
      this.form.fields.codigo = recurso.codigo
      this.form.fields.fuente_financiamiento = recurso.fuente_financiamiento
      this.form.fields.total_recursos = recurso.total_recursos
      this.form.fields.programa_id = recurso.programa_id
      this.form.fields.rubro = recurso.rubro
      this.form.fields.nombre = recurso.nombre
      this.form.fields.aliado = recurso.aliado
      this.form.fields.aliado1 = recurso.aliado1
      this.form.fields.aliado2 = recurso.aliado2
      this.form.fields.aliado3 = recurso.aliado3
      this.form.fields.suma_contratos = recurso.suma_contratos
      this.form.fields.adjudicacion = recurso.adjudicacion
      this.form.fields.sucursal_id = recurso.sucursal_id
      this.form.fields.tipo = recurso.tipo
      await this.cargarDataContratos()
      await this.cargarDataProject()
      await this.cargarDataOrganismos()
      await this.cargarDataComisiones()
      this.setView('update')
      this.setViewPresupuestos('grid')
      this.tabPrincipal = 'contratos'
      this.setViewContrato('grid')
      this.$q.loading.hide()
    },
    async editRow2P (row, roww) {
      let recurso = { ...row }
      this.form.fields.id = row.id
      this.form.fields.cliente_id = recurso.cliente_id
      this.form.fields.subprograma_id = recurso.subprograma_id
      this.form.fields.monto = recurso.monto
      this.form.fields.montovalidar = recurso.monto
      this.form.fields.montovalidariva = recurso.monto_sin_iva
      this.form.fields.monto_licitado = recurso.monto_licitado
      this.form.fields.monto_adjudicado = recurso.monto_adjudicado
      this.form.fields.codigo = recurso.codigo
      this.form.fields.fuente_financiamiento = recurso.fuente_financiamiento
      this.form.fields.total_recursos = recurso.total_recursos
      this.form.fields.programa_id = recurso.programa_id
      this.form.fields.rubro = recurso.rubro
      this.form.fields.nombre = recurso.nombre
      this.form.fields.aliado = recurso.aliado
      this.form.fields.aliado1 = recurso.aliado1
      this.form.fields.aliado2 = recurso.aliado2
      this.form.fields.aliado3 = recurso.aliado3
      this.form.fields.suma_contratos = recurso.suma_contratos
      this.form.fields.adjudicacion = recurso.adjudicacion
      this.form.fields.sucursal_id = recurso.sucursal_id
      this.form.fields.tipo = recurso.tipo
      await this.cargarDataContratos()
      await this.cargarDataProject()
      await this.cargarDataOrganismos()
      await this.cargarDataComisiones()
      this.setView('update')
      await this.projects(roww)
      // this.setViewPresupuestos('update')
    },
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este recurso?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete(id)
      }).catch(() => {})
    },
    verContratos (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: 'Sera redireccionado a contratos...',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.redirContratos(id)
      }).catch(() => {})
    },
    redirContratos (id) {
      this.$router.push({ path: `/contratos/${id}` })
    },
    verPresupuestos (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: 'Sera redireccionado a presupuestos...',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.redirPresupuestos(id)
      }).catch(() => {})
    },
    redirPresupuestos (id) {
      this.$router.push({ path: `/presupuestos/${id}` })
    },
    delete (id) {
      let params = {id: id}
      this.deleteRecurso(params).then(({data}) => {
        this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          this.loadAll()
          this.setView('grid')
        }
      }).catch(error => {
        console.error(error)
      })
    },
    verTodo (row) {
      let recurso = { ...row }
      // console.log(recurso)
      this.form.fields.id = row.id
      this.form.fields.cliente_id = recurso.cliente_id
      this.form.fields.subprograma_id = recurso.subprograma_id
      this.form.fields.monto = recurso.monto
      this.form.fields.montovalidar = recurso.monto
      this.form.fields.montovalidariva = recurso.monto_sin_iva
      this.form.fields.monto_licitado = recurso.monto_licitado
      this.form.fields.monto_adjudicado = recurso.monto_adjudicado
      this.form.fields.codigo = recurso.codigo
      this.form.fields.fuente_financiamiento = recurso.fuente_financiamiento
      this.form.fields.total_recursos = recurso.total_recursos
      this.form.fields.programa_id = recurso.programa_id
      this.form.fields.rubro = recurso.rubro
      this.form.fields.nombre = recurso.nombre
      this.form.fields.aliado = recurso.aliado
      this.form.fields.aliado1 = recurso.aliado1
      this.form.fields.aliado2 = recurso.aliado2
      this.form.fields.aliado3 = recurso.aliado3
      this.form.fields.suma_contratos = recurso.suma_contratos
      this.form.fields.adjudicacion = recurso.adjudicacion
      this.form.fields.sucursal_id = recurso.sucursal_id
      this.form.fields.tipo = recurso.tipo
      this.setView('info')
    },
    newRow () {
      this.$v.form.$reset()
      this.form.fields.id = 0
      this.form.fields.cliente_id = 0
      this.form.fields.subprograma_id = 0
      this.form.fields.monto = 0
      this.form.fields.montovalidar = 0
      this.form.fields.montovalidariva = 0
      this.form.fields.codigo = ''
      this.form.fields.fuente_financiamiento = ''
      this.form.fields.total_recursos = 0
      this.form.fields.programa_id = 0
      this.form.fields.rubro = ''
      this.form.fields.nombre = ''
      this.form.fields.aliado = ''
      this.form.fields.aliado1 = ''
      this.form.fields.aliado2 = ''
      this.form.fields.aliado3 = ''
      this.form.fields.suma_contratos = 0
      this.form.fields.adjudicacion = ''
      this.form.fields.sucursal_id = 0
      this.form.fields.tipo = ''
      this.setView('create')
    },
    async clickFila (row) {
      this.form.fields.id = row.id
      this.form.fields.cliente_id = row.cliente_id
      this.form.fields.subprograma_id = row.subprograma_id
      this.form.fields.monto = row.monto
      this.form.fields.montovalidar = row.monto
      this.form.fields.monto_licitado = row.monto_licitado
      this.form.fields.monto_adjudicado = row.monto_adjudicado
      this.form.fields.codigo = row.codigo
      this.form.fields.fuente_financiamiento = row.fuente_financiamiento
      this.form.fields.total_recursos = row.total_recursos
      this.form.fields.programa_id = row.programa_id
      this.form.fields.rubro = row.rubro
      this.form.fields.nombre = row.nombre
      this.form.fields.aliado = row.aliado
      this.form.fields.aliado1 = row.aliado1
      this.form.fields.aliado2 = row.aliado2
      this.form.fields.aliado3 = row.aliado3
      this.form.fields.suma_contratos = row.suma_contratos
      this.form.fields.adjudicacion = row.adjudicacion
      this.form.fields.sucursal_id = row.sucursal_id
      this.form.fields.tipo = row.tipo
      await this.cargarDataContratos()
      await this.cargarDataProject()
      await this.cargarDataOrganismos()
      await this.cargarDataComisiones()
      this.informacion = true
    },
    borrarMonto () {
      this.form.fields.monto = 0
    },
    selectSubprograma () {
      this.form.fields.subprograma_id = 0
    },
    // contratos
    newContrato () {
      this.licitacionesOptions = []
      this.$v.form.$reset()
      this.form.fieldsContrato.recurso_id = 0
      this.form.fieldsContrato.empresa_id = 0
      this.form.fieldsContrato.licitacion_id = 0
      this.form.fieldsContrato.fecha_inicio = ''
      this.form.fieldsContrato.num_contrato = ''
      this.form.fieldsContrato.fecha_fin = ''
      this.form.fieldsContrato.monto_total = 0
      this.form.fieldsContrato.monto_total_validar = 0
      this.form.fieldsContrato.rep_legal_contrato = ''
      this.form.fieldsContrato.nombre = ''
      this.form.fieldsContrato.observaciones = ''
      this.setView('createContrato')
    },
    savePresupuestos () {
      this.form.fieldsPresupuesto.presupuesto = this.evaluaMonto(this.form.fieldsPresupuesto.presupuestovalidar)
      this.$v.form.fieldsPresupuesto.$touch()
      if (!this.$v.form.fieldsPresupuesto.$error) {
        if (this.validarFechasPresupuestos(this.form.fieldsPresupuesto.inicio, this.form.fieldsPresupuesto.fin)) {
          this.loadingButton = true
          let params = this.form.fieldsPresupuesto
          params.year = this.year
          params.monto_bolsa_validado = this.evaluaMonto(this.form.fieldsPresupuesto.monto_bolsa)
          params.monto_bolsa_validado_iva = this.evaluaMonto(this.form.fieldsPresupuesto.monto_bolsa_iva)
          this.saveProyecto(params).then(({data}) => {
            this.loadingButton = false
            this.$showMessage(data.message.title, data.message.content)
            if (data.result === 'success') {
              this.cargarDataProject()
              this.setViewPresupuestos('grid')
            }
          }).catch(error => {
            console.error(error)
          })
        } else {
          this.mensajeNotify('Revisa las fechas', 3000, 'negative', 'fas fa-exclamation-triangle', 'La fecha de inicio no debe ser mayor a la fecha de término.', 'top-right')
        }
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    updatePresupuestos () {
      this.form.fieldsPresupuesto.presupuesto = this.evaluaMonto(this.form.fieldsPresupuesto.presupuestovalidar)
      this.$v.form.fieldsPresupuesto.$touch()
      if (!this.$v.form.fieldsPresupuesto.$error) {
        if (this.validarFechasPresupuestos(this.form.fieldsPresupuesto.inicio, this.form.fieldsPresupuesto.fin)) {
          this.loadingButton = true
          let params = this.form.fieldsPresupuesto
          params.monto_bolsa_validado = this.evaluaMonto(this.form.fieldsPresupuesto.monto_bolsa)
          params.monto_bolsa_validado_iva = this.evaluaMonto(this.form.fieldsPresupuesto.monto_bolsa_iva)
          this.updateProyecto(params).then(({data}) => {
            this.loadingButton = false
            this.$showMessage(data.message.title, data.message.content)
            if (data.result === 'success') {
              // this.loadAll()
              this.cargarDataProject()
              this.setViewPresupuestos('grid')
            }
          }).catch(error => {
            console.error(error)
          })
        } else {
          this.mensajeNotify('Revisa las fechas', 3000, 'negative', 'fas fa-exclamation-triangle', 'La fecha de inicio no debe ser mayor a la fecha de término.', 'top-right')
        }
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    async editRowPresupuestos (row) {
      this.setViewPresupuestos('update')
      this.loadingMessage('Cargando ...')
      let proyecto = { ...row }
      this.form.fieldsPresupuesto.status = proyecto.status
      this.form.fieldsPresupuesto.id = proyecto.id
      this.form.fieldsPresupuesto.nombre_proyecto = proyecto.nombre_proyecto
      this.form.fieldsPresupuesto.nombre_corto = proyecto.nombre_corto
      this.form.fieldsPresupuesto.recurso_id = row.recurso_id
      this.form.fieldsPresupuesto.dias = proyecto.dias
      this.form.fieldsPresupuesto.presupuesto = proyecto.presupuesto
      this.form.fieldsPresupuesto.presupuesto_actual = this.currencyFormat(proyecto.presupuesto_actual)
      this.form.fieldsPresupuesto.presupuestovalidar = proyecto.presupuesto
      this.form.fieldsPresupuesto.presupuestoActual = proyecto.presupuesto
      this.form.fieldsPresupuesto.monto_bolsa = proyecto.monto_bolsa
      this.form.fieldsPresupuesto.monto_bolsa_iva = proyecto.monto_bolsa_iva
      this.form.fieldsPresupuesto.lider_proyecto = proyecto.lider_proyecto
      this.form.fieldsPresupuesto.lider_usuario = proyecto.nickname
      this.form.fieldsPresupuesto.sucursal_id = this.form.fields.sucursal_id
      if (proyecto.licitacion_id === null) {
        this.form.fieldsPresupuesto.licitacion_id = 0
        this.empresasOptions.push({ 'label': '---Seleccione---', 'value': 0 })
      } else {
        this.form.fieldsPresupuesto.licitacion_id = proyecto.licitacion_id
        this.obtenerEmpresa()
      }
      if (proyecto.empresa_id === null) {
        this.form.fieldsPresupuesto.empresa_id = 0
      } else {
        this.form.fieldsPresupuesto.empresa_id = proyecto.empresa_id
      }
      if (proyecto.sucursal_id === null) {
        this.form.fieldsPresupuesto.sucursal_id = 0
      } else {
        this.form.fieldsPresupuesto.sucursal_id = proyecto.sucursal_id
      }
      if (proyecto.inicio === null) {
        this.form.fieldsPresupuesto.inicio = ''
      } else {
        this.form.fieldsPresupuesto.inicio = moment(String(proyecto.inicio)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      }
      if (proyecto.fin === null) {
        this.form.fieldsPresupuesto.fin = ''
      } else {
        this.form.fieldsPresupuesto.fin = moment(String(proyecto.fin)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      }
      this.form_solicitantes.fieldsPresupuesto.proyecto_id = proyecto.id
      this.form_autorizadores.fieldsPresupuesto.proyecto_id = proyecto.id
      this.form_responsables.fieldsPresupuesto.proyecto_id = proyecto.id
      this.form_colaboradores.fieldsPresupuesto.proyecto_id = proyecto.id
      this.form_perfiles.fieldsPresupuesto.proyecto_id = proyecto.id
      //
      this.form.fieldsContrato.recurso_id = row.recurso_id
      await this.cargarLicitaciones()
      await this.cargarDetalles()
      // await this.getEmpresas()
      /* await this.cargarResponsables()
      await this.cargarSolicitantes()
      await this.cargarAutorizadores() */
      await this.cargarColaboradores()
      // await this.getPerfiles()
      await this.getUsuarios()
      this.$q.loading.hide()
    },
    deleteSingleRowPresupuestos (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este proyecto?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.deletePresupuesto(id)
      }).catch(() => {})
    },
    deletePresupuesto (id) {
      let params = {id: id}
      this.deleteProyecto(params).then(({data}) => {
        this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          this.cargarDataProject()
          this.setViewPresupuestos('grid')
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async newRowPresupuestos () {
      this.$v.form.$reset()
      this.form.fieldsPresupuesto.recurso_id = this.form.fields.id
      this.form.fieldsPresupuesto.inicio = ''
      this.form.fieldsPresupuesto.fin = ''
      this.form.fieldsPresupuesto.dias = 0
      this.form.fieldsPresupuesto.presupuesto = 0
      this.form.fieldsPresupuesto.presupuestovalidar = 0
      this.form.fieldsPresupuesto.monto_bolsa = 0
      this.form.fieldsPresupuesto.monto_bolsa_iva = 0
      this.form.fieldsPresupuesto.nombre_proyecto = ''
      this.form.fieldsPresupuesto.nombre_corto = ''
      this.form.fieldsPresupuesto.lider_proyecto = ''
      this.form.fieldsPresupuesto.licitacion_id = 0
      this.form.fieldsPresupuesto.empresa_id = 0
      this.form.fieldsPresupuesto.sucursal_id = this.form.fields.sucursal_id
      //
      this.form.fieldsContrato.recurso_id = this.form.fields.id
      await this.cargarLicitaciones()
      //
      this.setViewPresupuestos('create')
    },
    clicFilaPresupuesto (row) {
      const aMoneda = (numero, opciones) => {
        // Valores por defecto
        opciones = opciones || {}
        opciones.simbolo = opciones.simbolo || '$'
        opciones.separadorDecimal = opciones.separadorDecimal || '.'
        opciones.separadorMiles = opciones.separadorMiles || ','
        opciones.numeroDeDecimales = opciones.numeroDeDecimales >= 0 ? opciones.numeroDeDecimales : 2
        opciones.posicionSimbolo = opciones.posicionSimbolo || 'i'
        const CIFRAS_MILES = 3

        // Redondear y convertir a cadena
        let numeroComoCadena = numero.toFixed(opciones.numeroDeDecimales)

        // Comenzar desde la izquierda del separador o desde el final de la cadena si no se proporciona
        let posicionDelSeparador = numeroComoCadena.indexOf(opciones.separadorDecimal)
        if (posicionDelSeparador === -1) posicionDelSeparador = numeroComoCadena.length
        let formateadoSinDecimales = '', indice = posicionDelSeparador
        // Ir cortando desde la derecha de 3 en 3, y concatenar en una nueva cadena
        while (indice >= 0) {
          let limiteInferior = indice - CIFRAS_MILES
          // Agregar separador si cortamos más de 3
          formateadoSinDecimales = (limiteInferior > 0 ? opciones.separadorMiles : '') + numeroComoCadena.substring(limiteInferior, indice) + formateadoSinDecimales
          indice -= CIFRAS_MILES
        }
        let formateadoSinSimbolo = `${formateadoSinDecimales}${numeroComoCadena.substr(posicionDelSeparador, opciones.numeroDeDecimales + 1)}`
        return opciones.posicionSimbolo === 'i' ? opciones.simbolo + formateadoSinSimbolo : formateadoSinSimbolo + opciones.simbolo
      }

      const opcionesPesosMexicanos = {
        numeroDeDecimales: 2,
        separadorDecimal: '.',
        separadorMiles: ',',
        simbolo: '$', // Con un espacio, ya que la función no agrega espacios
        posicionSimbolo: 'i' // i = izquierda, d = derecha
      }

      this.objetoInformacionPresupuesto = {
        'dias': row.dias,
        'fin': row.fin,
        'id': row.id,
        'inicio': row.inicio,
        'nombre_proyecto': row.nombre_proyecto,
        'presupuesto': row.presupuesto,
        'recurso': row.recurso,
        'recurso_id': row.recurso_id,
        'presupuestoCopia': row.presupuesto
      }
      this.objetoInformacionPresupuesto.presupuesto = aMoneda(parseFloat(this.objetoInformacionPresupuesto.presupuesto), opcionesPesosMexicanos)
      this.informacionPresupuestos = true
    },
    getDiasLaborables (fechaInicio, fechaFin) {
      let ffin = moment(moment(String(fechaFin)).utcOffset('-04:00:00', false).format('YYYY-MM-DD'))
      let finicio = moment(moment(String(fechaInicio)).utcOffset('-04:00:00', false).format('YYYY-MM-DD'))

      let calcBusinessDays = 1 + (ffin.diff(finicio, 'days') * 5 - (finicio.day() - ffin.day()) * 2) / 7

      if (ffin.day() === 6) calcBusinessDays--// SAT
      if (finicio.day() === 0) calcBusinessDays--// SUN

      return calcBusinessDays
    },
    loadingMessage (mensaje) {
      this.$q.loading.show({message: mensaje})
    },
    exportCSVActividades () {
      let uri = process.env.API + `carga_csv/exportCSV_actividades/${this.form.fieldsPresupuesto.id}`
      window.open(uri, '_blank')
    },
    uploadCsvFile () {
      this.loadingMessage('Cargando los datos del csv ...')
      let file = this.$refs.fileInputCSV.files[0]
      let formData = new FormData()
      formData.append('file', file)
      formData.append('proyecto_id', this.form.fieldsPresupuesto.id)
      axios.post('/carga_csv/uploadCsv', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }).then(response => {
        if (response.data.result === 'success') {
          this.$showMessage('Exito', response.data.message)
          this.form.fieldsPresupuesto.presupuesto_actual = this.currencyFormat(response.data.presupuesto)
          this.form_actividades.actividades_proyecto = true
          this.cargarDetalles()
          this.$q.loading.hide()
        } else {
          this.$q.loading.hide()
          if (response.data.err !== '') {
            this.$showMessage('Errores en archivo', response.data.error[1])
          }
        }
      }).catch(error => {
        this.$q.loading.hide()
        console.error(error)
      })
    },
    async cargarDetalles () {
      this.form_actividades.loading = true
      this.actividades = []
      await this.getActividadesByProyecto({proyecto_id: this.form.fieldsPresupuesto.id}).then(({data}) => {
        if (data.actividades.length > 0) {
          this.actividades = data.actividades
          // this.form_actividades.actividades_proyecto = true
        }
        this.form_actividades.loading = false
      }).catch(error => {
        console.error(error)
        this.form_actividades.loading = false
      })
    },
    async cargarSolicitantes () {
      this.form_solicitantes.loading = true
      this.form_solicitantes.data = []
      await this.getSolicitantesByProyecto({proyecto_id: this.form_solicitantes.fieldsPresupuesto.proyecto_id}).then(({data}) => {
        if (data.solicitantes.length > 0) {
          this.form_solicitantes.data = data.solicitantes
        }
        this.form_solicitantes.loading = false
      }).catch(error => {
        console.error(error)
        this.form_solicitantes.loading = false
      })
    },
    async cargarColaboradores () {
      this.form_colaboradores.loading = true
      this.form_colaboradores.data = []
      await this.getColaboradoresByProyecto({proyecto_id: this.form_colaboradores.fieldsPresupuesto.proyecto_id}).then(({data}) => {
        if (data.colaboradores.length > 0) {
          this.form_colaboradores.data = data.colaboradores
        }
        this.form_colaboradores.loading = false
      }).catch(error => {
        console.error(error)
        this.form_colaboradores.loading = false
      })
    },
    async cargarAutorizadores () {
      this.form_autorizadores.loading = true
      this.form_autorizadores.data = []
      await this.getAutorizadoresByProyecto({proyecto_id: this.form_autorizadores.fieldsPresupuesto.proyecto_id}).then(({data}) => {
        if (data.autorizadores.length > 0) {
          this.form_autorizadores.data = data.autorizadores
        }
        this.form_autorizadores.loading = false
      }).catch(error => {
        console.error(error)
        this.form_autorizadores.loading = false
      })
    },
    async cargarResponsables () {
      this.form_responsables.loading = true
      this.form_responsables.data = []
      await this.getResponsablesByProyecto({proyecto_id: this.form_responsables.fieldsPresupuesto.proyecto_id}).then(({data}) => {
        if (data.responsable_pagos.length > 0) {
          this.form_responsables.data = data.responsable_pagos
        }
        this.form_responsables.loading = false
      }).catch(error => {
        console.error(error)
        this.form_responsables.loading = false
      })
    },
    finalizarActividad (actividad, index) {
      let params = {id: actividad.id}
      this.updateFinalizadoActividad(params).then(({data}) => {
        this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          this.cargarDetalles()
        }
      }).catch(error => {
        console.error(error)
      })
    },
    visibleActividad (actividad, index) {
      let params = {id: actividad.id}
      this.updateVisibleActividad(params).then(({data}) => {
        this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          this.cargarDetalles()
        }
      }).catch(error => {
        console.error(error)
      })
    },
    editActividad (actividad, index) {
      this.form_actividades.modal_editar = true

      this.form_actividades.fieldsPresupuesto.id = actividad.id
      this.form_actividades.fieldsPresupuesto.proyecto_id = actividad.proyecto_id
      this.form_actividades.fieldsPresupuesto.nombre = actividad.nombre
      this.form_actividades.fieldsPresupuesto.avance = actividad.avance
      this.form_actividades.fieldsPresupuesto.presupuesto_inicial = actividad.presupuesto_inicial

      if (actividad.inicio === null) {
        this.form_actividades.fieldsPresupuesto.inicio = ''
      } else {
        this.form_actividades.fieldsPresupuesto.inicio = moment(String(actividad.inicio)).format('YYYY-MM-DD HH:mm:ss')
      }
      if (actividad.fin === null) {
        this.form_actividades.fieldsPresupuesto.fin = ''
      } else {
        this.form_actividades.fieldsPresupuesto.fin = moment(String(actividad.fin)).format('YYYY-MM-DD HH:mm:ss')
      }

      this.form_actividades.fieldsPresupuesto.costo = actividad.costo

      if (actividad.fin_real === null) {
        this.form_actividades.fieldsPresupuesto.fin_real = ''
      } else {
        this.form_actividades.fieldsPresupuesto.fin_real = moment(String(actividad.fin_real)).format('YYYY-MM-DD HH:mm:ss')
      }

      this.form_actividades.fieldsPresupuesto.indice = actividad.indice
      this.form_actividades.fieldsPresupuesto.nivel = actividad.nivel
      if (actividad.resumen === 'SI') {
        this.form_actividades.fieldsPresupuesto.resumen = true
      } else {
        this.form_actividades.fieldsPresupuesto.resumen = false
      }
      this.form_actividades.fieldsPresupuesto.duracion = actividad.duracion
      this.form_actividades.fieldsPresupuesto.duracion_restante = actividad.duracion_restante
      this.form_actividades.fieldsPresupuesto.padre_id = actividad.padre_id
      this.form_actividades.fieldsPresupuesto.children_cantidad = actividad.children.length
    },
    createActividad (actividad, index) {
      this.$v.form_actividades.$reset()
      this.form_actividades.modal_crear = true

      this.form_actividades.fieldsPresupuesto.proyecto_id = actividad.proyecto_id
      this.form_actividades.fieldsPresupuesto.nombre = ''
      this.form_actividades.fieldsPresupuesto.avance = 0
      this.form_actividades.fieldsPresupuesto.presupuesto_inicial = 0

      this.form_actividades.fieldsPresupuesto.inicio = ''
      this.form_actividades.fieldsPresupuesto.fin = ''
      this.form_actividades.fieldsPresupuesto.costo = 0
      this.form_actividades.fieldsPresupuesto.fin_real = ''
      this.form_actividades.fieldsPresupuesto.indice = 'Generado automáticamente'
      this.form_actividades.fieldsPresupuesto.nivel = 3
      this.form_actividades.fieldsPresupuesto.resumen = false
      this.form_actividades.fieldsPresupuesto.duracion = 0
      this.form_actividades.fieldsPresupuesto.duracion_restante = 0
      this.form_actividades.fieldsPresupuesto.padre_id = actividad.id
      this.form_actividades.fieldsPresupuesto.children_cantidad = 0
    },
    async uploadFiles (actividad, index) {
      this.form_actividades.modal_documentos = true
      this.form_actividades.fieldsPresupuesto.nombre = actividad.nombre
      this.form_actividades.fieldsPresupuesto.id = actividad.id
      await this.getFiles()
    },
    updateActividadDetalle () {
      this.$v.form_actividades.$touch()
      if (!this.$v.form_actividades.$error) {
        if (this.validarFechasActividades(this.form_actividades.fieldsPresupuesto.inicio, this.form_actividades.fieldsPresupuesto.fin, this.form_actividades.fieldsPresupuesto.fin_real)) {
          this.loadingButton = true
          let params = this.form_actividades.fieldsPresupuesto
          params.costo_validado = this.evaluaMonto(this.form_actividades.fieldsPresupuesto.costo)
          this.updateActividad(params).then(({data}) => {
            this.loadingButton = false
            this.$showMessage(data.message.title, data.message.content)
            if (data.result === 'success') {
              this.form_actividades.modal_editar = false
              this.form.fieldsPresupuesto.presupuesto_actual = this.currencyFormat(data.monto_actual)
              this.cargarDetalles()
              // console.log('-----------------------------------------------')
              // console.log(this.form.fieldsPresupuesto.presupuesto_actual)
            }
          }).catch(error => {
            console.error(error)
          })
        } else {
          this.mensajeNotify('Revisa las fechas', 3000, 'negative', 'fas fa-exclamation-triangle', 'La fecha de fin real no debe ser menor a la fecha fin ni a la fecha inicio.', 'top-right')
        }
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    crearActividadDetalle () {
      this.costo_actividad = this.evaluaMonto(this.form_actividades.fieldsPresupuesto.costo)
      this.$v.form_actividades.$touch()
      if (!this.$v.form_actividades.$error) {
        if (this.validarFechasActividades(this.form_actividades.fieldsPresupuesto.inicio, this.form_actividades.fieldsPresupuesto.fin, this.form_actividades.fieldsPresupuesto.fin_real)) {
          // console.log(this.costo_actividad)
          if (!this.$v.form_actividades.$error) {
            //
            // console.log(this.costo_actividad)
            this.$q.dialog({
              title: 'Confirmar',
              message: 'Al crear esta actividad se modificará el costo de las actividades principales y del project',
              ok: 'Aceptar',
              cancel: 'Cancelar'
            }).then(() => {
              this.loadingButton = true
              let params = this.form_actividades.fieldsPresupuesto
              params.costo_actividad = this.costo_actividad
              this.saveActividad(params).then(({data}) => {
                this.loadingButton = false
                this.$showMessage(data.message.title, data.message.content)
                if (data.result === 'success') {
                  // this.nuevo_costo = parseFloat(this.evaluaMonto(this.form.fieldsPresupuesto.presupuesto_actual)) + parseFloat(this.form_actividades.fieldsPresupuesto.costo)
                  // this.form.fieldsPresupuesto.presupuesto_actual = this.nuevo_costo
                  this.form_actividades.modal_crear = false
                  this.form.fieldsPresupuesto.presupuesto_actual = this.currencyFormat(data.monto_actual)
                  this.cargarDetalles()
                }
              }).catch(error => {
                console.error(error)
              })
            }).catch(() => {})
            //
          } else {
            // this.$showMessage('Error', 'El costo debe ser mayor a $0. y menor a $1,000,000,000.')
            this.$showMessage('Error', 'Por favor revise los campos.')
          }
        } else {
          this.mensajeNotify('Revisa las fechas', 3000, 'negative', 'fas fa-exclamation-triangle', 'La fecha de fin real no debe ser menor a la fecha fin ni a la fecha inicio.', 'top-right')
        }
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    validarFechasActividades (fechaInicio, fechaFin, fechaFinReal) {
      if (fechaInicio !== '') {
        fechaInicio = moment(String(fechaInicio)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      }
      if (fechaFin !== '') {
        fechaFin = moment(String(fechaFin)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      }
      if (fechaFinReal !== '') {
        fechaFinReal = moment(String(fechaFinReal)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      }
      if (fechaInicio !== '' && fechaFin === '' && fechaFinReal !== '') {
        if (fechaInicio > fechaFinReal) {
          return false
        }
      } else if (fechaFin !== '' && fechaFinReal !== '') {
        if (fechaFin > fechaFinReal) {
          return false
        }
      }
      return true
    },
    borrarPresupuesto () {
      this.form.fieldsPresupuesto.presupuesto = 0
    },
    delete_Actividad (item, index) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar esta actividad?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.form_actividades.loading = true
        let actividadesEliminar = this.ids_actividades(item)
        let params = {arrayActividades: actividadesEliminar}
        this.deleteActividad(params).then(({data}) => {
          this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            this.cargarDetalles()
            this.form_actividades.loading = false
            this.setViewPresupuestos('update')
          }
        }).catch(error => {
          console.error(error)
          this.form_actividades.loading = false
        })
      }).catch(() => {})
    },
    ids_actividades (item) {
      let eliminados = []
      eliminados.push(item.id)
      if (item.children.length > 0) {
        eliminados = this.buscarHijos(item.children, eliminados)
      }
      return eliminados.reverse()
    },
    buscarHijos (padres, eliminados) {
      for (let i = 0; i < padres.length; i++) {
        eliminados.push(padres[i].id)
        if (padres[i].children.length > 0) {
          this.buscarHijos(padres[i].children, eliminados)
        }
      }
      return eliminados
    },
    eliminarNivel (eliminar) {
      for (let j = 0; j < eliminar.length; j++) {
        for (let i = 0; i < this.form_autorizadores.selectNivel.length; i++) {
          if (parseInt(this.form_autorizadores.selectNivel[i].value) === parseInt(eliminar[j])) {
            this.form_autorizadores.selectNivel.splice(i, 1)
            break
          }
        }
      }
    },
    newNivel () {
      let eliminar = []
      for (let eliminados of this.form_autorizadores.data) {
        eliminar.push(eliminados.orden)
      }
      this.eliminarNivel(eliminar)
    },
    eliminarSolicitante (eliminar) {
      for (let j = 0; j < eliminar.length; j++) {
        for (let i = 0; i < this.form_solicitantes.solicitantesOptions.length; i++) {
          if (parseInt(this.form_solicitantes.solicitantesOptions[i].value) === parseInt(eliminar[j])) {
            this.form_solicitantes.solicitantesOptions.splice(i, 1)
            break
          }
        }
      }
    },
    newSolicitante () {
      this.$v.form_solicitantes.$reset()
      this.form_solicitantes.solicitantesOptions = []

      let eliminar = []
      eliminar.push(this.form_solicitantes.fieldsPresupuesto.usuario_id)

      this.form_solicitantes.fieldsPresupuesto.usuario_id = 0
      this.form_solicitantes.fieldsPresupuesto.perfil = ''

      this.form_solicitantes.solicitantes_proyecto = true

      for (let pojo of this.usuariosOptions) {
        this.form_solicitantes.solicitantesOptions.push(pojo)
      }

      for (let eliminados of this.form_solicitantes.data) {
        eliminar.push(eliminados.usuario_id)
      }
      this.eliminarSolicitante(eliminar)
    },
    saveSolicitantes () {
      this.$v.form_solicitantes.$touch()
      if (!this.$v.form_solicitantes.$error) {
        this.loadingButton = true
        let params = this.form_solicitantes.fieldsPresupuesto
        this.saveSolicitante(params).then(({data}) => {
          this.loadingButton = false
          this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            this.cargarSolicitantes()
            this.form_solicitantes.solicitantes_proyecto = false
            this.setViewPresupuestos('update')
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    deleteSolicitantes (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este solicitante?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        let params = {id: id}
        this.deleteSolicitante(params).then(({data}) => {
          this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            this.cargarSolicitantes()
            this.setViewPresupuestos('update')
          }
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
    },
    eliminarAutorizador (eliminar) {
      for (let j = 0; j < eliminar.length; j++) {
        for (let i = 0; i < this.form_autorizadores.autorizadoresOptions.length; i++) {
          if (parseInt(this.form_autorizadores.autorizadoresOptions[i].value) === parseInt(eliminar[j])) {
            this.form_autorizadores.autorizadoresOptions.splice(i, 1)
            break
          }
        }
      }
    },
    newAutorizador () {
      this.$v.form_autorizadores.$reset()
      this.form_autorizadores.autorizadoresOptions = []

      let eliminar = []
      // eliminar.push(this.form_autorizadores.fieldsPresupuesto.usuario_id)

      this.form_autorizadores.fieldsPresupuesto.usuario_id = 0
      this.form_autorizadores.fieldsPresupuesto.perfil = ''
      this.form_autorizadores.fieldsPresupuesto.orden = 0
      this.form_autorizadores.fieldsPresupuesto.alteracion = 'NO'

      this.form_autorizadores.autorizadores_proyecto = true

      for (let pojo of this.usuariosOptions) {
        this.form_autorizadores.autorizadoresOptions.push(pojo)
      }

      for (let eliminados of this.form_autorizadores.data) {
        eliminar.push(eliminados.usuario_id)
      }
      this.eliminarAutorizador(eliminar)
    },
    async ejecutaAutorizador () {
      // await this.cargarAutorizadores()
      await this.newAutorizador()
      // await this.newNivel()
    },
    saveAutorizadores () {
      this.$v.form_autorizadores.$touch()
      if (!this.$v.form_autorizadores.$error) {
        this.loadingButton = true
        let params = this.form_autorizadores.fieldsPresupuesto
        this.saveAutorizador(params).then(({data}) => {
          this.loadingButton = false
          this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            this.cargarAutorizadores()
            this.form_autorizadores.autorizadores_proyecto = false
            this.setViewPresupuestos('update')
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    deleteAutorizadores (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este autorizador?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        let params = {id: id}
        this.deleteAutorizador(params).then(({data}) => {
          this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            this.cargarAutorizadores()
            this.setViewPresupuestos('update')
          }
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
    },
    eliminarResponsable (eliminar) {
      for (let j = 0; j < eliminar.length; j++) {
        for (let i = 0; i < this.form_responsables.responsablesOptions.length; i++) {
          if (parseInt(this.form_responsables.responsablesOptions[i].value) === parseInt(eliminar[j])) {
            this.form_responsables.responsablesOptions.splice(i, 1)
            break
          }
        }
      }
    },
    newResponsable () {
      this.$v.form_responsables.$reset()
      this.form_responsables.responsablesOptions = []

      let eliminar = []
      eliminar.push(this.form_responsables.fieldsPresupuesto.usuario_id)

      this.form_responsables.fieldsPresupuesto.usuario_id = 0
      this.form_responsables.fieldsPresupuesto.perfil = ''

      this.form_responsables.responsables_proyecto = true

      for (let pojo of this.usuariosOptions) {
        this.form_responsables.responsablesOptions.push(pojo)
      }

      for (let eliminados of this.form_responsables.data) {
        eliminar.push(eliminados.usuario_id)
      }
      this.eliminarResponsable(eliminar)
    },
    saveResponsables () {
      this.$v.form_responsables.$touch()
      if (!this.$v.form_responsables.$error) {
        this.loadingButton = true
        let params = this.form_responsables.fieldsPresupuesto
        this.saveResponsable(params).then(({data}) => {
          this.loadingButton = false
          this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            this.cargarResponsables()
            this.form_responsables.responsables_proyecto = false
            this.setViewPresupuestos('update')
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    deleteResponsables (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este responsable?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        let params = {id: id}
        this.deleteResponsable(params).then(({data}) => {
          this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            this.cargarResponsables()
            this.setViewPresupuestos('update')
          }
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
    },
    eliminarColaborador (eliminar) {
      for (let j = 0; j < eliminar.length; j++) {
        for (let i = 0; i < this.form_colaboradores.colaboradoresOptions.length; i++) {
          if (parseInt(this.form_colaboradores.colaboradoresOptions[i].value) === parseInt(eliminar[j])) {
            this.form_colaboradores.colaboradoresOptions.splice(i, 1)
            break
          }
        }
      }
    },
    newColaborador () {
      this.$v.form_colaboradores.$reset()
      this.form_colaboradores.colaboradoresOptions = []

      let eliminar = []
      eliminar.push(this.form_colaboradores.fieldsPresupuesto.usuario_id)

      this.form_colaboradores.fieldsPresupuesto.usuario_id = 0
      this.form_colaboradores.fieldsPresupuesto.perfil = ''

      this.form_colaboradores.colaboradores_proyecto = true

      for (let pojo of this.usuariosOptions) {
        this.form_colaboradores.colaboradoresOptions.push(pojo)
      }

      for (let eliminados of this.form_colaboradores.data) {
        eliminar.push(eliminados.usuario_id)
      }
      this.eliminarColaborador(eliminar)
    },
    saveColaboradores () {
      this.$v.form_colaboradores.$touch()
      if (!this.$v.form_colaboradores.$error) {
        this.loadingButton = true
        let params = this.form_colaboradores.fieldsPresupuesto
        this.saveColaborador(params).then(({data}) => {
          this.loadingButton = false
          this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            this.cargarColaboradores()
            this.form_colaboradores.colaboradores_proyecto = false
            this.setViewPresupuestos('update')
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    deleteColaboradores (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este solicitante?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        let params = {id: id}
        this.deleteColaborador(params).then(({data}) => {
          this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            this.cargarColaboradores()
            this.setViewPresupuestos('update')
          }
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
    },
    activarPerifles () {
      this.form_actividades.actividades_proyecto = true
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
    selectedFileActividad (item) {
      this.$refs.actividadInput.value = ''
      this.registro_actividad = item
      this.$refs.actividadInput.click()
    },
    async getFiles () {
      this.form_archivos.loading = true
      this.form_archivos.data = []
      await axios.get('/carga_csv/getFilesByActividad/' + this.form_actividades.fieldsPresupuesto.id).then(({data}) => {
        if (data.files.length > 0) {
          this.form_archivos.data = data.files
        }
        this.form_archivos.loading = false
      }).catch(error => {
        console.error(error)
        this.form_archivos.loading = false
      })
    },
    uploadFormatoActividad () {
      try {
        let file = this.$refs.fileInputFormato.files[0]
        let formData = new FormData()
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('actividad_id', this.form_actividades.fieldsPresupuesto.id)
        formData.append('formato_requisito_id', 0)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('carga_csv/uploadArchivo', formData, {
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
                this.getFiles()
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
    descargarFormatoActividad (id, ext) {
      let uri = process.env.API + `carga_csv/getFile/${id}`
      window.open(uri, '_blank')
    },
    deleteFormatoActividad (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este archivo?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        let params = {id: id}
        this.deleteFormatoAct(params).then(({data}) => {
          this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            this.getFiles()
          }
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
    },
    editColaborador (user) {
      this.form_colaboradores.fieldsPresupuesto.usuario_id = user.usuario_id
      this.form_colaboradores.fieldsPresupuesto.perfil = user.perfil
      this.form_colaboradores.fieldsPresupuesto.id = user.id
      this.$v.form_colaboradores.$reset()
      this.form_colaboradores.colaboradoresOptions = []

      let eliminar = []
      eliminar.push(this.form_colaboradores.fieldsPresupuesto.usuario_id)
      for (let pojo of this.usuariosOptions) {
        this.form_colaboradores.colaboradoresOptions.push(pojo)
      }
      for (let eliminados of this.form_colaboradores.data) {
        eliminar.push(eliminados.usuario_id)
      }
      this.eliminarColaborador(eliminar)
      this.form_colaboradores.colaboradoresOptions.push({ 'label': user.nombre, 'value': user.usuario_id })
      this.form_colaboradores.editar_colaboradores_proyecto = true
    },
    editarColaborador () {
      let params = this.form_colaboradores.fieldsPresupuesto
      this.updateColaborador(params).then(({data}) => {
        this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          this.form_colaboradores.editar_colaboradores_proyecto = false
          this.cargarColaboradores()
          this.setViewPresupuestos('update')
        }
      }).catch(error => {
        console.error(error)
      })
    },
    editSolicitante (user) {
      this.form_solicitantes.fieldsPresupuesto.usuario_id = user.usuario_id
      this.form_solicitantes.fieldsPresupuesto.perfil = user.perfil
      this.form_solicitantes.fieldsPresupuesto.id = user.id
      this.$v.form_solicitantes.$reset()
      this.form_solicitantes.solicitantesOptions = []

      let eliminar = []
      eliminar.push(this.form_solicitantes.fieldsPresupuesto.usuario_id)
      for (let pojo of this.usuariosOptions) {
        this.form_solicitantes.solicitantesOptions.push(pojo)
      }
      for (let eliminados of this.form_solicitantes.data) {
        eliminar.push(eliminados.usuario_id)
      }
      this.eliminarSolicitante(eliminar)
      this.form_solicitantes.solicitantesOptions.push({ 'label': user.nombre, 'value': user.usuario_id })
      this.form_solicitantes.editar_solicitantes_proyecto = true
    },
    editarSolicitante () {
      let params = this.form_solicitantes.fieldsPresupuesto
      this.updateSolicitante(params).then(({data}) => {
        this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          this.form_solicitantes.editar_solicitantes_proyecto = false
          this.cargarSolicitantes()
          this.setViewPresupuestos('update')
        }
      }).catch(error => {
        console.error(error)
      })
    },
    editAutorizador (user) {
      this.form_autorizadores.fieldsPresupuesto.usuario_id = user.usuario_id
      this.form_autorizadores.fieldsPresupuesto.perfil = user.perfil
      this.form_autorizadores.fieldsPresupuesto.id = user.id
      this.form_autorizadores.fieldsPresupuesto.orden = user.orden
      if (user.alteracion === 'SI') {
        this.alterar_presupuesto = true
      } else {
        this.alterar_presupuesto = false
      }
      this.form_autorizadores.fieldsPresupuesto.alteracion = user.alteracion
      this.$v.form_autorizadores.$reset()
      this.form_autorizadores.autorizadoresOptions = []

      let eliminar = []
      for (let pojo of this.usuariosOptions) {
        this.form_autorizadores.autorizadoresOptions.push(pojo)
      }
      for (let eliminados of this.form_autorizadores.data) {
        eliminar.push(eliminados.usuario_id)
      }
      this.eliminarAutorizador(eliminar)
      this.form_autorizadores.autorizadoresOptions.push({ 'label': user.nombre, 'value': user.usuario_id })
      // this.newNivel()
      this.form_autorizadores.selectNivel.push({ 'label': user.orden, 'value': parseInt(user.orden) })
      this.form_autorizadores.editar_autorizadores_proyecto = true
    },
    editarAutorizador () {
      let params = this.form_autorizadores.fieldsPresupuesto
      this.updateAutorizador(params).then(({data}) => {
        this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          this.form_autorizadores.editar_autorizadores_proyecto = false
          this.cargarAutorizadores()
          this.setViewPresupuestos('update')
        }
      }).catch(error => {
        console.error(error)
      })
    },
    editResponsable (user) {
      this.form_responsables.fieldsPresupuesto.usuario_id = user.usuario_id
      this.form_responsables.fieldsPresupuesto.perfil = user.perfil
      this.form_responsables.fieldsPresupuesto.id = user.id
      this.$v.form_responsables.$reset()
      this.form_responsables.responsablesOptions = []

      let eliminar = []
      for (let pojo of this.usuariosOptions) {
        this.form_responsables.responsablesOptions.push(pojo)
      }
      for (let eliminados of this.form_responsables.data) {
        eliminar.push(eliminados.usuario_id)
      }
      this.eliminarResponsable(eliminar)
      this.form_responsables.responsablesOptions.push({ 'label': user.nombre, 'value': user.usuario_id })
      this.form_responsables.editar_responsables_proyecto = true
    },
    editarResponsable () {
      let params = this.form_responsables.fieldsPresupuesto
      this.updateResponsable(params).then(({data}) => {
        this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          this.form_responsables.editar_responsables_proyecto = false
          this.cargarResponsables()
          this.setViewPresupuestos('update')
        }
      }).catch(error => {
        console.error(error)
      })
    },
    obtenerEmpresa () {
      if (this.form.fieldsPresupuesto.licitacion_id > 0) {
        this.getEmpresaConcursante({id: this.form.fieldsPresupuesto.licitacion_id}).then(({data}) => {
          if (data.result === 'success') {
            this.form.fieldsPresupuesto.empresa_id = parseInt(data.empresa[0].empresa_id)
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.form.fieldsPresupuesto.empresa_id = 0
        this.empresasOptions.push({ 'label': '---Seleccione---', 'value': 0 })
      }
    },
    borrarActividades () {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea eliminar las actividades de este project?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        let params = {id: this.form.fieldsPresupuesto.id}
        this.deleteActividad(params).then(({data}) => {
          this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            this.cargarDetalles()
          }
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
    },
    editOrganismo (row) {
      this.form_organismos.fields.id = row.id
      this.form_organismos.fields.nombre = row.nombre
      this.setViewOrganismos('update')
    },
    async newRowOrganismos () {
      this.$v.form_organismos.$reset()
      this.form_organismos.fields.id = 0
      this.form_organismos.fields.nombre = ''
      this.setViewOrganismos('create')
    },
    saveOrganismos () {
      this.$v.form_organismos.fields.$touch()
      if (!this.$v.form_organismos.fields.$error) {
        this.loadingButton = true
        let params = this.form_organismos.fields
        params.recurso_id = this.form.fields.id
        params.year = this.year
        this.saveOrganismo(params).then(({data}) => {
          this.loadingButton = false
          // this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            this.mensajeNotify('Éxito', 'green', 'fas fa-check', data.message.content, 'top')
            this.cargarDataOrganismos()
            this.setViewOrganismos('grid')
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
    updateOrganismos () {
      this.$v.form_organismos.fields.$touch()
      if (!this.$v.form_organismos.fields.$error) {
        this.loadingButton = true
        let params = this.form_organismos.fields
        this.updateOrganismo(params).then(({data}) => {
          this.loadingButton = false
          this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            // this.loadAll()
            this.form_organismos.loading = true
            this.cargarDataOrganismos()
            this.setViewOrganismos('grid')
            this.form_organismos.loading = false
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    deleteOrganismos (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este organismo?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        let params = {id: id}
        this.deleteOrganismo(params).then(({data}) => {
          this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            this.cargarDataOrganismos()
            this.setViewPresupuestos('grid')
          }
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
    },
    selectedRowContrato (row) {
      this.$refs.contratoInput.value = ''
      this.registro_contrato = row
      this.$refs.contratoInput.click()
    },
    uploadContratoFile () {
      try {
        let file = this.$refs.contratoInput.files[0]
        let formData = new FormData()
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('contrato_id', this.registro_contrato.id)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('contratos/uploadArchivoFinal', formData, {
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
                this.setViewContrato('grid')
                this.cargarDataContratos()
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
    verDocumentoContrato (id, ext) {
      let uri = process.env.API + '/public/assets/contratos_documentos_finales/' + id + '.' + ext
      window.open(uri, '_blank')
    },
    descargarDocumentoContrato (id, ext) {
      let uri = process.env.API + `contratos/getFile/${id}/${ext}`
      window.open(uri, '_blank')
    },
    eliminarDocumentoContrato (id) {
      this.$q.dialog({
        title: 'Confirmar acción',
        message: '¿Desea eliminar el archivo?',
        ok: 'Eliminar',
        cancel: 'Cancelar',
        preventClose: true
      }).then(() => {
        this.borrarDocumento(id)
      }).catch(() => {
      })
    },
    borrarDocumento (id) {
      this.eliminarDocumento({id: id}).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'delete', position: 'top-right'
          })
          this.cargarDataContratos()
          this.setViewContrato('grid')
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async newPerfil () {
      this.$v.form_perfiles.$reset()
      this.form_perfiles.perfilesOptions = []

      this.form_perfiles.fieldsPresupuesto.perfil_id = 0
      this.form_perfiles.fieldsPresupuesto.participacion = ''
      await this.getAllPerfiles()

      this.form_perfiles.perfiles_proyecto = true
    },
    savePerfiles () {
      this.$v.form_perfiles.$touch()
      if (!this.$v.form_perfiles.$error) {
        this.loadingButton = true
        let params = this.form_perfiles.fieldsPresupuesto
        this.savePerfil(params).then(({data}) => {
          this.loadingButton = false
          this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            this.getPerfiles()
            this.form_perfiles.perfiles_proyecto = false
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    getPerfiles () {
      let params = this.form_perfiles.fieldsPresupuesto
      this.getPerfilesByProject(params).then(({data}) => {
        if (data.result === 'success') {
          this.form_perfiles.data = data.proyectos_perfiles
        }
      }).catch(error => {
        console.error(error)
      })
    },
    deletePerfiles (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea eliminar el perfil del project?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        let params = {id: id}
        this.deletePerfil(params).then(({data}) => {
          this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            this.getPerfiles()
          }
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
    },
    updateStatusBloqueado () {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea bloquear el project? No podrán realizarse solicitudes.',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.form.fieldsPresupuesto.status = 'BLOQUEADO'
        let params = this.form.fieldsPresupuesto
        this.updateProyectoStatus(params).then(({data}) => {
          // this.$showMessage(data.message.title, data.message.content)
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
          }
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
    },
    updateStatusActivo () {
      this.form.fieldsPresupuesto.status = 'ACTIVO'
      let params = this.form.fieldsPresupuesto
      this.updateProyectoStatus(params).then(({data}) => {
        // this.$showMessage(data.message.title, data.message.content)
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
        }
      }).catch(error => {
        console.error(error)
      })
    },
    finalizarProyecto (row) {
      let params = row
      this.updateFinalizado(params).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content,
            timeout: 3000,
            type: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'done',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    checkContrato () {
      if (this.$refs.fileInputFormato.files !== null) {
        this.show_file_contrato = true
      }
    },
    backProjects () {
      this.setViewPresupuestos('grid')
      this.cargarDataProject()
    },
    async editComisiones (row) {
      this.$v.form_comisiones.$reset()
      this.form_comisiones.fields.id = row.id
      this.form_comisiones.fields.proyecto_id = row.proyecto_id
      this.form_comisiones.fields.aliado_id = row.aliado_id
      this.form_comisiones.fields.tipo = row.tipo
      this.form_comisiones.fields.porcentaje = row.porcentaje
      this.form_comisiones.fields.monto = row.monto
      this.form_comisiones.fields.monto_total = row.monto_total
      this.form_comisiones.fields.metodo_pago = row.metodo_pago
      this.form_comisiones.fields.contrato_id = row.contrato_id
      this.form_comisiones.fields.iva = row.iva
      this.form_comisiones.fields.aplicado = row.aplicado
      this.form_comisiones.fields.tiempo_pago = row.tiempo_pago
      this.form_comisiones.fields.observaciones = row.observaciones
      this.form_comisiones.monto_bolsa = 0
      this.form_comisiones.monto_base = 0
      this.form_comisiones.monto_comision = 0
      await this.contrato_utilidad()
      this.setViewComisiones('update')
    },
    async newRowComisiones () {
      this.$v.form_comisiones.$reset()
      this.form_comisiones.fields.id = 0
      this.form_comisiones.fields.proyecto_id = this.form.fields.id
      this.form_comisiones.fields.aliado_id = 0
      this.form_comisiones.fields.tipo = 'all'
      this.form_comisiones.fields.porcentaje = 0.00
      this.form_comisiones.fields.monto = 0.00
      this.form_comisiones.fields.monto_total = 0.00
      this.form_comisiones.fields.metodo_pago = 'all'
      this.form_comisiones.fields.contrato_id = 0
      this.form_comisiones.fields.iva = 'all'
      this.form_comisiones.fields.aplicado = 'all'
      this.form_comisiones.fields.tiempo_pago = 'all'
      this.form_comisiones.fields.observaciones = ''
      this.form_comisiones.monto_bolsa = 0
      this.form_comisiones.monto_base = 0
      this.form_comisiones.monto_comision = 0
      this.setViewComisiones('create')
    },
    saveComisiones () {
      if (this.form_comisiones.fields.aplicado === 'A LA UTILIDAD') {
        this.form_comisiones.fields.contrato_id = 1
      }
      this.$v.form_comisiones.fields.$touch()
      if (!this.$v.form_comisiones.fields.$error) {
        this.loadingButton = true
        let params = this.form_comisiones.fields
        params.monto_evaluado = this.evaluaMonto(this.form_comisiones.fields.monto)
        params.monto_total_evaluado = this.evaluaMonto(this.form_comisiones.fields.monto_total)
        params.year = this.year
        this.saveComision(params).then(({data}) => {
          this.loadingButton = false
          // this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            this.mensajeNotify('Éxito', 'green', 'fas fa-check', data.message.content, 'top')
            this.cargarDataComisiones()
            this.setViewComisiones('grid')
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
    updateComisiones () {
      if (this.form_comisiones.fields.aplicado === 'A LA UTILIDAD') {
        this.form_comisiones.fields.contrato_id = 1
      }
      this.$v.form_comisiones.fields.$touch()
      if (!this.$v.form_comisiones.fields.$error) {
        this.loadingButton = true
        let params = this.form_comisiones.fields
        params.monto_evaluado = this.evaluaMonto(this.form_comisiones.fields.monto)
        params.monto_total_evaluado = this.evaluaMonto(this.form_comisiones.fields.monto_total)
        this.updateComision(params).then(({data}) => {
          this.loadingButton = false
          this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            // this.loadAll()
            this.form_comisiones.loading = true
            this.cargarDataComisiones()
            this.setViewComisiones('grid')
            this.form_comisiones.loading = false
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    deleteComisiones (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar esta comisión?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        let params = {id: id}
        this.deleteComision(params).then(({data}) => {
          this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            this.cargarDataComisiones()
            this.setViewComisiones('grid')
          }
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
    },
    isNumber (evt, input) {
      switch (input) {
        case 'cantidad':
          this.form_comisiones.fields.porcentaje = this.form_comisiones.fields.porcentaje.replace(/[^0-9.]/g, '')
          break
        default:
          break
      }
    },
    getContratosOpt () {
      this.getContratosOptions({id: this.form.fields.id}).then(({data}) => {
        if (data.result === 'success') {
          if (data.contratosOptions.length > 0) {
            this.contratosOptions.push({ 'label': '---Seleccione---', 'value': 0 })
            this.contratosOptions = data.contratosOptions
            this.form_comisiones.fields.contrato_id = 0
          } else {
            this.contratosOptions.push({ 'label': '---No hay contratos---', 'value': 0 })
            this.contratosOptions = []
            this.form_comisiones.fields.contrato_id = 0
          }
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async contrato_utilidad () {
      if (this.form_comisiones.fields.aplicado === 'A LA UTILIDAD') {
        this.form_comisiones.fields.contrato_id = 1
        if (this.form_comisiones.fields.aplicado === 'A LA UTILIDAD') {
          this.aplicado = 'UTILIDAD'
        } else {
          this.aplicado = 'CONTRATO'
        }
      }
      if (this.form_comisiones.fields.tipo !== 'all' && this.form_comisiones.fields.porcentaje !== 0.00 && this.form_comisiones.fields.aplicado !== 'all' && this.form_comisiones.fields.contrato_id !== 0 && this.form_comisiones.fields.contrato_id !== null && this.form_comisiones.fields.iva !== 'all') {
        await axios.get(`/recursos/getMontoComision/${this.form_comisiones.fields.tipo}/${this.form_comisiones.fields.porcentaje}/${this.form_comisiones.fields.aplicado}/${this.form_comisiones.fields.contrato_id}/${this.form_comisiones.fields.iva}/${this.form.fields.id}`).then(({data}) => {
          this.form_comisiones.monto_bolsa = data.monto_bolsa
          this.form_comisiones.monto_base = data.monto_base
          if (this.form_comisiones.fields.tipo === 'CANTIDAD FIJA') {
            this.form_comisiones.monto_comision = this.form_comisiones.fields.monto
          } else {
            this.form_comisiones.monto_comision = data.monto_comision
          }
          if (this.form_comisiones.fields.aplicado === 'A LA UTILIDAD') {
            this.form_comisiones.monto_base = data.utility
          }
        }).catch(error => {
          console.error(error)
        })
      }
    },
    busca () {
      this.form.filter = ''
      this.form.filter_presupuesto = ''
      this.filterByYear()
    },
    cancelFactura (row) {
      let params = row
      this.cancelarFactura(params).then(({data}) => {
        this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          this.cargarFacturas()
        }
      }).catch(error => {
        console.error(error)
      })
    },
    updateFactura (row) {
      let params = row
      this.actualizarFactura(params).then(({data}) => {
        this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          this.cargarFacturas()
        }
      }).catch(error => {
        console.error(error)
      })
    },
    downloadContrato () {
      var uri = process.env.API + '/public/assets/licitaciones/bases/' + this.form.fieldsContrato.documento_pdf + '.' + this.form.fieldsContrato.doc_type_pdf
      window.open(uri, '_blank')
    },
    delete_single (item) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar esta actividad? El monto del project será actualizado.',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.deleteActSingle(item)
      }).catch(() => {})
    },
    deleteActSingle (item) {
      let params = {id: item.id}
      this.deleteActividadSingle(params).then(({data}) => {
        this.$showMessage(data.message.title, data.message.content)
        if (data.result === 'success') {
          this.form.fieldsPresupuesto.presupuesto_actual = this.currencyFormat(data.monto_actual)
          this.cargarDetalles()
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async getcom () {
      await this.cargarDataComisiones()
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
        formData.append('comision_id', this.registro_comision.id)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('documentos_comisiones/uploadDocumento', formData, {
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
                this.cargarDataComisiones()
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
      let uri = process.env.API + '/public/assets/documentos_comisiones/' + id + '.' + ext
      window.open(uri, '_blank')
    },
    descargarDocumentoCom (id, ext) {
      let uri = process.env.API + `documentos_comisiones/getFile/${id}/${ext}`
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
          this.cargarDataComisiones()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    uploadPDFFile () {
      try {
        let file = this.$refs.pdfInput.files[0]
        let formData = new FormData()
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('id', this.registroContrato.factura_id)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('facturasContratos/uploadArchivoPDF', formData, {
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
                this.cargarFacturas()
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
    uploadArchivoPDF (row) {
      this.$refs.pdfInput.value = ''
      this.registroContrato = row
      this.$refs.pdfInput.click()
    }
  },
  validations: {
    form: {
      fields: {
        cliente_id: { required, minValue: minValue(1) },
        subprograma_id: {required, minValue: minValue(1)},
        programa_id: {required, minValue: minValue(1)},
        fuente_financiamiento: {required, maxLength: maxLength(100)},
        nombre: {required, minLength: minLength(1)},
        tipo: {required}
      },
      fieldsContrato: {
        recurso_id: {required, minValue: minValue(1)},
        empresa_id: {required, minValue: minValue(1)},
        fecha_inicio: {required},
        fecha_fin: {required},
        num_contrato: {required},
        monto_total: {required, minValue: minValue(1), maxValue: maxValue(1000000000)},
        rep_legal_contrato: {required, nombreRep}
      },
      file: {
        // nombre: {required, minLength: minLength(3)},
        fecha_pago: {required},
        // fecha_factura: {required},
        fecha_vencimiento: {required}
      },
      retail: {
        nombre: {required, minLength: minLength(3)},
        fecha_pago: {required},
        fecha_factura: {required},
        fecha_vencimiento: {required}
      },
      fieldsPresupuesto: {
        recurso_id: {required, minValue: minValue(1)},
        inicio: {required},
        fin: {required},
        presupuesto: {required, minValue: minValue(1), maxValue: maxValue(1000000000)},
        nombre_proyecto: {required, maxLength: maxLength(100)},
        nombre_corto: {required, maxLength: maxLength(50)},
        lider_proyecto: {required}
      }
    },
    form_actividades: {
      fieldsPresupuesto: {
        nombre: {required, maxLength: maxLength(100)},
        duracion: {required, minValue: minValue(0)},
        duracion_restante: {required, minValue: minValue(0)},
        inicio: {required},
        fin: {required}// ,
        // costo: {required, minValue: minValue(1), maxValue: maxValue(1000000000)}
      }
    },
    form_solicitantes: {
      fieldsPresupuesto: {
        usuario_id: {required},
        perfil: {required, maxLength: maxLength(30)}
      }
    },
    form_colaboradores: {
      fieldsPresupuesto: {
        usuario_id: {required},
        perfil: {required, maxLength: maxLength(30)}
      }
    },
    form_autorizadores: {
      fieldsPresupuesto: {
        usuario_id: {required},
        perfil: {required, maxLength: maxLength(30)},
        orden: {required, minValue: minValue(1)}
      }
    },
    form_responsables: {
      fieldsPresupuesto: {
        usuario_id: {required},
        perfil: {required, maxLength: maxLength(30)}
      }
    },
    form_organismos: {
      fields: {
        nombre: {required, maxLength: maxLength(100)}
      }
    },
    form_perfiles: {
      fieldsPresupuesto: {
        perfil_id: {required, minValue: minValue(1)},
        participacion: {maxLength: maxLength(50)}
      }
    },
    form_comisiones: {
      fields: {
        // aliado_id: {required, minValue: minValue(1)},
        tipo: {required, minLength: minLength(4)},
        metodo_pago: {required, minLength: minLength(4)},
        contrato_id: {required, minValue: minValue(1)},
        iva: {required, minLength: minLength(4)},
        aplicado: {required, minLength: minLength(4)}
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
</style>
