<template>
  <q-page>
    <div class="layout-padding">
      <div class="row" v-if="viewsPresupuestos.grid">
        <div class="col-sm-12">
          <div class="row justify-between">
            <div class="col-sm-6">
              <q-btn size="15px" icon="work" disable class="btn_categoria" label="PRESUPUESTOS" />
            </div>
            <div class="col-sm-6">
              <div class="row justify-end">
                <div class="col-sm-4" style="text-align: right">
                  <q-btn @click="newRowPresupuestos()" class="btn_nuevo" icon="add">
                    Nuevo
                  </q-btn>
                </div>
              </div>
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
                      <q-btn small flat @click="editRowPresupuestos(props.row)" color="blue-6" icon="edit">
                        <q-tooltip>Editar</q-tooltip>
                      </q-btn>
                      <q-btn small flat @click="deleteSingleRowPresupuestos(props.row.id)" color="negative" icon="delete">
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

    <!--Crear un proyecto-->

      <div class="row" v-if="viewsPresupuestos.create">
        <div class="col-sm-12">
          <div class="row justify-between">
            <div class="col-sm-9">
              <h5 style="margin: 7px 0; font-weight: bold">NUEVO PRESUPUESTO</h5>
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
            <div class="col-sm-4">
              <q-field icon="fas fa-microchip" icon-color="dark" :error="$v.form.fieldsPresupuesto.recurso_id.$error" error-label="Elija un proyecto">
                <q-select v-model="form.fieldsPresupuesto.recurso_id" inverted color="dark" float-label="Proyecto" :options="recursosOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-8">
              <q-field icon="fas fa-pen-square" icon-color="dark" :error="$v.form.fieldsPresupuesto.nombre_proyecto.$error" error-label="Ingrese el nombre del presupuesto">
                <q-input upper-case v-model="form.fieldsPresupuesto.nombre_proyecto" inverted color="dark" float-label="Nombre del presupuesto"/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div style="height:53px;width:200px;margin-top:5px;">
              <q-field icon="date_range" icon-color="dark" :error="$v.form.fieldsPresupuesto.inicio.$error" error-label="Ingrese la fecha de inicio">
                <q-datetime v-model="form.fieldsPresupuesto.inicio" type="date" inverted color="dark" float-label="Fecha inicio" align="center"></q-datetime>
              </q-field>
            </div>
            <div style="height:53px;width:200px;margin-top:5px;">
              <q-field icon="date_range" icon-color="dark" :error="$v.form.fieldsPresupuesto.fin.$error" error-label="Ingrese la fecha de término">
                <q-datetime v-model="form.fieldsPresupuesto.fin" type="date" inverted color="dark" float-label="Fecha término" align="center"></q-datetime>
              </q-field>
            </div>
            <div style="height:53px;width:100px;margin-top:5px;">
              <q-field icon="fas fa-sun" icon-color="dark">
                <q-input v-model="form.fieldsPresupuesto.dias" type="number" inverted color="dark" float-label="Días" readonly ></q-input>
              </q-field>
            </div>
            <div style="height:53px;width:350px;margin-top:5px;">
              <div class="icono_field">
                <q-field icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form.fieldsPresupuesto.presupuesto.$error" error-label="Rango del presupuesto ($0.00 - $1,000,000,000.00)" float-label="Presupuesto">
                  <q-input float-label="Días" v-model.lazy="form.fieldsPresupuesto.presupuesto" v-bind="money"></q-input>
                </q-field>
              </div>
            </div>
          <div style="height:53px;width:350px;margin-top:5px;">
            <q-field icon="person" icon-color="dark" :error="$v.form.fieldsPresupuesto.lider_proyecto.$error" error-label="Por favor elija un usuario">
              <q-select v-model="form.fieldsPresupuesto.lider_proyecto" inverted color="dark" float-label="Usuario" :options="usuariosOptions" filter/>
            </q-field>
          </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-2 offset-sm-10 pull-right">
              <q-btn @click="savePresupuestos()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal editar PROYECTO -->
      <div class="row" v-if="viewsPresupuestos.update">
        <div class="col-sm-12">
          <div class="row justify-between">
            <div class="col-sm-9">
              <h5 style="margin: 7px 0; font-weight: bold">MODIFICAR PRESUPUESTO</h5>
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
            <div class="col-sm-4">
              <q-field icon="fas fa-microchip" icon-color="dark" :error="$v.form.fieldsPresupuesto.recurso_id.$error" error-label="Elija un proyecto">
                <q-select v-model="form.fieldsPresupuesto.recurso_id" inverted color="dark" float-label="Proyecto" :options="recursosOptions" filter/>
              </q-field>
            </div>
            <div class="col-sm-8">
              <q-field icon="fas fa-pen-square" icon-color="dark" :error="$v.form.fieldsPresupuesto.nombre_proyecto.$error" error-label="Ingrese el nombre del presupuesto">
                <q-input upper-case v-model="form.fieldsPresupuesto.nombre_proyecto" inverted color="dark" float-label="Nombre del presupuesto"/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div style="height:53px;width:210px;margin-top:5px;">
              <q-field icon="date_range" icon-color="dark" :error="$v.form.fieldsPresupuesto.inicio.$error" error-label="Ingrese la fecha de inicio">
                <q-datetime v-model="form.fieldsPresupuesto.inicio" type="date" inverted color="dark" float-label="Fecha inicio" align="center"></q-datetime>
              </q-field>
            </div>
            <div style="height:53px;width:210px;margin-top:5px;">
              <q-field icon="date_range" icon-color="dark" :error="$v.form.fieldsPresupuesto.fin.$error" error-label="Ingrese la fecha de término">
                <q-datetime v-model="form.fieldsPresupuesto.fin" type="date" inverted color="dark" float-label="Fecha término" align="center"></q-datetime>
              </q-field>
            </div>
            <div style="height:53px;width:100px;margin-top:5px;">
              <q-field icon="fas fa-sun" icon-color="dark">
                <q-input v-model="form.fieldsPresupuesto.dias" type="number" inverted color="dark" float-label="Días" readonly ></q-input>
              </q-field>
            </div>
            <div style="height:53px;width:350px;margin-top:5px;">
              <div class="icono_field">
                <q-field icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form.fieldsPresupuesto.presupuesto.$error" error-label="Rango del presupuesto ($0.00 - $1,000,000,000.00)" helper="Presupuesto inicial">
                  <money v-model="form.fieldsPresupuesto.presupuesto" readonly disable v-bind="money" style="height:55px;width:100%;" v-bind:class="{ moneyBien: !($v.form.fieldsPresupuesto.presupuesto.$error), moneyError: $v.form.fieldsPresupuesto.presupuesto.$error }" @input="$v.form.fieldsPresupuesto.presupuesto.$touch"></money>
                </q-field>
              </div>
            </div>
            <div style="height:53px;width:350px;margin-top:5px;">
              <div class="icono_field">
                <q-field  icon="fas fa-dollar-sign" con-color="dark"  helper="Presupuesto Actual">
                  <money readonly disable v-model="form.fieldsPresupuesto.presupuestoActual" v-bind="money" style="height:55px;width:100%;margin-top:5px;" v-bind:class="{ moneyBien: true, moneyError: false }"></money>
                </q-field>
              </div>
            </div>
            <div style="height:53px;width:420px;margin-top:30px;">
              <q-field icon="person" icon-color="dark">
                <q-input v-model="form.fieldsPresupuesto.lider_usuario" inverted color="dark" float-label="Lider de presupuesto" readonly ></q-input>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg">
            <div class="col-sm-2 offset-sm-10 pull-right">
              <q-btn @click="updatePresupuestos()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
            </div>
          </div>

          <div>
          <div class="row q-mt-lg">
            <div class="col-sm-12">
              <q-collapsible v-model="open_colaboradores" style="border-bottom: 3px solid green;" icon="perm_identity" label="Colaboradores">
                <!-- aqui va el contenido -->
                <div class="col-sm-3">
                  <q-btn @click="newColaborador()" class="btn_nuevo" icon="fas fa-plus" :loading="loadingButton" label="Nuevo colaborador"></q-btn>
                </div>
                <div class="row q-mt-lg" style="margin-top:30px;">
                  <div class="col-sm-12" style="padding-right:10px;">
                    <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                      <q-table id="sticky-table"
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
                              <q-btn small flat @click="deleteColaboradores(props.row.id)" color="negative" icon="delete">
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
              </q-collapsible>
            </div>
          </div>

          <div class="row q-mt-lg">
            <div class="col-sm-12">
              <q-collapsible v-model="open_solicitantes" style="border-bottom: 3px solid green;" icon="perm_identity" label="Solicitantes">
                <!-- aqui va el contenido -->
                <div class="col-sm-3">
                  <q-btn @click="newRespaldo()" class="btn_nuevo" icon="fas fa-plus" :loading="loadingButton" label="Nuevo solicitante"></q-btn>
                </div>
                <div class="row q-mt-lg" style="margin-top:30px;">
                  <div class="col-sm-12" style="padding-right:10px;">
                    <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                      <q-table id="sticky-table"
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
                              <q-btn small flat @click="deleteSolicitantes(props.row.id)" color="negative" icon="delete">
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
              </q-collapsible>
            </div>
          </div>

          <div class="row q-mt-lg">
            <div class="col-sm-12">
              <q-collapsible v-model="open_autorizadores" style="border-bottom: 3px solid green;" icon="perm_identity" label="Autorizadores">
                <!-- aqui va el contenido -->
                <div class="col-sm-3">
                  <q-btn @click="newAutorizador()" class="btn_nuevo" icon="fas fa-plus" :loading="loadingButton" label="Nuevo autorizador"></q-btn>
                </div>
                <div class="row q-mt-lg" style="margin-top:30px;">
                  <div class="col-sm-12">
                    <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                      <q-table id="sticky-table"
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
                              <q-btn small flat @click="deleteAutorizadores(props.row.id)" color="negative" icon="delete">
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
              </q-collapsible>
            </div>
          </div>

          <div class="row q-mt-lg">
            <div class="col-sm-12">
              <q-collapsible v-model="open_responsables" style="border-bottom: 3px solid green;" icon="perm_identity" label="Responsable de pagos">
                <!-- aqui va el contenido -->
                <div class="col-sm-3">
                  <q-btn @click="newResponsable()" class="btn_nuevo" icon="fas fa-plus" :loading="loadingButton" label="Nuevo responsable de pagos"></q-btn>
                </div>
                <div class="row q-mt-lg" style="margin-top:30px;">
                  <div class="col-sm-12" style="padding-left:10px;">
                    <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                      <q-table id="sticky-table"
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
                              <q-btn small flat @click="deleteResponsables(props.row.id)" color="negative" icon="delete">
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
              </q-collapsible>
            </div>
          </div>
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
            <q-btn @click="saveSolicitantes()" class="btn_guardar" icon-right="fas fa-save" :loading="loadingButton">Agregar</q-btn>
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
              <q-select v-model="form_colaboradores.fieldsPresupuesto.usuario_id" inverted color="dark" float-label="Usuario" :options="form_colaboradores.colaboradoresOptions" filter/>
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

      <!-- AUTORIZADORES -->
        <q-modal no-backdrop-dismiss v-if="form_autorizadores.autorizadores_proyecto" style="background-color: rgba(0,0,0,0.7);" v-model="form_autorizadores.autorizadores_proyecto" :content-css="{width: '45vw', height: '230px'}">
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
              <q-select v-model="form_autorizadores.fieldsPresupuesto.usuario_id" inverted color="dark" float-label="Usuario" :options="form_autorizadores.autorizadoresOptions" filter/>
            </q-field>
          </div>
          <div class="col-sm-4">
              <q-field icon="folder_shared" icon-color="dark" :error="$v.form_autorizadores.fieldsPresupuesto.perfil.$error" error-label="Escriba el perfil">
                <q-input upper-case v-model="form_autorizadores.fieldsPresupuesto.perfil" type="text" inverted color="dark" float-label="Perfil" maxlength="100" />
              </q-field>
          </div>
          <div class="col-sm-2">
              <q-field icon="list" icon-color="dark" :error="$v.form_autorizadores.fieldsPresupuesto.orden.$error" error-label="Escriba el nivel del autorizador">
                <q-input upper-case v-model="form_autorizadores.fieldsPresupuesto.orden" type="text" inverted color="dark" float-label="Nivel"/>
              </q-field>
          </div>
        </div>
        <div class="row q-mt-lg" style="margin-right:20px;">
          <div class="col-sm-3" style="margin-left:15px;">
              <q-checkbox v-model="alterar_presupuesto" label="Alterar presupuesto" color="amber"/>
          </div>
          <div class="col-sm-4 offset-sm-8 pull-right">
            <q-btn @click="saveAutorizadores()" class="btn_guardar" icon-right="fas fa-save" :loading="loadingButton">Agregar</q-btn>
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
              <q-select v-model="form_responsables.fieldsPresupuesto.usuario_id" inverted color="dark" float-label="Usuario" :options="form_responsables.responsablesOptions" filter/>
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

          <div class="row q-mt-lg subtitulos">
            DETALLE DE ACTIVIDADES
          </div>
          <div class="row q-mt-sm">
            <div class="col sm-4">
              <q-field icon="file" icon-color="dark">
                <input style="display:none" inverted color="dark" stack-label="Archivo csv" type="file" name="" value="" ref="fileInput" accept=".csv" @change="uploadCsvFile()">
                <q-btn @click="$refs.fileInput.click()" color="tertiary" :loading="loadingButton">
                  <i class="fa fa-upload" aria-hidden="true"></i>&nbsp;Subir CSV
                </q-btn>
              </q-field>
            </div>
          </div>

          <div class="col-sm-12 q-mt-sm" id="sticky-arbol">
            <div class="q-table-container q-table-dense" style="margin-top:20px;">
              <div class="q-table-middle scroll">
                <table class="q-table">
                  <thead>
                    <tr style="text-align: center;">
                      <th>Acciones</th>
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
                      <td data-th="" style="text-align: center;">
                        <q-btn small flat @click="editActividad(item,index)" color="blue-6" icon="fas fa-edit" size="sm">
                          <q-tooltip>Editar</q-tooltip>
                        </q-btn>
                      </td>
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

      <q-modal no-backdrop-dismiss v-if="form_actividades.modal_editar" style="background-color: rgba(0,0,0,0.7);" v-model="form_actividades.modal_editar" :content-css="{minWidth: '90vw', minHeight: '680px'}">
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
            <div class="col-sm-4" style="margin-top:5px;">
              <q-field icon="fas fa-arrow-circle-down" icon-color="dark">
                <q-input upper-case v-model="form_actividades.fieldsPresupuesto.indice" type="text" inverted color="dark" float-label="EDT" readonly disable></q-input>
              </q-field>
            </div>
              <div class="col-sm-4" style="margin-top:5px;">
              <q-field icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form_actividades.fieldsPresupuesto.costo.$error" error-label="Rango del costo ($0.00 - $1,000,000,000.00)" helper="Monto actual ">
                <money readonly v-model="form_actividades.fieldsPresupuesto.presupuesto_inicial" v-bind="money" style="height:53px;width:100%;" v-bind:class="{ moneyBien: !($v.form_actividades.fieldsPresupuesto.costo.$error), moneyError: $v.form_actividades.fieldsPresupuesto.costo.$error }"></money>
              </q-field>
            </div>
            <div class="col-sm-4" style="margin-top:5px;">
              <q-field icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form_actividades.fieldsPresupuesto.costo.$error" error-label="Rango del costo ($0.00 - $1,000,000,000.00)" helper="Monto Real">
                <money readonly v-model="form_actividades.fieldsPresupuesto.costo" v-bind="money" style="height:53px;width:100%;" v-bind:class="{ moneyBien: !($v.form_actividades.fieldsPresupuesto.costo.$error), moneyError: $v.form_actividades.fieldsPresupuesto.costo.$error }" @input="$v.form_actividades.fieldsPresupuesto.costo.$touch"></money>
              </q-field>
            </div>
            <div class="col-sm-4" style="margin-top:5px;">
              <q-field icon="fas fa-file-alt" icon-color="dark" helper="Resumen">
                <q-toggle v-model="form_actividades.fieldsPresupuesto.resumen" color="green" checked-icon="fas fa-check" unchecked-icon="fas fa-times"/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right:10px;">
            <div class="col-sm-4" style="margin-top:5px;">
              <q-field icon="date_range" icon-color="dark">
                <q-datetime v-model="form_actividades.fieldsPresupuesto.inicio" type="date" inverted color="dark" float-label="Inicio" clearable align="center"></q-datetime>
              </q-field>
            </div>
            <div class="col-sm-4" style="margin-top:5px;">
              <q-field icon="date_range" icon-color="dark">
                <q-datetime v-model="form_actividades.fieldsPresupuesto.fin" type="date" inverted color="dark" float-label="Fin" clearable align="center"></q-datetime>
              </q-field>
            </div>
            <div class="col-sm-4" style="margin-top:5px;">
              <q-field icon="date_range" icon-color="dark">
                <q-datetime v-model="form_actividades.fieldsPresupuesto.fin_real" type="date" inverted color="dark" float-label="Fin real" clearable align="center"></q-datetime>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right:10px;">
            <div class="col-sm-4" style="margin-top:5px;">
              <q-field icon="fas fa-sun" icon-color="dark">
                <q-input v-model="form_actividades.fieldsPresupuesto.duracion" type="number" inverted color="dark" float-label="Duración" readonly numeric-keyboard-toggle></q-input>
              </q-field>
            </div>
            <div class="col-sm-4" style="margin-top:5px;">
              <q-field icon="fas fa-sun" icon-color="dark">
                <q-input readonly v-model="form_actividades.fieldsPresupuesto.duracion_restante" type="number" inverted color="dark" float-label="Duración restante" numeric-keyboard-toggle></q-input>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right:10px;margin-bottom:10px;">
            <div class="col-sm-3 offset-sm-9 pull-right">
              <q-btn @click="updateActividadDetalle()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
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

    </div>
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { required, minValue, maxValue, maxLength } from 'vuelidate/lib/validators'
import moment from 'moment'
import axios from 'axios'
import {Money} from 'v-money'

export default {
  components: {Money},
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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS/COMISIONES'.toUpperCase()) {
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

      loadingButton: false,
      informacionPresupuestos: false,
      money: {
        decimal: '.',
        thousands: ',',
        precision: 2,
        masked: false
      },

      viewsPresupuestos: {
        grid: true,
        create: false,
        update: false
      },
      form: {
        fieldsPresupuesto: {
          id: 0,
          nombre_proyecto: '',
          recurso_id: 0,
          inicio: '',
          fin: '',
          dias: 0,
          presupuesto: 0,
          lider_proyecto: '',
          lider_usuario: ''
        },
        // data: [],
        columnsPresupuesto: [
          {name: 'nombre_proyecto', label: 'Nombre', field: 'nombre_proyecto', sortable: true, type: 'string', align: 'left'},
          { name: 'recurso', label: 'Proyecto', field: 'recurso', sortable: true, type: 'string', align: 'left' },
          { name: 'inicio', label: 'Fecha de inicio', field: 'inicio', sortable: true, type: 'string', align: 'left' },
          { name: 'fin', label: 'Fecha de término', field: 'fin', sortable: true, type: 'string', align: 'left' },
          { name: 'lider', label: 'Lider', field: 'fin', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'center' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_actividades: {
        loading: false,
        modal_editar: false,
        fieldsPresupuesto: {
          id: 0,
          proyecto_id: 0,
          nombre: '',
          avance: 0,
          inicio: '',
          fin: '',
          costo: 0,
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
        columns: [
          {name: 'nombre', label: 'Usuario', field: 'nombre', sortable: true, type: 'string', align: 'left'},
          {name: 'perfil', label: 'Perfil', field: 'perfil', sortable: true, type: 'string', align: 'left'},
          { name: 'acciones', label: 'Acciones', field: 'acciones', type: 'string', align: 'right' }
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
        columns: [
          {name: 'orden', label: 'Nivel', field: 'orden', sortable: true, type: 'string', align: 'left'},
          {name: 'nombre', label: 'Usuario', field: 'nombre', sortable: true, type: 'string', align: 'left'},
          {name: 'perfil', label: 'Perfil', field: 'perfil', sortable: true, type: 'string', align: 'left'},
          {name: 'alteracion', label: 'Alterar presupuesto', field: 'alteracion', sortable: true, type: 'string', align: 'left'},
          { name: 'acciones', label: 'Acciones', field: 'acciones', type: 'string', align: 'right' }
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
        columns: [
          {name: 'nombre', label: 'Usuario', field: 'nombre', sortable: true, type: 'string', align: 'left'},
          {name: 'perfil', label: 'Perfil', field: 'perfil', sortable: true, type: 'string', align: 'left'},
          { name: 'acciones', label: 'Acciones', field: 'acciones', type: 'string', align: 'right' }
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
        columns: [
          {name: 'nombre', label: 'Usuario', field: 'nombre', sortable: true, type: 'string', align: 'left'},
          {name: 'perfil', label: 'Perfil', field: 'perfil', sortable: true, type: 'string', align: 'left'},
          { name: 'acciones', label: 'Acciones', field: 'acciones', type: 'string', align: 'right' }
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
      modal: {
        x: 0,
        y: 0,
        transition: null
      }
    }
  },
  computed: {
    ...mapGetters({
      recursosOptions: 'vnt/recurso/selectOptions',
      usuariosOptions: 'sys/users/selectOptionsName',
      proyectos: 'pmo/proyecto/proyectos'
    }),
    arrayTreeObj () {
      let newObj = []
      this.recursive(this.actividades, newObj, 0, this.itemId, this.isExpanded)
      return newObj
    }
  },
  created () {
    this.loadAll()
  },
  watch: {
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
    }
  },
  methods: {
    ...mapActions({
      getProyectos: 'pmo/proyecto/refresh',
      getProyectosbyID: 'pmo/proyecto/getByID',
      saveProyecto: 'pmo/proyecto/save',
      updateProyecto: 'pmo/proyecto/update',
      deleteProyecto: 'pmo/proyecto/delete',
      getUsuarios: 'sys/users/refresh',
      getRecursos: 'vnt/recurso/refresh',

      getActividadesByProyecto: 'pmo/carga/getByProyecto',
      getSolicitantesByProyecto: 'pmo/solicitantes/getByProyecto',
      getAutorizadoresByProyecto: 'pmo/autorizadores/getByProyecto',
      getResponsablesByProyecto: 'pmo/responsable_pagos/getByProyecto',
      getColaboradoresByProyecto: 'pmo/colaboradores/getByProyecto',
      updateActividad: 'pmo/carga/update',
      deleteActividad: 'pmo/carga/delete',
      saveSolicitante: 'pmo/solicitantes/save',
      deleteSolicitante: 'pmo/solicitantes/delete',
      saveAutorizador: 'pmo/autorizadores/save',
      deleteAutorizador: 'pmo/autorizadores/delete',
      saveResponsable: 'pmo/responsable_pagos/save',
      deleteResponsable: 'pmo/responsable_pagos/delete',
      saveColaborador: 'pmo/colaboradores/save',
      deleteColaborador: 'pmo/colaboradores/delete'
    }),

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
      this.form.loading = true
      await this.getRecursos()
      if (this.$route.params.id > 0) {
        await this.getProyectosbyID({id: this.$route.params.id})
      } else {
        await this.getProyectos()
      }
      await this.getUsuarios()
      this.form.loading = false
    },
    setViewPresupuestos (view) {
      this.viewsPresupuestos.grid = false
      this.viewsPresupuestos.create = false
      this.viewsPresupuestos.update = false
      this.viewsPresupuestos[view] = true
    },
    validarFechasPresupuestos (fechaInicio, fechaFin) {
      if (moment(String(fechaInicio)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss') <= moment(String(fechaFin)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')) {
        return true
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
    getAllActividadesProject () {
      this.getActividades().then(({data}) => {
        if (data.result === 'success') {
        }
      }).catch(error => {
        console.error(error)
      })
    },
    savePresupuestos () {
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        if (this.validarFechasPresupuestos(this.form.fieldsPresupuesto.inicio, this.form.fieldsPresupuesto.fin)) {
          this.loadingButton = true
          let params = this.form.fieldsPresupuesto
          this.saveProyecto(params).then(({data}) => {
            this.loadingButton = false
            this.$showMessage(data.message.title, data.message.content)
            if (data.result === 'success') {
              this.loadAll()
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
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        if (this.validarFechasPresupuestos(this.form.fieldsPresupuesto.inicio, this.form.fieldsPresupuesto.fin)) {
          this.loadingButton = true
          let params = this.form.fieldsPresupuesto
          this.updateProyecto(params).then(({data}) => {
            this.loadingButton = false
            this.$showMessage(data.message.title, data.message.content)
            if (data.result === 'success') {
              this.loadAll()
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
      this.loadingMessage('Cargando ...')
      let proyecto = { ...row }
      this.form.fieldsPresupuesto.id = proyecto.id
      this.form.fieldsPresupuesto.nombre_proyecto = proyecto.nombre_proyecto
      this.form.fieldsPresupuesto.recurso_id = row.recurso_id
      this.form.fieldsPresupuesto.dias = proyecto.dias
      this.form.fieldsPresupuesto.presupuesto = proyecto.presupuesto
      this.form.fieldsPresupuesto.presupuestoActual = proyecto.presupuesto
      this.form.fieldsPresupuesto.lider_proyecto = proyecto.lider_proyecto
      this.form.fieldsPresupuesto.lider_usuario = proyecto.nickname
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
      await this.cargarDetalles()
      await this.cargarResponsables()
      await this.cargarSolicitantes()
      await this.cargarAutorizadores()
      await this.cargarColaboradores()
      this.$q.loading.hide()
      this.setViewPresupuestos('update')
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
          this.loadAll()
          this.setViewPresupuestos('grid')
        }
      }).catch(error => {
        console.error(error)
      })
    },
    newRowPresupuestos () {
      this.$v.form.$reset()
      this.form.fieldsPresupuesto.recurso_id = 0
      this.form.fieldsPresupuesto.inicio = ''
      this.form.fieldsPresupuesto.fin = ''
      this.form.fieldsPresupuesto.dias = 0
      this.form.fieldsPresupuesto.presupuesto = 0
      this.form.fieldsPresupuesto.nombre_proyecto = ''
      this.form.fieldsPresupuesto.lider_proyecto = ''
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
    uploadCsvFile () {
      this.loadingMessage('Cargando los datos del csv ...')
      let file = this.$refs.fileInput.files[0]
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
          this.form_actividades.actividades_proyecto = true
          this.cargarDetalles()
          this.$q.loading.hide()
        } else {
          this.$q.loading.hide()
          if (response.data.err !== '') {
            this.$showMessage('Errores en archivo', response.data.err)
          } else {
            this.$showMessage('Error', 'No puede cargar nuevas actividades, porque las actuales ya tienen movimientos de gastos.')
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
    updateActividadDetalle () {
      this.$v.form_actividades.$touch()
      if (!this.$v.form_actividades.$error) {
        if (this.validarFechasActividades(this.form_actividades.fieldsPresupuesto.inicio, this.form_actividades.fieldsPresupuesto.fin, this.form_actividades.fieldsPresupuesto.fin_real)) {
          this.loadingButton = true
          let params = this.form_actividades.fieldsPresupuesto
          this.updateActividad(params).then(({data}) => {
            this.loadingButton = false
            this.$showMessage(data.message.title, data.message.content)
            if (data.result === 'success') {
              this.cargarDetalles()
              this.form_actividades.modal_editar = false
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
    newRespaldo () {
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
      eliminar.push(this.form_autorizadores.fieldsPresupuesto.usuario_id)

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
    }
  },
  validations: {
    form: {
      fieldsPresupuesto: {
        recurso_id: {required, minValue: minValue(1)},
        inicio: {required},
        fin: {required},
        presupuesto: {required, minValue: minValue(0), maxValue: maxValue(1000000000)},
        nombre_proyecto: {required, maxLength: maxLength(100)},
        lider_proyecto: {required}
      }
    },
    form_actividades: {
      fieldsPresupuesto: {
        nombre: {required, maxLength: maxLength(100)},
        costo: {required, minValue: minValue(0), maxValue: maxValue(1000000000)}
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
    }
  }
}
</script>

<style lang="stylus" scoped>
  q-collapsible {

  }

</style>
