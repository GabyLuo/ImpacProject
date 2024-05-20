<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Clientes"/>
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
            <div class="col q-pa-md ">
              <div class="col-sm-12" style="padding-bottom: 10px;">
                <q-search color="primary" v-model="form.filter"/>
              </div>
              <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
                <q-table id="sticky-table-newstyle"
                  :data="clientes"
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
                      <q-td key="codigo" :props="props">{{ props.row.codigo }}</q-td>
                      <q-td key="razon_social" :props="props" @click.native="clickRazonSocial(props.row)" style="cursor: pointer; text-align: left;">{{ props.row.razon_social }}</q-td>
                      <q-td key="estado_nombre" :props="props">{{ props.row.estado_nombre }}</q-td>
                      <q-td key="municipio_nombre" :props="props">{{ props.row.municipio_nombre }}</q-td>
                      <q-td key="status" :props="props">{{ props.row.status }}</q-td>
                      <q-td key="acciones" :props="props">
                        <q-btn small flat @click="addLogo(props.row)" color="green" icon="far fa-file-image">
                          <q-tooltip>Subir logo</q-tooltip>
                        </q-btn>
                        <q-btn small flat @click="editRow(props.row)" color="blue-6" icon="edit">
                          <q-tooltip>Editar</q-tooltip>
                        </q-btn>
                        <q-btn small flat @click="reporteEmpresas(props.row.id)" color="amber" icon="fas fa-building">
                          <q-tooltip>Empresas</q-tooltip>
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
      <!-- Modal agregar nuevo cliente -->
    <div v-if="views.create">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Clientes" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el label="Nuevo cliente"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
            </div>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md col-sm-12">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-2">
                <q-field icon="fas fa-key" icon-color="dark" :error="$v.form.fields.codigo.$error" error-label="Ingrese el código del cliente">
                  <q-input @keydown="alphaOnly" upper-case inverted color="dark" v-model="form.fields.codigo" type="text" float-label="Código" maxlength="4"/>
                </q-field>
              </div>
              <div class="col-sm-5">
                <q-field icon="person" icon-color="dark" :error="$v.form.fields.razon_social.$error" error-label="Ingrese la razon social">
                  <q-input upper-case inverted color="dark" v-model="form.fields.razon_social" type="text" float-label="Razón social" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-5">
                <q-field icon="person" icon-color="dark" :error="$v.form.fields.nombre_corto.$error" error-label="Nombre corto del cliente">
                  <q-input upper-case inverted color="dark" v-model="form.fields.nombre_corto" type="text" float-label="Nombre corto del cliente" maxlength="100" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs" v-if="form.fields.tipo === 'PRODUCTOS'">
              <div class="col-sm-2">
                <q-field icon="fas fa-id-card" icon-color="dark" :error="$v.form.fields.rfc.$error" error-label="RFC inválido">
                  <q-input upper-case v-model="form.fields.rfc" type="text" inverted color="dark" float-label="RFC" maxlength="13" />
                </q-field>
              </div>
              <!-- </div>
              <div class="row q-mt-lg"> -->
              <div class="col-sm-3">
                <q-field icon="fas fa-globe" icon-color="dark" :error="$v.form.fields.estado_id.$error" error-label="Elija un estado">
                  <q-select v-model="form.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()"></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-map-pin" icon-color="dark">
                  <q-select v-model="form.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="municipiosOptions" filter></q-select>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="fas fa-dollar-sign" icon-color="dark">
                  <q-select v-model="form.fields.precio" inverted color="dark" float-label="Precio de lista" :options="form.selectPrecio"></q-select>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="fas fa-info" icon-color="dark" :error="$v.form.fields.status.$error" error-label="Estatus">
                  <q-select v-model="form.fields.status" inverted color="dark" float-label="Estatus" :options="form.selectStatus"></q-select>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs" v-else>
              <div class="col-sm-2">
                <q-field icon="fas fa-id-card" icon-color="dark" :error="$v.form.fields.rfc.$error" error-label="RFC inválido">
                  <q-input upper-case v-model="form.fields.rfc" type="text" inverted color="dark" float-label="RFC" maxlength="13" />
                </q-field>
              </div>
              <!-- </div>
              <div class="row q-mt-lg"> -->
              <div class="col-sm-4">
                <q-field icon="fas fa-globe" icon-color="dark" :error="$v.form.fields.estado_id.$error" error-label="Elija un estado">
                  <q-select v-model="form.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()"></q-select>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="fas fa-map-pin" icon-color="dark">
                  <q-select v-model="form.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="municipiosOptions" filter></q-select>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="fas fa-info" icon-color="dark" :error="$v.form.fields.status.$error" error-label="Estatus">
                  <q-select v-model="form.fields.status" inverted color="dark" float-label="Estatus" :options="form.selectStatus"></q-select>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-2 offset-sm-10 pull-right">
                <q-btn @click="save()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
      <!-- Modal editar cliente -->
    <div v-if="views.update">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Clientes" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el :label="form.fields.codigo"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
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
          <div class="col q-pa-md col-sm-12">
            {{ form.fields.razon_social }}
          </div>
          <div class="col q-pa-md">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-2">
                <q-field icon="fas fa-key" icon-color="dark" :error="$v.form.fields.codigo.$error" error-label="Ingrese el código del cliente">
                  <q-input @keydown="alphaOnly" upper-case inverted color="dark" v-model="form.fields.codigo" type="text" float-label="Código" maxlength="4" :readonly="parseInt(form.fields.total_recursos) > 0 && form.fields.codigo_provisional!==''"/>
                </q-field>
              </div>
              <div class="col-sm-5">
                <q-field icon="person" icon-color="dark" :error="$v.form.fields.razon_social.$error" error-label="Ingrese la razon social">
                  <q-input upper-case inverted color="dark" v-model="form.fields.razon_social" type="text" float-label="Razón social" maxlength="100" />
                </q-field>
              </div>
              <div class="col-sm-5">
                <q-field icon="person" icon-color="dark" :error="$v.form.fields.nombre_corto.$error" error-label="Nombre corto del cliente">
                  <q-input upper-case inverted color="dark" v-model="form.fields.nombre_corto" type="text" float-label="Nombre corto del cliente" maxlength="100" />
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs" v-if="form.fields.tipo === 'PRODUCTOS'">
              <div class="col-sm-2">
                <q-field icon="fas fa-id-card" icon-color="dark" :error="$v.form.fields.rfc.$error" error-label="RFC inválido">
                  <q-input upper-case v-model="form.fields.rfc" type="text" inverted color="dark" float-label="RFC" maxlength="13" />
                </q-field>
              </div>
              <!-- </div>
              <div class="row q-mt-lg">-->
              <div class="col-sm-3">
                <q-field icon="fas fa-globe" icon-color="dark" :error="$v.form.fields.estado_id.$error" error-label="Elija un estado">
                  <q-select v-model="form.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()" :readonly="parseInt(form.fields.total_recursos) > 0 "></q-select>
                </q-field>
              </div>
              <div class="col-sm-3">
                <q-field icon="fas fa-map-pin" icon-color="dark">
                  <q-select v-model="form.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="municipiosOptions" filter :readonly="parseInt(form.fields.total_recursos) > 0"></q-select>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="fas fa-dollar-sign" icon-color="dark">
                  <q-select v-model="form.fields.precio" inverted color="dark" float-label="Precio de lista" :options="form.selectPrecio"></q-select>
                </q-field>
              </div>
              <div class="col-sm-2">
                <q-field icon="fas fa-info" icon-color="dark" :error="$v.form.fields.status.$error" error-label="Estatus">
                  <q-select v-model="form.fields.status" inverted color="dark" float-label="Estatus" :options="form.selectStatus"></q-select>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs" v-else>
              <div class="col-sm-2">
                <q-field icon="fas fa-id-card" icon-color="dark" :error="$v.form.fields.rfc.$error" error-label="RFC inválido">
                  <q-input upper-case v-model="form.fields.rfc" type="text" inverted color="dark" float-label="RFC" maxlength="13" />
                </q-field>
              </div>
              <!-- </div>
              <div class="row q-mt-lg">-->
              <div class="col-sm-4">
                <q-field icon="fas fa-globe" icon-color="dark" :error="$v.form.fields.estado_id.$error" error-label="Elija un estado">
                  <q-select v-model="form.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()" :readonly="parseInt(form.fields.total_recursos) > 0 "></q-select>
                </q-field>
              </div>
              <div class="col-sm-4">
                <q-field icon="fas fa-map-pin" icon-color="dark">
                  <q-select v-model="form.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="municipiosOptions" filter :readonly="parseInt(form.fields.total_recursos) > 0"></q-select>
                </q-field>
              </div>
              <!-- <div class="col-sm-2">
                <q-field icon="fas fa-dollar-sign" icon-color="dark">
                  <q-select v-model="form.fields.precio" inverted color="dark" float-label="Precio de lista" :options="form.selectPrecio"></q-select>
                </q-field>
              </div> -->
              <div class="col-sm-2">
                <q-field icon="fas fa-info" icon-color="dark" :error="$v.form.fields.status.$error" error-label="Estatus">
                  <q-select v-model="form.fields.status" inverted color="dark" float-label="Estatus" :options="form.selectStatus"></q-select>
                </q-field>
              </div>
            </div>
            <div class="row q-pa-md">
              DIRECCIONES
            </div>
            <div class="row justify-end">
              <q-btn @click="newDireccionRow()" class="btn_nuevo" icon="add">
                Nuevo
              </q-btn>
            </div>
            <div class="col q-pa-md">
              <div class="col q-pa-md border-panel">
                <div class="col-sm-12" style="padding-bottom: 10px;">
                  <q-search color="primary" v-model="form_direcciones.filter"/>
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
                      <q-td key="tipo_vialidad" :props="props">{{ props.row.tipo_vialidad }}</q-td>
                      <q-td key="calle" :props="props">{{ props.row.calle }}</q-td>
                      <q-td key="num_ext" :props="props">{{ props.row.num_ext }}</q-td>
                      <q-td key="num_int" :props="props">{{ props.row.num_int }}</q-td>
                      <q-td key="colonia" :props="props">{{ props.row.colonia }}</q-td>
                      <q-td key="poblacion" :props="props">{{ props.row.poblacion }}</q-td>
                      <q-td key="municipio_nombre" :props="props">{{ props.row.municipio_nombre }}</q-td>
                      <q-td key="estado_nombre" :props="props">{{ props.row.estado_nombre }}</q-td>
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
            <div class="row q-pa-md">
              REQUISITOS
            </div>
            <div class="col q-pa-md">
              <div class="col q-pa-md border-panel">
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
                      <q-td key="tipo" :props="props">{{ props.row.tipo }}</q-td>
                      <q-td key="acciones" :props="props">
                        <q-checkbox v-model="props.row.status" color="teal" @click.native="saveClientesRequisitos(props.row)"></q-checkbox>
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
    </div>

      <!--Crear una direccion-->

    <div v-if="views.create_direccion">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Clientes" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el label="Editar cliente" to="" @click.native="setView('update')"/>
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
                  <q-btn @click="setView('update')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
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
            {{ form.fields.razon_social }}
          </div>
          <div class="col q-pa-md col-sm-12">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-12">
                <q-field icon="fas fa-road" icon-color="dark">
                  <q-input upper-case maxlenght="100" v-model="form_direcciones.fields.tipo_vialidad" inverted color="dark" float-label="Tipo de vialidad"></q-input>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="fas fa-road" icon-color="dark" :error="$v.form_direcciones.fields.calle.$error" error-label="Ingrese el nombre de la vialidad">
                  <q-input upper-case v-model="form_direcciones.fields.calle" type="text" inverted color="dark" float-label="Nombre de la vialidad" maxlength="100" />
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
                  <q-select v-model="form_direcciones.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipiosDirecciones()"></q-select>
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
                <q-breadcrumbs-el label="Clientes" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el label="Editar cliente" to="" @click.native="setView('update')"/>
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
                  <q-btn @click="setView('update')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
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
            {{ form.fields.razon_social }}
          </div>
          <div class="col q-pa-md col-sm-12">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-12">
                <q-field icon="fas fa-road" icon-color="dark">
                  <q-input upper-case maxlenght="100" v-model="form_direcciones.fields.tipo_vialidad" inverted color="dark" float-label="Tipo de vialidad"></q-input>
                </q-field>
              </div>
            </div>
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="fas fa-road" icon-color="dark" :error="$v.form_direcciones.fields.calle.$error" error-label="Ingrese el nombre de la vialidad">
                  <q-input upper-case v-model="form_direcciones.fields.calle" type="text" inverted color="dark" float-label="Nombre de la vialidad" maxlength="100" />
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
                  <q-select v-model="form_direcciones.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipiosDirecciones()"></q-select>
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

      <!--Subir logo-->
    <div v-if="views.upload">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                <q-breadcrumbs-el label="Clientes" to="" @click.native="setView('grid')"/>
                <q-breadcrumbs-el label="Subir logo" to=""/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="col-xs-12 col-sm-12 col-md-12 pull-right" >
              <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
            </div>
          </div>
        </div>
      </div>

      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md col-sm-12">
            {{ form.fields.razon_social }}
          </div>
          <div class="col q-pa-md col-sm-12">
            <div class="row q-col-gutter-xs">
              <div class="col-sm-6">
                <q-field icon="fas fa-images" icon-color="dark">
                  <q-uploader color="primary" stack-label="Logotipo"
                  extensions=".jpg, .jpeg, .png" url=""
                  :url-factory="uploadFile"
                  />
                </q-field>
                <q-progress indeterminate color="warning" v-if="terminado"/>
              </div>
            </div>
            <div class="row q-col-gutter-xs" style="margin:auto;margin-top:20px;height:200px;" v-if="form.fields.logo !== ''" >
              <img id="myImg1" style="max-height:200px;max-width:200px;" :src="form.logo.url" @click="clickLogo()">
            </div>
          </div>
        </div>
      </div>
    </div>

      <div id="myModal" class="modal1" v-if="form.fields.logo !== ''">
        <span class="close1" @click="desaparecer()">&times;</span>
        <img id="img01" class="modal-content1">
      </div>

      <q-modal v-if="informacion" style="background-color: rgba(0,0,0,0.7);" v-model="informacion" :content-css="{width: '90vw', height: '600px'}">
        <q-modal-layout>
          <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
            <div class="col-sm-6">
              <q-toolbar-title>
                {{this.objetoInformacion.razon_social}} - {{ this.objetoInformacion.tipo}}
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
        </q-modal-layout>
        <div class="row q-mt-lg subtitulos" style="margin-left:15px; margin-top:70px;" v-if="objetoInformacion.logo!==''">
            Logotipo
        </div>
        <div class="row q-mt-lg" v-if="objetoInformacion.logo!==''">
            <div class="col-sm-4" style="text-align: center;margin-left: auto;margin-right: auto;" v-if="objetoInformacion.logo!==''">
                <img style="height:90%;width:90%;" :src="objetoInformacion.ruta">
            </div>
          </div>
        <div class="row q-mt-lg subtitulos" style="margin-left:15px;" v-if="objetoInformacion.logo!==''">
            Datos generales
        </div>
        <div class="row q-mt-lg subtitulos" style="margin-left:15px; margin-top:70px;" v-if="objetoInformacion.logo===''">
            Datos generales
        </div>
        <div class="row q-mt-lg">
            <div class="col-sm-4">
              <q-field icon="fas fa-key" icon-color="dark">
                <q-input readonly upper-case inverted-color="dark" v-model="this.objetoInformacion.codigo" type="text" float-label="Código"/>
              </q-field>
            </div>
            <div class="col-sm-8">
              <q-field icon="person" icon-color="dark">
                <q-input readonly upper-case inverted-color="dark" v-model="this.objetoInformacion.razon_social" type="text" float-label="Razón social"/>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right:15px;">
            <div class="col-sm-4">
              <q-field icon="person" icon-color="dark">
                <q-input readonly upper-case inverted-color="dark" v-model="this.objetoInformacion.nombre_corto" type="text" float-label="Nombre corto del cliente"/>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="fas fa-id-card" icon-color="dark">
                <q-input readonly upper-case v-model="this.objetoInformacion.rfc" type="text" inverted-color="dark" float-label="RFC"/>
              </q-field>
            </div>
            <div class="col-sm-4">
              <q-field icon="fas fa-info" icon-color="dark">
                <q-select readonly v-model="this.objetoInformacion.status" inverted-color="dark" float-label="Estatus" :options="form.selectStatus"></q-select>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg" style="margin-right:15px;">
            <div class="col-sm-6">
              <q-field icon="fas fa-globe" icon-color="dark">
                <q-select readonly v-model="this.objetoInformacion.estado_id" inverted-color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()"></q-select>
              </q-field>
            </div>
            <div class="col-sm-6">
              <q-field icon="fas fa-map-pin" icon-color="dark">
                <q-select readonly v-model="this.objetoInformacion.municipio_id" inverted-color="dark" float-label="Municipio" :options="municipiosOptions" filter></q-select>
              </q-field>
            </div>
          </div>
          <div class="row q-mt-lg subtitulos" style="margin-left:15px;">
            DIRECCIONES
          </div>
          <div class="row q-mt-sm"  style="margin-left:15px; margin-right:15px;">
            <div class="row" style="width:60vw;">
              <q-search hide-underline color="secondary" v-model="form_direcciones.filter"/>
            </div>
            <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
              <q-table id="sticky-table-corto"
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
                    <q-td key="tipo_vialidad" :props="props">{{ props.row.tipo_vialidad }}</q-td>
                    <q-td key="calle" :props="props">{{ props.row.calle }}</q-td>
                    <q-td key="num_ext" :props="props">{{ props.row.num_ext }}</q-td>
                    <q-td key="num_int" :props="props">{{ props.row.num_int }}</q-td>
                    <q-td key="colonia" :props="props">{{ props.row.colonia }}</q-td>
                    <q-td key="poblacion" :props="props">{{ props.row.poblacion }}</q-td>
                    <q-td key="municipio_nombre" :props="props">{{ props.row.municipio_nombre }}</q-td>
                    <q-td key="estado_nombre" :props="props">{{ props.row.estado_nombre }}</q-td>
                    <q-td key="cp" :props="props">{{ props.row.cp }}</q-td>
                    <q-td key="acciones" :props="props">
                    </q-td>
                  </q-tr>
                </template>
              </q-table>
              <q-inner-loading :visible="form_direcciones.loading">
                <q-spinner-dots size="64px" color="primary" />
              </q-inner-loading>
            </div>
          </div>
          <div class="row q-mt-lg subtitulos" style="margin-left:15px;">
            REQUISITOS
          </div>
          <div class="row q-mt-lg" style="margin-top:5px;margin-left:15px;margin-right:15px;">
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
                    <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                    <q-td key="tipo" :props="props">{{ props.row.tipo }}</q-td>
                    <q-td key="acciones" :props="props">
                      <q-checkbox readonly v-model="props.row.status" color="teal"></q-checkbox>
                    </q-td>
                  </q-tr>
                </template>
              </q-table>
              <q-inner-loading :visible="form_requisitos.loading">
                <q-spinner-dots size="64px" color="primary" />
              </q-inner-loading>
            </div>
          </div>
      </q-modal>

      <!-- Modal reporte de clientes -->
      <q-modal v-if="reporte_empresas" style="background-color: rgba(0,0,0,0.7);" v-model="reporte_empresas" :content-css="{width: '40vw', height: '40vh'}">
        <q-modal-layout>
          <q-toolbar slot="header" style="background-color: rgba(8,85,134,1)!important;">
            <div class="col-sm-9">
              <q-toolbar-title>
                Reporte de empresas
              </q-toolbar-title>
            </div>
            <div class="col-sm-3 pull-right">
              <q-btn
                flat
                round
                dense
                color="white"
                @click="reporte_empresas = false"
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
import { required, minLength, maxLength, minValue, helpers, numeric } from 'vuelidate/lib/validators'
import axios from 'axios'

const codigov = helpers.regex('codigov', /^[A-Z0-9Ñ]+$/)
const rfc = helpers.regex('rfc', /[A-ZÑ&]{3,4}\d{6}[A-V1-9][A-Z1-9][0-9A]/)

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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'INVENTARIOS'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'COBRANZA'.toUpperCase() || propiedades[i].toUpperCase() === 'PLANEACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS/COMISIONES'.toUpperCase()) {
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
      terminado: false,
      municipiosOptions: [],
      informacion: false,
      objetoInformacion: null,
      reporte_empresas: false,
      props: [],
      propsExpanded: ['Clientes'],
      views: {
        grid: true,
        create: false,
        update: false,
        upload: false,
        create_direccion: false,
        update_direccion: false
      },
      form: {
        fields: {
          id: 0,
          razon_social: '',
          nombre_corto: '',
          codigo: '',
          status: '',
          logo: '',
          estado_id: 0,
          municipio_id: 0,
          rfc: '',

          total_recursos: 0,
          codigo_provisional: '',
          tipo: '',
          precio: ''
        },
        // data: [],
        selectStatus: [ { 'label': 'ACTIVO', 'value': 'ACTIVO' }, { 'label': 'INACTIVO', 'value': 'INACTIVO' } ],
        selectPrecio: [ { 'label': 'A', 'value': 'A' }, { 'label': 'B', 'value': 'B' }, { 'label': 'C', 'value': 'C' }, { 'label': 'D', 'value': 'D' }, { 'label': 'E', 'value': 'E' } ],
        logo: {
          url: ''
        },
        columns: [
          { name: 'codigo', label: 'Código', field: 'codigo', sortable: true, type: 'string', align: 'left' },
          { name: 'razon_social', label: 'Razón social', field: 'razon_social', sortable: true, type: 'string', align: 'left' },
          { name: 'estado_nombre', label: 'Estado', field: 'estado_nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'municipio_nombre', label: 'Municipio', field: 'municipio_nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'status', label: 'Estatus', field: 'status', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_direcciones: {
        fields: {
          id: 0,
          cliente_id: 0,
          tipo_vialidad: '',
          calle: '',
          num_ext: '',
          num_int: '',
          colonia: '',
          poblacion: '',
          municipio_id: 0,
          estado_id: 0,
          cp: ''
        },
        municipiosOptions: [],
        // data: [],
        columns: [
          { name: 'tipo_vialidad', label: 'Vialidad', field: 'tipo_vialidad', sortable: true, type: 'string', align: 'left' },
          { name: 'calle', label: 'Nombre', field: 'calle', sortable: true, type: 'string', align: 'left' },
          { name: 'num_ext', label: 'Núm. Ext.', field: 'num_ext', sortable: true, type: 'string', align: 'left' },
          { name: 'num_int', label: 'Núm. Int.', field: 'num_int', sortable: true, type: 'string', align: 'left' },
          { name: 'colonia', label: 'Colonia', field: 'colonia', sortable: true, type: 'string', align: 'left' },
          { name: 'poblacion', label: 'Población', field: 'poblacion', sortable: true, type: 'string', align: 'left' },
          { name: 'municipio_nombre', label: 'Municipio', field: 'municipio_nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'estado_nombre', label: 'Estado', field: 'estado_nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'cp', label: 'C. P.', field: 'cp', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'center' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_requisitos: {
        requisitos: [],
        columns: [
          { name: 'nombre', label: 'Requisito', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'tipo', label: 'Tipo de archivo', field: 'tipo', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Requerido', field: 'acciones', sortable: false, type: 'string', align: 'center' }
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
      clientes: 'ventas/clientes/clientes',
      estadosOptions: 'vnt/estado/selectOptions'
    })
  },
  mounted () {
    this.loadAll()
  },
  watch: {
    informacion (newValue, old) {
      if (newValue === false) {
        this.objetoInformacion = null
      }
    }
  },
  methods: {
    ...mapActions({
      getClientes: 'ventas/clientes/refresh',
      saveCliente: 'ventas/clientes/save',
      updateCliente: 'ventas/clientes/update',
      deleteCliente: 'ventas/clientes/delete',
      getEstados: 'vnt/estado/refresh',
      getMunicipiosByEstado: 'vnt/municipio/getByEstado',

      getEmpresasByCliente: 'vnt/proveedor/getByCliente',

      getDirecciones: 'vnt/direccion/getByCliente',
      saveDireccion: 'vnt/direccion/save',
      updateDireccion: 'vnt/direccion/update',
      deleteDireccion: 'vnt/direccion/delete',

      getRequisitosByTramite: 'exp/requisitos/getByTramite',
      saveClienteRequisito: 'vnt/clientesRequisitos/save'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getClientes()
      await this.getEstados()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views.upload = false
      this.views.create_direccion = false
      this.views.update_direccion = false
      this.views[view] = true
    },
    cargaMunicipios () {
      this.municipiosOptions = []
      this.form.fields.municipio_id = 0
      this.getMunicipiosByEstado({estado_id: this.form.fields.estado_id}).then(({data}) => {
        this.municipiosOptions.push({label: '---Ninguno---', value: 0})
        if (data.municipios.length > 0) {
          data.municipios.forEach(municipio => {
            this.municipiosOptions.push({label: municipio.nombre, value: municipio.id})
          })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    save () {
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        this.saveCliente(params).then(({data}) => {
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
    update () {
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        this.updateCliente(params).then(({data}) => {
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
    async editRow (row) {
      this.$q.loading.show({message: 'Cargando...'})
      this.municipiosOptions = []
      let cliente = { ...row }
      this.form.fields.id = row.id
      this.form.fields.razon_social = cliente.razon_social
      this.form.fields.status = cliente.status
      this.form.fields.total_recursos = cliente.total_recursos
      if (cliente.codigo === null) {
        this.form.fields.codigo = ''
        this.form.fields.codigo_provisional = ''
      } else {
        this.form.fields.codigo = cliente.codigo
        this.form.fields.codigo_provisional = cliente.codigo
      }
      if (cliente.estado_id === null) {
        this.form.fields.estado_id = 0
        this.form.fields.municipio_id = 0
      } else {
        this.form.fields.estado_id = cliente.estado_id
        await this.cargaMunicipios()
        if (cliente.municipio_id === null) {
          this.form.fields.municipio_id = 0
        } else {
          this.form.fields.municipio_id = cliente.municipio_id
        }
      }
      if (cliente.rfc === null) {
        this.form.fields.rfc = ''
      } else {
        this.form.fields.rfc = cliente.rfc
      }
      if (cliente.nombre_corto === null) {
        this.form.fields.nombre_corto = ''
      } else {
        this.form.fields.nombre_corto = cliente.nombre_corto
      }
      this.form.fields.tipo = cliente.tipo
      this.form.fields.precio = cliente.precio_lista

      this.form_direcciones.fields.cliente_id = this.form.fields.id
      await this.cargarDirecciones()
      await this.cargarRequisitos()
      this.$q.loading.hide()
      this.setView('update')
    },
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este cliente?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete([id])
      }).catch(() => {})
    },
    delete (id) {
      let params = {id: id}
      this.deleteCliente(params).then(({data}) => {
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
      this.municipiosOptions = []
      this.form.fields.id = 0
      this.form.fields.razon_social = ''
      this.form.fields.status = ''
      this.form.fields.estado_id = 0
      this.form.fields.municipio_id = 0
      this.form.fields.codigo = ''
      this.form.fields.total_recursos = 0
      this.form.fields.codigo_provisional = ''
      this.form.fields.nombre_corto = ''
      this.form.fields.rfc = ''
      this.form.fields.precio = ''
      this.setView('create')
    },
    addLogo (row) {
      let logoCliente = { ...row }
      this.form.fields.id = row.id
      this.form.fields.razon_social = logoCliente.razon_social
      this.form.fields.logo = logoCliente.logo
      if (this.form.fields.logo !== '') {
        this.form.logo.url = process.env.API + '/public/assets/logos/' + logoCliente.logo + '?' + Math.random()
      }
      this.setView('upload')
    },
    uploadFile (file) {
      this.terminado = true
      if (file.type.split('/')[1] === 'jpg' || file.type.split('/')[1] === 'jpeg' || file.type.split('/')[1] === 'png') {
        let formData = new FormData()
        formData.append('file', file)
        formData.append('id', this.form.fields.id)
        axios.post('ventas/clientes/uploadLogo', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        }).then(response => {
          if (response.data.result === 'success') {
            this.terminado = false
            this.$q.notify({
            // only required parameter is the message:
              message: 'Se ha subido el logotipo',
              timeout: 3000,
              type: 'green',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'done',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.getClientes()
            this.setView('grid')
          } else {
            if (response.data.err !== '') {
              this.terminado = false
              this.$showMessage('Errores en archivo', response.data.err)
            }
          }
        }).catch(error => {
          this.terminado = false
          console.error(error)
        })
      } else {
        this.terminado = false
        this.$showMessage('Error', 'No ha seleccionado un archivo con las extensión .jpg, .jpeg o .png')
      }
    },
    clickLogo () {
      document.getElementById('img01').src = this.form.logo.url
      console.log(this.form.logo.url)
      document.getElementById('myModal').style.display = 'block'
    },
    desaparecer () {
      document.getElementById('myModal').style.display = 'none'
    },
    async clickRazonSocial (row) {
      this.informacion = true
      this.objetoInformacion = row
      this.objetoInformacion.ruta = process.env.API + '/public/assets/logos/' + row.logo + '?' + Math.random()
      this.$q.loading.show({message: 'Cargando...'})
      this.municipiosOptions = []
      let cliente = { ...row }
      this.form.fields.id = row.id
      this.form.fields.razon_social = cliente.razon_social
      this.form.fields.status = cliente.status
      this.form.fields.total_recursos = cliente.total_recursos
      if (cliente.codigo === null) {
        this.form.fields.codigo = ''
        this.form.fields.codigo_provisional = ''
      } else {
        this.form.fields.codigo = cliente.codigo
        this.form.fields.codigo_provisional = cliente.codigo
      }
      if (cliente.estado_id === null) {
        this.form.fields.estado_id = 0
        this.form.fields.municipio_id = 0
      } else {
        this.form.fields.estado_id = cliente.estado_id
        await this.cargaMunicipios()
        if (cliente.municipio_id === null) {
          this.form.fields.municipio_id = 0
        } else {
          this.form.fields.municipio_id = cliente.municipio_id
        }
      }
      if (cliente.rfc === null) {
        this.form.fields.rfc = ''
      } else {
        this.form.fields.rfc = cliente.rfc
      }
      if (cliente.nombre_corto === null) {
        this.form.fields.nombre_corto = ''
      } else {
        this.form.fields.nombre_corto = cliente.nombre_corto
      }
      this.form.fields.tipo = cliente.tipo

      this.form_direcciones.fields.cliente_id = this.form.fields.id
      await this.cargarDirecciones()
      await this.cargarRequisitos()
      this.$q.loading.hide()
    },
    alphaOnly (event) {
      let allowedKeys = [8, 37, 39, 46]
      let numeros = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 96]
      let letras = [192, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122]
      let key = event.keyCode
      if (!letras.includes(key) && !allowedKeys.includes(key) && !numeros.includes(key)) {
        event.preventDefault()
      }
    },
    reporteEmpresas (id) {
      this.props = []
      this.reporte_empresas = true
      this.getEmpresasByCliente({cliente_id: id}).then(({data}) => {
        this.props = data.resultado
        this.propsExpanded = ['Empresas']
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
    cargaMunicipiosDirecciones () {
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
    async cargarDirecciones () {
      this.form_direcciones.loading = true
      this.form_direcciones.data = []
      this.getDirecciones({cliente_id: this.form_direcciones.fields.cliente_id}).then(({data}) => {
        this.form_direcciones.loading = false
        if (data.direcciones.length > 0) {
          this.form_direcciones.data = data.direcciones
        }
      }).catch(error => {
        this.form_direcciones.loading = false
        console.error(error)
      })
    },
    newDireccionRow () {
      this.$v.form_direcciones.$reset()
      this.form_direcciones.fields.id = 0
      this.form_direcciones.fields.tipo_vialidad = ''
      this.form_direcciones.fields.calle = ''
      this.form_direcciones.fields.num_ext = ''
      this.form_direcciones.fields.num_int = ''
      this.form_direcciones.fields.colonia = ''
      this.form_direcciones.fields.poblacion = ''
      this.form_direcciones.fields.municipio_id = 0
      this.form_direcciones.fields.estado_id = 0
      this.form_direcciones.fields.cp = ''
      this.form_direcciones.municipiosOptions = []
      this.setView('create_direccion')
    },
    async editDireccionRow (row) {
      this.form_direcciones.municipiosOptions = []
      let direccion = { ...row }
      this.form_direcciones.fields.id = row.id
      this.form_direcciones.fields.tipo_vialidad = direccion.tipo_vialidad
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
        await this.cargaMunicipiosDirecciones()
        if (direccion.municipio_id === null) {
          this.form_direcciones.fields.municipio_id = 0
        } else {
          this.form_direcciones.fields.municipio_id = direccion.municipio_id
        }
      }
      this.form_direcciones.fields.cp = direccion.cp
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
            this.setView('update')
          } else {
            this.$showMessage(data.message.title, data.message.content)
          }
        }).catch(error => {
          console.error(error)
        })
      }).catch(() => {})
    },
    save_direccion () {
      this.form_direcciones.fields.calle = this.form_direcciones.fields.calle.trim()
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
            this.setView('update')
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
    update_direccion () {
      this.form_direcciones.fields.calle = this.form_direcciones.fields.calle.trim()
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
            this.setView('update')
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
    async cargarRequisitos () {
      this.form_requisitos.loading = true
      this.form_requisitos.data = []
      await this.getRequisitosByTramite({cliente_id: this.form.fields.id, tramite: 'PADRÓN DE PROVEEDORES'}).then(({data}) => {
        if (data.requisitos.length > 0) {
          this.form_requisitos.data = data.requisitos
        }
        this.form_requisitos.loading = false
      }).catch(error => {
        this.form_requisitos.loading = false
        console.error(error)
      })
    },
    async saveClientesRequisitos (row) {
      row.cliente_id = this.form.fields.id
      let params = row
      await this.saveClienteRequisito(params).then(({data}) => {
        this.$showMessage(data.message.title, data.message.content)
        this.cargarRequisitos()
      }).catch(error => {
        this.$showMessage('Error', 'Aún tiene registros relacionados')
        this.cargarRequisitos()
        console.error(error)
      })
    }
  },
  validations: {
    form: {
      fields: {
        razon_social: { required, maxLength: maxLength(100) },
        status: {required, maxLength: maxLength(10)},
        estado_id: {required, minValue: minValue(1)},
        codigo: {required, maxLength: maxLength(4), codigov},
        nombre_corto: {required, maxLength: maxLength(100)},
        rfc: {minLength: minLength(12), maxLength: maxLength(13), rfc}
      }
    },
    form_direcciones: {
      fields: {
        cp: {numeric, minLength: minLength(5), maxLength: maxLength(5)},
        calle: {required}
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
