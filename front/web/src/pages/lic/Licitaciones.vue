<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Licitaciones" />
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-4 pull-right" style="margin-top: 10px;">
            <q-btn-toggle color="teal" v-model="year" toggle-color="primary" :options="selectYear" @input="getLicitaciones()"/>
          </div>
          <div class="col-sm-2 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn class="btn_nuevo" icon="add" label="Nuevo" @click="newRow()" />
            </div>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-4">
                <q-field icon="date_range" icon-color="dark">
                  <q-datetime v-model="form_filtros.fields.fecha_inicio" type="date" inverted color="dark" float-label="Fecha de inicio" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="date_range" icon-color="dark">
                  <q-datetime v-model="form_filtros.fields.fecha_fallo" type="date" inverted color="dark" float-label="Fecha fallo" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="fas fa-info" icon-color="dark">
                  <q-select v-model="form_filtros.fields.status" inverted color="dark" float-label="Estatus" :options="form_filtros.selectStatus"></q-select>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs" style="padding-bottom: 10px;">
              <div class="col-sm-12 pull-right">
                <q-btn @click="borrar()" color="red" icon="loop">Limpiar</q-btn>
                <q-btn @click="getLicitaciones()" inverted color="orange" icon-right="fas fa-filter" :loading="loadingButton">Filtrar</q-btn>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md" style="padding: 0;">
            <div class="col q-pa-md ">
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
                      <q-td key="folio" :props="props" @click.native="clickFila(props.row)" style="cursor: pointer;">{{ props.row.folio }}</q-td>
                      <q-td key="descripcion" :props="props" @click.native="clickFila(props.row)" style="cursor: pointer;">{{ props.row.descripcion }}</q-td>
                      <q-td key="fecha_inicio" :props="props">{{ props.row.fecha_inicio }}</q-td>
                      <q-td key="fecha_fallo" :props="props">{{ props.row.fecha_fallo }}</q-td>
                      <q-td key="fecha_presentacion" :props="props">{{ props.row.fecha_presentacion }}</q-td>
                      <q-td key="documento_final" :props="props">
                        <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="entregableInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadEntregableFile()" />
                        <q-btn small flat @click="selectedRowEntregable(props.row)" color="green-9" :loading="loadingButton">
                          <q-icon name="cloud_upload" />&nbsp;
                          <q-tooltip>Subir carta de entera satisfacción</q-tooltip>
                        </q-btn>
                        <q-btn small flat :icon="props.row.icon_ef" :color="props.row.color_ef" v-if="props.row.documento_final !== null">
                          <q-tooltip>{{ props.row.documento_final }}</q-tooltip>
                          <q-popover>
                            <q-list link separator class="scroll" style="min-width: 100px">
                              <q-item v-close-overlay @click.native="verDocumentoEntregable(props.row.id, props.row.extension)" v-if="props.row.extension !== 'docx' && props.row.extension !== 'xml' && props.row.extension !== 'xlsx'">
                                <q-item-main label="Ver"/>
                              </q-item>
                              <q-item v-close-overlay @click.native="descargarDocumentoEntregable(props.row.id, props.row.extension)">
                                <q-item-main label="Descargar"/>
                              </q-item>
                              <q-item v-close-overlay @click.native="eliminarDocumentoFinal(props.row.id)">
                                <q-item-main label="Eliminar"/>
                              </q-item>
                            </q-list>
                          </q-popover>
                        </q-btn>
                      </q-td>
                      <q-td key="status" :props="props"><q-chip dense :icon="props.row.icon" :color="props.row.color" text-color="white">{{ props.row.status }}</q-chip></q-td>
                      <q-td key="acciones" :props="props">
                        <q-btn small flat @click="editRow(props.row)" color="blue-6" icon="edit">
                          <q-tooltip>Editar</q-tooltip>
                        </q-btn>
                        <q-btn small flat @click="deleteRow(props.row)" color="red" icon="delete">
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
      </div>
    </div>

    <div v-if="views.create">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Licitaciones" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el label="Nueva licitación"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm" >
              <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
              <q-btn @click="guardarLicitacion()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
            </div>
          </div>
        </div>
      </div>
      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-px-md">
            <div class="row q-pa-md">
              General
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="fas fa-tag" icon-color="dark" :error="$v.form.fields.folio.$error" error-label="Ingrese el folio">
                  <q-input upper-case v-model="form.fields.folio" type="text" inverted color="dark" float-label="Folio" maxlength="20" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-microchip" icon-color="dark">
                  <q-select v-model="form.fields.recurso_id" inverted color="dark" float-label="Proyecto" :options="recursosOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-info" icon-color="dark">
                  <q-select v-model="form.fields.status" inverted color="dark" float-label="Estatus" :options="form.selectStatus"></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-microchip" icon-color="dark"  :error="$v.form.fields.entidad_id.$error" error-label="Elija una entidad">
                  <q-select v-model="form.fields.entidad_id" inverted color="dark" float-label="Entidad" :options="entidadesOptions" filter/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-12">
                <q-field icon="label" icon-color="dark">
                  <q-input upper-case v-model="form.fields.observaciones_creada" type="text" inverted color="dark" float-label="Observaciones creada" v-if="form.fields.status === 'CREADA'"/>
                  <q-input upper-case v-model="form.fields.observaciones_proceso" type="text" inverted color="dark" float-label="Observaciones proceso" v-if="form.fields.status === 'EN PROCESO'"/>
                  <q-input upper-case v-model="form.fields.observaciones_presentada" type="text" inverted color="dark" float-label="Observaciones presentada" v-if="form.fields.status === 'PRESENTADA'"/>
                  <q-input upper-case v-model="form.fields.observaciones_adjudicada" type="text" inverted color="dark" float-label="Observaciones adjudicada" v-if="form.fields.status === 'ADJUDICADA'"/>
                  <q-input upper-case v-model="form.fields.observaciones_no_adjudicada" type="text" inverted color="dark" float-label="Observaciones no adjudicada" v-if="form.fields.status === 'NO ADJUDICADA'"/>
                  <q-input upper-case v-model="form.fields.observaciones_cancelada" type="text" inverted color="dark" float-label="Observaciones cancelada" v-if="form.fields.status === 'CANCELADA'"/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="format_size" icon-color="dark" :error="$v.form.fields.titulo.$error" error-label="Ingrese el procedimiento">
                  <q-input upper-case v-model="form.fields.titulo" type="text" inverted color="dark" float-label="Procedimiento" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="format_size" icon-color="dark" :error="$v.form.fields.descripcion.$error" error-label="Ingrese la descripción">
                  <q-input upper-case v-model="form.fields.descripcion" type="text" inverted color="dark" float-label="Descripción" maxlength="1000" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="fas fa-tag" icon-color="dark">
                  <q-input upper-case v-model="form.fields.contrato" type="text" inverted color="dark" float-label="Contrato" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-tag" icon-color="dark">
                  <q-input upper-case v-model="form.fields.orden_compra" type="text" inverted color="dark" float-label="Orden de compra" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <div class="icono_field">
                  <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                    <q-input v-model="form.fields.monto_inicial" v-money="money" inverted color="dark" float-label="Monto inicial" suffix="MXN"></q-input>
                  </q-field>
                  <!-- <q-field  icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form.fields.monto.$error" error-label="Ingrese el monto ($0.00 - $1,000,000,000.00)" float-label="Monto adjudicado">
                    <money v-model="form.fields.monto" v-bind="money" style="height:53px;width:100%;margin-bottom:2px;" v-bind:class="{ moneyBien: !($v.form.fields.monto.$error), moneyError: $v.form.fields.monto.$error }" @input="$v.form.fields.monto.$touch"></money>
                  </q-field> -->
                </div>
              </div>
              <div class="col-sm-3">
                <div class="icono_field">
                  <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                    <q-input v-model="form.fields.monto_adjudicado" v-money="money" inverted color="dark" float-label="Monto adjudicado" suffix="MXN"></q-input>
                  </q-field>
                  <!-- <q-field  icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form.fields.monto.$error" error-label="Ingrese el monto ($0.00 - $1,000,000,000.00)" float-label="Monto adjudicado">
                    <money v-model="form.fields.monto" v-bind="money" style="height:53px;width:100%;margin-bottom:2px;" v-bind:class="{ moneyBien: !($v.form.fields.monto.$error), moneyError: $v.form.fields.monto.$error }" @input="$v.form.fields.monto.$touch"></money>
                  </q-field> -->
                </div>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field  icon="fas fa-user" icon-color="dark" :error="$v.form.fields.comprador.$error" error-label="Texto demasiado largo">
                  <q-input upper-case v-model="form.fields.comprador" inverted color="dark" float-label="Comprador" maxlength="200"></q-input>
                </q-field>
              </div>
              <div class="col-sm-2" style="margin-top:5px;">
                <q-field icon="fas fa-file-alt" icon-color="dark" helper="Anticipo">
                  <q-toggle v-model="form.fields.campo_anticipo" color="green" checked-icon="fas fa-check" unchecked-icon="fas fa-times"/>
                </q-field>
              </div>
              <div class="col-sm-2">
                <div class="icono_field">
                  <q-field  icon="fas fa-dollar-sign" icon-color="dark" v-if="form.fields.campo_anticipo === true">
                    <q-input align="right" :decimals="0" numeric-keyboard-toggle v-model="form.fields.porcentaje_anticipo" type="number" inverted color="dark" float-label="Porcentaje anticipo" suffix="%"></q-input>
                  </q-field>
                </div>
              </div>
              <div class="col-sm-2">
                <div class="icono_field">
                  <q-field  icon="fas fa-dollar-sign" icon-color="dark" v-if="form.fields.campo_anticipo === true">
                    <q-input v-model="form.fields.monto_anticipo" v-money="money" inverted color="dark" float-label="Monto anticipo" suffix="MXN"></q-input>
                  </q-field>
                </div>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field  icon="fas fa-user" icon-color="dark">
                  <q-input upper-case v-model="form.fields.responsable" inverted color="dark" float-label="Responsable licitación"></q-input>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field  icon="fas fa-user" icon-color="dark">
                  <q-input upper-case v-model="form.fields.responsable_gdp" inverted color="dark" float-label="Responsable GDP"></q-input>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs" style="padding-top: 10px; padding-bottom: 15px;">
              Empresa concursante
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fields.empresa_id.$error" error-label="Elija una empresa">
                  <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa concursante" :options="empresasOptions" filter @input="concursante()"/>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field  icon="fas fa-building" icon-color="dark">
                  <q-input upper-case v-model="form.fields.empresa" inverted color="dark" float-label="Empresa aliada" maxlength="500"></q-input>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs" style="padding-top: 10px; padding-bottom: 15px;">
              Empresas de respaldo
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="fas fa-building" icon-color="dark">
                  <q-select v-model="form.fields.respaldo1" inverted color="dark" float-label="Empresa de respaldo 1" :options="empresasOptions" filter @input="r1()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-building" icon-color="dark">
                  <q-select v-model="form.fields.respaldo2" inverted color="dark" float-label="Empresa de respaldo 2" :options="empresasOptions" filter @input="r2()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-building" icon-color="dark">
                  <q-select v-model="form.fields.respaldo3" inverted color="dark" float-label="Empresa de respaldo 3" :options="empresasOptions" filter @input="r3()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-building" icon-color="dark">
                  <q-select v-model="form.fields.respaldo4" inverted color="dark" float-label="Empresa de respaldo 4" :options="empresasOptions" filter @input="r4()"/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field  icon="fas fa-building" icon-color="dark">
                  <q-input :readonly="form.fields.respaldo1 !== 0" upper-case v-model="form.fields.empresa_respaldo_1" inverted color="dark" float-label="Empresa aliada" maxlength="500"></q-input>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-building" icon-color="dark">
                  <q-input :readonly="form.fields.respaldo2 !== 0" upper-case v-model="form.fields.empresa_respaldo_2" inverted color="dark" float-label="Empresa aliada" maxlength="500"></q-input>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-building" icon-color="dark">
                  <q-input :readonly="form.fields.respaldo3 !== 0" upper-case v-model="form.fields.empresa_respaldo_3" inverted color="dark" float-label="Empresa aliada" maxlength="500"></q-input>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-building" icon-color="dark">
                  <q-input :readonly="form.fields.respaldo4 !== 0" upper-case v-model="form.fields.empresa_respaldo_4" inverted color="dark" float-label="Empresa aliada" maxlength="500"></q-input>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs" style="padding-top: 10px; padding-bottom: 15px;">
              Fechas
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-4">
                <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha_inicio.$error" error-label="Ingrese la fecha de firma">
                  <q-datetime v-model="form.fields.fecha_inicio" type="date" inverted color="dark" float-label="Fecha de inicio" align="center" @input="cambiaFechaInicio()"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-4" v-if="form.fields.fecha_inicio==='' || form.fields.fecha_inicio===null">
                <q-field icon="date_range" icon-color="dark">
                  <q-datetime disabled readonly v-model="form.fields.fecha_presentacion" type="date" inverted color="dark" float-label="Fecha presentación" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-4" v-else>
                <q-field icon="date_range" icon-color="dark">
                  <q-datetime v-model="form.fields.fecha_presentacion" type="date" inverted color="dark" float-label="Fecha presentación" align="center" @input="cambiaFechaIFallo()"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-4" v-if="form.fields.fecha_presentacion==='' || form.fields.fecha_presentacion===null">
                <q-field icon="date_range" icon-color="dark">
                  <q-datetime disabled readonly v-model="form.fields.fecha_fallo" type="date" inverted color="dark" float-label="Fecha fallo" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-4" v-else>
                <q-field icon="date_range" icon-color="dark">
                  <q-datetime v-model="form.fields.fecha_fallo" type="date" inverted color="dark" float-label="Fecha fallo" align="center"></q-datetime>
                </q-field>
              </div>
              <!-- <div class="col-sm-3" v-if="form.fields.fecha_arranque==='' || form.fields.fecha_arranque===null">
                <q-field icon="date_range" icon-color="dark">
                  <q-datetime disabled readonly v-model="form.fields.fecha_termino" type="date" inverted color="dark" float-label="Fecha de término" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-3" v-else>
                <q-field icon="date_range" icon-color="dark">
                  <q-datetime v-model="form.fields.fecha_termino" type="date" inverted color="dark" float-label="Fecha de término" align="center"></q-datetime>
                </q-field>
              </div> -->
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="views.update">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Licitaciones" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el label="Editar licitación"/>
              </q-breadcrumbs>
            </div>
          </div>
           <div class="col-sm-6 pull-right">
                <div class="col-xs-12 col-sm-12 col-md-12 pull-left" >
                  <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
                  <q-btn @click="create_from()" class="btn_guardar" icon-right="folder" :loading="loadingButton" style="margin-right: 10px;" v-if="form.fields.status === 'ADJUDICADA'">Generar proyecto</q-btn>
                  <q-btn @click="actualizarLicitacion()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
                </div>
              </div>
        </div>
      </div>
      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-px-md">
            <div class="row q-pa-md">
              General
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="fas fa-tag" icon-color="dark" :error="$v.form.fields.folio.$error" error-label="Ingrese el folio">
                  <q-input upper-case v-model="form.fields.folio" type="text" inverted color="dark" float-label="Folio" maxlength="20" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-microchip" icon-color="dark">
                  <q-select v-model="form.fields.recurso_id" inverted color="dark" float-label="Proyecto" :options="recursosOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-info" icon-color="dark">
                  <q-select v-model="form.fields.status" inverted color="dark" float-label="Estatus" :options="form.selectStatus"></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-microchip" icon-color="dark" :error="$v.form.fields.entidad_id.$error" error-label="Elija una entidad">
                  <q-select v-model="form.fields.entidad_id" inverted color="dark" float-label="Entidad" :options="entidadesOptions" filter/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-12">
                <q-field icon="label" icon-color="dark">
                  <q-input upper-case v-model="form.fields.observaciones_creada" type="text" inverted color="dark" float-label="Observaciones creada" v-if="form.fields.status === 'CREADA'"/>
                  <q-input upper-case v-model="form.fields.observaciones_proceso" type="text" inverted color="dark" float-label="Observaciones proceso" v-if="form.fields.status === 'EN PROCESO'"/>
                  <q-input upper-case v-model="form.fields.observaciones_presentada" type="text" inverted color="dark" float-label="Observaciones presentada" v-if="form.fields.status === 'PRESENTADA'"/>
                  <q-input upper-case v-model="form.fields.observaciones_adjudicada" type="text" inverted color="dark" float-label="Observaciones adjudicada" v-if="form.fields.status === 'ADJUDICADA'"/>
                  <q-input upper-case v-model="form.fields.observaciones_no_adjudicada" type="text" inverted color="dark" float-label="Observaciones no adjudicada" v-if="form.fields.status === 'NO ADJUDICADA'"/>
                  <q-input upper-case v-model="form.fields.observaciones_cancelada" type="text" inverted color="dark" float-label="Observaciones cancelada" v-if="form.fields.status === 'CANCELADA'"/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="format_size" icon-color="dark" :error="$v.form.fields.titulo.$error" error-label="Ingrese el procedimiento">
                  <q-input upper-case v-model="form.fields.titulo" type="text" inverted color="dark" float-label="Procedimiento" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="format_size" icon-color="dark" :error="$v.form.fields.descripcion.$error" error-label="Ingrese la descripción">
                  <q-input upper-case v-model="form.fields.descripcion" type="text" inverted color="dark" float-label="Descripción" maxlength="100" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="fas fa-tag" icon-color="dark">
                  <q-input upper-case v-model="form.fields.contrato" type="text" inverted color="dark" float-label="Contrato" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-tag" icon-color="dark">
                  <q-input upper-case v-model="form.fields.orden_compra" type="text" inverted color="dark" float-label="Orden de compra" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <div class="icono_field">
                  <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                    <q-input v-model="form.fields.monto_inicial" v-money="money" inverted color="dark" float-label="Monto inicial" suffix="MXN"></q-input>
                  </q-field>
                </div>
              </div>
              <div class="col-sm-3">
                <div class="icono_field">
                  <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                    <q-input v-model="form.fields.monto_adjudicado" v-money="money" inverted color="dark" float-label="Monto adjudicado" suffix="MXN"></q-input>
                  </q-field>
                </div>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field  icon="fas fa-user" icon-color="dark" :error="$v.form.fields.comprador.$error" error-label="Texto demasiado largo">
                  <q-input upper-case v-model="form.fields.comprador" inverted color="dark" float-label="Comprador" maxlength="200"></q-input>
                </q-field>
              </div>
              <!-- </div>
              <div class="row q-mt-lg"> -->
              <div class="col-sm-3" style="margin-top:5px;">
                <q-field icon="fas fa-file-alt" icon-color="dark" helper="Anticipo">
                  <q-toggle v-model="form.fields.campo_anticipo" color="green" checked-icon="fas fa-check" unchecked-icon="fas fa-times"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <div class="icono_field">
                  <q-field  icon="fas fa-dollar-sign" icon-color="dark" v-if="form.fields.campo_anticipo === true">
                    <!-- <q-input v-model="form.fields.porcentaje_anticipo" type="number" inverted color="dark" float-label="Porcentaje anticipo" suffix="%"></q-input> -->
                    <q-input align="right" :decimals="0" numeric-keyboard-toggle v-model="form.fields.porcentaje_anticipo" type="number" inverted color="dark" float-label="Porcentaje anticipo" suffix="%"></q-input>
                  </q-field>
                </div>
              </div>
              <div class="col-sm-3">
                <div class="icono_field">
                  <q-field  icon="fas fa-dollar-sign" icon-color="dark" v-if="form.fields.campo_anticipo === true">
                    <q-input v-model="form.fields.monto_anticipo" v-money="money" inverted color="dark" float-label="Monto anticipo" suffix="MXN"></q-input>
                  </q-field>
                  <!-- <q-field  icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form.fields.monto.$error" error-label="Ingrese el monto ($0.00 - $1,000,000,000.00)" float-label="Monto adjudicado">
                    <money v-model="form.fields.monto" v-bind="money" style="height:53px;width:100%;margin-bottom:2px;" v-bind:class="{ moneyBien: !($v.form.fields.monto.$error), moneyError: $v.form.fields.monto.$error }" @input="$v.form.fields.monto.$touch"></money>
                  </q-field> -->
                </div>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field  icon="fas fa-user" icon-color="dark">
                  <q-input upper-case v-model="form.fields.responsable" inverted color="dark" float-label="Responsable licitación"></q-input>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field  icon="fas fa-user" icon-color="dark">
                  <q-input upper-case v-model="form.fields.responsable_gdp" inverted color="dark" float-label="Responsable GDP"></q-input>
                </q-field>
              </div>
            </div>
            <div class="row q-pa-md">
              Empresa concursante
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fields.empresa_id.$error" error-label="Elija una empresa">
                  <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa concursante" :options="empresasOptions" filter @input="concursante()"/>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field  icon="fas fa-building" icon-color="dark">
                  <q-input upper-case v-model="form.fields.empresa" inverted color="dark" float-label="Empresa aliada" maxlength="500"></q-input>
                </q-field>
              </div>
            </div>
            <div class="row q-pa-md">
              Empresas de respaldo
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field icon="fas fa-building" icon-color="dark">
                  <q-select v-model="form.fields.respaldo1" inverted color="dark" float-label="Empresa de respaldo 1" :options="empresasOptions" filter @input="r1()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-building" icon-color="dark">
                  <q-select v-model="form.fields.respaldo2" inverted color="dark" float-label="Empresa de respaldo 2" :options="empresasOptions" filter @input="r2()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-building" icon-color="dark">
                  <q-select v-model="form.fields.respaldo3" inverted color="dark" float-label="Empresa de respaldo 3" :options="empresasOptions" filter @input="r3()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-building" icon-color="dark">
                  <q-select v-model="form.fields.respaldo4" inverted color="dark" float-label="Empresa de respaldo 4" :options="empresasOptions" filter @input="r4()"/>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-3">
                <q-field  icon="fas fa-building" icon-color="dark">
                  <q-input :readonly="form.fields.respaldo1 !== 0" upper-case v-model="form.fields.empresa_respaldo_1" inverted color="dark" float-label="Empresa aliada" maxlength="500"></q-input>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-building" icon-color="dark">
                  <q-input :readonly="form.fields.respaldo2 !== 0" upper-case v-model="form.fields.empresa_respaldo_2" inverted color="dark" float-label="Empresa aliada" maxlength="500"></q-input>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-building" icon-color="dark">
                  <q-input :readonly="form.fields.respaldo3 !== 0" upper-case v-model="form.fields.empresa_respaldo_3" inverted color="dark" float-label="Empresa aliada" maxlength="500"></q-input>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-building" icon-color="dark">
                  <q-input :readonly="form.fields.respaldo4 !== 0" upper-case v-model="form.fields.empresa_respaldo_4" inverted color="dark" float-label="Empresa aliada" maxlength="500"></q-input>
                </q-field>
              </div>
            </div>
            <div class="row q-pa-md">
              Fechas
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-4">
                <q-field icon="date_range" icon-color="dark" :error="$v.form.fields.fecha_inicio.$error" error-label="Ingrese la fecha de firma">
                  <q-datetime v-model="form.fields.fecha_inicio" type="date" inverted color="dark" float-label="Fecha de inicio" align="center" @input="cambiaFechaInicio()"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-4" v-if="form.fields.fecha_inicio==='' || form.fields.fecha_inicio===null">
                <q-field icon="date_range" icon-color="dark">
                  <q-datetime disabled readonly v-model="form.fields.fecha_presentacion" type="date" inverted color="dark" float-label="Fecha presentación" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-4" v-else>
                <q-field icon="date_range" icon-color="dark">
                  <q-datetime v-model="form.fields.fecha_presentacion" type="date" inverted color="dark" float-label="Fecha presentación" align="center" @input="cambiaFechaIFallo()"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-4" v-if="form.fields.fecha_presentacion==='' || form.fields.fecha_presentacion===null">
                <q-field icon="date_range" icon-color="dark">
                  <q-datetime disabled readonly v-model="form.fields.fecha_fallo" type="date" inverted color="dark" float-label="Fecha fallo" align="center"></q-datetime>
                </q-field>
              </div>
              <div class="col-sm-4" v-else>
                <q-field icon="date_range" icon-color="dark">
                  <q-datetime v-model="form.fields.fecha_fallo" type="date" inverted color="dark" float-label="Fecha fallo" align="center"></q-datetime>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
            </div>
            <div>
              <q-tabs class="shadow-1" inverted animated swipeable color="light" align="justify" style="margin-top: 30px;">
                <q-tab default name="bases" slot="title" icon="add_box" label="Bases" @click="documentos()"/>
                <q-tab name="eventos" slot="title" icon="date_range" label="Eventos" @click="eventos()"/>
                <q-tab name="requisitos" slot="title" icon="attachment" label="Requisitos" @click="requisitos()"/>
                <q-tab name="perfiles" slot="title" icon="person" label="Perfiles expertos" @click="perfiles()"/>

                <div class="tabcontent">
                  <q-tab-pane name="bases"> <!-- -------------------TAB BASES---------------------- -->
                    <div class="row q-mt-lg">
                      <div class="col-sm-4">
                        <q-field icon="description" icon-color="dark" :error="$v.form_bases.fields.nombre_documento.$error" error-label="Ingrese el nombre del archivo">
                          <q-input v-model="form_bases.fields.nombre_documento" type="text" inverted color="dark" float-label="Nombre de documento" maxlength="50" />
                        </q-field>
                      </div>
                      <div class="col-sm-8">
                        <q-field icon="insert_comment" icon-color="dark">
                          <q-input v-model="form_bases.fields.observaciones_archivo" type="text" inverted color="dark" float-label="Observaciones" maxlength="1000" />
                        </q-field>
                      </div>
                    </div>
                    <div class="row q-mt-lg">
                      <div class="col-sm-12 pull-right">
                        <input style="display:none" inverted color="dark" stack-label="Logo" @input="checkFile()" type="file" name="" value="" ref="fileInputFormato" accept=".jpg,.jpeg,.png,.pdf,.docx" />
                        <q-btn v-if="showSelectedFile !== true && !this.form_bases.editar"  @click="$refs.fileInputFormato.click()" color="tertiary" icon="fa fa-upload" :loading="loadingButton">&nbsp; Examinar</q-btn>
                        <q-btn v-if="showSelectedFile === true && !this.form_bases.editar" @click="$refs.fileInputFormato.click()" color="green" icon="fa fa-check-circle" :loading="loadingButton">&nbsp; Examinar</q-btn>
                        <q-btn v-if="!this.form_bases.editar && !showSelectedFile" @click="agregar()" class="btn_guardar" icon-right="save" :loading="loadingButton">Agregar</q-btn>
                        <q-btn v-if="!this.form_bases.editar && showSelectedFile" @click="uploadFormato()" class="btn_guardar" icon-right="save" :loading="loadingButton">Agregar</q-btn>
                        <q-btn v-if="this.form_bases.editar" @click="updateFormato()" class="btn_guardar" icon-right="save" :loading="loadingButton">Actualizar</q-btn>
                      </div>
                    </div>
                    <div class="row q-mt-sm" style="margin-top:20px;">
                      <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                        <q-table id="sticky-table-newstyle"
                          :data="form_bases.data"
                          :columns="form_bases.columns"
                          :selected.sync="form_bases.selected"
                          :filter="form_bases.filter"
                          color="positive"
                          title=""
                          :dense="true"
                          :pagination.sync="form_bases.pagination"
                          :loading="form_bases.loading"
                          :rows-per-page-options="form_bases.rowsOptions">
                          <template slot="body" slot-scope="props">
                            <q-tr :props="props">
                              <q-td key="nombre_documento" :props="props">{{ props.row.nombre_documento }}</q-td>
                              <q-td key="descripcion" :props="props">{{ props.row.descripcion }}</q-td>
                              <q-td key="name" :props="props">{{ props.row.name }}</q-td>
                              <q-td key="acciones" :props="props">
                                <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="fileInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadImageFile()" />
                                <q-btn small flat @click="selectedFile(props.row)" color="green-9" :loading="loadingButton">
                                  <q-icon name="cloud_upload" />&nbsp;
                                </q-btn>
                                <!-- <q-btn small flat @click="descargarFormato(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name===null)">
                                  <q-tooltip>Descargar</q-tooltip>
                                </q-btn> -->
                                <q-btn small flat :icon="props.row.icon" :color="props.row.color">
                                    <q-popover>
                                      <q-list link separator class="scroll" style="min-width: 100px">
                                        <q-item v-close-overlay @click.native="verFormato(props.row)" v-if="props.row.doc_type !== 'docx' && props.row.doc_type !== 'xml' && props.row.doc_type !== 'xlsx'">
                                          <q-item-main label="Ver"/>
                                        </q-item>
                                        <q-item v-close-overlay @click.native="descargarFormato(props.row)">
                                          <q-item-main label="Descargar"/>
                                        </q-item>
                                        <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                          <q-item-main label="Eliminar"/>
                                        </q-item> -->
                                      </q-list>
                                    </q-popover>
                                  </q-btn>
                                <q-btn small flat @click="updateFormatoBase(props.row)" color="blue" icon="edit">
                                  <q-tooltip>Editar</q-tooltip>
                                </q-btn>
                                <q-btn small flat @click="deleteFormatoBase(props.row)" color="negative" icon="delete">
                                  <q-tooltip>Eliminar</q-tooltip>
                                </q-btn>
                              </q-td>
                            </q-tr>
                          </template>
                        </q-table>
                        <q-inner-loading :visible="form_bases.loading">
                          <q-spinner-dots size="64px" color="primary" />
                        </q-inner-loading>
                      </div>
                    </div>
                  </q-tab-pane> <!-- -------------------------TAB EVENTOS ------------------------- -->
                  <q-tab-pane name="eventos">
                    <div class="row q-mt-lg">
                      <div class="col-sm-4">
                        <q-field icon="label" icon-color="dark" :error="$v.form_eventos.fields.evento.$error" error-label="Ingrese el nombre del evento">
                          <q-input v-model="form_eventos.fields.evento" type="text" inverted color="dark" float-label="Nombre del evento" maxlength="100" />
                        </q-field>
                      </div>
                      <div class="col-sm-4">
                        <q-field icon="date_range" icon-color="dark" :error="$v.form_eventos.fields.fecha_evento.$error" error-label="Ingrese la fecha del evento">
                          <q-datetime v-model="form_eventos.fields.fecha_evento" type="datetime" inverted color="dark" float-label="Fecha del evento" align="center" @change="cambiaFecha()"></q-datetime>
                        </q-field>
                      </div>
                    </div>
                    <div class="row q-mt-lg">
                      <div class="col-sm-12 pull-right">
                        <q-btn v-if="!this.form_eventos.editar" @click="agregarEvento()" class="btn_guardar" icon-right="save" :loading="loadingButton">Agregar</q-btn>
                        <q-btn v-if="this.form_eventos.editar" @click="updateEvento()" class="btn_guardar" icon-right="done" :loading="loadingButton">Actualizar</q-btn>
                      </div>
                    </div>

                    <div class="row q-mt-sm" style="margin-top:20px;">
                      <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                        <q-table id="sticky-table-newstyle"
                          :data="form_eventos.data"
                          :columns="form_eventos.columns"
                          :selected.sync="form_eventos.selected"
                          :filter="form_eventos.filter"
                          color="positive"
                          title=""
                          :dense="true"
                          :pagination.sync="form_eventos.pagination"
                          :loading="form_eventos.loading"
                          :rows-per-page-options="form_eventos.rowsOptions">
                          <template slot="body" slot-scope="props">
                            <q-tr :props="props">
                              <q-td key="evento" :props="props">{{ props.row.evento }}</q-td>
                              <q-td key="fecha_evento" :props="props">{{ props.row.fecha_evento }}</q-td>
                              <q-td key="name" :props="props">
                                <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="eventoInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadEventFile()" />
                                <q-btn small flat @click="selectedFileEvento(props.row)" color="green-9" :loading="loadingButton">
                                  <q-icon name="cloud_upload" />&nbsp;
                                </q-btn>
                                <!-- <q-btn small flat @click="descargarFormatoEvento(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name===null)">
                                  <q-tooltip>Descargar</q-tooltip>
                                </q-btn> -->
                                <q-btn small flat :icon="props.row.icon" :color="props.row.color" v-if="!(props.row.name===null)">
                                    <q-popover>
                                      <q-list link separator class="scroll" style="min-width: 100px">
                                        <q-item v-close-overlay @click.native="verFormatoEvento(props.row)" v-if="props.row.doc_type !== 'docx' && props.row.doc_type !== 'xml' && props.row.doc_type !== 'xlsx'">
                                          <q-item-main label="Ver"/>
                                        </q-item>
                                        <q-item v-close-overlay @click.native="descargarFormatoEvento(props.row)">
                                          <q-item-main label="Descargar"/>
                                        </q-item>
                                        <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                          <q-item-main label="Eliminar"/>
                                        </q-item> -->
                                      </q-list>
                                    </q-popover>
                                  </q-btn>
                              </q-td>
                              <q-td key="documentos" :props="props">
                                <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="docEventoInput" accept=".png, .jpg, .jpeg, .docx, .pdf" @change="uploadDocsEvento()" />
                                <q-btn small flat @click="selectedEvento(props.row)" color="green-9" :loading="loadingButton">
                                  <q-icon name="cloud_upload" />&nbsp;
                                  <q-tooltip>Subir documento</q-tooltip>
                                </q-btn>
                                <div style="display: inline;" v-for="doc in props.row.documentos" :key="doc.id">
                                  <q-btn small flat :icon="doc.icon" :color="doc.color">
                                    <q-tooltip>{{ doc.archivo }}</q-tooltip>
                                    <q-popover>
                                      <q-list link separator class="scroll" style="min-width: 100px">
                                        <q-item v-close-overlay @click.native="verDocumentoEvento(doc.id, doc.extension)" v-if="doc.extension !== 'docx' && doc.extension !== 'xml' && doc.extension !== 'xlsx'">
                                          <q-item-main label="Ver"/>
                                        </q-item>
                                        <q-item v-close-overlay @click.native="descargarDocumentoEvento(doc.id, doc.extension)">
                                          <q-item-main label="Descargar"/>
                                        </q-item>
                                        <q-item v-close-overlay @click.native="deleteDocsEvento(doc.id)">
                                          <q-item-main label="Eliminar"/>
                                        </q-item>
                                      </q-list>
                                    </q-popover>
                                  </q-btn>
                                </div>
                              </q-td>
                              <q-td key="acciones" :props="props">
                                <q-btn small flat @click="editEvento(props.row)" color="blue" icon="edit">
                                  <q-tooltip>Editar</q-tooltip>
                                </q-btn>
                                <q-btn small flat @click="deleteEvento(props.row)" color="negative" icon="delete">
                                  <q-tooltip>Eliminar</q-tooltip>
                                </q-btn>
                              </q-td>
                            </q-tr>
                          </template>
                        </q-table>
                        <q-inner-loading :visible="form_eventos.loading">
                          <q-spinner-dots size="64px" color="primary" />
                        </q-inner-loading>
                      </div>
                    </div>
                  </q-tab-pane>
                  <q-tab-pane name="requisitos">
                    <q-collapsible v-model="open_requisitos" icon="label" label="Requisitos (Factores de éxito)">
                      <div class="row q-mt-lg">
                        <div class="col-sm-8">
                          <q-field icon="description" icon-color="dark" :error="$v.form_requisitos.fields.requisito.$error" error-label="Ingrese el nombre del requisito">
                            <q-input v-model="form_requisitos.fields.requisito" type="text" inverted color="dark" float-label="Nombre del requisito" maxlength="100" />
                          </q-field>
                        </div>
                        <div class="col-sm-4">
                          <q-field icon="date_range" icon-color="dark" :error="$v.form_requisitos.fields.fecha.$error" error-label="Ingrese la fecha">
                            <q-datetime v-model="form_requisitos.fields.fecha" type="date" inverted color="dark" float-label="Fecha" align="center"></q-datetime>
                          </q-field>
                        </div>
                      </div>
                      <div class="row q-mt-lg">
                        <div class="col-sm-12">
                          <q-field icon="insert_comment" icon-color="dark" :error="$v.form_requisitos.fields.descripcion.$error" error-label="Ingrese la descripción">
                            <q-input v-model="form_requisitos.fields.descripcion" type="text" inverted color="dark" float-label="Descripción" maxlength="100" />
                          </q-field>
                        </div>
                      </div>
                      <div class="row q-mt-lg">
                        <div class="col-sm-12 pull-right">
                          <q-btn v-if="!this.form_requisitos.editar" @click="agregarRequisito()" class="btn_guardar" icon-right="save" :loading="loadingButton">
                          Agregar</q-btn>
                          <q-btn v-if="this.form_requisitos.editar" @click="actualizarRequisito()" class="btn_guardar" icon-right="done" :loading="loadingButton">
                          Actualizar</q-btn>
                        </div>
                      </div>
                      <div class="row q-mt-sm" style="margin-top:20px;">
                        <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                          <q-table id="sticky-table-newstyle"
                            :data="form_requisitos.data"
                            :columns="form_requisitos.columns"
                            :selected.sync="form_requisitos.selected"
                            :filter="form_requisitos.filter"
                            color="positive"
                            title=""
                            :dense="true"
                            :pagination.sync="form_requisitos.pagination"
                            :loading="form_requisitos.loading"
                            :rows-per-page-options="form_requisitos.rowsOptions">
                            <template slot="body" slot-scope="props">
                              <q-tr :props="props">
                                <q-td key="requisito" :props="props">{{ props.row.requisito }}</q-td>
                                <q-td key="descripcion" :props="props">{{ props.row.descripcion }}</q-td>
                                <q-td key="fecha" :props="props">{{ props.row.fecha }}</q-td>
                                <q-td key="name0" :props="props">
                                  <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="requisitoInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadRequisitoFile()" />
                                  <q-btn small flat @click="selectedFileDocumento(props.row)" color="green-9" :loading="loadingButton">
                                    <q-icon name="cloud_upload" />&nbsp;
                                    <q-tooltip>Subir Archivo</q-tooltip>
                                  </q-btn>
                                  <!-- <q-btn small flat @click="descargarRequisito0(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name0===null)">
                                    <q-tooltip>Descargar: {{ props.row.name0 }}</q-tooltip>
                                  </q-btn> -->
                                  <q-btn small flat :icon="props.row.icon0" :color="props.row.color0" v-if="!(props.row.name0===null)">
                                    <q-tooltip>{{ props.row.name0 }}</q-tooltip>
                                      <q-popover>
                                        <q-list link separator class="scroll" style="min-width: 100px">
                                          <q-item v-close-overlay @click.native="verFormatoRequisito0(props.row)" v-if="props.row.doc_type0 !== 'docx' && props.row.doc_type0 !== 'xml' && props.row.doc_type0 !== 'xlsx'">
                                            <q-item-main label="Ver"/>
                                          </q-item>
                                          <q-item v-close-overlay @click.native="descargarRequisito0(props.row)">
                                            <q-item-main label="Descargar"/>
                                          </q-item>
                                          <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                            <q-item-main label="Eliminar"/>
                                          </q-item> -->
                                        </q-list>
                                      </q-popover>
                                    </q-btn>
                                </q-td>
                                <q-td key="responsable" :props="props">
                                  <q-select v-model="props.row.responsable" @change="cambiarResponsable(props.row)" :options="usuariosOptions" filter/>
                                </q-td>
                                <q-td key="name1" :props="props">
                                  <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="requisitoInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadRequisitoFile()" />
                                  <q-btn small flat @click="selectedFileDocumento1(props.row)" color="green-9" :loading="loadingButton">
                                    <q-icon name="cloud_upload" />&nbsp;
                                    <q-tooltip>Subir Archivo</q-tooltip>
                                  </q-btn>
                                  <!-- <q-btn small flat @click="descargarRequisito1(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name1===null)">
                                    <q-tooltip>Descargar: {{ props.row.name1 }}</q-tooltip>
                                  </q-btn> -->
                                  <q-btn small flat :icon="props.row.icon1" :color="props.row.color1" v-if="!(props.row.name1===null)">
                                    <q-tooltip>{{ props.row.name1 }}</q-tooltip>
                                      <q-popover>
                                        <q-list link separator class="scroll" style="min-width: 100px">
                                          <q-item v-close-overlay @click.native="verFormatoRequisito1(props.row)" v-if="props.row.doc_type1 !== 'docx' && props.row.doc_type1 !== 'xml' && props.row.doc_type1 !== 'xlsx'">
                                            <q-item-main label="Ver"/>
                                          </q-item>
                                          <q-item v-close-overlay @click.native="descargarRequisito1(props.row)">
                                            <q-item-main label="Descargar"/>
                                          </q-item>
                                          <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                            <q-item-main label="Eliminar"/>
                                          </q-item> -->
                                        </q-list>
                                      </q-popover>
                                    </q-btn>
                                </q-td>
                                <q-td key="responsable1" :props="props">
                                  <q-select v-model="props.row.responsable1" @change="cambiarResponsable(props.row)" :options="usuariosOptions" filter/>
                                </q-td>
                                <q-td key="name2" :props="props">
                                  <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="requisitoInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadRequisitoFile()" />
                                  <q-btn small flat @click="selectedFileDocumento2(props.row)" color="green-9" :loading="loadingButton">
                                    <q-icon name="cloud_upload" />&nbsp;
                                    <q-tooltip>Subir Archivo</q-tooltip>
                                  </q-btn>
                                  <!-- <q-btn small flat @click="descargarRequisito2(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name2===null)">
                                    <q-tooltip>Descargar: {{ props.row.name2 }}</q-tooltip>
                                  </q-btn> -->
                                  <q-btn small flat :icon="props.row.icon2" :color="props.row.color2" v-if="!(props.row.name2===null)">
                                    <q-tooltip>{{ props.row.name2 }}</q-tooltip>
                                      <q-popover>
                                        <q-list link separator class="scroll" style="min-width: 100px">
                                          <q-item v-close-overlay @click.native="verFormatoRequisito2(props.row)" v-if="props.row.doc_type2 !== 'docx' && props.row.doc_type2 !== 'xml' && props.row.doc_type2 !== 'xlsx'">
                                            <q-item-main label="Ver"/>
                                          </q-item>
                                          <q-item v-close-overlay @click.native="descargarRequisito2(props.row)">
                                            <q-item-main label="Descargar"/>
                                          </q-item>
                                          <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                            <q-item-main label="Eliminar"/>
                                          </q-item> -->
                                        </q-list>
                                      </q-popover>
                                    </q-btn>
                                </q-td>
                                <q-td key="responsable2" :props="props">
                                  <q-select v-model="props.row.responsable2" @change="cambiarResponsable(props.row)" :options="usuariosOptions" filter/>
                                </q-td>
                                <q-td key="name3" :props="props">
                                  <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="requisitoInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadRequisitoFile()" />
                                  <q-btn small flat @click="selectedFileDocumento3(props.row)" color="green-9" :loading="loadingButton">
                                    <q-icon name="cloud_upload" />&nbsp;
                                    <q-tooltip>Subir Archivo</q-tooltip>
                                  </q-btn>
                                  <!-- <q-btn small flat @click="descargarRequisito3(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name3===null)">
                                    <q-tooltip>Descargar: {{ props.row.name3 }}</q-tooltip>
                                  </q-btn> -->
                                  <q-btn small flat :icon="props.row.icon3" :color="props.row.color3" v-if="!(props.row.name3===null)">
                                    <q-tooltip>{{ props.row.name3 }}</q-tooltip>
                                      <q-popover>
                                        <q-list link separator class="scroll" style="min-width: 100px">
                                          <q-item v-close-overlay @click.native="verFormatoRequisito3(props.row)" v-if="props.row.doc_type3 !== 'docx' && props.row.doc_type3 !== 'xml' && props.row.doc_type3 !== 'xlsx'">
                                            <q-item-main label="Ver"/>
                                          </q-item>
                                          <q-item v-close-overlay @click.native="descargarRequisito3(props.row)">
                                            <q-item-main label="Descargar"/>
                                          </q-item>
                                          <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                            <q-item-main label="Eliminar"/>
                                          </q-item> -->
                                        </q-list>
                                      </q-popover>
                                    </q-btn>
                                </q-td>
                                <q-td key="responsable3" :props="props">
                                  <q-select v-model="props.row.responsable3" @change="cambiarResponsable(props.row)" :options="usuariosOptions" filter/>
                                </q-td>
                                <q-td key="name4" :props="props">
                                  <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="requisitoInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadRequisitoFile()" />
                                  <q-btn small flat @click="selectedFileDocumento4(props.row)" color="green-9" :loading="loadingButton">
                                    <q-icon name="cloud_upload" />&nbsp;
                                    <q-tooltip>Subir Archivo</q-tooltip>
                                  </q-btn>
                                  <!-- <q-btn small flat @click="descargarRequisito4(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name4===null)">
                                    <q-tooltip>Descargar: {{ props.row.name4 }}</q-tooltip>
                                  </q-btn> -->
                                  <q-btn small flat :icon="props.row.icon4" :color="props.row.color4" v-if="!(props.row.name4===null)">
                                    <q-tooltip>{{ props.row.name4 }}</q-tooltip>
                                      <q-popover>
                                        <q-list link separator class="scroll" style="min-width: 100px">
                                          <q-item v-close-overlay @click.native="verFormatoRequisito4(props.row)" v-if="props.row.doc_type4 !== 'docx' && props.row.doc_type4 !== 'xml' && props.row.doc_type4 !== 'xlsx'">
                                            <q-item-main label="Ver"/>
                                          </q-item>
                                          <q-item v-close-overlay @click.native="descargarRequisito4(props.row)">
                                            <q-item-main label="Descargar"/>
                                          </q-item>
                                          <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                            <q-item-main label="Eliminar"/>
                                          </q-item> -->
                                        </q-list>
                                      </q-popover>
                                    </q-btn>
                                </q-td>
                                <q-td key="responsable4" :props="props">
                                  <q-select v-model="props.row.responsable4" @change="cambiarResponsable(props.row)" :options="usuariosOptions" filter/>
                                </q-td>
                                <q-td key="acciones" :props="props">
                                  <q-btn small flat @click="editRequisito(props.row)" color="blue" icon="edit">
                                    <q-tooltip>Editar</q-tooltip>
                                  </q-btn>
                                  <q-btn small flat @click="eliminarRequisito(props.row)" color="negative" icon="delete">
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
                    </q-collapsible>
                    <q-collapsible v-model="open_propuestas" icon="label" label="Propuestas finales">
                      <div class="row q-mt-lg">
                        <div class="col-sm-8">
                          <q-field icon="description" icon-color="dark" :error="$v.form_propuestas.fields.propuesta.$error" error-label="Ingrese el nombre de la propuesta">
                            <q-input v-model="form_propuestas.fields.propuesta" type="text" inverted color="dark" float-label="Nombre de la propuesta" maxlength="100" />
                          </q-field>
                        </div>
                        <div class="col-sm-4">
                          <q-field icon="date_range" icon-color="dark" :error="$v.form_propuestas.fields.fecha.$error" error-label="Ingrese la fecha">
                            <q-datetime v-model="form_propuestas.fields.fecha" type="date" inverted color="dark" float-label="Fecha" align="center"></q-datetime>
                          </q-field>
                        </div>
                      </div>
                      <div class="row q-mt-lg">
                        <div class="col-sm-12">
                          <q-field icon="insert_comment" icon-color="dark" :error="$v.form_propuestas.fields.descripcion.$error" error-label="Ingrese la descripción">
                            <q-input v-model="form_propuestas.fields.descripcion" type="text" inverted color="dark" float-label="Descripción" maxlength="100" />
                          </q-field>
                        </div>
                      </div>
                      <div class="row q-mt-lg">
                        <div class="col-sm-12 pull-right">
                          <q-btn v-if="!this.form_propuestas.editar" @click="agregarPropuesta()" class="btn_guardar" icon-right="save" :loading="loadingButton">
                          Agregar</q-btn>
                          <q-btn v-if="this.form_propuestas.editar" @click="actualizarPropuesta()" class="btn_guardar" icon-right="done" :loading="loadingButton">
                          Actualizar</q-btn>
                        </div>
                      </div>

                      <div class="row q-mt-sm" style="margin-top:20px;">
                        <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                          <q-table id="sticky-table-newstyle"
                            :data="form_propuestas.data"
                            :columns="form_propuestas.columns"
                            :selected.sync="form_propuestas.selected"
                            :filter="form_propuestas.filter"
                            color="positive"
                            title=""
                            :dense="true"
                            :pagination.sync="form_propuestas.pagination"
                            :loading="form_propuestas.loading"
                            :rows-per-page-options="form_propuestas.rowsOptions">
                            <template slot="body" slot-scope="props">
                              <q-tr :props="props">
                                <q-td key="propuesta" :props="props">{{ props.row.propuesta }}</q-td>
                                <q-td key="descripcion" :props="props">{{ props.row.descripcion }}</q-td>
                                <q-td key="fecha" :props="props">{{ props.row.fecha }}</q-td>
                                <q-td key="name0" :props="props">
                                  <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="propuestaInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadPropuestaFile()" />
                                  <q-btn small flat @click="selectedFileDocumentoPropuesta(props.row)" color="green-9" :loading="loadingButton">
                                    <q-icon name="cloud_upload" />&nbsp;
                                    <q-tooltip>Subir Archivo</q-tooltip>
                                  </q-btn>
                                  <!-- <q-btn small flat @click="descargarPropuesta0(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name0===null)">
                                    <q-tooltip>Descargar: {{ props.row.name0 }}</q-tooltip>
                                  </q-btn> -->
                                  <q-btn small flat :icon="props.row.icon0" :color="props.row.color0" v-if="!(props.row.name0===null)">
                                    <q-tooltip>{{ props.row.name0 }}</q-tooltip>
                                      <q-popover>
                                        <q-list link separator class="scroll" style="min-width: 100px">
                                          <q-item v-close-overlay @click.native="verFormatoPropuesta0(props.row)" v-if="props.row.doc_type0.toLowerCase() !== 'docx' && props.row.doc_type0 !== 'xml' && props.row.doc_type0 !== 'xlsx'">
                                            <q-item-main label="Ver"/>
                                          </q-item>
                                          <q-item v-close-overlay @click.native="descargarPropuesta0(props.row)">
                                            <q-item-main label="Descargar"/>
                                          </q-item>
                                          <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                            <q-item-main label="Eliminar"/>
                                          </q-item> -->
                                        </q-list>
                                      </q-popover>
                                    </q-btn>
                                </q-td>
                                <q-td key="responsable" :props="props">
                                  <q-select v-model="props.row.responsable" @change="cambiarResponsablePropuesta(props.row)" :options="usuariosOptions" filter/>
                                </q-td>
                                <q-td key="name1" :props="props">
                                  <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="requisitoInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadRequisitoFile()" />
                                  <q-btn small flat @click="selectedFileDocumentoPropuesta1(props.row)" color="green-9" :loading="loadingButton">
                                    <q-icon name="cloud_upload" />&nbsp;
                                    <q-tooltip>Subir Archivo</q-tooltip>
                                  </q-btn>
                                  <!-- <q-btn small flat @click="descargarPropuesta1(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name1===null)">
                                    <q-tooltip>Descargar: {{ props.row.name1 }}</q-tooltip>
                                  </q-btn> -->
                                  <q-btn small flat :icon="props.row.icon1" :color="props.row.color1" v-if="!(props.row.name1===null)">
                                    <q-tooltip>{{ props.row.name1 }}</q-tooltip>
                                      <q-popover>
                                        <q-list link separator class="scroll" style="min-width: 100px">
                                          <q-item v-close-overlay @click.native="verFormatoPropuesta1(props.row)" v-if="props.row.doc_type1.toLowerCase() !== 'docx' && props.row.doc_type1 !== 'xml' && props.row.doc_type1 !== 'xlsx'">
                                            <q-item-main label="Ver"/>
                                          </q-item>
                                          <q-item v-close-overlay @click.native="descargarPropuesta1(props.row)">
                                            <q-item-main label="Descargar"/>
                                          </q-item>
                                          <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                            <q-item-main label="Eliminar"/>
                                          </q-item> -->
                                        </q-list>
                                      </q-popover>
                                    </q-btn>
                                </q-td>
                                <q-td key="responsable1" :props="props">
                                  <q-select v-model="props.row.responsable1" @change="cambiarResponsablePropuesta(props.row)" :options="usuariosOptions" filter/>
                                </q-td>
                                <q-td key="name2" :props="props">
                                  <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="requisitoInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadRequisitoFile()" />
                                  <q-btn small flat @click="selectedFileDocumentoPropuesta2(props.row)" color="green-9" :loading="loadingButton">
                                    <q-icon name="cloud_upload" />&nbsp;
                                    <q-tooltip>Subir Archivo</q-tooltip>
                                  </q-btn>
                                  <!-- <q-btn small flat @click="descargarPropuesta2(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name2===null)">
                                    <q-tooltip>Descargar: {{ props.row.name2 }}</q-tooltip>
                                  </q-btn> -->
                                  <q-btn small flat :icon="props.row.icon2" :color="props.row.color2" v-if="!(props.row.name2===null)">
                                    <q-tooltip>{{ props.row.name2 }}</q-tooltip>
                                      <q-popover>
                                        <q-list link separator class="scroll" style="min-width: 100px">
                                          <q-item v-close-overlay @click.native="verFormatoPropuesta2(props.row)" v-if="props.row.doc_type2.toLowerCase() !== 'docx' && props.row.doc_type2 !== 'xml' && props.row.doc_type2 !== 'xlsx'">
                                            <q-item-main label="Ver"/>
                                          </q-item>
                                          <q-item v-close-overlay @click.native="descargarPropuesta2(props.row)">
                                            <q-item-main label="Descargar"/>
                                          </q-item>
                                          <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                            <q-item-main label="Eliminar"/>
                                          </q-item> -->
                                        </q-list>
                                      </q-popover>
                                    </q-btn>
                                </q-td>
                                <q-td key="responsable2" :props="props">
                                  <q-select v-model="props.row.responsable2" @change="cambiarResponsablePropuesta(props.row)" :options="usuariosOptions" filter/>
                                </q-td>
                                <q-td key="name3" :props="props">
                                  <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="requisitoInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadRequisitoFile()" />
                                  <q-btn small flat @click="selectedFileDocumentoPropuesta3(props.row)" color="green-9" :loading="loadingButton">
                                    <q-icon name="cloud_upload" />&nbsp;
                                    <q-tooltip>Subir Archivo</q-tooltip>
                                  </q-btn>
                                  <!-- <q-btn small flat @click="descargarPropuesta3(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name3===null)">
                                    <q-tooltip>Descargar: {{ props.row.name3 }}</q-tooltip>
                                  </q-btn> -->
                                  <q-btn small flat :icon="props.row.icon3" :color="props.row.color3" v-if="!(props.row.name3===null)">
                                    <q-tooltip>{{ props.row.name3 }}</q-tooltip>
                                      <q-popover>
                                        <q-list link separator class="scroll" style="min-width: 100px">
                                          <q-item v-close-overlay @click.native="verFormatoPropuesta3(props.row)" v-if="props.row.doc_type3.toLowerCase() !== 'docx' && props.row.doc_type3 !== 'xml' && props.row.doc_type3 !== 'xlsx'">
                                            <q-item-main label="Ver"/>
                                          </q-item>
                                          <q-item v-close-overlay @click.native="descargarPropuesta3(props.row)">
                                            <q-item-main label="Descargar"/>
                                          </q-item>
                                          <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                            <q-item-main label="Eliminar"/>
                                          </q-item> -->
                                        </q-list>
                                      </q-popover>
                                    </q-btn>
                                </q-td>
                                <q-td key="responsable3" :props="props">
                                  <q-select v-model="props.row.responsable3" @change="cambiarResponsablePropuesta(props.row)" :options="usuariosOptions" filter/>
                                </q-td>
                                <q-td key="name4" :props="props">
                                  <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="requisitoInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadRequisitoFile()" />
                                  <q-btn small flat @click="selectedFileDocumentoPropuesta4(props.row)" color="green-9" :loading="loadingButton">
                                    <q-icon name="cloud_upload" />&nbsp;
                                    <q-tooltip>Subir Archivo</q-tooltip>
                                  </q-btn>
                                  <!-- <q-btn small flat @click="descargarPropuesta4(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name4===null)">
                                    <q-tooltip>Descargar: {{ props.row.name4 }}</q-tooltip>
                                  </q-btn> -->
                                  <q-btn small flat :icon="props.row.icon4" :color="props.row.color4" v-if="!(props.row.name4===null)">
                                    <q-tooltip>{{ props.row.name4 }}</q-tooltip>
                                      <q-popover>
                                        <q-list link separator class="scroll" style="min-width: 100px">
                                          <q-item v-close-overlay @click.native="verFormatoPropuesta4(props.row)" v-if="props.row.doc_type4.toLowerCase() !== 'docx' && props.row.doc_type4 !== 'xml' && props.row.doc_type4 !== 'xlsx'">
                                            <q-item-main label="Ver"/>
                                          </q-item>
                                          <q-item v-close-overlay @click.native="descargarPropuesta4(props.row)">
                                            <q-item-main label="Descargar"/>
                                          </q-item>
                                          <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                            <q-item-main label="Eliminar"/>
                                          </q-item> -->
                                        </q-list>
                                      </q-popover>
                                    </q-btn>
                                </q-td>
                                <q-td key="responsable4" :props="props">
                                  <q-select v-model="props.row.responsable4" @change="cambiarResponsablePropuesta(props.row)" :options="usuariosOptions" filter/>
                                </q-td>
                                <q-td key="acciones" :props="props">
                                  <q-btn small flat @click="editPropuesta(props.row)" color="blue" icon="edit">
                                    <q-tooltip>Editar</q-tooltip>
                                  </q-btn>
                                  <q-btn small flat @click="eliminarPropuesta(props.row)" color="negative" icon="delete">
                                    <q-tooltip>Eliminar</q-tooltip>
                                  </q-btn>
                                </q-td>
                              </q-tr>
                            </template>
                          </q-table>
                          <q-inner-loading :visible="form_propuestas.loading">
                            <q-spinner-dots size="64px" color="primary" />
                          </q-inner-loading>
                        </div>
                      </div>
                    </q-collapsible>
                  </q-tab-pane>
                  <q-tab-pane name="perfiles">
                    <q-tabs class="shadow-1" inverted animated swipeable color="dark" align="justify">
                      <q-tab default name="busqueda" slot="title" icon="search" label="Búsqueda" @click="cargar()"/>
                      <q-tab name="perfiles_involucrados" slot="title" icon="person" label="Perfiles involucrados" @click="cargarPerfiles()"/>
                      <q-tab-pane name="busqueda">
                        <div class="row q-mt-lg">
                          <div class="col-sm-2">
                            <q-field icon="fas fa-globe" icon-color="dark">
                              <q-select v-model="form_busqueda.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()"></q-select>
                            </q-field>
                          </div>
                          <div class="col-sm-2">
                            <q-field icon="fas fa-map-pin" icon-color="dark">
                              <q-select v-model="form_busqueda.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="form_busqueda.municipiosOptions" filter></q-select>
                            </q-field>
                          </div>
                          <div class="col-sm-2">
                            <q-field icon="fas fa-globe" icon-color="dark">
                              <q-select v-model="form_busqueda.fields.area_id" inverted color="dark" float-label="Área" :options="areasOptions" filter></q-select>
                            </q-field>
                          </div>
                          <div class="col-sm-6">
                            <q-field icon="grade" icon-color="dark">
                              <q-chips-input v-model="form_busqueda.fields.aptitudes" inverted color="dark" float-label="Aptitudes" @duplicate="duplicate">
                                <q-autocomplete @search="search" @selected="selected"/>
                              </q-chips-input>
                            </q-field>
                          </div>
                          <div class="col-sm-4">
                            <q-field icon="school" icon-color="dark">
                              <q-input upper-case v-model="form_busqueda.fields.curso" type="text" inverted color="dark" float-label="Nombre del posgrado, curso adquirido o impartido" maxlength="300" />
                            </q-field>
                          </div>
                        </div>
                        <div class="row justify-end" style="text-align: right; padding-top: 10px;">
                          <q-btn @click="filtrarP()" inverted color="orange" icon-right="fas fa-filter" :loading="loadingButton">Filtrar</q-btn>
                          <q-btn @click="borrar_filtros()" color="red" icon="loop" style="margin-left:15px;">Limpiar</q-btn>
                        </div>
                        <div class="row q-mt-lg">
                          <div class="col-sm-12">
                            <q-search color="primary" v-model="form_busqueda.filter"/>
                          </div>
                          <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                            <q-table id="sticky-table-newstyle"
                              :data="form_busqueda.data"
                              :columns="form_busqueda.columns"
                              :selected.sync="form_busqueda.selected"
                              :filter="form_busqueda.filter"
                              color="positive"
                              title=""
                              :dense="true"
                              :pagination.sync="form_busqueda.pagination"
                              :loading="form_busqueda.loading"
                              :rows-per-page-options="form_busqueda.rowsOptions">
                              <template slot="body" slot-scope="props">
                                <q-tr :props="props">
                                  <q-td key="nombre_completo" :props="props">{{ props.row.nombre_completo }}</q-td>
                                  <q-td key="licenciatura" :props="props">{{ props.row.licenciatura }}</q-td>
                                  <q-td key="area" :props="props">{{ props.row.area }}</q-td>
                                  <q-td key="aptitudes" :props="props">
                                    <div v-for="apt in props.row.aptitudes" :key="apt.id" style="display: inline-grid; padding-right: 5px; padding-bottom: 10px;"><q-chip dense icon-right="grade" color="pink">{{ apt.aptitud }}</q-chip></div>
                                  </q-td>
                                  <q-td key="acciones" :props="props">
                                    <q-btn small flat @click="addPerfil(props.row)" color="blue-6" icon="check_circle">
                                      <q-tooltip>Agregar perfil a licitación</q-tooltip>
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
                      </q-tab-pane>
                      <q-tab-pane name="perfiles_involucrados">
                        <div class="row q-mt-lg">
                          <div class="col-md-12">
                            <q-search color="primary" v-model="form_perfiles_involucrados.filter"/>
                          </div>
                          <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                            <q-table id="sticky-table-newstyle"
                              :data="form_perfiles_involucrados.data"
                              :columns="form_perfiles_involucrados.columns"
                              :selected.sync="form_perfiles_involucrados.selected"
                              :filter="form_perfiles_involucrados.filter"
                              color="positive"
                              title=""
                              :dense="true"
                              :pagination.sync="form_perfiles_involucrados.pagination"
                              :loading="form_perfiles_involucrados.loading"
                              :rows-per-page-options="form_perfiles_involucrados.rowsOptions">
                              <template slot="body" slot-scope="props">
                                <q-tr :props="props">
                                  <q-td key="nombre" :props="props">{{ props.row.nombre }} {{ props.row.apellido_paterno }} {{ props.row.apellido_materno }} </q-td>
                                  <q-td key="licenciatura" :props="props">{{ props.row.licenciatura }}</q-td>
                                  <q-td key="area" :props="props">{{ props.row.area }}</q-td>
                                  <q-td key="participacion" :props="props">{{ props.row.participacion }}</q-td>
                                  <q-td key="acciones" :props="props">
                                    <q-btn small flat @click="deletePerfil(props.row)" color="red" icon="delete">
                                      <q-tooltip>Quitar perfil de licitación</q-tooltip>
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
                      </q-tab-pane>
                    </q-tabs>
                  </q-tab-pane>
                </div>
              </q-tabs>
            </div>
          </div>
        </div>
      </div>
    </div>

      <q-modal v-if="agregar_perfil_licitacion" style="background-color: rgba(0,0,0,0.7);" v-model="agregar_perfil_licitacion" :content-css="{width: '40vw', height: '200px'}">
        <q-modal-layout>
          <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
            <div class="col-sm-6">
              <q-toolbar-title>
                Agregar perfil
              </q-toolbar-title>
            </div>
            <div class="col-sm-6 pull-right">
              <q-btn
                flat
                round
                dense
                color="white"
                @click="agregar_perfil_licitacion = false"
                icon="fas fa-times-circle"
              />
            </div>
          </q-toolbar>
          <div class="row q-mt-lg" style="padding-right: 20px;">
            <div class="col-sm-12">
              <q-field icon="person" icon-color="dark">
                <q-input v-model="form_busqueda.fields.participacion" inverted color="dark" float-label="Participación" type="text"></q-input>
              </q-field>
            </div>
          </div>
          <div class="row justify-end" style="text-align: right; padding-top: 10px;padding-right: 20px;">
                <q-btn @click="agregarPerfil()" inverted color="green" icon-right="add" :loading="loadingButton">Agregar</q-btn>
              </div>
        </q-modal-layout>
      </q-modal>

      <q-modal no-backdrop-dismiss v-if="informacion" style="background-color: rgba(0,0,0,0.7);" v-model="informacion" :content-css="{minWidth: '90vw', minHeight: '600px'}">
        <q-modal-layout>
          <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
            <div class="col-sm-10">
              <q-toolbar-title>
                Licitación {{ this.form.codigo }}
              </q-toolbar-title>
            </div>
            <div class="col-sm-2 pull-right">
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
        </q-modal-layout>
        <!-- <div class="row q-mt-lg" style="margin-top:70px;margin-right:20px;"> -->
          <div class="row q-mt-lg subtitulos" style="margin-top:70px;margin-right:20px;margin-left:15px;">
            General
          </div>
          <div class="row q-mt-lg" style="margin-right:20px;">
            <div class="col-sm-3">
              <q-field icon="fas fa-tag" icon-color="dark">
                <q-input readonly upper-case v-model="form.fields.folio" type="text" inverted-color="dark" float-label="Folio" maxlength="20" />
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="fas fa-microchip" icon-color="dark">
                <q-select readonly v-model="form.fields.recurso_id" inverted-color="dark" float-label="Proyecto" :options="recursosOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="fas fa-info" icon-color="dark">
                <q-select readonly v-model="form.fields.status" inverted-color="dark" float-label="Estatus" :options="form.selectStatus"></q-select>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="fas fa-microchip" icon-color="dark">
                <q-select readonly v-model="form.fields.entidad_id" inverted-color="dark" float-label="Entidad" :options="entidadesOptions" filter/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right:20px;">
            <div class="col-sm-6">
              <q-field icon="format_size" icon-color="dark">
                <q-input readonly upper-case v-model="form.fields.titulo" type="text" inverted-color="dark" float-label="Procedimiento" maxlength="100" />
              </q-field>
            </div>
            <div class="col-sm-6">
              <q-field icon="format_size" icon-color="dark">
                <q-input readonly upper-case v-model="form.fields.descripcion" type="text" inverted-color="dark" float-label="Descripción" maxlength="100" />
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right:20px;">
            <div class="col-sm-3">
              <q-field icon="fas fa-tag" icon-color="dark">
                <q-input readonly upper-case v-model="form.fields.contrato" type="text" inverted-color="dark" float-label="Contrato" maxlength="20" />
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="fas fa-tag" icon-color="dark">
                <q-input readonly upper-case v-model="form.fields.orden_compra" type="text" inverted-color="dark" float-label="Orden de compra" maxlength="100" />
              </q-field>
            </div>
            <div class="col-sm-3">
              <div class="icono_field">
                <!-- <q-field  icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form.fields.monto.$error" error-label="Ingrese el monto ($0.00 - $1,000,000,000.00)" float-label="Monto adjudicado">
                  <money v-model="form.fields.monto" v-bind="money" style="height:53px;width:100%;margin-bottom:2px;" v-bind:class="{ moneyBien: !($v.form.fields.monto.$error), moneyError: $v.form.fields.monto.$error }" @input="$v.form.fields.monto.$touch"></money>
                </q-field> -->
                <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                  <q-input readonly v-model="form.fields.monto_inicial" v-money="money" inverted-color="dark" float-label="Monto inicial" suffix="MXN"></q-input>
                </q-field>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="icono_field">
                <!-- <q-field  icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form.fields.monto.$error" error-label="Ingrese el monto ($0.00 - $1,000,000,000.00)" float-label="Monto adjudicado">
                  <money v-model="form.fields.monto" v-bind="money" style="height:53px;width:100%;margin-bottom:2px;" v-bind:class="{ moneyBien: !($v.form.fields.monto.$error), moneyError: $v.form.fields.monto.$error }" @input="$v.form.fields.monto.$touch"></money>
                </q-field> -->
                <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                  <q-input readonly v-model="form.fields.monto_adjudicado" v-money="money" inverted-color="dark" float-label="Monto adjudicado" suffix="MXN"></q-input>
                </q-field>
              </div>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-12">
              <q-field  icon="fas fa-user" icon-color="dark" :error="$v.form.fields.comprador.$error" error-label="Texto demasiado largo">
                <q-input readonly upper-case v-model="form.fields.comprador" inverted-color="dark" float-label="Comprador" maxlength="200"></q-input>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-3" style="margin-top:5px;">
              <q-field icon="fas fa-file-alt" icon-color="dark" helper="Anticipo">
                <q-toggle readonly v-model="form.fields.campo_anticipo" color="green" checked-icon="fas fa-check" unchecked-icon="fas fa-times"/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <div class="icono_field">
                <q-field  icon="fas fa-dollar-sign" icon-color="dark" v-if="form.fields.campo_anticipo === true">
                  <!-- <q-input v-model="form.fields.porcentaje_anticipo" type="number" inverted color="dark" float-label="Porcentaje anticipo" suffix="%"></q-input> -->
                  <q-input readonly align="right" :decimals="0" numeric-keyboard-toggle v-model="form.fields.porcentaje_anticipo" type="number" inverted-color="dark" float-label="Porcentaje anticipo" suffix="%"></q-input>
                </q-field>
              </div>
            </div>
            <div class="col-sm-3">
              <div class="icono_field">
                <q-field  icon="fas fa-dollar-sign" icon-color="dark" v-if="form.fields.campo_anticipo === true">
                  <q-input readonly v-model="form.fields.monto_anticipo" v-money="money" inverted-color="dark" float-label="Monto anticipo" suffix="MXN"></q-input>
                </q-field>
                <!-- <q-field  icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form.fields.monto.$error" error-label="Ingrese el monto ($0.00 - $1,000,000,000.00)" float-label="Monto adjudicado">
                  <money v-model="form.fields.monto" v-bind="money" style="height:53px;width:100%;margin-bottom:2px;" v-bind:class="{ moneyBien: !($v.form.fields.monto.$error), moneyError: $v.form.fields.monto.$error }" @input="$v.form.fields.monto.$touch"></money>
                </q-field> -->
              </div>
            </div>
          </div>
          <div class="row q-mt-lg subtitulos" style="margin-left:15px;">
            Empresa concursante
          </div>
          <div class="row q-mt-lg" style="margin-right:20px;">
            <div class="col-sm-12">
              <q-field icon="fas fa-building" icon-color="dark">
                <q-select readonly v-model="form.fields.empresa_id" inverted-color="dark" float-label="Empresa concursante" :options="empresasOptions" filter/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg subtitulos" style="margin-left:15px;">
            Empresas de respaldo
          </div>
          <div class="row q-mt-lg" style="margin-right:20px;">
            <div class="col-sm-3">
              <q-field icon="fas fa-building" icon-color="dark">
                <q-select readonly v-model="form.fields.respaldo1" inverted-color="dark" float-label="Empresa de respaldo 1" :options="empresasOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="fas fa-building" icon-color="dark">
                <q-select readonly v-model="form.fields.respaldo2" inverted-color="dark" float-label="Empresa de respaldo 2" :options="empresasOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="fas fa-building" icon-color="dark">
                <q-select readonly v-model="form.fields.respaldo3" inverted-color="dark" float-label="Empresa de respaldo 3" :options="empresasOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-3">
              <q-field icon="fas fa-building" icon-color="dark">
                <q-select readonly v-model="form.fields.respaldo4" inverted-color="dark" float-label="Empresa de respaldo 4" :options="empresasOptions" filter/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg subtitulos" style="margin-left:15px;">
            Fechas
          </div>
          <div class="row q-mt-lg" style="margin-right:20px;">
            <div class="col-sm-4">
              <q-field icon="date_range" icon-color="dark">
                <q-datetime readonly v-model="form.fields.fecha_inicio" type="date" inverted-color="dark" float-label="Fecha de inicio" align="center" @input="cambiaFechaInicio()"></q-datetime>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="date_range" icon-color="dark">
                <q-datetime readonly v-model="form.fields.fecha_presentacion" type="date" inverted-color="dark" float-label="Fecha presentación" align="center" @input="cambiaFechaIFallo()"></q-datetime>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="date_range" icon-color="dark">
                <q-datetime readonly v-model="form.fields.fecha_fallo" type="date" inverted-color="dark" float-label="Fecha fallo" align="center"></q-datetime>
              </q-field>
            </div>
          </div>
          <!-- <div class="row q-mt-lg">
            <div class="col-sm-2 offset-sm-10 pull-right">
              <q-btn @click="actualizarLicitacion()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
            </div>
          </div> -->
          <div class="row q-mt-lg">
          </div>
          <div>
      <q-tabs class="shadow-1" inverted animated swipeable color="light" align="justify" style="margin-top: 30px;">
        <q-tab default name="bases" slot="title" icon="add_box" label="Bases" @click="documentos()"/>
        <q-tab name="eventos" slot="title" icon="date_range" label="Eventos" @click="eventos()"/>
        <q-tab name="requisitos" slot="title" icon="attachment" label="Requisitos" @click="requisitos()"/>

      <div class="tabcontent">
        <q-tab-pane name="bases"> <!-- -------------------TAB BASES---------------------- -->
          <div class="row q-mt-sm" style="margin-top:20px;">
            <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
              <q-table id="sticky-table"
                :data="form_bases.data"
                :columns="form_bases.columns"
                :selected.sync="form_bases.selected"
                :filter="form_bases.filter"
                color="positive"
                title=""
                :dense="true"
                :pagination.sync="form_bases.pagination"
                :loading="form_bases.loading"
                :rows-per-page-options="form_bases.rowsOptions">
                <template slot="body" slot-scope="props">
                  <q-tr :props="props">
                    <q-td key="nombre_documento" :props="props">{{ props.row.nombre_documento }}</q-td>
                    <q-td key="descripcion" :props="props">{{ props.row.descripcion }}</q-td>
                    <q-td key="name" :props="props">{{ props.row.name }}</q-td>
                    <q-td key="acciones" :props="props">
                      <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="fileInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadImageFile()" />
                      <q-btn small flat @click="selectedFile(props.row)" color="green-9" :loading="loadingButton">
                        <q-icon name="cloud_upload" />&nbsp;
                      </q-btn>
                      <!-- <q-btn small flat @click="descargarFormato(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name===null)">
                        <q-tooltip>Descargar</q-tooltip>
                      </q-btn> -->
                      <q-btn small flat :icon="props.row.icon" :color="props.row.color">
                          <q-popover>
                            <q-list link separator class="scroll" style="min-width: 100px">
                              <q-item v-close-overlay @click.native="verFormato(props.row)" v-if="props.row.doc_type !== 'docx' && props.row.doc_type !== 'xml' && props.row.doc_type !== 'xlsx'">
                                <q-item-main label="Ver"/>
                              </q-item>
                              <q-item v-close-overlay @click.native="descargarFormato(props.row)">
                                <q-item-main label="Descargar"/>
                              </q-item>
                              <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                <q-item-main label="Eliminar"/>
                              </q-item> -->
                            </q-list>
                          </q-popover>
                        </q-btn>
                    </q-td>
                  </q-tr>
                </template>
              </q-table>
              <q-inner-loading :visible="form_bases.loading">
                <q-spinner-dots size="64px" color="primary" />
              </q-inner-loading>
            </div>
          </div>
        </q-tab-pane> <!-- -------------------------TAB EVENTOS ------------------------- -->
        <q-tab-pane name="eventos">
          <div class="row q-mt-sm" style="margin-top:20px;">
            <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
              <q-table id="sticky-table"
                :data="form_eventos.data"
                :columns="form_eventos.columns"
                :selected.sync="form_eventos.selected"
                :filter="form_eventos.filter"
                color="positive"
                title=""
                :dense="true"
                :pagination.sync="form_eventos.pagination"
                :loading="form_eventos.loading"
                :rows-per-page-options="form_eventos.rowsOptions">
                <template slot="body" slot-scope="props">
                  <q-tr :props="props">
                    <q-td key="evento" :props="props">{{ props.row.evento }}</q-td>
                    <q-td key="fecha_evento" :props="props">{{ props.row.fecha_evento }}</q-td>
                    <q-td key="name" :props="props">{{ props.row.name }}</q-td>
                    <q-td key="acciones" :props="props">
                      <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="eventoInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadEventFile()" />
                      <q-btn small flat @click="selectedFileEvento(props.row)" color="green-9" :loading="loadingButton">
                        <q-icon name="cloud_upload" />&nbsp;
                      </q-btn>
                      <!-- <q-btn small flat @click="descargarFormatoEvento(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name===null)">
                        <q-tooltip>Descargar</q-tooltip>
                      </q-btn> -->
                      <q-btn small flat :icon="props.row.icon" :color="props.row.color" v-if="!(props.row.name===null)">
                          <q-popover>
                            <q-list link separator class="scroll" style="min-width: 100px">
                              <q-item v-close-overlay @click.native="verFormatoEvento(props.row)" v-if="props.row.doc_type !== 'docx' && props.row.doc_type !== 'xml' && props.row.doc_type !== 'xlsx'">
                                <q-item-main label="Ver"/>
                              </q-item>
                              <q-item v-close-overlay @click.native="descargarFormatoEvento(props.row)">
                                <q-item-main label="Descargar"/>
                              </q-item>
                              <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                <q-item-main label="Eliminar"/>
                              </q-item> -->
                            </q-list>
                          </q-popover>
                        </q-btn>
                    </q-td>
                  </q-tr>
                </template>
              </q-table>
              <q-inner-loading :visible="form_eventos.loading">
                <q-spinner-dots size="64px" color="primary" />
              </q-inner-loading>
            </div>
          </div>
          </q-tab-pane>
        <!-- <q-tab-pane name="sesiones">Sesiones</q-tab-pane> -->
        <q-tab-pane name="requisitos">
          <q-collapsible v-model="open_requisitos" icon="label" label="Requisitos (Factores de éxito)">
          <div class="row q-mt-sm" style="margin-top:20px;">
            <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
              <q-table id="sticky-table"
                :data="form_requisitos.data"
                :columns="form_requisitos.columns"
                :selected.sync="form_requisitos.selected"
                :filter="form_requisitos.filter"
                color="positive"
                title=""
                :dense="true"
                :pagination.sync="form_requisitos.pagination"
                :loading="form_requisitos.loading"
                :rows-per-page-options="form_requisitos.rowsOptions">
                <template slot="body" slot-scope="props">
                  <q-tr :props="props">
                    <q-td key="requisito" :props="props">{{ props.row.requisito }}</q-td>
                    <q-td key="descripcion" :props="props">{{ props.row.descripcion }}</q-td>
                    <q-td key="fecha" :props="props">{{ props.row.fecha }}</q-td>
                    <q-td key="name0" :props="props">
                      <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="requisitoInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadRequisitoFile()" />
                      <q-btn small flat @click="selectedFileDocumento(props.row)" color="green-9" :loading="loadingButton">
                        <q-icon name="cloud_upload" />&nbsp;
                        <q-tooltip>Subir Archivo</q-tooltip>
                      </q-btn>
                      <!-- <q-btn small flat @click="descargarRequisito0(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name0===null)">
                        <q-tooltip>Descargar: {{ props.row.name0 }}</q-tooltip>
                      </q-btn> -->
                      <q-btn small flat :icon="props.row.icon0" :color="props.row.color0" v-if="!(props.row.name0===null)">
                        <q-tooltip>{{ props.row.name0 }}</q-tooltip>
                          <q-popover>
                            <q-list link separator class="scroll" style="min-width: 100px">
                              <q-item v-close-overlay @click.native="verFormatoRequisito0(props.row)" v-if="props.row.doc_type0.toLowerCase() !== 'docx' && props.row.doc_type0 !== 'xml' && props.row.doc_type0 !== 'xlsx'">
                                <q-item-main label="Ver"/>
                              </q-item>
                              <q-item v-close-overlay @click.native="descargarRequisito0(props.row)">
                                <q-item-main label="Descargar"/>
                              </q-item>
                              <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                <q-item-main label="Eliminar"/>
                              </q-item> -->
                            </q-list>
                          </q-popover>
                        </q-btn>
                    </q-td>
                    <q-td key="responsable" :props="props">
                      <q-select v-model="props.row.responsable" @change="cambiarResponsable(props.row)" :options="usuariosOptions" filter/>
                    </q-td>
                    <q-td key="name1" :props="props">
                      <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="requisitoInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadRequisitoFile()" />
                      <q-btn small flat @click="selectedFileDocumento1(props.row)" color="green-9" :loading="loadingButton">
                        <q-icon name="cloud_upload" />&nbsp;
                        <q-tooltip>Subir Archivo</q-tooltip>
                      </q-btn>
                      <!-- <q-btn small flat @click="descargarRequisito1(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name1===null)">
                        <q-tooltip>Descargar: {{ props.row.name1 }}</q-tooltip>
                      </q-btn> -->
                      <q-btn small flat :icon="props.row.icon1" :color="props.row.color1" v-if="!(props.row.name1===null)">
                        <q-tooltip>{{ props.row.name1 }}</q-tooltip>
                          <q-popover>
                            <q-list link separator class="scroll" style="min-width: 100px">
                              <q-item v-close-overlay @click.native="verFormatoRequisito1(props.row)" v-if="props.row.doc_type1 !== 'docx' && props.row.doc_type1 !== 'xml' && props.row.doc_type1 !== 'xlsx'">
                                <q-item-main label="Ver"/>
                              </q-item>
                              <q-item v-close-overlay @click.native="descargarRequisito1(props.row)">
                                <q-item-main label="Descargar"/>
                              </q-item>
                              <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                <q-item-main label="Eliminar"/>
                              </q-item> -->
                            </q-list>
                          </q-popover>
                        </q-btn>
                    </q-td>
                    <q-td key="responsable1" :props="props">
                      <q-select v-model="props.row.responsable1" @change="cambiarResponsable(props.row)" :options="usuariosOptions" filter/>
                    </q-td>
                    <q-td key="name2" :props="props">
                      <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="requisitoInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadRequisitoFile()" />
                      <q-btn small flat @click="selectedFileDocumento2(props.row)" color="green-9" :loading="loadingButton">
                        <q-icon name="cloud_upload" />&nbsp;
                        <q-tooltip>Subir Archivo</q-tooltip>
                      </q-btn>
                      <!-- <q-btn small flat @click="descargarRequisito2(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name2===null)">
                        <q-tooltip>Descargar: {{ props.row.name2 }}</q-tooltip>
                      </q-btn> -->
                      <q-btn small flat :icon="props.row.icon2" :color="props.row.color2" v-if="!(props.row.name2===null)">
                        <q-tooltip>{{ props.row.name2 }}</q-tooltip>
                          <q-popover>
                            <q-list link separator class="scroll" style="min-width: 100px">
                              <q-item v-close-overlay @click.native="verFormatoRequisito2(props.row)" v-if="props.row.doc_type2 !== 'docx' && props.row.doc_type2 !== 'xml' && props.row.doc_type2 !== 'xlsx'">
                                <q-item-main label="Ver"/>
                              </q-item>
                              <q-item v-close-overlay @click.native="descargarRequisito2(props.row)">
                                <q-item-main label="Descargar"/>
                              </q-item>
                              <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                <q-item-main label="Eliminar"/>
                              </q-item> -->
                            </q-list>
                          </q-popover>
                        </q-btn>
                    </q-td>
                    <q-td key="responsable2" :props="props">
                      <q-select v-model="props.row.responsable2" @change="cambiarResponsable(props.row)" :options="usuariosOptions" filter/>
                    </q-td>
                    <q-td key="name3" :props="props">
                      <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="requisitoInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadRequisitoFile()" />
                      <q-btn small flat @click="selectedFileDocumento3(props.row)" color="green-9" :loading="loadingButton">
                        <q-icon name="cloud_upload" />&nbsp;
                        <q-tooltip>Subir Archivo</q-tooltip>
                      </q-btn>
                      <!-- <q-btn small flat @click="descargarRequisito3(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name3===null)">
                        <q-tooltip>Descargar: {{ props.row.name3 }}</q-tooltip>
                      </q-btn> -->
                      <q-btn small flat :icon="props.row.icon3" :color="props.row.color3" v-if="!(props.row.name3===null)">
                        <q-tooltip>{{ props.row.name3 }}</q-tooltip>
                          <q-popover>
                            <q-list link separator class="scroll" style="min-width: 100px">
                              <q-item v-close-overlay @click.native="verFormatoRequisito3(props.row)" v-if="props.row.doc_type3 !== 'docx' && props.row.doc_type3 !== 'xml' && props.row.doc_type3 !== 'xlsx'">
                                <q-item-main label="Ver"/>
                              </q-item>
                              <q-item v-close-overlay @click.native="descargarRequisito3(props.row)">
                                <q-item-main label="Descargar"/>
                              </q-item>
                              <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                <q-item-main label="Eliminar"/>
                              </q-item> -->
                            </q-list>
                          </q-popover>
                        </q-btn>
                    </q-td>
                    <q-td key="responsable3" :props="props">
                      <q-select v-model="props.row.responsable3" @change="cambiarResponsable(props.row)" :options="usuariosOptions" filter/>
                    </q-td>
                    <q-td key="name4" :props="props">
                      <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="requisitoInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadRequisitoFile()" />
                      <q-btn small flat @click="selectedFileDocumento4(props.row)" color="green-9" :loading="loadingButton">
                        <q-icon name="cloud_upload" />&nbsp;
                        <q-tooltip>Subir Archivo</q-tooltip>
                      </q-btn>
                      <!-- <q-btn small flat @click="descargarRequisito4(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name4===null)">
                        <q-tooltip>Descargar: {{ props.row.name4 }}</q-tooltip>
                      </q-btn> -->
                      <q-btn small flat :icon="props.row.icon4" :color="props.row.color4" v-if="!(props.row.name4===null)">
                        <q-tooltip>{{ props.row.name4 }}</q-tooltip>
                          <q-popover>
                            <q-list link separator class="scroll" style="min-width: 100px">
                              <q-item v-close-overlay @click.native="verFormatoRequisito4(props.row)" v-if="props.row.doc_type4 !== 'docx' && props.row.doc_type4 !== 'xml' && props.row.doc_type4 !== 'xlsx'">
                                <q-item-main label="Ver"/>
                              </q-item>
                              <q-item v-close-overlay @click.native="descargarRequisito4(props.row)">
                                <q-item-main label="Descargar"/>
                              </q-item>
                              <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                <q-item-main label="Eliminar"/>
                              </q-item> -->
                            </q-list>
                          </q-popover>
                        </q-btn>
                    </q-td>
                    <q-td key="responsable4" :props="props">
                      <q-select v-model="props.row.responsable4" @change="cambiarResponsable(props.row)" :options="usuariosOptions" filter/>
                    </q-td>
                  </q-tr>
                </template>
              </q-table>
              <q-inner-loading :visible="form_requisitos.loading">
                <q-spinner-dots size="64px" color="primary" />
              </q-inner-loading>
            </div>
          </div>
        </q-collapsible>
        <q-collapsible v-model="open_propuestas" icon="label" label="Propuestas finales">
          <div class="row q-mt-sm" style="margin-top:20px;">
            <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
              <q-table id="sticky-table"
                :data="form_propuestas.data"
                :columns="form_propuestas.columns"
                :selected.sync="form_propuestas.selected"
                :filter="form_propuestas.filter"
                color="positive"
                title=""
                :dense="true"
                :pagination.sync="form_propuestas.pagination"
                :loading="form_propuestas.loading"
                :rows-per-page-options="form_propuestas.rowsOptions">
                <template slot="body" slot-scope="props">
                  <q-tr :props="props">
                    <q-td key="propuesta" :props="props">{{ props.row.propuesta }}</q-td>
                    <q-td key="descripcion" :props="props">{{ props.row.descripcion }}</q-td>
                    <q-td key="fecha" :props="props">{{ props.row.fecha }}</q-td>
                    <q-td key="name0" :props="props">
                      <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="propuestaInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadPropuestaFile()" />
                      <q-btn small flat @click="selectedFileDocumentoPropuesta(props.row)" color="green-9" :loading="loadingButton">
                        <q-icon name="cloud_upload" />&nbsp;
                        <q-tooltip>Subir Archivo</q-tooltip>
                      </q-btn>
                      <!-- <q-btn small flat @click="descargarPropuesta0(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name0===null)">
                        <q-tooltip>Descargar: {{ props.row.name0 }}</q-tooltip>
                      </q-btn> -->
                      <q-btn small flat :icon="props.row.icon0" :color="props.row.color0" v-if="!(props.row.name0===null)">
                        <q-tooltip>{{ props.row.name0 }}</q-tooltip>
                          <q-popover>
                            <q-list link separator class="scroll" style="min-width: 100px">
                              <q-item v-close-overlay @click.native="verFormatoPropuesta0(props.row)" v-if="props.row.doc_type0.toLowerCase() !== 'docx' && props.row.doc_type0 !== 'xml' && props.row.doc_type0 !== 'xlsx'">
                                <q-item-main label="Ver"/>
                              </q-item>
                              <q-item v-close-overlay @click.native="descargarPropuesta0(props.row)">
                                <q-item-main label="Descargar"/>
                              </q-item>
                              <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                <q-item-main label="Eliminar"/>
                              </q-item> -->
                            </q-list>
                          </q-popover>
                        </q-btn>
                    </q-td>
                    <q-td key="responsable" :props="props">
                      <q-select v-model="props.row.responsable" @change="cambiarResponsablePropuesta(props.row)" :options="usuariosOptions" filter/>
                    </q-td>
                    <q-td key="name1" :props="props">
                      <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="requisitoInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadRequisitoFile()" />
                      <q-btn small flat @click="selectedFileDocumentoPropuesta1(props.row)" color="green-9" :loading="loadingButton">
                        <q-icon name="cloud_upload" />&nbsp;
                        <q-tooltip>Subir Archivo</q-tooltip>
                      </q-btn>
                      <!-- <q-btn small flat @click="descargarPropuesta1(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name1===null)">
                        <q-tooltip>Descargar: {{ props.row.name1 }}</q-tooltip>
                      </q-btn> -->
                      <q-btn small flat :icon="props.row.icon1" :color="props.row.color1" v-if="!(props.row.name1===null)">
                        <q-tooltip>{{ props.row.name1 }}</q-tooltip>
                          <q-popover>
                            <q-list link separator class="scroll" style="min-width: 100px">
                              <q-item v-close-overlay @click.native="verFormatoPropuesta1(props.row)" v-if="props.row.doc_type1.toLowerCase() !== 'docx' && props.row.doc_type1 !== 'xml' && props.row.doc_type1 !== 'xlsx'">
                                <q-item-main label="Ver"/>
                              </q-item>
                              <q-item v-close-overlay @click.native="descargarPropuesta1(props.row)">
                                <q-item-main label="Descargar"/>
                              </q-item>
                              <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                <q-item-main label="Eliminar"/>
                              </q-item> -->
                            </q-list>
                          </q-popover>
                        </q-btn>
                    </q-td>
                    <q-td key="responsable1" :props="props">
                      <q-select v-model="props.row.responsable1" @change="cambiarResponsablePropuesta(props.row)" :options="usuariosOptions" filter/>
                    </q-td>
                    <q-td key="name2" :props="props">
                      <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="requisitoInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadRequisitoFile()" />
                      <q-btn small flat @click="selectedFileDocumentoPropuesta2(props.row)" color="green-9" :loading="loadingButton">
                        <q-icon name="cloud_upload" />&nbsp;
                        <q-tooltip>Subir Archivo</q-tooltip>
                      </q-btn>
                      <!-- <q-btn small flat @click="descargarPropuesta2(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name2===null)">
                        <q-tooltip>Descargar: {{ props.row.name2 }}</q-tooltip>
                      </q-btn> -->
                      <q-btn small flat :icon="props.row.icon2" :color="props.row.color2" v-if="!(props.row.name2===null)">
                        <q-tooltip>{{ props.row.name2 }}</q-tooltip>
                          <q-popover>
                            <q-list link separator class="scroll" style="min-width: 100px">
                              <q-item v-close-overlay @click.native="verFormatoPropuesta2(props.row)" v-if="props.row.doc_type2.toLowerCase() !== 'docx' && props.row.doc_type2 !== 'xml' && props.row.doc_type2 !== 'xlsx'">
                                <q-item-main label="Ver"/>
                              </q-item>
                              <q-item v-close-overlay @click.native="descargarPropuesta2(props.row)">
                                <q-item-main label="Descargar"/>
                              </q-item>
                              <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                <q-item-main label="Eliminar"/>
                              </q-item> -->
                            </q-list>
                          </q-popover>
                        </q-btn>
                    </q-td>
                    <q-td key="responsable2" :props="props">
                      <q-select v-model="props.row.responsable2" @change="cambiarResponsablePropuesta(props.row)" :options="usuariosOptions" filter/>
                    </q-td>
                    <q-td key="name3" :props="props">
                      <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="requisitoInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadRequisitoFile()" />
                      <q-btn small flat @click="selectedFileDocumentoPropuesta3(props.row)" color="green-9" :loading="loadingButton">
                        <q-icon name="cloud_upload" />&nbsp;
                        <q-tooltip>Subir Archivo</q-tooltip>
                      </q-btn>
                      <!-- <q-btn small flat @click="descargarPropuesta3(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name3===null)">
                        <q-tooltip>Descargar: {{ props.row.name3 }}</q-tooltip>
                      </q-btn> -->
                      <q-btn small flat :icon="props.row.icon3" :color="props.row.color3" v-if="!(props.row.name3===null)">
                        <q-tooltip>{{ props.row.name3 }}</q-tooltip>
                          <q-popover>
                            <q-list link separator class="scroll" style="min-width: 100px">
                              <q-item v-close-overlay @click.native="verFormatoPropuesta3(props.row)" v-if="props.row.doc_type3.toLowerCase() !== 'docx' && props.row.doc_type3 !== 'xml' && props.row.doc_type3 !== 'xlsx'">
                                <q-item-main label="Ver"/>
                              </q-item>
                              <q-item v-close-overlay @click.native="descargarPropuesta3(props.row)">
                                <q-item-main label="Descargar"/>
                              </q-item>
                              <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                <q-item-main label="Eliminar"/>
                              </q-item> -->
                            </q-list>
                          </q-popover>
                        </q-btn>
                    </q-td>
                    <q-td key="responsable3" :props="props">
                      <q-select v-model="props.row.responsable3" @change="cambiarResponsablePropuesta(props.row)" :options="usuariosOptions" filter/>
                    </q-td>
                    <q-td key="name4" :props="props">
                      <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="requisitoInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf" @change="uploadRequisitoFile()" />
                      <q-btn small flat @click="selectedFileDocumentoPropuesta4(props.row)" color="green-9" :loading="loadingButton">
                        <q-icon name="cloud_upload" />&nbsp;
                        <q-tooltip>Subir Archivo</q-tooltip>
                      </q-btn>
                      <!-- <q-btn small flat @click="descargarPropuesta4(props.row)" color="blue-8" icon="cloud_download" v-if="!(props.row.name4===null)">
                        <q-tooltip>Descargar: {{ props.row.name4 }}</q-tooltip>
                      </q-btn> -->
                      <q-btn small flat :icon="props.row.icon4" :color="props.row.color4" v-if="!(props.row.name4===null)">
                        <q-tooltip>{{ props.row.name4 }}</q-tooltip>
                          <q-popover>
                            <q-list link separator class="scroll" style="min-width: 100px">
                              <q-item v-close-overlay @click.native="verFormatoPropuesta4(props.row)" v-if="props.row.doc_type4.toLowerCase() !== 'docx' && props.row.doc_type4 !== 'xml' && props.row.doc_type4 !== 'xlsx'">
                                <q-item-main label="Ver"/>
                              </q-item>
                              <q-item v-close-overlay @click.native="descargarPropuesta4(props.row)">
                                <q-item-main label="Descargar"/>
                              </q-item>
                              <!-- <q-item v-close-overlay @click.native="cotizacionAccion(props.row)">
                                <q-item-main label="Eliminar"/>
                              </q-item> -->
                            </q-list>
                          </q-popover>
                        </q-btn>
                    </q-td>
                    <q-td key="responsable4" :props="props">
                      <q-select v-model="props.row.responsable4" @change="cambiarResponsablePropuesta(props.row)" :options="usuariosOptions" filter/>
                    </q-td>
                  </q-tr>
                </template>
              </q-table>
              <q-inner-loading :visible="form_propuestas.loading">
                <q-spinner-dots size="64px" color="primary" />
              </q-inner-loading>
            </div>
          </div>
        </q-collapsible>
        </q-tab-pane>
      </div>
      </q-tabs>
    </div>
      </q-modal> <!-- AQUI TERMINA EL MODAL DE SOLO LECTURA DE UNA SOLICITUD -->

      <div id="myModal" class="modal1" v-if="vista_imagen" style="display: block;">
        <span class="close1" @click="desaparecer()">&times;</span>
        <img id="img01" :src="url_img" class="modal-content1">
        <embed id="pdf01" :src="url_pdf" class="modal-content1" type="application/pdf" width="800" height="100%" />
      </div>
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { required, minValue, maxLength, helpers } from 'vuelidate/lib/validators'
import moment from 'moment'
import axios from 'axios'
// import {Money} from 'v-money'
import { filter } from 'quasar'
import {VMoney} from 'v-money'
const alpha = helpers.regex('alpha', /^[A-Z0-9Ñ]+([-/]{1}[A-Z0-9Ñ]+)*$/)

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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'AUXILIAR'.toUpperCase() || propiedades[i].toUpperCase() === 'COBRANZA'.toUpperCase() || propiedades[i].toUpperCase() === 'VENTAS'.toUpperCase() || propiedades[i].toUpperCase() === 'PLANEACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'Coordinador GDP'.toUpperCase()) {
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
      validar_observaciones: true,
      tab: 'bases',
      selectYear: [ { label: '' + (new Date().getFullYear() - 3), value: '' + (new Date().getFullYear() - 3) }, { label: '' + (new Date().getFullYear() - 2), value: '' + (new Date().getFullYear() - 2) }, { label: '' + (new Date().getFullYear() - 1), value: '' + (new Date().getFullYear() - 1) }, { label: '' + (new Date().getFullYear()), value: '' + (new Date().getFullYear()) }, { label: '' + (new Date().getFullYear() + 1), value: '' + (new Date().getFullYear() + 1) } ],
      year: '' + (new Date().getFullYear()),
      agregar_perfil_licitacion: false,
      registro_perfil: [],
      open_requisitos: false,
      open_propuestas: false,
      showSelectedFile: false,
      vista_imagen: false,
      url_img: '',
      url_pdf: '',
      money: {
        decimal: '.',
        thousands: ',',
        precision: 2,
        masked: false
      },
      loadingButton: false,
      informacion: false,
      objetoInformacion: null,
      views: {
        grid: true,
        create: false,
        update: false
      },
      form_filtros: {
        fields: {
          fecha_inicio: 'i',
          fecha_fallo: 'i',
          status: 'Todas'
        },
        selectStatus: [ { 'label': 'CREADA', 'value': 'CREADA' }, { 'label': 'EN PROCESO', 'value': 'EN PROCESO' }, { 'label': 'PRESENTADA', 'value': 'PRESENTADA' }, { 'label': 'ADJUDICADA', 'value': 'ADJUDICADA' }, { 'label': 'NO ADJUDICADA', 'value': 'NO ADJUDICADA' }, { 'label': 'CANCELADA', 'value': 'CANCELADA' }, { 'label': 'Todas', 'value': 'Todas' } ]
      },
      form: {
        fields: {
          id: 0,
          recurso_id: 0,
          empresa_id: 0,
          fecha_inicio: '',
          folio: '',
          titulo: '',
          fecha_fallo: '',
          fecha_presentacion: '',
          fecha_arranque: '',
          formato_requisito_id: '',
          status: '',
          entidad_id: 0,
          descripcion: '',
          respaldo1: 0,
          respaldo2: 0,
          respaldo3: 0,
          respaldo4: 0,
          empresa_respaldo_1: '',
          empresa_respaldo_2: '',
          empresa_respaldo_3: '',
          empresa_respaldo_4: '',
          id_respaldo1: 0,
          id_respaldo2: 0,
          id_respaldo3: 0,
          id_respaldo4: 0,
          contrato: '',
          monto_inicial: 0,
          monto_adjudicado: 0,
          orden_compra: '',
          campo_anticipo: false,
          porcentaje_anticipo: 0,
          monto_anticipo: 0,
          comprador: '',
          empresa: '',
          observaciones_creada: '',
          observaciones_proceso: '',
          observaciones_presentada: '',
          observaciones_adjudicada: '',
          observaciones_no_adjudicada: '',
          observaciones_cancelada: '',
          responsable: '',
          responsable_gdp: ''
        },
        // data: [],
        selectStatus: [ { 'label': 'CREADA', 'value': 'CREADA' }, { 'label': 'EN PROCESO', 'value': 'EN PROCESO' }, { 'label': 'PRESENTADA', 'value': 'PRESENTADA' }, { 'label': 'ADJUDICADA', 'value': 'ADJUDICADA' }, { 'label': 'NO ADJUDICADA', 'value': 'NO ADJUDICADA' }, { 'label': 'CANCELADA', 'value': 'CANCELADA' } ],
        columns: [
          { name: 'folio', label: 'Folio', field: 'folio', sortable: true, type: 'string', align: 'left' },
          { name: 'descripcion', label: 'Descripción', field: 'descripcion', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha_inicio', label: 'Fecha inicio', field: 'fecha_inicio', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_fallo', label: 'Fecha fallo', field: 'fecha_fallo', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_presentacion', label: 'Fecha presentación', field: 'fecha_presentacion', sortable: true, type: 'string', align: 'center' },
          { name: 'documento_final', label: 'Documento final', field: 'documento_final', sortable: true, type: 'string', align: 'left' },
          { name: 'status', label: 'Estatus', field: 'status', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'center' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_respaldo: {
        lic_respaldo: false,
        empresasRespaldoOptions: [],
        fields: {
          licitacion_id: 0,
          empresa_id: 0
        },
        columns: [
          { name: 'empresa', label: 'Empresa de respaldo', field: 'empresa', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'center' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_bases: {
        fields: {
          licitacion_id: 0,
          id: 0,
          name: '',
          descripcion: '',
          documento_id: '',
          nombre_documento: '',
          observaciones_archivo: '',
          name2: '',
          documento_id2: 0
        },
        columns: [
          { name: 'nombre_documento', label: 'Nombre documento', field: 'nombre_documento', sortable: true, type: 'string', align: 'left' },
          { name: 'descripcion', label: 'Observaciones', field: 'descripcion', sortable: true, type: 'string', align: 'left' },
          { name: 'name', label: 'Nombre archivo', field: 'name', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false,
        edita: false
      },
      form_eventos: {
        fields: {
          licitacion_id: 0,
          id: 0,
          evento: '',
          fecha_evento: '',
          name: ''
        },
        columns: [
          { name: 'evento', label: 'Evento', field: 'evento', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha_evento', label: 'Fecha', field: 'fecha_evento', sortable: true, type: 'string', align: 'left' },
          { name: 'name', label: 'Acta/Documento', field: 'name', sortable: true, type: 'string', align: 'left' },
          { name: 'documentos', label: 'Documentos', field: 'documentos', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false,
        editar: false
      },
      form_requisitos: {
        fields: {
          id: 0,
          licitacion_id: 0,
          requisito: '',
          descripcion: '',
          fecha: '',
          ganadora: '',
          documento: '',
          responsable: 0,
          respaldo1: '',
          documento1: '',
          responsable1: 0,
          respaldo2: '',
          documento2: '',
          responsable2: 0,
          respaldo3: '',
          documento3: '',
          responsable3: 0,
          respaldo4: '',
          documento4: '',
          responsable4: 0,
          name0: '',
          name1: '',
          name2: '',
          name3: '',
          name4: '',
          responsable_name: '',
          responsable_name1: '',
          responsable_name2: '',
          responsable_name3: '',
          responsable_name4: ''
        },
        columns: [
          { name: 'requisito', label: 'Requisito', field: 'requisito', sortable: true, type: 'string', align: 'left' },
          { name: 'descripcion', label: 'Descripción', field: 'descripcion', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha', label: 'Fecha', field: 'fecha', sortable: true, type: 'string', align: 'left' },
          { name: 'name0', label: 'Ganadora', field: 'name1', sortable: true, type: 'string', align: 'left' },
          { name: 'responsable', label: 'Responsable', field: 'responsable', sortable: true, type: 'string', align: 'left' },
          { name: 'name1', label: 'Respaldo 1', field: 'name1', sortable: true, type: 'string', align: 'left' },
          { name: 'responsable1', label: 'Responsable', field: 'responsable1', sortable: true, type: 'string', align: 'left' },
          { name: 'name2', label: 'Respaldo 2', field: 'name2', sortable: true, type: 'string', align: 'left' },
          { name: 'responsable2', label: 'Responsable', field: 'responsable2', sortable: true, type: 'string', align: 'left' },
          { name: 'name3', label: 'Respaldo 3', field: 'name3', sortable: true, type: 'string', align: 'left' },
          { name: 'responsable3', label: 'Responsable', field: 'responsable3', sortable: true, type: 'string', align: 'left' },
          { name: 'name4', label: 'Respaldo 4', field: 'name4', sortable: true, type: 'string', align: 'left' },
          { name: 'responsable4', label: 'Responsable', field: 'responsable4', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false,
        editar: false,
        bandera_ganadora: false,
        bandera_documento1: false,
        bandera_documento2: false,
        bandera_documento3: false,
        bandera_documento4: false
      },
      form_propuestas: {
        fields: {
          id: 0,
          licitacion_id: 0,
          propuesta: '',
          descripcion: '',
          fecha: '',
          ganadora: '',
          documento: '',
          responsable: 0,
          respaldo1: '',
          documento1: '',
          responsable1: 0,
          respaldo2: '',
          documento2: '',
          responsable2: 0,
          respaldo3: '',
          documento3: '',
          responsable3: 0,
          respaldo4: '',
          documento4: '',
          responsable4: 0,
          name0: '',
          name1: '',
          name2: '',
          name3: '',
          name4: '',
          responsable_name: '',
          responsable_name1: '',
          responsable_name2: '',
          responsable_name3: '',
          responsable_name4: ''
        },
        columns: [
          { name: 'propuesta', label: 'Propuesta', field: 'propuesta', sortable: true, type: 'string', align: 'left' },
          { name: 'descripcion', label: 'Descripción', field: 'descripcion', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha', label: 'Fecha', field: 'fecha', sortable: true, type: 'string', align: 'left' },
          { name: 'name0', label: 'Ganadora', field: 'name1', sortable: true, type: 'string', align: 'left' },
          { name: 'responsable', label: 'Responsable', field: 'responsable', sortable: true, type: 'string', align: 'left' },
          { name: 'name1', label: 'Respaldo 1', field: 'name1', sortable: true, type: 'string', align: 'left' },
          { name: 'responsable1', label: 'Responsable', field: 'responsable1', sortable: true, type: 'string', align: 'left' },
          { name: 'name2', label: 'Respaldo 2', field: 'name2', sortable: true, type: 'string', align: 'left' },
          { name: 'responsable2', label: 'Responsable', field: 'responsable2', sortable: true, type: 'string', align: 'left' },
          { name: 'name3', label: 'Respaldo 3', field: 'name3', sortable: true, type: 'string', align: 'left' },
          { name: 'responsable3', label: 'Responsable', field: 'responsable3', sortable: true, type: 'string', align: 'left' },
          { name: 'name4', label: 'Respaldo 4', field: 'name4', sortable: true, type: 'string', align: 'left' },
          { name: 'responsable4', label: 'Responsable', field: 'responsable4', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false,
        editar: false,
        bandera_ganadora: false,
        bandera_documento1: false,
        bandera_documento2: false,
        bandera_documento3: false,
        bandera_documento4: false
      },
      form_busqueda: {
        municipiosOptions: [{label: '---Todos---', value: 0}],
        fields: {
          area_id: 0,
          estado_id: 0,
          municipio_id: 0,
          aptitudes: [],
          aptitudes_param: ['all'],
          participacion: '',
          curso: ''
        },
        columns: [
          { name: 'nombre_completo', label: 'Nombre', field: 'nombre_completo', sortable: true, type: 'string', align: 'left' },
          { name: 'licenciatura', label: 'Licenciatura', field: 'licenciatura', sortable: true, type: 'string', align: 'left' },
          { name: 'area', label: 'Área', field: 'area', sortable: true, type: 'string', align: 'left' },
          { name: 'aptitudes', label: 'Aptitudes', field: 'aptitudes', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        data: [],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_perfiles_involucrados: {
        columns: [
          { name: 'nombre', label: 'Nombre', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'licenciatura', label: 'Licenciatura', field: 'licenciatura', sortable: true, type: 'string', align: 'left' },
          { name: 'area', label: 'Área', field: 'area', sortable: true, type: 'string', align: 'left' },
          { name: 'participacion', label: 'Participación', field: 'participacion', sortable: true, type: 'string', align: 'left' },
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
      usuariosOptions: 'sys/users/selectOptionsName',
      entidadesOptions: 'vnt/estado/selectOptions',
      licitaciones: 'lic/licitacion/licitaciones',
      aptitudes: 'per/aptitudes/aptitudes'
    }),
    empresasOptions () {
      let empresas = this.$store.getters['vnt/empresa/selectOptions']
      empresas.push({label: '---Otra---', value: 0})
      empresas.push({label: '---Seleccione---', value: -1})
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
    estadosOptions () {
      let estado = this.$store.getters['vnt/estado/selectOptions']
      estado.push({label: '---Todos---', value: 0})
      estado.sort(function (a, b) {
        if (a.nombre > b.nombre) {
          return 1
        }
        if (a.nombre < b.nombre) {
          return -1
        }
        return 0
      })
      return estado
    },
    areasOptions () {
      let areas = this.$store.getters['per/areas/selectOptions']
      areas.push({label: '---Todos---', value: 0})
      areas.sort(function (a, b) {
        if (a.nombre > b.nombre) {
          return 1
        }
        if (a.nombre < b.nombre) {
          return -1
        }
        return 0
      })
      return areas
    },
    recursosOptions () {
      let recursos = this.$store.getters['vnt/recurso/selectOptions']
      recursos.push({label: '---Ninguno---', value: 0})
      return recursos
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
    open_requisitos (newValue, old) {
      if (newValue === true) {
        this.cargarRequisitos()
      }
    },
    open_propuestas (newValue, old) {
      if (newValue === true) {
        this.cargarPropuestas()
      }
    }
  },
  methods: {
    ...mapActions({
      getAllLicitaciones: 'lic/licitacion/refresh',
      saveLicitacion: 'lic/licitacion/save',
      updateLicitacion: 'lic/licitacion/update',
      deleteLicitacion: 'lic/licitacion/delete',
      getRecursos: 'vnt/recurso/refresh',
      getEmpresas: 'vnt/empresa/refresh',
      getEntidades: 'vnt/estado/refresh',
      getUsuarios: 'sys/users/refresh',
      filtrar: 'lic/licitacion/filtrar',
      getRespaldosByLicitacion: 'lic/respaldo/getByLicitacion',
      getBasesByLicitacion: 'lic/requerimientos/getBasesByLicitacion',
      saveRespaldo: 'lic/respaldo/save',
      deleteRespaldo: 'lic/respaldo/delete',
      deleteFormato: 'lic/requerimientos/deleteFormato',
      actualizarFormato: 'lic/requerimientos/update',
      guardarArchivo: 'lic/requerimientos/guardarArchivo',
      saveEvento: 'lic/eventos/save',
      getEventosByLicitacion: 'lic/eventos/getEventosByLicitacion',
      actualizarEvento: 'lic/eventos/update',
      borrarEvento: 'lic/eventos/delete',
      saveRequisito: 'lic/requisitos/save',
      getRequisitosByLicitacion: 'lic/requisitos/getRequisitosByLicitacion',
      updateRequisito: 'lic/requisitos/update',
      deleteRequisito: 'lic/requisitos/delete',
      savePropuesta: 'lic/propuestas/save',
      getPropuestasByLicitacion: 'lic/propuestas/getPropuestasByLicitacion',
      updatePropuesta: 'lic/propuestas/update',
      deletePropuesta: 'lic/propuestas/delete',
      eliminarDocumentoEntregable: 'lic/licitacion/deleteFile',
      getAreas: 'per/areas/refresh',
      getMunicipiosByEstado: 'vnt/municipio/getByEstado',
      getAptitudes: 'per/aptitudes/refresh',
      filtrarPerfiles: 'per/perfiles/filtrar',
      getPerfilesByLicitacion: 'per/licitaciones_perfiles/getByLicitacion',
      savePerfilLicitacion: 'per/licitaciones_perfiles/save',
      deletePerfilLicitacion: 'per/licitaciones_perfiles/delete',
      deleteDocEvento: 'lic/eventos/deleteFormato',
      createFrom: 'vnt/recurso/create_from_licitacion'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getLicitaciones()
      await this.getRecursos()
      await this.getEmpresas()
      await this.getEntidades()
      await this.getUsuarios()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    guardarLicitacion () {
      this.form.fields.titulo = this.form.fields.titulo.trim()
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.save()
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    save () {
      this.form.fields.titulo = this.form.fields.titulo.trim()
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        this.cantidad = this.evaluaMonto(this.form.fields.monto_inicial)
        this.form.fields.monto_inicial = this.cantidad
        this.cantidad2 = this.evaluaMonto(this.form.fields.monto_adjudicado)
        this.form.fields.monto_adjudicado = this.cantidad2
        this.cantidad3 = this.evaluaMonto(this.form.fields.monto_anticipo)
        this.form.fields.monto_anticipo = this.cantidad3
        let params = this.form.fields
        if (this.form.fields.campo_anticipo === true) {
          params.campo_anticipo = 'SI'
        } else {
          params.campo_anticipo = 'NO'
        }
        params.year = this.year
        this.saveLicitacion(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.loadAll()
            this.setView('grid')
            this.$q.notify({
              // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'done',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
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
    actualizarLicitacion () {
      this.form.fields.titulo = this.form.fields.titulo.trim()
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.update()
      } else {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    update () {
      this.form.fields.titulo = this.form.fields.titulo.trim()
      this.$v.form.$touch()
      this.validar_observaciones = true
      this.validar_observaciones_cancelada = true
      if (this.form.fields.status === 'NO ADJUDICADA') {
        if (!this.form.fields.observaciones_no_adjudicada) {
          this.validar_observaciones = false
          this.$showMessage('Error', 'El estatus de la licitación no cambiará si el campo de las observaciones está vacío.')
        } else if (this.form.fields.observaciones_no_adjudicada.trim() === '') {
          this.validar_observaciones = false
          this.$showMessage('Error', 'El estatus de la licitación no cambiará si el campo de las observaciones está vacío.')
        }
      }
      if (this.form.fields.status === 'CANCELADA') {
        if (!this.form.fields.observaciones_cancelada) {
          this.validar_observaciones_cancelada = false
          this.$showMessage('Error', 'El estatus de la licitación no cambiará si el campo de las observaciones está vacío.')
        } else if (this.form.fields.observaciones_cancelada.trim() === '') {
          this.validar_observaciones_cancelada = false
          this.$showMessage('Error', 'El estatus de la licitación no cambiará si el campo de las observaciones está vacío.')
        }
      }
      if (!this.$v.form.$error && this.validar_observaciones === true && this.validar_observaciones_cancelada === true) {
        this.loadingButton = true
        this.cantidad = this.evaluaMonto(this.form.fields.monto_inicial)
        this.form.fields.monto_inicial = this.cantidad
        this.cantidad2 = this.evaluaMonto(this.form.fields.monto_adjudicado)
        this.form.fields.monto_adjudicado = this.cantidad2
        this.cantidad3 = this.evaluaMonto(this.form.fields.monto_anticipo)
        this.form.fields.monto_anticipo = this.cantidad3
        let params = this.form.fields
        if (this.form.fields.campo_anticipo === true) {
          params.campo_anticipo = 'SI'
        } else {
          params.campo_anticipo = 'NO'
        }
        this.updateLicitacion(params).then(({data}) => {
          this.loadingButton = false
          this.$q.notify({
            // only required parameter is the message:
            message: data.message.content,
            timeout: 3000,
            type: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'done',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
          if (data.result === 'success') {
            this.loadAll()
            this.setView('grid')
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      } else if (this.$v.form.$error) {
        this.$showMessage('Error', 'Por favor revise los campos.')
      }
    },
    deleteRow (row) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea eliminar la licitación?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.deleteLicitacion({id: row.id}).then(({data}) => {
          this.loadingButton = false
          this.$q.notify({
            // only required parameter is the message:
            message: data.message.content,
            timeout: 3000,
            type: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'done',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
          if (data.result === 'success') {
            this.loadAll()
            this.setView('grid')
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
    },
    async editRow (row) {
      this.$v.form.$reset()
      let licitacion = { ...row }
      this.form.fields.id = licitacion.id
      this.form.fields.recurso_id = licitacion.recurso_id
      this.form.fields.empresa_id = licitacion.empresa_id
      this.form.fields.folio = licitacion.folio
      this.form.fields.titulo = licitacion.titulo
      this.form.fields.fecha_inicio = licitacion.fecha_inicio
      this.form.fields.fecha_fallo = licitacion.fecha_fallo
      this.form.fields.fecha_presentacion = licitacion.fecha_presentacion
      this.form.fields.fecha_arranque = licitacion.fecha_arranque
      this.form.fields.formato_requisito_id = 0
      this.form.fields.status = licitacion.status
      this.form.fields.entidad_id = licitacion.entidad_id
      this.form.fields.descripcion = licitacion.descripcion
      this.form.fields.contrato = licitacion.contrato
      this.form.fields.orden_compra = licitacion.orden_compra
      this.form.fields.monto_inicial = licitacion.monto_inicial
      this.form.fields.monto_adjudicado = licitacion.monto_adjudicado
      this.form.fields.comprador = licitacion.comprador
      this.form.fields.empresa = licitacion.empresa
      if (licitacion.campo_anticipo === 'SI') {
        this.form.fields.campo_anticipo = true
      } else {
        this.form.fields.campo_anticipo = false
      }
      this.form.fields.monto_anticipo = licitacion.monto_anticipo
      this.form.fields.porcentaje_anticipo = licitacion.porcentaje_anticipo
      this.form_bases.fields.nombre_documento = ''
      this.form_bases.fields.observaciones_archivo = ''

      if (licitacion.fecha_inicio === null) {
        this.form.fields.fecha_inicio = ''
      } else {
        this.form.fields.fecha_inicio = moment(String(licitacion.fecha_inicio)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      }
      if (licitacion.fecha_fallo === null) {
        this.form.fields.fecha_fallo = ''
      } else {
        this.form.fields.fecha_fallo = moment(String(licitacion.fecha_fallo)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      }
      if (licitacion.fecha_presentacion === null) {
        this.form.fields.fecha_presentacion = ''
      } else {
        this.form.fields.fecha_presentacion = moment(String(licitacion.fecha_presentacion)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      }
      if (licitacion.fecha_arranque === null) {
        this.form.fields.fecha_arranque = ''
      } else {
        this.form.fields.fecha_arranque = moment(String(licitacion.fecha_arranque)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      }
      //
      this.form.fields.observaciones_creada = licitacion.observaciones_creada
      this.form.fields.observaciones_proceso = licitacion.observaciones_proceso
      this.form.fields.observaciones_presentada = licitacion.observaciones_presentada
      this.form.fields.observaciones_adjudicada = licitacion.observaciones_adjudicada
      this.form.fields.observaciones_no_adjudicada = licitacion.observaciones_no_adjudicada
      this.form.fields.observaciones_cancelada = licitacion.observaciones_cancelada
      //
      this.form.fields.responsable = licitacion.responsable
      this.form.fields.responsable_gdp = licitacion.responsable_gdp
      // Aqui esta form_respaldo licitacion_id
      this.form_respaldo.fields.licitacion_id = this.form.fields.id
      this.form_bases.fields.licitacion_id = this.form.fields.id
      this.form_eventos.fields.licitacion_id = this.form.fields.id
      this.form_requisitos.fields.licitacion_id = this.form.fields.id
      this.form_requisitos.fields.ganadora = this.form.fields.empresa_id
      this.form_propuestas.fields.licitacion_id = this.form.fields.id
      this.form_propuestas.fields.ganadora = this.form.fields.empresa_id
      // await this.cargarDocumentos()
      await this.cargarRespaldos()
      this.setView('update')
    },
    newRow () {
      this.$v.form.$reset()
      this.form.fields.recurso_id = 0
      this.form.fields.empresa_id = -1
      this.form.fields.fecha_inicio = ''
      this.form.fields.titulo = ''
      this.form.fields.folio = ''
      this.form.fields.fecha_fallo = ''
      this.form.fields.fecha_presentacion = ''
      this.form.fields.fecha_arranque = ''
      this.form.fields.status = 'CREADA'
      this.form.fields.entidad_id = 0
      this.form.fields.descripcion = ''
      this.form.fields.respaldo1 = -1
      this.form.fields.respaldo2 = -1
      this.form.fields.respaldo3 = -1
      this.form.fields.respaldo4 = -1
      this.form.fields.empresa_respaldo_1 = ''
      this.form.fields.empresa_respaldo_2 = ''
      this.form.fields.empresa_respaldo_3 = ''
      this.form.fields.empresa_respaldo_4 = ''
      this.form.fields.contrato = ''
      this.form.fields.orden_compra = ''
      this.form.fields.porcentaje_anticipo = 0
      this.form.fields.monto_inicial = 0
      this.form.fields.monto_adjudicado = 0
      this.form.fields.monto_anticipo = 0
      this.form.fields.campo_anticipo = false
      this.form.fields.comprador = ''
      this.form.fields.empresa = ''
      //
      this.form.fields.observaciones_creada = ''
      this.form.fields.observaciones_proceso = ''
      this.form.fields.observaciones_presentada = ''
      this.form.fields.observaciones_adjudicada = ''
      this.form.fields.observaciones_no_adjudicada = ''
      this.form.fields.observaciones_cancelada = ''
      this.form.fields.responsable = ''
      this.form.fields.responsable_gdp = ''
      this.setView('create')
    },
    async clickFila (row) {
      this.$v.form.$reset()
      let licitacion = { ...row }
      this.form.fields.id = licitacion.id
      this.form.fields.recurso_id = licitacion.recurso_id
      this.form.fields.empresa_id = licitacion.empresa_id
      this.form.fields.folio = licitacion.folio
      this.form.fields.titulo = licitacion.titulo
      this.form.fields.fecha_inicio = licitacion.fecha_inicio
      this.form.fields.fecha_fallo = licitacion.fecha_fallo
      this.form.fields.fecha_presentacion = licitacion.fecha_presentacion
      this.form.fields.fecha_arranque = licitacion.fecha_arranque
      this.form.fields.formato_requisito_id = 0
      this.form.fields.status = licitacion.status
      this.form.fields.entidad_id = licitacion.entidad_id
      this.form.fields.descripcion = licitacion.descripcion
      this.form.fields.contrato = licitacion.contrato
      this.form.fields.orden_compra = licitacion.orden_compra
      this.form.fields.porcentaje_anticipo = licitacion.porcentaje_anticipo
      this.form.fields.monto_inicial = licitacion.monto_inicial
      this.form.fields.monto_adjudicado = licitacion.monto_adjudicado
      this.form.fields.monto_anticipo = licitacion.monto_anticipo
      this.form.fields.comprador = licitacion.comprador
      this.form.fields.empresa = licitacion.empresa
      if (licitacion.campo_anticipo === 'SI') {
        this.form.fields.campo_anticipo = true
      } else {
        this.form.fields.campo_anticipo = false
      }
      this.form_bases.fields.nombre_documento = ''
      this.form_bases.fields.observaciones_archivo = ''

      if (licitacion.fecha_inicio === null) {
        this.form.fields.fecha_inicio = ''
      } else {
        this.form.fields.fecha_inicio = moment(String(licitacion.fecha_inicio)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      }
      if (licitacion.fecha_fallo === null) {
        this.form.fields.fecha_fallo = ''
      } else {
        this.form.fields.fecha_fallo = moment(String(licitacion.fecha_fallo)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      }
      if (licitacion.fecha_presentacion === null) {
        this.form.fields.fecha_presentacion = ''
      } else {
        this.form.fields.fecha_presentacion = moment(String(licitacion.fecha_presentacion)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      }
      if (licitacion.fecha_arranque === null) {
        this.form.fields.fecha_arranque = ''
      } else {
        this.form.fields.fecha_arranque = moment(String(licitacion.fecha_arranque)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      }
      //
      this.form.fields.observaciones_creada = licitacion.observaciones_creada
      this.form.fields.observaciones_proceso = licitacion.observaciones_proceso
      this.form.fields.observaciones_presentada = licitacion.observaciones_presentada
      this.form.fields.observaciones_adjudicada = licitacion.observaciones_adjudicada
      this.form.fields.observaciones_no_adjudicada = licitacion.observaciones_no_adjudicada
      this.form.fields.observaciones_cancelada = licitacion.observaciones_cancelada
      //
      // Aqui esta form_respaldo licitacion_id
      this.form_respaldo.fields.licitacion_id = this.form.fields.id
      this.form_bases.fields.licitacion_id = this.form.fields.id
      this.form_eventos.fields.licitacion_id = this.form.fields.id
      this.form_requisitos.fields.licitacion_id = this.form.fields.id
      this.form_requisitos.fields.ganadora = this.form.fields.empresa_id
      this.form_propuestas.fields.licitacion_id = this.form.fields.id
      this.form_propuestas.fields.ganadora = this.form.fields.empresa_id
      // await this.cargarDocumentos()
      await this.cargarRespaldos()
      this.informacion = true
    },
    async cargarRespaldos () {
      console.log('Cargando respaldos')
      this.form_respaldo.loading = true
      this.form_respaldo.data = []
      this.getRespaldosByLicitacion({licitacion_id: this.form_respaldo.fields.licitacion_id}).then(({data}) => {
        this.form_respaldo.loading = false
        if (data.licitaciones_respaldo.length > 0) {
          if (data.licitaciones_respaldo.length === 1) {
            this.form.fields.respaldo1 = data.licitaciones_respaldo[0].empresa_id
            this.form.fields.id_respaldo1 = data.licitaciones_respaldo[0].id
            this.form.fields.empresa_respaldo_1 = data.licitaciones_respaldo[0].empresa
            this.form_requisitos.fields.respaldo1 = data.licitaciones_respaldo[0].id
            this.form_propuestas.fields.respaldo1 = data.licitaciones_respaldo[0].id
            this.form.fields.respaldo2 = -1
            this.form.fields.respaldo3 = -1
            this.form.fields.respaldo4 = -1
            this.form.fields.empresa_respaldo_2 = ''
            this.form.fields.empresa_respaldo_3 = ''
            this.form.fields.empresa_respaldo_4 = ''
            console.log(1)
          }
          if (data.licitaciones_respaldo.length === 2) {
            this.form.fields.respaldo1 = data.licitaciones_respaldo[0].empresa_id
            this.form.fields.respaldo2 = data.licitaciones_respaldo[1].empresa_id
            this.form.fields.id_respaldo1 = data.licitaciones_respaldo[0].id
            this.form.fields.id_respaldo2 = data.licitaciones_respaldo[1].id
            this.form.fields.empresa_respaldo_1 = data.licitaciones_respaldo[0].empresa
            this.form.fields.empresa_respaldo_2 = data.licitaciones_respaldo[1].empresa
            this.form_requisitos.fields.respaldo1 = data.licitaciones_respaldo[0].id
            this.form_requisitos.fields.respaldo2 = data.licitaciones_respaldo[1].id
            this.form_propuestas.fields.respaldo1 = data.licitaciones_respaldo[0].id
            this.form_propuestas.fields.respaldo2 = data.licitaciones_respaldo[1].id
            this.form.fields.respaldo3 = -1
            this.form.fields.respaldo4 = -1
            this.form.fields.empresa_respaldo_3 = ''
            this.form.fields.empresa_respaldo_4 = ''
            console.log(2)
          }
          if (data.licitaciones_respaldo.length === 3) {
            this.form.fields.respaldo1 = data.licitaciones_respaldo[0].empresa_id
            this.form.fields.respaldo2 = data.licitaciones_respaldo[1].empresa_id
            this.form.fields.respaldo3 = data.licitaciones_respaldo[2].empresa_id
            this.form.fields.id_respaldo1 = data.licitaciones_respaldo[0].id
            this.form.fields.id_respaldo2 = data.licitaciones_respaldo[1].id
            this.form.fields.id_respaldo3 = data.licitaciones_respaldo[2].id
            this.form.fields.empresa_respaldo_1 = data.licitaciones_respaldo[0].empresa
            this.form.fields.empresa_respaldo_2 = data.licitaciones_respaldo[1].empresa
            this.form.fields.empresa_respaldo_3 = data.licitaciones_respaldo[2].empresa
            this.form_requisitos.fields.respaldo1 = data.licitaciones_respaldo[0].id
            this.form_requisitos.fields.respaldo2 = data.licitaciones_respaldo[1].id
            this.form_requisitos.fields.respaldo3 = data.licitaciones_respaldo[2].id
            this.form_propuestas.fields.respaldo1 = data.licitaciones_respaldo[0].id
            this.form_propuestas.fields.respaldo2 = data.licitaciones_respaldo[1].id
            this.form_propuestas.fields.respaldo3 = data.licitaciones_respaldo[2].id
            this.form.fields.respaldo4 = -1
            this.form.fields.empresa_respaldo_4 = ''
            console.log(3)
          }
          if (data.licitaciones_respaldo.length === 4 || data.licitaciones_respaldo.length > 4) {
            this.form.fields.respaldo1 = data.licitaciones_respaldo[0].empresa_id
            this.form.fields.respaldo2 = data.licitaciones_respaldo[1].empresa_id
            this.form.fields.respaldo3 = data.licitaciones_respaldo[2].empresa_id
            this.form.fields.respaldo4 = data.licitaciones_respaldo[3].empresa_id
            this.form.fields.id_respaldo1 = data.licitaciones_respaldo[0].id
            this.form.fields.id_respaldo2 = data.licitaciones_respaldo[1].id
            this.form.fields.id_respaldo3 = data.licitaciones_respaldo[2].id
            this.form.fields.id_respaldo4 = data.licitaciones_respaldo[3].id
            this.form.fields.empresa_respaldo_1 = data.licitaciones_respaldo[0].empresa
            this.form.fields.empresa_respaldo_2 = data.licitaciones_respaldo[1].empresa
            this.form.fields.empresa_respaldo_3 = data.licitaciones_respaldo[2].empresa
            this.form.fields.empresa_respaldo_4 = data.licitaciones_respaldo[3].empresa
            this.form_requisitos.fields.respaldo1 = data.licitaciones_respaldo[0].id
            this.form_requisitos.fields.respaldo2 = data.licitaciones_respaldo[1].id
            this.form_requisitos.fields.respaldo3 = data.licitaciones_respaldo[2].id
            this.form_requisitos.fields.respaldo4 = data.licitaciones_respaldo[3].id
            this.form_propuestas.fields.respaldo1 = data.licitaciones_respaldo[0].id
            this.form_propuestas.fields.respaldo2 = data.licitaciones_respaldo[1].id
            this.form_propuestas.fields.respaldo3 = data.licitaciones_respaldo[2].id
            this.form_propuestas.fields.respaldo4 = data.licitaciones_respaldo[3].id
            console.log(4)
          }
        } else {
          this.form.fields.respaldo1 = -1
          this.form.fields.respaldo2 = -1
          this.form.fields.respaldo3 = -1
          this.form.fields.respaldo4 = -1
          this.form.fields.empresa_respaldo_1 = ''
          this.form.fields.empresa_respaldo_2 = ''
          this.form.fields.empresa_respaldo_3 = ''
          this.form.fields.empresa_respaldo_4 = ''
        }
      }).catch(error => {
        this.form_respaldo.loading = false
        console.error(error)
      })
    },
    async cargarDocumentos () {
      this.form_bases.loading = true
      this.form_bases.data = []
      this.getBasesByLicitacion({licitacion_id: this.form_bases.fields.licitacion_id}).then(({data}) => {
        this.form_bases.loading = false
        if (data.bases.length > 0) {
          data.bases.forEach(function (documento) {
            if (documento.doc_type === 'docx') {
              documento.color = 'blue-9'
              documento.icon = 'fas fa-file-word'
            } else if (documento.doc_type === 'pdf') {
              documento.color = 'red-10'
              documento.icon = 'fas fa-file-pdf'
            } else if (documento.doc_type === 'xml') {
              documento.color = 'light-green'
              documento.icon = 'fas fa-file-code'
            } else if (documento.doc_type === 'jpg' || documento.doc_type === 'jpg' || documento.doc_type === 'png') {
              documento.color = 'amber'
              documento.icon = 'fas fa-file-image'
            }
          })
          this.form_bases.data = data.bases
        }
      }).catch(error => {
        this.form_bases.loading = false
        console.error(error)
      })
    },
    checkFile () {
      if (this.$refs.fileInputFormato.files !== null) {
        this.showSelectedFile = true
      }
    },
    uploadFormato () {
      if (this.form_bases.fields.nombre_documento !== '') {
        try {
          this.loadingButton = true
          let file = this.$refs.fileInputFormato.files[0]
          let formData = new FormData()
          console.log(file.name)
          this.form_bases.fields.name2 = file.name
          formData.append('file', file)
          formData.append('formato_requisito_id', this.form.fields.formato_requisito_id)
          formData.append('nombre', file.name)
          formData.append('licitacion_id', this.form_bases.fields.licitacion_id)
          formData.append('observaciones_archivo', this.form_bases.fields.observaciones_archivo)
          formData.append('nombre_documento', this.form_bases.fields.nombre_documento)
          if (file === null || file === undefined) {
            this.loadingButton = false
          } else if (file.type) {
            if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
              this.$q.loading.show({message: 'Subiendo archivo...'})
              axios.post('licitaciones/uploadArchivo', formData, {
                headers: {
                  'Content-Type': 'multipart/form-data'
                }
              }).then(response => {
                if (response.data.result === 'success') {
                  this.$q.notify({
                    // only required parameter is the message:
                    message: 'Se ha guardado el documento',
                    timeout: 3000,
                    type: 'green',
                    textColor: 'white', // if default 'white' doesn't fits
                    icon: 'done',
                    position: 'top-right' // 'top', 'left', 'bottom-left' etc
                  })
                  console.log(response)
                  this.showSelectedFile = false
                  this.cargarDocumentos()
                  this.form_bases.fields.nombre_documento = ''
                  this.form_bases.fields.observaciones_archivo = ''
                  this.$v.form_bases.$reset()
                  // this.form_bases.fields.observaciones_archivo = ''
                } else {
                  this.$refs.fileInputFormato.value = ''
                  this.showSelectedFile = false
                  console.log(response)
                  if (response.data.err !== '') {
                    console.log(response)
                    console.log(file.type.split('/')[1].toLowerCase())
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
              this.$refs.fileInputFormato.value = ''
              this.showSelectedFile = false
              this.loadingButton = false
              this.$showMessage('Error', 'El tipo de archivo no es el requerido para este requisito.')
              console.log(file.type.split('/')[1].toLowerCase())
            }
          } else {
            this.loadingButton = false
            this.$showMessage('Error', 'No ha seleccionado un archivo con la extensión .docx')
          }
        } catch (error) {
          this.loadingButton = false
        }
      }
    },
    selectedFile (row) {
      this.$refs.fileInput.value = ''
      this.registro = row
      this.$refs.fileInput.click()
    },
    uploadImageFile () {
      try {
        // this.loadingButton = true
        let file = this.$refs.fileInput.files[0]
        let formData = new FormData()
        console.log(file.name)
        console.log(this.registro.id)
        // this.form_bases.fields.name2 = file.name
        formData.append('file', file)
        if (this.registro.name === null || this.registro.name === 'null') {
          formData.append('formato_requisito_id', 0)
        } else {
          formData.append('formato_requisito_id', this.registro.documento_id)
          formData.append('nombre_antiguo', this.registro.name)
        }
        formData.append('nombre', file.name)
        formData.append('id', this.registro.id)
        formData.append('licitacion_id', this.registro.licitacion_id)
        if (file === null || file === undefined) {
          // this.loadingButton = false
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('requerimientos/uploadArchivo', formData, {
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
                this.showSelectedFile = false
                this.cargarDocumentos()
                // this.form_bases.fields.observaciones_archivo = ''
              } else {
                if (response.data.err !== '') {
                  console.log(file.type.split('/')[1].toLowerCase())
                  this.$showMessage('Errores en archivo', response.data.err)
                }
              }
              // this.loadingButton = false
              this.$q.loading.hide()
            }).catch(error => {
              // this.loadingButton = false
              console.error(error)
              this.$q.loading.hide()
            })
          } else {
            // this.loadingButton = false
            this.$showMessage('Error', 'El tipo de archivo no es el requerido para este requisito.')
            console.log(file.type.split('/')[1].toLowerCase())
          }
        } else {
          // this.loadingButton = false
          this.$showMessage('Error', 'No ha seleccionado un archivo con la extensión .jpg, .jpeg, .png, .pdf, .xml')
        }
      } catch (error) {
        // this.loadingButton = false
      }
    },
    descargarFormato (row) {
      let uri = process.env.API + `licitaciones/getFile/${row.documento_id}/${row.doc_type}`
      window.open(uri, '_blank')
    },
    async documentos () {
      await this.cargarDocumentos()
    },
    deleteFormatoBase (row) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este formato de requisitos?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        let params = {id: row.id, documento_id: row.documento_id}
        this.deleteFormato(params).then(({data}) => {
          console.log(data)
          if (data.result === 'success') {
            this.$q.notify({
              // only required parameter is the message:
              message: 'Se ha eliminado el formato',
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'delete',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.cargarDocumentos()
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
    },
    agregar () {
      this.$v.form_bases.$touch()
      if (!this.$v.form_bases.$error) {
        this.loadingButton = true
        let params = this.form_bases.fields
        this.guardarArchivo(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.form_bases.fields.nombre_documento = ''
            this.form_bases.fields.observaciones_archivo = ''
            this.$v.form_bases.$reset()
            this.$q.notify({
              // only required parameter is the message:
              message: 'Se ha creado el documento',
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'done',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.cargarDocumentos()
            this.loadingButton = false
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
          this.loadingButton = false
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
        this.loadingButton = false
      }
    },
    updateFormatoBase (row) {
      this.form_bases.fields.id = row.id
      this.form_bases.fields.nombre_documento = row.nombre_documento
      this.form_bases.fields.observaciones_archivo = row.descripcion
      this.form_bases.editar = true
    },
    updateFormato () {
      this.$v.form_bases.$touch()
      if (!this.$v.form_bases.$error) {
        let bases = this.form_bases.fields
        this.actualizarFormato(bases).then(({data}) => {
          if (data.result === 'success') {
            this.form_bases.editar = false
            this.form_bases.fields.nombre_documento = ''
            this.form_bases.fields.observaciones_archivo = ''
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
            this.form_bases.fields.nombre_documento = ''
            this.form_bases.fields.observaciones_archivo = ''
            this.$v.form_bases.$reset()
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
          this.form_bases.editar = false
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
        this.form_bases.editar = false
      }
    }, // AQUI EMPIEZA CODIGO DE EVENTOS
    async cargarEventos () {
      this.form_eventos.loading = true
      this.form_eventos.data = []
      this.getEventosByLicitacion({licitacion_id: this.form_eventos.fields.licitacion_id}).then(({data}) => {
        this.form_eventos.loading = false
        if (data.eventos.length > 0) {
          data.eventos.forEach(function (documento) {
            if (documento.name !== null) {
              if (documento.doc_type.toLowerCase() === 'docx') {
                documento.color = 'blue-9'
                documento.icon = 'fas fa-file-word'
              } else if (documento.doc_type.toLowerCase() === 'pdf') {
                documento.color = 'red-10'
                documento.icon = 'fas fa-file-pdf'
              } else if (documento.doc_type.toLowerCase() === 'xml') {
                documento.color = 'light-green'
                documento.icon = 'fas fa-file-code'
              } else if (documento.doc_type.toLowerCase() === 'jpg' || documento.doc_type.toLowerCase() === 'jpg' || documento.doc_type.toLowerCase() === 'png') {
                documento.color = 'amber'
                documento.icon = 'fas fa-file-image'
              }
            }
          })
          this.form_eventos.data = data.eventos
        }
      }).catch(error => {
        this.form_eventos.loading = false
        console.error(error)
      })
    },
    async eventos () {
      await this.cargarEventos()
    },
    limpiar_evento () {
      this.form_eventos.fields.evento = ''
      this.form_eventos.fields.fecha_evento = ''
      this.$v.form_eventos.$reset()
    },
    agregarEvento () {
      this.$v.form_eventos.$touch()
      if (!this.$v.form_eventos.$error) {
        this.form_eventos.fields.fecha_evento = moment(String(this.form_eventos.fields.fecha_evento)).utcOffset('-06:00:00', false).format('YYYY-MM-DD HH:mm:ss')
        let eventos = this.form_eventos.fields
        this.saveEvento(eventos).then(({data}) => {
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
            this.limpiar_evento()
            this.cargarEventos()
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.log(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    editEvento (row) {
      this.form_eventos.fields.id = row.id
      this.form_eventos.fields.evento = row.evento
      this.form_eventos.fields.fecha_evento = row.fecha_evento
      this.form_eventos.editar = true
    },
    updateEvento () {
      this.$v.form_eventos.$touch()
      if (!this.$v.form_eventos.$error) {
        let evento = this.form_eventos.fields
        this.actualizarEvento(evento).then(({data}) => {
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
            this.form_eventos.editar = false
            this.limpiar_evento()
            this.cargarEventos()
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.log(error)
          this.form_eventos.editar = false
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    deleteEvento (row) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este evento?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        let params = {id: row.id, documento_id: row.documento_id}
        this.borrarEvento(params).then(({data}) => {
          console.log(data)
          if (data.result === 'success') {
            this.$q.notify({
              // only required parameter is the message:
              message: 'Se ha eliminado el evento',
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'delete',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.cargarEventos()
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
    },
    selectedFileEvento (row) {
      this.$refs.eventoInput.value = ''
      this.registro_evento = row
      this.$refs.eventoInput.click()
    },
    uploadEventFile () {
      try {
        // this.loadingButton = true
        let file = this.$refs.eventoInput.files[0]
        let formData = new FormData()
        console.log(file.name)
        console.log(this.registro_evento.id)
        // this.form_bases.fields.name2 = file.name
        formData.append('file', file)
        if (this.registro_evento.name === null || this.registro_evento.name === 'null') {
          formData.append('formato_requisito_id', 0)
        } else {
          formData.append('formato_requisito_id', this.registro_evento.documento_id)
          console.log(this.registro_evento.documento_id)
          formData.append('nombre_antiguo', this.registro_evento.name)
        }
        formData.append('nombre', file.name)
        formData.append('id', this.registro_evento.id)
        formData.append('licitacion_id', this.registro_evento.licitacion_id)
        if (file === null || file === undefined) {
          // this.loadingButton = false
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('eventos/uploadArchivo', formData, {
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
                this.cargarEventos()
                // this.form_bases.fields.observaciones_archivo = ''
              } else {
                if (response.data.err !== '') {
                  console.log(file.type.split('/')[1].toLowerCase())
                  this.$showMessage('Errores en archivo', response.data.err)
                }
              }
              // this.loadingButton = false
              this.$q.loading.hide()
            }).catch(error => {
              // this.loadingButton = false
              console.error(error)
              this.$q.loading.hide()
            })
          } else {
            // this.loadingButton = false
            this.$showMessage('Error', 'El tipo de archivo no es el requerido para este requisito.')
            console.log(file.type.split('/')[1].toLowerCase())
          }
        } else {
          // this.loadingButton = false
          this.$showMessage('Error', 'No ha seleccionado un archivo con la extensión .jpg, .jpeg, .png, .pdf, .xml')
        }
      } catch (error) {
        // this.loadingButton = false
      }
    },
    descargarFormatoEvento (row) {
      console.log(row.documento_id + '.' + row.doc_type)
      let uri = process.env.API + `eventos/getFile/${row.documento_id}/${row.doc_type}`
      window.open(uri, '_blank')
    },
    selectedEvento (row) {
      this.$refs.docEventoInput.value = ''
      this.registro_evento = row
      this.$refs.docEventoInput.click()
    },
    uploadDocsEvento () {
      try {
        let file = this.$refs.docEventoInput.files[0]
        let formData = new FormData()
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('id', this.registro_evento.id)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('eventos/uploadDocumento', formData, {
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
                this.eventos()
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
    verDocumentoEvento (id, ext) {
      let uri = process.env.API + '/public/assets/licitaciones/documentos_eventos/' + id + '.' + ext
      window.open(uri, '_blank')
    },
    descargarDocumentoEvento (id, ext) {
      let uri = process.env.API + `eventos/getFileDocumentos/${id}/${ext}`
      window.open(uri, '_blank')
    },
    deleteDocsEvento (id) {
      this.$q.dialog({
        title: 'Confirmar acción',
        message: '¿Desea eliminar el archivo?',
        ok: 'Eliminar',
        cancel: 'Cancelar',
        preventClose: true
      }).then(() => {
        this.delete_documento_evento(id)
      }).catch(() => {
      })
    },
    delete_documento_evento (id) {
      this.deleteDocEvento({id: id}).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'delete', position: 'top-right'
          })
          this.eventos()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    getLicitaciones () {
      this.form.loading = true
      this.form.data = []
      let params = this.form_filtros.fields
      params.year = this.year
      this.filtrar(params).then(({data}) => {
        if (data.licitaciones.length > 0) {
          data.licitaciones.forEach(function (element) {
            // console.log('----------------------')
            if (element.status === 'PRESENTADA') {
              element.color = 'amber'
              element.icon = 'fas fa-arrow-circle-right'
            } else if (element.status === 'EN PROCESO') {
              element.color = 'blue'
              element.icon = 'restore'
            } else if (element.status === 'CREADA') {
              element.color = 'grey'
              element.icon = 'fas fa-arrow-circle-right'
            } else if (element.status === 'ADJUDICADA') {
              element.color = 'green'
              element.icon = 'fas fa-arrow-circle-up'
            } else if (element.status === 'NO ADJUDICADA') {
              element.color = 'orange'
              element.icon = 'error_outline'
            } else if (element.status === 'CANCELADA') {
              element.color = 'red'
              element.icon = 'highlight_off'
            }
            if (element.extension !== null) {
              if (element.extension.toLowerCase() === 'docx') {
                element.color_ef = 'blue-9'
                element.icon_ef = 'fas fa-file-word'
              } else if (element.extension.toLowerCase() === 'pdf') {
                element.color_ef = 'red-10'
                element.icon_ef = 'fas fa-file-pdf'
              } else if (element.extension.toLowerCase() === 'xml') {
                element.color_ef = 'light-green'
                element.icon_ef = 'fas fa-file-code'
              } else if (element.extension.toLowerCase() === 'jpg' || element.extension.toLowerCase() === 'jpg' || element.extension.toLowerCase() === 'png') {
                element.color_ef = 'amber'
                element.icon_ef = 'fas fa-file-image'
              }
            }
          })
          this.form.data = data.licitaciones
        } else {
          this.form.data = []
        }
        this.form.loading = false
      }).catch(error => {
        console.error(error)
      })
    },
    async requisitos () {
      await this.cargarRequisitos()
      await this.getUsuarios()
    },
    agregarRequisito () {
      this.$v.form_requisitos.$touch()
      if (!this.$v.form_requisitos.$error) {
        let requisitos = this.form_requisitos.fields
        this.saveRequisito(requisitos).then(({data}) => {
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
            this.limpiar_requisito()
            this.cargarRequisitos()
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.log(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    async cargarRequisitos () {
      this.form_requisitos.loading = true
      this.form_requisitos.data = []
      this.getRequisitosByLicitacion({licitacion_id: this.form_requisitos.fields.licitacion_id}).then(({data}) => {
        this.form_requisitos.loading = false
        if (data.requisitos.length > 0) {
          data.requisitos.forEach(function (documento) {
            if (documento.name1 !== null) {
              if (documento.doc_type1.toLowerCase() === 'docx') {
                documento.color1 = 'blue-9'
                documento.icon1 = 'fas fa-file-word'
              } else if (documento.doc_type1.toLowerCase() === 'pdf') {
                documento.color1 = 'red-10'
                documento.icon1 = 'fas fa-file-pdf'
              } else if (documento.doc_type1.toLowerCase() === 'xml') {
                documento.color1 = 'light-green'
                documento.icon1 = 'fas fa-file-code'
              } else if (documento.doc_type1.toLowerCase() === 'jpg' || documento.doc_type1.toLowerCase() === 'jpg' || documento.doc_type1.toLowerCase() === 'png') {
                documento.color1 = 'amber'
                documento.icon1 = 'fas fa-file-image'
              }
            }
            if (documento.name2 !== null) {
              if (documento.doc_type2.toLowerCase() === 'docx') {
                documento.color2 = 'blue-9'
                documento.icon2 = 'fas fa-file-word'
              } else if (documento.doc_type2.toLowerCase() === 'pdf') {
                documento.color2 = 'red-10'
                documento.icon2 = 'fas fa-file-pdf'
              } else if (documento.doc_type2.toLowerCase() === 'xml') {
                documento.color2 = 'light-green'
                documento.icon2 = 'fas fa-file-code'
              } else if (documento.doc_type2.toLowerCase() === 'jpg' || documento.doc_type2.toLowerCase() === 'jpg' || documento.doc_type2.toLowerCase() === 'png') {
                documento.color2 = 'amber'
                documento.icon2 = 'fas fa-file-image'
              }
            }
            if (documento.name3 !== null) {
              if (documento.doc_type3.toLowerCase() === 'docx') {
                documento.color3 = 'blue-9'
                documento.icon3 = 'fas fa-file-word'
              } else if (documento.doc_type3.toLowerCase() === 'pdf') {
                documento.color3 = 'red-10'
                documento.icon3 = 'fas fa-file-pdf'
              } else if (documento.doc_type3.toLowerCase() === 'xml') {
                documento.color3 = 'light-green'
                documento.icon3 = 'fas fa-file-code'
              } else if (documento.doc_type3.toLowerCase() === 'jpg' || documento.doc_type3.toLowerCase() === 'jpg' || documento.doc_type3.toLowerCase() === 'png') {
                documento.color3 = 'amber'
                documento.icon3 = 'fas fa-file-image'
              }
            }
            if (documento.name4 !== null) {
              if (documento.doc_type4.toLowerCase() === 'docx') {
                documento.color4 = 'blue-9'
                documento.icon4 = 'fas fa-file-word'
              } else if (documento.doc_type4.toLowerCase() === 'pdf') {
                documento.color4 = 'red-10'
                documento.icon4 = 'fas fa-file-pdf'
              } else if (documento.doc_type4.toLowerCase() === 'xml') {
                documento.color4 = 'light-green'
                documento.icon4 = 'fas fa-file-code'
              } else if (documento.doc_type4.toLowerCase() === 'jpg' || documento.doc_type4.toLowerCase() === 'jpg' || documento.doc_type4.toLowerCase() === 'png') {
                documento.color4 = 'amber'
                documento.icon4 = 'fas fa-file-image'
              }
            }
            if (documento.name0 !== null) {
              if (documento.doc_type0.toLowerCase() === 'docx') {
                documento.color0 = 'blue-9'
                documento.icon0 = 'fas fa-file-word'
              } else if (documento.doc_type0.toLowerCase() === 'pdf') {
                documento.color0 = 'red-10'
                documento.icon0 = 'fas fa-file-pdf'
              } else if (documento.doc_type0.toLowerCase() === 'xml') {
                documento.color0 = 'light-green'
                documento.icon0 = 'fas fa-file-code'
              } else if (documento.doc_type0.toLowerCase() === 'jpg' || documento.doc_type0.toLowerCase() === 'jpg' || documento.doc_type0.toLowerCase() === 'png') {
                documento.color0 = 'amber'
                documento.icon0 = 'fas fa-file-image'
              }
            }
          })
          this.form_requisitos.data = data.requisitos
        }
      }).catch(error => {
        this.form_requisitos.loading = false
        console.error(error)
      })
    },
    limpiar_requisito () {
      this.form_requisitos.fields.requisito = ''
      this.form_requisitos.fields.fecha = ''
      this.form_requisitos.fields.descripcion = ''
      this.form_requisitos.fields.responsable = 0
      this.form_requisitos.fields.responsable1 = 0
      this.form_requisitos.fields.responsable2 = 0
      this.form_requisitos.fields.responsable3 = 0
      this.form_requisitos.fields.responsable4 = 0
      this.$v.form_requisitos.$reset()
    },
    cambiarResponsable (row) {
      this.updateRequisito({id: row.id, requisito: row.requisito, descripcion: row.descripcion, fecha: row.fecha, responsable: row.responsable, responsable1: row.responsable1, responsable2: row.responsable2, responsable3: row.responsable3, responsable4: row.responsable4}).then(({data}) => {
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
          this.cargarRequisitos()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.log(error)
      })
    },
    editRequisito (row) {
      this.form_requisitos.fields.id = row.id
      this.form_requisitos.fields.requisito = row.requisito
      this.form_requisitos.fields.fecha = row.fecha
      this.form_requisitos.fields.descripcion = row.descripcion
      this.form_requisitos.fields.responsable = row.responsable
      this.form_requisitos.fields.responsable1 = row.responsable1
      this.form_requisitos.fields.responsable2 = row.responsable2
      this.form_requisitos.fields.responsable3 = row.responsable3
      this.form_requisitos.fields.responsable4 = row.responsable4
      this.form_requisitos.editar = true
    },
    actualizarRequisito () {
      this.$v.form_requisitos.$touch()
      if (!this.$v.form_requisitos.$error) {
        let requisito = this.form_requisitos.fields
        this.updateRequisito(requisito).then(({data}) => {
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
            this.form_requisitos.editar = false
            this.limpiar_requisito()
            this.cargarRequisitos()
          }
        }).catch(error => {
          console.log(error)
          this.form_requisitos.editar = false
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    eliminarRequisito (row) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este requisito?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.deleteRequisito({id: row.id}).then(({data}) => {
          if (data.result === 'success') {
            this.$q.notify({
              // only required parameter is the message:
              message: 'Se ha eliminado el requisito',
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'done',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.cargarRequisitos()
          }
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
    },
    selectedFileDocumento (row) {
      this.form_requisitos.bandera_ganadora = true
      this.form_requisitos.bandera_documento1 = false
      this.form_requisitos.bandera_documento2 = false
      this.form_requisitos.bandera_documento3 = false
      this.form_requisitos.bandera_documento4 = false
      this.$refs.requisitoInput.value = ''
      this.registro_requisito = row
      this.$refs.requisitoInput.click()
    },
    selectedFileDocumento1 (row) {
      this.form_requisitos.bandera_documento1 = true
      this.form_requisitos.bandera_ganadora = false
      this.form_requisitos.bandera_documento2 = false
      this.form_requisitos.bandera_documento3 = false
      this.form_requisitos.bandera_documento4 = false
      this.$refs.requisitoInput.value = ''
      this.registro_requisito = row
      this.$refs.requisitoInput.click()
    },
    selectedFileDocumento2 (row) {
      this.form_requisitos.bandera_documento2 = true
      this.form_requisitos.bandera_ganadora = false
      this.form_requisitos.bandera_documento1 = false
      this.form_requisitos.bandera_documento3 = false
      this.form_requisitos.bandera_documento4 = false
      this.$refs.requisitoInput.value = ''
      this.registro_requisito = row
      this.$refs.requisitoInput.click()
    },
    selectedFileDocumento3 (row) {
      this.form_requisitos.bandera_documento3 = true
      this.form_requisitos.bandera_ganadora = false
      this.form_requisitos.bandera_documento1 = false
      this.form_requisitos.bandera_documento2 = false
      this.form_requisitos.bandera_documento4 = false
      this.$refs.requisitoInput.value = ''
      this.registro_requisito = row
      this.$refs.requisitoInput.click()
    },
    selectedFileDocumento4 (row) {
      this.form_requisitos.bandera_documento4 = true
      this.form_requisitos.bandera_ganadora = false
      this.form_requisitos.bandera_documento1 = false
      this.form_requisitos.bandera_documento2 = false
      this.form_requisitos.bandera_documento3 = false
      this.$refs.requisitoInput.value = ''
      this.registro_requisito = row
      this.$refs.requisitoInput.click()
    },
    uploadRequisitoFile () {
      try {
        // this.loadingButton = true
        let file = this.$refs.requisitoInput.files[0]
        let formData = new FormData()
        console.log(file.name)
        console.log(this.registro_requisito.id)
        // this.form_bases.fields.name2 = file.name
        formData.append('file', file)
        // hay que validar a que empresa de respaldo se le adjuntará el documento
        if (this.form_requisitos.bandera_ganadora) {
          if (this.registro_requisito.name0 === null || this.registro_requisito.name0 === 'null') {
            formData.append('formato_requisito_id', 0)
          } else {
            formData.append('formato_requisito_id', this.registro_requisito.documento)
            formData.append('nombre_antiguo', this.registro_requisito.name0)
          }
        }
        if (this.form_requisitos.bandera_documento1) {
          if (this.registro_requisito.name1 === null || this.registro_requisito.name1 === 'null') {
            formData.append('formato_requisito_id', 0)
          } else {
            formData.append('formato_requisito_id', this.registro_requisito.documento1)
            formData.append('nombre_antiguo', this.registro_requisito.name1)
          }
        }
        if (this.form_requisitos.bandera_documento2) {
          if (this.registro_requisito.name2 === null || this.registro_requisito.name2 === 'null') {
            formData.append('formato_requisito_id', 0)
          } else {
            formData.append('formato_requisito_id', this.registro_requisito.documento2)
            formData.append('nombre_antiguo', this.registro_requisito.name2)
          }
        }
        if (this.form_requisitos.bandera_documento3) {
          if (this.registro_requisito.name3 === null || this.registro_requisito.name3 === 'null') {
            formData.append('formato_requisito_id', 0)
          } else {
            formData.append('formato_requisito_id', this.registro_requisito.documento3)
            formData.append('nombre_antiguo', this.registro_requisito.name3)
          }
        }
        if (this.form_requisitos.bandera_documento4) {
          if (this.registro_requisito.name4 === null || this.registro_requisito.name4 === 'null') {
            formData.append('formato_requisito_id', 0)
          } else {
            formData.append('formato_requisito_id', this.registro_requisito.documento4)
            formData.append('nombre_antiguo', this.registro_requisito.name4)
          }
        }
        formData.append('bandera_ganadora', this.form_requisitos.bandera_ganadora)
        formData.append('bandera_documento1', this.form_requisitos.bandera_documento1)
        formData.append('bandera_documento2', this.form_requisitos.bandera_documento2)
        formData.append('bandera_documento3', this.form_requisitos.bandera_documento3)
        formData.append('bandera_documento4', this.form_requisitos.bandera_documento4)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('id', this.registro_requisito.id) // id del requisito
        formData.append('licitacion_id', this.registro_requisito.licitacion_id)
        if (file === null || file === undefined) {
          // this.loadingButton = false
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('licitaciones_requisitos/uploadArchivo', formData, {
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
                this.cargarRequisitos()
                // this.form_bases.fields.observaciones_archivo = ''
              } else {
                if (response.data.err !== '') {
                  console.log(file.type.split('/')[1].toLowerCase())
                  this.$showMessage('Errores en archivo', response.data.err)
                }
              }
              // this.loadingButton = false
              this.$q.loading.hide()
            }).catch(error => {
              // this.loadingButton = false
              console.error(error)
              this.$q.loading.hide()
            })
          } else {
            // this.loadingButton = false
            this.$showMessage('Error', 'El tipo de archivo no es el requerido para este requisito.')
            console.log(file.type.split('/')[1].toLowerCase())
          }
        } else {
          // this.loadingButton = false
          this.$showMessage('Error', 'No ha seleccionado un archivo con la extensión .jpg, .jpeg, .png, .pdf, .xml')
        }
      } catch (error) {
        console.log(error)
      }
    },
    descargarRequisito0 (row) {
      let uri = process.env.API + `licitaciones_requisitos/getFile/${row.documento}/${row.doc_type0}`
      window.open(uri, '_blank')
    },
    verFormatoRequisito0 (row) {
      var uri = process.env.API + '/public/assets/licitaciones/requisitos/' + row.documento + '.' + row.doc_type0
      window.open(uri, '_blank')
    },
    descargarRequisito1 (row) {
      let uri = process.env.API + `licitaciones_requisitos/getFile/${row.documento1}/${row.doc_type1}`
      window.open(uri, '_blank')
    },
    verFormatoRequisito1 (row) {
      var uri = process.env.API + '/public/assets/licitaciones/requisitos/' + row.documento1 + '.' + row.doc_type1
      window.open(uri, '_blank')
    },
    descargarRequisito2 (row) {
      let uri = process.env.API + `licitaciones_requisitos/getFile/${row.documento2}/${row.doc_type2}`
      window.open(uri, '_blank')
    },
    verFormatoRequisito2 (row) {
      var uri = process.env.API + '/public/assets/licitaciones/requisitos/' + row.documento2 + '.' + row.doc_type2
      window.open(uri, '_blank')
    },
    descargarRequisito3 (row) {
      let uri = process.env.API + `licitaciones_requisitos/getFile/${row.documento3}/${row.doc_type3}`
      window.open(uri, '_blank')
    },
    verFormatoRequisito3 (row) {
      var uri = process.env.API + '/public/assets/licitaciones/requisitos/' + row.documento3 + '.' + row.doc_type3
      window.open(uri, '_blank')
    },
    descargarRequisito4 (row) {
      let uri = process.env.API + `licitaciones_requisitos/getFile/${row.documento4}/${row.doc_type4}`
      window.open(uri, '_blank')
    }, // PROPUESTAS
    verFormatoRequisito4 (row) {
      var uri = process.env.API + '/public/assets/licitaciones/requisitos/' + row.documento4 + '.' + row.doc_type4
      window.open(uri, '_blank')
    },
    agregarPropuesta () {
      this.$v.form_propuestas.$touch()
      if (!this.$v.form_propuestas.$error) {
        let propuestas = this.form_propuestas.fields
        this.savePropuesta(propuestas).then(({data}) => {
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
            this.limpiar_propuesta()
            this.cargarPropuestas()
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.log(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    async cargarPropuestas () {
      this.form_propuestas.loading = true
      this.form_propuestas.data = []
      this.getPropuestasByLicitacion({licitacion_id: this.form_propuestas.fields.licitacion_id}).then(({data}) => {
        this.form_propuestas.loading = false
        if (data.propuestas.length > 0) {
          data.propuestas.forEach(function (documento) {
            if (documento.name1 !== null) {
              if (documento.doc_type1.toLowerCase() === 'docx') {
                documento.color1 = 'blue-9'
                documento.icon1 = 'fas fa-file-word'
              } else if (documento.doc_type1.toLowerCase() === 'pdf') {
                documento.color1 = 'red-10'
                documento.icon1 = 'fas fa-file-pdf'
              } else if (documento.doc_type1.toLowerCase() === 'xml') {
                documento.color1 = 'light-green'
                documento.icon1 = 'fas fa-file-code'
              } else if (documento.doc_type1.toLowerCase() === 'jpg' || documento.doc_type1.toLowerCase() === 'jpg' || documento.doc_type1.toLowerCase() === 'png') {
                documento.color1 = 'amber'
                documento.icon1 = 'fas fa-file-image'
              }
            }
            if (documento.name2 !== null) {
              if (documento.doc_type2.toLowerCase() === 'docx') {
                documento.color2 = 'blue-9'
                documento.icon2 = 'fas fa-file-word'
              } else if (documento.doc_type2.toLowerCase() === 'pdf') {
                documento.color2 = 'red-10'
                documento.icon2 = 'fas fa-file-pdf'
              } else if (documento.doc_type2.toLowerCase() === 'xml') {
                documento.color2 = 'light-green'
                documento.icon2 = 'fas fa-file-code'
              } else if (documento.doc_type2.toLowerCase() === 'jpg' || documento.doc_type2.toLowerCase() === 'jpg' || documento.doc_type2.toLowerCase() === 'png') {
                documento.color2 = 'amber'
                documento.icon2 = 'fas fa-file-image'
              }
            }
            if (documento.name3 !== null) {
              if (documento.doc_type3.toLowerCase() === 'docx') {
                documento.color3 = 'blue-9'
                documento.icon3 = 'fas fa-file-word'
              } else if (documento.doc_type3.toLowerCase() === 'pdf') {
                documento.color3 = 'red-10'
                documento.icon3 = 'fas fa-file-pdf'
              } else if (documento.doc_type3.toLowerCase() === 'xml') {
                documento.color3 = 'light-green'
                documento.icon3 = 'fas fa-file-code'
              } else if (documento.doc_type3.toLowerCase() === 'jpg' || documento.doc_type3.toLowerCase() === 'jpg' || documento.doc_type3.toLowerCase() === 'png') {
                documento.color3 = 'amber'
                documento.icon3 = 'fas fa-file-image'
              }
            }
            if (documento.name4 !== null) {
              if (documento.doc_type4.toLowerCase() === 'docx') {
                documento.color4 = 'blue-9'
                documento.icon4 = 'fas fa-file-word'
              } else if (documento.doc_type4.toLowerCase() === 'pdf') {
                documento.color4 = 'red-10'
                documento.icon4 = 'fas fa-file-pdf'
              } else if (documento.doc_type4.toLowerCase() === 'xml') {
                documento.color4 = 'light-green'
                documento.icon4 = 'fas fa-file-code'
              } else if (documento.doc_type4.toLowerCase() === 'jpg' || documento.doc_type4.toLowerCase() === 'jpg' || documento.doc_type4.toLowerCase() === 'png') {
                documento.color4 = 'amber'
                documento.icon4 = 'fas fa-file-image'
              }
            }
            if (documento.name0 !== null) {
              if (documento.doc_type0.toLowerCase() === 'docx') {
                documento.color0 = 'blue-9'
                documento.icon0 = 'fas fa-file-word'
              } else if (documento.doc_type0.toLowerCase() === 'pdf') {
                documento.color0 = 'red-10'
                documento.icon0 = 'fas fa-file-pdf'
              } else if (documento.doc_type0.toLowerCase() === 'xml') {
                documento.color0 = 'light-green'
                documento.icon0 = 'fas fa-file-code'
              } else if (documento.doc_type0.toLowerCase() === 'jpg' || documento.doc_type0.toLowerCase() === 'jpg' || documento.doc_type0.toLowerCase() === 'png') {
                documento.color0 = 'amber'
                documento.icon0 = 'fas fa-file-image'
              }
            }
          })
          this.form_propuestas.data = data.propuestas
        }
      }).catch(error => {
        this.form_propuestas.loading = false
        console.error(error)
      })
    },
    limpiar_propuesta () {
      this.form_propuestas.fields.propuesta = ''
      this.form_propuestas.fields.fecha = ''
      this.form_propuestas.fields.descripcion = ''
      this.form_propuestas.fields.responsable = 0
      this.form_propuestas.fields.responsable1 = 0
      this.form_propuestas.fields.responsable2 = 0
      this.form_propuestas.fields.responsable3 = 0
      this.form_propuestas.fields.responsable4 = 0
      this.$v.form_propuestas.$reset()
    },
    cambiarResponsablePropuesta (row) {
      this.updatePropuesta({id: row.id, propuesta: row.propuesta, descripcion: row.descripcion, fecha: row.fecha, responsable: row.responsable, responsable1: row.responsable1, responsable2: row.responsable2, responsable3: row.responsable3, responsable4: row.responsable4}).then(({data}) => {
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
          this.cargarPropuestas()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.log(error)
      })
    },
    editPropuesta (row) {
      this.form_propuestas.fields.id = row.id
      this.form_propuestas.fields.propuesta = row.propuesta
      this.form_propuestas.fields.fecha = row.fecha
      this.form_propuestas.fields.descripcion = row.descripcion
      this.form_propuestas.fields.responsable = row.responsable
      this.form_propuestas.fields.responsable1 = row.responsable1
      this.form_propuestas.fields.responsable2 = row.responsable2
      this.form_propuestas.fields.responsable3 = row.responsable3
      this.form_propuestas.fields.responsable4 = row.responsable4
      this.form_propuestas.editar = true
    },
    actualizarPropuesta () {
      this.$v.form_propuestas.$touch()
      if (!this.$v.form_propuestas.$error) {
        let propuesta = this.form_propuestas.fields
        this.updatePropuesta(propuesta).then(({data}) => {
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
            this.form_propuestas.editar = false
            this.limpiar_propuesta()
            this.cargarPropuestas()
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.log(error)
          this.form_propuestas.editar = false
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    eliminarPropuesta (row) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar esta propuesta?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.deletePropuesta({id: row.id}).then(({data}) => {
          if (data.result === 'success') {
            this.$q.notify({
              // only required parameter is the message:
              message: 'Se ha eliminado la propuesta',
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'delete',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.cargarPropuestas()
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
    },
    selectedFileDocumentoPropuesta (row) {
      this.form_propuestas.bandera_ganadora = true
      this.form_propuestas.bandera_documento1 = false
      this.form_propuestas.bandera_documento2 = false
      this.form_propuestas.bandera_documento3 = false
      this.form_propuestas.bandera_documento4 = false
      this.$refs.propuestaInput.value = ''
      this.registro_propuesta = row
      this.$refs.propuestaInput.click()
    },
    selectedFileDocumentoPropuesta1 (row) {
      this.form_propuestas.bandera_documento1 = true
      this.form_propuestas.bandera_ganadora = false
      this.form_propuestas.bandera_documento2 = false
      this.form_propuestas.bandera_documento3 = false
      this.form_propuestas.bandera_documento4 = false
      this.$refs.propuestaInput.value = ''
      this.registro_propuesta = row
      this.$refs.propuestaInput.click()
    },
    selectedFileDocumentoPropuesta2 (row) {
      this.form_propuestas.bandera_documento2 = true
      this.form_propuestas.bandera_ganadora = false
      this.form_propuestas.bandera_documento1 = false
      this.form_propuestas.bandera_documento3 = false
      this.form_propuestas.bandera_documento4 = false
      this.$refs.propuestaInput.value = ''
      this.registro_propuesta = row
      this.$refs.propuestaInput.click()
    },
    selectedFileDocumentoPropuesta3 (row) {
      this.form_propuestas.bandera_documento3 = true
      this.form_propuestas.bandera_ganadora = false
      this.form_propuestas.bandera_documento1 = false
      this.form_propuestas.bandera_documento2 = false
      this.form_propuestas.bandera_documento4 = false
      this.$refs.propuestaInput.value = ''
      this.registro_propuesta = row
      this.$refs.propuestaInput.click()
    },
    selectedFileDocumentoPropuesta4 (row) {
      this.form_propuestas.bandera_documento4 = true
      this.form_propuestas.bandera_ganadora = false
      this.form_propuestas.bandera_documento1 = false
      this.form_propuestas.bandera_documento2 = false
      this.form_propuestas.bandera_documento3 = false
      this.$refs.propuestaInput.value = ''
      this.registro_propuesta = row
      this.$refs.propuestaInput.click()
    },
    uploadPropuestaFile () {
      try {
        // this.loadingButton = true
        let file = this.$refs.propuestaInput.files[0]
        let formData = new FormData()
        console.log(file.name)
        console.log(this.registro_propuesta.id)
        // this.form_bases.fields.name2 = file.name
        formData.append('file', file)
        // hay que validar a que empresa de respaldo se le adjuntará el documento
        if (this.form_propuestas.bandera_ganadora) {
          if (this.registro_propuesta.name0 === null || this.registro_propuesta.name0 === 'null') {
            formData.append('formato_requisito_id', 0)
          } else {
            formData.append('formato_requisito_id', this.registro_propuesta.documento)
            formData.append('nombre_antiguo', this.registro_propuesta.name0)
          }
        }
        if (this.form_propuestas.bandera_documento1) {
          if (this.registro_propuesta.name1 === null || this.registro_propuesta.name1 === 'null') {
            formData.append('formato_requisito_id', 0)
          } else {
            formData.append('formato_requisito_id', this.registro_propuesta.documento1)
            formData.append('nombre_antiguo', this.registro_propuesta.name1)
          }
        }
        if (this.form_propuestas.bandera_documento2) {
          if (this.registro_propuesta.name2 === null || this.registro_propuesta.name2 === 'null') {
            formData.append('formato_requisito_id', 0)
          } else {
            formData.append('formato_requisito_id', this.registro_propuesta.documento2)
            formData.append('nombre_antiguo', this.registro_propuesta.name2)
          }
        }
        if (this.form_propuestas.bandera_documento3) {
          if (this.registro_propuesta.name3 === null || this.registro_propuesta.name3 === 'null') {
            formData.append('formato_requisito_id', 0)
          } else {
            formData.append('formato_requisito_id', this.registro_propuesta.documento3)
            formData.append('nombre_antiguo', this.registro_propuesta.name3)
          }
        }
        if (this.form_propuestas.bandera_documento4) {
          if (this.registro_propuesta.name4 === null || this.registro_propuesta.name4 === 'null') {
            formData.append('formato_requisito_id', 0)
          } else {
            formData.append('formato_requisito_id', this.registro_propuesta.documento4)
            formData.append('nombre_antiguo', this.registro_propuesta.name4)
          }
        }
        formData.append('bandera_ganadora', this.form_propuestas.bandera_ganadora)
        formData.append('bandera_documento1', this.form_propuestas.bandera_documento1)
        formData.append('bandera_documento2', this.form_propuestas.bandera_documento2)
        formData.append('bandera_documento3', this.form_propuestas.bandera_documento3)
        formData.append('bandera_documento4', this.form_propuestas.bandera_documento4)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('id', this.registro_propuesta.id) // id del requisito
        formData.append('licitacion_id', this.registro_propuesta.licitacion_id)
        if (file === null || file === undefined) {
          // this.loadingButton = false
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('licitaciones_propuestas/uploadArchivo', formData, {
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
                this.cargarPropuestas()
                // this.form_bases.fields.observaciones_archivo = ''
              } else {
                if (response.data.err !== '') {
                  console.log(file.type.split('/')[1].toLowerCase())
                  this.$showMessage('Errores en archivo', response.data.err)
                }
              }
              // this.loadingButton = false
              this.$q.loading.hide()
            }).catch(error => {
              // this.loadingButton = false
              console.error(error)
              this.$q.loading.hide()
            })
          } else {
            // this.loadingButton = false
            this.$showMessage('Error', 'El tipo de archivo no es el requerido para este requisito.')
            console.log(file.type.split('/')[1].toLowerCase())
          }
        } else {
          // this.loadingButton = false
          this.$showMessage('Error', 'No ha seleccionado un archivo con la extensión .jpg, .jpeg, .png, .pdf, .xml')
        }
      } catch (error) {
        console.log(error)
      }
    },
    descargarPropuesta0 (row) {
      console.log(row.documento_id + '.' + row.doc_type0)
      let uri = process.env.API + `licitaciones_propuestas/getFile/${row.documento}/${row.doc_type0}`
      window.open(uri, '_blank')
    },
    descargarPropuesta1 (row) {
      let uri = process.env.API + `licitaciones_propuestas/getFile/${row.documento1}/${row.doc_type1}`
      window.open(uri, '_blank')
    },
    descargarPropuesta2 (row) {
      let uri = process.env.API + `licitaciones_propuestas/getFile/${row.documento2}/${row.doc_type2}`
      window.open(uri, '_blank')
    },
    descargarPropuesta3 (row) {
      let uri = process.env.API + `licitaciones_propuestas/getFile/${row.documento3}/${row.doc_type3}`
      window.open(uri, '_blank')
    },
    descargarPropuesta4 (row) {
      let uri = process.env.API + `licitaciones_propuestas/getFile/${row.documento4}/${row.doc_type4}`
      window.open(uri, '_blank')
    },
    verFormatoPropuesta0 (row) {
      var uri = process.env.API + '/public/assets/licitaciones/propuestas/' + row.documento + '.' + row.doc_type0
      window.open(uri, '_blank')
    },
    verFormatoPropuesta1 (row) {
      var uri = process.env.API + '/public/assets/licitaciones/propuestas/' + row.documento1 + '.' + row.doc_type1
      window.open(uri, '_blank')
    },
    verFormatoPropuesta2 (row) {
      var uri = process.env.API + '/public/assets/licitaciones/propuestas/' + row.documento2 + '.' + row.doc_type2
      window.open(uri, '_blank')
    },
    verFormatoPropuesta3 (row) {
      var uri = process.env.API + '/public/assets/licitaciones/propuestas/' + row.documento3 + '.' + row.doc_type3
      window.open(uri, '_blank')
    },
    verFormatoPropuesta4 (row) {
      var uri = process.env.API + '/public/assets/licitaciones/propuestas/' + row.documento4 + '.' + row.doc_type4
      window.open(uri, '_blank')
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
    cambiaFechaInicio () {
      console.log(this.form.fields.fecha_inicio)
    },
    cambiaFechaIFallo () {
      console.log(this.form.fields.fecha_inicio)
      console.log(this.form.fields.fecha_fallo)
    },
    cambiaFecha () {
      console.log(this.form_eventos.fields.fecha_evento)
    },
    borrar () {
      this.form_filtros.fields.fecha_inicio = 'i'
      this.form_filtros.fields.fecha_fallo = 'i'
      this.form_filtros.fields.status = 'Todas'
    },
    verFormato (row) {
      /* this.vista_imagen = true
      if (row.doc_type === 'pdf') {
        this.url_pdf = process.env.API + '/public/assets/licitaciones/bases/' + row.documento_id + '.' + row.doc_type
      } else {
        this.url_img = process.env.API + '/public/assets/licitaciones/bases/' + row.documento_id + '.' + row.doc_type + '?' + Math.random()
      } */
      var uri = process.env.API + '/public/assets/licitaciones/bases/' + row.documento_id + '.' + row.doc_type
      window.open(uri, '_blank')
    },
    verFormatoEvento (row) {
      var uri = process.env.API + '/public/assets/licitaciones/eventos/' + row.documento_id + '.' + row.doc_type
      window.open(uri, '_blank')
    },
    desaparecer () {
      this.url_img = ''
      this.url_pdf = ''
      this.vista_imagen = false
    },
    selectedRowEntregable (row) {
      this.$refs.entregableInput.value = ''
      this.registro_entregable = row
      this.$refs.entregableInput.click()
    },
    uploadEntregableFile () {
      try {
        let file = this.$refs.entregableInput.files[0]
        let formData = new FormData()
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('contrato_id', this.registro_entregable.id)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('licitaciones/uploadArchivoFinal', formData, {
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
                this.loadAll()
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
    verDocumentoEntregable (id, ext) {
      let uri = process.env.API + '/public/assets/licitaciones_documentos_finales/' + id + '.' + ext
      window.open(uri, '_blank')
    },
    descargarDocumentoEntregable (id, ext) {
      let uri = process.env.API + `licitaciones/getFileFinal/${id}/${ext}`
      window.open(uri, '_blank')
    },
    eliminarDocumentoFinal (id) {
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
      this.eliminarDocumentoEntregable({id: id}).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'delete', position: 'top-right'
          })
          this.loadAll()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    perfiles () {
      console.log('hola')
    },
    async cargar () {
      await this.getAreas()
      await this.getEntidades()
      await this.cargarAptitudes()
    },
    async cargaMunicipios () {
      this.form_busqueda.municipiosOptions = []
      this.form_busqueda.fields.municipio_id = 0
      await this.getMunicipiosByEstado({estado_id: this.form_busqueda.fields.estado_id}).then(({data}) => {
        this.form_busqueda.municipiosOptions.push({label: '---Todos---', value: 0})
        if (data.municipios.length > 0) {
          data.municipios.forEach(municipio => {
            this.form_busqueda.municipiosOptions.push({label: municipio.nombre, value: municipio.id})
          })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async cargarAptitudes () {
      this.form_busqueda.aptitudes_data = []
      await this.getAptitudes().then(({data}) => {
        this.form_busqueda.aptitudes_data = data.aptitudes
      }).catch(error => {
        console.error(error)
      })
    },
    parseAptitudes () {
      return this.form_busqueda.aptitudes_data.map(aptitud => {
        return {
          label: aptitud.nombre,
          value: aptitud.nombre
        }
      })
    },
    search (terms, done) {
      setTimeout(() => {
        done(filter(terms, {field: 'value', list: this.parseAptitudes()}))
      }, 500)
    },
    selected (item) {
      // this.$q.notify(`Se ha agregado "${item.label}"`)
      console.log(this.form_busqueda.fields.aptitudes)
      // console.log(this.form_aptitudes.aptitud)
    },
    duplicate (label) {
      this.$q.notify(`"${label}" ya se encuentra en el filtro de aptitudes`)
    },
    async filtrarP () {
      this.curso_var = this.form_busqueda.fields.curso
      if (this.curso_var.trim() === '') {
        this.curso_var = 'all'
      }
      if (this.form_busqueda.fields.aptitudes.length !== 0) {
        this.form_busqueda.data = []
        await this.filtrarPerfiles({estado: this.form_busqueda.fields.estado_id, municipio: this.form_busqueda.fields.municipio_id, area: this.form_busqueda.fields.area_id, aptitudes: this.form_busqueda.fields.aptitudes, curso: this.curso_var}).then(({data}) => {
          this.form_busqueda.data = data.perfiles
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.form_busqueda.data = []
        await this.filtrarPerfiles({estado: this.form_busqueda.fields.estado_id, municipio: this.form_busqueda.fields.municipio_id, area: this.form_busqueda.fields.area_id, aptitudes: this.form_busqueda.fields.aptitudes_param, curso: this.curso_var}).then(({data}) => {
          this.form_busqueda.data = data.perfiles
        }).catch(error => {
          console.error(error)
        })
      }
    },
    async cargarPerfiles () {
      console.log(this.form.fields.id)
      await this.getPerfilesByLicitacion({licitacion_id: this.form.fields.id}).then(({data}) => {
        if (data.result === 'success') {
          this.form_perfiles_involucrados.data = data.licitaciones_perfiles
        }
      }).catch(error => {
        console.error(error)
      })
    },
    addPerfil (row) {
      this.agregar_perfil_licitacion = true
      this.registro_perfil = row
    },
    agregarPerfil () {
      this.savePerfilLicitacion({licitacion_id: this.form.fields.id, perfil_id: this.registro_perfil.id, participacion: this.form_busqueda.fields.participacion}).then(({data}) => {
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
          this.form_busqueda.fields.participacion = ''
          this.agregar_perfil_licitacion = false
          this.cargarPerfiles()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    deletePerfil (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este perfil de la lictación?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete_perfil(id)
      }).catch(() => {})
    },
    delete_perfil (id) {
      let params = {id: id}
      this.deletePerfilLicitacion(params).then(({data}) => {
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
          this.cargarPerfiles()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    borrar_filtros () {
      this.form_busqueda.fields.estado_id = 0
      this.form_busqueda.fields.municipio_id = 0
      this.form_busqueda.fields.area_id = 0
      this.form_busqueda.fields.aptitudes = []
      this.filtrarP()
    },
    r1 () {
      console.log('aaaaaaaaaaaa')
      if (this.form.fields.respaldo1 !== 0) {
        this.form.fields.empresa_respaldo_1 = ''
      }
    },
    r2 () {
      if (this.form.fields.respaldo2 !== 0) {
        this.form.fields.empresa_respaldo_2 = ''
      }
    },
    r3 () {
      if (this.form.fields.respaldo3 !== 0) {
        this.form.fields.empresa_respaldo_3 = ''
      }
    },
    r4 () {
      if (this.form.fields.respaldo4 !== 0) {
        this.form.fields.empresa_respaldo_4 = ''
      }
    },
    concursante () {
      if (this.form.fields.empresa_id !== 0) {
        this.form.fields.empresa = ''
      }
    },
    create_from () {
      let params = this.form.fields
      params.licitacion_id = this.form.fields.id
      params.year = this.year
      this.createFrom(params).then(({data}) => {
        this.loadingButton = false
        if (data.result === 'success') {
          this.form.fields.recurso_id = parseInt(data.id)
          this.getRecursos()
          this.$q.notify({
            // only required parameter is the message:
            message: data.message.content,
            timeout: 3000,
            type: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'done',
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
        // recurso_id: {required, minValue: minValue(1)},
        empresa_id: {required, minValue: minValue(0)},
        entidad_id: {required, minValue: minValue(1)},
        fecha_inicio: {required},
        status: {required},
        titulo: {required, maxLength: maxLength(100)},
        descripcion: {required, maxLength: maxLength(2000)},
        folio: {required, maxLength: maxLength(20), alpha},
        comprador: {maxLength: maxLength(200)}
      }
    },
    form_respaldo: {
      fields: {
        empresa_id: {required, minValue: minValue(0)},
        licitacion_id: {required, minValue: minValue(1)}
      }
    },
    form_bases: {
      fields: {
        nombre_documento: {required, maxLength: maxLength(50)}
      }
    },
    form_eventos: {
      fields: {
        evento: {required},
        fecha_evento: {required}
      }
    },
    form_requisitos: {
      fields: {
        requisito: {required, maxLength: maxLength(100)},
        descripcion: {required, maxLength: maxLength(100)},
        fecha: {required}
      }
    },
    form_propuestas: {
      fields: {
        propuesta: {required, maxLength: maxLength(100)},
        descripcion: {required, maxLength: maxLength(100)},
        fecha: {required}
      }
    }
  }
}
</script>

<style scoped>
  .tabs {
    color: rgba(8, 85, 134, 1);
    border-color: rgba(8, 85, 134, 1);
  }
  /* .tabcontent {
    height: 600px;
  } */
  .shadow-1 {
    border-color: rgba(8, 85, 134, 1);
  }

/*.tab {
  float: left;
  //border: 1px solid #ccc;
  //background-color: #f1f1f1;
  width: 15%;
  height: 600px;
  background-color: rgba(8, 85, 134, 1);
  color: rgba(255, 255, 255, 1);
}

.tab2 {
  background-color: rgba(8, 85, 134, 1);
  color: rgba(255, 255, 255, 1);
}

.tabcontent {
  float: left;
  padding: 0px 12px;
  //border: 1px solid #ccc;
  width: 85%;
  border-left: none;
  height: 600px;
}*/

#myImg1 {
  border-radius: 5px;
  cursor: pointer;
  transition: 0.3s;
}

#myImg1:hover {opacity: 0.7;}

/* The Modal (background) */
.modal1 {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
}

/* Modal Content (image) */
.modal-content1 {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
}

@-webkit-keyframes zoom {
  from {-webkit-transform:scale(0)}
  to {-webkit-transform:scale(1)}
}

@keyframes zoom {
  from {transform:scale(0)}
  to {transform:scale(1)}
}

/* The Close Button */
.close1 {
  position: absolute;
  top: 15px;
  right: 15px;
  color: #f1f1f1;
  font-size: 40px;
  font-weight: bold;
  transition: 0.3s;
  z-index: 3;
  margin-top: 30px;
}

.close1:hover,
.close1:focus {
  color: #bbb;
  text-decoration: none;
  cursor: pointer;
}

/* 100% Image Width on Smaller Screens */
@media only screen and (max-width: 700px){
  .modal-content1 {
    width: 100%;
  }
}
</style>
