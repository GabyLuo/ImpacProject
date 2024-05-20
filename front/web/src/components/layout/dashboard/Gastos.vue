<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6 pull-right" style="margin-top: 10px;">
            <q-btn-toggle color="teal" v-model="year" toggle-color="primary" :options="selectYear" @input="filtrar()"/>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn v-if="perfil_colaborador==='si'" @click="newSolicitud(0)" class="btn_nuevo" icon="add">
                Nueva Solicitud
              </q-btn>
              <q-btn @click="newProveedor()" class="btn_nuevo" icon="add">
                Nuevo Proveedor
              </q-btn>
            </div>
          </div>
        </div>
      </div>

        <div class="row bg-white">
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs"> <!-- AQUI EMPIEZAN LOS FILTROS -->
              <div class="col-sm-3">
                <q-field icon="work" icon-color="dark">
                  <q-select v-model="form.fields.proyecto_id" inverted color="dark" float-label="Project" :options="proyectosFiltro" filter @input="cargarActandUsers()"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="folder" icon-color="dark">
                  <q-select v-model="form.fields.actividad_id" inverted color="dark" float-label="Actividad" :options="actividadesFiltro" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="person" icon-color="dark">
                  <q-select v-model="form.fields.responsable_id" inverted color="dark" float-label="Asignado a:" :options="usuariosOpt" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="forward" icon-color="dark">
                  <q-select v-model="form.fields.status" inverted color="dark" float-label="Estatus" :options="form.selectStatus" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fa-check" icon-color="dark">
                  <q-select v-model="form.fields.comprobado" inverted color="dark" float-label="Comprobado" :options="form.selectComprobada" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="search" icon-color="dark">
                  <q-input v-model="form_solicitudes.filter" type="text" inverted color="dark" float-label="Buscar" maxlenght=1000></q-input>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="person" icon-color="dark">
                  <q-select v-model="form.fields.proveedor_id" inverted color="dark" float-label="Proveedor" :options="proveedoresOptions2" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="person" icon-color="dark">
                  <q-select v-model="form.fields.creador_id" inverted color="dark" float-label="Creador" :options="usuariosOptions" filter/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="person" icon-color="dark">
                  <q-select v-model="form.fields.factura" inverted color="dark" float-label="Factura" :options="form.selectFactura" filter/>
                </q-field>
              </div>
              <div class="col-sm-9 pull-right">
                <q-btn @click="filtrar()" inverted color="orange" icon-right="fas fa-filter" :loading="loadingButton">Filtrar</q-btn>
              </div>
            </div>
          </div>
        </div>

        <div class="row bg-white">
          <div class="col q-pa-md">
            <div class="col q-pa-md">
              <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                <q-table id="sticky-table-newstyle"
                  :data="solicitudes"
                  :columns="form_solicitudes.columns"
                  :selected.sync="form_solicitudes.selected"
                  :filter="form_solicitudes.filter"
                  color="positive"
                  title=""
                  :pagination.sync="form_solicitudes.pagination"
                  :loading="form_solicitudes.loading"
                  :rows-per-page-options="form_solicitudes.rowsOptions"
                  @request="qTableRequest"
                  >
                  <template slot="body" slot-scope="props">
                    <q-tr :props="props">
                      <q-td key="id" :props="props" @click.native="clickSolicitud(props.row)" style="cursor: pointer;">{{ props.row.id }}</q-td>
                      <q-td key="nombre_proyecto" :props="props">{{ props.row.nombre_proyecto }}</q-td>
                      <q-td key="creador_nombre" :props="props">{{ props.row.creador_nombre }}</q-td>
                      <q-td key="fecha_creado_validacion" :props="props">{{ props.row.fecha_creado_validacion }}</q-td>
                      <q-td key="fecha_solicitado_validacion" :props="props">{{ props.row.fecha_solicitado_validacion }}</q-td>
                      <q-td key="fecha_ejercido" :props="props">{{ props.row.fecha_ejercido }}</q-td>
                      <q-td key="columna_total" :props="props">${{ props.row.columna_total }}</q-td>
                      <q-td key="responsable_solicitud" :props="props">{{ props.row.responsable_solicitud }}</q-td>
                      <q-td key="status" :props="props"><q-chip dense :icon="props.row.icon" :color="props.row.color" text-color="white">{{ props.row.status }}</q-chip></q-td>
                      <q-td key="comprobado" :props="props"><q-field :icon="props.row.icon_comprobado" :icon-color="props.row.color_comprobado"></q-field></q-td>
                      <q-td key="factura" :props="props"><q-field :icon="props.row.icon_factura" :icon-color="props.row.color_factura"></q-field></q-td>
                      <q-td key="color_clasificado" :props="props"><q-field :icon="props.row.icon_clasificado" :icon-color="props.row.color_clasificado"></q-field></q-td>
                      <q-td key="acciones" :props="props">
                      <q-btn small flat @click="verInformacion(props.row)" color="blue" icon="edit">
                          <q-tooltip>Ver detalles</q-tooltip>
                      </q-btn>
                      </q-td>
                    </q-tr>
                  </template>
                </q-table>
                <q-inner-loading :visible="form_solicitudes.loading">
                  <q-spinner-dots size="64px" color="primary" />
                </q-inner-loading>
              </div>
            </div>
          </div> <!-- AQUI ACABAN LOS FILTROS -->
        </div>

    </div>

          <!-- AQUI EMPIEZA EL MODAL DE CREAR NUEVA SOLICITUD -->
      <q-modal no-backdrop-dismiss v-if="form_solicitudes.solicitudes_proyecto" style="background-color: rgba(0,0,0,0.7);" v-model="form_solicitudes.solicitudes_proyecto" :content-css="{width: '50vw', height: '400px'}">
        <q-modal-layout>
          <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
            <div class="col-sm-10">
              <q-toolbar-title>
                Nueva solicitud
              </q-toolbar-title>
            </div>
            <div class="col-sm-2 pull-right">
              <q-btn
                flat
                round
                dense
                color="white"
                @click="form_solicitudes.solicitudes_proyecto = false"
                icon="fas fa-times-circle"
              />
            </div>
          </q-toolbar>
        </q-modal-layout>
        <div class="row q-mt-lg" style="margin-top:70px;margin-right:20px;">
          <div class="col-sm-12">
              <q-field icon="work" icon-color="dark">
                <q-select v-model="form_solicitudes.fields.proyecto_id" inverted color="dark" float-label="Presupuesto" :options="proyectosOpt" filter @input="cargarActandUsers2()"/>
              </q-field>
            </div>
            <div class="col-sm-12" icon-color="dark">
              <q-field icon="fas fa-building" icon-color="dark">
                <q-select v-model="form_solicitudes.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter @input="actualizarEmpresa()"/>
              </q-field>
            </div>
            <div class="col-sm-12">
              <q-field icon="business" icon-color="dark">
                <q-select v-model="form_solicitudes.fields.proveedor_id" inverted color="dark" float-label="Proveedor" :options="proveedoresOptions" filter @input="getProveedor()"/>
              </q-field>
            </div>
            <div class="row q-mt-lg subtitulos" style="margin-left:40px;" v-if="form_solicitudes.fields.proveedor_id > 0">
              <div class="col-sm-12" v-if="form_solicitudes.fields.cuentas[0].banco!== null && form_solicitudes.fields.cuentas[0].clabe!== null">
              <q-checkbox v-model="banco1" :label="form_solicitudes.fields.cuentas[0].banco + ' - ' + form_solicitudes.fields.cuentas[0].clabe" color="amber"  @click.native="clickCuenta1()"/>
              </div>
            </div>
            <div class="row q-mt-lg subtitulos" style="margin-left:40px;" v-if="form_solicitudes.fields.proveedor_id > 0">
              <div class="col-sm-12" v-if="form_solicitudes.fields.cuentas[0].banco2!== null && form_solicitudes.fields.cuentas[0].clabe2!== null">
              <q-checkbox v-model="banco2" :label="form_solicitudes.fields.cuentas[0].banco2 + ' - ' + form_solicitudes.fields.cuentas[0].clabe2" color="amber" @click.native="clickCuenta2()"/>
              </div>
            </div>
            <div class="row q-mt-lg subtitulos" style="margin-left:40px;" v-if="form_solicitudes.fields.proveedor_id > 0">
              <div class="col-sm-12" v-if="form_solicitudes.fields.cuentas[0].toka!== null">
              <q-checkbox v-model="toka" :label="'TOKA ' + '- ' + form_solicitudes.fields.cuentas[0].toka" color="amber"  @click.native="clickCuenta3()"/>
              </div>
            </div>
        </div>
        <div class="row q-mt-lg" style="margin-right:20px;">
          <div class="col-sm-6 pull-left" style="padding-left: 40px;">
            <q-btn @click="see_details()" color="red" icon="fas fa-eye" :loading="loadingButton" v-if="ver_detalles_proveedor===false && form_solicitudes.fields.proveedor_id>0"></q-btn>
          </div>
          <div class="col-sm-6 pull-right">
            <q-btn @click="createSolicitud()" class="btn_guardar" icon-right="fas fa-save" :loading="loadingButton">Crear</q-btn>
          </div>
        </div>
        <q-card v-if="ver_detalles_proveedor" style="margin-top:10px;margin-left: 20px;margin-right: 20px;">
          <q-card-main>
            <q-btn @click="ver_detalles_proveedor=false" color="negative" icon="visibility_off">
              <q-tooltip>Ocultar información</q-tooltip>
            </q-btn>
            <div class="row q-mt-lg subtitulos" style="margin-left:15px;">
                Proveedor y datos bancarios
            </div>
            <div class="row q-mt-lg">
              <div class="col-sm-6">
                <q-field icon="fas fa-building" icon-color="dark">
                  <q-input readonly upper-case v-model="form_solicitudes.fields.cuentas[0].razon_social" inverted-color="dark" float-label="Razón social"/>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="fas fa-id-card" icon-color="dark">
                  <q-input readonly upper-case v-model="form_solicitudes.fields.cuentas[0].rfc" inverted-color="dark" float-label="RFC"/>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="account_balance" icon-color="dark">
                  <q-input readonly upper-case v-model="form_solicitudes.fields.cuentas[0].banco" inverted-color="dark" float-label="Banco"/>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="vpn_key" icon-color="dark">
                  <q-input readonly upper-case v-model="form_solicitudes.fields.cuentas[0].clabe" inverted-color="dark" float-label="CLABE"/>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="account_balance" icon-color="dark">
                  <q-input readonly upper-case v-model="form_solicitudes.fields.cuentas[0].banco2" inverted-color="dark" float-label="Banco 2"/>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="vpn_key" icon-color="dark">
                  <q-input readonly upper-case v-model="form_solicitudes.fields.cuentas[0].clabe2" inverted-color="dark" float-label="CLABE 2"/>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="vpn_key" icon-color="dark">
                  <q-input readonly upper-case v-model="form_solicitudes.fields.cuentas[0].toka" inverted-color="dark" float-label="toka"/>
                </q-field>
              </div>
            </div>
          </q-card-main>
        </q-card>
      </q-modal> <!-- AQUI TERMINA EL MODAL DE CREAR UNA NUEVA SOLICITUD -->

      <!-- VISTA para rellenar datos de la NUEVA SOLICITUD -->
      <div v-if="views.update">
        <div class="q-pa-sm panel-header">
          <div class="row">
            <div class="col-sm-6">
              <div class="q-pa-sm q-gutter-sm">
                <q-breadcrumbs align="left">
                  <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                  <q-breadcrumbs-el label="Solicitudes" to="" @click.native="regresar()"/>
                  <q-breadcrumbs-el label="Solicitud"/><label style="padding-top: 10px;">: #{{form_solicitudes.fields.id}} </label>
                </q-breadcrumbs>
              </div>
            </div>
            <div class="col-sm-6 pull-right">
              <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
                <q-btn style="margin-right: 10px" v-if="((form_solicitudes.fields.autorizacion_id===null && form_solicitudes.fields.colaborador && form_solicitudes.fields.status==='RECHAZADO'))" @click="cancelSolicitud()" class="btn_cancelar" icon="highlight_off">Cancelar</q-btn>
                <q-btn v-if="((this.form_solicitudes.fields.autorizador && (this.form_solicitudes.fields.autorizacion_id===this.form_solicitudes.fields.orden) && this.user_id!==29) || (this.form_solicitudes.fields.pagos && this.form_solicitudes.fields.autorizacion_id===0 && this.form_gastos.numero_gastos_pagados===0 && this.user_id!==39))" style="margin-right: 10px" @click="form_solicitudes.solicitudes_proyecto2 = true" class="btn_rechazar" icon="clear">Rechazar</q-btn>
                <q-btn style="margin-right: 10px" @click="solicitar()" class="btn_solicitar" icon="done" v-if="form_solicitudes.fields.solicitante && form_solicitudes.fields.autorizacion_id===null && this.form_solicitudes.fields.status==='RECHAZADO'">Solicitar</q-btn>
                <!-- <q-btn style="margin-right: 10px" v-if="((form_solicitudes.fields.autorizacion_id===null && form_solicitudes.fields.colaborador))" @click="actualizarSolicitud()" class="btn_regresar" icon="save">Guardar</q-btn> -->
                <q-btn v-if="this.form_solicitudes.fields.autorizador && this.form_solicitudes.fields.autorizacion_id===this.form_solicitudes.fields.orden" style="margin-right: 10px" @click="autorizar()" class="btn_regresar" icon="done">Autorizar</q-btn>
                <q-btn v-if="((form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id===0) || (form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id===-1))" style="margin-right: 10px" @click="pagoParcialSolicitud()" class="btn_pagar" icon="done">Pago Parcial</q-btn>
                <q-btn @click="regresar()" class="btn_regresar" icon="fa-arrow-left" />
              </div>
            </div>
          </div>
        </div>

        <div class="q-pa-md bg-grey-3">
          <div class="row bg-white border-panel">
            <div class="col q-pa-md col-sm-12">
              <div class="row q-col-gutter-xs">
                <div class="col-sm-4" v-if="((form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id===0) || (form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id===-1)) || (form_solicitudes.fields.colaborador && form_solicitudes.fields.autorizacion_id===1)">
                  <q-field icon="fas fa-building" icon-color="dark">
                    <q-select v-model="form_solicitudes.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter @input="actualizarEmpresa()"/>
                  </q-field>
                </div>
                <div class="col-sm-4" v-else>
                  <q-field icon="business" icon-color="dark">
                    <q-select readonly v-model="form_solicitudes.fields.empresa_id" inverted color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                  </q-field>
                </div>
                <div class="col-sm-4" v-if="((form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id===0) || (form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id===-1)) || (form_solicitudes.fields.colaborador && form_solicitudes.fields.autorizacion_id===1)">
                  <q-field icon="fas fa-building" icon-color="dark">
                    <q-select v-model="form_solicitudes.fields.subempresa_id" inverted color="dark" float-label="Subempresa" :options="form_solicitudes.subempresasOptions" filter @input="actualizarEmpresa()"/>
                  </q-field>
                </div>
                <div class="col-sm-4" v-else>
                  <q-field icon="business" icon-color="dark">
                    <q-select readonly v-model="form_solicitudes.fields.subempresa_id" inverted color="dark" float-label="Subempresa" :options="form_solicitudes.subempresasOptions" filter/>
                  </q-field>
                </div>
                <div class="col-sm-4" v-if="((form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id===0) || (form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id===-1))">
                  <q-field icon="date_range" icon-color="dark">
                    <q-datetime v-model="form_solicitudes.fields.fecha_ejercido" type="date" inverted color="dark" float-label="Fecha pago" align="center" @input="actualizarSolicitud()"></q-datetime>
                  </q-field>
                </div>
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-4">
                  <q-field icon="work" icon-color="dark" >
                    <q-select readonly v-model="form_solicitudes.fields.proyecto_id" inverted color="dark" float-label="Presupuesto" :options="proyectosFiltro" filter @input="cargarActandUsers2()"/>
                  </q-field>
                </div>
                <div class="col-sm-2" style="margin-left:40px;">
                  <!-- <q-checkbox v-model="incluir_iva" label="CON IVA" color="amber" @click.native="clickIva()"/> -->
                  <q-checkbox v-model="incluir_iva" label="CON IVA" color="amber" v-if="form_solicitudes.fields.colaborador && (form_solicitudes.fields.autorizacion_id===null || form_solicitudes.fields.autorizacion_id===1) && form_solicitudes.fields.iva==='SI'" @click.native="clickIva()"/>
                  <q-checkbox v-model="incluir_iva" label="SIN IVA" color="amber" v-if="form_solicitudes.fields.colaborador && (form_solicitudes.fields.autorizacion_id===null || form_solicitudes.fields.autorizacion_id===1) && form_solicitudes.fields.iva==='NO'" @click.native="clickIva()"/>
                  <q-checkbox readonly v-model="incluir_iva" label="CON IVA" color="amber" v-if="(!form_solicitudes.fields.colaborador && form_solicitudes.fields.iva === 'SI') || (form_solicitudes.fields.colaborador && form_solicitudes.fields.autorizacion_id!==null && form_solicitudes.fields.autorizacion_id!==1 && form_solicitudes.fields.iva === 'SI')"/>
                  <q-checkbox readonly v-model="incluir_iva" label="SIN IVA" color="amber" v-if="(!form_solicitudes.fields.colaborador && form_solicitudes.fields.iva === 'NO') || (form_solicitudes.fields.colaborador && form_solicitudes.fields.autorizacion_id!==null && form_solicitudes.fields.autorizacion_id!==1 && form_solicitudes.fields.iva === 'NO')"/>
                </div>
                <div class="col-sm-2">
                  <q-checkbox v-model="comprobar_solicitud" label="Comprobada" color="amber" v-if="form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id<=0 && (form_solicitudes.fields.status==='POR PAGAR' || form_solicitudes.fields.status==='PAGO PARCIAL' || form_solicitudes.fields.status==='PAGADO') && (form_solicitudes.fields.empresa_id > 0) && (form_gastos.numero_gastos === form_gastos.numero_gastos_comprobados)" @click.native="clickComprobado()"/>
                  <q-checkbox readonly v-model="comprobar_solicitud" label="Comprobada" color="amber" v-if="form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id<=0 && (form_solicitudes.fields.status==='POR PAGAR' || form_solicitudes.fields.status==='PAGO PARCIAL' || form_solicitudes.fields.status==='PAGADO') && form_solicitudes.fields.empresa_id > 0 && (form_solicitudes.fields.empresa_id > 0) && (form_gastos.numero_gastos !== form_gastos.numero_gastos_comprobados)" @click.native="clickErrorComprobado()"/>
                  <q-checkbox readonly v-model="comprobar_solicitud" label="Comprobada" color="amber" v-if="form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id<=0 && (form_solicitudes.fields.status==='POR PAGAR' || form_solicitudes.fields.status==='PAGO PARCIAL' || form_solicitudes.fields.status==='PAGADO') && form_solicitudes.fields.empresa_id === 0" @click.native="clickErrorEmpresa()"/>
                  <q-checkbox readonly v-model="comprobar_solicitud" label="Comprobada" color="amber" v-if="!form_solicitudes.fields.pagos || (form_solicitudes.fields.pagos && (form_solicitudes.fields.autorizacion_id === null || form_solicitudes.fields.autorizacion_id > 0 || form_solicitudes.fields.autorizacion_id === -2))"/>
                </div>
                <div class="col-sm-2">
                  <q-checkbox v-model="factura_solicitud" label="CON FACTURA" color="amber" v-if="form_solicitudes.fields.factura==='SI'" @click.native="clickFactura()"/>
                  <q-checkbox v-model="factura_solicitud" label="SIN FACTURA" color="amber" v-if="form_solicitudes.fields.factura==='NO'" @click.native="clickFactura()"/>
                </div>
              </div> <!-- AGREGAR NUEVO GASTO -->
              <div class="row q-col-gutter-xs" v-if="((form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id===0) || (form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id===-1))">
                <!-- <div class="col-sm-4">
                  <q-field icon="folder" icon-color="dark">
                    <q-select v-model="form_solicitudes.fields.concepto_id" inverted color="dark" float-label="Concepto" :options="conceptosOptions" filter @input="getSubconceptosByconcepto()"/>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="folder_open" icon-color="dark">
                    <q-select v-model="form_solicitudes.fields.subconcepto_id" inverted color="dark" float-label="Subconcepto" :options="subconceptosOpt" filter @input="guardarClasificacion()"/>
                  </q-field>
                </div> -->
              </div>
              <div class="row q-col-gutter-xs" v-if="(form_solicitudes.fields.status==='CREADO' || form_solicitudes.fields.status==='RECHAZADO' || form_solicitudes.fields.status==='POR AUTORIZAR') && form_solicitudes.fields.colaborador && (form_solicitudes.fields.autorizacion_id===null || form_solicitudes.fields.autorizacion_id===1)">
                <div class="col-sm-4">
                  <q-field icon="folder" icon-color="dark" :error="$v.form_gastos.fields.actividad_id.$error" error-label="Elija una actividad">
                    <q-select v-model="form_gastos.fields.actividad_id" inverted color="dark" float-label="Actividad" :options="actividadesOpt" filter @input="presupuestoActividad()"/>
                  </q-field>
                </div>
                <div class="col-sm-2">
                  <q-field icon="fas fa-dollar-sign" icon-color="dark">
                    <q-input readonly v-model="form_gastos.fields.presupuesto_real" type="text" inverted color="dark" float-label="Presupuesto actual" suffix="MXN"></q-input>
                  </q-field>
                </div>
                <div class="col-sm-2">
                  <q-field icon="fas fa-dollar-sign" icon-color="dark">
                    <q-input readonly v-model="form_gastos.fields.presupuesto_disponible" type="text" inverted color="dark" float-label="Presupuesto disponible" suffix="MXN"></q-input>
                  </q-field>
                </div>
                <div class="col-sm-2">
                  <q-field icon="fas fa-dollar-sign" icon-color="dark">
                    <q-input readonly v-model="form_gastos.fields.presupuesto_disponible_real" type="text" inverted color="dark" float-label="Total solicitado" suffix="MXN"></q-input>
                  </q-field>
                </div>
                <div class="col-sm-2">
                  <q-field icon="business" icon-color="dark"  :error="$v.form_gastos.fields.tipo.$error" error-label="Elija una categoría">
                    <q-select v-model="form_gastos.fields.tipo" inverted color="dark" float-label="Tipo de gasto" :options="gastosOptions" filter/>
                  </q-field>
                </div>
              </div>
              <div class="row q-col-gutter-xs" v-if="(form_solicitudes.fields.status==='CREADO' || form_solicitudes.fields.status==='RECHAZADO' || form_solicitudes.fields.status === 'POR AUTORIZAR') && form_solicitudes.fields.colaborador && (form_solicitudes.fields.autorizacion_id===null || form_solicitudes.fields.autorizacion_id===1)">
                <div class="col-sm-4">
                  <q-field icon="business" icon-color="dark" :error="$v.form_gastos.fields.proveedor_id.$error" error-label="Elija un proveedor">
                    <q-select v-model="form_gastos.fields.proveedor_id" inverted color="dark" float-label="Proveedor" :options="proveedoresOptions" filter/>
                  </q-field>
                </div>
                <div class="col-sm-2">
                  <div class="icono_field">
                    <q-field  icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form_gastos.fields.monto.$error" error-label="Ingrese el monto ($1.00 - $1,000,000,000.00)">
                      <q-input v-model="form_gastos.fields.montovalidar" inverted color="dark" float-label="Monto a pagar" suffix="MXN"></q-input>
                    </q-field>
                  </div>
                </div>
                <div class="col-sm-6">
                  <q-field icon="insert_comment" icon-color="dark">
                    <q-input upper-case v-model="form_gastos.fields.descripcion_gasto" type="text" inverted color="dark" float-label="Descripción gasto" maxlenght=1000></q-input>
                  </q-field>
                </div>
                <div class="col-sm-2 offset-sm-10 pull-right">
                  <q-btn @click="validarGastos()" class="btn_guardar" icon-right="fas fa-dollar-sign" :loading="loadingButton" v-if="!form_gastos.modificar && form_solicitudes.fields.colaborador && (form_solicitudes.fields.autorizacion_id===null || form_solicitudes.fields.autorizacion_id===1)">Agregar gasto</q-btn>
                  <q-btn @click="validar_actualizarGastos()" style="background-color: orange;" icon-right="fas fa-dollar-sign" :loading="loadingButton" v-if="form_gastos.modificar && ((form_solicitudes.fields.colaborador && (form_solicitudes.fields.autorizacion_id===null || form_solicitudes.fields.autorizacion_id===1)) || (form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id===0))">Actualizar gasto</q-btn>
                </div>
              </div>
          <div class="row q-mt-sm subtitulos" style="padding-top: 15px;">
            GASTOS DE LA SOLICITUD
          </div>

          <div class="row q-mt-sm">
            <div class="col-sm-12">
              <div class="row" style="width:60vw;">
                <q-search hide-underline color="secondary" v-model="form_gastos.filter" />
              </div>
              <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                <q-table id="sticky-table-newstyle"
                  :data="form_gastos.data"
                  :columns="(form_solicitudes.fields.pagos === true && (form_solicitudes.fields.status==='POR PAGAR' || form_solicitudes.fields.status==='PAGO PARCIAL' || form_solicitudes.fields.status==='PAGADO')) ? form_gastos.columnsPagos : form_gastos.columns"
                  :selected.sync="form_gastos.selected"
                  :filter="form_gastos.filter"
                  color="positive"
                  title=""
                  :pagination.sync="form_gastos.pagination"
                  :loading="form_gastos.loading"
                  :rows-per-page-options="form_gastos.rowsOptions"
                  >
                  <q-tr slot="bottom-row" slot-scope="props">
                    <q-td colspan="100%" style="text-align: right;">
                      <strong>Total: ${{ form_solicitudes.fields.totalcopia }} MXN</strong>
                    </q-td>
                  </q-tr>
                  <template slot="body" slot-scope="props">
                    <q-tr :props="props" style="cursor: pointer;">
                      <q-td key="indice_gasto" :props="props">{{ props.row.indice_gasto }}</q-td>
                      <q-td @click.native="clickFila(props.row)" key="nombre_actividad" :props="props">{{ props.row.nombre_actividad }}
                        <q-tooltip>{{ props.row.tipo_gasto }}</q-tooltip></q-td>
                        <!-- <q-td @click.native="clickFila(props.row)" key="tipo_gasto" :props="props">{{ props.row.tipo_gasto }}
                        <q-tooltip>{{ props.row.tipo_gasto }}</q-tooltip>
                        </q-td> -->
                      <q-td key="cotizacion" :props="props">
                        <q-uploader color="primary" extensions=".jpg, .jpeg, .png, .xml, .docx, .pdf, .PNG, .JPG, .JPEG, .XML, .DOCX, .PDF, .txt, .TXT" url="" @add="cotizacion_id(props.row)"
                          :url-factory="uploadCotizacion_id"/>
                        <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="cotizacionInput" accept=".txt, .TXT, .png, .jpg, .jpeg, .xml, .docx, .pdf, .PNG, .JPG, .JPEG, .XML, .DOCX, .PDF" @change="uploadCotizacionFile()" />
                        <q-btn small flat @click="selectedCotizacionDocumento(props.row)" color="green-9" :loading="loadingButton">
                          <q-icon name="cloud_upload" />&nbsp;
                          <q-tooltip>Subir cotización</q-tooltip>
                        </q-btn>
                        <div style="display: inline;" v-for="cot in props.row.cotizacion" :key="cot.id">
                          <q-btn small flat :icon="cot.icon" :color="cot.color">
                            <q-tooltip>{{ cot.name }}</q-tooltip>
                            <q-popover>
                              <q-list link separator class="scroll" style="min-width: 100px">
                                <q-item v-close-overlay @click.native="verCotizacion(cot)" v-if="cot.doc_type !== 'docx' && cot.doc_type !== 'xml' && cot.doc_type !== 'xlsx' && cot.doc_type !== 'XLSX' && cot.doc_type !== 'DOCX' && cot.doc_type !== 'txt'">
                                  <q-item-main label="Ver"/>
                                </q-item>
                                <q-item v-close-overlay @click.native="descargarCotizacion(cot.id, cot.doc_type)">
                                  <q-item-main label="Descargar"/>
                                </q-item>
                                <q-item v-close-overlay @click.native="cotizacionAccion(cot)">
                                  <q-item-main label="Eliminar"/>
                                </q-item>
                              </q-list>
                            </q-popover>
                          </q-btn>
                        </div>
                      </q-td>
                      <q-td key="factura" :props="props">
                        <!-- <q-field icon="fas fa-images" icon-color="dark"> -->
                          <q-uploader color="primary" extensions=".jpg, .jpeg, .png, .xml, .docx, .pdf, .PNG, .JPG, .JPEG, .XML, .DOCX, .PDF, .txt, .TXT" url="" @add="factura_id(props.row)"
                          :url-factory="uploadFactura_id"/>
                        <!-- </q-field> -->
                        <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="facturaInput" accept=".txt, .TXT, .png, .jpg, .jpeg, .xml, .docx, .pdf, .PNG, .JPG, .JPEG, .XML, .DOCX, .PDF" @change="uploadFacturaFile()" />
                        <q-btn small flat @click="selectedFacturaDocumento(props.row)" color="green-9" :loading="loadingButton">
                          <q-icon name="cloud_upload" />&nbsp;
                          <q-tooltip>Subir factura</q-tooltip>
                        </q-btn>
                        <div style="display: inline;" v-for="fac in props.row.factura" :key="fac.id">
                          <q-btn small flat :icon="fac.icon" :color="fac.color">
                            <q-tooltip>{{ fac.name }}</q-tooltip>
                            <q-popover>
                              <q-list link separator class="scroll" style="min-width: 100px">
                                <q-item v-close-overlay @click.native="verFactura(fac)" v-if="fac.doc_type !== 'docx' && fac.doc_type !== 'xml' && fac.doc_type !== 'XML' && fac.doc_type !== 'xlsx' && fac.doc_type !== 'txt'">
                                  <q-item-main label="Ver"/>
                                </q-item>
                                <q-item v-close-overlay @click.native="descargarFactura(fac.id, fac.doc_type)">
                                  <q-item-main label="Descargar"/>
                                </q-item>
                                <q-item v-close-overlay @click.native="facturaAccion(fac)">
                                  <q-item-main label="Eliminar"/>
                                </q-item>
                              </q-list>
                            </q-popover>
                          </q-btn>
                        </div>
                      </q-td>
                      <q-td key="comprobante" :props="props">
                        <!-- <q-field icon="fas fa-images" icon-color="dark"> -->
                          <q-uploader color="primary" extensions=".jpg, .jpeg, .png, .xml, .docx, .pdf, .PNG, .JPG, .JPEG, .XML, .DOCX, .PDF, .txt, .TXT" url="" @add="comprobante_id(props.row)"
                          :url-factory="uploadComprobante_id" v-if="form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id<=0 && (form_solicitudes.fields.status==='POR PAGAR' || form_solicitudes.fields.status==='PAGO PARCIAL' || form_solicitudes.fields.status==='PAGADO') && (form_solicitudes.fields.empresa_id > 0)"/>
                        <!-- </q-field> -->
                        <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="comprobanteInput" accept=".png, .jpg, .jpeg, .xml, .docx, .pdf, .00, .*, .txt, .TXT" @change="uploadComprobanteFile()" />
                        <q-btn small flat @click="selectedComprobanteDocumento(props.row)" color="green-9" :loading="loadingButton" v-if="form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id<=0 && (form_solicitudes.fields.status==='POR PAGAR' || form_solicitudes.fields.status==='PAGO PARCIAL' || form_solicitudes.fields.status==='PAGADO') && (form_solicitudes.fields.empresa_id > 0)">
                          <q-icon name="cloud_upload" />&nbsp;
                          <q-tooltip>Subir recibo de pago</q-tooltip>
                        </q-btn>
                        <div style="display: inline;" v-for="com in props.row.comprobante" :key="com.id">
                          <q-btn small flat :icon="com.icon" :color="com.color">
                            <q-tooltip>{{ com.name }}</q-tooltip>
                            <q-popover>
                              <q-list link separator class="scroll" style="min-width: 100px">
                                <q-item v-close-overlay @click.native="verComprobante(com)" v-if="com.doc_type !== 'docx' && com.doc_type !== 'xml' && com.doc_type !== 'xlsx' && com.doc_type !== 'txt'">
                                  <q-item-main label="Ver"/>
                                </q-item>
                                <q-item v-close-overlay @click.native="descargarComprobante(com.id, com.doc_type)">
                                  <q-item-main label="Descargar"/>
                                </q-item>
                                <q-item v-close-overlay @click.native="comprobanteAccion(com)" v-if="form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id<=0 && (form_solicitudes.fields.status==='POR PAGAR' || form_solicitudes.fields.status==='PAGO PARCIAL' || form_solicitudes.fields.status==='PAGADO') && (form_solicitudes.fields.empresa_id > 0)">
                                  <q-item-main label="Eliminar"/>
                                </q-item>
                              </q-list>
                            </q-popover>
                          </q-btn>
                        </div>
                      </q-td>
                      <q-td key="comprobado" :props="props">
                        <q-checkbox v-model="props.row.comprobado" @click.native="comprobarGasto(props.row)" color="teal" v-if="form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id<=0 && form_solicitudes.fields.empresa_id > 0"/>
                        <q-checkbox v-model="props.row.comprobado" @click.native="clickErrorEmpresa()" color="teal" v-if="form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id<=0 && form_solicitudes.fields.empresa_id === 0"/>
                        <q-checkbox readonly v-model="props.row.comprobado" color="teal" v-if="!form_solicitudes.fields.pagos || (form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id>0)"/>
                      </q-td>
                      <q-td key="sumatoria_factura" :props="props">${{ props.row.sumatoria_factura }}</q-td>
                      <!-- <q-td key="montocopia" :props="props">${{ props.row.montocopia }}</q-td>
                      <q-td key="iva" :props="props">${{ props.row.iva }}</q-td> -->
                      <q-td key="total" :props="props">${{ props.row.total }}</q-td>
                      <q-td key="concepto" :props="props" v-if="form_solicitudes.fields.pagos === true">{{ props.row.concepto }}</q-td>
                      <q-td key="subconcepto" :props="props" v-if="form_solicitudes.fields.pagos === true">{{ props.row.subconcepto }}</q-td>
                      <q-td key="acciones" :props="props">
                        <q-btn small flat @click="editSingleGasto(props.row)" color="blue" icon="edit" v-if="(form_solicitudes.fields.status==='CREADO' || form_solicitudes.fields.status==='RECHAZADO' || form_solicitudes.fields.status==='POR AUTORIZAR') && form_solicitudes.fields.colaborador && (form_solicitudes.fields.autorizacion_id===null || form_solicitudes.fields.autorizacion_id===1)">
                          <q-tooltip>Editar</q-tooltip>
                        </q-btn>
                        <q-btn small flat @click="pagarSingleGasto(props.row)" color="amber" icon="fas fa-dollar-sign" v-if="((form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id===0) || (form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id===-1)) && props.row.fecha_ejercido===null && form_solicitudes.fields.empresa_id > 0">
                        </q-btn>
                        <q-btn small flat @click="modalConcepto(props.row)" color="green-10" icon="build" v-if="(form_solicitudes.fields.pagos && (form_solicitudes.fields.autorizacion_id===0 || form_solicitudes.fields.autorizacion_id===-1))">
                        </q-btn>
                        <q-btn small flat @click="clickErrorEmpresa()" color="amber" icon="fas fa-dollar-sign" v-if="((form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id===0) || (form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id===-1)) && props.row.fecha_ejercido===null && form_solicitudes.fields.empresa_id === 0">
                          <q-tooltip>Pagar</q-tooltip>
                        </q-btn>
                        <!-- <q-btn small flat @click="capturarClasificacion(props.row)" color="cyan-10" icon="note" v-if="((form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id===0) || (form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id===-1)) && form_solicitudes.fields.empresa_id > 0">
                          <q-tooltip>Capturar clasificación y objeto del gasto</q-tooltip>
                        </q-btn> -->
                        <q-btn disable small flat color="purple" icon="done_all" v-if="props.row.fecha_ejercido!==null">
                          <q-tooltip>Gasto pagado</q-tooltip>
                        </q-btn>
                        <q-btn small flat @click="deleteSingleGasto(props.row)" color="negative" icon="delete" v-if="(form_solicitudes.fields.status==='CREADO' || form_solicitudes.fields.status==='RECHAZADO' || form_solicitudes.fields.status==='POR AUTORIZAR') && form_solicitudes.fields.colaborador && (form_solicitudes.fields.autorizacion_id===null || form_solicitudes.fields.autorizacion_id===1)">
                          <q-tooltip>Eliminar</q-tooltip>
                        </q-btn>
                      </q-td>
                    </q-tr>
                  </template>
                </q-table>
                <q-inner-loading :visible="form_gastos.loading">
                  <q-spinner-dots size="64px" color="primary" />
                </q-inner-loading>
              </div>
            </div>
          </div>
          <!-- AQUI EMPIEZA EL MODAL DE VER EL GASTO DE LA SOLICITUD -->
          <q-card v-if="form_gastos.modal_informacion" style="margin-top:15px;">
            <q-card-main>
              <q-btn @click="form_gastos.modal_informacion=false" color="negative" icon="visibility_off">
                <q-tooltip>Ocultar información</q-tooltip>
              </q-btn>
              <div class="row q-mt-lg subtitulos" style="margin-left:15px;">
                  Datos generales del gasto
              </div>
              <div class="row q-mt-lg">
                <div class="col-sm-3">
                  <q-field icon="folder" icon-color="dark">
                    <q-input readonly upper-case v-model="form_gastos.detalles.nombre_actividad" type="text" inverted-color="dark" float-label="Actividad"></q-input>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="business" icon-color="dark">
                    <q-input readonly upper-case v-model="form_gastos.detalles.tipo_gasto" type="text" inverted-color="dark" float-label="Tipo de gasto"></q-input>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="date_range" icon-color="dark">
                     <q-datetime readonly v-model="form_gastos.detalles.created" type="date" inverted-color="dark" float-label="Fecha" align="center"></q-datetime>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-checkbox readonly v-model="form_gastos.detalles.comprobado" label="Comprobar gasto" color="amber"/>
                </div>
                <div class="col-sm-12">
                  <q-field icon="label" icon-color="dark">
                    <q-input readonly upper-case v-model="form_gastos.detalles.descripcion_gasto" type="text" inverted-color="dark" float-label="Descripción"></q-input>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-dollar-sign" icon-color="dark">
                    <q-input readonly upper-case v-model="form_gastos.detalles.costo" type="text" inverted-color="dark" float-label="Presupuesto actual"></q-input>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-dollar-sign" icon-color="dark">
                    <q-input readonly upper-case v-model="form_gastos.detalles.cantidad_disponible" type="text" inverted-color="dark" float-label="Presupuesto disponible"></q-input>
                  </q-field>
                </div>
                <div class="col-sm-6">
                  <q-field icon="fas fa-dollar-sign" icon-color="dark">
                    <q-input readonly upper-case v-model="form_gastos.detalles.monto_total_pagar" type="text" inverted-color="dark" float-label="Monto total a pagar"></q-input>
                  </q-field>
                </div>
              </div>
              <div class="row q-mt-lg subtitulos" style="margin-left:15px;">
                  Proveedor y datos bancarios
              </div>
              <div class="row q-mt-lg">
                <div class="col-sm-9">
                  <q-field icon="fas fa-building" icon-color="dark">
                    <q-input readonly upper-case v-model="form_gastos.detalles.razon_social" inverted-color="dark" float-label="Razón social"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-id-card" icon-color="dark">
                    <q-input readonly upper-case v-model="form_gastos.detalles.rfc" inverted-color="dark" float-label="RFC"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="account_balance" icon-color="dark">
                    <q-input readonly upper-case v-model="form_gastos.detalles.banco" inverted-color="dark" float-label="Banco"/>
                  </q-field>
                </div>
                <div class="col-sm-3" v-if="form_solicitudes.fields.banco1 === 'SI'">
                  <q-field icon="vpn_key" icon-color="dark">
                    <q-input readonly upper-case v-model="form_gastos.detalles.clabe" inverted color="teal" float-label="CLABE"/>
                  </q-field>
                </div>
                <div class="col-sm-3" v-if="form_solicitudes.fields.banco1 === 'NO'">
                  <q-field icon="vpn_key" icon-color="dark">
                    <q-input readonly upper-case v-model="form_gastos.detalles.clabe" inverted-color="dark" float-label="CLABE"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="account_balance" icon-color="dark">
                    <q-input readonly upper-case v-model="form_gastos.detalles.banco2" inverted-color="dark" float-label="Banco 2"/>
                  </q-field>
                </div>
                <div class="col-sm-3" v-if="form_solicitudes.fields.banco2 === 'SI'">
                  <q-field icon="vpn_key" icon-color="dark">
                    <q-input readonly upper-case v-model="form_gastos.detalles.clabe2" inverted color="teal" float-label="CLABE 2"/>
                  </q-field>
                </div>
                <div class="col-sm-3" v-if="form_solicitudes.fields.banco2 === 'NO'">
                  <q-field icon="vpn_key" icon-color="dark">
                    <q-input readonly upper-case v-model="form_gastos.detalles.clabe2" inverted-color="dark" float-label="CLABE 2"/>
                  </q-field>
                </div>
                <div class="col-sm-3" v-if="form_solicitudes.fields.toka === 'SI'">
                  <q-field icon="vpn_key" icon-color="dark">
                    <q-input readonly upper-case v-model="form_gastos.detalles.toka" inverted color="teal" float-label="toka"/>
                  </q-field>
                </div>
                <div class="col-sm-3" v-if="form_solicitudes.fields.toka === 'NO'">
                  <q-field icon="vpn_key" icon-color="dark">
                    <q-input readonly upper-case v-model="form_gastos.detalles.toka" inverted-color="dark" float-label="toka"/>
                  </q-field>
                </div>
              </div>
            </q-card-main>
          </q-card>
          <!-- </q-modal> --> <!-- AQUI TERMINA EL MODAL DE VER EL GASTO DE UNA SOLICITUD -->
          <!-- AGREGAR COMENTARIOS POR SOLICITUD -->
          <div class="row q-col-gutter-xs" style="padding-top: 15px;">
            <!-- <div class="col-sm-12"> -->
              <q-collapsible class="col-sm-12" @click="cargarComentarios()" v-model="open_comentarios" style="border-bottom: 1px solid teal;" icon="insert_comment" label="Comentarios de la solicitud">
                <!-- aqui va el contenido -->
                <div class="row q-mt-lg">
                  <div class="col-sm-3">
                    <q-field icon="person" icon-color="dark">
                      <q-select v-model="form_comentarios.fields.usuario_destino" inverted color="dark" float-label="Comentario para:" :options="usuariosOpt" filter/>
                    </q-field>
                  </div>
                  <div class="col-sm-9">
                    <q-field icon="insert_comment" icon-color="dark" :error="$v.form_comentarios.fields.descripcion.$error" error-label="Escriba un comentario">
                      <q-input upper-case v-model="form_comentarios.fields.descripcion" type="text" inverted color="dark" float-label="Escriba un comentario o descripción de la solicitud" maxlenght=1000></q-input>
                    </q-field>
                  </div>
                  <div class="col-sm-2 offset-sm-10 pull-right">
                    <q-btn @click="agregarComentario()" class="btn_guardar" icon-right="insert_comment" :loading="loadingButton">Agregar comentario</q-btn>
                  </div>
                </div>
                <div class="row q-mt-lg">
                  <q-list highlight inset-separator class="col-sm-12" v-for="msje in form_solicitudes.mensajes" :key="msje.id">
                    <q-item multiline>
                      <q-item-side>
                        <q-item-tile avatar>
                          <img src="statics/usuario_avatar.png">
                        </q-item-tile>
                      </q-item-side>
                      <q-item-main>
                        <q-item-tile label>{{ msje.autor }}</q-item-tile>
                        <q-item-tile sublabel lines="4">{{ msje.mensaje }}</q-item-tile>
                      </q-item-main>
                      <q-item-side right>
                        <q-item-tile stamp>Fecha: {{ msje.fecha_mensaje }}</q-item-tile>
                      </q-item-side>
                    </q-item>
                  </q-list>
                </div>
              </q-collapsible> <!-- AQUI TERMINA LO DE AGREGAR COMENTARIOS -->
              <!-- </div> -->
              <!-- AGREGAR ARCHIVOS POR SOLICITUD -->
              <q-collapsible class="col-sm-12" @click="cargarArchivos()" v-model="open_archivos" style="border-bottom: 1px solid teal;" icon="note" label="Archivos de la solicitud">
                <!-- aqui va el contenido -->
                <div class="col-sm-3 pull-right">
                  <input style="display:none" inverted color="dark" stack-label="Archivo" @change="uploadFormato()" type="file" name="" value="" ref="fileInputFormato" accept=".jpg,.jpeg,.png,.pdf,.docx, .PNG, .JPG, .JPEG, .XML, .DOCX, .PDF" />
                    <q-btn @click="$refs.fileInputFormato.click()" color="tertiary" icon="attach_file" :loading="loadingButton">&nbsp; Cargar Archivo</q-btn>
                </div>
                <div class="row q-mt-sm" style="margin-top:30px; margin-right:30px;">
                  <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                    <q-table id="sticky-table-newstyle"
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
                            <q-btn small flat @click="descargarFormato(props.row)" color="blue-8" icon="cloud_download">
                              <q-tooltip>Descargar</q-tooltip>
                            </q-btn>
                            <q-btn small flat @click="deleteFormato(props.row)" color="negative" icon="delete">
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
              </q-collapsible> <!-- AQUI TERMINA LO DE AGREGAR ARCHIVOS -->
              <!-- NOTAS DE LA SOLICITUD -->
              <q-collapsible class="col-sm-12" @click="cargarNotas()" v-model="open_notas" style="border-bottom: 1px solid teal;" icon="note" label="Notas">
                <div class="row q-mt-lg">
                  <q-list highlight inset-separator class="col-sm-12" v-for="msje in form_gastos.notas" :key="msje.id">
                    <q-item multiline>
                      <q-item-side>
                        <q-item-tile avatar>
                          <img src="statics/usuario_avatar.png">
                        </q-item-tile>
                      </q-item-side>
                      <q-item-main>
                        <q-item-tile label>{{ msje.perfil }} : {{ msje.nickname }}</q-item-tile>
                        <q-item-tile sublabel lines="4">{{ msje.nota }}</q-item-tile>
                      </q-item-main>
                      <q-item-side right>
                        <q-item-tile stamp>Fecha: {{ msje.fecha }}</q-item-tile>
                      </q-item-side>
                    </q-item>
                  </q-list>
                </div>
              </q-collapsible>
          </div>
          <!-- AQUI EMPIEZA EL MODAL DE RECHAZAR LA SOLICITUD -->
          <q-modal no-backdrop-dismiss v-if="form_solicitudes.solicitudes_proyecto2" style="background-color: rgba(0,0,0,0.7);" v-model="form_solicitudes.solicitudes_proyecto2" :content-css="{width: '50vw', height: '230px'}">
            <q-modal-layout>
              <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
                <div class="col-sm-10">
                  <q-toolbar-title>
                    Rechazar solicitud
                  </q-toolbar-title>
                </div>
                <div class="col-sm-2 pull-right">
                  <q-btn
                    flat
                    round
                    dense
                    color="white"
                    @click="form_solicitudes.solicitudes_proyecto2 = false"
                    icon="fas fa-times-circle"
                  />
                </div>
              </q-toolbar>
            </q-modal-layout>
            <div class="row q-mt-lg" style="margin-top:70px;margin-right:20px;">
              <div class="col-sm-12">
                <q-field icon="insert_comment" icon-color="dark" :error="$v.form_rechazar.fields.descripcion.$error" error-label="Escriba un comentario de porque se rechaza la solicitud">
                  <q-input upper-case v-model="form_rechazar.fields.descripcion" type="text" inverted color="dark" float-label="Escriba un comentario o descripción de porque se rechaza la solicitud" maxlenght=1000></q-input>
                </q-field>
              </div>
            </div>
            <div class="row q-mt-lg" style="margin-right:20px;">
              <div class="col-sm-4 offset-sm-8 pull-right">
                <q-btn @click="rechazar()" class="btn_rechazar" icon="clear" :loading="loadingButton">Rechazar</q-btn>
              </div>
            </div>
          </q-modal> <!-- AQUI TERMINA EL MODAL DE RECHAZAR UNA SOLICITUD -->
          <!-- AQUI EMPIEZA EL MODAL DE PAGAR UN GASTO -->
          <q-modal no-backdrop-dismiss v-if="form_pagar.modal_pagar" style="background-color: rgba(0,0,0,0.7);" v-model="form_pagar.modal_pagar" :content-css="{width: '40vw', height: '350px'}">
            <q-modal-layout>
              <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
                <div class="col-sm-10">
                  <q-toolbar-title>
                    Pagar Gasto
                  </q-toolbar-title>
                </div>
                <div class="col-sm-2 pull-right">
                  <q-btn
                    flat
                    round
                    dense
                    color="white"
                    @click="form_pagar.modal_pagar = false"
                    icon="fas fa-times-circle"
                  />
                </div>
              </q-toolbar>
            </q-modal-layout>
            <div class="row q-mt-lg" style="margin-top:70px;margin-right:20px;" v-if="form_solicitudes.fields.subempresa_id > 0">
              <div class="col-sm-8">
                <q-field  icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form_pagar.fields.monto.$error" error-label="Ingrese el monto ($0.00 - $1,000,000,000.00)">
                  <q-input v-model.lazy="form_pagar.fields.monto" v-money="money" inverted color="dark" float-label="Monto a pagar" suffix="MXN"></q-input>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field  icon="fas fa-donate" icon-color="dark">
                  <q-input align="right" :decimals="2" numeric-keyboard-toggle v-model="form_pagar.fields.comision" type="number" inverted color="dark" float-label="Comisión" suffix="%"></q-input>
                </q-field>
              </div>
            </div>
            <div class="row q-mt-lg" style="margin-top:70px;margin-right:20px;" v-else>
              <div class="col-sm-12">
                <q-field  icon="fas fa-dollar-sign" icon-color="dark" :error="$v.form_pagar.fields.monto.$error" error-label="Ingrese el monto ($0.00 - $1,000,000,000.00)">
                  <q-input v-model.lazy="form_pagar.fields.monto" v-money="money" inverted color="dark" float-label="Monto a pagar" suffix="MXN"></q-input>
                </q-field>
              </div>
            </div>
            <div class="row q-mt-lg" v-if="form_gastos.notas.length > 0 && form_solicitudes.fields.sobrepasa_presupuesto === 'SI'">
              <q-list highlight inset-separator class="col-sm-12" v-for="msje in form_gastos.notas" :key="msje.id">
                <q-item multiline>
                  <q-item-side>
                    <q-item-tile avatar>
                      <img src="statics/usuario_avatar.png">
                    </q-item-tile>
                  </q-item-side>
                <q-item-main>
                  <q-item-tile label>{{ msje.perfil }} : {{ msje.nickname }}</q-item-tile>
                  <q-item-tile sublabel lines="4">{{ msje.nota }}</q-item-tile>
                </q-item-main>
                  <q-item-side right>
                    <q-item-tile stamp>Fecha: {{ msje.fecha }}</q-item-tile>
                  </q-item-side>
                </q-item>
              </q-list>
            </div>
            <div class="row q-mt-lg" style="margin-right:20px;" v-if="form_solicitudes.fields.sobrepasa_presupuesto === 'SI'">
              <q-label style="margin-left: 20px;margin-bottom: 10px;" v-if="form_gastos.notas.length===0">
                ---No existen notas---
              </q-label>
              <!-- <q-label style="margin-left: 20px;margin-bottom:10px;">
                El gasto de esta solicitud sobrepasa el monto disponible de la actividad, por favor escriba una nota para justificar el pago.
              </q-label>
              <div class="col-sm-12">
                <q-field icon="label" icon-color="dark">
                  <q-input upper-case v-model="form_notas.fields.nota" type="textarea" inverted color="dark" float-label="Escriba una nota para justificar el pago" maxlength="1000" />
                </q-field>
              </div> -->
            </div>
            <div class="row q-mt-lg" style="margin-right:20px;">
              <div class="col-sm-4 offset-sm-8 pull-right">
                <q-btn @click="pagarGasto()" class="btn_guardar" icon="done_all" :loading="loadingButton">Pagar Gasto</q-btn>
              </div>
            </div>
          </q-modal> <!-- AQUI TERMINA EL MODAL DE RECHAZAR UNA SOLICITUD -->
          <q-modal no-backdrop-dismiss v-if="form_solicitudes.modal_notas" style="background-color: rgba(0,0,0,0.7);" v-model="form_solicitudes.modal_notas" :content-css="{width: '50vw', height: '350px'}">
            <q-modal-layout>
              <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
                <div class="col-sm-10">
                  <q-toolbar-title>
                    Nota
                  </q-toolbar-title>
                </div>
                <div class="col-sm-2 pull-right">
                  <q-btn
                    flat
                    round
                    dense
                    color="white"
                    @click="form_solicitudes.modal_notas = false"
                    icon="fas fa-times-circle"
                  />
                </div>
              </q-toolbar>
            </q-modal-layout>
            <div class="row q-mt-lg" style="margin-top:70px;margin-right:20px;">
              <div class="row q-mt-lg subtitulos" style="margin-left: 20px;margin-bottom: 20px;">
                El gasto de esta solicitud sobrepasa el monto disponible de la actividad, por favor escriba una nota para que el responsable de pago lo tome en cuenta y evalúe si la solicitud será pagada o no.
              </div>
              <div class="col-sm-12">
                <q-field icon="label" icon-color="dark" :error="$v.form_notas.fields.nota.$error" error-label="Escriba porque la solicitud sobrepasa el presupuesto de la actividad">
                  <q-input upper-case v-model="form_notas.fields.nota" type="textarea" inverted color="dark" float-label="Escriba una nota" maxlength="1000" />
                </q-field>
              </div>
            </div>
            <div class="row q-mt-lg" style="margin-right:20px;">
              <div class="col-sm-4 offset-sm-8 pull-right">
                <q-btn @click="agregarGasto()" class="btn_guardar" icon="done_all" :loading="loadingButton" v-if="!form_gastos.modificar && form_solicitudes.fields.colaborador && (form_solicitudes.fields.autorizacion_id===null || form_solicitudes.fields.autorizacion_id===1)">Guardar nota y solicitar</q-btn>
                <q-btn @click="actualizarGastos()" class="btn_guardar" icon="done_all" :loading="loadingButton" v-if="form_gastos.modificar && ((form_solicitudes.fields.colaborador && (form_solicitudes.fields.autorizacion_id===null || form_solicitudes.fields.autorizacion_id===1)) || (form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id===0))">Guardar nota y solicitar</q-btn>
              </div>
            </div>
          </q-modal>
          <q-modal no-backdrop-dismiss v-if="form_gastos.modal_concepto" style="background-color: rgba(0,0,0,0.7);" v-model="form_gastos.modal_concepto" :content-css="{width: '50vw', height: '200px'}">
            <q-modal-layout>
              <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
                <div class="col-sm-10">
                  <q-toolbar-title>
                    Concepto de gasto
                  </q-toolbar-title>
                </div>
                <div class="col-sm-2 pull-right">
                  <q-btn
                    flat
                    round
                    dense
                    color="white"
                    @click="form_gastos.modal_concepto = false"
                    icon="fas fa-times-circle"
                  />
                </div>
              </q-toolbar>
            </q-modal-layout>
            <div class="row q-mt-lg" style="margin-top:70px;margin-right:20px;">
              <div class="col-sm-6">
                <q-field icon="folder" icon-color="dark">
                  <q-select v-model="form_gastos.gasto.concepto_id" inverted color="dark" float-label="Concepto" :options="conceptosOptions" filter @input="getSubconceptosByconceptoGasto()"></q-select>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="folder_open" icon-color="dark">
                  <q-select v-model="form_gastos.gasto.subconcepto_id" inverted color="dark" float-label="Subconcepto" :options="subconceptosOptGastos" filter></q-select>
                </q-field>
              </div>
            </div>
            <div class="row q-mt-lg" style="margin-right:20px;">
              <div class="col-sm-4 offset-sm-8 pull-right">
                <q-btn @click="updateConceptoGasto()" class="btn_guardar" icon="save" :loading="loadingButton">Guardar</q-btn>
              </div>
            </div>
          </q-modal>
        </div>
      </div>
    </div>
  </div>

      <q-modal no-backdrop-dismiss v-if="form_solicitudes.modal_informacion" style="background-color: rgba(0,0,0,0.7);" v-model="form_solicitudes.modal_informacion" :content-css="{minWidth: '90vw', minHeight: '600px'}">
        <q-modal-layout>
          <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
            <div class="col-sm-10">
              <q-toolbar-title>
                Solicitud {{ form_solicitudes.fields.id }}
              </q-toolbar-title>
            </div>
            <div class="col-sm-2 pull-right">
              <q-btn
                flat
                round
                dense
                color="white"
                @click="form_solicitudes.modal_informacion = false"
                icon="fas fa-times-circle"
              />
            </div>
          </q-toolbar>
        </q-modal-layout>
        <div class="row q-mt-lg" style="margin-top:70px;margin-right:20px;">
          <div class="col-sm-12">
            <div class="row q-mt-lg">
              <div class="col-sm-12">
                <q-field icon="business" icon-color="dark" >
                  <q-select v-model="form_solicitudes.fields.empresa_id" inverted-color="dark" float-label="Empresa" :options="empresasOptions" filter/>
                </q-field>
              </div>
            </div>
            <div class="row q-mt-lg">
              <div class="col-sm-4">
                <q-field icon="work" icon-color="dark" >
                  <q-select readonly v-model="form_solicitudes.fields.proyecto_id" inverted-color="dark" float-label="Presupuesto" :options="proyectosFiltro" filter @input="cargarActandUsers2()"/>
                </q-field>
              </div>
              <div class="col-sm-2" style="margin-left:40px;">
                <q-checkbox readonly v-model="incluir_iva" label="CON IVA" color="amber" v-if="(form_solicitudes.fields.iva === 'SI')"/>
                <q-checkbox readonly v-model="incluir_iva" label="SIN IVA" color="amber" v-if="(form_solicitudes.fields.iva === 'NO')"/>
              </div>
              <div class="col-sm-2">
                <q-checkbox v-model="comprobar_solicitud" label="Comprobada" color="amber" v-if="form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id<=0 && (form_solicitudes.fields.status==='POR PAGAR' || form_solicitudes.fields.status==='PAGO PARCIAL' || form_solicitudes.fields.status==='PAGADO') && (form_solicitudes.fields.empresa_id > 0) && (form_gastos.numero_gastos === form_gastos.numero_gastos_comprobados)" @click.native="clickComprobado()"/>
                <q-checkbox readonly v-model="comprobar_solicitud" label="Comprobada" color="amber" v-if="form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id<=0 && (form_solicitudes.fields.status==='POR PAGAR' || form_solicitudes.fields.status==='PAGO PARCIAL' || form_solicitudes.fields.status==='PAGADO') && form_solicitudes.fields.empresa_id > 0 && (form_solicitudes.fields.empresa_id > 0) && (form_gastos.numero_gastos !== form_gastos.numero_gastos_comprobados)" @click.native="clickErrorComprobado()"/>
                <q-checkbox readonly v-model="comprobar_solicitud" label="Comprobada" color="amber" v-if="form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id<=0 && (form_solicitudes.fields.status==='POR PAGAR' || form_solicitudes.fields.status==='PAGO PARCIAL' || form_solicitudes.fields.status==='PAGADO') && form_solicitudes.fields.empresa_id === 0" @click.native="clickErrorEmpresa()"/>
                <q-checkbox readonly v-model="comprobar_solicitud" label="Comprobada" color="amber" v-if="!form_solicitudes.fields.pagos || (form_solicitudes.fields.pagos && (form_solicitudes.fields.autorizacion_id === null || form_solicitudes.fields.autorizacion_id > 0 || form_solicitudes.fields.autorizacion_id === -2))"/>
              </div>
            </div>
            <!-- AGREGAR COMENTARIOS POR SOLICITUD -->
            <div class="row q-mt-lg">
              <!-- <div class="col-sm-12"> -->
              <q-collapsible class="col-sm-12" @click="cargarComentarios()" v-model="open_comentarios" style="border-bottom: 1px solid green; margin-left: 15px;" icon="insert_comment" label="Comentarios de la solicitud">
                <div class="row q-mt-lg">
                  <div class="col-sm-3">
                    <q-field icon="person" icon-color="dark">
                      <q-select v-model="form_comentarios.fields.usuario_destino" inverted color="dark" float-label="Comentario para:" :options="usuariosOpt" filter/>
                    </q-field>
                  </div>
                  <div class="col-sm-9">
                    <q-field icon="insert_comment" icon-color="dark" :error="$v.form_comentarios.fields.descripcion.$error" error-label="Escriba un comentario">
                      <q-input upper-case v-model="form_comentarios.fields.descripcion" type="text" inverted color="dark" float-label="Escriba un comentario o descripción de la solicitud" maxlenght=1000></q-input>
                    </q-field>
                  </div>
                  <div class="col-sm-2 offset-sm-10 pull-right">
                    <q-btn @click="agregarComentario()" class="btn_guardar" icon-right="insert_comment" :loading="loadingButton">Guardar</q-btn>
                  </div>
                </div>
                <!-- aqui va el contenido -->
                <div class="row q-mt-lg">
                  <q-list highlight inset-separator class="col-sm-12" v-for="msje in form_solicitudes.mensajes" :key="msje.id">
                    <q-item multiline>
                      <q-item-side>
                        <q-item-tile avatar>
                          <img src="statics/usuario_avatar.png">
                        </q-item-tile>
                      </q-item-side>
                      <q-item-main>
                        <q-item-tile label>{{ msje.autor }}</q-item-tile>
                        <q-item-tile sublabel lines="4">{{ msje.mensaje }}</q-item-tile>
                      </q-item-main>
                      <q-item-side right>
                        <q-item-tile stamp>Fecha: {{ msje.fecha_mensaje }}</q-item-tile>
                      </q-item-side>
                    </q-item>
                  </q-list>
                </div>
              </q-collapsible> <!-- AQUI TERMINA LO DE AGREGAR COMENTARIOS -->
              <!-- </div> -->
              <!-- AGREGAR ARCHIVOS POR SOLICITUD -->
              <q-collapsible class="col-sm-12" @click="cargarArchivos()" v-model="open_archivos" style="border-bottom: 1px solid green; margin-left: 15px;" icon="note" label="Archivos de la solicitud">
                <div class="row q-mt-sm" style="margin-top:30px; margin-right:30px;">
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
                            <q-btn small flat @click="descargarFormato(props.row)" color="blue-8" icon="cloud_download">
                              <q-tooltip>Descargar</q-tooltip>
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
              </q-collapsible> <!-- AQUI TERMINA LO DE AGREGAR ARCHIVOS -->

              <div class="row q-mt-lg subtitulos" style="margin-left:15px;">
                GASTOS DE LA SOLICITUD
              </div>

              <div class="row q-mt-sm" style="margin-left:15px;margin-right:45px;">
                <div class="row" style="width:50vw;">
                  <q-search hide-underline color="secondary" v-model="form_gastos.filter" />
                </div>
                <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                  <q-table id="sticky-table"
                    :data="form_gastos.data"
                    :columns="form_gastos.columns"
                    :selected.sync="form_gastos.selected"
                    :filter="form_gastos.filter"
                    color="positive"
                    title=""
                    :pagination.sync="form_gastos.pagination"
                    :loading="form_gastos.loading"
                    :rows-per-page-options="form_gastos.rowsOptions"
                    >
                    <q-tr slot="bottom-row" slot-scope="props">
                        <q-td colspan="100%" style="text-align: right;">
                        <strong>Total: ${{ form_solicitudes.fields.totalcopia }} MXN</strong>
                        </q-td>
                      </q-tr>
                    <template slot="body" slot-scope="props">
                      <q-tr :props="props" style="cursor: pointer;">
                        <q-td @click.native="clickFila(props.row)" key="nombre_actividad" :props="props">{{ props.row.nombre_actividad }}</q-td>
                        <q-td key="cotizacion" :props="props">
                          <div style="display: inline;" v-for="cot in props.row.cotizacion" :key="cot.id">
                            <q-btn small flat :icon="cot.icon" :color="cot.color">
                              <q-tooltip>{{ cot.name }}</q-tooltip>
                              <q-popover>
                                <q-list link separator class="scroll" style="min-width: 100px">
                                  <q-item v-close-overlay @click.native="verCotizacion2(cot)" v-if="cot.doc_type !== 'docx' && cot.doc_type !== 'xml' && cot.doc_type !== 'xlsx' && cot.doc_type !== 'XLSX' && cot.doc_type !== 'DOCX'">
                                    <q-item-main label="Ver"/>
                                  </q-item>
                                  <q-item v-close-overlay @click.native="descargarCotizacion(cot.id, cot.doc_type)">
                                    <q-item-main label="Descargar"/>
                                  </q-item>
                                  <q-item v-close-overlay @click.native="cotizacionAccion(cot)">
                                    <q-item-main label="Eliminar"/>
                                  </q-item>
                                </q-list>
                              </q-popover>
                            </q-btn>
                          </div>
                        </q-td>
                        <q-td key="factura" :props="props">
                          <div style="display: inline;" v-for="fac in props.row.factura" :key="fac.id">
                            <q-btn small flat :icon="fac.icon" :color="fac.color">
                              <q-tooltip>{{ fac.name }}</q-tooltip>
                              <q-popover>
                                <q-list link separator class="scroll" style="min-width: 100px">
                                  <q-item v-close-overlay @click.native="verFactura2(fac)" v-if="fac.doc_type !== 'docx' && fac.doc_type !== 'xml' && fac.doc_type !== 'xlsx'">
                                    <q-item-main label="Ver"/>
                                  </q-item>
                                  <q-item v-close-overlay @click.native="descargarFactura(fac.id, fac.doc_type)">
                                    <q-item-main label="Descargar"/>
                                  </q-item>
                                  <q-item v-close-overlay @click.native="facturaAccion(fac)">
                                    <q-item-main label="Eliminar"/>
                                  </q-item>
                                </q-list>
                              </q-popover>
                            </q-btn>
                          </div>
                        </q-td>
                        <q-td key="comprobante" :props="props">
                          <div style="display: inline;" v-for="com in props.row.comprobante" :key="com.id">
                            <q-btn small flat :icon="com.icon" :color="com.color">
                              <q-tooltip>{{ com.name }}</q-tooltip>
                              <q-popover>
                                <q-list link separator class="scroll" style="min-width: 100px">
                                  <q-item v-close-overlay @click.native="verComprobante2(com)" v-if="com.doc_type !== 'docx' && com.doc_type !== 'xml' && com.doc_type !== 'xlsx'">
                                    <q-item-main label="Ver"/>
                                  </q-item>
                                  <q-item v-close-overlay @click.native="descargarComprobante(com.id, com.doc_type)">
                                    <q-item-main label="Descargar"/>
                                  </q-item>
                                  <q-item v-close-overlay @click.native="comprobanteAccion(com)" v-if="form_solicitudes.fields.pagos && form_solicitudes.fields.autorizacion_id<=0 && (form_solicitudes.fields.status==='POR PAGAR' || form_solicitudes.fields.status==='PAGO PARCIAL' || form_solicitudes.fields.status==='PAGADO') && (form_solicitudes.fields.empresa_id > 0)">
                                    <q-item-main label="Eliminar"/>
                                  </q-item>
                                </q-list>
                              </q-popover>
                            </q-btn>
                          </div>
                        </q-td>
                        <q-td key="comprobado" :props="props">
                          <q-checkbox readonly v-model="props.row.comprobado" color="teal"/>
                        </q-td>
                        <q-td key="montocopia" :props="props">${{ props.row.montocopia }}</q-td>
                        <q-td key="iva" :props="props">${{ props.row.iva }}</q-td>
                        <q-td key="total" :props="props">${{ props.row.total }}</q-td>
                        <q-td key="acciones" :props="props">
                          <q-btn disable small flat color="purple" icon="done_all" v-if="props.row.fecha_ejercido!==null">
                            <q-tooltip>Gasto pagado</q-tooltip>
                          </q-btn>
                          <q-btn disable small flat color="red" icon="highlight_off" v-if="props.row.fecha_ejercido===null">
                            <q-tooltip>Gasto no pagado</q-tooltip>
                          </q-btn>
                        </q-td>
                      </q-tr>
                    </template>
                  </q-table>
                  <q-inner-loading :visible="form_gastos.loading">
                    <q-spinner-dots size="64px" color="primary" />
                  </q-inner-loading>
                </div>
              </div>
              <!-- AQUI EMPIEZA EL CARD PARA VER EL GASTO DE LA SOLICITUD -->
              <q-card v-if="form_gastos.modal_informacion" style="margin-top:15px;">
                <q-card-main>
                  <q-btn @click="form_gastos.modal_informacion=false" color="negative" icon="visibility_off">
                    <q-tooltip>Ocultar información</q-tooltip>
                  </q-btn>
                  <div class="row q-mt-lg subtitulos" style="margin-left:15px;">
                      Datos generales del gasto
                  </div>
                  <div class="row q-mt-lg">
                    <div class="col-sm-3">
                      <q-field icon="folder" icon-color="dark">
                        <q-input readonly upper-case v-model="form_gastos.detalles.nombre_actividad" type="text" inverted-color="dark" float-label="Actividad"></q-input>
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="business" icon-color="dark">
                        <q-input readonly upper-case v-model="form_gastos.detalles.tipo_gasto" type="text" inverted-color="dark" float-label="Tipo de gasto"></q-input>
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="date_range" icon-color="dark">
                         <q-datetime readonly v-model="form_gastos.detalles.created" type="date" inverted-color="dark" float-label="Fecha" align="center"></q-datetime>
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-checkbox readonly v-model="form_gastos.detalles.comprobado" label="Comprobar gasto" color="amber"/>
                    </div>
                    <div class="col-sm-12">
                      <q-field icon="label" icon-color="dark">
                        <q-input readonly upper-case v-model="form_gastos.detalles.descripcion_gasto" type="text" inverted-color="dark" float-label="Descripción"></q-input>
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="fas fa-dollar-sign" icon-color="dark">
                        <q-input readonly upper-case v-model="form_gastos.detalles.costo" type="text" inverted-color="dark" float-label="Presupuesto actual"></q-input>
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="fas fa-dollar-sign" icon-color="dark">
                        <q-input readonly upper-case v-model="form_gastos.detalles.cantidad_disponible" type="text" inverted-color="dark" float-label="Presupuesto disponible"></q-input>
                      </q-field>
                    </div>
                    <div class="col-sm-6">
                      <q-field icon="fas fa-dollar-sign" icon-color="dark">
                        <q-input readonly upper-case v-model="form_gastos.detalles.monto_total_pagar" type="text" inverted-color="dark" float-label="Monto total a pagar"></q-input>
                      </q-field>
                    </div>
                  </div>
                  <div class="row q-mt-lg subtitulos" style="margin-left:15px;">
                      Proveedor y datos bancarios
                  </div>
                  <div class="row q-mt-lg">
                    <div class="col-sm-9">
                      <q-field icon="fas fa-building" icon-color="dark">
                        <q-input readonly upper-case v-model="form_gastos.detalles.razon_social" inverted-color="dark" float-label="Razón social"/>
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="fas fa-id-card" icon-color="dark">
                        <q-input readonly upper-case v-model="form_gastos.detalles.rfc" inverted-color="dark" float-label="RFC"/>
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="account_balance" icon-color="dark">
                        <q-input readonly upper-case v-model="form_gastos.detalles.banco" inverted-color="dark" float-label="Banco"/>
                      </q-field>
                    </div>
                    <div class="col-sm-3" v-if="form_solicitudes.fields.banco1 === 'SI'">
                      <q-field icon="vpn_key" icon-color="dark">
                        <q-input readonly upper-case v-model="form_gastos.detalles.clabe" inverted color="teal" float-label="CLABE"/>
                      </q-field>
                    </div>
                    <div class="col-sm-3" v-if="form_solicitudes.fields.banco1 === 'NO'">
                      <q-field icon="vpn_key" icon-color="dark">
                        <q-input readonly upper-case v-model="form_gastos.detalles.clabe" inverted-color="dark" float-label="CLABE"/>
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="account_balance" icon-color="dark">
                        <q-input readonly upper-case v-model="form_gastos.detalles.banco2" inverted-color="dark" float-label="Banco 2"/>
                      </q-field>
                    </div>
                    <div class="col-sm-3" v-if="form_solicitudes.fields.banco2 === 'SI'">
                      <q-field icon="vpn_key" icon-color="dark">
                        <q-input readonly upper-case v-model="form_gastos.detalles.clabe2" inverted color="teal" float-label="CLABE 2"/>
                      </q-field>
                    </div>
                    <div class="col-sm-3" v-if="form_solicitudes.fields.banco2 === 'NO'">
                      <q-field icon="vpn_key" icon-color="dark">
                        <q-input readonly upper-case v-model="form_gastos.detalles.clabe2" inverted-color="dark" float-label="CLABE 2"/>
                      </q-field>
                    </div>
                    <div class="col-sm-3" v-if="form_solicitudes.fields.toka === 'SI'">
                      <q-field icon="vpn_key" icon-color="dark">
                        <q-input readonly upper-case v-model="form_gastos.detalles.toka" inverted color="teal" float-label="toka"/>
                      </q-field>
                    </div>
                    <div class="col-sm-3" v-if="form_solicitudes.fields.toka === 'NO'">
                      <q-field icon="vpn_key" icon-color="dark">
                        <q-input readonly upper-case v-model="form_gastos.detalles.toka" inverted-color="dark" float-label="toka"/>
                      </q-field>
                    </div>
                  </div>
                </q-card-main>
              </q-card>
              <!-- AQUI EMPIEZA EL MODAL DE VER EL ARCHIVO DE LA SOLICITUD -->
              <q-modal no-backdrop-dismiss v-if="form_gastos.modal_archivo" style="background-color: rgba(0,0,0,0.7);" v-model="form_gastos.modal_archivo" :content-css="{width: '80vw', height: '700px'}">
                <q-modal-layout>
                  <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
                    <div class="col-sm-10">
                      <q-toolbar-title>
                        Archivo
                      </q-toolbar-title>
                    </div>
                    <div class="col-sm-2 pull-right">
                      <q-btn
                        flat
                        round
                        dense
                        color="white"
                        @click="desaparecer2()"
                        icon="fas fa-times-circle"
                      />
                    </div>
                  </q-toolbar>
                </q-modal-layout>
                <div class="row q-mt-lg" style="margin-top:50px;">
                  <div class="col-sm-12">
                    <img id="img01" :src="form_gastos.url_img" class="modal-content2">
                    <embed id="pdf01" :src="form_gastos.url_pdf" class="modal-content2" type="application/pdf" width="100vw" height="700px" />
                  </div>
                </div>
              </q-modal>
            </div>
          </div>
        </div>
      </q-modal> <!-- AQUI TERMINA EL MODAL DE SOLO LECTURA DE UNA SOLICITUD -->

      <div id="myModal" class="modal1" v-if="vista_imagen" style="display: block;">
        <span class="close1" @click="desaparecer()" style=" margin-top:50px;">&times;</span>
        <img id="img01" :src="form_gastos.url_img" class="modal-content1">
        <embed id="pdf01" :src="form_gastos.url_pdf" class="modal-content1" type="application/pdf" width="600" height="100%" />
      </div>

      <q-modal no-backdrop-dismiss v-if="crear_nuevo_proveedor" style="background-color: rgba(0,0,0,0.7);" v-model="crear_nuevo_proveedor" :content-css="{minWidth: '90vw', minHeight: '500px'}">
        <q-modal-layout>
          <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
            <div class="col-sm-10">
              <q-toolbar-title>
                Nuevo proveedor
              </q-toolbar-title>
            </div>
            <div class="col-sm-2 pull-right">
              <q-btn
                flat
                round
                dense
                color="white"
                @click="crear_nuevo_proveedor = false"
                icon="fas fa-times-circle"
              />
            </div>
          </q-toolbar>
        </q-modal-layout>
        <div class="row" style="margin-right: 20px;">
          <div class="col-sm-12">
            <div class="row justify-between">
              <div class="col-sm-9">
                <h5 style="margin: 7px 0; font-weight: bold">NUEVO PROVEEDOR</h5>
              </div>
            </div>
            <div class="row q-mt-lg subtitulos" style="margin-left: 20px;">
              Datos generales
            </div>
            <div class="row q-mt-lg">
              <div class="col-sm-6">
                <q-field icon="fas fa-building" icon-color="dark" :error="$v.form_proveedor.fields.nombre_comercial.$error" error-label="Escriba un nombre">
                  <q-input upper-case v-model="form_proveedor.fields.nombre_comercial" inverted color="dark" float-label="Nombre Comercial"/>
                </q-field>
              </div>
              <div class="col-sm-6">
                <q-field icon="fas fa-building" icon-color="dark" :error="$v.form_proveedor.fields.razon_social.$error" error-label="Ingrese la razón social">
                  <q-input upper-case v-model="form_proveedor.fields.razon_social" inverted color="dark" float-label="Razón social"/>
                </q-field>
              </div>
            </div>
            <div class="row q-mt-lg">
              <div class="col-sm-6">
                <q-field icon="fas fa-address-book" icon-color="dark" :error="$v.form_proveedor.fields.direccion.$error" error-label="Escriba una dirección">
                  <q-input upper-case v-model="form_proveedor.fields.direccion" inverted color="dark" float-label="Dirección"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-id-card" icon-color="dark">
                  <q-input upper-case v-model="form_proveedor.fields.rfc" inverted color="dark" float-label="RFC"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-id-badge" icon-color="dark">
                  <q-input upper-case v-model="form_proveedor.fields.curp" inverted color="dark" float-label="CURP"/>
                </q-field>
              </div>
            </div>
            <div class="row q-mt-lg">
              <div class="col-sm-4">
                <q-field icon="contact_phone" icon-color="dark" :error="$v.form_proveedor.fields.telefono.$error" error-label="Verifique el número telefónico">
                  <q-input  @keyup="isNumber($event,'telefono')" upper-case v-model="form_proveedor.fields.telefono" inverted color="dark" float-label="Telefono"/>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="person" icon-color="dark" :error="$v.form_proveedor.fields.contacto.$error" error-label="Ingrese el Contacto">
                  <q-input upper-case v-model="form_proveedor.fields.contacto" inverted color="dark" float-label="Contacto"/>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="email" icon-color="dark" :error="$v.form_proveedor.fields.email.$error" error-label="Por favor ingrese un email válido">
                  <q-input v-model="form_proveedor.fields.email" type="email" inverted color="dark" float-label="Correo electrónico" maxlength="100" />
                </q-field>
              </div>
            </div>
            <div class="row q-mt-lg subtitulos" style="margin-left: 20px;">
              Datos bancarios
            </div>
            <div class="row q-mt-lg">
              <div class="col-sm-3">
                <q-field icon="account_balance" icon-color="dark" :error="$v.form_proveedor.fields.banco.$error" error-label="Escriba el nombre del banco">
                  <q-input upper-case v-model="form_proveedor.fields.banco" inverted color="dark" float-label="Banco"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="vpn_key" icon-color="dark" :error="$v.form_proveedor.fields.clabe.$error" error-label="Verifique el número CLABE">
                  <q-input  @keyup="isNumber($event,'clabe1')" upper-case v-model="form_proveedor.fields.clabe" inverted color="dark" float-label="CLABE"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="account_balance" icon-color="dark" :error="$v.form_proveedor.fields.banco2.$error" error-label="Escriba el nombre del Banco">
                  <q-input upper-case v-model="form_proveedor.fields.banco2" inverted color="dark" float-label="Banco 2"/>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="vpn_key" icon-color="dark" :error="$v.form_proveedor.fields.clabe2.$error" error-label="Verifique el número clabe">
                  <q-input  @keyup="isNumber($event,'clabe2')" upper-case v-model="form_proveedor.fields.clabe2" inverted color="dark" float-label="CLABE 2"/>
                </q-field>
              </div>
            </div>
            <div class="row q-mt-lg">
              <div class="col-sm-3">
                <q-field icon="vpn_key" icon-color="dark" :error="$v.form_proveedor.fields.toka.$error" error-label="Verifique el número de la tarjeta toka">
                  <q-input @keyup="isNumber($event,'toka')" upper-case v-model="form_proveedor.fields.toka" inverted color="dark" float-label="TOKA"/>
                </q-field>
              </div>
            </div>
            <div class="row q-mt-lg">
              <div class="col-sm-2 offset-sm-10 pull-right">
                <q-btn @click="save_new_proveedor()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
              </div>
            </div>
          </div>
        </div>
      </q-modal>
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { required, maxValue, minValue, maxLength, email, minLength, helpers } from 'vuelidate/lib/validators'
import {VMoney} from 'v-money'
import axios from 'axios'
const clabe = helpers.regex('clabe', /[0-9]{18}/)

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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'AUXILIAR'.toUpperCase() || propiedades[i].toUpperCase() === 'COBRANZA'.toUpperCase() || propiedades[i].toUpperCase() === 'INVENTARIOS'.toUpperCase() || propiedades[i].toUpperCase() === 'VENTAS'.toUpperCase() || propiedades[i].toUpperCase() === 'PLANEACIÓN'.toUpperCase()) {
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
      role: '',
      user_id: 0,
      selectYear: [ { label: '' + (new Date().getFullYear() - 1), value: '' + (new Date().getFullYear() - 1) }, { label: '' + (new Date().getFullYear()), value: '' + (new Date().getFullYear()) }, { label: '' + (new Date().getFullYear() + 1), value: '' + (new Date().getFullYear() + 1) } ],
      year: '' + (new Date().getFullYear()),
      crear_nuevo_proveedor: false,
      ver_detalles_proveedor: false,
      usuariosOptions: [],
      gasto_id_archivo: 0,
      isExpanded: true,
      loadingButton: false,
      factura_solicitud: false,
      incluir_iva: false,
      comprobar_solicitud: false,
      comprobar_gasto: false,
      open_archivos: false,
      open_comentarios: false,
      open_notas: false,
      mensajes: [],
      perfil_colaborador: 'no',
      proyectosFiltro: [],
      proyectosOpt: [],
      actividadesFiltro: [],
      actividadesOpt: [],
      usuariosOpt: [],
      subconceptosOpt: [],
      subconceptosOptGastos: [],
      vista_imagen: false,
      banco1: false,
      banco2: false,
      toka: false,

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
      form: { // para los filtros
        fields: {
          status: 'all',
          proyecto_id: 0,
          responsable_id: 0,
          actividad_id: 0,
          comprobado: 'all',
          proveedor_id: 0,
          creador_id: 0,
          factura: 'all'
        },
        selectComprobada: [ { 'label': 'SI', 'value': 'SI' }, { 'label': 'NO', 'value': 'NO' }, { 'label': 'Todos', 'value': 'all' } ],
        selectStatus: [ { 'label': 'CREADO', 'value': 'CREADO' }, { 'label': 'CANCELADO', 'value': 'CANCELADO' }, { 'label': 'POR AUTORIZAR', 'value': 'POR AUTORIZAR' }, { 'label': 'RECHAZADO', 'value': 'RECHAZADO' }, { 'label': 'POR PAGAR', 'value': 'POR PAGAR' }, { 'label': 'PAGADO', 'value': 'PAGADO' }, { 'label': 'PAGO PARCIAL', 'value': 'PAGO PARCIAL' }, { 'label': 'Todos', 'value': 'all' } ],
        selectFactura: [ { 'label': 'SI', 'value': 'SI' }, { 'label': 'NO', 'value': 'NO' }, { 'label': 'Todos', 'value': 'all' } ]
      },
      form_solicitudes: {
        fields: {
          id: 0,
          status: '',
          fecha_solicitado: 0,
          fecha_creado: '',
          fecha_ejercido: '',
          nombre_proyecto: '',
          proyecto_id: 0,
          autorizacion_id: 0,
          responsable_solicitud: '',
          creador: 0,
          total: 0,
          totalcopia: 0,
          iva: '',
          comprobado: 'NO',
          subtotal: 0, // este campo se usa para hacer el calculo de suma de gastos
          colaborador: false,
          solicitante: false,
          autorizador: false,
          pagos: false,
          orden: 0,
          alteracion: '',
          autorizador_id: 0,
          empresa_id: 0,
          subempresa_id: 0,
          columna_total: 0,
          proveedor_id: 0,
          cuentas: [],
          cuenta: '',
          banco1: 'NO',
          banco2: 'NO',
          toka: 'NO',
          concepto_id: 0,
          subconcepto_id: 0,
          sobrepasa_presupuesto: 'NO'
        },
        columns: [
          {name: 'id', label: '#', field: 'id', sortable: true, type: 'number', align: 'left'},
          { name: 'nombre_proyecto', label: 'Project', field: 'nombre_proyecto', sortable: true, type: 'string', align: 'left' },
          { name: 'creador_nombre', label: 'Creador', field: 'creador_nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha_creado_validacion', label: 'Fecha creación', field: 'fecha_creado_validacion', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_solicitado_validacion', label: 'Fecha de solicitud', field: 'fecha_solicitado_validacion', sortable: true, type: 'string', align: 'center' },
          { name: 'fecha_ejercido', label: 'Fecha ejercido', field: 'fecha_ejercido', sortable: true, type: 'string', align: 'center' },
          { name: 'columna_total', label: 'Total:', field: 'columna_total', sortable: true, type: 'string', align: 'right' },
          { name: 'responsable_solicitud', label: 'Asignado a:', field: 'responsable_solicitud', sortable: true, type: 'string', align: 'left' },
          { name: 'status', label: 'Estatus', field: 'status', sortable: true, type: 'string', align: 'left' },
          { name: 'comprobado', label: 'Comprobado', field: 'comprobado', sortable: true, type: 'string', align: 'center' },
          { name: 'factura', label: 'Factura', field: 'factura', sortable: true, type: 'string', align: 'center' },
          { name: 'color_clasificado', label: 'Clasificado', field: 'color_clasificado', sortable: true, type: 'string', align: 'center' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', type: 'string', align: 'center' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: {
          sortBy: '',
          descending: 'false',
          page: 1,
          rowsNumber: 0,
          rowsPerPage: 50
        },
        selected: [],
        filter: '',
        solicitudes_proyecto: false,
        solicitudes_proyecto2: false,
        contador_bandera_comprobado: 0,
        contador_bandera_iva: 0,
        bandera_iva: false,
        bandera_comprobado: false,
        modal_informacion: false,
        loading: false,
        mensajes: [],
        modal_notas: false,
        subempresasOptions: []
      },
      form_gastos: {
        tipoOptions: [ { 'label': 'HOSPEDAJE', 'value': 1 }, { 'label': 'ALIMENTACIÓN', 'value': 2 }, { 'label': 'TRANSPORTE', 'value': 3 }, { 'label': 'PAPELERÍA', 'value': 4 } ],
        columns: [
          {name: 'indice_gasto', label: '#', field: 'indice_gasto', sortable: true, type: 'string', align: 'left'},
          {name: 'nombre_actividad', label: 'Actividad', field: 'nombre_actividad', sortable: true, type: 'string', align: 'left'},
          // {name: 'tipo_gasto', label: 'Tipo de gasto', field: 'tipo_gasto', sortable: true, type: 'string', align: 'left'},
          {name: 'cotizacion', label: 'Cotización', field: 'cotizacion', sortable: true, type: 'string', align: 'left'},
          {name: 'factura', label: 'Factura', field: 'factura', sortable: true, type: 'string', align: 'left'},
          {name: 'comprobante', label: 'Recibo de pago', field: 'comprobante', sortable: true, type: 'string', align: 'left'},
          {name: 'comprobado', label: 'Comprobado', field: 'comprobado', sortable: true, type: 'string', align: 'center'},
          // {name: 'montocopia', label: 'Monto solicitado', field: 'montocopia', sortable: true, type: 'string', align: 'right'},
          // {name: 'iva', label: 'IVA', field: 'iva', sortable: true, type: 'string', align: 'right'},
          {name: 'sumatoria_factura', label: 'Monto facturado', field: 'sumatoria_factura', sortable: true, type: 'string', align: 'right'},
          {name: 'total', label: 'Total', field: 'total', sortable: true, type: 'string', align: 'center'},
          { name: 'acciones', label: 'Acciones', field: 'acciones', type: 'string', align: 'center' }
        ],
        columnsPagos: [
          {name: 'indice_gasto', label: '#', field: 'indice_gasto', sortable: true, type: 'string', align: 'left'},
          {name: 'nombre_actividad', label: 'Actividad', field: 'nombre_actividad', sortable: true, type: 'string', align: 'left'},
          // {name: 'tipo_gasto', label: 'Tipo de gasto', field: 'tipo_gasto', sortable: true, type: 'string', align: 'left'},
          {name: 'cotizacion', label: 'Cotización', field: 'cotizacion', sortable: true, type: 'string', align: 'left'},
          {name: 'factura', label: 'Factura', field: 'factura', sortable: true, type: 'string', align: 'left'},
          {name: 'comprobante', label: 'Recibo de pago', field: 'comprobante', sortable: true, type: 'string', align: 'left'},
          {name: 'comprobado', label: 'Comprobado', field: 'comprobado', sortable: true, type: 'string', align: 'center'},
          // {name: 'montocopia', label: 'Monto solicitado', field: 'montocopia', sortable: true, type: 'string', align: 'right'},
          // {name: 'iva', label: 'IVA', field: 'iva', sortable: true, type: 'string', align: 'right'},
          {name: 'sumatoria_factura', label: 'Monto facturado', field: 'sumatoria_factura', sortable: true, type: 'string', align: 'right'},
          {name: 'total', label: 'Total', field: 'total', sortable: true, type: 'string', align: 'center'},
          {name: 'concepto', label: 'Concepto', field: 'concepto', sortable: true, type: 'string', align: 'left'},
          {name: 'subconcepto', label: 'Subconcepto', field: 'subconcepto', sortable: true, type: 'string', align: 'left'},
          { name: 'acciones', label: 'Acciones', field: 'acciones', type: 'string', align: 'center' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false,
        modificar: false,
        modal_informacion: false,
        modal_archivo: false,
        modal_concepto: false,
        detalles: [],
        numero_gastos_pagados: 0,
        numero_gastos: 0,
        numero_gastos_comprobados: 0,
        url_img: '',
        url_pdf: '',
        notas: [],
        gasto: [],
        fields: {
          id: 0,
          actividad_id: 0,
          monto: 0.00,
          montovalidar: 0,
          montocopia: 0,
          fecha_ejercido: '',
          proveedor_id: 0,
          solicitud_id: 0,
          presupuesto_real: 0, // sse usa para validaciones
          presupuesto_disponible: 0, // sse usa para validaciones
          presupuesto_disponible_validar: 0, // sse usa para validaciones
          presupuesto_disponible_validar_real: 0,
          presupuesto_disponible_real: 0,
          presupuesto_real_validar: 0,
          nombre_actividad: '',
          nombre_proveedor: '',
          descripcion_gasto: '',
          total: 0,
          tipo: 0,
          comprobado: 'NO',
          iva: 0,
          sumatoria_factura: 0,
          clasificacion: '',
          objeto_gasto: '',
          comision: 0,
          concepto_id: 0,
          subconcepto_id: 0
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
      form_comentarios: {
        columns: [
          {name: 'autor', label: 'Autor', field: 'autor', sortable: true, type: 'string', align: 'left'},
          {name: 'comentario', label: 'Acciones', field: 'acciones', type: 'string', align: 'right'}
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false,
        fields: {
          autor: 0,
          comentario: '',
          usuario_destino: 0
        }
      },
      form_rechazar: {
        niveles: [ { 'label': 'COLABORADORES', 'value': 1 }, { 'label': 'SOLICITANTES', 'value': 2 }, { 'label': 'AUTORIZADORES', 'value': 3 } ],
        niveles2: [ { 'label': 'COLABORADORES', 'value': 1 }, { 'label': 'SOLICITANTES', 'value': 2 } ],
        fields: {
          autor: 0,
          comentario: '',
          usuario_destino: 0,
          nivel: 0, // usada para rechazar
          orden: 0
        }
      },
      form_pagar: {
        modal_pagar: false,
        fields: {
          id: 0,
          cantidad_solicitada: 0,
          actividad_id: 0,
          comision: 0,
          monto: '0.00',
          presupuesto_disponible: 0,
          presupuesto_real: 0
        }
      },
      form_notas: {
        fields: {
          nota: ''
        }
      },
      form_proveedor: {
        fields: {
          id: 0,
          nombre_comercial: '',
          razon_social: '',
          rfc: '',
          curp: '',
          direccion: '',
          banco: '',
          banco2: '',
          clabe: '',
          clabe2: '',
          telefono: '',
          contacto: '',
          toka: '',
          email: ''
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
      recursosOptions: 'vnt/recurso/selectOptions',
      empresasOptions: 'vnt/empresa/selectOptions',
      proveedoresOptions: 'pmo/proveedor/selectOptions',
      gastosOptions: 'fin/tiposgastos/selectOptions',
      proyectos: 'pmo/proyecto/proyectos',
      solicitudes: 'fin/solicitudes/solicitudes',
      conceptosOptions: 'vnt/concepto/selectOptions'
    }),
    actividadesByProyectoOptions () {
      let actividades = this.$store.getters['pmo/actividades/selectOtherOptions'].filter(p => p.proyecto_id === this.form.fields.proyecto_id)
      return actividades
    },
    actividadesByProyectoOptions2 () {
      let actividades = this.$store.getters['pmo/actividades/selectOtherOptions'].filter(p => p.proyecto_id === this.form_solicitudes.fields.proyecto_id)
      actividades.push({ 'label': 'Todos', 'value': 0 })
      return actividades
    },
    usuariosOptions2 () {
      let usuarios = this.$store.getters['sys/users/selectOptionsName']
      usuarios.push({ 'label': 'Todos', 'value': 0 })
      return usuarios
    },
    proyectosOptions2 () {
      let proyectos2 = this.$store.getters['pmo/proyecto/selectOptions']
      proyectos2.push({ 'label': 'Todos', 'value': 0 })
      return proyectos2
    },
    proveedoresOptions2 () {
      let proveedoresOpt = this.$store.getters['pmo/proveedor/selectOptions']
      proveedoresOpt.push({ 'label': 'Todos', 'value': 0 })
      return proveedoresOpt
    },
    arrayTreeObj () {
      let newObj = []
      this.recursive(this.actividades, newObj, 0, this.itemId, this.isExpanded)
      return newObj
    }
  },
  created () {
    // this.loadAll()
  },
  watch: {
    incluir_iva (newValue, old) {
      this.form_solicitudes.contador_bandera_iva++
      if (newValue === true) {
        this.form_solicitudes.fields.total = parseFloat(this.form_solicitudes.fields.subtotal) + parseFloat(this.form_solicitudes.fields.subtotal * 0.16)
        this.form_solicitudes.fields.totalcopia = this.currencyFormat(this.form_solicitudes.fields.total)
        this.form_solicitudes.fields.iva = 'SI'
        this.cargarGastos()
      }
      if (newValue === false) {
        this.form_solicitudes.fields.total = parseFloat(this.form_solicitudes.fields.subtotal)
        this.form_solicitudes.fields.totalcopia = this.currencyFormat(this.form_solicitudes.fields.total)
        this.form_solicitudes.fields.iva = 'NO'
        this.cargarGastos()
      }
    },
    comprobar_solicitud (newValue, old) {
      this.form_solicitudes.contador_bandera_comprobado++
      if (newValue === true) {
        this.form_solicitudes.fields.comprobado = 'SI'
      }
      if (newValue === false) {
        this.form_solicitudes.fields.comprobado = 'NO'
      }
      /* if ((this.form_solicitudes.contador_bandera_comprobado > 1) && (this.form_solicitudes.fields.status !== 'CREADO') && (this.form_solicitudes.bandera_comprobado === 1)) {
        this.actualizarSolicitud()
        console.log('comprobado')
      } */
    },
    factura_solicitud (newValue, old) {
      this.form_solicitudes.contador_bandera_factura++
      if (newValue === true) {
        this.form_solicitudes.fields.factura = 'SI'
      }
      if (newValue === false) {
        this.form_solicitudes.fields.factura = 'NO'
      }
    },
    comprobar_gasto (newValue, old) {
      if (newValue === true) {
        this.form_gastos.fields.comprobado = 'SI'
      }
      if (newValue === false) {
        this.form_gastos.fields.comprobado = 'NO'
      }
    },
    banco1 (newValue, old) {
      if (newValue === true) {
        this.form_solicitudes.fields.banco1 = 'SI'
        this.form_solicitudes.fields.banco2 = 'NO'
        this.form_solicitudes.fields.toka = 'NO'
      }
      if (newValue === false) {
        this.form_solicitudes.fields.banco1 = 'NO'
      }
    },
    banco2 (newValue, old) {
      if (newValue === true) {
        this.form_solicitudes.fields.banco2 = 'SI'
        this.form_solicitudes.fields.banco1 = 'NO'
        this.form_solicitudes.fields.toka = 'NO'
      }
      if (newValue === false) {
        this.form_solicitudes.fields.banco2 = 'NO'
      }
    },
    toka (newValue, old) {
      if (newValue === true) {
        this.form_solicitudes.fields.toka = 'SI'
        this.form_solicitudes.fields.banco1 = 'NO'
        this.form_solicitudes.fields.banco2 = 'NO'
      }
      if (newValue === false) {
        this.form_solicitudes.fields.toka = 'NO'
      }
    },
    open_archivos (newValue, old) {
      if (newValue === true) {
        this.cargarArchivos()
      }
    },
    open_comentarios (newValue, old) {
      if (newValue === true) {
        this.cargarComentarios()
      }
    },
    open_notas (newValue, old) {
      if (newValue === true) {
        this.cargarNotas()
      }
    }
  },
  methods: {
    ...mapActions({
      getUser: 'user/refresh',
      getEmpresas: 'vnt/empresa/refresh',
      getTiposGastos: 'fin/tiposgastos/refresh',
      getSolicitudes: 'fin/solicitudes/refresh',
      getProyectosPerfiles: 'pmo/proyecto/getProyectosPerfiles',
      getProyectosColaborador: 'pmo/proyecto/getProyectosColaborador',
      getUsuariosByPresupuesto: 'pmo/proyecto/getUsuariosByPresupuesto',
      getRecursos: 'vnt/recurso/refresh',
      getProveedores: 'pmo/proveedor/refresh',
      getUsuarios: 'sys/users/refresh',
      getActividadesByProyecto: 'pmo/actividades/getActividadesOpt',
      getGastosByActividad: 'fin/gastos/getByActividad',
      getGastosBySolicitud: 'fin/gastos/getBySolicitud',
      getPresupuestoActividad: 'pmo/actividades/getPresupuestoActividad',
      getPresupuestoActividadReal: 'pmo/actividades/getPresupuestoActividadReal',
      getProveedorById: 'pmo/proveedor/getById',
      getActividad: 'pmo/actividades/getByActividad',
      saveGasto: 'fin/gastos/save',
      updateGasto: 'fin/gastos/update',
      deleteGasto: 'fin/gastos/delete',
      savenewSolicitud: 'fin/solicitudes/save',
      updateSolicitud: 'fin/solicitudes/update',
      getSolicitudById: 'fin/solicitudes/getById',
      filtrarSolicitudes: 'fin/solicitudes/filtrar',
      archivos: 'fin/archivos_solicitudes/getArchivosBySolicitud',
      eliminarArchivo: 'fin/archivos_solicitudes/deleteFormato',
      guardarComentario: 'fin/mensajes/save',
      mensajesBySolicitud: 'fin/mensajes/getMensajesBySolicitud',
      notasBySolicitud: 'fin/notas/getNotasBySolicitud',
      deleteCotizacion: 'fin/cotizacion/deleteFormato',
      deleteFactura: 'fin/factura/deleteFormato',
      deleteComprobante: 'fin/comprobante/deleteFormato',
      autorizarSolicitud: 'fin/autorizar/save',
      rechazarSolicitud: 'fin/solicitudes/rechazar',
      cancelarSolicitud: 'fin/solicitudes/cancelar',
      getGastoById: 'fin/gastos/getById',
      pagarSolicitud: 'fin/pagos/pagar',
      pagarGastoS: 'fin/gastos/pagar',
      comprobarGastoS: 'fin/gastos/comprobar',
      updateSolicitudEmpresa: 'fin/solicitudes/updateEmpresa',
      getConceptos: 'vnt/concepto/refresh',
      getSubconceptos: 'vnt/subconcepto/getByConcepto',
      getEmpresasByPadre: 'vnt/empresa/getByPadre',
      saveProveedores: 'pmo/proveedor/save',
      updateConcepto: 'fin/gastos/updateConcepto'
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
      return `padding-left: ${item.level * 15}px;`
    },

    async loadAll () {
      // await this.getRecursos()
      // await this.getSolicitudes() --------------------------------------
      //
      await this.cargarUsuarios()
      await this.getProyectosFiltro()
      await this.getProyectosOpt()
      await this.getActividadesFiltro()
      await this.getProveedores()
      if (this.$route.params.id > 0 && this.$route.params.accion === 'filtrar') {
        await this.projects(this.$route.params.id)
      }
      await this.qTableRequest({
        pagination: this.form_solicitudes.pagination,
        filter: this.form_solicitudes.filter
      })
      await this.getTiposGastos()
      if (this.$route.params.id > 0 && this.$route.params.accion === 'crear') {
        await this.projects2(this.$route.params.id)
        await this.newSolicitud(this.form_solicitudes.fields.proyecto_id)
      }
      await this.getRole()
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
    async projects (id) {
      await axios.get('/proyectos/getBy/' + id).then(({data}) => {
        this.form.fields.proyecto_id = data.proyectos[0].id
      }).catch(error => {
        console.error(error)
      })
    },
    async projects2 (id) {
      await axios.get('/proyectos/getBy/' + id).then(({data}) => {
        this.form_solicitudes.fields.proyecto_id = data.proyectos[0].id
      }).catch(error => {
        console.error(error)
      })
    },
    async cargarUsuarios () {
      this.usuariosOptions = []
      this.getUsuarios().then(({data}) => {
        data.users.forEach(element => {
          this.usuariosOptions.push({ label: element.nickname, value: element.id })
        })
        this.usuariosOptions.push({ label: 'Todos', value: 0 })
      }).catch(error => {
        console.error(error)
      })
    },
    setView (view) {
      this.views.grid = false
      this.views.update = false
      this.views.update2 = false
      this.views[view] = true
    },
    async qTableRequest (props) {
      this.form_solicitudes.loading = true
      this.form_solicitudes.pagination = props.pagination
      this.form_solicitudes.filter = props.filter
      await this.filtrar()
      this.form.loading = false
    },
    filtrar () {
      // this.getProyectosFiltro()
      this.loadingButton = true
      this.form_solicitudes.loading = true
      this.form_solicitudes.data = []
      let params = this.form.fields
      params.year = this.year
      params.pagination = this.form_solicitudes.pagination
      params.filter = this.form_solicitudes.filter
      this.filtrarSolicitudes(params).then(({data}) => {
        this.loadingButton = false
        this.form_solicitudes.pagination.rowsNumber = data.solicitudes_count
        if (data.solicitudes.length > 0) {
          data.solicitudes.forEach(function (element) {
            if (element.comprobado === 'SI') {
              element.color_comprobado = 'green-9'
              element.icon_comprobado = 'fas fa-check'
            } else {
              element.color_comprobado = 'red-10'
              element.icon_comprobado = 'fas fa-times'
            }
            if (element.factura === 'SI') {
              element.color_factura = 'green-9'
              element.icon_factura = 'fas fa-check'
            } else {
              element.color_factura = 'red-10'
              element.icon_factura = 'fas fa-times'
            }
            if (element.status === 'CREADO') {
              element.color = 'amber'
              element.icon = 'fas fa-arrow-circle-right'
            } else if (element.status === 'POR PAGAR') {
              element.color = 'blue'
              element.icon = 'fas fa-check-circle'
            } else if (element.status === 'POR AUTORIZAR') {
              element.color = 'blue-10'
              element.icon = 'fas fa-check-circle'
            } else if (element.status === 'RECHAZADO') {
              element.color = 'orange-8'
              element.icon = 'clear'
            } else if (element.status === 'PAGADO') {
              element.color = 'green'
              element.icon = 'done_all'
            } else if (element.status === 'PAGO PARCIAL') {
              element.color = 'green'
              element.icon = 'done'
            } else if (element.status === 'CANCELADO') {
              element.color = 'orange-10'
              element.icon = 'highlight_off'
            }
          })
          this.form_solicitudes.data = data.solicitudes
        }
        if (data.perfil_colaborador === 'si') {
          this.perfil_colaborador = 'si'
        } else {
          this.perfil_colaborador = 'no'
        }
        this.form_solicitudes.loading = false
      }).catch(error => {
        console.error(error)
        this.form_solicitudes.loading = false
      })
    },
    getProveedor () {
      this.getProveedorById({id: this.form_solicitudes.fields.proveedor_id}).then(({data}) => {
        if (data.result === 'success') {
          this.form_solicitudes.fields.cuentas = data.proveedorid
          console.log(this.form_solicitudes.fields.cuentas)
          console.log(this.form_solicitudes.fields.cuentas[0].razon_social)
          this.banco1 = false
          this.banco2 = false
          this.toka = false
          this.form_solicitudes.fields.banco1 = 'NO'
          this.form_solicitudes.fields.banco2 = 'NO'
          this.form_solicitudes.fields.toka = 'NO'
        }
      }).catch(error => {
        console.error(error)
      })
    },
    getProyectosFiltro () {
      this.getProyectosPerfiles({year: this.year}).then(({data}) => {
        if (data.result === 'success') {
          this.proyectosFiltro = data.proyectos
          this.proyectosFiltro.splice(0, 0, { 'label': 'Todos', 'value': 0 })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    getProyectosOpt () {
      this.getProyectosColaborador({year: this.year}).then(({data}) => {
        if (data.result === 'success') {
          this.proyectosOpt = data.proyectos
          this.proyectosOpt.splice(0, 0, { 'label': '--Seleccione--', 'value': 0 })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    getActividadesFiltro () {
      this.getActividadesByProyecto({proyecto_id: this.form.fields.proyecto_id}).then(({data}) => {
        if (data.result === 'success') {
          this.actividadesFiltro = data.actividades
          this.actividadesFiltro.push({ 'label': 'Todos', 'value': 0 })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    getActividadesOpt () {
      this.getActividadesByProyecto({proyecto_id: this.form_solicitudes.fields.proyecto_id}).then(({data}) => {
        if (data.result === 'success') {
          this.actividadesOpt = data.actividades
          this.actividadesOpt.push({ 'label': '--Seleccione--', 'value': 0 })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    getUsuariosInvolucrados () {
      this.getUsuariosByPresupuesto({proyecto_id: this.form.fields.proyecto_id}).then(({data}) => {
        if (data.result === 'success') {
          this.usuariosOpt = data.users
          this.usuariosOpt.push({ 'label': 'Todos', 'value': 0 })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    getUsuariosInvolucrados2 () {
      this.getUsuariosByPresupuesto({proyecto_id: this.form_solicitudes.fields.proyecto_id}).then(({data}) => {
        if (data.result === 'success') {
          this.usuariosOpt = data.users
          this.usuariosOpt.push({ 'label': 'Todos', 'value': 0 })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async cargarActandUsers () {
      await this.getActividadesFiltro()
      await this.getUsuariosInvolucrados()
    },
    async cargarActandUsers2 () {
      await this.getActividadesOpt()
      await this.getUsuariosInvolucrados2()
    },
    selectActividades () {
      this.$v.form_gastos.$reset()
      this.form_gastos.fields.id = 0
      this.form_gastos.fields.actividad_id = 0
      this.form_gastos.fields.presupuesto_disponible = 0
      this.form_gastos.fields.presupuesto_disponible_validar = 0
      this.form_gastos.fields.presupuesto_disponible_real = 0
      this.form_gastos.fields.presupuesto_real = 0
      this.form_gastos.fields.nombre_actividad = ''
      this.form_gastos.fields.nombre_proveedor = ''
      this.form_gastos.fields.monto = 0.00
      this.form_gastos.fields.montovalidar = 0
      this.form_gastos.fields.proveedor_id = 0
      this.form_gastos.fields.descripcion_gasto = ''
      this.form_gastos.fields.subtotal = 0
      this.form_gastos.fields.total = 0
      this.incluir_iva = false
      this.form_solicitudes.fields.iva = 'NO'
      this.comprobar_gasto = false
      this.form_gastos.fields.comprobado = 'NO'
    },
    currencyFormat (num) {
      return Number.parseFloat(num).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
    },
    presupuestoActividad () {
      this.getPresupuestoActividad({id: this.form_gastos.fields.actividad_id}).then(({data}) => {
        if (data.result === 'success') {
          if (data.actividades[0].cantidad_disponible < 0) {
            this.form_gastos.fields.presupuesto_disponible = 0
            this.form_gastos.fields.presupuesto_disponible_real = 0
          } else {
            this.form_gastos.fields.presupuesto_disponible = this.currencyFormat(data.actividades[0].cantidad_disponible)
            this.form_gastos.fields.presupuesto_disponible_real = this.currencyFormat(data.actividades[0].cantidad_real)
          }
          this.form_gastos.fields.presupuesto_disponible_validar = data.actividades[0].cantidad_disponible
          this.form_gastos.fields.presupuesto_disponible_validar_real = data.actividades[0].cantidad_real
          this.form_gastos.fields.presupuesto_real = this.currencyFormat(data.actividades[0].costo)
          this.form_gastos.fields.presupuesto_real_validar = data.actividades[0].costo
          this.form_gastos.fields.nombre_actividad = data.actividades[0].nombre
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async newSolicitud (id) {
      this.$q.loading.show({message: 'Cargando...'})
      this.$v.form_gastos.$reset()
      this.form_solicitudes.fields.id = 0
      this.form_solicitudes.fields.total = 0
      this.form_solicitudes.fields.iva = 'SI'
      this.form_solicitudes.fields.fecha_solicitado = ''
      this.form_solicitudes.fields.fecha_ejercido = ''
      this.form_solicitudes.fields.comprobado = 'NO'
      this.form_solicitudes.fields.factura = 'NO'
      this.form_solicitudes.fields.total = 0
      this.form_solicitudes.fields.subtotal = 0
      this.form_solicitudes.fields.totalcopia = 0
      this.factura_solicitud = false
      this.incluir_iva = true
      this.comprobar_solicitud = false
      this.comprobar_gasto = false
      this.form_gastos.fields.comprobado = 'NO'
      this.form_gastos.fields.id = 0
      this.form_gastos.fields.solicitud_id = 0
      this.form_gastos.fields.monto = 0.00
      this.form_gastos.fields.montovalidar = 0
      this.form_gastos.fields.montocopia = 0.00
      this.form_gastos.fields.fecha_ejercido = ''
      this.form_gastos.fields.proveedor_id = 0
      this.form_gastos.fields.tipo = 0
      this.form_gastos.fields.descripcion_gasto = ''
      this.form_gastos.fields.actividad_id = 0
      this.form_gastos.fields.presupuesto_real = 0
      this.form_gastos.fields.presupuesto_disponible = 0
      this.form_gastos.fields.presupuesto_disponible_validar = 0
      this.form_gastos.fields.presupuesto_disponible_real = 0
      this.form_gastos.numero_gastos_pagados = 0 // cuantos gastos estan pagados???
      this.form_gastos.data = []
      this.form_solicitudes.mensajes = []
      this.form_gastos.notas = []
      this.form_solicitudes.fields.colaborador = true
      this.form_solicitudes.fields.solicitante = false
      this.form_solicitudes.fields.autorizador = false
      this.form_solicitudes.fields.pagos = false
      this.form_solicitudes.fields.autorizacion_id = null
      this.form_solicitudes.fields.status = 'CREADO'
      this.form_solicitudes.solicitudes_proyecto = true
      this.form_solicitudes.bandera_iva = 0
      this.form_solicitudes.bandera_comprobado = 0
      this.form_solicitudes.fields.empresa_id = 0
      this.form_solicitudes.fields.subempresa_id = 0
      this.form_solicitudes.fields.proveedor_id = 0
      this.banco1 = false
      this.banco2 = false
      this.toka = false
      this.form_solicitudes.fields.banco1 = 'NO'
      this.form_solicitudes.fields.banco2 = 'NO'
      this.form_solicitudes.fields.toka = 'NO'
      this.form_solicitudes.fields.sobrepasa_presupuesto = 'NO'
      this.ver_detalles_proveedor = false
      await this.getEmpresas()
      await this.getProveedores()
      await this.getProyectosOpt()
      this.form_solicitudes.fields.proyecto_id = id
      this.$q.loading.hide()
    },
    createSolicitud () {
      this.$q.loading.show({message: 'Cargando...'})
      if (this.form_solicitudes.fields.proyecto_id > 0) {
        if (this.form_solicitudes.fields.empresa_id > 0) {
          if (this.form_solicitudes.fields.proveedor_id > 0) {
            if (this.form_solicitudes.fields.banco1 !== 'NO' || this.form_solicitudes.fields.banco2 !== 'NO' || this.form_solicitudes.fields.toka !== 'NO') {
              let params = this.form_solicitudes.fields
              this.savenewSolicitud(params).then(({data}) => {
                this.form_solicitudes.solicitudes_proyecto = false
                if (data.result === 'success') {
                  this.form_gastos.fields.solicitud_id = data.id
                  this.form_solicitudes.fields.solicitante = data.solicitante
                  this.form_solicitudes.fields.id = data.id
                  this.form_solicitudes.fields.status = 'CREADO'
                  this.$q.notify({
                    message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'done', position: 'top-right'
                  })
                  this.getActividadesOpt()
                  this.form_gastos.fields.proveedor_id = this.form_solicitudes.fields.proveedor_id
                  this.setView('update')
                }
              }).catch(error => {
                console.error(error)
                this.form_solicitudes.loading = false
              })
            } else {
              this.$showMessage('¡Advertencia!', 'Debe elegir una cuenta para el pago al proveedor')
            }
          } else {
            this.$showMessage('¡Advertencia!', 'Debe elegir una proveedor')
          }
        } else {
          this.$showMessage('¡Advertencia!', 'Debe elegir una empresa')
        }
      } else {
        this.$showMessage('¡Advertencia!', 'Debe elegir un presupuesto')
      }
      this.$q.loading.hide()
    },
    limpiar_gasto () {
      this.form_gastos.fields.id = 0
      this.form_gastos.fields.monto = 0.00
      this.form_gastos.fields.montovalidar = 0.00
      this.form_gastos.fields.tipo = 0
      this.form_gastos.fields.fecha_ejercido = ''
      this.form_gastos.fields.descripcion_gasto = ''
      this.form_gastos.fields.actividad_id = 0
      if (this.form_solicitudes.fields.proveedor_id > 0) {
        this.form_gastos.fields.proveedor_id = this.form_solicitudes.fields.proveedor_id
      } else {
        // this.form_gastos.fields.proveedor_id = 0
      }
      this.form_gastos.fields.presupuesto_real = 0
      this.form_gastos.fields.presupuesto_disponible = 0
      this.form_gastos.fields.presupuesto_disponible_validar = 0
      this.form_gastos.fields.presupuesto_disponible_real = 0
      this.form_gastos.fields.nombre_actividad = ''
      this.form_gastos.fields.nombre_proveedor = ''
      this.form_gastos.fields.comprobado = ''
      this.comprobar_gasto = false
      this.form_gastos.fields.comprobado = 'NO'
      this.form_gastos.fields.sumatoria_factura = 0
      this.$v.form_gastos.$reset()
    },
    agregarGasto () {
      this.$v.form_notas.$touch()
      if (!this.$v.form_notas.$error) {
        let params = this.form_gastos.fields
        params.sobrepasa_presupuesto = this.form_solicitudes.fields.sobrepasa_presupuesto
        params.notas = this.form_notas.fields.nota
        this.saveGasto(params).then(({data}) => {
          if (data.result === 'success') {
            this.$q.notify({
              message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'done', position: 'top-right'
            })
            this.form_solicitudes.fields.total = data.total
            this.form_solicitudes.fields.subtotal = data.total
            this.form_solicitudes.fields.totalcopia = data.totalcopia
            if (this.form_solicitudes.fields.iva === 'SI') {
              this.form_solicitudes.fields.total = parseFloat(this.form_solicitudes.fields.subtotal) + parseFloat(this.form_solicitudes.fields.subtotal * 0.16)
            }
            this.form_solicitudes.fields.totalcopia = this.currencyFormat(this.form_solicitudes.fields.total)
            this.form_solicitudes.fields.sobrepasa_presupuesto = 'NO'
            this.form_solicitudes.modal_notas = false
            this.limpiar_gasto()
            this.cargarGastos()
            //
            //
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
    validarGastos () {
      this.getTiposGastos()
      this.form_gastos.fields.monto = this.evaluaMonto(this.form_gastos.fields.montovalidar)
      this.$v.form_gastos.$touch()
      if (!this.$v.form_gastos.$error) {
        if ((parseFloat(this.form_gastos.fields.presupuesto_disponible_validar) < parseFloat(this.form_gastos.fields.monto)) || (parseFloat(this.form_gastos.fields.presupuesto_real_validar - this.form_gastos.fields.presupuesto_disponible_validar_real) < parseFloat(this.form_gastos.fields.monto))) {
          this.$q.dialog({
            title: 'Alerta',
            message: 'Al agregar este gasto se sobrepasa el presupuesto asignado para esta subactividad, actividad y project, si continúa, la solicitud será creada pero no se garantiza su autorización y pago',
            ok: 'Aceptar',
            cancel: 'Cancelar'
          }).then(() => {
            this.form_solicitudes.fields.sobrepasa_presupuesto = 'SI'
            this.$v.form_notas.$reset()
            this.form_notas.fields.nota = ''
            this.form_solicitudes.modal_notas = true
            // this.agregarGasto()
          }).catch(() => {
            this.$q.notify('Cancelado')
          })
        } else {
          this.form_notas.fields.nota = 'XXXXXXXXXX'
          this.agregarGasto()
        }
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    deleteSingleGasto (row) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este gasto?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.form_solicitudes.fields.montocopia = this.currencyFormat(this.form_solicitudes.fields.total)
        this.deleteGasto({id: row.id, solicitud_id: this.form_gastos.fields.solicitud_id}).then(({data}) => {
          if (data.result === 'success') {
            this.$q.notify({
              message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'delete', position: 'top-right'
            })
            this.form_solicitudes.fields.total = data.total
            this.form_solicitudes.fields.subtotal = data.total
            this.form_solicitudes.fields.totalcopia = data.totalcopia
            this.limpiar_gasto()
            this.cargarGastos()
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
          if (this.form_solicitudes.fields.iva === 'SI') {
            this.form_solicitudes.fields.total = parseFloat(this.form_solicitudes.fields.subtotal) + parseFloat(this.form_solicitudes.fields.subtotal * 0.16)
          }
          this.form_solicitudes.fields.totalcopia = this.currencyFormat(this.form_solicitudes.fields.total)
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
    },
    editSingleGasto (row) {
      this.form_gastos.fields.id = row.id
      this.form_gastos.fields.proveedor_id = row.proveedor_id
      this.form_gastos.fields.actividad_id = row.actividad_id
      this.form_gastos.fields.monto = row.monto
      this.form_gastos.fields.montovalidar = row.monto
      this.presupuestoActividad()
      this.form_gastos.fields.tipo = row.tipo
      this.form_gastos.fields.descripcion_gasto = row.descripcion_gasto
      this.form_gastos.fields.fecha_ejercido = row.fecha_ejercido
      this.form_gastos.fields.comprobado = row.comprobado
      this.form_gastos.fields.clasificacion = row.clasificacion
      this.form_gastos.fields.objeto_gasto = row.objeto_gasto
      if (row.comprobado === true) {
        this.comprobar_gasto = true
      } else {
        this.comprobar_gasto = false
      }
      this.form_gastos.modificar = true
    },
    validar_actualizarGastos () {
      this.form_gastos.fields.monto = this.evaluaMonto(this.form_gastos.fields.montovalidar)
      this.$v.form_gastos.$touch()
      if (!this.$v.form_gastos.$error) {
        if ((parseFloat(this.form_gastos.fields.presupuesto_disponible_validar) < parseFloat(this.form_gastos.fields.monto))) {
          this.$q.dialog({
            title: 'Alerta',
            message: 'Al actualizar este gasto se sobrepasa el presupuesto asignado para esta subactividad, actividad y project, si continúa, el gasto será actualizado pero no se garantiza su autorización y pago',
            ok: 'Aceptar',
            cancel: 'Cancelar'
          }).then(() => {
            this.form_solicitudes.fields.sobrepasa_presupuesto = 'SI'
            this.$v.form_notas.$reset()
            this.form_notas.fields.nota = ''
            this.form_solicitudes.modal_notas = true
            // this.agregarGasto()
          }).catch(() => {
            this.$q.notify('Cancelado')
          })
        } else {
          this.form_notas.fields.nota = 'XXXXXXXXXX'
          this.actualizarGastos()
        }
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    actualizarGastos () {
      this.$v.form_notas.$touch()
      if (!this.$v.form_notas.$error) {
        let params = this.form_gastos.fields
        params.sobrepasa_presupuesto = this.form_solicitudes.fields.sobrepasa_presupuesto
        params.notas = this.form_notas.fields.nota
        this.updateGasto(params).then(({data}) => {
          if (data.result === 'success') {
            this.form_notas.fields.nota = ''
            this.form_solicitudes.modal_notas = false
            this.$q.notify({
              message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'done', position: 'top-right'
            })
            this.form_gastos.modificar = false
            this.form_solicitudes.fields.sobrepasa_presupuesto = 'NO'
            this.form_solicitudes.fields.total = data.total
            this.form_solicitudes.fields.subtotal = data.total
            this.form_solicitudes.fields.totalcopia = data.totalcopia
            if (this.form_solicitudes.fields.iva === 'SI') {
              this.form_solicitudes.fields.total = parseFloat(this.form_solicitudes.fields.subtotal) + parseFloat(this.form_solicitudes.fields.subtotal * 0.16)
            }
            this.form_solicitudes.fields.totalcopia = this.currencyFormat(this.form_solicitudes.fields.total)
            this.limpiar_gasto()
            this.cargarGastos()
          } else {
            this.$q.notify(data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    async verInformacion (row) {
      this.$q.loading.show({message: 'Cargando...'})
      this.form_gastos.fields.solicitud_id = row.id
      this.form_solicitudes.fields.id = row.id
      this.form_solicitudes.fields.status = row.status
      this.form_solicitudes.fields.fecha_solicitado = row.fecha_solicitado
      this.form_solicitudes.fields.autorizacion_id = row.autorizacion_id
      this.form_solicitudes.fields.fecha_creado = row.fecha_creado
      this.form_solicitudes.fields.proyecto_id = row.proyecto_id
      this.form_solicitudes.fields.creador = row.creador
      this.form_solicitudes.contador_bandera_comprobado = 0
      this.form_solicitudes.contador_bandera_iva = 0
      if (row.comprobado === 'SI') {
        this.comprobar_solicitud = true
      }
      if (row.comprobado === 'NO') {
        this.comprobar_solicitud = false
      }
      this.form_solicitudes.fields.comprobado = row.comprobado
      this.form_solicitudes.fields.nombre_proyecto = row.nombre_proyecto
      this.form_solicitudes.fields.responsable_solicitud = row.responsable_solicitud
      this.form_solicitudes.fields.total = row.total
      this.form_solicitudes.fields.subtotal = row.total
      this.form_solicitudes.fields.iva = row.iva
      this.form_solicitudes.fields.factura = row.factura
      this.form_solicitudes.fields.totalcopia = row.totalcopia
      if (row.iva === 'SI') {
        this.incluir_iva = true
      }
      if (row.iva === 'NO') {
        this.incluir_iva = false
      }
      if (row.factura === 'SI') {
        this.factura_solicitud = true
      }
      if (row.factura === 'NO') {
        this.factura_solicitud = false
      }
      this.form_solicitudes.contador_bandera_comprobado = 1
      this.form_solicitudes.contador_bandera_iva = 1
      this.form_solicitudes.contador_bandera_factura = 1
      this.form_solicitudes.bandera_factura = 0
      this.form_solicitudes.bandera_iva = 0
      this.form_solicitudes.bandera_comprobado = 0
      this.form_solicitudes.mensajes = []
      this.form_gastos.notas = []
      this.form_solicitudes.fields.colaborador = row.colaborador
      this.form_solicitudes.fields.solicitante = row.solicitante
      this.form_solicitudes.fields.autorizador = row.autorizador
      if (row.autorizador === true) {
        this.form_solicitudes.fields.orden = row.orden
        this.form_solicitudes.fields.alteracion = row.alteracion
        this.form_solicitudes.fields.autorizador_id = row.autorizador_id
      } else {
        this.form_solicitudes.fields.orden = 0
        this.form_solicitudes.fields.alteracion = ''
        this.form_solicitudes.fields.autorizador_id = 0
      }
      this.form_solicitudes.fields.pagos = row.pagos
      if (row.empresa_id === null) {
        this.form_solicitudes.fields.empresa_id = 0
      } else {
        this.form_solicitudes.fields.empresa_id = row.empresa_id
        await this.getSubempresas()
      }
      if (row.subempresa_id === null) {
        this.form_solicitudes.fields.subempresa_id = 0
      } else {
        this.form_solicitudes.fields.subempresa_id = row.subempresa_id
      }
      // await this.getConceptos()
      if (row.concepto_id === null) {
        this.form_solicitudes.fields.concepto_id = 0
      } else {
        this.form_solicitudes.fields.concepto_id = row.concepto_id
      }
      // await this.getSubconceptosByconcepto2()
      if (row.subconcepto_id === null) {
        this.form_solicitudes.fields.subconcepto_id = 0
      } else {
        this.form_solicitudes.fields.subconcepto_id = row.subconcepto_id
      }
      this.form_solicitudes.fields.fecha_ejercido = row.fecha_ejercido
      this.form_solicitudes.proveedor_id = row.proveedor_id
      this.form_gastos.fields.proveedor_id = row.proveedor_id
      console.log(this.form_solicitudes.fields.subconcepto_id)
      console.log(row.subconcepto_id)
      this.form_gastos.numero_gastos = 0
      this.form_gastos.numero_gastos_pagados = 0
      await this.getEmpresas()
      await this.cargarGastos()
      await this.getActividadesOpt()
      await this.getUsuariosInvolucrados2()

      if (this.form_solicitudes.fields.iva === 'SI') {
        this.form_solicitudes.fields.total = parseFloat(this.form_solicitudes.fields.subtotal) + parseFloat(this.form_solicitudes.fields.subtotal * 0.16)
        this.form_solicitudes.fields.totalcopia = this.currencyFormat(this.form_solicitudes.fields.total)
      }
      console.log(this.form_gastos.numero_gastos_pagados)
      if (row.proveedor_id === null) {
        this.form_solicitudes.fields.proveedor_id = 0
      } else {
        this.form_solicitudes.fields.proveedor_id = row.proveedor_id
      }
      if (row.banco1 === 'SI') {
        this.banco1 = true
        this.form_solicitudes.fields.banco1 = 'SI'
      } else {
        this.banco1 = false
        this.form_solicitudes.fields.banco1 = 'NO'
      }
      if (row.banco2 === 'SI') {
        this.banco2 = true
        this.form_solicitudes.fields.banco2 = 'SI'
      } else {
        this.banco2 = false
        this.form_solicitudes.fields.banco2 = 'NO'
      }
      if (row.toka === 'SI') {
        this.toka = true
        this.form_solicitudes.fields.toka = 'SI'
      } else {
        this.toka = false
        this.form_solicitudes.fields.toka = 'NO'
      }
      this.form_solicitudes.fields.sobrepasa_presupuesto = row.sobrepasa_presupuesto
      this.form_gastos.modal_informacion = false
      this.form_gastos.modal_notas = false
      // await this.getTiposGastos()
      /* if (((this.form_solicitudes.fields.colaborador && this.form_solicitudes.fields.solicitante) || (this.form_solicitudes.fields.colaborador && !this.form_solicitudes.fields.solicitante)) && this.form_solicitudes.fields.autorizacion_id === null) {
        this.setView('update2')
      } else {
        this.setView('update')
      } */
      this.setView('update')
      this.$q.loading.hide()
    },
    async clickSolicitud (row) {
      this.$q.loading.show({message: 'Cargando...'})
      this.form_solicitudes.modal_notas = false
      this.form_gastos.fields.solicitud_id = row.id
      this.form_solicitudes.fields.id = row.id
      this.form_solicitudes.fields.status = row.status
      this.form_solicitudes.fields.fecha_solicitado = row.fecha_solicitado
      this.form_solicitudes.fields.fecha_ejercido = row.fecha_ejercido
      this.form_solicitudes.fields.autorizacion_id = row.autorizacion_id
      this.form_solicitudes.fields.fecha_creado = row.fecha_creado
      this.form_solicitudes.fields.proyecto_id = row.proyecto_id
      this.form_solicitudes.fields.creador = row.creador
      this.form_solicitudes.contador_bandera_comprobado = 0
      if (row.comprobado === 'SI') {
        this.comprobar_solicitud = true
      }
      if (row.comprobado === 'NO') {
        this.comprobar_solicitud = false
      }
      this.form_solicitudes.fields.comprobado = row.comprobado
      this.form_solicitudes.fields.nombre_proyecto = row.nombre_proyecto
      this.form_solicitudes.fields.responsable_solicitud = row.responsable_solicitud
      this.form_solicitudes.fields.total = row.total
      this.form_solicitudes.fields.subtotal = row.total
      this.form_solicitudes.fields.iva = row.iva
      this.form_solicitudes.fields.factura = row.factura
      this.form_solicitudes.fields.totalcopia = row.totalcopia
      if (row.iva === 'SI') {
        this.incluir_iva = true
      }
      if (row.iva === 'NO') {
        this.incluir_iva = false
      }
      if (row.factura === 'SI') {
        this.factura_solicitud = true
      }
      if (row.factura === 'NO') {
        this.factura_solicitud = false
      }
      this.form_solicitudes.mensajes = []
      this.form_gastos.notas = []
      this.form_solicitudes.fields.colaborador = row.colaborador
      this.form_solicitudes.fields.solicitante = row.solicitante
      this.form_solicitudes.fields.autorizador = row.autorizador
      if (row.autorizador === true) {
        this.form_solicitudes.fields.orden = row.orden
        this.form_solicitudes.fields.alteracion = row.alteracion
        this.form_solicitudes.fields.autorizador_id = row.autorizador_id
      } else {
        this.form_solicitudes.fields.orden = 0
        this.form_solicitudes.fields.alteracion = ''
        this.form_solicitudes.fields.autorizador_id = 0
      }
      this.form_solicitudes.fields.pagos = row.pagos
      if (row.empresa_id === null) {
        this.form_solicitudes.fields.empresa_id = 0
      } else {
        this.form_solicitudes.fields.empresa_id = row.empresa_id
        await this.getSubempresas()
      }
      if (row.subempresa_id === null) {
        this.form_solicitudes.fields.subempresa_id = 0
      } else {
        this.form_solicitudes.fields.subempresa_id = row.subempresa_id
      }
      if (row.proveedor_id === null) {
        this.form_solicitudes.fields.proveedor_id = 0
      } else {
        this.form_solicitudes.fields.proveedor_id = row.proveedor_id
      }
      if (row.banco1 === 'SI') {
        this.banco1 = true
        this.form_solicitudes.fields.banco1 = 'SI'
      } else {
        this.banco1 = false
        this.form_solicitudes.fields.banco1 = 'NO'
      }
      if (row.banco2 === 'SI') {
        this.banco2 = true
        this.form_solicitudes.fields.banco2 = 'SI'
      } else {
        this.banco2 = false
        this.form_solicitudes.fields.banco2 = 'NO'
      }
      if (row.toka === 'SI') {
        this.toka = true
        this.form_solicitudes.fields.toka = 'SI'
      } else {
        this.toka = false
        this.form_solicitudes.fields.toka = 'NO'
      }
      // await this.getConceptos()
      if (row.concepto_id === null) {
        this.form_solicitudes.fields.concepto_id = 0
      } else {
        this.form_solicitudes.fields.concepto_id = row.concepto_id
      }
      // await this.getSubconceptosByconcepto2()
      if (row.subconcepto_id === null) {
        this.form_solicitudes.fields.subconcepto_id = 0
      } else {
        this.form_solicitudes.fields.subconcepto_id = row.subconcepto_id
      }
      this.form_solicitudes.fields.sobrepasa_presupuesto = row.sobrepasa_presupuesto
      this.form_gastos.numero_gastos = 0
      this.form_gastos.numero_gastos_pagados = 0
      await this.getEmpresas()
      await this.cargarGastos()
      await this.getActividadesOpt()
      await this.getUsuariosInvolucrados2()

      if (this.form_solicitudes.fields.iva === 'SI') {
        this.form_solicitudes.fields.total = parseFloat(this.form_solicitudes.fields.subtotal) + parseFloat(this.form_solicitudes.fields.subtotal * 0.16)
        this.form_solicitudes.fields.totalcopia = this.currencyFormat(this.form_solicitudes.fields.total)
      }
      this.form_gastos.modal_informacion = false
      this.form_solicitudes.modal_informacion = true
      this.$q.loading.hide()
    },
    async cargarGastos () {
      this.form_gastos.loading = true
      this.form_gastos.data = []
      this.getGastosBySolicitud({id: this.form_solicitudes.fields.id}).then(({data}) => {
        if (data.gastos.length > 0) {
          this.form_gastos.numero_gastos = data.gastos.length // el numero total de gastos
          this.form_gastos.numero_gastos_pagados = data.numero_gastos_pagados
          this.form_gastos.numero_gastos_comprobados = data.numero_gastos_comprobados
          if (this.form_solicitudes.fields.iva === 'NO') {
            data.gastos.forEach(function (element) {
              element.iva = 0
              element.total = element.montocopia
            })
          }
          data.gastos.forEach(function (element) {
            if (element.comprobado === 'SI') {
              element.comprobado = true
            } else {
              element.comprobado = false
            }
            if (element.cotizacion.length > 0) {
              element.cotizacion.forEach(function (documento) {
                if (documento.doc_type === 'docx') {
                  documento.color = 'blue-9'
                  documento.icon = 'fas fa-file-word'
                } else if (documento.doc_type === 'pdf' || documento.doc_type === 'PDF') {
                  documento.color = 'red-10'
                  documento.icon = 'fas fa-file-pdf'
                } else if (documento.doc_type === 'xml' || documento.doc_type === 'XML' || documento.doc_type === 'txt' || documento.doc_type === 'TXT') {
                  documento.color = 'light-green'
                  documento.icon = 'fas fa-file-code'
                } else if (documento.doc_type === 'jpg' || documento.doc_type === 'jpeg' || documento.doc_type === 'png' || documento.doc_type === 'JPG' || documento.doc_type === 'JPEG' || documento.doc_type === 'PNG') {
                  documento.color = 'amber'
                  documento.icon = 'fas fa-file-image'
                }
              })
            }
            if (element.factura.length > 0) {
              element.factura.forEach(function (documento) {
                if (documento.doc_type === 'docx') {
                  documento.color = 'blue-9'
                  documento.icon = 'fas fa-file-word'
                } else if (documento.doc_type === 'pdf' || documento.doc_type === 'PDF') {
                  documento.color = 'red-10'
                  documento.icon = 'fas fa-file-pdf'
                } else if (documento.doc_type === 'xml' || documento.doc_type === 'XML' || documento.doc_type === 'txt' || documento.doc_type === 'TXT') {
                  documento.color = 'light-green'
                  documento.icon = 'fas fa-file-code'
                } else if (documento.doc_type === 'jpg' || documento.doc_type === 'jpeg' || documento.doc_type === 'png' || documento.doc_type === 'JPG' || documento.doc_type === 'JPEG' || documento.doc_type === 'PNG') {
                  documento.color = 'amber'
                  documento.icon = 'fas fa-file-image'
                }
              })
            }
            if (element.comprobante.length > 0) {
              element.comprobante.forEach(function (documento) {
                if (documento.doc_type === 'docx') {
                  documento.color = 'blue-9'
                  documento.icon = 'fas fa-file-word'
                } else if (documento.doc_type === 'pdf' || documento.doc_type === 'PDF') {
                  documento.color = 'red-10'
                  documento.icon = 'fas fa-file-pdf'
                } else if (documento.doc_type === 'xml' || documento.doc_type === 'XML' || documento.doc_type === 'txt' || documento.doc_type === 'TXT') {
                  documento.color = 'light-green'
                  documento.icon = 'fas fa-file-code'
                } else if (documento.doc_type === 'jpg' || documento.doc_type === 'jpeg' || documento.doc_type === 'png' || documento.doc_type === 'JPG' || documento.doc_type === 'JPEG' || documento.doc_type === 'PNG') {
                  documento.color = 'amber'
                  documento.icon = 'fas fa-file-image'
                }
              })
            }
          })
          this.form_gastos.data = data.gastos
        }
        this.form_gastos.loading = false
        console.log('numero de gastos pagados: ' + this.form_gastos.numero_gastos_pagados)
        console.log('numero de gastos: ' + this.form_gastos.numero_gastos)
        console.log('status: ' + this.form_solicitudes.fields.status)
        if ((this.form_gastos.numero_gastos_pagados === this.form_gastos.numero_gastos && this.form_gastos.numero_gastos !== 0 && this.form_solicitudes.fields.status === 'PAGO PARCIAL') || (this.form_gastos.numero_gastos_pagados === this.form_gastos.numero_gastos && this.form_gastos.numero_gastos === 1 && this.form_solicitudes.fields.status === 'POR PAGAR')) {
          this.pagar()
        } else {
          this.pagoParcial()
        }
        //
        if (this.form_gastos.numero_gastos > 0 && this.form_solicitudes.fields.status === 'CREADO') {
          this.solicitar()
        }
        //
      }).catch(error => {
        console.log(error)
        this.form_gastos.loading = false
      })
    },
    ocultarDetalles () {
      this.limpiar_gasto()
      this.form_gastos.modificar = false
    },
    async cargarArchivos () {
      this.form_archivos.loading = true
      this.form_archivos.data = []
      await this.archivos({solicitud_id: this.form_solicitudes.fields.id}).then(({data}) => {
        if (data.archivos_solicitudes.length > 0) {
          this.form_archivos.data = data.archivos_solicitudes
        }
        this.form_archivos.loading = false
      }).catch(error => {
        console.log(error)
        this.form_archivos.loading = false
      })
    },
    uploadFormato () {
      try {
        this.loadingButton = true
        let file = this.$refs.fileInputFormato.files[0]
        let formData = new FormData()
        console.log(file.name)
        // this.form_bases.fields.name2 = file.name
        formData.append('file', file)
        formData.append('formato_requisito_id', 0)
        formData.append('nombre', file.name)
        formData.append('solicitud_id', this.form_solicitudes.fields.id)
        if (file === null || file === undefined) {
          this.loadingButton = false
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document' || file.type.split('/')[1].toLowerCase() === 'plain') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('archivos_solicitudes/uploadArchivo', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              if (response.data.result === 'success') {
                this.$q.notify({
                  message: 'Se ha cargado el archivo', timeout: 3000, type: 'green', textColor: 'white', icon: 'done', position: 'top-right'
                })
                this.cargarArchivos()
                // this.form_bases.fields.observaciones_archivo = ''
              } else {
                if (response.data.err !== '') {
                  console.log(file.type.split('/')[1].toLowerCase())
                  this.$q.notify('Errores en archivo')
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
            console.log(file.type.split('/')[1].toLowerCase())
          }
        } else {
          this.loadingButton = false
          this.$showMessage('Error', 'No ha seleccionado un archivo con la extensión .jpg, .jpeg, .png, .pdf, .xml')
        }
      } catch (error) {
        this.loadingButton = false
      }
    },
    descargarFormato (row) {
      let uri = process.env.API + `archivos_solicitudes/getFile/${row.documento_id}/${row.doc_type}`
      window.open(uri, '_blank')
    },
    deleteFormato (row) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este formato de requisitos?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.eliminarArchivo({id: row.id}).then(({data}) => {
          if (data.result === 'success') {
            this.$q.notify({
              message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'delete', position: 'top-right'
            })
            this.cargarArchivos()
          } else {
            this.$q.notify(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.log(error)
        })
      }).catch(() => {})
    },
    actualizarSolicitud () {
      let params = this.form_solicitudes.fields
      this.updateSolicitud(params).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: 'Se han guardado los cambios', timeout: 3000, type: 'green', textColor: 'white', icon: 'done', position: 'top-right'
          })
          // this.cargarGastos()
        }
      }).catch(error => {
        console.error(error)
      })
    },
    regresar () {
      this.form_solicitudes.fields.status = ''
      this.filtrar()
      this.form_gastos.modificar = false
      this.limpiar_gasto()
      this.limpiar_comentario()
      this.form_solicitudes.mensajes = []
      this.form_gastos.notas = []
      this.form_gastos.modal_informacion = false
      this.setView('grid')
    },
    limpiar_comentario () {
      this.$v.form_comentarios.$reset()
      this.form_comentarios.fields.descripcion = ''
      this.form_comentarios.fields.usuario_destino = 0
    },
    agregarComentario () {
      this.$v.form_comentarios.$touch()
      if (!this.$v.form_comentarios.$error) {
        this.guardarComentario({solicitud_id: this.form_solicitudes.fields.id, mensaje: this.form_comentarios.fields.descripcion, user_destino: this.form_comentarios.fields.usuario_destino, proyecto: this.form_solicitudes.fields.proyecto_id}).then(({data}) => {
          if (data.result === 'success') {
            this.$q.notify({
              message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'done', position: 'top-right'
            })
            this.limpiar_comentario()
            this.cargarComentarios()
          } else {
            this.$q.notify(data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    solicitar () {
      // this.form_gastos.data.length>0
      this.$q.loading.show({message: 'Cargando...'})
      // this.$q.dialog({
      // title: 'Confirmar',
      // message: '¿Enviar solicitud a autorizadores?',
      // ok: 'Aceptar',
      // cancel: 'Cancelar'
      // }).then(() => {
      if (this.form_solicitudes.fields.sobrepasa_presupuesto === 'SI') {
        this.$v.form_notas.$reset()
        this.form_notas.fields.nota = ''
        this.form_solicitudes.modal_notas = true
      } else {
        this.metodo_solicitar()
      }
      // }).catch(() => {})
      this.$q.loading.hide()
    },
    valida_solicitar () {
      this.$v.form_notas.$touch()
      if (!this.$v.form_notas.$error) {
        this.metodo_solicitar()
        this.form_solicitudes.modal_notas = false
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    metodo_solicitar () {
      this.$q.loading.show({message: 'Cargando...'})
      this.form_solicitudes.fields.status = 'POR AUTORIZAR'
      this.form_solicitudes.fields.autorizacion_id = 1
      let params = this.form_solicitudes.fields
      params.nota = this.form_notas.fields.nota
      this.updateSolicitud(params).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'blue-10', textColor: 'white', icon: 'fas fa-check-circle', position: 'top-right'
          })
          //
          console.log(data)
          this.form_solicitudes.fields.autorizacion_id = 1
          this.form_solicitudes.fields.autorizador = data.autorizador
          if (data.autorizador === true) {
            this.form_solicitudes.fields.orden = data.orden
            this.form_solicitudes.fields.alteracion = data.alteracion
            this.form_solicitudes.fields.autorizador_id = data.autorizador_id
          }
          // this.regresar()
        }
      }).catch(error => {
        console.error(error)
      })
      this.$q.loading.hide()
    },
    async cargarComentarios () {
      this.form_solicitudes.mensajes = []
      console.log(this.form_solicitudes.fields.id)
      await this.mensajesBySolicitud({solicitud_id: this.form_solicitudes.fields.id}).then(({data}) => {
        if (data.mensajes.length > 0) {
          this.form_solicitudes.mensajes = data.mensajes
        }
      }).catch(error => {
        console.log(error)
      })
    },
    async cargarNotas () {
      this.form_gastos.notas = []
      await this.notasBySolicitud({solicitud_id: this.form_solicitudes.fields.id}).then(({data}) => {
        if (data.notas.length > 0) {
          this.form_gastos.notas = data.notas
        }
      }).catch(error => {
        console.log(error)
      })
    },
    selectedCotizacionDocumento (row) {
      this.$refs.cotizacionInput.value = ''
      this.registro_cotizacion = row
      this.$refs.cotizacionInput.click()
    },
    uploadCotizacionFile () {
      try {
        let file = this.$refs.cotizacionInput.files[0]
        let formData = new FormData()
        console.log(file.name)
        console.log(this.registro_cotizacion.id)
        console.log('aaaaaaaaa')
        console.log(file.type.split('/')[1].toLowerCase())
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('gasto_id', this.registro_cotizacion.id)
        formData.append('formato_requisito_id', 0)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document' || file.type.split('/')[1].toLowerCase() === 'plain') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('cotizaciones_gastos/uploadArchivo', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              console.log(response)
              if (response.data.result === 'success') {
                this.$q.notify({
                  message: 'Se ha cargado el archivo', timeout: 3000, type: 'green', textColor: 'white', icon: 'done', position: 'top-right'
                })
                this.cargarGastos()
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
    uploadCotizacion_id (file) {
      try {
        let formData = new FormData()
        console.log(file.name)
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('gasto_id', this.gasto_id_archivo)
        formData.append('formato_requisito_id', 0)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document' || file.type.split('/')[1].toLowerCase() === 'plain') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('cotizaciones_gastos/uploadArchivo', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              console.log(response)
              if (response.data.result === 'success') {
                this.$q.notify({
                  message: 'Se ha cargado el archivo', timeout: 3000, type: 'green', textColor: 'white', icon: 'done', position: 'top-right'
                })
                this.cargarGastos()
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
    cotizacionAccion (cotizacion) {
      this.$q.dialog({
        title: 'Confirmar acción',
        message: 'Se eliminará la cotización',
        ok: 'Eliminar',
        cancel: 'Cancelar',
        preventClose: true
      }).then(() => {
        this.borrarCotizacion(cotizacion)
      }).catch(() => {})
    },
    descargarCotizacion (id, ext) {
      let uri = process.env.API + `cotizaciones_gastos/getFile/${id}/${ext}`
      window.open(uri, '_blank')
    },
    borrarCotizacion (cotizacion) {
      let params = cotizacion
      this.deleteCotizacion(params).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'delete', position: 'top-right'
          })
          this.cargarGastos()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    selectedFacturaDocumento (row) {
      this.$refs.facturaInput.value = ''
      this.registro_factura = row
      this.$refs.facturaInput.click()
    },
    uploadFacturaFile () {
      try {
        let file = this.$refs.facturaInput.files[0]
        let formData = new FormData()
        console.log(file.name)
        console.log(this.registro_factura.id)
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('gasto_id', this.registro_factura.id)
        formData.append('formato_requisito_id', 0)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document' || file.type.split('/')[1].toLowerCase() === 'plain') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('facturas_gastos/uploadArchivo', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              console.log(response)
              if (response.data.result === 'success') {
                this.$q.notify({
                  message: 'Se ha cargado el archivo', timeout: 3000, type: 'green', textColor: 'white', icon: 'done', position: 'top-right'
                })
                this.cargarGastos()
              } else {
                if (response.data.result === 'error') {
                  this.$showMessage(response.data.message.title, response.data.message.content)
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
    uploadFactura_id (file) {
      try {
        let formData = new FormData()
        console.log(file.name)
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('gasto_id', this.gasto_id_archivo)
        formData.append('formato_requisito_id', 0)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document' || file.type.split('/')[1].toLowerCase() === 'plain') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('facturas_gastos/uploadArchivo', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              console.log(response)
              if (response.data.result === 'success') {
                this.$q.notify({
                  message: 'Se ha cargado el archivo', timeout: 3000, type: 'green', textColor: 'white', icon: 'done', position: 'top-right'
                })
                this.cargarGastos()
              } else {
                if (response.data.result === 'error') {
                  this.$showMessage(response.data.message.title, response.data.message.content)
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
    facturaAccion (factura) {
      this.$q.dialog({
        title: 'Confirmar acción',
        message: 'Se eliminará la factura',
        ok: 'Eliminar',
        cancel: 'Cancelar',
        preventClose: true
      }).then(() => {
        this.borrarFactura(factura)
      }).catch(() => {})
    },
    descargarFactura (id, ext) {
      let uri = process.env.API + `facturas_gastos/getFile/${id}/${ext}`
      window.open(uri, '_blank')
    },
    borrarFactura (factura) {
      let params = factura
      this.deleteFactura(params).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'delete', position: 'top-right'
          })
          this.cargarGastos()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    selectedComprobanteDocumento (row) {
      this.$refs.comprobanteInput.value = ''
      this.registro_comprobante = row
      this.$refs.comprobanteInput.click()
    },
    uploadComprobanteFile () {
      try {
        let file = this.$refs.comprobanteInput.files[0]
        let formData = new FormData()
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('gasto_id', this.registro_comprobante.id)
        formData.append('formato_requisito_id', 0)
        if (file === null || file === undefined) {
        } else if (file.name) {
          if (file.type === '' || file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document' || file.type.split('/')[1].toLowerCase() === 'plain') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('comprobantes_gastos/uploadArchivo', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              if (response.data.result === 'success') {
                this.$q.notify({
                  message: 'Se ha cargado el archivo', timeout: 3000, type: 'green', textColor: 'white', icon: 'done', position: 'top-right'
                })
                this.cargarGastos()
              } else {
                if (response.data.err !== '') {
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
          }
        } else {
          this.$showMessage('Error', 'No ha seleccionado un archivo con la extensión .jpg, .jpeg, .png, .pdf, .xml')
        }
      } catch (error) {
        console.log(error)
      }
    },
    uploadComprobante_id (file) {
      try {
        let formData = new FormData()
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('gasto_id', this.gasto_id_archivo)
        formData.append('formato_requisito_id', 0)
        if (file === null || file === undefined) {
        } else if (file.name) {
          if (file.type === '' || file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document' || file.type.split('/')[1].toLowerCase() === 'plain') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('comprobantes_gastos/uploadArchivo', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              if (response.data.result === 'success') {
                this.$q.notify({
                  message: 'Se ha cargado el archivo', timeout: 3000, type: 'green', textColor: 'white', icon: 'done', position: 'top-right'
                })
                this.cargarGastos()
              } else {
                if (response.data.err !== '') {
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
          }
        } else {
          this.$showMessage('Error', 'No ha seleccionado un archivo con la extensión .jpg, .jpeg, .png, .pdf, .xml')
        }
      } catch (error) {
        console.log(error)
      }
    },
    comprobanteAccion (comprobante) {
      this.$q.dialog({
        title: 'Confirmar acción',
        message: 'Se eliminará el comprobante',
        ok: 'Eliminar',
        cancel: 'Cancelar',
        preventClose: true
      }).then(() => {
        this.borrarComprobante(comprobante)
      }).catch(() => {})
    },
    descargarComprobante (id, ext) {
      // let ur = process.env.API + '/public/assets/solicitudes/comprobantes/' + id + '.' + ext
      let uri = process.env.API + `comprobantes_gastos/getFile/${id}/${ext}`
      window.open(uri, '_blank')
    },
    borrarComprobante (comprobante) {
      let params = comprobante
      this.deleteComprobante(params).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'delete', position: 'top-right'
          })
          this.cargarGastos()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    autorizar () {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Autorizar solicitud?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        if (this.form_solicitudes.fields.sobrepasa_presupuesto === 'SI' && this.form_solicitudes.fields.alteracion === 'SI') {
          this.$v.form_notas.$reset()
          this.form_notas.fields.nota = ''
          this.form_solicitudes.modal_notas = true
        } else {
          this.autorizar_solicitud()
        }
      }).catch(() => {})
    },
    validar_autorizar () {
      this.$v.form_notas.$touch()
      if (!this.$v.form_notas.$error) {
        this.autorizar_solicitud()
        this.form_solicitudes.modal_notas = false
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    autorizar_solicitud () {
      let params = this.form_solicitudes.fields
      params.nota = this.form_notas.fields.nota
      this.$q.loading.show({message: 'Cargando...'})
      this.autorizarSolicitud(params).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'blue-10', textColor: 'white', icon: 'fas fa-check-circle', position: 'top-right'
          })
          this.setView('grid')
          this.filtrar()
          this.$q.loading.hide()
        } else {
          this.$q.loading.hide()
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    limpiar_rechazar () {
      this.$v.form_rechazar.$reset()
      this.form_rechazar.fields.descripcion = ''
      this.form_rechazar.fields.nivel = 0
      this.form_rechazar.fields.orden = 0
    },
    rechazar () {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Rechazar solicitud?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.$v.form_rechazar.$touch()
        if (!this.$v.form_rechazar.$error) {
          this.rechazarSolicitud({solicitud_id: this.form_solicitudes.fields.id, mensaje: this.form_rechazar.fields.descripcion}).then(({data}) => {
            if (data.result === 'success') {
              this.$q.notify({
                message: data.message.content, timeout: 3000, type: 'orange-8', textColor: 'white', icon: 'clear', position: 'top-right'
              })
              this.limpiar_rechazar()
              this.filtrar()
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
      }).catch(() => {
        this.limpiar_rechazar()
        this.form_solicitudes.solicitudes_proyecto2 = false
      })
    },
    cancelSolicitud () {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Cancelar solicitud?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.cancelarSolicitud({solicitud_id: this.form_solicitudes.fields.id}).then(({data}) => {
          if (data.result === 'success') {
            this.$q.notify({
              message: data.message.content, timeout: 3000, type: 'orange-10', textColor: 'white', icon: 'highlight_off', position: 'top-right'
            })
            this.filtrar()
            this.setView('grid')
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
    },
    clickFila (row) {
      this.form_gastos.modal_informacion = false
      this.getGastoById({id: row.id}).then(({data}) => {
        if (data.result === 'success') {
          this.form_gastos.detalles = ''
          this.form_gastos.detalles = data.gasto[0]
          this.comprobar_gasto = this.form_gastos.detalles.comprobado
          this.form_gastos.fields.id = this.form_gastos.detalles.id
          if (this.form_gastos.detalles.cantidad_disponible < 0) {
            this.form_gastos.detalles.cantidad_disponible = 0
          }
        }
      }).catch(error => {
        console.error(error)
      })
      this.form_gastos.modal_informacion = true
    },
    pagar () {
      this.pagarSolicitud({id: this.form_solicitudes.fields.id, status: 'PAGADO'}).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'done_all', position: 'top-right'
          })
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    pagoParcial () {
      if (this.form_solicitudes.fields.status === 'POR PAGAR' && this.form_gastos.numero_gastos_pagados === 1 && this.form_gastos.numero_gastos > this.form_gastos.numero_gastos_pagados) {
        this.pagarSolicitud({id: this.form_solicitudes.fields.id, status: 'PAGO PARCIAL'}).then(({data}) => {
          if (data.result === 'success') {
            this.$q.notify({
              message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'done', position: 'top-right'
            })
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      }
    },
    pagoParcialSolicitud () {
      this.pagarSolicitud({id: this.form_solicitudes.fields.id, status: 'PAGO PARCIAL'}).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'done', position: 'top-right'
          })
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async pagarSingleGasto (row) {
      this.form_solicitudes.fields.sobrepasa_presupuesto = 'NO'
      this.form_pagar.fields.id = row.id
      this.form_pagar.fields.cantidad_solicitada = row.total
      this.form_pagar.fields.actividad_id = row.actividad_id
      this.form_pagar.fields.comision = row.comision
      this.form_pagar.fields.presupuesto_disponible = 0
      this.form_pagar.fields.presupuesto_real = 0
      this.form_pagar.fields.monto = 0
      this.$v.form_pagar.$reset()
      await this.presupuestoPagar()
      this.form_pagar.modal_pagar = true
    },
    pagarGasto () {
      if ((this.form_pagar.fields.monto === this.form_pagar.fields.cantidad_solicitada)) {
        if (parseFloat(this.evaluaMonto(this.form_pagar.fields.cantidad_solicitada)) > parseFloat(this.form_pagar.fields.presupuesto_disponible)) {
          this.cantidad_de_diferencia = parseFloat(this.evaluaMonto(this.form_pagar.fields.cantidad_solicitada)) - parseFloat(this.form_pagar.fields.presupuesto_disponible)
          this.$q.dialog({
            title: 'Alerta',
            message: 'Este gasto sobrepasa el presupuesto actual de la subactividad, actividad y project agregando $' + this.cantidad_de_diferencia + '. ¿Desea continuar con el pago?',
            ok: 'Aceptar',
            cancel: 'Cancelar'
          }).then(() => {
            this.pagarGastoS({id: this.form_pagar.fields.id, cantidad_diferencia: this.cantidad_de_diferencia, nota: this.form_notas.fields.nota, comision: this.form_pagar.fields.comision}).then(({data}) => {
              if (data.result === 'success') {
                this.form_pagar.modal_pagar = false
                this.cargarGastos()
                this.$q.notify({
                  message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'done', position: 'top-right'
                })
              } else {
                this.$showMessage(data.message.title, data.message.content)
              }
            }).catch(error => {
              console.error(error)
            })
          }).catch(() => {})
        } else {
          this.cantidad_de_diferencia = 0
          this.pagarGastoS({id: this.form_pagar.fields.id, cantidad_diferencia: this.cantidad_de_diferencia, nota: this.form_notas.fields.nota, comision: this.form_pagar.fields.comision}).then(({data}) => {
            if (data.result === 'success') {
              this.form_pagar.modal_pagar = false
              this.cargarGastos()
              this.$q.notify({
                message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'done', position: 'top-right'
              })
            } else {
              this.$showMessage(data.message.title, data.message.content)
            }
          }).catch(error => {
            console.error(error)
          })
        }
      } else {
        this.$showMessage('¡Advertencia!', 'La cantidad a pagar debe ser igual a la solicitada.')
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
    formatoMonto () {
      this.cadena2 = '' + this.form_gastos.fields.montovalidar
      this.cadena = this.cadena2.split('.')
      this.monto_a_pagar = ''
      for (var i = 0; i < this.cadena.length; i++) {
        this.monto_a_pagar = this.monto_a_pagar + this.cadena[i]
      }
      this.form_gastos.fields.montovalidar = this.monto_a_pagar
      this.form_gastos.fields.montovalidar = this.evaluaMonto(this.form_gastos.fields.montovalidar)
      this.form_gastos.fields.montovalidar = this.currencyFormat(this.form_gastos.fields.montovalidar)
    },
    async presupuestoPagar () {
      this.getPresupuestoActividadReal({id: this.form_pagar.fields.actividad_id}).then(({data}) => {
        if (data.result === 'success') {
          if (parseFloat(data.actividades[0].cantidad_disponible) < 0) {
            console.log('es 0')
            this.form_pagar.fields.presupuesto_disponible = 0
          } else {
            console.log('no es 0')
            this.form_pagar.fields.presupuesto_disponible = parseFloat(data.actividades[0].cantidad_disponible)
            console.log(this.form_pagar.fields.presupuesto_disponible)
            if (parseFloat(this.evaluaMonto(this.form_pagar.fields.cantidad_solicitada)) > this.form_pagar.fields.presupuesto_disponible) {
              this.form_solicitudes.fields.sobrepasa_presupuesto = 'SI'
              this.form_notas.fields.nota = ''
              this.cargarNotas()
            } else {
              // this.form_notas.fields.nota = 'aaaaaa'
            }
          }
          this.form_pagar.fields.presupuesto_real = data.actividades[0].costo
        }
      }).catch(error => {
        console.error(error)
      })
    },
    verFactura (factura) {
      /* this.vista_imagen = true
      if (factura.doc_type === 'pdf' || factura.doc_type === 'PDF') {
        this.form_gastos.url_pdf = process.env.API + '/public/assets/solicitudes/facturas/' + factura.id + '.' + factura.doc_type
      } else {
        this.form_gastos.url_img = process.env.API + '/public/assets/solicitudes/facturas/' + factura.id + '.' + factura.doc_type + '?' + Math.random()
      } */
      //
      var uri = process.env.API + '/public/assets/solicitudes/facturas/' + factura.id + '.' + factura.doc_type
      window.open(uri, '_blank')
    },
    verFactura2 (factura) {
      /* if (factura.doc_type === 'pdf' || factura.doc_type === 'PDF') {
        this.form_gastos.url_pdf = process.env.API + '/public/assets/solicitudes/facturas/' + factura.id + '.' + factura.doc_type
      } else {
        this.form_gastos.url_img = process.env.API + '/public/assets/solicitudes/facturas/' + factura.id + '.' + factura.doc_type + '?' + Math.random()
      }
      this.form_gastos.modal_archivo = true */
      var uri = process.env.API + '/public/assets/solicitudes/facturas/' + factura.id + '.' + factura.doc_type
      window.open(uri, '_blank')
    },
    verCotizacion (cotizacion) {
      /* this.vista_imagen = true
      if (cotizacion.doc_type === 'pdf' || cotizacion.doc_type === 'PDF') {
        this.form_gastos.url_pdf = process.env.API + '/public/assets/solicitudes/cotizaciones/' + cotizacion.id + '.' + cotizacion.doc_type
      } else {
        this.form_gastos.url_img = process.env.API + '/public/assets/solicitudes/cotizaciones/' + cotizacion.id + '.' + cotizacion.doc_type + '?' + Math.random()
      } */
      var uri = process.env.API + '/public/assets/solicitudes/cotizaciones/' + cotizacion.id + '.' + cotizacion.doc_type
      window.open(uri, '_blank')
    },
    verCotizacion2 (cotizacion) {
      /* if (cotizacion.doc_type === 'pdf' || cotizacion.doc_type === 'PDF') {
        this.form_gastos.url_pdf = process.env.API + '/public/assets/solicitudes/cotizaciones/' + cotizacion.id + '.' + cotizacion.doc_type
      } else {
        this.form_gastos.url_img = process.env.API + '/public/assets/solicitudes/cotizaciones/' + cotizacion.id + '.' + cotizacion.doc_type + '?' + Math.random()
      }
      this.form_gastos.modal_archivo = true */
      var uri = process.env.API + '/public/assets/solicitudes/cotizaciones/' + cotizacion.id + '.' + cotizacion.doc_type
      window.open(uri, '_blank')
    },
    verComprobante (comprobante) {
      /* this.vista_imagen = true
      if (comprobante.doc_type === 'pdf' || comprobante.doc_type === 'PDF') {
        this.form_gastos.url_pdf = process.env.API + '/public/assets/solicitudes/comprobantes/' + comprobante.id + '.' + comprobante.doc_type
      } else {
        this.form_gastos.url_img = process.env.API + '/public/assets/solicitudes/comprobantes/' + comprobante.id + '.' + comprobante.doc_type + '?' + Math.random()
      } */
      var uri = process.env.API + '/public/assets/solicitudes/comprobantes/' + comprobante.id + '.' + comprobante.doc_type
      window.open(uri, '_blank')
    },
    verComprobante2 (comprobante) {
      /* if (comprobante.doc_type === 'pdf' || comprobante.doc_type === 'PDF') {
        this.form_gastos.url_pdf = process.env.API + '/public/assets/solicitudes/comprobantes/' + comprobante.id + '.' + comprobante.doc_type
      } else {
        this.form_gastos.url_img = process.env.API + '/public/assets/solicitudes/comprobantes/' + comprobante.id + '.' + comprobante.doc_type + '?' + Math.random()
      }
      this.form_gastos.modal_archivo = true */
      var uri = process.env.API + '/public/assets/solicitudes/comprobantes/' + comprobante.id + '.' + comprobante.doc_type
      window.open(uri, '_blank')
    },
    desaparecer () {
      this.form_gastos.url_img = ''
      this.form_gastos.url_pdf = ''
      this.vista_imagen = false
      // document.getElementById('myModal').style.display = 'none'
    },
    desaparecer2 () {
      this.form_gastos.url_img = ''
      this.form_gastos.url_pdf = ''
      this.form_gastos.modal_archivo = false
      // document.getElementById('myModal').style.display = 'none'
    },
    comprobarGasto (gasto) {
      // if (gasto.total_verificar === gasto.sumatoria_factura || gasto.total_verificar !== gasto.sumatoria_factura) {
      if (gasto.comprobado_cadena === 'NO') {
        gasto.comprobado_cadena = 'SI'
      } else {
        gasto.comprobado_cadena = 'NO'
      }
      console.log(gasto.comprobado_cadena)
      this.comprobarGastoS({id: gasto.id, comprobar_gasto: gasto.comprobado_cadena}).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'done', position: 'top-right'
          })
          this.cargarGastos()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
      // } else {
    // this.$showMessage('Error', 'Para poder comprobar el gasto, el monto total de las facturas debe ser igual al total a pagar')
      // }
    },
    clickIva () {
      if (this.incluir_iva === true) {
        this.form_solicitudes.fields.iva = 'SI'
      } else {
        this.form_solicitudes.fields.iva = 'NO'
      }
      this.actualizarSolicitud()
    },
    clickComprobado () {
      if (this.form_solicitudes.fields.empresa_id > 0) {
        if (this.comprobar_solicitud === true) {
          this.form_solicitudes.fields.comprobado = 'SI'
        }
        if (this.comprobar_solicitud === false) {
          this.form_solicitudes.fields.comprobado = 'NO'
        }
        this.actualizarSolicitud()
      } else {
        this.$showMessage('Error', 'Para poder comprobar la solicitud, debe asignarle una empresa primero')
      }
    },

    clickErrorComprobado () {
      this.$showMessage('Error', 'Para poder comprobar la solicitud, debe comprobar todos los gastos')
    },
    clickErrorEmpresa () {
      this.$showMessage('Error', 'Para realizar cualquier acción, primero debe asignar una empresa a la solicitud')
    },
    actualizarEmpresa () {
      // console.log(this.form_solicitudes.fields.subempresa_id)
      this.updateSolicitudEmpresa({empresa_id: this.form_solicitudes.fields.empresa_id, subempresa_id: this.form_solicitudes.fields.subempresa_id, id: this.form_solicitudes.fields.id}).then(({data}) => {
        if (data.result === 'success') {
          this.form_solicitudes.fields.empresa_id = data.empresa_id
          this.form_solicitudes.fields.subempresa_id = data.subempresa_id
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
      this.getSubempresas()
    },
    clickCuenta1 () {
      if (this.banco1 === true) {
        this.banco2 = false
        this.toka = false
      }
    },
    clickCuenta2 () {
      if (this.banco2 === true) {
        this.banco1 = false
        this.toka = false
      }
    },
    clickCuenta3 () {
      if (this.toka === true) {
        this.banco2 = false
        this.banco1 = false
      }
    },
    cotizacion_id (row) {
      this.gasto_id_archivo = row.id
    },
    factura_id (row) {
      this.gasto_id_archivo = row.id
    },
    comprobante (row) {
      this.gasto_id_archivo = row.id
    },
    getSubempresas () {
      this.form_solicitudes.subempresasOptions = []
      this.getEmpresasByPadre({padre: this.form_solicitudes.fields.empresa_id}).then(({data}) => {
        if (data.subempresas.length > 0) {
          this.form_solicitudes.subempresasOptions.push({label: '---Seleccione---', value: 0})
          data.subempresas.forEach(subempresa => {
            this.form_solicitudes.subempresasOptions.push({label: subempresa.razon_social, value: subempresa.id})
          })
        } else {
          this.form_solicitudes.subempresasOptions.push({label: '---No hay subempresas---', value: 0})
        }
      }).catch(error => {
        console.error(error)
      })
    },
    see_details () {
      this.ver_detalles_proveedor = true
    },
    no_see_details () {
      this.ver_detalles_proveedor = false
    },
    newProveedor () {
      this.crear_nuevo_proveedor = true
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
        case 'toka':
          this.form.fields.toka = this.form.fields.toka.replace(/[^0-9.]/g, '')
          this.$v.form.fields.toka.$touch()
          break
        default:
          break
      }
    },
    save_new_proveedor () {
      this.$v.form_proveedor.$touch()
      if (!this.$v.form_proveedor.$error) {
        this.loadingButton = true
        let params = this.form_proveedor.fields
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
            this.crear_nuevo_proveedor = false
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
    clickFactura () {
      if (this.factura_solicitud === true) {
        this.form_solicitudes.fields.factura = 'SI'
      } else {
        this.form_solicitudes.fields.factura = 'NO'
      }
      this.actualizarSolicitud()
    },
    modalConcepto (gasto) {
      this.form_gastos.gasto = gasto
      if (this.form_gastos.gasto.concepto_id > 0) {
        this.getSubconceptosGasto()
      }
      this.getConceptos()
      this.form_gastos.modal_concepto = true
    },
    updateConceptoGasto () {
      let params = this.form_gastos.gasto
      this.updateConcepto(params).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'done', position: 'top-right'
          })
          this.form_gastos.modal_concepto = false
          this.cargarGastos()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    getSubconceptosByconceptoGasto () {
      this.getSubconceptos({concepto_id: this.form_gastos.gasto.concepto_id}).then(({data}) => {
        if (data.result === 'success') {
          this.subconceptosOptGastos = data.subconceptos
          this.subconceptosOptGastos.push({ 'label': 'Todos', 'value': 0 })
          this.form_gastos.gasto.subconcepto_id = 0
        }
      }).catch(error => {
        console.error(error)
      })
    },
    getSubconceptosGasto () {
      this.getSubconceptos({concepto_id: this.form_gastos.gasto.concepto_id}).then(({data}) => {
        if (data.result === 'success') {
          this.subconceptosOptGastos = data.subconceptos
          this.subconceptosOptGastos.push({ 'label': 'Todos', 'value': 0 })
        }
      }).catch(error => {
        console.error(error)
      })
    }
  },
  validations: {
    form_gastos: {
      fields: {
        monto: {required, minValue: minValue(1), maxValue: maxValue(1000000000)},
        actividad_id: {required, minValue: minValue(1)},
        proveedor_id: {required, minValue: minValue(1)},
        tipo: {required, minValue: minValue(1)}
      }
    },
    form_comentarios: {
      fields: {
        descripcion: {required}
      }
    },
    form_rechazar: {
      fields: {
        descripcion: {required}
      }
    },
    form_pagar: {
      fields: {
        monto: {required}
      }
    },
    form_clasificacion: {
      fields: {
        concepto_id: {minValue: minValue(1)},
        subconcepto_id: {minValue: minValue(1)}
      }
    },
    form_notas: {
      fields: {
        nota: {required}
      }
    },
    form_proveedor: {
      fields: {
        // curp: {maxLength: maxLength(18), minLength: minLength(18), curp},
        nombre_comercial: {required, maxLength: maxLength(100)},
        razon_social: {required, maxLength: maxLength(100)},
        // rfc: {minLength: minLength(12), maxLength: maxLength(13), rfc},
        direccion: {maxLength: maxLength(100)},
        clabe: {required, maxLength: maxLength(18), minLength: minLength(18), clabe},
        clabe2: {maxLength: maxLength(18), minLength: minLength(18), clabe},
        banco: {required, maxLength: maxLength(20), minLength: minLength(1)},
        banco2: {maxLength: maxLength(20), minLength: minLength(1)},
        telefono: {maxLength: maxLength(13)},
        contacto: {maxLength: maxLength(50)},
        toka: {maxLength: maxLength(16), minLength: minLength(16)},
        email: { maxLength: maxLength(100), email }
      }
    }
  }
}
</script>

<style scoped>

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

.modal-content2 {
  margin: auto;
  display: block;
  width: 100%;
  max-width: 100%;
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

@media only screen and (max-width: 100%){
  .modal-content2 {
    width: 100%;
  }
}
</style>
