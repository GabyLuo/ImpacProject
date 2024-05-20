<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-3">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Oportunidades" />
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-3 text-center" style="margin-top: 10px;">
            <q-btn-toggle color="teal" v-model="carril_id" toggle-color="primary" :options="carrilesOptions" @input="inicio()"/>
            <q-btn color="teal" @click="volver()" v-if="views.prospeccion === true">
              <q-icon name="star" color="white"/>
            </q-btn>
            <q-btn color="teal" @click="volver()" v-else>
              <q-icon name="star_half" color="white"/>
            </q-btn>
            <q-btn color="orange" @click="views_ganadas()" v-if="views.ganadas === true">
              <q-icon name="clear" color="white"/>
            </q-btn>
            <q-btn color="orange" @click="views_ganadas()" v-else>
              <q-icon name="clear" color="white"/>
            </q-btn>
          </div>
          <div class="col-sm-3 q-mt-sm">
            <center>
              <q-btn-toggle color="teal" v-model="year" toggle-color="primary" :options="selectYear" @input="filterByCarril()"/>
            </center>
          </div>
          <div class="col-sm-3 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn class="btn_nuevo" icon="add" label="Tarea" @click="newTasks(true, true, 0)"/>
              <q-btn class="btn_nuevo" icon="add" label="Oportunidad" @click="newRow()"/>
            </div>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3" style="overflow-y: auto;" v-if="views.prospeccion">
        <!-- <div class="row bg-white border-panel"> -->
          <div class="col q-px-sm">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-2 bg-white" style="margin-right: 10px; min-height: 80vh; max-height: 80vh; overflow-y: auto;">
                <div class="col-sm-12 q-title text-center" style="padding-top: 20px;padding-bottom: 15px;">
                  TAREAS
                </div>
                <q-card v-for="tar in form_tareas_ejecutivo.data" :key="tar.id" style="margin-bottom: 15px; margin-left: 10px; margin-right: 10px;" :class="'text-white bg-' + tar.color">
                  <div class="row q-col-gutter-xs">
                    <div class="col-sm-9 text-center" style="padding-top: 15px;padding-bottom: 10px;padding-left: 10px;" v-if="tar.task_type === 'PARTICULAR'">
                      {{ tar.oportunidad }}
                    </div>
                    <div class="col-sm-9 text-center" style="padding-top: 15px;padding-bottom: 10px;padding-left: 10px;" v-if="tar.task_type === 'GENERAL'">
                      {{ tar.task_type }}
                    </div>
                    <div class="col-sm-2 pull-right" style="padding-top: 5px;">
                      <q-btn small round flat icon="settings" slot="right" color="white">
                        <q-popover>
                          <q-list link separator class="no-border">
                            <q-item v-close-overlay @click.native="newTasks(true, false, tar.id)" v-if="tar.task_type === 'GENERAL'">
                              <q-item-main label="Ver"/>
                            </q-item>
                            <q-item v-close-overlay @click.native="completarTarea(tar)">
                              <q-item-main label="Marcar como completada"/>
                            </q-item>
                          </q-list>
                        </q-popover>
                      </q-btn>
                    </div>
                  </div>
                  <q-card-main class="text-black bg-grey-3">
                    <div class="col-sm-12">
                      <q-icon name="folder" :color="tar.color" size="20px" v-if="tar.task_type === 'PARTICULAR'"/>
                       {{ tar.prospecto }}
                    </div>
                    <div class="col-sm-12">
                      <q-icon name="call" :color="tar.color" size="20px" v-if="tar.tipo === 'LLAMADA'"/>
                      <q-icon name="mail" :color="tar.color" size="20px" v-if="tar.tipo === 'EMAIL'"/>
                      <q-icon name="business" :color="tar.color" size="20px" v-if="tar.tipo === 'CITA'"/>
                      <q-icon name="style" :color="tar.color" size="20px" v-if="tar.tipo === 'TAREA'"/>
                       {{ tar.actividad }}
                    </div>
                    <div class="col-sm-12">
                      <q-icon name="alarm" :color="tar.color" size="20px"/>
                       {{ tar.end_time_day }}
                    </div>
                  </q-card-main>
                </q-card>
              </div>
              <div id="scroll-impact" class="col-sm-9 bg-white" style="max-height: 80vh; overflow-y: auto;">
                <q-card v-for="msje in form_etapas.data" :key="msje.id" style="margin-bottom: 15px;" :class="'text-white bg-' + msje.color">
                  <div class="row q-col-gutter-xs" style="padding-top: 20px;">
                    <div class="col-sm-4 q-title" style="padding-left: 15px;">
                      {{ msje.nombre }}
                    </div>
                    <div class="col-sm-4 q-title text-center">
                      ${{ currencyFormat(msje.valor_prospectos) }}
                    </div>
                    <div :class="'col-sm-4 bg-' + msje.color" style="padding-right: 10px;">
                      <div :class="'col-sm-4 bg-' + msje.color + '-1'" style="border-style: solid; border-width: 3px;">
                        <q-progress :percentage="msje.porcentaje" :color="msje.color + '-10'" stripe animate style="height: 30px;" :label="msje.porcentaje"/>
                      </div>
                    </div>
                  </div>
                  <div class="row q-col-gutter-xs">
                    <div class="col-sm-4">
                    </div>
                    <div class="col-sm-4 text-center">
                    </div>
                    <div class="col-sm-4 text-center">
                      <q-label>{{ msje.porcentaje }}%</q-label>
                    </div>
                  </div>
                  <q-card-main>
                    <div class="row q-col-gutter-xs">
                      <div class="col-sm-6" v-for="oportunidad in msje.prospectos" :key="oportunidad.id">
                        <q-card class="bg-grey-3" style="margin-right: 10px;">
                          <div class="row q-col-gutter-xs" style="margin-left: 10px; margin-right: 10px;margin-bottom: 5px;">
                            <div class="col-sm-5" style="margin-bottom: 10px;margin-top: 10px;">
                              <q-item-tile class="text-black bg-grey-3 text-center" style="margin-top: 10px;text-align: center"> {{oportunidad.nombre}} </q-item-tile>
                              <q-item-tile sublabel class="text-primary text-center">{{ oportunidad.nombre_comercial }}</q-item-tile>
                              <q-item-tile sublabel class="text-center">${{ currencyFormat(oportunidad.valor_final) }}</q-item-tile>
                            </div>
                            <div class="col-sm-4" style="margin-top: 20px;">
                              <div class="col-sm-12">
                                <q-item-tile sublabel class="text-center">Tareas: {{ oportunidad.completadas }}/{{ oportunidad.tareas_por_etapa }}</q-item-tile>
                              </div>
                              <div class="col-sm-12 text-center">
                                <q-icon :name="tareas.completado ? 'star' : 'star_border'" v-for="tareas in oportunidad.tareas" :key="tareas.num" style="margin: 0;" :color="tareas.completado ? msje.color : 'grey-7'" size="25px"/>
                              </div>
                            </div>
                            <div class="col-sm-3 pull-right" style="margin-top: 13px; margin-bottom: 5px;">
                              <q-btn round dense size="12px" color="blue" @click="editRow(oportunidad)" style="margin-bottom: 5px;" v-if="validateEdit(oportunidad.ejecutivo_id)">
                                <q-tooltip>Editar</q-tooltip>
                                  <q-icon name="edit" color="white" />
                              </q-btn>
                              <q-btn round dense small size="12px" color="purple-10" @click="verProspecto(oportunidad)" style="margin-bottom: 5px;">
                                <q-tooltip>Ver prospecto</q-tooltip>
                                  <q-icon name="person" color="white" />
                              </q-btn>
                              <q-btn round dense small size="12px" color="green-10" @click="showTasks(oportunidad)" style="margin-bottom: 5px;">
                                <q-tooltip>Ver tareas</q-tooltip>
                                  <q-icon name="style" color="white" />
                              </q-btn>
                              <q-btn round dense small size="12px" color="orange" @click="modalComentario(true, oportunidad.id)" style="margin-bottom: 5px;">
                                <q-tooltip>Ver comentarios</q-tooltip>
                                  <i class="fas fa-sticky-note"></i>
                              </q-btn>
                              <!-- <q-btn round dense small size="md" color="lime-10" @click="showEjecutivos(oportunidad)" style="margin-bottom: 5px;">
                                <q-tooltip>Reasignar oportunidad</q-tooltip>
                                <q-icon name="fas fa-users" color="white" />
                              </q-btn> -->
                              <q-btn round v-if="msje.nivel!==1 && oportunidad.status !== 'LOGRADA'" dense small size="12px" color="red-10" icon="fa-arrow-up" @click="paso_atras(oportunidad)" style="margin-bottom: 5px;">
                                <q-tooltip>Volver al paso anterior</q-tooltip>
                              </q-btn>
                              <q-btn round v-if="oportunidad.status !== 'LOGRADA'" dense small size="12px" color="red-10" icon="fas fa-times" @click="no_lograda(oportunidad)" style="margin-bottom: 5px;">
                                <q-tooltip>Marcar como no lograda</q-tooltip>
                              </q-btn>
                              <q-btn round v-if="oportunidad.completada_obligatorio === oportunidad.obligatorio && msje.cierre === false" dense small size="12px" :color="msje.color" icon="fa-arrow-down" @click="paso_adelante(oportunidad)" style="margin-bottom: 5px;">
                                <q-tooltip>Paso siguiente</q-tooltip>
                              </q-btn>
                              <q-btn round v-if="oportunidad.completada_obligatorio === oportunidad.obligatorio && msje.cierre === true && oportunidad.status === 'PENDIENTE'" dense small size="12px" color="green" icon="fas fa-check" @click="paso_adelante(oportunidad)" style="margin-bottom: 5px;">
                                <q-tooltip>Marcar como lograda</q-tooltip>
                              </q-btn>
                              <q-btn round v-if="oportunidad.completada_obligatorio === oportunidad.obligatorio && msje.cierre === true && oportunidad.status === 'LOGRADA'" dense small size="12px" color="green" icon="done_all" style="margin-bottom: 5px;">
                                <q-tooltip>Lograda</q-tooltip>
                              </q-btn>
                            </div>
                            <div class="col-sm-12 pull-right">
                              <q-item-tile sublabel class="text-teal-10">{{ oportunidad.nombre_ejecutivo }}</q-item-tile>
                            </div>
                          </div>
                        </q-card>
                      </div>
                    </div>
                  </q-card-main>
                </q-card>
              </div>
            </div>
          </div>
        <!-- </div> -->
      </div>

      <div class="q-pa-md bg-grey-3" style="margin-right: 10px;overflow-y: auto;" v-if="views.logradas">
        <!-- <div class="row bg-white border-panel"> -->
          <div class="col q-px-md">
            <div class="row q-col-gutter-xs">
              <div id="scroll-impact" class="col-sm-12 bg-white" style="max-height: 80vh; overflow-y: auto;">
                <q-card style="margin-bottom: 15px;" class="text-white bg-green">
                  <div class="row q-col-gutter-xs" style="padding-top: 20px;">
                    <div class="col-sm-4 q-title" style="padding-left: 15px;">
                      OPORTUNIDADES GANADAS
                    </div>
                  </div>
                  <q-card-main>
                    <div class="row q-col-gutter-xs">
                      <div class="col-sm-4" v-for="oportunidad in ganadas.prospectos" :key="oportunidad.id">
                        <q-card class="bg-grey-3" style="margin-right: 10px;">
                          <div class="row q-col-gutter-xs" style="margin-left: 10px; margin-right: 10px;margin-bottom: 5px;">
                            <div class="col-sm-8" style="margin-bottom: 10px;margin-top: 10px;">
                              <q-item-tile class="text-black bg-grey-3 text-center" style="margin-top: 10px;text-align: center"> {{oportunidad.nombre}} </q-item-tile>
                              <q-item-tile sublabel class="text-primary text-center">{{ oportunidad.nombre_comercial }}</q-item-tile>
                              <q-item-tile sublabel class="text-center">${{ currencyFormat(oportunidad.valor_final) }}</q-item-tile>
                            </div>
                            <div class="col-sm-2" style="margin-top: 20px;">
                              <div class="col-sm-12">
                                <q-item-tile sublabel class="text-center">Lograda</q-item-tile>
                              </div>
                              <div class="col-sm-12 text-center">
                                <q-icon name="star" color="green" size="25px"/>
                              </div>
                            </div>
                            <div class="col-sm-2 pull-right" style="margin-top: 30px; margin-bottom: 5px;">
                              <q-btn round dense size="12px" color="green-10" @click="editRow(oportunidad)" style="margin-bottom: 5px;">
                                <q-tooltip>Ver oportunidad</q-tooltip>
                                  <q-icon name="visibility" color="white" />
                              </q-btn>
                              <q-btn round dense small size="12px" color="orange" @click="modalComentario(true, oportunidad.id)" style="margin-bottom: 5px;">
                                <q-tooltip>Ver comentarios</q-tooltip>
                                  <i class="fas fa-sticky-note"></i>
                              </q-btn>
                            </div>
                            <div class="col-sm-12 pull-right">
                              <q-item-tile sublabel class="text-teal-10">{{ oportunidad.nombre_ejecutivo }}</q-item-tile>
                            </div>
                          </div>
                        </q-card>
                      </div>
                    </div>
                  </q-card-main>
                </q-card>
              </div>
            </div>
          </div>
        <!-- </div> -->
      </div>

      <div class="q-pa-md bg-grey-3" style="margin-right: 10px;overflow-y: auto;" v-if="views.nologradas">
        <!-- <div class="row bg-white border-panel"> -->
          <div class="col q-px-md">
            <div class="row q-col-gutter-xs">
              <div id="scroll-impact" class="col-sm-12 bg-white" style="max-height: 80vh; overflow-y: auto;">
                <q-card style="margin-bottom: 15px;" class="text-white bg-orange">
                  <div class="row q-col-gutter-xs" style="padding-top: 20px;">
                    <div class="col-sm-4 q-title" style="padding-left: 15px;">
                      OPORTUNIDADES NO LOGRADAS
                    </div>
                  </div>
                  <q-card-main>
                    <div class="row q-col-gutter-xs">
                      <div class="col-sm-4" v-for="oportunidad in nologradas.prospectos" :key="oportunidad.id">
                        <q-card class="bg-grey-3" style="margin-right: 10px;">
                          <div class="row q-col-gutter-xs" style="margin-left: 10px; margin-right: 10px;margin-bottom: 5px;">
                            <div class="col-sm-8" style="margin-bottom: 10px;margin-top: 10px;">
                              <q-item-tile class="text-black bg-grey-3 text-center" style="margin-top: 10px;text-align: center"> {{oportunidad.nombre}} </q-item-tile>
                              <q-item-tile sublabel class="text-primary text-center">{{ oportunidad.nombre_comercial }}</q-item-tile>
                              <q-item-tile sublabel class="text-center">${{ currencyFormat(oportunidad.valor_final) }}</q-item-tile>
                            </div>
                            <div class="col-sm-2" style="margin-top: 20px;">
                              <div class="col-sm-12">
                                <q-item-tile sublabel class="text-center">No lograda</q-item-tile>
                              </div>
                              <div class="col-sm-12 text-center">
                                <q-icon name="clear" color="orange" size="25px"/>
                              </div>
                            </div>
                            <div class="col-sm-2 pull-right" style="margin-top: 30px; margin-bottom: 5px;">
                              <q-btn round dense size="12px" color="green-10" @click="editRow(oportunidad)" style="margin-bottom: 5px;">
                                <q-tooltip>Ver oportunidad</q-tooltip>
                                  <q-icon name="visibility" color="white" />
                              </q-btn>
                              <q-btn round dense small size="12px" color="orange" @click="modalComentario(true, oportunidad.id)" style="margin-bottom: 5px;">
                                <q-tooltip>Ver comentarios</q-tooltip>
                                  <i class="fas fa-sticky-note"></i>
                              </q-btn>
                            </div>
                            <div class="col-sm-12 pull-right">
                              <q-item-tile sublabel class="text-teal-10">{{ oportunidad.nombre_ejecutivo }}</q-item-tile>
                            </div>
                          </div>
                        </q-card>
                      </div>
                    </div>
                  </q-card-main>
                </q-card>
              </div>
            </div>
          </div>
        <!-- </div> -->
      </div>
    </div>

    <!--Crear un aptitudes-->

    <div v-if="views.create">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Oportunidades" to="" @click.native="atras()"/>
                <q-breadcrumbs-el label="Nueva oportunidad"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
             <div class="q-pa-sm q-gutter-sm" >
              <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
              <q-btn @click="save()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
            </div>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="folder" icon-color="dark" :error="$v.form.fields.nombre.$error" error-label="Ingrese el nombre de la oportunidad">
                  <q-input upper-case v-model="form.fields.nombre" type="text" inverted color="dark" float-label="Nombre de la oportunidad" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="business" icon-color="dark" :error="$v.form.fields.prospecto.$error" error-label="Ingrese o seleccione el nombre comercial">
                  <q-select v-model="form.fields.prospecto_id" inverted color="dark" float-label="Prospecto" :options="prospectosOptions" filter v-if="form.fields.tipo_prospecto === 'EXISTENTE'" @input="selected()"></q-select>
                </q-field>
              </div>
              <div class="col-sm-1" style="padding-left: 40px;">
                <q-btn @click="newProspecto()" class="btn_nuevo" icon="add" :loading="loadingButton"/>
              </div>
              <div class="col-sm-2">
                <q-field icon="label" icon-color="dark">
                  <q-select v-model="form.fields.tipo" inverted color="dark" float-label="Tipo" :options="tiposOptions" filter></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="label" icon-color="dark">
                  <q-select v-model="form.fields.area" inverted color="dark" float-label="Area" :options="areasOptions" filter></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="label" icon-color="dark" :error="$v.form.fields.giro_comercial.$error" error-label="Seleccione el giro comercial">
                  <q-select :readonly="form.fields.tipo_prospecto === 'EXISTENTE'" v-model="form.fields.giro_comercial" inverted color="dark" float-label="Giro comercial" :options="girosOptions" filter></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="work" icon-color="dark">
                  <q-select v-model="form.fields.sector_id" inverted color="dark" float-label="Sector" :options="programasOptions" filter @input="selectSubprograma"></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="folder_special" icon-color="dark">
                  <q-select v-model="form.fields.subsector_id" inverted color="dark" float-label="Subsector" :options="subprogramasOptions" filter></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                  <q-input v-model.lazy="form.fields.valor" v-money="money" inverted color="dark" float-label="Valor de la prospección" suffix="MXN"></q-input>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-user"  icon-color="dark" :error="$v.form.fields.ejecutivo_id.$error" error-label="Seleccione un ejecutivo">
                  <q-select v-model="form.fields.ejecutivo_id" inverted color="dark" float-label="Ejecutivo de ventas" :options="usersOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="person" icon-color="dark" :error="$v.form.fields.nombre_contacto.$error" error-label="Ingrese el nombre del contacto">
                  <q-input :readonly="form.fields.tipo_prospecto === 'EXISTENTE'" upper-case v-model="form.fields.nombre_contacto" type="text" inverted color="dark" float-label="Nombre del contacto" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="email" icon-color="dark" :error="$v.form.fields.email.$error" error-label="Por favor ingrese un email válido">
                  <q-input :readonly="form.fields.tipo_prospecto === 'EXISTENTE'" v-model="form.fields.email" type="email" inverted color="dark" float-label="Correo electrónico" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="phone" icon-color="dark" :error="$v.form.fields.telefono.$error" error-label="Verifique el número telefónico">
                  <q-input :readonly="form.fields.tipo_prospecto === 'EXISTENTE'" @keyup="isNumber($event,'telefono')" upper-case v-model="form.fields.telefono" inverted color="dark" float-label="Teléfono" maxlength="10"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fields.empresa_id.$error" error-label="Elija una empresa">
                  <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3" v-if="form.fields.tipo === 'GDP'">
                <q-field icon="vpn_lock" icon-color="dark">
                  <q-select v-model="form.fields.orden" inverted color="dark" float-label="Orden de gobierno" :options="ordenOptions" filter @input="is_federal()"/>
                </q-field>
              </div>
              <div class="col-sm-3" v-if="form.fields.tipo === 'GDP'">
                <q-field icon="business" icon-color="dark">
                  <q-select v-model="form.fields.ente" inverted color="dark" float-label="Tipo de ente" :options="enteOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3" v-if="form.fields.tipo === 'GDP'">
                <q-field icon="layers" icon-color="dark">
                  <q-select v-model="form.fields.adjudicacion" inverted color="dark" float-label="Método de adjudicación" :options="adjudicacionOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3" v-if="form.fields.tipo === 'GDP' && form.fields.orden !== 'FEDERAL' && form.fields.orden !== 'all'">
                <q-field icon="fas fa-globe" icon-color="dark">
                  <q-select v-model="form.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="municipiosOportunidad()"/>
                </q-field>
              </div>
              <div class="col-sm-3" v-if="form.fields.tipo === 'GDP' && form.fields.orden === 'MUNICIPAL' && form.fields.orden !== 'all'">
                <q-field icon="fas fa-map-pin" icon-color="dark">
                  <q-select v-model="form.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="municipiosOptions" filter></q-select>
                </q-field>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal editar aptitudes -->
    <div v-if="views.update">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Oportunidades" to="" @click.native="atras()"/>
                <q-breadcrumbs-el label="Editar oportunidad"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
             <div class="q-pa-sm q-gutter-sm" >
              <q-btn @click="atras()" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
              <q-btn @click="update()" class="btn_guardar" icon-right="save" :loading="loadingButton" v-if="views.prospeccion === true">Guardar</q-btn>
            </div>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="folder" icon-color="dark" :error="$v.form.fields.nombre.$error" error-label="Ingrese el nombre de la oportunidad">
                  <q-input upper-case v-model="form.fields.nombre" type="text" inverted color="dark" float-label="Nombre de la oportunidad" maxlength="100" />
                </q-field>
              </div>
              <!-- <div class="col-sm-3">
                <q-field icon="label" icon-color="dark">
                  <q-select readonly v-model="form.fields.tipo_prospecto" inverted color="dark" float-label="Prospecto" :options="tipo_prospecto" filter></q-select>
                </q-field>
              </div> -->
              <div class="col-sm-3">
                <q-field icon="business" icon-color="dark" :error="$v.form.fields.prospecto.$error" error-label="Ingrese el nombre comercial">
                  <q-select v-model="form.fields.prospecto_id" inverted color="dark" float-label="Prospecto" :options="prospectosOptions" filter v-if="form.fields.tipo_prospecto === 'EXISTENTE'" @input="selected()"></q-select>
                  <!-- <q-input upper-case v-model="form.fields.prospecto" type="text" inverted color="dark" float-label="Nombre comercial prospecto" maxlength="100" v-else/> -->
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="label" icon-color="dark">
                  <q-select v-model="form.fields.tipo" inverted color="dark" float-label="Tipo" :options="tiposOptions" filter></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="label" icon-color="dark">
                  <q-select v-model="form.fields.area" inverted color="dark" float-label="Area" :options="areasOptions" filter></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="work" icon-color="dark">
                  <q-select v-model="form.fields.sector_id" inverted color="dark" float-label="Sector" :options="programasOptions" filter @input="selectSubprograma"></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="folder_special" icon-color="dark">
                  <q-select v-model="form.fields.subsector_id" inverted color="dark" float-label="Subsector" :options="subprogramasOptions" filter></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-dollar-sign" icon-color="dark">
                  <q-input v-model.lazy="form.fields.valor" v-money="money" inverted color="dark" float-label="Valor de la prospección" suffix="MXN"></q-input>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-dollar-sign" icon-color="dark">
                  <q-input v-model.lazy="form.fields.valor_final" v-money="money" inverted color="dark" float-label="Valor final de la prospección" suffix="MXN"></q-input>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="person" icon-color="dark" :error="$v.form.fields.nombre_contacto.$error" error-label="Por favor ingrese el nombre del contacto">
                  <q-input readonly upper-case v-model="form.fields.nombre_contacto" type="text" inverted color="dark" float-label="Nombre del contacto" maxlength="50" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="email" icon-color="dark" :error="$v.form.fields.email.$error" error-label="Por favor ingrese un email válido">
                  <q-input readonly v-model="form.fields.email" type="email" inverted color="dark" float-label="Correo electrónico" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="phone" icon-color="dark" :error="$v.form.fields.telefono.$error" error-label="Verifique el número telefónico">
                  <q-input readonly @keyup="isNumber($event,'telefono')" upper-case v-model="form.fields.telefono" inverted color="dark" float-label="Teléfono" maxlength="10"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-user"  icon-color="dark" :error="$v.form.fields.ejecutivo_id.$error" error-label="Seleccione un ejecutivo">
                  <q-select v-model="form.fields.ejecutivo_id" inverted color="dark" float-label="Ejecutivo de ventas" :options="usersOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-building" icon-color="dark" :error="$v.form.fields.empresa_id.$error" error-label="Elija una empresa">
                  <q-select v-model="form.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3" v-if="form.fields.tipo === 'GDP'">
                <q-field icon="vpn_lock" icon-color="dark">
                  <q-select v-model="form.fields.orden" inverted color="dark" float-label="Orden de gobierno" :options="ordenOptions" filter @input="is_federal()"/>
                </q-field>
              </div>
              <div class="col-sm-3" v-if="form.fields.tipo === 'GDP'">
                <q-field icon="business" icon-color="dark">
                  <q-select v-model="form.fields.ente" inverted color="dark" float-label="Tipo de ente" :options="enteOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3" v-if="form.fields.tipo === 'GDP'">
                <q-field icon="layers" icon-color="dark">
                  <q-select v-model="form.fields.adjudicacion" inverted color="dark" float-label="Método de adjudicación" :options="adjudicacionOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3" v-if="form.fields.tipo === 'GDP' && form.fields.orden !== 'all'">
                <q-field icon="fas fa-globe" icon-color="dark" :error="$v.form_licitaciones.fields.estado_id.$error" error-label="Seleccione un estado">
                  <q-select v-model="form_licitaciones.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="municipiosOportunidad()"/>
                </q-field>
              </div>
              <div class="col-sm-3" v-if="form.fields.tipo === 'GDP' && form.fields.orden === 'MUNICIPAL' && form.fields.orden !== 'all'">
                <q-field icon="fas fa-map-pin" icon-color="dark">
                  <q-select v-model="form.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="municipiosOptions" filter></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-tag" icon-color="dark" :error="$v.form_licitaciones.fields.folio.$error" error-label="Escriba el folio de la licitación">
                  <q-input upper-case v-model="form_licitaciones.fields.folio" type="text" inverted color="dark" float-label="Folio licitación" maxlength="20" />
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="format_size" icon-color="dark" :error="$v.form_licitaciones.fields.procedimiento.$error" error-label="Escriba el procedimiento de la licitación">
                  <q-input upper-case v-model="form_licitaciones.fields.procedimiento" type="text" inverted color="dark" float-label="Procedimiento licitación" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-3">
                <div class="icono_field">
                  <q-field  icon="fas fa-dollar-sign" icon-color="dark">
                    <q-input v-model="form.fields.valor_licitacion" v-money="money" inverted color="dark" float-label="Monto inicial licitación" suffix="MXN"></q-input>
                  </q-field>
                </div>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-12">
                <q-tabs v-model="tabPrincipal" class="shadow-1" inverted animated swipeable color="teal" align="justify" style="margin-top: 30px;">
                  <q-tab default name="tab_aliados" icon="person" slot="title" label="Aliados"/>
                  <q-tab name="tab_historial" icon="history" slot="title" label="Historial"/>
                  <q-tab name="tab_cotizaciones" icon="fas fa-file-pdf" slot="title" label="Cotizaciones"/>
                  <q-tab-pane name="tab_aliados">
                    <div class="row q-col-gutter-xs">
                      <div class="col-sm-4">
                        <q-field icon="fas fa-user-tie" icon-color="dark" :error="$v.form_aliados.fields.aliado_id.$error" error-label="Elija un aliado">
                          <q-select v-model="form_aliados.fields.aliado_id" inverted color="dark" float-label="Aliado" :options="aliadosOptions" filter></q-select>
                        </q-field>
                      </div>
                      <div class="col-sm-4" style="margin-left: 40px;">
                        <q-btn @click="agregarAliado()" class="btn_guardar" icon-right="add" :loading="loadingButton">Agregar</q-btn>
                      </div>
                    </div>
                    <div class="row q-col-gutter-xs">
                      <div class="col-sm-12 q-pa-md" id="sticky-table-scroll-corto">
                        <q-table id="sticky-table-newstyle"
                          :data="form_aliados.data"
                          :columns="form_aliados.columns"
                          :selected.sync="form_aliados.selected"
                          :filter="form_aliados.filter"
                          color="positive"
                          title=""
                          :dense="true"
                          :pagination.sync="form_aliados.pagination"
                          :loading="form_aliados.loading"
                          :rows-per-page-options="form_aliados.rowsOptions">
                          <template slot="body" slot-scope="props">
                            <q-tr :props="props">
                              <q-td  key="aliado" :props="props">{{ props.row.aliado }}</q-td>
                              <q-td key="acciones" :props="props">
                                <q-btn small flat @click="deleteSingleAliado(props.row.id)" color="negative" icon="delete">
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
                  </q-tab-pane>
                  <q-tab-pane name="tab_historial">
                    <div class="row q-col-gutter-xs">
                      <q-timeline responsive style="padding: 0 24px;">
                        <q-timeline-entry v-for="historial in form_oportunidades.historial" :key="historial.id"
                          :subtitle="historial.created"
                          side="right"
                          :icon="historial.icon"
                          :color="historial.color"
                        >
                          <div>
                            {{ historial.descripcion }}
                          </div>
                        </q-timeline-entry>
                    </q-timeline>
                    </div>
                  </q-tab-pane>
                  <q-tab-pane name="tab_cotizaciones">
                    <cotizaciones :oportunidad_id="form.fields.id"/>
                  </q-tab-pane>
                </q-tabs>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <q-modal v-if="form_oportunidades.modal_reasignar" style="background-color: rgba(0,0,0,0.7);" v-model="form_oportunidades.modal_reasignar" :content-css="{width: '50vw', height: '180px'}">
      <q-modal-layout>
        <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
          <div class="col-sm-6">
            <q-toolbar-title>
              Reasignar oportunidad
            </q-toolbar-title>
          </div>
          <div class="col-sm-6 pull-right">
            <q-btn
              flat
              round
              dense
              color="white"
              @click="form_oportunidades.modal_reasignar = false"
              icon="fas fa-times-circle"
            />
          </div>
        </q-toolbar>
        <div class="row q-mt-lg">
          <div class="col-sm-8" style="margin-top: 5px;margin-right: 15px;">
            <q-field icon="fas fa-user"  icon-color="dark">
              <q-select v-model="form_oportunidades.fields.usuario_id" inverted color="dark" float-label="Usuario" :options="usersOptions" filter/>
            </q-field>
          </div>
          <div class="col-sm-3" style="margin-top: 10px;">
            <q-btn @click="reasignarOportunidad()" class="btn_guardar" icon-right="save" :loading="loadingButton">Reasignar</q-btn>
          </div>
        </div>
      </q-modal-layout>
    </q-modal>

    <q-modal v-if="form_logs.modal_previous_step" style="background-color: rgba(0,0,0,0.7);" v-model="form_logs.modal_previous_step" :content-css="{width: '50vw', height: '180px'}">
      <q-modal-layout>
        <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
          <div class="col-sm-6">
            <q-toolbar-title>
              Retroceder un paso a la oportunidad
            </q-toolbar-title>
          </div>
          <div class="col-sm-6 pull-right">
            <q-btn
              flat
              round
              dense
              color="white"
              @click="form_logs.modal_previous_step = false"
              icon="fas fa-times-circle"
            />
          </div>
        </q-toolbar>
        <div class="row q-mt-lg">
          <div class="col-sm-8" style="margin-top: 5px;margin-right: 15px;">
            <q-field icon="label" icon-color="dark" :error="$v.form_logs.fields.descripcion.$error" error-label="Ingrese el motio por el cual la oportunidad retrocederá un paso">
              <q-input upper-case v-model="form_logs.fields.descripcion" type="text" inverted color="dark" float-label="Motivo" maxlength="1000" />
            </q-field>
          </div>
          <div class="col-sm-3" style="margin-top: 10px;">
            <q-btn @click="updatePasoAtras()" class="btn_guardar" icon-right="save" :loading="loadingButton">Retroceder</q-btn>
          </div>
        </div>
      </q-modal-layout>
    </q-modal>

    <q-modal v-if="form_logs.modal_lograda" style="background-color: rgba(0,0,0,0.7);" v-model="form_logs.modal_lograda" :content-css="{width: '50vw', height: '180px'}">
      <q-modal-layout>
        <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
          <div class="col-sm-6">
            <q-toolbar-title>
              Marcar oportunidad como no lograda
            </q-toolbar-title>
          </div>
          <div class="col-sm-6 pull-right">
            <q-btn
              flat
              round
              dense
              color="white"
              @click="form_logs.modal_lograda = false"
              icon="fas fa-times-circle"
            />
          </div>
        </q-toolbar>
        <div class="row q-mt-lg">
          <div class="col-sm-8" style="margin-top: 5px;margin-right: 15px;">
            <q-field icon="label" icon-color="dark" :error="$v.form_logs.fields.descripcion.$error" error-label="Ingrese el motio por el cual la oportunidad será marcada como no lograda">
              <q-input upper-case v-model="form_logs.fields.descripcion" type="text" inverted color="dark" float-label="Motivo" maxlength="1000" />
            </q-field>
          </div>
          <div class="col-sm-3" style="margin-top: 10px;">
            <q-btn @click="updateNoLograda()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
          </div>
        </div>
      </q-modal-layout>
    </q-modal>

    <q-modal v-if="form_oportunidades.modal_tareas" style="background-color: rgba(0,0,0,0.7);" v-model="form_oportunidades.modal_tareas" :content-css="{width: '50vw', height: '300px'}">
      <q-modal-layout>
        <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
          <div class="col-sm-6">
            <q-toolbar-title>
              Tareas
            </q-toolbar-title>
          </div>
          <div class="col-sm-6 pull-right">
            <q-btn
              flat
              round
              dense
              color="white"
              @click="form_oportunidades.modal_tareas = false"
              icon="fas fa-times-circle"
            />
          </div>
        </q-toolbar>
        <div class="row q-mt-sm" style="margin-left: 10px; margin-right: 10px;">
          <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                <q-table id="sticky-table-newstyle"
                    :data="form_tareas.data"
                    :columns="form_tareas.columns"
                    :selected.sync="form_tareas.selected"
                    :filter="form_tareas.filter"
                    color="positive"
                    title=""
                    :dense="true"
                    class="bg-white"
                    :pagination.sync="form_tareas.pagination"
                    :loading="form_tareas.loading"
                    :rows-per-page-options="form_tareas.rowsOptions">
                    <template slot="body" slot-scope="props">
                      <q-tr :props="props">
                        <q-td key="tipo" :props="props">
                          <q-item-side icon="mail" color="green-10" v-if="props.row.tipo === 'EMAIL'"/>
                          <q-item-side icon="call" color="green-10" v-if="props.row.tipo === 'LLAMADA'"/>
                          <q-item-side icon="business" color="green-10" v-if="props.row.tipo === 'CITA'"/>
                          <q-item-side icon="style" color="green-10" v-if="props.row.tipo === 'TAREA'"/>
                        </q-td>
                        <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                        <q-td key="obligatorio" :props="props">
                          <q-checkbox readonly v-model="props.row.obligatorio" color="red-10"/>
                        </q-td>
                        <q-td key="completado" :props="props">
                          <q-checkbox readonly v-model="props.row.completado" color="green-10" v-if="(form_oportunidades.oportunidad.ejecutivo_id !== props.row.ejecutivo_id) || form_oportunidades.oportunidad.status === 'LOGRADA'"/>
                          <q-checkbox v-model="props.row.completado" color="green-10" v-else @click.native="completarTarea(props.row)"/>
                        </q-td>
                        <q-td key="id" :props="props" class="col-sm-12">
                          <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="tareaInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf, .PNG, .JPG, .JPEG, .XML, .DOCX, .PDF" @change="uploadTareaFile()" />
                          <q-btn small flat @click="selectedTarea(props.row)" color="green-9" :loading="loadingButton">
                            <q-icon name="cloud_upload" />&nbsp;
                            <q-tooltip>Subir archivo</q-tooltip>
                          </q-btn>
                          <div style="display: inline;" v-for="cot in props.row.archivos" :key="cot.id">
                            <q-btn small flat :icon="cot.icon" :color="cot.color">
                              <q-tooltip>{{ cot.nombre }}</q-tooltip>
                              <q-popover>
                                <q-list link separator class="scroll" style="min-width: 100px">
                                  <q-item v-close-overlay @click.native="verDocumentoTarea(cot)" v-if="cot.extension !== 'docx' && cot.extension !== 'xml' && cot.extension !== 'xlsx' && cot.extension !== 'XLSX' && cot.extension !== 'DOCX'">
                                    <q-item-main label="Ver"/>
                                  </q-item>
                                  <q-item v-close-overlay @click.native="descargarDocumentoTarea(cot.id, cot.extension)">
                                    <q-item-main label="Descargar"/>
                                  </q-item>
                                  <q-item v-close-overlay @click.native="tareaAccion(cot)">
                                    <q-item-main label="Eliminar"/>
                                  </q-item>
                                </q-list>
                              </q-popover>
                            </q-btn>
                          </div>
                        </q-td>
                      </q-tr>
                    </template>
                  </q-table>
              </div>
        </div>
      </q-modal-layout>
    </q-modal>

    <q-modal v-if="form_prospectos.modal_prospecto" style="background-color: rgba(0,0,0,0.7);" v-model="form_prospectos.modal_prospecto" :content-css="{width: '80vw', height: '500px'}">
      <q-modal-layout>
        <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
          <div class="col-sm-6">
            <q-toolbar-title>
              Prospecto {{ this.form_prospectos.oportunidad }}
            </q-toolbar-title>
          </div>
          <div class="col-sm-6 pull-right">
            <!-- <q-btn
              flat
              round
              dense
              color="green"
              @click="actualizarProspecto()"
              icon="fas fa-check-circle"
            /> -->
            <q-btn
              flat
              round
              dense
              color="white"
              @click="form_prospectos.modal_prospecto = false"
              icon="fas fa-times-circle"
            />
          </div>
        </q-toolbar>
        <div class="row q-mt-sm" style="margin-left: 10px; margin-right: 10px;">
          <div class="col q-pa-md">
              <div class="row q-col-gutter-xs">
                <div class="col-sm-3">
                  <q-field icon="business" icon-color="dark">
                    <q-input upper-case v-model="form_prospectos.fields.nombre_compania" type="text" inverted color="dark" float-label="Nombre compañía" maxlength="100"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="store" icon-color="dark" :error="$v.form_prospectos.fields.nombre_comercial.$error" error-label="Escriba el nombre comercial">
                    <q-input upper-case v-model="form_prospectos.fields.nombre_comercial" type="text" inverted color="dark" float-label="Nombre comercial" maxlength="100"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="label" icon-color="dark" :error="$v.form_prospectos.fields.giro_comercial.$error" error-label="Seleccione un giro comercial">
                    <q-select v-model="form_prospectos.fields.giro_comercial" inverted color="dark" float-label="Giro comercial" :options="girosOptions" filter></q-select>
                  </q-field>
                </div>
                <!-- <div class="col-sm-3">
                  <q-field icon="label" icon-color="dark">
                    <q-select v-model="form_prospectos.fields.producto_id" inverted color="dark" float-label="Producto" :options="productosOptions" filter></q-select>
                  </q-field>
                </div> -->
                <div class="col-sm-3">
                  <q-field icon="fas fa-id-card" icon-color="dark" :error="$v.form_prospectos.fields.rfc.$error" error-label="Escriba un rfc válido">
                    <q-input upper-case v-model="form_prospectos.fields.rfc" inverted color="dark" float-label="RFC"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="business_center" icon-color="dark">
                    <q-input upper-case v-model="form_prospectos.fields.razon_social" type="text" inverted color="dark" float-label="Razón social" maxlength="100" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="label" icon-color="dark">
                    <q-input @keyup="isNumber($event,'dias')" v-model="form_prospectos.fields.dias_credito" inverted color="dark" float-label="Días de crédito" maxlength="5" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="dns" icon-color="dark">
                    <q-input upper-case v-model="form_prospectos.fields.abreviatura" type="text" inverted color="dark" float-label="Abreviatura" maxlength="100" />
                  </q-field>
                </div>
              </div>
              <div class="row q-mt-lg" style="padding-bottom: 15px;">
                Contacto
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-6">
                  <q-field icon="person" icon-color="dark" :error="$v.form_prospectos.fields.nombre_contacto.$error" error-label="Por favor ingrese un nombre de contacto">
                    <q-input upper-case v-model="form_prospectos.fields.nombre_contacto" type="text" inverted color="dark" float-label="Nombre del contacto" maxlength="50" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="email" icon-color="dark" :error="$v.form_prospectos.fields.email.$error" error-label="Por favor ingrese un email válido">
                    <q-input v-model="form_prospectos.fields.email" type="email" inverted color="dark" float-label="Correo electrónico" maxlength="100" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="phone" icon-color="dark" :error="$v.form_prospectos.fields.telefono.$error" error-label="Verifique el número telefónico">
                    <q-input @keyup="isNumber($event,'telefono')" upper-case v-model="form_prospectos.fields.telefono" inverted color="dark" float-label="Teléfono" maxlength="10"/>
                  </q-field>
                </div>
              </div>
              <div class="row q-mt-lg" style="padding-bottom: 15px;">
                Domicilio
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-3">
                  <q-field icon="fas fa-road" icon-color="dark">
                    <q-input upper-case v-model="form_prospectos.fields.calle" type="text" inverted color="dark" float-label="Calle" maxlength="100" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-map-signs" icon-color="dark" >
                    <q-input upper-case v-model="form_prospectos.fields.colonia" type="text" inverted color="dark" float-label="Colonia" maxlength="100" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="map" icon-color="dark" >
                    <q-input upper-case v-model="form_prospectos.fields.poblacion" type="text" inverted color="dark" float-label="Población" maxlength="100" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-hashtag" icon-color="dark">
                    <q-input upper-case v-model="form_prospectos.fields.numero_exterior" type="text" inverted color="dark" float-label="Número exterior" maxlength="20" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-hashtag" icon-color="dark">
                    <q-input upper-case v-model="form_prospectos.fields.numero_interior" type="text" inverted color="dark" float-label="Número interior" maxlength="20" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="room" icon-color="dark" :error="$v.form_prospectos.fields.codigo_postal.$error" error-label="Verifique el código postal">
                    <q-input @keyup="isNumber($event,'codigo_postal')" v-model="form_prospectos.fields.codigo_postal" inverted color="dark" float-label="C.P." maxlength="5" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-globe" icon-color="dark">
                    <q-select v-model="form_prospectos.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-map-pin" icon-color="dark">
                    <q-select v-model="form_prospectos.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="municipiosOptions" filter></q-select>
                  </q-field>
                </div>
              </div>
              <div class="row q-mt-lg" style="padding-bottom: 15px;">
                Acta constitucional
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-3">
                  <q-field icon="person" icon-color="dark">
                    <q-input upper-case v-model="form_prospectos.fields.acta_representante" type="text" inverted color="dark" float-label="Representante legal" maxlength="50" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-globe" icon-color="dark">
                    <q-select v-model="form_prospectos.fields.acta_estado" inverted color="dark" float-label="Estado" :options="estadosOptions" filter/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="label" icon-color="dark">
                    <q-input @keyup="isNumber($event,'notaria')" upper-case v-model="form_prospectos.fields.acta_notaria" inverted color="dark" float-label="N. Notaría" maxlength="5"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="account_balance" icon-color="dark">
                    <q-input upper-case v-model="form_prospectos.fields.acta_notario" type="text" inverted color="dark" float-label="Notario" maxlength="50" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="date_range" icon-color="dark">
                    <q-datetime v-model="form_prospectos.fields.acta_fecha" type="date" inverted color="dark" float-label="Fecha" align="center"></q-datetime>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="folder" icon-color="dark">
                    <q-input  @keyup="isNumber($event,'libro')" upper-case v-model="form_prospectos.fields.acta_libro" inverted color="dark" float-label="Libro" maxlength="5"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="label" icon-color="dark">
                    <q-input upper-case v-model="form_prospectos.fields.acta_rpp" type="text" inverted color="dark" float-label="RPP" maxlength="50" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="description" icon-color="dark">
                    <q-input upper-case v-model="form_prospectos.fields.acta_giro" type="text" inverted color="dark" float-label="Giro" maxlength="50" />
                  </q-field>
                </div>
                <div class="col-sm-12 pull-right">
                  <q-btn @click="guardarProspecto()" class="btn_guardar" icon-right="save" :loading="loadingButton" v-if="this.form_prospectos.editar_prospecto !== true">Guardar</q-btn>
                  <q-btn @click="actualizarProspecto()" class="btn_guardar" icon-right="save" :loading="loadingButton" v-if="this.form_prospectos.editar_prospecto === true">Guardar</q-btn>
                </div>
              </div>
            </div>
        </div>
      </q-modal-layout>
    </q-modal>

    <q-modal v-if="form_tareas_ejecutivo.modal_files" style="background-color: rgba(0,0,0,0.7);" v-model="form_tareas_ejecutivo.modal_files" :content-css="{width: '50vw', height: '180px'}">
      <q-modal-layout>
        <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
          <div class="col-sm-6">
            <q-toolbar-title>
              Detalles de la tarea
            </q-toolbar-title>
          </div>
          <div class="col-sm-6 pull-right">
            <q-btn
              flat
              round
              dense
              color="white"
              @click="form_tareas_ejecutivo.modal_files = false"
              icon="fas fa-times-circle"
            />
          </div>
        </q-toolbar>
        <div class="row q-mt-lg">
          <div class="col-sm-8" style="margin-top: 5px;margin-right: 15px;">
            <q-field icon="fas fa-user"  icon-color="dark">
              <q-select v-model="form_tareas_ejecutivo.fields.usuario_id" inverted color="dark" float-label="Usuario" :options="usersOptions" filter/>
            </q-field>
          </div>
          <div class="col-sm-3" style="margin-top: 10px;">
            <q-btn @click="reasignarOportunidad()" class="btn_guardar" icon-right="save" :loading="loadingButton">Reasignar</q-btn>
          </div>
        </div>
      </q-modal-layout>
    </q-modal>

    <comentarios :show="form_oportunidades.modal_comentario" :oportunidad_id="form_oportunidades.fields.id" @closeModal="modalComentario($event)"/>
    <tareas :show="new_tarea" :create="create_tarea" :tarea="id_tarea_general" @closeModal="newTasks($event)"/>
  </q-page>
</template>

<script>
import { filter } from 'quasar'
import {VMoney} from 'v-money'
import axios from 'axios'
import { mapActions, mapGetters } from 'vuex'
import { required, maxLength, minLength, minValue, email, helpers } from 'vuelidate/lib/validators'
import Comentarios from '../../components/layout/crm/Comentarios'
import Tareas from '../../components/layout/crm/Tareas'
import Cotizaciones from '../../components/layout/crm/Cotizaciones'
// import VCalendar from 'v-calendar'
const rfc = helpers.regex('rfc', /[A-ZÑ&]{3,4}\d{6}[A-V1-9][A-Z1-9][0-9A]/)

export default {
  components: {
    Comentarios,
    Tareas,
    Cotizaciones
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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'DIRECTOR'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'AUXILIAR'.toUpperCase() || propiedades[i].toUpperCase() === 'VENTAS'.toUpperCase() || propiedades[i].toUpperCase() === 'Coordinador GDP'.toUpperCase()) {
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
      user_id: null,
      new_tarea: false,
      create_tarea: false,
      id_tarea_general: 0,
      today: new Date(),
      tabPrincipal: false,
      municipiosOptions: [{label: '---Seleccione---', value: 0}],
      areasOptions: [ { label: 'GDP', value: 'GDP' }, { label: 'COMERCIAL', value: 'COMERCIAL' }, { label: '--Seleccione--', value: 'all' } ],
      tiposOptions: [ { label: 'VENTAS GOBIERNO', value: 'GDP' }, { label: 'VENTAS DIRECTAS', value: 'RETAIL' } ],
      tipo_prospecto: [ { label: 'NUEVO', value: 'NUEVO' }, { label: 'EXISTENTE', value: 'EXISTENTE' } ],
      ordenOptions: [ { label: 'FEDERAL', value: 'FEDERAL' }, { label: 'ESTATAL', value: 'ESTATAL' }, { label: 'MUNICIPAL', value: 'MUNICIPAL' }, { label: '--Seleccione--', value: 'all' } ],
      enteOptions: [ { label: 'EJECUTIVO', value: 'EJECUTIVO' }, { label: 'DEPENDENCIA', value: 'DEPENDENCIA' }, { label: 'OPD', value: 'OPD' }, { label: '--Seleccione--', value: 'all' } ],
      adjudicacionOptions: [ { label: 'LICITACIÓN', value: 'LICITACIÓN' }, { label: 'ADJUDICACIÓN DIRECTA', value: 'ADJUDICACIÓN DIRECTA' }, { label: 'INVITACIÓN A 3', value: 'INVITACIÓN A 3' }, { label: 'VENTA DIRECTA', value: 'VENTA DIRECTA' }, { label: '--Seleccione--', value: 'all' } ],
      selectYear: [ { label: '' + (new Date().getFullYear() - 3), value: '' + (new Date().getFullYear() - 3) }, { label: '' + (new Date().getFullYear() - 2), value: '' + (new Date().getFullYear() - 2) }, { label: '' + (new Date().getFullYear() - 1), value: '' + (new Date().getFullYear() - 1) }, { label: '' + (new Date().getFullYear()), value: '' + (new Date().getFullYear()) } ],
      year: '' + (new Date().getFullYear()),
      money: {
        decimal: '.',
        thousands: ',',
        precision: 2,
        masked: false
      },
      carril_id: 1,
      loadingButton: false,
      views: {
        prospeccion: true,
        logradas: false,
        nologradas: false,
        grid: true,
        create: false,
        update: false
      },
      ganadas: {
        prospectos: []
      },
      nologradas: {
        prospectos: []
      },
      form: {
        fields: {
          id: 0,
          nombre: '',
          sector_id: 0,
          subsector_id: 0,
          email: '',
          telefono: '',
          nombre_contacto: '',
          prospecto: '',
          prospecto_id: 0,
          giro_comercial: 0,
          valor: 0,
          valor_final: 0,
          ejecutivo_id: 0,
          tipo: 'GDP',
          tipo_prospecto: 'EXISTENTE',
          empresa_id: 0,
          orden: 'all',
          ente: 'all',
          adjudicacion: 'all',
          estado_id: 0,
          municipio_id: 0,
          folio: '',
          procedimiento: '',
          valor_licitacion: 0,
          area: 'all'
        },
        // data: [],
        columns: [
          { name: 'nombre', label: 'Nombre', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_etapas: {
        data: []
      },
      form_prospectos: {
        modal_prospecto: false,
        editar_prospecto: false,
        data: [],
        prospecto: [],
        oportunidad: '',
        fields: {
          id: 0,
          nombre_compania: '',
          nombre_comercial: '',
          giro_comercial: 0,
          producto_id: 0,
          rfc: '',
          razon_social: '',
          dias_credito: 0,
          abreviatura: '',
          nombre_contacto: '',
          email: '',
          telefono: '',
          calle: '',
          colonia: '',
          poblacion: '',
          numero_exterior: '',
          numero_interior: '',
          codigo_postal: '',
          estado_id: 0,
          municipio_id: 0,
          acta_representante: '',
          acta_estado: 0,
          acta_notaria: 0,
          acta_notario: '',
          acta_fecha: '',
          acta_libro: 0,
          acta_rpp: '',
          acta_giro: ''
        }
      },
      form_oportunidades: {
        fields: {
          id: 0,
          usuario_id: 0
        },
        oportunidad: [],
        modal_reasignar: false,
        modal_tareas: false,
        modal_comentario: false,
        historial: []
      },
      form_aliados: {
        fields: {
          id: 0,
          oportunidad_id: 0,
          aliado_id: 0
        },
        columns: [
          { name: 'aliado', label: 'Aliado', field: 'aliado', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        data: [],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_tareas: {
        tarea: [],
        data: [],
        registro_tarea: [],
        columns: [
          { name: 'tipo', label: 'Tipo', field: 'tipo', sortable: true, type: 'string', align: 'left' },
          { name: 'nombre', label: 'Nombre', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'obligatorio', label: 'Obligatorio', field: 'obligatorio', sortable: true, type: 'string', align: 'center' },
          { name: 'completado', label: 'Completada', field: 'completado', sortable: true, type: 'string', align: 'center' },
          { name: 'id', label: 'Archivos', field: 'id', sortable: true, type: 'string', align: 'center' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_tareas_ejecutivo: {
        data: [],
        modal_files: false
      },
      form_logs: {
        data: [],
        fields: {
          descripcion: ''
        },
        modal_lograda: false,
        modal_previous_step: false
      },
      form_licitaciones: {
        fields: {
          folio: 0,
          estado_id: 0,
          procedimiento: ''
        }
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
      carrilesOptions: 'crm/crm/selectOptions'
    }),
    estadosOptions () {
      let estados = this.$store.getters['vnt/estado/selectOptions']
      estados.splice(0, 0, {label: '---Seleccione---', value: 0})
      estados.sort(function (a, b) {
        if (a.nombre > b.nombre) {
          return 1
        }
        if (a.nombre < b.nombre) {
          return -1
        }
        return 0
      })
      return estados
    },
    programasOptions () {
      let programas = this.$store.getters['vnt/programa/selectOptions']
      programas.splice(0, 0, {label: '---Seleccione---', value: 0})
      programas.sort(function (a, b) {
        if (a.nombre > b.nombre) {
          return 1
        }
        if (a.nombre < b.nombre) {
          return -1
        }
        return 0
      })
      return programas
    },
    subprogramasOptions () {
      let subprogramas = this.$store.getters['vnt/subprograma/selectOtherOptions'].filter(p => p.programa_id === this.form.fields.sector_id)
      subprogramas.splice(0, 0, {label: '--Seleccione--', value: 0})
      subprogramas.sort((a, b) => {
        return (a.label < b.label) ? -1 : (a.label > b.label) ? 1 : 0
      })
      return subprogramas
    },
    girosOptions () {
      let giros = this.$store.getters['crm/giros/selectOptions']
      giros.splice(0, 0, {label: '---Seleccione---', value: 0})
      giros.sort(function (a, b) {
        if (a.nombre > b.nombre) {
          return 1
        }
        if (a.nombre < b.nombre) {
          return -1
        }
        return 0
      })
      return giros
    },
    usersOptions () {
      let users = this.$store.getters['sys/users/selectOptionsName']
      users.sort(function (a, b) {
        if (a.nickname > b.nickname) {
          return 1
        }
        if (a.nickname < b.nickname) {
          return -1
        }
        return 0
      })
      return users
    },
    aliadosOptions () {
      let aliados = this.$store.getters['com/aliados/selectOptions']
      aliados.splice(0, 0, {label: '---Seleccione---', value: 0})
      return aliados
    },
    prospectosOptions () {
      let prospectos = this.$store.getters['crm/prospectos/selectOptions']
      prospectos.splice(0, 0, {label: '---Seleccione---', value: 0})
      return prospectos
    },
    empresasOptions () {
      let empresas = this.$store.getters['vnt/empresa/selectOptions']
      empresas.splice(0, 0, {label: '---Seleccione---', value: 0})
      return empresas
    }
  },
  created () {
    this.loadAll()
  },
  methods: {
    ...mapActions({
      getCarriles: 'crm/crm/refresh',
      getGiros: 'crm/giros/refresh',
      getProgramas: 'vnt/programa/refresh',
      getSubprogramas: 'vnt/subprograma/refresh',
      getUsuarios: 'sys/users/refresh',
      getEtapasByCarril: 'crm/etapas/getWithData',
      getOportunidadesGanadas: 'crm/oportunidades/getGanadas',
      getOportunidadesNoLogradas: 'crm/oportunidades/getNoLogradas',
      getProspectos: 'crm/prospectos/refresh',
      getProspectosById: 'crm/prospectos/getById',
      saveOportunidad: 'crm/oportunidades/save',
      updateOportunidad: 'crm/oportunidades/update',
      updateEjecutivo: 'crm/oportunidades/updateEjecutivo',
      getTareas: 'crm/tareas/getByOportunidad',
      updateTarea: 'crm/tareas/updateComplete',
      updateEtapa: 'crm/oportunidades/updateStep',
      previousStep: 'crm/oportunidades/previousStep',
      getProspectoBy: 'crm/prospectos/getBy',
      getEstados: 'vnt/estado/refresh',
      getMunicipiosByEstado: 'vnt/municipio/getByEstado',
      updateProspectos: 'crm/prospectos/update',
      saveProspectos: 'crm/prospectos/save',
      getAliados: 'com/aliados/refresh',
      getAliadosByOportunidad: 'crm/aliados/getByOportunidad',
      saveAliados: 'crm/aliados/save',
      deleteAliados: 'crm/aliados/delete',
      getTareasByEjecutivo: 'crm/tareas/getByEjecutivo',
      getHistorial: 'crm/oportunidades/getHistorial',
      noLograda: 'crm/oportunidades/noLograda',
      deleteArchivo: 'crm/archivostareas/deleteFormato',
      getEmpresas: 'vnt/empresa/refresh',
      getAllTareas: 'crm/tareas/getByAllOportunidad'
    }),
    loadCarril () {
      this.getCarriles().then(({data}) => {
        if (data.crm.length > 0) {
          this.carril_id = data.crm[0].id
          this.filterByCarril()
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async loadAll () {
      this.form.loading = true
      await this.loadCarril()
      // this.filterByCarril()
      await this.getUsuarios()
      await this.getTareasEjecutivo()
      await this.getEstados()
      await this.getGiros()
      await this.getProgramas()
      await this.getSubprogramas()
      await this.getProspectos()
      await this.getEmpresas()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    atras () {
      this.filterByCarril()
      this.getTareasEjecutivo()
      this.setView('grid')
    },
    save () {
      this.form.fields.nombre = this.form.fields.nombre.trim()
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        params.carril_id = this.carril_id
        params.monto = this.evaluaMonto(this.form.fields.valor)
        this.saveOportunidad(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.form.fields.ejecutivo_id = data.id_user
            this.form_aliados.fields.oportunidad_id = data.oportunidad_id
            this.form.fields.id = data.oportunidad_id
            this.$q.notify({
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'done',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.getAliados()
            this.historial()
            this.setView('update')
            // this.filterByCarril()
            // this.setView('grid')
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
      this.form.fields.nombre = this.form.fields.nombre.trim()
      this.form.fields.estado_id = this.form_licitaciones.fields.estado_id
      this.form.fields.folio = this.form_licitaciones.fields.folio
      this.form.fields.procedimiento = this.form_licitaciones.fields.procedimiento
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        params.monto = this.evaluaMonto(this.form.fields.valor)
        params.monto_final = this.evaluaMonto(this.form.fields.valor_final)
        params.monto_licitacion = this.evaluaMonto(this.form.fields.valor_licitacion)
        this.updateOportunidad(params).then(({data}) => {
          this.loadingButton = false
          // this.$showMessage(data.message.title, data.message.content)
          if (data.result === 'success') {
            // ***
            this.$q.notify({
            // only required parameter is the message:
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'done',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            // ***
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
    editRow (row) {
      this.form.fields.id = row.id
      this.form.fields.nombre = row.nombre
      this.form.fields.sector_id = row.sector_id
      this.form.fields.subsector_id = row.subsector_id
      this.form.fields.email = row.email
      this.form.fields.telefono = row.telefono
      this.form.fields.nombre_contacto = row.nombre_contacto
      this.form.fields.prospecto = row.nombre_comercial
      this.form.fields.prospecto_id = row.prospecto_id
      this.form.fields.giro_comercial = row.giro_comercial
      this.form.fields.valor = row.valor
      this.form.fields.valor_final = row.valor_final
      this.form_aliados.fields.oportunidad_id = row.id
      this.form_aliados.fields.aliado_id = 0
      this.form.fields.ejecutivo_id = row.ejecutivo_id
      this.form.fields.tipo = row.tipo
      this.form.fields.tipo_prospecto = row.tipo_prospecto
      this.form.fields.empresa_id = row.empresa_id
      this.form.fields.orden = row.orden
      this.form.fields.ente = row.ente
      this.form.fields.adjudicacion = row.adjudicacion
      this.form.fields.estado_id = row.estado_id
      if (this.form.fields.estado_id > 0) {
        this.municipiosOportunidad()
      }
      this.form.fields.municipio_id = row.municipio_id
      this.form.fields.folio = row.folio
      this.form.fields.procedimiento = row.procedimiento
      this.form.fields.valor_licitacion = row.monto_licitacion
      this.form.fields.area = row.area
      this.form_licitaciones.fields.estado_id = this.form.fields.estado_id
      this.form_licitaciones.fields.folio = this.form.fields.folio
      this.form_licitaciones.fields.procedimiento = this.form.fields.procedimiento
      this.selected()
      this.getAliados()
      this.getAliadosOportunidad()
      this.historial()
      this.setView('update')
    },
    editRowReadonly (row) {
      this.form.fields.id = row.id
      this.form.fields.nombre = row.nombre
      this.form.fields.sector_id = row.sector_id
      this.form.fields.subsector_id = row.subsector_id
      this.form.fields.email = row.email
      this.form.fields.telefono = row.telefono
      this.form.fields.nombre_contacto = row.nombre_contacto
      this.form.fields.prospecto = row.nombre_comercial
      this.form.fields.prospecto_id = row.prospecto_id
      this.form.fields.giro_comercial = row.giro_comercial
      this.form.fields.valor = row.valor
      this.form.fields.valor_final = row.valor_final
      this.form_aliados.fields.oportunidad_id = row.id
      this.form_aliados.fields.aliado_id = 0
      this.form.fields.ejecutivo_id = row.ejecutivo_id
      this.form.fields.tipo = row.tipo
      this.form.fields.tipo_prospecto = row.tipo_prospecto
      this.form.fields.empresa_id = row.empresa_id
      this.form.fields.orden = row.orden
      this.form.fields.ente = row.ente
      this.form.fields.adjudicacion = row.adjudicacion
      this.form.fields.estado_id = row.estado_id
      this.form.fields.municipio_id = row.municipio_id
      this.form.fields.folio = row.folio
      this.form.fields.procedimiento = row.procedimiento
      this.form.fields.valor_licitacion = row.monto_licitacion
      this.form.fields.area = row.area
      this.form_licitaciones.fields.estado_id = this.form.fields.estado_id
      this.form_licitaciones.fields.folio = this.form.fields.folio
      this.form_licitaciones.fields.procedimiento = this.form.fields.procedimiento
    },
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar el giro?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete(id)
      }).catch(() => {})
    },
    delete (id) {
      let params = {id: id}
      this.deleteGiros(params).then(({data}) => {
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
      this.form.fields.nombre = ''
      this.form.fields.sector_id = 0
      this.form.fields.subsector_id = 0
      this.form.fields.email = ''
      this.form.fields.telefono = ''
      this.form.fields.nombre_contacto = ''
      this.form.fields.prospecto = ''
      this.form.fields.prospecto_id = 0
      this.form.fields.giro_comercial = 0
      this.form.fields.valor = 0
      this.form.fields.tipo = 'GDP'
      this.form.fields.tipo_prospecto = 'EXISTENTE'
      this.form.fields.empresa_id = 0
      this.form.fields.orden = 'all'
      this.form.fields.ente = 'all'
      this.form.fields.adjudicacion = 'all'
      this.form.fields.estado_id = 0
      this.form.fields.municipio_id = 0
      this.form.fields.area = 'all'
      this.form.fields.ejecutivo_id = 0
      this.cargarProspectos()
      this.getGiros()
      this.setView('create')
    },
    async filterByCarril () {
      if (this.views.logradas === true) {
        this.getDataGanadas()
      }
      if (this.views.prospeccion === true) {
        this.getDataByCarril()
      }
      if (this.views.nologradas === true) {
        this.getDataNoLogradas()
      }
    },
    async getDataByCarril () {
      this.$q.loading.show({message: 'Cargando...'})
      await this.getEtapasByCarril({carril_id: this.carril_id, year: this.year}).then(({data}) => {
        if (data.etapas.length > 0) {
          this.form_etapas.data = data.etapas
          this.user_id = data.user_id
        }
      }).catch(error => {
        console.error(error)
      })
      this.$q.loading.hide()
    },
    async getDataGanadas () {
      this.$q.loading.show({message: 'Cargando...'})
      await this.getOportunidadesGanadas({carril_id: this.carril_id, year: this.year}).then(({data}) => {
        if (data.oportunidades.length > 0) {
          this.ganadas.prospectos = data.oportunidades
        }
      }).catch(error => {
        console.error(error)
      })
      this.$q.loading.hide()
    },
    async getDataNoLogradas () {
      this.$q.loading.show({message: 'Cargando...'})
      await this.getOportunidadesNoLogradas({carril_id: this.carril_id, year: this.year}).then(({data}) => {
        if (data.oportunidades.length > 0) {
          this.nologradas.prospectos = data.oportunidades
        }
      }).catch(error => {
        console.error(error)
      })
      this.$q.loading.hide()
    },
    cargarProspectos () {
      this.form_prospectos.data = []
      this.getProspectos().then(({data}) => {
        this.form_prospectos.data = data.prospectos
      }).catch(error => {
        console.error(error)
      })
    },
    parseProspectos () {
      return this.form_prospectos.data.map(prospecto => {
        return {
          label: prospecto.nombre_comercial,
          value: prospecto.nombre_comercial
        }
      })
    },
    search (terms, done) {
      setTimeout(() => {
        done(filter(terms, {field: 'value', list: this.parseProspectos()}))
      }, 500)
    },
    selected () {
      this.getProspectosById({id: this.form.fields.prospecto_id}).then(({data}) => {
        if (data.prospectos.length > 0) {
          this.form_prospectos.prospecto = data.prospectos
          this.form.fields.prospecto_id = data.prospectos[0].id
          this.form.fields.prospecto = data.prospectos[0].nombre_comercial
          this.form.fields.giro_comercial = data.prospectos[0].giro_comercial
          this.form.fields.nombre_contacto = data.prospectos[0].nombre_contacto
          this.form.fields.email = data.prospectos[0].email
          this.form.fields.telefono = data.prospectos[0].telefono
        }
      }).catch(error => {
        console.error(error)
      })
    },
    selectedName (item) {
      this.getProspectosById({nombre_comercial: item}).then(({data}) => {
        if (data.prospectos.length > 0) {
          this.form_prospectos.prospecto = data.prospectos
          this.form.fields.prospecto_id = data.prospectos[0].id
          this.form.fields.giro_comercial = data.prospectos[0].giro_comercial
          this.form.fields.nombre_contacto = data.prospectos[0].nombre_contacto
          this.form.fields.email = data.prospectos[0].email
          this.form.fields.telefono = data.prospectos[0].telefono
        }
      }).catch(error => {
        console.error(error)
      })
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
    selectSubprograma () {
      this.form.fields.subsector_id = 0
    },
    showEjecutivos (oportunidad) {
      this.getUsuarios()
      this.form_oportunidades.fields.id = oportunidad.id
      this.form_oportunidades.fields.usuario_id = oportunidad.ejecutivo_id
      this.form_oportunidades.modal_reasignar = true
    },
    reasignarOportunidad () {
      let params = this.form_oportunidades.fields
      this.updateEjecutivo(params).then(({data}) => {
        this.loadingButton = false
        if (data.result === 'success') {
          this.$q.notify({
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
        this.form_oportunidades.modal_reasignar = false
        this.filterByCarril()
        this.getTareasEjecutivo()
        this.setView('grid')
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
        case 'numero_exterior':
          this.form.fields.numero_exterior = this.form.fields.numero_exterior.replace(/[^0-9.]/g, '')
          break
        case 'numero_interior':
          this.form.fields.numero_interior = this.form.fields.numero_interior.replace(/[^0-9.]/g, '')
          break
        case 'codigo_postal':
          this.form.fields.codigo_postal = this.form.fields.codigo_postal.replace(/[^0-9.]/g, '')
          this.$v.form.fields.codigo_postal.$touch()
          break
        case 'notaria':
          this.form.fields.acta_notaria = this.form.fields.acta_notaria.replace(/[^0-9.]/g, '')
          break
        case 'libro':
          this.form.fields.acta_libro = this.form.fields.acta_libro.replace(/[^0-9.]/g, '')
          break
        case 'dias':
          this.form.fields.dias_credito = this.form.fields.dias_credito.replace(/[^0-9.]/g, '')
          break
        default:
          break
      }
    },
    showComment (oportunidad) {
      this.form_oportunidades.fields.id = oportunidad.id
      this.form_oportunidades.modal_comentario = true
      this.form_oportunidades.oportunidad = oportunidad
    },
    showTasks (oportunidad) {
      this.editRowReadonly(oportunidad)
      this.form_oportunidades.fields.id = oportunidad.id
      this.form_oportunidades.oportunidad = oportunidad
      this.is_federal()
      this.selected()
      this.getTareasByOportunidad()
      this.form_oportunidades.modal_tareas = true
    },
    getTareasByOportunidad () {
      this.getTareas({oportunidad_id: this.form_oportunidades.fields.id}).then(({data}) => {
        this.form_tareas.data = data.tareas
      }).catch(error => {
        console.error(error)
      })
    },
    completarTarea (row) {
      if (row.completado) {
        if (row.licitacion) {
          this.$v.form.$reset()
          this.$v.form_licitaciones.$reset()
          this.$v.form.$touch()
          this.$v.form_licitaciones.$touch()
          if (!this.$v.form.$error && !this.$v.form_licitaciones.$error) {
            if (this.form.fields.orden === 'all') {
              this.$showMessage('¡Advertencia!', 'Debe elegir una orden de gobierno')
              row.completado = false
            }
            if (this.form_oportunidades.oportunidad.monto_licitacion !== this.form_oportunidades.oportunidad.valor_final) {
              this.$showMessage('¡Advertencia!', 'El monto de la licitación debe ser igual al valor final de la prospección.')
              row.completado = false
            }
            if (this.form.fields.orden !== 'all' && (this.form_oportunidades.oportunidad.monto_licitacion === this.form_oportunidades.oportunidad.valor_final)) {
              this.tareaCompletada(row)
            }
          } else {
            this.$showMessage('¡Advertencia!', 'Campos incompletos, será redirigido a la sección de editar oportunidad')
            if (this.form.fields.estado_id > 0) {
              this.municipiosOportunidad()
            }
            this.selected()
            this.getAliados()
            this.getAliadosOportunidad()
            this.historial()
            this.setView('update')
            row.completado = false
          }
        }
        if (row.proyecto) {
          this.$v.form.$reset()
          this.$v.form.$touch()
          if (!this.$v.form.$error) {
            if (this.form.fields.subsector_id === 0) {
              this.$showMessage('¡Advertencia!', 'Debe elegir un sector y un subsector')
              row.completado = false
            }
            if (parseFloat(this.form_oportunidades.oportunidad.valor_final) < 1) {
              this.$showMessage('¡Advertencia!', 'El valor final de la prospección debe ser mayor a 0.')
              row.completado = false
            }
            if (this.form.fields.subsector_id !== 0 && (parseFloat(this.form_oportunidades.oportunidad.valor_final) > 1)) {
              this.tareaCompletada(row)
            }
          } else {
            this.$showMessage('¡Advertencia!', 'Campos incompletos, será redirigido a la sección de editar oportunidad')
            if (this.form.fields.estado_id > 0) {
              this.municipiosOportunidad()
            }
            this.selected()
            this.getAliados()
            this.getAliadosOportunidad()
            this.historial()
            this.setView('update')
            row.completado = false
          }
        }
        /* if (row.proyecto) {
          this.tareaCompletada(row)
        } */
        if (!row.licitacion && !row.proyecto) {
          this.tareaCompletada(row)
        }
      } else {
        this.tareaCompletada(row)
      }
    },
    tareaCompletada (row) {
      let params = row
      this.updateTarea(params).then(({data}) => {
        this.loadingButton = false
        if (data.result === 'success') {
          this.$q.notify({
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
        // this.form_oportunidades.modal_tareas = false
        this.filterByCarril()
        this.getTareasEjecutivo()
      }).catch(error => {
        console.error(error)
      })
    },
    paso_adelante (row) {
      let params = { carril_id: row.carril_id, etapa_id: row.etapa_id, id: row.id }
      this.updateEtapa(params).then(({data}) => {
        this.loadingButton = false
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content,
            timeout: 6000,
            type: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'done',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
        // this.form_oportunidades.modal_tareas = false
        this.filterByCarril()
        this.getTareasEjecutivo()
      }).catch(error => {
        console.error(error)
      })
    },
    paso_atras (oportunidad) {
      this.form_oportunidades.oportunidad = oportunidad
      this.form_logs.fields.descripcion = ''
      this.form_logs.modal_previous_step = true
    },
    updatePasoAtras () {
      let params = this.form_oportunidades.oportunidad
      params.descripcion = this.form_logs.fields.descripcion
      this.previousStep(params).then(({data}) => {
        this.loadingButton = false
        if (data.result === 'success') {
          this.$q.notify({
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
        this.form_logs.modal_previous_step = false
        this.filterByCarril()
        this.getTareasEjecutivo()
      }).catch(error => {
        console.error(error)
      })
    },
    verProspecto (prospecto) {
      this.$v.form_prospectos.$reset()
      this.form_prospectos.oportunidad = prospecto.nombre
      this.getProspectoBy({id: prospecto.prospecto_id}).then(({data}) => {
        this.form_prospectos.prospecto = data.prospectos
        this.form_prospectos.fields.id = this.form_prospectos.prospecto[0].id
        this.form_prospectos.fields.nombre_compania = this.form_prospectos.prospecto[0].nombre_compania
        this.form_prospectos.fields.nombre_comercial = this.form_prospectos.prospecto[0].nombre_comercial
        this.form_prospectos.fields.giro_comercial = this.form_prospectos.prospecto[0].giro_comercial
        this.form_prospectos.fields.producto_id = this.form_prospectos.prospecto[0].producto_id
        this.form_prospectos.fields.rfc = this.form_prospectos.prospecto[0].rfc
        this.form_prospectos.fields.razon_social = this.form_prospectos.prospecto[0].razon_social
        this.form_prospectos.fields.dias_credito = this.form_prospectos.prospecto[0].dias_credito
        this.form_prospectos.fields.abreviatura = this.form_prospectos.prospecto[0].abreviatura
        this.form_prospectos.fields.nombre_contacto = this.form_prospectos.prospecto[0].nombre_contacto
        this.form_prospectos.fields.email = this.form_prospectos.prospecto[0].email
        this.form_prospectos.fields.telefono = this.form_prospectos.prospecto[0].telefono
        this.form_prospectos.fields.calle = this.form_prospectos.prospecto[0].calle
        this.form_prospectos.fields.colonia = this.form_prospectos.prospecto[0].colonia
        this.form_prospectos.fields.poblacion = this.form_prospectos.prospecto[0].poblacion
        this.form_prospectos.fields.numero_exterior = this.form_prospectos.prospecto[0].numero_exterior
        this.form_prospectos.fields.numero_interior = this.form_prospectos.prospecto[0].numero_interior
        this.form_prospectos.fields.codigo_postal = this.form_prospectos.prospecto[0].codigo_postal
        this.form_prospectos.fields.estado_id = this.form_prospectos.prospecto[0].estado_id
        if (this.form_prospectos.fields.estado_id > 0) {
          this.cargaMunicipios()
        }
        this.form_prospectos.fields.municipio_id = this.form_prospectos.prospecto[0].municipio_id
        this.form_prospectos.fields.acta_representante = this.form_prospectos.prospecto[0].acta_representante
        this.form_prospectos.fields.acta_estado = this.form_prospectos.prospecto[0].acta_estado
        this.form_prospectos.fields.acta_notaria = this.form_prospectos.prospecto[0].acta_notaria
        this.form_prospectos.fields.acta_notario = this.form_prospectos.prospecto[0].acta_notario
        this.form_prospectos.fields.acta_fecha = this.form_prospectos.prospecto[0].acta_fecha
        this.form_prospectos.fields.acta_libro = this.form_prospectos.prospecto[0].acta_libro
        this.form_prospectos.fields.acta_rpp = this.form_prospectos.prospecto[0].acta_rpp
        this.form_prospectos.fields.acta_giro = this.form_prospectos.prospecto[0].acta_giro
        this.form_prospectos.editar_prospecto = true
        this.form_prospectos.modal_prospecto = true
      }).catch(error => {
        console.error(error)
      })
    },
    async cargaMunicipios () {
      this.municipiosOptions = []
      this.form_prospectos.fields.municipio_id = 0
      await this.getMunicipiosByEstado({estado_id: this.form_prospectos.fields.estado_id}).then(({data}) => {
        this.municipiosOptions.push({label: '---Seleccione---', value: 0})
        if (data.municipios.length > 0) {
          data.municipios.forEach(municipio => {
            this.municipiosOptions.push({label: municipio.nombre, value: municipio.id})
          })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async municipiosOportunidad () {
      this.municipiosOptions = []
      this.form.fields.municipio_id = 0
      await this.getMunicipiosByEstado({estado_id: this.form.fields.estado_id}).then(({data}) => {
        this.municipiosOptions.push({label: '---Seleccione---', value: 0})
        if (data.municipios.length > 0) {
          data.municipios.forEach(municipio => {
            this.municipiosOptions.push({label: municipio.nombre, value: municipio.id})
          })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    newProspecto () {
      this.$v.form_prospectos.$reset()
      this.form_prospectos.fields.id = 0
      this.form_prospectos.fields.nombre_compania = ''
      this.form_prospectos.fields.nombre_comercial = ''
      this.form_prospectos.fields.giro_comercial = 0
      this.form_prospectos.fields.producto_id = 0
      this.form_prospectos.fields.rfc = ''
      this.form_prospectos.fields.razon_social = ''
      this.form_prospectos.fields.dias_credito = 0
      this.form_prospectos.fields.abreviatura = ''
      this.form_prospectos.fields.nombre_contacto = ''
      this.form_prospectos.fields.email = ''
      this.form_prospectos.fields.telefono = ''
      this.form_prospectos.fields.calle = ''
      this.form_prospectos.fields.colonia = ''
      this.form_prospectos.fields.poblacion = ''
      this.form_prospectos.fields.numero_exterior = ''
      this.form_prospectos.fields.numero_interior = ''
      this.form_prospectos.fields.codigo_postal = ''
      this.form_prospectos.fields.estado_id = 0
      this.form_prospectos.fields.municipio_id = 0
      this.form_prospectos.fields.acta_representante = ''
      this.form_prospectos.fields.acta_estado = ''
      this.form_prospectos.fields.acta_notaria = ''
      this.form_prospectos.fields.acta_notario = ''
      this.form_prospectos.fields.acta_fecha = ''
      this.form_prospectos.fields.acta_libro = ''
      this.form_prospectos.fields.acta_rpp = ''
      this.form_prospectos.fields.acta_giro = ''
      this.municipiosOptions = []
      this.municipiosOptions.push({label: '---Seleccione---', value: 0})
      this.form_prospectos.editar_prospecto = false
      this.getGiros()
      this.form_prospectos.modal_prospecto = true
    },
    guardarProspecto () {
      this.$v.form_prospectos.$touch()
      if (!this.$v.form_prospectos.$error) {
        this.loadingButton = true
        let params = this.form_prospectos.fields
        this.saveProspectos(params).then(({data}) => {
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
            this.getProspectos()
            this.form.fields.prospecto_id = parseInt(data.prospecto_id)
            this.selected()
            // this.getProspectos()
            // console.log(this.form_prospectos.data)
            // this.parseProspectos()
            this.form_prospectos.modal_prospecto = false
            // this.getProspectos()
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
    actualizarProspecto () {
      this.$v.form_prospectos.$touch()
      if (!this.$v.form_prospectos.$error) {
        this.loadingButton = true
        let params = this.form_prospectos.fields
        this.updateProspectos(params).then(({data}) => {
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
            this.filterByCarril()
            this.form_prospectos.modal_prospecto = false
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
    getAliadosOportunidad () {
      this.form_aliados.data = []
      this.getAliadosByOportunidad({oportunidad_id: this.form.fields.id}).then(({data}) => {
        if (data.aliados.length > 0) {
          this.form_aliados.data = data.aliados
        }
      }).catch(error => {
        console.error(error)
      })
    },
    agregarAliado () {
      this.$v.form_aliados.$touch()
      if (!this.$v.form_aliados.$error) {
        this.loadingButton = true
        let params = this.form_aliados.fields
        this.saveAliados(params).then(({data}) => {
          this.loadingButton = false
          if (data.result === 'success') {
            this.$q.notify({
              message: data.message.content,
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'done',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.$v.form_aliados.$reset()
            this.form_aliados.fields.aliado_id = 0
            this.getAliadosOportunidad()
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
    deleteSingleAliado (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar el aliado?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.deleteAliado(id)
      }).catch(() => {})
    },
    deleteAliado (id) {
      let params = {id: id}
      this.deleteAliados(params).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content,
            timeout: 3000,
            type: 'green',
            textColor: 'white', // if default 'white' doesn't fits
            icon: 'delete',
            position: 'top-right' // 'top', 'left', 'bottom-left' etc
          })
          this.getAliadosOportunidad()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    getTareasEjecutivo () {
      this.form_tareas_ejecutivo.data = []
      this.getTareasByEjecutivo().then(({data}) => {
        if (data.tareas.length > 0) {
          this.form_tareas_ejecutivo.data = data.tareas
        }
      }).catch(error => {
        console.error(error)
      })
    },
    verTarea (row) {
      this.form_tareas.tarea = row
    },
    historial () {
      this.getHistorial({id: this.form.fields.id}).then(({data}) => {
        this.form_oportunidades.historial = data.history
      }).catch(error => {
        console.error(error)
      })
    },
    no_lograda (oportunidad) {
      this.form_oportunidades.oportunidad = oportunidad
      this.form_logs.fields.descripcion = ''
      this.form_logs.modal_lograda = true
    },
    updateNoLograda () {
      let params = this.form_oportunidades.oportunidad
      params.descripcion = this.form_logs.fields.descripcion
      this.noLograda(params).then(({data}) => {
        this.loadingButton = false
        if (data.result === 'success') {
          this.$q.notify({
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
        this.form_logs.modal_lograda = false
        this.filterByCarril()
        this.getTareasEjecutivo()
      }).catch(error => {
        console.error(error)
      })
    },
    selectedTarea (row) {
      this.$refs.tareaInput.value = ''
      this.form_tareas.registro_tarea = row
      this.$refs.tareaInput.click()
    },
    uploadTareaFile () {
      try {
        let file = this.$refs.tareaInput.files[0]
        let formData = new FormData()
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('tarea_id', this.form_tareas.registro_tarea.id)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('archivostareas/uploadDocumento', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              console.log(response)
              if (response.data.result === 'success') {
                this.$q.notify({
                  message: 'Se ha cargado el archivo', timeout: 3000, type: 'green', textColor: 'white', icon: 'done', position: 'top-right'
                })
                this.getTareasByOportunidad()
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
    verDocumentoTarea (tarea) {
      var uri = process.env.API + '/public/assets/archivos_tareas/' + tarea.id + '.' + tarea.extension
      window.open(uri, '_blank')
    },
    descargarDocumentoTarea (id, ext) {
      // let ur = process.env.API + '/public/assets/solicitudes/comprobantes/' + id + '.' + ext
      let uri = process.env.API + `archivostareas/getFile/${id}/${ext}`
      window.open(uri, '_blank')
    },
    tareaAccion (archivo) {
      this.$q.dialog({
        title: 'Confirmar acción',
        message: 'Se eliminará el archivo',
        ok: 'Eliminar',
        cancel: 'Cancelar',
        preventClose: true
      }).then(() => {
        this.borrarArchivo(archivo)
      }).catch(() => {})
    },
    borrarArchivo (archivo) {
      let params = archivo
      this.deleteArchivo(params).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'delete', position: 'top-right'
          })
          this.getTareasByOportunidad()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    is_federal () {
      if (this.form.fields.orden === 'FEDERAL') {
        this.form.fields.estado_id = 33
      }
    },
    volver () {
      if (this.views.logradas === true) {
        this.views.prospeccion = true
        this.views.logradas = false
        this.views.nologradas = false
      } else {
        this.views.prospeccion = false
        this.views.logradas = true
        this.views.nologradas = false
      }
      this.filterByCarril()
    },
    views_ganadas () {
      if (this.views.nologradas === true) {
        this.views.logradas = false
        this.views.nologradas = false
        this.views.prospeccion = true
      } else {
        this.views.nologradas = true
        this.views.logradas = false
        this.views.prospeccion = false
      }
      this.filterByCarril()
    },
    inicio () {
      this.views.prospeccion = true
      this.views.nologradas = false
      this.views.logradas = false
      this.filterByCarril()
    },
    modalComentario (show, id) {
      this.form_oportunidades.modal_comentario = show
      this.form_oportunidades.fields.id = id
    },
    newTasks (show, create, tarea) {
      this.new_tarea = show
      this.create_tarea = create
      this.id_tarea_general = tarea
      if (show === false) {
        this.getTareasEjecutivo()
      }
    },
    validateEdit (assigned) {
      if (this.user_id === 41 || this.user_id === 44) {
        if (this.user_id === assigned) {
          return true
        }
        return false
      }
      return true
    }
  },
  validations: {
    form: {
      fields: {
        nombre: { required, maxLength: maxLength(100) },
        prospecto: { required, maxLength: maxLength(100) },
        giro_comercial: { required, minValue: minValue(1) },
        nombre_contacto: { required, maxLength: maxLength(100) },
        email: { required, maxLength: maxLength(100), email },
        telefono: { required, maxLength: maxLength(10), minLength: minLength(10) },
        empresa_id: { required, minValue: minValue(1) },
        ejecutivo_id: { required, minValue: minValue(1) }
      }
    },
    form_prospectos: {
      fields: {
        nombre_contacto: { required, maxLength: maxLength(100) },
        giro_comercial: { required, minValue: minValue(1) },
        nombre_comercial: { required, maxLength: maxLength(100) },
        email: { required, maxLength: maxLength(100), email },
        telefono: { required, maxLength: maxLength(10), minLength: minLength(10) },
        codigo_postal: {maxLength: maxLength(5), minLength: minLength(5)},
        rfc: {minLength: minLength(12), maxLength: maxLength(13), rfc}
      }
    },
    form_logs: {
      fields: {
        descripcion: { required, minLength: minLength(1) }
      }
    },
    form_aliados: {
      fields: {
        aliado_id: { required, minValue: minValue(1) }
      }
    },
    form_licitaciones: {
      fields: {
        folio: { required, minLength: minLength(2) },
        estado_id: { required, minValue: minValue(1) },
        procedimiento: { required, minLength: minLength(1) }
      }
    }
  }
}
</script>

<style scoped>
/* #scroll-impact::-webkit-scrollbar {
    -webkit-appearance: none;
}
#scroll-impact::-webkit-scrollbar:vertical {
    width:10px;
}
#scroll-impact::-webkit-scrollbar-button:increment,#scroll-impact::-webkit-scrollbar-button {
    display: none;
}
#scroll-impact::-webkit-scrollbar:horizontal {
    height: 10px;
}
#scroll-impact::-webkit-scrollbar-thumb {
    background-color: #797979;
    border-radius: 20px;
    border: 2px solid #f1f2f3;
}
#scroll-impact::-webkit-scrollbar-track {
    border-radius: 10px;
} */
</style>
