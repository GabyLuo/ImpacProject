<template>
  <q-page>
    <div class="q-pa-sm panel-header">
      <div class="row">
        <div class="col-sm-12">
          <div class="q-pa-sm q-gutter-sm">
            <q-breadcrumbs align="left">
              <q-breadcrumbs-el class="fs28 page-title" label="Dashboard" icon="home" to="/dashboard"/>
            </q-breadcrumbs>
          </div>
        </div>
      </div>
    </div>

    <div class="q-pa-md bg-grey-3" v-if="principal">
      <div class="col-sm-12">
        <div class="row q-col-gutter-xs">
          <div class="col-sm-12">
            <q-tabs v-model="tab" class="shadow-1 bg-white" inverted animated swipeable color="teal" align="justify">
              <q-tab name="licitaciones" slot="title" icon="folder" label="Licitaciones" v-if="role.toUpperCase() !== 'Lider'.toUpperCase() && role.toUpperCase() !== 'Cobranza'.toUpperCase() && role.toUpperCase() !== 'Auxiliar'.toUpperCase()"/>
              <q-tab name="operaciones" slot="title" icon="work" label="Presupuestos" v-if="role.toUpperCase() !== 'Cobranza'.toUpperCase()"/>
              <q-tab name="cobranza" slot="title" icon="fas fa-dollar-sign" label="Cobranza" v-if="role.toUpperCase() !== 'Lider'.toUpperCase() && role.toUpperCase() !== 'Auxiliar'.toUpperCase() && role.toUpperCase() !== 'Ventas'.toUpperCase() && role.toUpperCase() !== 'Coordinador GDP'.toUpperCase()"/>
              <q-tab name="direccion" slot="title" icon="folder_special" label="CRM" v-if="role.toUpperCase() !== 'Lider'.toUpperCase() && role.toUpperCase() !== 'Cobranza'.toUpperCase() && role.toUpperCase() !== 'Auxiliar'.toUpperCase() && role.toUpperCase() !== 'planeación'.toUpperCase() && role.toUpperCase() !== 'Ventas'.toUpperCase() && role.toUpperCase() !== 'Finanzas'.toUpperCase()"/>
              <q-tab name="operacion" slot="title" icon="fas fa-dollar-sign" label="Finanzas" v-if="role.toUpperCase() === 'operación'.toUpperCase() || role.toUpperCase() === 'Root'.toUpperCase()"/>
              <q-tab name="finanzas" slot="title" icon="fas fa-donate" label="Gastos" v-if="(role === 'Root' || role === 'Finanzas'  || role === 'Admin' || role === 'Finanzas/Comisiones')"/>

              <q-tab-pane name="licitaciones">
                <div class="row q-mt-sm"  v-if="role.toUpperCase() == 'admin'.toUpperCase() || role.toUpperCase() == 'root'.toUpperCase()">
                  <div class="col-sm-3">
                  </div>
                  <div class="col-sm-6 q-mt-sm">
                    <q-card style="margin-left: 10px; margin-right: 10px;">
                      <q-card-title class="text-white bg-pink-10">
                          Eventos
                          <span slot="subtitle" style="color: white;">Eventos próximos</span>
                      </q-card-title>
                      <div id="lista">
                        <q-list highlight inset-separator class="col-sm-12" v-for="evt in eventos_proximos" :key="evt.id">
                          <q-item multiline>
                            <q-item-side :icon="evt.icon" :color="evt.color">
                              <!-- <q-item-tile avatar>
                                <img src="statics/calendar_evt.png">
                              </q-item-tile> -->
                            </q-item-side>
                            <q-item-main>
                              <q-item-tile :color="evt.color" label>LICITACIÓN: {{ evt.titulo }}</q-item-tile>
                              <q-item-tile :color="evt.color" sublabel lines="4">Evento: {{ evt.evento }}</q-item-tile>
                            </q-item-main>
                          <q-item-side right>
                            <q-item-tile :color="evt.color" stamp>Fecha y hora: {{ evt.fecha_evento }}</q-item-tile>
                            <q-item-tile :color="evt.color" stamp>{{ evt.nickname }}</q-item-tile>
                          </q-item-side>
                            </q-item>
                        </q-list>
                      </div>
                    </q-card>
                  </div>
                  <div class="col-sm-3">
                  </div>
                </div>
              </q-tab-pane>
              <q-tab-pane name="operaciones">
                <div class="row q-mt-lg">
                  <div class="col-sm-12 pull-right" style="margin-top: 10px;">
                    <q-btn-toggle color="teal" v-model="year" toggle-color="primary" :options="selectYear" @input="projects()"/>
                  </div>
                </div>
                <div class="row q-mt-lg">
                  <div class="col-sm-3">
                      <q-field icon="person" icon-color="dark">
                        <q-select v-model="form_filtros.fields.lider_id" inverted color="dark" float-label="Líder" :options="usuariosOptionsFilter" filter></q-select>
                      </q-field>
                    </div>
                    <div class="col-sm-3" icon-color="dark">
                      <q-field icon="fas fa-building" icon-color="dark">
                        <q-select v-model="form_filtros.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptionsFilter" filter/>
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="fas fa-globe" icon-color="dark">
                        <q-select v-model="form_filtros.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptionsFilter" filter @input="cargaMunicipios()"></q-select>
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="fas fa-map-pin" icon-color="dark">
                        <q-select v-model="form_filtros.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="municipiosOptions" filter></q-select>
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="fas fa-map-pin" icon-color="dark">
                        <q-select v-model="form_filtros.fields.tipo" inverted color="dark" float-label="Tipo" :options="tipoOptions" filter></q-select>
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="fas fa-info" icon-color="dark">
                        <q-select v-model="form_filtros.fields.finalizado" inverted color="dark" float-label="Finalizado" :options="finalizadoOptions" filter></q-select>
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="settings" icon-color="dark">
                        <q-select v-model="form_filtros.fields.activo" inverted color="dark" float-label="Activo" :options="activoOptions" filter></q-select>
                      </q-field>
                    </div>
                </div>
                <div class="col-sm-6 pull-right">
                  <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
                   <q-btn @click="projects()" color="green" icon="fas fa-search" :loading="loadingButton" style="margin-top:5px;"><q-tooltip>Buscar</q-tooltip></q-btn>
                   <q-btn @click="borrar()" color="red" icon="loop" style="margin-top:5px;"><q-tooltip>Limpiar</q-tooltip></q-btn>
                 </div>
               </div>
                <div class="row q-mt-sm">
                  <div class="col-sm-12 q-mt-sm">
                    <div class="row q-mt-sm">
                      <div class="col-sm-3" v-for="pro in data_projects" :key="pro.id">
                        <q-card style="margin-left: 10px; margin-right: 10px; margin-bottom: 20px;">
                          <div style="padding: 10px; text-align: center; min-height: 120px;max-height: 120px; font-size: 15px" v-bind:class="pro.color">
                            {{ pro.nickname }} <br>
                            {{ pro.codigo }} <br>
                            {{ pro.nombre }}
                          </div>
                          <q-item-main style="max-height: 450px; min-height: 450px;">
                            <div class="row q-mt-sm" style="max-height: 450px; min-height: 450px;">
                              <div class="col-sm-12">
                                <q-field icon="event_note" icon-color="dark">Avance (semanas): {{pro.fecha_limite}}
                                  <q-btn flat size="m" @click="capturar_avance(pro)" color="orange" icon="add_circle" style="margin-right: 10px; float:right; font-size: 13px;">
                                    <q-tooltip>Capturar avance</q-tooltip>
                                  </q-btn>
                                </q-field>
                              </div>
                              <!--  v-if="pro.colaborador === true" -->
                              <div class="col-sm-6" style="padding-top: 10px; padding-bottom: 10px;padding-left: 18px;">
                                <label>
                                  Meta: {{pro.semanas_estimadas}}/{{pro.semanas}}
                                </label>
                              </div>
                              <div class="col-sm-6" style="padding-top: 10px; padding-bottom: 10px;padding-right: 1px;">
                                <div style="width: 90%; height: 25px; background-color: #e3f2fd;">
                                  <div v-bind:style="{ width: pro.avance_estimado + '%', height: '100%', background: '#2196f3' }">
                                    <q-chip dense color="blue" text-color="white">{{ pro.avance_estimado }}%</q-chip>
                                  </div>
                                  <!-- <q-chip dense color="purple" text-color="white" v-else>{{ pro.avance_estimado }}%</q-chip> -->
                                </div>
                              </div>
                              <div class="col-sm-6" style="padding-top: 10px; padding-bottom: 10px;padding-left: 18px;">
                                <label>
                                  Real: {{pro.semanas_reales}}/{{pro.semanas}}
                                </label>
                              </div>
                              <div class="col-sm-6" style="padding-bottom: 10px;padding-right: 1px;">
                                <div style="width: 90%; height: 25px; background-color: #a5d6a7; margin-right: 1px;" v-if="pro.avance_estimado < pro.avance_real">
                                  <div v-bind:style="{ width: pro.avance_real + '%', height: '100%', background: 'green' }">
                                    <q-chip dense color="green" text-color="white">{{ pro.avance_real }}%</q-chip>
                                  </div>
                                </div>
                                <div style="width: 90%; height: 25px; background-color: #f8dd93; margin-right: 1px;" v-if="((pro.avance_real >= (pro.avance_estimado-5)) && (pro.avance_real <= pro.avance_estimado))">
                                  <div v-bind:style="{ width: pro.avance_real + '%', height: '100%', background: '#e65100' }">
                                    <q-chip dense color="amber" text-color="white">{{ pro.avance_real }}%</q-chip>
                                  </div>
                                </div>
                                <div style="width: 90%; height: 25px; background-color: #ffebee;margin-right: 1px;" v-if="(pro.avance_real < (pro.avance_estimado-5))">
                                  <div v-bind:style="{ width: pro.avance_real + '%', height: '100%', background: 'red' }">
                                    <q-chip dense color="red" text-color="white">{{ pro.avance_real }}%</q-chip>
                                  </div>
                                  <!-- <q-chip dense color="pink-10" text-color="white" v-else>Real: {{ pro.avance_real }}%</q-chip> -->
                                </div>
                              </div>
                              <div class="col-sm-12">
                                <q-field icon="fas fa-dollar-sign" icon-color="dark">Presupuesto: ${{ pro.presupuesto }}
                                </q-field>
                                <!--  v-if="pro.colaborador === true" -->
                              </div>
                              <div class="col-sm-6" style="padding-top: 10px; padding-bottom: 10px;padding-left: 18px;">
                                <label>
                                  Meta: ${{pro.costo_estimado}}
                                </label>
                              </div>
                              <div class="col-sm-6" style="padding-top: 10px; padding-bottom: 10px;padding-right: 1px;" v-if="pro.porcentaje_costo_estimado > 100">
                                <div style="width: 90%; height: 25px; background-color: #e3f2fd;">
                                  <div v-bind:style="{ width: '100' + '%', height: '100%', background: '#2196f3' }">
                                    <q-chip dense color="blue" text-color="white">{{ pro.porcentaje_costo_estimado }}%</q-chip>
                                  </div>
                                  <!-- <q-chip dense color="purple" text-color="white" v-else>{{ pro.avance_estimado }}%</q-chip> -->
                                </div>
                              </div>
                              <div class="col-sm-6" style="padding-top: 10px; padding-bottom: 10px;padding-right: 1px;" v-else>
                                <div style="width: 90%; height: 25px; background-color: #e3f2fd;">
                                  <div v-bind:style="{ width: pro.porcentaje_costo_estimado + '%', height: '100%', background: '#2196f3' }">
                                    <q-chip dense color="blue" text-color="white">{{ pro.porcentaje_costo_estimado }}%</q-chip>
                                  </div>
                                  <!-- <q-chip dense color="purple" text-color="white" v-else>{{ pro.avance_estimado }}%</q-chip> -->
                                </div>
                              </div>
                              <div class="col-sm-6" style="padding-top: 10px; padding-bottom: 10px;padding-left: 18px;">
                                <label>
                                  Solicitado: ${{pro.costo_solicitado}}
                                </label>
                              </div>
                              <div class="col-sm-6" style="padding-bottom: 10px;" v-if="pro.solicitado > 100">
                                <div style="width: 90%; height: 25px; background-color: #f8dd93; margin-right: 1px;">
                                  <div v-bind:style="{ width: '100' + '%', height: '100%', background: '#e65100' }">
                                    <q-chip dense color="amber" text-color="white">{{ pro.solicitado }}%</q-chip>
                                  </div>
                                </div>
                              </div>
                              <div class="col-sm-6" style="padding-bottom: 10px;" v-else>
                                <div style="width: 90%; height: 25px; background-color: #f8dd93; margin-right: 1px;">
                                  <div v-bind:style="{ width: pro.solicitado + '%', height: '100%', background: '#e65100' }">
                                    <q-chip dense color="amber" text-color="white">{{ pro.solicitado }}%</q-chip>
                                  </div>
                                </div>
                              </div>
                              <div class="col-sm-6" style="padding-top: 10px; padding-bottom: 10px;padding-left: 18px;">
                                <label>
                                  Pagado: ${{pro.costo_pagado}}
                                </label>
                              </div>
                              <div class="col-sm-6" style="padding-bottom: 10px;" v-if="pro.pagado > 100">
                                <div style="width: 90%; height: 25px; background-color: #a5d6a7; margin-right: 1px;">
                                  <div v-bind:style="{ width: '100' + '%', height: '100%', background: 'green' }">
                                    <q-chip dense color="green" text-color="white">{{ pro.pagado }}%</q-chip>
                                  </div>
                                </div>
                              </div>
                              <div class="col-sm-6" style="padding-bottom: 10px;" v-else>
                                <div style="width: 90%; height: 25px; background-color: #a5d6a7; margin-right: 1px;">
                                  <div v-bind:style="{ width: pro.pagado + '%', height: '100%', background: 'green' }">
                                    <q-chip dense color="green" text-color="white">{{ pro.pagado }}%</q-chip>
                                  </div>
                                </div>
                              </div>
                              <div class="col-sm-12">
                                <q-field icon="forward" icon-color="dark">Solicitudes
                                </q-field>
                              </div>
                              <div class="col-sm-12" style="padding-bottom: 20px; padding-top: 10px;">
                                <q-btn @click="ver_solicitudes(pro)" color="blue-10" icon="search" style="margin-left: 18px; font-size: 13px; float: left;">Ver</q-btn>
                                <q-btn @click="crear_solicitudes(pro)" color="orange" icon="add" style="margin-right: 22px; float:right; font-size: 13px;" v-if="pro.colaborador === true">Crear</q-btn>
                              </div>
                            </div>
                          </q-item-main>
                        </q-card>
                      </div>
                    </div>
                  </div>
                </div>
              </q-tab-pane>
              <q-tab-pane name="cobranza">
                <div class="row q-mt-lg">
                  <div class="col-sm-12 pull-right" style="margin-top: 10px;">
                    <q-btn-toggle color="teal" v-model="year" toggle-color="primary" :options="selectYear" @input="getAllCobranza()"/>
                  </div>
                </div>
                <div class="col q-pa-md">
                  <div class="row q-col-gutter-xs">
                    <div class="col-sm-4 q-pa-xs" v-if="this.year !== '2021'">
                      <q-card style="min-height: 400px;">
                        <q-card-title class="bg-light-green-10 text-white" style="text-align: center;">
                          <div class="row q-col-xs">
                            <div class="col-sm-12">
                              Cuentas por cobrar
                            </div>
                          </div>
                        </q-card-title>
                        <q-card-separator />
                        <q-card-main>
                          <div class="row q-col-gutter-sm" style="padding-bottom: 5px;">
                            <div class="col-sm-10">
                              <q-select v-model="cliente_id" inverted-color="bg-light-green-10" float-label="Cliente" :options="clientesOptions" filter @input="getAdeudosCliente()"/>
                            </div>
                            <div class="col-sm-2">
                              <q-btn flat @click="verDetallesAdeudos()" color="light-green-10" icon="visibility">
                                <q-tooltip>Ver adeudos por proyecto</q-tooltip>
                              </q-btn>
                            </div>
                          </div>
                          <div class="row q-col-gutter-sm" style="padding-bottom: 10px;padding-top: 10px;">
                            <div class="col-sm-12">
                              N° proyectos: {{ this.adeudos.proyectos }}
                            </div>
                          </div>
                          <div class="row q-col-gutter-sm">
                          </div>
                              <div class="row q-col-gutter-sm" style="padding-bottom: 5px;">
                                <div class="col-sm-6" style="padding-top: 5px;">
                                  Monto total:
                                </div>
                                <div class="col-sm-6 pull-right">
                                  <q-chip icon="fas fa-dollar-sign" color="light-green-10" text-color="white">
                                  {{ this.adeudos.contratos }}
                                  </q-chip>
                                </div>
                              </div>
                              <div class="row q-col-gutter-sm" style="padding-bottom: 5px;">
                                <div class="col-sm-6" style="padding-top: 5px;">
                                  Monto total facturado:
                                </div>
                                <div class="col-sm-6 pull-right">
                                  <q-chip icon="fas fa-dollar-sign" color="light-green-10" text-color="white">
                                  {{ this.adeudos.monto_facturado }}
                                  </q-chip>
                                </div>
                              </div>
                          <div class="row q-col-gutter-sm" style="padding-bottom: 5px;">
                            <div class="col-sm-6" style="padding-top: 5px;">
                              Monto total cobrado:
                            </div>
                            <div class="col-sm-6 pull-right">
                              <q-chip icon="fas fa-dollar-sign" color="light-green-10" text-color="white">
                              {{ this.adeudos.abonado }}
                              </q-chip>
                            </div>
                          </div>
                          <div class="row q-col-gutter-sm" style="padding-bottom: 5px;">
                            <div class="col-sm-6" style="padding-top: 5px;">
                              Monto total adeudo:
                            </div>
                            <div class="col-sm-6 pull-right">
                              <q-chip icon="fas fa-dollar-sign" color="red-10" text-color="white">
                              {{ this.adeudos.restante }}
                              </q-chip>
                            </div>
                          </div>
                          <div class="row q-col-gutter-sm" style="padding-bottom: 5px;">
                            <div class="col-sm-6" style="padding-top: 5px;">
                              Monto descontado (remisiones):
                            </div>
                            <div class="col-sm-6 pull-right">
                              <q-chip icon="fas fa-dollar-sign" color="lime-10" text-color="white">
                              {{ this.adeudos.monto_descontado }}
                              </q-chip>
                            </div>
                          </div>
                        </q-card-main>
                      </q-card>
                    </div>
                    <div class="col-sm-8 q-pa-xs" v-else>
                      <q-card style="min-height: 400px;">
                        <q-card-title class="bg-light-green-10 text-white" style="text-align: center;">
                          <div class="row q-col-xs">
                            <div class="col-sm-12">
                              Cuentas por cobrar
                            </div>
                          </div>
                        </q-card-title>
                        <q-card-separator />
                        <q-card-main>
                          <div class="row q-col-gutter-sm" style="padding-bottom: 5px;">
                            <div class="col-sm-10">
                              <q-select v-model="cliente_id" inverted-color="bg-light-green-10" float-label="Cliente" :options="clientesOptions" filter @input="getAdeudosCliente()"/>
                            </div>
                            <div class="col-sm-2">
                              <q-btn flat @click="verDetallesAdeudos()" color="light-green-10" icon="visibility">
                                <q-tooltip>Ver adeudos por proyecto</q-tooltip>
                              </q-btn>
                            </div>
                          </div>
                          <div class="row q-col-gutter-sm" style="padding-bottom: 10px;padding-top: 10px;">
                            <div class="col-sm-5">
                              N° proyectos: {{ this.adeudos.proyectos }}
                            </div>
                            <div class="col-sm-3">
                              2020
                            </div>
                            <div class="col-sm-2">
                              2021
                            </div>
                            <div class="col-sm-2 text-center">
                              Total
                            </div>
                          </div>
                          <div class="row q-col-gutter-sm">
                          </div>
                              <div class="row q-col-gutter-sm" style="padding-bottom: 5px;">
                                <div class="col-sm-3" style="padding-top: 5px;">
                                  Monto total:
                                </div>
                                <div class="col-sm-3 pull-right">
                                  <q-chip icon="fas fa-dollar-sign" color="light-green-10" text-color="white">
                                  {{ this.adeudos.contratos_anterior }}
                                  </q-chip>
                                </div>
                                <div class="col-sm-3 pull-right">
                                  <q-chip icon="fas fa-dollar-sign" color="light-green-10" text-color="white">
                                  {{ this.adeudos.contratos }}
                                  </q-chip>
                                </div>
                                <div class="col-sm-3 pull-right">
                                  <q-chip icon="fas fa-dollar-sign" color="light-green-10" text-color="white">
                                  {{ this.adeudos.contratosAll }}
                                  </q-chip>
                                </div>
                              </div>
                              <div class="row q-col-gutter-sm" style="padding-bottom: 5px;">
                                <div class="col-sm-3" style="padding-top: 5px;">
                                  Monto facturado:
                                </div>
                                <div class="col-sm-3 pull-right">
                                  <q-chip icon="fas fa-dollar-sign" color="light-green-10" text-color="white">
                                  {{ this.adeudos.monto_facturado_anterior }}
                                  </q-chip>
                                </div>
                                <div class="col-sm-3 pull-right">
                                  <q-chip icon="fas fa-dollar-sign" color="light-green-10" text-color="white">
                                  {{ this.adeudos.monto_facturado }}
                                  </q-chip>
                                </div>
                                <div class="col-sm-3 pull-right">
                                  <q-chip icon="fas fa-dollar-sign" color="light-green-10" text-color="white">
                                  {{ this.adeudos.monto_facturadoAll }}
                                  </q-chip>
                                </div>
                              </div>
                          <div class="row q-col-gutter-sm" style="padding-bottom: 5px;">
                            <div class="col-sm-3" style="padding-top: 5px;">
                              Monto cobrado:
                            </div>
                            <div class="col-sm-3 pull-right">
                              <q-chip icon="fas fa-dollar-sign" color="light-green-10" text-color="white">
                              {{ this.adeudos.abonado_anterior }}
                              </q-chip>
                            </div>
                            <div class="col-sm-3 pull-right">
                              <q-chip icon="fas fa-dollar-sign" color="light-green-10" text-color="white">
                              {{ this.adeudos.abonado }}
                              </q-chip>
                            </div>
                            <div class="col-sm-3 pull-right">
                              <q-chip icon="fas fa-dollar-sign" color="light-green-10" text-color="white">
                              {{ this.adeudos.abonadoAll }}
                              </q-chip>
                            </div>
                          </div>
                          <div class="row q-col-gutter-sm" style="padding-bottom: 5px;">
                            <div class="col-sm-3" style="padding-top: 5px;">
                              Monto adeudo:
                            </div>
                            <div class="col-sm-3 pull-right">
                              <q-chip icon="fas fa-dollar-sign" color="red-10" text-color="white">
                              {{ this.adeudos.restante_anterior }}
                              </q-chip>
                            </div>
                            <div class="col-sm-3 pull-right">
                              <q-chip icon="fas fa-dollar-sign" color="red-10" text-color="white">
                              {{ this.adeudos.restante }}
                              </q-chip>
                            </div>
                            <div class="col-sm-3 pull-right">
                              <q-chip icon="fas fa-dollar-sign" color="red-10" text-color="white">
                              {{ this.adeudos.restanteAll }}
                              </q-chip>
                            </div>
                          </div>
                          <div class="row q-col-gutter-sm" style="padding-bottom: 5px;">
                            <div class="col-sm-3" style="padding-top: 5px;">
                              Monto descontado (remisiones):
                            </div>
                            <div class="col-sm-3 pull-right">
                              <q-chip icon="fas fa-dollar-sign" color="lime-10" text-color="white">
                              {{ this.adeudos.monto_descontado_anterior }}
                              </q-chip>
                            </div>
                            <div class="col-sm-3 pull-right">
                              <q-chip icon="fas fa-dollar-sign" color="lime-10" text-color="white">
                              {{ this.adeudos.monto_descontado }}
                              </q-chip>
                            </div>
                            <div class="col-sm-3 pull-right">
                              <q-chip icon="fas fa-dollar-sign" color="lime-10" text-color="white">
                              {{ this.adeudos.descontadoAll }}
                              </q-chip>
                            </div>
                          </div>
                          <!-- <div class="row q-col-gutter-sm" style="padding-bottom: 5px;">
                            <div class="col-sm-12" style="padding-top: 5px;">
                              Facturas vencidas: {{ this.adeudos.facturas_vencidas }}
                            </div>
                          </div> -->
                        </q-card-main>
                      </q-card>
                    </div>
                    <div class="col-sm-4 q-pa-xs">
                      <q-card style="min-height: 400px;">
                        <q-card-title class="bg-blue text-white" style="text-align: center;">
                          <div class="row q-col-xs">
                            <div class="col-sm-12">
                              Facturación
                            </div>
                          </div>
                        </q-card-title>
                        <q-card-separator />
                        <q-card-main>
                          <div class="row q-col-gutter-sm" style="padding-bottom: 5px;">
                            <div class="col-sm-6">
                              <q-chip icon="fas fa-arrow-circle-right" color="amber" text-color="white">Emitidas:
                              {{ this.data_all_facturas.emitidas }}
                              </q-chip>
                            </div>
                            <div class="col-sm-6 pull-right">
                              <q-chip color="white" text-color="black">
                              Total: ${{ this.data_all_facturas.monto_emitidas }}
                              </q-chip>
                            </div>
                          </div>
                          <div class="row q-col-gutter-sm" style="padding-bottom: 5px;">
                            <div class="col-sm-6">
                              <q-chip icon="fas fa-check-circle" color="blue" text-color="white">Abonadas: {{ this.data_all_facturas.pagadas }}
                              </q-chip>
                            </div>
                            <div class="col-sm-6 pull-right">
                              <q-chip color="white" text-color="black">Total: ${{ this.data_all_facturas.monto_pagadas }}
                              </q-chip>
                            </div>
                          </div>
                          <div class="row q-col-gutter-sm" style="padding-bottom: 10px;">
                            <div class="col-sm-6">
                              <q-chip icon="done_all" color="green" text-color="white">Pagadas: {{ this.data_all_facturas.pagadas }}
                              </q-chip>
                            </div>
                            <div class="col-sm-6 pull-right">
                              <q-chip color="white" text-color="black">Total: ${{ this.data_all_facturas.monto_pagadas }}
                              </q-chip>
                            </div>
                          </div>
                          <q-card-separator/>
                          <div class="row q-col-gutter-sm" style="padding-top: 10px;padding-bottom: 10px;">
                            <div class="col-sm-12 text-center">
                              Monto total: ${{ this.data_all_facturas.total_monto_facturas }}
                            </div>
                          </div>
                          <q-card-separator/>
                          <div class="row q-col-gutter-sm" style="padding-top: 10px;">
                            <div class="col-sm-6">
                              Cobrado: ${{ this.data_all_facturas.total_cobrado }}
                            </div>
                            <div class="col-sm-6" style="padding-bottom: 10px;" v-if="this.data_all_facturas.porcentaje_cobrado > 100">
                              <div style="width: 90%; height: 25px; background-color: #a5d6a7; margin-right: 1px;">
                                <div v-bind:style="{ width: '100' + '%', height: '100%', background: 'green' }">
                                  <q-chip dense color="green" text-color="white">{{ this.data_all_facturas.porcentaje_cobrado }}%</q-chip>
                                </div>
                              </div>
                            </div>
                            <div class="col-sm-6" style="padding-bottom: 10px;" v-else>
                              <div style="width: 90%; height: 25px; background-color: #a5d6a7; margin-right: 1px;">
                                <div v-bind:style="{ width: this.data_all_facturas.porcentaje_cobrado + '%', height: '100%', background: 'green' }">
                                  <q-chip dense color="green" text-color="white">{{ this.data_all_facturas.porcentaje_cobrado }}%</q-chip>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="row q-col-gutter-sm">
                            <div class="col-sm-6">
                              Por cobrar: ${{ this.data_all_facturas.por_cobrar }}
                            </div>
                            <div class="col-sm-6" style="padding-bottom: 10px;" v-if="this.data_all_facturas.porcentaje_por_cobrar > 100">
                              <div style="width: 90%; height: 25px; background-color: #f8dd93;">
                                <div v-bind:style="{ width: '100' + '%', height: '100%', background: '#e65100' }">
                                  <q-chip dense color="amber" text-color="white">{{ this.data_all_facturas.porcentaje_por_cobrar }}%</q-chip>
                                </div>
                              </div>
                            </div>
                            <div class="col-sm-6" style="padding-bottom: 10px;" v-else>
                              <div style="width: 90%; height: 25px; background-color: #f8dd93;">
                                <div v-bind:style="{ width: this.data_all_facturas.porcentaje_por_cobrar + '%', height: '100%', background: '#e65100' }">
                                  <q-chip dense color="amber" text-color="white">{{ this.data_all_facturas.porcentaje_por_cobrar }}%</q-chip>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="row q-col-gutter-sm">
                            <div class="col-sm-6">
                              Descontado: ${{ this.data_all_facturas.ndc }}
                            </div>
                            <div class="col-sm-6" style="padding-bottom: 10px;">
                              <div style="width: 90%; height: 25px; background-color: #e6ee9c;">
                                <div v-bind:style="{ width: this.data_all_facturas.porcentaje_ndc + '%', height: '100%', background: '#827717' }">
                                  <q-chip dense color="lime" text-color="white">{{ this.data_all_facturas.porcentaje_ndc }}%</q-chip>
                                </div>
                              </div>
                            </div>
                          </div>
                        </q-card-main>
                      </q-card>
                    </div>
                    <div class="col-sm-4 q-pa-xs">
                      <q-card style="min-height: 400px;">
                        <q-card-title class="bg-red-10 text-white" style="text-align: center;">
                          <div class="row q-col-xs">
                            <div class="col-sm-12">
                              Morosidad
                            </div>
                          </div>
                        </q-card-title>
                        <q-card-separator />
                        <q-card-main>
                          <div class="row q-col-gutter-xs">
                            <div class="col-sm-12 text-center">
                              <q-btn-toggle color="red-10" v-model="days" toggle-color="primary" :options="selectDays" @input="filterByDays()"/>
                            </div>
                          </div>
                          <div class="row q-col-gutter-md" style="padding-top: 20px;padding-bottom: 5px;">
                            <div class="col-sm-6">
                              <q-chip icon="fas fa-arrow-circle-right" color="amber" text-color="white">Emitidas:
                              {{ this.data_facturas.emitido }}
                              </q-chip>
                            </div>
                            <div class="col-sm-6">
                              <q-chip color="white" text-color="black">
                              Total: ${{ this.data_facturas.monto_emitido }}
                              </q-chip>
                            </div>
                            <!-- <div class="col-sm-1">
                              <q-btn flat @click="seeRowContrato(props.row)" color="amber" icon="visibility">
                                <q-tooltip>Ver en cobranza</q-tooltip>
                              </q-btn>
                            </div> -->
                          </div>
                          <div class="row q-col-gutter-sm" style="padding-bottom: 5px;">
                            <div class="col-sm-6">
                              <q-chip icon="fas fa-check-circle" color="blue" text-color="white">Abonadas: {{ this.data_facturas.abonado }}
                              </q-chip>
                            </div>
                            <div class="col-sm-6">
                              <q-chip color="white" text-color="black">Total: ${{ this.data_facturas.monto_abonado }}
                              </q-chip>
                            </div>
                            <!-- <div class="col-sm-1">
                              <q-btn flat @click="seeRowContrato(props.row)" color="blue" icon="visibility">
                                <q-tooltip>Ver en cobranza</q-tooltip>
                              </q-btn>
                            </div> -->
                          </div>
                          <div class="row q-col-gutter-sm" style="padding-bottom: 5px;">
                            <div class="col-sm-6">
                              <q-chip icon="done_all" color="green" text-color="white">Pagadas: {{ this.data_facturas.pagado }}
                              </q-chip>
                            </div>
                            <div class="col-sm-6">
                              <q-chip color="white" text-color="black">Total: ${{ this.data_facturas.monto_pagado }}
                              </q-chip>
                            </div>
                            <!-- <div class="col-sm-1">
                              <q-btn flat @click="seeRowContrato(props.row)" color="green" icon="visibility">
                                <q-tooltip>Ver en cobranza</q-tooltip>
                              </q-btn>
                            </div> -->
                          </div>
                        </q-card-main>
                      </q-card>
                    </div>
                    <div :class="this.year !== '2021' ? 'col-sm-12 q-pa-xs' : 'col-sm-8 q-pa-xs'">
                      <q-card>
                        <q-card-title class="text-white bg-cyan-10" style="text-align: center;">
                          Cantidad abonada por meses: {{ this.year }}
                          <span slot="subtitle" style="text-decoration-color: #03ce94;">
                          </span>
                        </q-card-title>
                        <apexchart class="bg-white" type="line" height="350" :options="chartFacturas" :series="data_facturas"></apexchart>
                      </q-card>
                    </div>
                  </div>
                  <div class="row q-col-gutter-xs">
                    <div class="col-sm-6 q-pa-xs">
                      <q-card style="min-height: 400px;">
                        <q-card-title class="bg-orange-10 text-white" style="text-align: center;">
                          <div class="row q-col-xs">
                            <div class="col-sm-12">
                              Top 3 cobranza: Clientes
                            </div>
                          </div>
                        </q-card-title>
                        <q-card-separator />
                        <q-card-main>
                          <div class="row q-col-gutter-sm" style="padding-top: 10px;padding-bottom: 10px;">
                            <div class="col-sm-12 text-center">
                              Cobros anticipados
                            </div>
                          </div>
                          <q-card-separator/>
                          <div class="row q-col-gutter-sm" style="padding-top: 10px;" v-for="cli in top.mejores" :key="cli.cliente_id">
                            <div class="col-sm-8" style="padding-top: 5px;">
                              {{ cli.razon_social }}
                            </div>
                            <div class="col-sm-4 pull-right">
                              <q-chip icon="fas fa-calendar" color="orange-10" text-color="white">
                              {{ cli.promedio }}
                              </q-chip>
                            </div>
                          </div>
                          <q-card-separator />
                          <div class="row q-col-gutter-sm" style="padding-top: 10px;padding-bottom: 10px;">
                            <div class="col-sm-12 text-center">
                              Mayor cobranza
                            </div>
                          </div>
                          <q-card-separator />
                          <div class="row q-col-gutter-sm" style="padding-bottom: 5px;" v-for="cli in top.peores" :key="cli.cliente_id">
                            <div class="col-sm-8">
                              {{ cli.razon_social }}
                            </div>
                            <div class="col-sm-4 pull-right">
                              <q-chip icon="fas fa-calendar" color="orange-10" text-color="white">
                              {{ cli.promedio }}
                              </q-chip>
                            </div>
                          </div>
                        </q-card-main>
                      </q-card>
                    </div>
                    <div class="col-sm-6 q-pa-xs">
                      <q-card style="min-height: 400px;">
                        <q-card-title class="bg-light-blue-10 text-white" style="text-align: center;">
                          <div class="row q-col-xs">
                            <div class="col-sm-12">
                              Top 3 cobranza: Aliados
                            </div>
                          </div>
                        </q-card-title>
                        <q-card-separator />
                        <q-card-main>
                          <div class="row q-col-gutter-sm" style="padding-top: 10px;padding-bottom: 10px;">
                            <div class="col-sm-12 text-center">
                              Cobros anticipados
                            </div>
                          </div>
                          <q-card-separator />
                          <div class="row q-col-gutter-sm" style="padding-bottom: 10px;" v-for="ali in top.aliados_mejores" :key="ali.cliente_id">
                            <div class="col-sm-8" style="padding-top: 10px;">
                              {{ ali.aliado }}
                            </div>
                            <div class="col-sm-4 pull-right">
                              <q-chip icon="fas fa-calendar" color="blue-10" text-color="white">
                              {{ ali.promedio }}
                              </q-chip>
                            </div>
                          </div>
                          <q-card-separator />
                          <div class="row q-col-gutter-sm" style="padding-top: 10px;padding-bottom: 10px;">
                            <div class="col-sm-12 text-center">
                              Mayor cobranza
                            </div>
                          </div>
                          <q-card-separator />
                          <div class="row q-col-gutter-sm" style="padding-bottom: 5px;" v-for="ali in top.aliados_peores" :key="ali.cliente_id">
                            <div class="col-sm-8" style="padding-top: 10px;">
                              {{ ali.aliado }}
                            </div>
                            <div class="col-sm-4 pull-right">
                              <q-chip icon="fas fa-calendar" color="blue-10" text-color="white">
                              {{ ali.promedio }}
                              </q-chip>
                            </div>
                          </div>
                        </q-card-main>
                      </q-card>
                    </div>
                  </div>
                </div>
              </q-tab-pane>
              <q-tab-pane name="direccion">
                <dashboard-crm/>
              </q-tab-pane>
              <q-tab-pane name="operacion">
                <gastos/>
              </q-tab-pane>
              <q-tab-pane name="finanzas">
                <finanzas/>
              </q-tab-pane>
            </q-tabs>
          </div>
        </div>
      </div>

      <q-modal no-backdrop-dismiss v-if="modal_actividades" style="background-color: rgba(0,0,0,0.7);" v-model="modal_actividades" :content-css="{minWidth: '90vw', minHeight: '500px'}">
        <q-modal-layout>
          <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
            <div class="col-sm-9">
              <q-toolbar-title>
                Captura de avance de actividades
              </q-toolbar-title>
            </div>
            <div class="col-sm-3 pull-right">
              <q-btn
                flat
                round
                dense
                color="white"
                @click="cerrarModalActividades()"
                icon="fas fa-times-circle"
              />
            </div>
          </q-toolbar>
          <div class="row q-mt-lg" style="margin-left: 10px; margin-right: 10px;">
            <div class="col-sm-12" id="sticky-arbol">
              <div class="row q-mt-lg">
                <div class="col-sm-12">
                  <div class="q-table-container q-table-dense">
                    <div class="q-table-middle scroll">
                      <table class="q-table layout-padding">
                        <thead>
                          <tr style="text-align: center;">
                            <th>Acciones</th>
                            <th>Nombre</th>
                            <th>Avance</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Duración</th>
                            <th>Restante</th>
                            <th>Costo actual</th>
                            <th>Disponible</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="(item ,index)  in (arrayTreeObj)" :key="index" v-bind:class="[(item.id != selectedRowID.id) ? 'my-label':'text-green bg-light-green-11','']">
                            <td data-th="" style="text-align: center;">
                              <q-btn small flat @click="editActividad(item)" color="blue-6" icon="schedule" size="m" v-if="item.children.length === 0">
                                <q-tooltip>Editar</q-tooltip>
                              </q-btn>
                            </td>
                            <td data-th="Nombre" @click="toggle(item, index)" style="cursor: pointer;text-align:left;">
                              <span class="q-tree-link q-tree-label" v-bind:style="setPadding(item)">
                                <q-icon  style="cursor: pointer;font-size:15px;" :name="iconName(item)" :color="item.color"></q-icon>
                                {{item.indice}}.- {{item.nombre}}
                              </span>
                            </td>
                            <td data-th="Avance" style="text-align: center;">{{item.avance}}%</td>
                            <td data-th="Inicio" style="text-align: center;">{{item.inicio}} </td>
                            <td data-th="Fin" style="text-align: center;">{{item.fin}}</td>
                            <td data-th="Duración" style="text-align: center;">{{item.duracion}}</td>
                            <td data-th="Restante" style="text-align: center;">{{item.duracion_restante}}</td>
                            <td data-th="Costo" style="text-align: right;">${{item.costoCopia}}</td>
                            <td data-th="Fin Real" style="text-align: center;">${{item.cantidad_disponible}} </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </q-modal-layout>
      </q-modal>
    </div>

    <q-modal no-backdrop-dismiss v-if="modal_editar" style="background-color: rgba(0,0,0,0.7);" v-model="modal_editar" :content-css="{minWidth: '25vw', minHeight: '330px'}">
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
                @click="modal_editar = false"
                icon="fas fa-times-circle"
              />
            </div>
          </q-toolbar>
          <div class="row q-mt-lg" style="margin-top:20px;">
            <div class="col-sm-12">
              <q-field icon="icono_prueba" icon-color="dark" helper="Porcentaje de avance" :error="$v.form.avance.$error" error-label="Escriba el avance con valores entre 1-100">
                <q-knob
                  v-model="form.avance"
                  size="100px"
                  style="font-size: 1rem; margin-left:30%;margin-right: 20%;"
                  color="light-blue-10"
                  track-color="light-blue-12"
                  line-width="5px"
                  :min="0"
                  :max="100"
                  :step="1"
                >
                  {{ form.avance }} <q-icon class="q-ml-xs" name="fas fa-percent" />
                </q-knob>
                <q-input clearable align="right" :decimals="0" numeric-keyboard-toggle v-model="form.avance" type="number" suffix="%" style="font-size: 1rem; margin-right: 15%;"></q-input>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right:10px;">
            <div class="col-sm-3 offset-sm-9 pull-right">
              <q-btn @click="updateActividadDetalle()" class="btn_guardar" icon-right="save">Guardar</q-btn>
            </div>
          </div>
        </q-modal-layout>
    </q-modal>

    <q-modal no-backdrop-dismiss v-if="form_adeudos.modal_proyectos" style="background-color: rgba(0,0,0,0.7);" v-model="form_adeudos.modal_proyectos" :content-css="{width: '70vw', height: '800px'}">
      <q-modal-layout>
        <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
          <div class="col-sm-10">
            <q-toolbar-title>
              Adeudos por proyecto
            </q-toolbar-title>
          </div>
          <div class="col-sm-2 pull-right">
            <q-btn
              flat
              round
              dense
              color="white"
              @click="form_adeudos.modal_proyectos = false"
              icon="fas fa-times-circle"
            />
          </div>
        </q-toolbar>
      </q-modal-layout>
      <div class="row q-mt-sm" style="margin-top:70px;margin-left:20px;margin-right:20px;margin-bottom:20px;">
        <div class="col-sm-12">
          <q-search color="primary" v-model="form_adeudos.filter"/>
        </div>
        <div  class="col-sm-12 q-mt-sm" id="sticky-table-scroll" >
          <q-table id="sticky-table-newstyle"
            :data="form_adeudos.data"
            :columns="form_adeudos.columns"
            :selected.sync="form_adeudos.selected"
            :filter="form_adeudos.filter"
            color="positive"
            title=""
            :dense="true"
            :pagination.sync="form_adeudos.pagination"
            :loading="form_adeudos.loading"
            :rows-per-page-options="form_adeudos.rowsOptions">
            <template slot="body" slot-scope="props">
              <q-tr :props="props">
                <q-td key="codigo" :props="props">{{ props.row.codigo }}</q-td>
                <q-td key="cliente" :props="props">{{ props.row.cliente }}</q-td>
                <q-td key="programa" :props="props">{{ props.row.programa }}</q-td>
                <q-td key="subprograma" :props="props">{{ props.row.subprograma }}</q-td>
                <q-td key="proyecto" :props="props">{{ props.row.proyecto }}</q-td>
                <q-td key="monto_total" :props="props">${{ currencyFormat(props.row.monto_total) }}</q-td>
                <q-td key="monto_facturado" :props="props">${{ currencyFormat(props.row.monto_facturado) }}</q-td>
                <q-td key="monto_total_abonado" :props="props">${{ currencyFormat(props.row.monto_total_abonado) }}</q-td>
                <q-td key="monto_restante" :props="props">${{ currencyFormat(props.row.monto_restante) }}</q-td>
              </q-tr>
            </template>
          </q-table>
          <q-inner-loading :visible="form_adeudos.loading">
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
import { required, minValue } from 'vuelidate/lib/validators'
import ApexCharts from 'apexcharts'
import {VMoney} from 'v-money'
import axios from 'axios'
import VueApexCharts from 'vue-apexcharts'
import Vue from 'vue'
import DashboardCrm from '../components/layout/dashboard/DashboardCrm'
import Gastos from '../components/layout/dashboard/Gastos'
import Finanzas from '../components/layout/dashboard/Finanzas'
Vue.use(VueApexCharts)
Vue.component('apexchart', VueApexCharts)

export default {
  components: {
    DashboardCrm,
    Gastos,
    Finanzas
  },
  beforeRouteEnter (to, from, next) {
    next(vm => {})
  },
  data () {
    return {
      tipoOptions: [ { label: 'ADMINISTRATIVOS', value: 'ADMINISTRATIVOS' }, { label: 'OPERATIVOS', value: 'OPERATIVOS' }, { label: 'Todos', value: 0 } ],
      selectYear: [ { label: '' + (new Date().getFullYear() - 2), value: '' + (new Date().getFullYear() - 2) }, { label: '' + (new Date().getFullYear() - 1), value: '' + (new Date().getFullYear() - 1) }, { label: '' + (new Date().getFullYear()), value: '' + (new Date().getFullYear()) }, { label: '' + (new Date().getFullYear() + 1), value: '' + (new Date().getFullYear() + 1) } ],
      finalizadoOptions: [ { label: 'SI', value: 'true' }, { label: 'NO', value: 'false' } ],
      activoOptions: [ { label: 'ACTIVO', value: 'ACTIVO' }, { label: 'BLOQUEADO', value: 'BLOQUEADO' } ],
      year: '' + (new Date().getFullYear()),
      days: 'all',
      selectDays: [ { label: '30', value: '30' }, { label: '60', value: '60' }, { label: '90', value: '90' }, { label: 'Todas', value: 'all' } ],
      data_facturas: [],
      total_facturas: [],
      chartFacturas: [],
      cliente_id: 0,
      top: [],
      adeudos: ['proyectos: 0.00', 'contratos: 0.00', 'monto_facturado: 0.00', 'abonado: 0.00', 'restante: 0.00', 'facturas_vencidas: 0'],
      data_all_facturas: [],
      tab: 'licitaciones',
      principal: true,
      modal_actividades: false,
      modal_editar: false,
      actividades: [],
      actividad: [],
      selectedRowID: {},
      isExpanded: true,
      itemId: null,
      data_projects: [],
      eventos_proximos: [],
      municipiosOptions: [{label: '---Seleccione---', value: 0}],
      form: {
        avance: 0
      },
      series: [],
      labels: [],
      series2: [],
      labels2: [],
      series3: [0, 0],
      labels3: ['Cantidad disponible: 0', 'Cantidad comprometida: 0'],
      series4: [0, 0],
      labels4: ['Cantidad disponible: 0', 'Cantidad comprometida: 0'],
      proyectosOpt3: [],
      proyectosOpt4: [],
      recursosOpt3: [],
      recursosOpt4: [],
      actividadesOpt: [],
      proyecto_id: 0,
      recurso_id: 0,
      id_proyecto: 0,
      id_recurso: 0,
      actividad_id: 0,
      user_id: 0,
      role: '',
      loadingButton: false,
      usuariosOpt: [],
      money: {
        decimal: '.',
        thousands: ',',
        precision: 2,
        masked: false
      },
      views: {
        grid: true,
        update: false,
        update2: false
      },
      form_filtros: {
        fields: {
          lider_id: 0,
          empresa_id: 0,
          estado_id: 0,
          municipio_id: 0,
          tipo: 0,
          finalizado: 'false',
          activo: 'ACTIVO'
        }
      },
      form_adeudos: {
        modal_proyectos: false,
        // data: [],
        columns: [
          { name: 'codigo', label: 'Código', field: 'codigo', sortable: true, type: 'string', align: 'left' },
          { name: 'cliente', label: 'Cliente', field: 'cliente', sortable: true, type: 'string', align: 'left' },
          { name: 'programa', label: 'Programa', field: 'programa', sortable: true, type: 'string', align: 'left' },
          { name: 'subprograma', label: 'Subprograma', field: 'subprograma', sortable: true, type: 'string', align: 'left' },
          { name: 'proyecto', label: 'Proyecto', field: 'proyecto', sortable: true, type: 'string', align: 'left' },
          { name: 'monto_total', label: 'Contratos', field: 'monto_total', sortable: true, type: 'string', align: 'right' },
          { name: 'monto_facturado', label: 'Facturado', field: 'monto_facturado', sortable: true, type: 'string', align: 'right' },
          { name: 'monto_total_abonado', label: 'Abonado', field: 'monto_total_abonado', sortable: true, type: 'string', align: 'right' },
          { name: 'monto_restante', label: 'Restante', field: 'monto_restante', sortable: true, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        data: [],
        selected: [],
        filter: '',
        loading: false
      }
    }
  },
  directives: {money: VMoney},
  computed: {
    ...mapGetters({
      recursosOptions: 'vnt/recurso/selectOptions',
      empresasOptions: 'vnt/empresa/selectOptions',
      proyectos: 'pmo/proyecto/proyectos'
    }),
    estadosOptionsFilter () {
      let estados = this.$store.getters['vnt/estado/selectOptions']
      estados.push({label: '---Seleccione---', value: 0})
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
    empresasOptionsFilter () {
      let empresas = this.$store.getters['vnt/empresa/selectOptions']
      empresas.push({ 'label': 'Todos', 'value': 0 })
      return empresas
    },
    usuariosOptionsFilter () {
      let usuarios = this.$store.getters['sys/users/selectOptionsName']
      usuarios.push({ 'label': 'Todos', 'value': 0 })
      return usuarios
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
    arrayTreeObj () {
      let newObj = []
      this.recursive(this.actividades, newObj, 0, this.itemId, this.isExpanded)
      return newObj
    }
  },
  mounted () {
    this.todo()
  },
  watch: {
    async tab (newValue, old) {
      this.$q.loading.show('Cargando ...')
      if (newValue === 'operaciones') {
        await this.getEmpresas()
        await this.getUsuarios()
        await this.getEstados()
        await this.projects()
      }
      if (newValue === 'operacion') {
        // ya tiene componente
      }
      if (newValue === 'cobranza') {
        this.days = 'all'
        await this.getAllCobranza()
      }
      this.$q.loading.hide()
    }
  },
  methods: {
    ...mapActions({
      getUser: 'user/refresh',
      updateActividad: 'pmo/carga/update_avance',
      getActividadesByProyectoDashboard: 'pmo/carga/getByProyecto',
      getEmpresas: 'vnt/empresa/refresh',
      getRecursos: 'vnt/recurso/refresh',
      getUsuarios: 'sys/users/refresh',
      getEstados: 'vnt/estado/refresh',
      getMunicipiosByEstado: 'vnt/municipio/getByEstado',
      getClientes: 'ventas/clientes/refresh'
    }),
    async isAdmin () {
      await this.getUser().then(({data}) => {
        this.role = data.role[0]
        console.log(this.role)
        if (this.role.toUpperCase() === 'Lider'.toUpperCase() || this.role.toUpperCase() === 'Auxiliar'.toUpperCase()) {
          this.tab = 'operaciones'
        }
        if (this.role.toUpperCase() === 'Cobranza'.toUpperCase()) {
          this.tab = 'cobranza'
        }
        this.user_id = data.user.id
      }).catch(error => {
        console.log(error)
      })
    },
    async todo () {
      await this.isAdmin()
      await this.eventos()
    },
    async projects () {
      if (this.role === 'Finanzas/Comisiones') {
        await axios.get('/graficas/getDataProjects/' + this.user_id + '/Finanzas/' + this.form_filtros.fields.lider_id + '/' + this.form_filtros.fields.empresa_id + '/' + this.form_filtros.fields.estado_id + '/' + this.form_filtros.fields.municipio_id + '/' + this.year + '/' + this.form_filtros.fields.tipo + '/' + this.form_filtros.fields.finalizado + '/' + this.form_filtros.fields.activo).then(({data}) => {
          this.data_projects = data.projects
        }).catch(error => {
          console.error(error)
        })
      } else {
        await axios.get('/graficas/getDataProjects/' + this.user_id + '/' + this.role + '/' + this.form_filtros.fields.lider_id + '/' + this.form_filtros.fields.empresa_id + '/' + this.form_filtros.fields.estado_id + '/' + this.form_filtros.fields.municipio_id + '/' + this.year + '/' + this.form_filtros.fields.tipo + '/' + this.form_filtros.fields.finalizado + '/' + this.form_filtros.fields.activo).then(({data}) => {
          this.data_projects = data.projects
        }).catch(error => {
          console.error(error)
        })
      }
    },
    ver_solicitudes (project) {
      this.$router.push({ path: `/ejecucion_presupuestos/${project.id}/filtrar` })
    },
    crear_solicitudes (project) {
      this.$router.push({ path: `/ejecucion_presupuestos/${project.id}/crear` })
    },
    async capturar_avance (project) {
      this.actividades = []
      this.id_proyecto = project.id
      this.$q.loading.show({message: 'Cargando...'})
      await this.getActividadesByProyectoDashboard({proyecto_id: project.id}).then(({data}) => {
        if (data.actividades.length > 0) {
          this.actividades = data.actividades
          this.modal_actividades = true
        }
      }).catch(error => {
        console.error(error)
      })
      this.$q.loading.hide()
    },
    async updateActividadDetalle () {
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        let params = []
        params.id = this.actividad.id
        params.avance = this.form.avance
        this.updateActividad(params).then(({data}) => {
          this.$showMessage(data.message.title, data.message.content)
          this.modal_editar = false
          this.actividades = []
          this.$q.loading.show({message: 'Cargando...'})
          this.getActividadesByProyectoDashboard({proyecto_id: this.id_proyecto}).then(({data}) => {
            if (data.actividades.length > 0) {
              this.actividades = data.actividades
              this.modal_actividades = true
            }
          }).catch(error => {
            console.error(error)
          })
          this.$q.loading.hide()
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('Error', 'Por favor revise el campo avance.')
      }
    },
    editActividad (act) {
      this.$v.form.$reset()
      this.actividad = act
      this.form.avance = act.avance
      this.modal_editar = true
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
      if (item.expend === true && item.children && item.children.length > 0) {
        return 'fas fa-minus-circle'
      }

      if (item.expend === false && item.children && item.children.length > 0) {
        return 'fas fa-plus-circle'
      }

      return 'fas fa-check'
    },
    toggle (item, index) {
      let vm = this
      vm.itemId = item.id
      // show  sub items after click on + (more)
      if (item.expend === false && item.children !== undefined) {
        if (item.children.length !== 0) {
          vm.recursive(item.children, [], item.level + 1, item.id, true)
        }
      }

      if (item.expend === true && item.children !== undefined) {
        item.children.forEach(function (o) {
          o.expend = false
        })

        // this.item.expend = undefined
        vm.$set(item, 'expend', false)
        vm.itemId = null
      }
    },
    setPadding (item) {
      return `padding-left: ${item.level * 10}px;`
    },
    async eventos () {
      await axios.get('/graficas/getEventos').then(({data}) => {
        this.eventos_proximos = data.eventos
      }).catch(error => {
        console.error(error)
      })
    },
    async cerrarModalActividades () {
      this.modal_actividades = false
      await this.projects()
    },
    setView (view) {
      this.views.grid = false
      this.views.update = false
      this.views.update2 = false
      this.views[view] = true
    },
    async cargaMunicipios () {
      this.municipiosOptions = []
      this.form_filtros.fields.municipio_id = 0
      await this.getMunicipiosByEstado({estado_id: this.form_filtros.fields.estado_id}).then(({data}) => {
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
    borrar () {
      this.form_filtros.fields.lider_id = 0
      this.form_filtros.fields.empresa_id = 0
      this.form_filtros.fields.estado_id = 0
      this.form_filtros.fields.municipio_id = 0
      this.form_filtros.fields.tipo = 0
      this.form_filtros.fields.finalizado = 'false'
      this.form_filtros.fields.activo = 'ACTIVO'
    },
    async filterByDays () {
      await axios.get(`/graficas/getFacturasByDays/${this.days}/${this.year}`).then(({data}) => {
        this.data_facturas = data.facturas_vencidas
      }).catch(error => {
        console.error(error)
      })
    },
    async getAllFacturas () {
      await axios.get('/graficas/getFacturas/' + this.year).then(({data}) => {
        this.data_all_facturas = data.facturas_vencidas
      }).catch(error => {
        console.error(error)
      })
    },
    // this.data_facturas
    // this.total_facturas
    async grafica2 () {
      let options = {
        chart: {
          height: '350px',
          type: 'area',
          toolbar: {
            show: true
          }
        },
        series: [{
          name: 'Cantidad abonada' + ' ' + this.year,
          data: this.data_facturas,
          type: 'column'
        },
        {
          name: 'Cantidad facturada' + ' ' + this.year,
          data: this.total_facturas,
          type: 'line'
        } /* ,
        {
          name: this.$t('mantenimientos') + ' ' + new Date().getFullYear(),
          data: this.data3,
          type: 'column'
        } */ ],
        dataLabels: {
          enabled: true,
          formatter: function (val) {
            // return val
            return '$' + Number.parseFloat(val).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
          },
          offsetY: -20,
          style: {
            fontSize: '12px',
            colors: ['#000000']
          }
        },
        plotOptions: {
          bar: {
            dataLabels: {
              position: 'top' // top, center, bottom
            }
          }
        },
        xaxis: {
          categories: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
          position: 'top',
          labels: {
            offsetY: -18
          },
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false
          },
          crosshairs: {
            fill: {
              type: 'gradient',
              gradient: {
                colorFrom: '#D8E3F0',
                colorTo: '#BED1E6',
                stops: [0, 100],
                opacityFrom: 0.4,
                opacityTo: 0.5
              }
            }
          },
          tooltip: {
            enabled: true,
            offsetY: -35

          }
        },
        yaxis: {
          axisBorder: {
            show: false
          },
          axisTicks: {
            show: false
          },
          labels: {
            show: false,
            formatter: function (val) {
              // return this.currencyFormat(val)
              return '$' + Number.parseFloat(val).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
            }
          }

        },
        colors: ['#4caf50', '#f44336', '#FF9800']
      }
      var chart = new ApexCharts(document.querySelector('#chart2'), options)
      await chart.render()
    },
    async getDataGrafica () {
      await axios.get('/graficas/getGraficaDataFactura/' + this.year).then(({data}) => {
        this.data_facturas = [{ name: 'Facturado', type: 'area', data: data.facturas_totales }, { name: 'Cobrado', type: 'column', data: data.facturas_recaudadas }]
      }).catch(error => {
        console.error(error)
      })
      this.chartFacturas = {
        colors: ['#ffc107', '#4caf50', '#d1c4e9', '#e91e63', '#2196f3', '#4caf50'],
        labels: ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'],
        chart: {
          height: 350,
          type: 'line',
          stacked: true,
          fontFamily: 'Nunito',
          toolbar: {
            show: true,
            tools: {
              download: true,
              selection: false,
              pan: false,
              zoom: false,
              zoomin: false,
              zoomout: false,
              reset: false
            }
          }
        },
        stroke: {
          curve: ['straight', 'straight', 'straight', 'straight'],
          width: [4, 4, 4, 4]
        },
        dataLabels: {
          enabled: true,
          enabledOnSeries: [0, 1],
          formatter: function (val) {
            return Number.parseFloat(val).toFixed(1).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
          }
        },
        xaxis: {
          categories: [],
          labels: {
            type: 'String',
            show: true
          }
        },
        yaxis: {
          max: function (max) {
            return max * 1
          },
          title: {
            text: 'Miles de pesos'
          },
          labels: {
            type: 'String',
            show: true,
            formatter: function (val) {
              return Number.parseFloat(val).toFixed(1).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
            }
          }
        },
        series: this.data_facturas,
        grid: {
          borderColor: '#c6c2cF',
          row: {
            colors: ['#f3f3f3', 'transparent'],
            opacity: 0.8
          }
        }
      }
    },
    async getAdeudosCliente () {
      await axios.get('/graficas/getAdeudosByCliente/' + this.cliente_id + '/' + this.year).then(({data}) => {
        this.adeudos = data.adeudos
      }).catch(error => {
        console.error(error)
      })
    },
    async verDetallesAdeudos () {
      // hola
      if (this.cliente_id > 0) {
        await axios.get('/graficas/getProyectosByCliente/' + this.cliente_id).then(({data}) => {
          this.form_adeudos.data = data.adeudos
          this.form_adeudos.modal_proyectos = true
        }).catch(error => {
          console.error(error)
        })
      }
    },
    async getTopClientes () {
      await axios.get('/graficas/getTopClientes/' + this.year).then(({data}) => {
        this.top = data.top
      }).catch(error => {
        console.error(error)
      })
    },
    async getAllCobranza () {
      this.days = 'all'
      this.$q.loading.show('Cargando ...')
      await this.getAdeudosCliente()
      await this.getAllFacturas()
      await this.getDataGrafica()
      await this.getClientes()
      await this.getTopClientes()
      await this.filterByDays()
      this.$q.loading.hide()
    }
  },
  validations: {
    form: {
      avance: {required, minValue: minValue(1)}
    }
  }
}
</script>

<style scoped>
</style>
