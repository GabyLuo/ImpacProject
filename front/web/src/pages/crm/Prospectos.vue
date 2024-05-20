<template>
  <q-page>
    <div v-if="views.grid">
      <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Prospectos" />
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-6 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn class="btn_solicitar" icon="autorenew" @click="loadAll()" />
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
                    :data="prospectos"
                    :columns="form.columns"
                    :selected.sync="form.selected"
                    :filter="form.filter"
                    color="positive"
                    title=""
                    :dense="true"
                    class="bg-white"
                    :pagination.sync="form.pagination"
                    :loading="form.loading"
                    :rows-per-page-options="form.rowsOptions">
                    <template slot="body" slot-scope="props">
                      <q-tr :props="props">
                        <q-td key="nombre_compania" :props="props">{{ props.row.nombre_compania }}</q-td>
                        <q-td key="nombre_comercial" :props="props">{{ props.row.nombre_comercial }}</q-td>
                        <q-td key="razon_social" :props="props">{{ props.row.razon_social }}</q-td>
                        <q-td key="estado" :props="props">{{ props.row.estado }}</q-td>
                        <q-td key="municipio" :props="props">{{ props.row.municipio }}</q-td>
                        <q-td key="nombre_contacto" :props="props">{{ props.row.nombre_contacto }}</q-td>
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
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!--Crear un municipio-->

      <div v-if="views.create">
        <div class="q-pa-sm panel-header">
          <div class="row">
            <div class="col-sm-6">
              <div class="q-pa-sm q-gutter-sm">
                <q-breadcrumbs align="left">
                  <q-breadcrumbs-el label="" icon="home" to="/dashboard" />
                  <q-breadcrumbs-el label="Prospectos" to="" @click.native="setView('grid')"/>
                  <q-breadcrumbs-el label="Nuevo"/>
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
                <div class="col-sm-3">
                  <q-field icon="business" icon-color="dark">
                    <q-input upper-case v-model="form.fields.nombre_compania" type="text" inverted color="dark" float-label="Nombre companiía" maxlength="100"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="store" icon-color="dark" :error="$v.form.fields.nombre_comercial.$error" error-label="Escriba el nombre comercial">
                    <q-input upper-case v-model="form.fields.nombre_comercial" type="text" inverted color="dark" float-label="Nombre comercial" maxlength="100"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="label" icon-color="dark" :error="$v.form.fields.giro_comercial.$error" error-label="Seleccione un giro comercial">
                    <q-select v-model="form.fields.giro_comercial" inverted color="dark" float-label="Giro comercial" :options="girosOptions" filter></q-select>
                  </q-field>
                </div>
                <!-- <div class="col-sm-3">
                  <q-field icon="label" icon-color="dark">
                    <q-select v-model="form.fields.producto_id" inverted color="dark" float-label="Producto" :options="productosOptions" filter></q-select>
                  </q-field>
                </div> -->
                <div class="col-sm-3">
                  <q-field icon="fas fa-id-card" icon-color="dark" :error="$v.form.fields.rfc.$error" error-label="Escriba un rfc válido">
                    <q-input upper-case v-model="form.fields.rfc" inverted color="dark" float-label="RFC"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="business_center" icon-color="dark">
                    <q-input upper-case v-model="form.fields.razon_social" type="text" inverted color="dark" float-label="Razón social" maxlength="100" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="label" icon-color="dark">
                    <q-input @keyup="isNumber($event,'dias')" v-model="form.fields.dias_credito" inverted color="dark" float-label="Días de crédito" maxlength="5" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="dns" icon-color="dark">
                    <q-input upper-case v-model="form.fields.abreviatura" type="text" inverted color="dark" float-label="Abreviatura" maxlength="100" />
                  </q-field>
                </div>
              </div>
              <div class="row q-mt-lg" style="padding-bottom: 15px;">
                Contacto
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-6">
                  <q-field icon="person" icon-color="dark" :error="$v.form.fields.nombre_contacto.$error" error-label="Por favor ingrese un nombre de contacto">
                    <q-input upper-case v-model="form.fields.nombre_contacto" type="text" inverted color="dark" float-label="Nombre del contacto" maxlength="50" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="email" icon-color="dark" :error="$v.form.fields.email.$error" error-label="Por favor ingrese un email válido">
                    <q-input v-model="form.fields.email" type="email" inverted color="dark" float-label="Correo electrónico" maxlength="100" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="phone" icon-color="dark" :error="$v.form.fields.telefono.$error" error-label="Verifique el número telefónico">
                    <q-input @keyup="isNumber($event,'telefono')" upper-case v-model="form.fields.telefono" inverted color="dark" float-label="Teléfono" maxlength="10"/>
                  </q-field>
                </div>
              </div>
              <div class="row q-mt-lg" style="padding-bottom: 15px;">
                Domicilio
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-3">
                  <q-field icon="fas fa-road" icon-color="dark">
                    <q-input upper-case v-model="form.fields.calle" type="text" inverted color="dark" float-label="Calle" maxlength="100" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-map-signs" icon-color="dark" >
                    <q-input upper-case v-model="form.fields.colonia" type="text" inverted color="dark" float-label="Colonia" maxlength="100" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="map" icon-color="dark" >
                    <q-input upper-case v-model="form.fields.poblacion" type="text" inverted color="dark" float-label="Población" maxlength="100" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-hashtag" icon-color="dark">
                    <q-input upper-case v-model="form.fields.numero_exterior" type="text" inverted color="dark" float-label="Número exterior" maxlength="20" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-hashtag" icon-color="dark">
                    <q-input upper-case v-model="form.fields.numero_interior" type="text" inverted color="dark" float-label="Número interior" maxlength="20" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="room" icon-color="dark" :error="$v.form.fields.codigo_postal.$error" error-label="Verifique el código postal">
                    <q-input @keyup="isNumber($event,'codigo_postal')" v-model="form.fields.codigo_postal" inverted color="dark" float-label="C.P." maxlength="5" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-globe" icon-color="dark">
                    <q-select v-model="form.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-map-pin" icon-color="dark">
                    <q-select v-model="form.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="municipiosOptions" filter></q-select>
                  </q-field>
                </div>
              </div>
              <div class="row q-mt-lg" style="padding-bottom: 15px;">
                Acta constitucional
              </div>
              <div class="row q-col-gutter-xs">
                <div class="col-sm-3">
                  <q-field icon="person" icon-color="dark">
                    <q-input upper-case v-model="form.fields.acta_representante" type="text" inverted color="dark" float-label="Representante legal" maxlength="50" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-globe" icon-color="dark">
                    <q-select v-model="form.fields.acta_estado" inverted color="dark" float-label="Estado" :options="estadosOptions" filter/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="label" icon-color="dark">
                    <q-input @keyup="isNumber($event,'notaria')" upper-case v-model="form.fields.acta_notaria" inverted color="dark" float-label="N. Notaría" maxlength="5"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="account_balance" icon-color="dark">
                    <q-input upper-case v-model="form.fields.acta_notario" type="text" inverted color="dark" float-label="Notario" maxlength="50" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="date_range" icon-color="dark">
                    <q-datetime v-model="form.fields.acta_fecha" type="date" inverted color="dark" float-label="Fecha" align="center"></q-datetime>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="folder" icon-color="dark">
                    <q-input  @keyup="isNumber($event,'libro')" upper-case v-model="form.fields.acta_libro" inverted color="dark" float-label="Libro" maxlength="5"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="label" icon-color="dark">
                    <q-input upper-case v-model="form.fields.acta_rpp" type="text" inverted color="dark" float-label="RPP" maxlength="50" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="description" icon-color="dark">
                    <q-input upper-case v-model="form.fields.acta_giro" type="text" inverted color="dark" float-label="Giro" maxlength="50" />
                  </q-field>
                </div>
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
                  <q-breadcrumbs-el label="Prospectos" to="" @click.native="setView('grid')"/>
                  <q-breadcrumbs-el :label="form.fields.nombre_compania.substring(0, 35)"/>
                </q-breadcrumbs>
              </div>
            </div>
            <div class="col-sm-6 pull-right">
               <div class="q-pa-sm q-gutter-sm" >
              <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" style="margin-right: 10px;"/>
              <q-btn @click="update()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
              </div>
            </div>
          </div>
        </div>
        <div class="q-pa-md bg-grey-3">
          <div class="row bg-white border-panel">
            <q-tabs class="shadow-1" inverted animated swipeable color="teal" align="justify">
              <q-tab default name="tab_prospecto" icon="person" slot="title" label="Prospecto"/>
              <q-tab name="tab_cultivo" icon="style" slot="title" label="Cultivo"/>
              <q-tab-pane name="tab_prospecto">
                <div class="row q-col-gutter-xs">
                  <div class="col-sm-3">
                    <q-field icon="business" icon-color="dark">
                      <q-input upper-case v-model="form.fields.nombre_compania" type="text" inverted color="dark" float-label="Nombre companiía" maxlength="100"/>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="store" icon-color="dark" :error="$v.form.fields.nombre_comercial.$error" error-label="Escriba el nombre comercial">
                      <q-input upper-case v-model="form.fields.nombre_comercial" type="text" inverted color="dark" float-label="Nombre comercial" maxlength="100"/>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="label" icon-color="dark" :error="$v.form.fields.giro_comercial.$error" error-label="Seleccione un giro comercial">
                      <q-select v-model="form.fields.giro_comercial" inverted color="dark" float-label="Giro comercial" :options="girosOptions" filter></q-select>
                    </q-field>
                  </div>
                  <!-- <div class="col-sm-3">
                    <q-field icon="label" icon-color="dark">
                      <q-select v-model="form.fields.producto_id" inverted color="dark" float-label="Producto" :options="productosOptions" filter></q-select>
                    </q-field>
                  </div> -->
                  <div class="col-sm-3">
                    <q-field icon="fas fa-id-card" icon-color="dark" :error="$v.form.fields.rfc.$error" error-label="Escriba un rfc válido">
                      <q-input upper-case v-model="form.fields.rfc" inverted color="dark" float-label="RFC"/>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="business_center" icon-color="dark">
                      <q-input upper-case v-model="form.fields.razon_social" type="text" inverted color="dark" float-label="Razón social" maxlength="100" />
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="label" icon-color="dark">
                      <q-input @keyup="isNumber($event,'dias')" v-model="form.fields.dias_credito" inverted color="dark" float-label="Días de crédito" maxlength="5" />
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="dns" icon-color="dark">
                      <q-input upper-case v-model="form.fields.abreviatura" type="text" inverted color="dark" float-label="Abreviatura" maxlength="100" />
                    </q-field>
                  </div>
                </div>
                <div class="row q-mt-lg" style="padding-bottom: 15px;">
                  Contacto
                </div>
                <div class="row q-col-gutter-xs">
                  <div class="col-sm-6">
                    <q-field icon="person" icon-color="dark" :error="$v.form.fields.nombre_contacto.$error" error-label="Por favor ingrese un nombre de contacto">
                      <q-input upper-case v-model="form.fields.nombre_contacto" type="text" inverted color="dark" float-label="Nombre del contacto" maxlength="50" />
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="email" icon-color="dark" :error="$v.form.fields.email.$error" error-label="Por favor ingrese un email válido">
                      <q-input v-model="form.fields.email" type="email" inverted color="dark" float-label="Correo electrónico" maxlength="100" />
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="phone" icon-color="dark" :error="$v.form.fields.telefono.$error" error-label="Verifique el número telefónico">
                      <q-input @keyup="isNumber($event,'telefono')" upper-case v-model="form.fields.telefono" inverted color="dark" float-label="Teléfono" maxlength="10"/>
                    </q-field>
                  </div>
                </div>
                <div class="row q-mt-lg" style="padding-bottom: 15px;">
                  Domicilio
                </div>
                <div class="row q-col-gutter-xs">
                  <div class="col-sm-3">
                    <q-field icon="fas fa-road" icon-color="dark">
                      <q-input upper-case v-model="form.fields.calle" type="text" inverted color="dark" float-label="Calle" maxlength="100" />
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="fas fa-map-signs" icon-color="dark" >
                      <q-input upper-case v-model="form.fields.colonia" type="text" inverted color="dark" float-label="Colonia" maxlength="100" />
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="map" icon-color="dark" >
                      <q-input upper-case v-model="form.fields.poblacion" type="text" inverted color="dark" float-label="Población" maxlength="100" />
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="fas fa-hashtag" icon-color="dark">
                      <q-input upper-case v-model="form.fields.numero_exterior" type="text" inverted color="dark" float-label="Número exterior" maxlength="20" />
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="fas fa-hashtag" icon-color="dark">
                      <q-input upper-case v-model="form.fields.numero_interior" type="text" inverted color="dark" float-label="Número interior" maxlength="20" />
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="room" icon-color="dark" :error="$v.form.fields.codigo_postal.$error" error-label="Verifique el código postal">
                      <q-input @keyup="isNumber($event,'codigo_postal')" v-model="form.fields.codigo_postal" inverted color="dark" float-label="C.P." maxlength="5" />
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="fas fa-globe" icon-color="dark">
                      <q-select v-model="form.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()"/>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="fas fa-map-pin" icon-color="dark">
                      <q-select v-model="form.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="municipiosOptions" filter></q-select>
                    </q-field>
                  </div>
                </div>
                <div class="row q-mt-lg" style="padding-bottom: 15px;">
                  Acta constitucional
                </div>
                <div class="row q-col-gutter-xs">
                  <div class="col-sm-3">
                    <q-field icon="person" icon-color="dark">
                      <q-input upper-case v-model="form.fields.acta_representante" type="text" inverted color="dark" float-label="Representante legal" maxlength="50" />
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="fas fa-globe" icon-color="dark">
                      <q-select v-model="form.fields.acta_estado" inverted color="dark" float-label="Estado" :options="estadosOptions" filter/>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="label" icon-color="dark">
                      <q-input @keyup="isNumber($event,'notaria')" upper-case v-model="form.fields.acta_notaria" inverted color="dark" float-label="N. Notaría" maxlength="5"/>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="account_balance" icon-color="dark">
                      <q-input upper-case v-model="form.fields.acta_notario" type="text" inverted color="dark" float-label="Notario" maxlength="50" />
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="date_range" icon-color="dark">
                      <q-datetime v-model="form.fields.acta_fecha" type="date" inverted color="dark" float-label="Fecha" align="center"></q-datetime>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="folder" icon-color="dark">
                      <q-input  @keyup="isNumber($event,'libro')" upper-case v-model="form.fields.acta_libro" inverted color="dark" float-label="Libro" maxlength="5"/>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="label" icon-color="dark">
                      <q-input upper-case v-model="form.fields.acta_rpp" type="text" inverted color="dark" float-label="RPP" maxlength="50" />
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="description" icon-color="dark">
                      <q-input upper-case v-model="form.fields.acta_giro" type="text" inverted color="dark" float-label="Giro" maxlength="50" />
                    </q-field>
                  </div>
                </div>
              </q-tab-pane>
              <q-tab-pane name="tab_cultivo">
                <div class="row q-col-gutter-xs">
                  <div class="col-sm-3">
                    <q-field icon="today" icon-color="dark">
                      <q-input upper-case v-model="form.fieldsCultivos.anio_administracion" type="text" inverted color="dark" float-label="Anio de administración" maxlength="50" />
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="fas fa-globe" icon-color="dark">
                      <q-select v-model="form.fieldsCultivos.estado_origen" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipiosCultivo()"/>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="fas fa-map-pin" icon-color="dark">
                      <q-select v-model="form.fieldsCultivos.ciudad_origen" inverted color="dark" float-label="Municipio" :options="municipiosCultivoOptions" filter></q-select>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="check" icon-color="dark">
                      <q-input upper-case v-model="form.fieldsCultivos.cita_confirmada" type="text" inverted color="dark" float-label="Cita confirmada" maxlength="100" />
                    </q-field>
                  </div>
                  <div class="col-sm-12">
                    <q-field icon="fas fa-id-card" icon-color="dark">
                      <q-input upper-case v-model="form.fieldsCultivos.acreditaciones" type="text" inverted color="dark" float-label="Acreditaciones" maxlength="150" />
                    </q-field>
                  </div>
                  <div class="col-sm-12">
                    <q-field icon="label" icon-color="dark">
                      <q-input upper-case v-model="form.fieldsCultivos.toma_desiciones" type="text" inverted color="dark" float-label="Capacidad de toma de decisiones" maxlength="150" />
                    </q-field>
                  </div>
                  <div class="col-sm-12">
                    <q-field icon="description" icon-color="dark">
                      <q-input upper-case v-model="form.fieldsCultivos.consideraciones" type="text" inverted color="dark" float-label="Consideraciones para contratarnos" maxlength="150" />
                    </q-field>
                  </div>
                  <div class="col-sm-12">
                    <q-field icon="business_center" icon-color="dark">
                      <q-input upper-case v-model="form.fieldsCultivos.contrato_proveedores" type="text" inverted color="dark" float-label="Contratan proveedores" maxlength="150" />
                    </q-field>
                  </div>
                  <div class="col-sm-12">
                    <q-field icon="close" icon-color="dark">
                      <q-input upper-case v-model="form.fieldsCultivos.enfoque_cierre" type="text" inverted color="dark" float-label="Enfoque para cierre" maxlength="150" />
                    </q-field>
                  </div>
                  <div class="col-sm-12">
                    <q-field icon="dns" icon-color="dark">
                      <q-input upper-case v-model="form.fieldsCultivos.esquema" type="text" inverted color="dark" float-label="Esquema" maxlength="100" />
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="today" icon-color="dark">
                      <q-datetime v-model="form.fieldsCultivos.fecha_cumpleanios" type="date" inverted color="dark" float-label="Fecha decumpleanios" align="center"></q-datetime>
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="fas fa-dollar-sign" icon-color="dark">
                      <q-input upper-case v-model="form.fieldsCultivos.monto_entrada" v-money="money" type="text" inverted color="dark" float-label="Monto asignado historicamente" maxlength="50" />
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="account_balance" icon-color="dark">
                      <q-input upper-case v-model="form.fieldsCultivos.partido_politico" type="text" inverted color="dark" float-label="Partido politico" maxlength="50" />
                    </q-field>
                  </div>
                  <div class="col-sm-3">
                    <q-field icon="person" icon-color="dark">
                      <q-input upper-case v-model="form.fieldsCultivos.contacto" type="text" inverted color="dark" float-label="Primer contacto" maxlength="50" />
                    </q-field>
                  </div>
                  <div class="col-sm-12">
                    <q-field icon="email" icon-color="dark">
                      <q-input upper-case v-model="form.fieldsCultivos.informacion_enviada" type="text" inverted color="dark" float-label="Informacion enviada" maxlength="150" />
                    </q-field>
                  </div>
                  <div class="col-sm-12">
                    <q-field icon="label" icon-color="dark">
                      <q-input upper-case v-model="form.fieldsCultivos.necesidades" type="text" inverted color="dark" float-label="Necesidades" maxlength="150" />
                    </q-field>
                  </div>
                  <div class="col-sm-12">
                    <q-field icon="label" icon-color="dark">
                      <q-input upper-case v-model="form.fieldsCultivos.taller" type="text" inverted color="dark" float-label="Taller 1" maxlength="50" />
                    </q-field>
                  </div>
                  <div class="col-sm-12">
                    <q-field icon="description" icon-color="dark">
                      <q-input upper-case v-model="form.fieldsCultivos.tipo_recurso" type="text" inverted color="dark" float-label="Tipo de recurso" maxlength="50" />
                    </q-field>
                  </div>
                  <div class="col-sm-12">
                    <q-field icon="business" icon-color="dark">
                      <q-input upper-case v-model="form.fieldsCultivos.tipo_servicio" type="text" inverted color="dark" float-label="Tipo de servico" maxlength="100" />
                    </q-field>
                  </div>
                </div>
              </q-tab-pane>
            </q-tabs>
          </div>
        </div>
      </div>
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { required, maxLength, minLength, email, helpers, minValue } from 'vuelidate/lib/validators'
import {VMoney} from 'v-money'
const rfc = helpers.regex('rfc', /[A-Zni&]{3,4}\d{6}[A-V1-9][A-Z1-9][0-9A]/)

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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'DIRECTOR'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'INVENTARIOS'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'AUXILIAR'.toUpperCase() || propiedades[i].toUpperCase() === 'VENTAS'.toUpperCase() || propiedades[i].toUpperCase() === 'Coordinador GDP'.toUpperCase()) {
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
      // girosOptions: [],
      productosOptions: [],
      municipiosOptions: [{label: '---Seleccione---', value: 0}],
      municipiosCultivoOptions: [{label: '---Seleccione---', value: 0}],
      loadingButton: false,
      money: {
        decimal: '.',
        thousands: ',',
        precision: 2,
        masked: false
      },
      views: {
        grid: true,
        create: false,
        update: false
      },
      form: {
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
        },
        fieldsCultivos: {
          anio_administracion: 0,
          ciudad_origen: 0,
          estado_origen: 0,
          cita_confirmada: '',
          acreditaciones: '',
          toma_desiciones: '',
          consideraciones: '',
          contrato_proveedores: '',
          enfoque_cierre: '',
          esquema: '',
          fecha_cumpleanios: '',
          monto_asignado: 0,
          monto_entrada: '',
          partido_politico: '',
          contacto: '',
          informacion_enviada: '',
          necesidades: '',
          taller: '',
          tipo_recurso: '',
          tipo_servicio: '',
          prospecto_id: 0
        },
        // data: [],
        columns: [
          { name: 'nombre_compania', label: 'Nombre', field: 'nombre_compania', sortable: true, type: 'string', align: 'left' },
          { name: 'nombre_comercial', label: 'Nombre comercial', field: 'nombre_comercial', sortable: true, type: 'string', align: 'left' },
          { name: 'razon_social', label: 'Razón social', field: 'razon_social', sortable: true, type: 'string', align: 'left' },
          { name: 'estado', label: 'Estado', field: 'estado', sortable: true, type: 'string', align: 'left' },
          { name: 'municipio', label: 'Municipio', field: 'municipio', sortable: true, type: 'string', align: 'left' },
          { name: 'nombre_contacto', label: 'Contacto', field: 'nombre_contacto', sortable: true, type: 'string', align: 'left' },
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
  directives: {money: VMoney},
  computed: {
    ...mapGetters({
      prospectos: 'crm/prospectos/prospectos',
      cultivos: 'crm/cultivos/cultivos'
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
    }
  },
  created () {
    this.loadAll()
  },
  methods: {
    ...mapActions({
      getProspectos: 'crm/prospectos/refresh',
      saveProspectos: 'crm/prospectos/save',
      updateProspectos: 'crm/prospectos/update',
      deleteProspectos: 'crm/prospectos/delete',
      getEstados: 'vnt/estado/refresh',
      getGiros: 'crm/giros/refresh',
      getMunicipiosByEstado: 'vnt/municipio/getByEstado',
      saveCultivo: 'crm/cultivos/save',
      getCultivo: 'crm/cultivos/getById'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getEstados()
      await this.getGiros()
      await this.getProspectos()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    async cargaMunicipios () {
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
    async cargaMunicipiosCultivo () {
      this.municipiosCultivoOptions = []
      await this.getMunicipiosByEstado({estado_id: this.form.fieldsCultivos.estado_origen}).then(({data}) => {
        this.municipiosCultivoOptions.push({label: '---Seleccione---', value: 0})
        if (data.municipios.length > 0) {
          data.municipios.forEach(municipio => {
            this.municipiosCultivoOptions.push({label: municipio.nombre, value: municipio.id})
          })
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
    save () {
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
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
        this.form.fieldsCultivos.monto_asignado = this.evaluaMonto(this.form.fieldsCultivos.monto_entrada)
        this.form.fieldsCultivos.prospecto_id = this.form.fields.id
        let params = this.form.fields
        params.cultivo = this.form.fieldsCultivos
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
      this.form.fields.nombre_compania = row.nombre_compania
      this.form.fields.nombre_comercial = row.nombre_comercial
      this.form.fields.giro_comercial = row.giro_comercial
      this.form.fields.producto_id = row.producto_id
      this.form.fields.rfc = row.rfc
      this.form.fields.razon_social = row.razon_social
      this.form.fields.dias_credito = row.dias_credito
      this.form.fields.abreviatura = row.abreviatura
      this.form.fields.nombre_contacto = row.nombre_contacto
      this.form.fields.email = row.email
      this.form.fields.telefono = row.telefono
      this.form.fields.calle = row.calle
      this.form.fields.colonia = row.colonia
      this.form.fields.poblacion = row.poblacion
      this.form.fields.numero_exterior = row.numero_exterior
      this.form.fields.numero_interior = row.numero_interior
      this.form.fields.codigo_postal = row.codigo_postal
      this.form.fields.estado_id = row.estado_id
      if (this.form.fields.estado_id > 0) {
        this.cargaMunicipios()
      }
      this.form.fields.municipio_id = row.municipio_id
      this.form.fields.acta_representante = row.acta_representante
      this.form.fields.acta_estado = row.acta_estado
      this.form.fields.acta_notaria = row.acta_notaria
      this.form.fields.acta_notario = row.acta_notario
      this.form.fields.acta_fecha = row.acta_fecha
      this.form.fields.acta_libro = row.acta_libro
      this.form.fields.acta_rpp = row.acta_rpp
      this.form.fields.acta_giro = row.acta_giro
      this.editCultivo(row.id)
      this.setView('update')
    },
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este prospecto?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete(id)
      }).catch(() => {})
    },
    delete (id) {
      let params = {id: id}
      this.deleteProspectos(params).then(({data}) => {
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
      this.form.fields.nombre_compania = ''
      this.form.fields.nombre_comercial = ''
      this.form.fields.giro_comercial = 0
      this.form.fields.producto_id = 0
      this.form.fields.rfc = ''
      this.form.fields.razon_social = ''
      this.form.fields.dias_credito = 0
      this.form.fields.abreviatura = ''
      this.form.fields.nombre_contacto = ''
      this.form.fields.email = ''
      this.form.fields.telefono = ''
      this.form.fields.calle = ''
      this.form.fields.colonia = ''
      this.form.fields.poblacion = ''
      this.form.fields.numero_exterior = ''
      this.form.fields.numero_interior = ''
      this.form.fields.codigo_postal = ''
      this.form.fields.estado_id = 0
      this.form.fields.municipio_id = 0
      this.form.fields.acta_representante = ''
      this.form.fields.acta_estado = ''
      this.form.fields.acta_notaria = ''
      this.form.fields.acta_notario = ''
      this.form.fields.acta_fecha = ''
      this.form.fields.acta_libro = ''
      this.form.fields.acta_rpp = ''
      this.form.fields.acta_giro = ''
      this.municipiosOptions = []
      this.municipiosOptions.push({label: '---Seleccione---', value: 0})
      this.setView('create')
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
    saveCultivos () {
      this.form.fieldsCultivos.monto_asignado = this.evaluaMonto(this.form.fieldsCultivos.monto_entrada)
      this.form.fieldsCultivos.prospecto_id = this.form.fields.id
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fieldsCultivos
        this.saveCultivo(params).then(({data}) => {
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
            this.loadAll()
            this.setView('grid')
            this.clearCultivo()
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
    editCultivo (id) {
      this.getCultivo({id: id}).then(({data}) => {
        if (data.cultivo.length > 0) {
          this.form.fieldsCultivos.anio_administracion = data.cultivo[0].anio_administracion
          this.form.fieldsCultivos.cita_confirmada = data.cultivo[0].cita_confirmada
          this.form.fieldsCultivos.acreditaciones = data.cultivo[0].acreditaciones
          this.form.fieldsCultivos.toma_desiciones = data.cultivo[0].toma_desiciones
          this.form.fieldsCultivos.consideraciones = data.cultivo[0].consideraciones
          this.form.fieldsCultivos.contrato_proveedores = data.cultivo[0].contrato_proveedores
          this.form.fieldsCultivos.enfoque_cierre = data.cultivo[0].enfoque_cierre
          this.form.fieldsCultivos.esquema = data.cultivo[0].esquema
          this.form.fieldsCultivos.fecha_cumpleanios = data.cultivo[0].fecha_cumpleanios
          this.form.fieldsCultivos.monto_asignado = data.cultivo[0].monto_asignado
          this.form.fieldsCultivos.monto_entrada = data.cultivo[0].monto_asignado
          this.form.fieldsCultivos.partido_politico = data.cultivo[0].partido_politico
          this.form.fieldsCultivos.contacto = data.cultivo[0].contacto
          this.form.fieldsCultivos.informacion_enviada = data.cultivo[0].informacion_enviada
          this.form.fieldsCultivos.necesidades = data.cultivo[0].necesidades
          this.form.fieldsCultivos.taller = data.cultivo[0].taller
          this.form.fieldsCultivos.tipo_recurso = data.cultivo[0].tipo_recurso
          this.form.fieldsCultivos.tipo_servicio = data.cultivo[0].tipo_servicio
          this.form.fieldsCultivos.estado_origen = data.cultivo[0].estado_origen
          this.form.fieldsCultivos.ciudad_origen = data.cultivo[0].ciudad_origen
          if (this.form.fieldsCultivos.estado_origen > 0) {
            this.cargaMunicipiosCultivo()
          } else {
            this.form.fieldsCultivos.ciudad_origen = 0
          }
        } else {
          this.clearCultivo()
        }
      }).catch(error => {
        console.error(error)
      })
    },
    clearCultivo () {
      this.form.fieldsCultivos.anio_administracion = ''
      this.form.fieldsCultivos.cita_confirmada = ''
      this.form.fieldsCultivos.acreditaciones = ''
      this.form.fieldsCultivos.toma_desiciones = ''
      this.form.fieldsCultivos.consideraciones = ''
      this.form.fieldsCultivos.contrato_proveedores = ''
      this.form.fieldsCultivos.enfoque_cierre = ''
      this.form.fieldsCultivos.esquema = ''
      this.form.fieldsCultivos.fecha_cumpleanios = ''
      this.form.fieldsCultivos.monto_asignado = ''
      this.form.fieldsCultivos.monto_entrada = ''
      this.form.fieldsCultivos.partido_politico = ''
      this.form.fieldsCultivos.contacto = ''
      this.form.fieldsCultivos.informacion_enviada = ''
      this.form.fieldsCultivos.necesidades = ''
      this.form.fieldsCultivos.taller = ''
      this.form.fieldsCultivos.tipo_recurso = ''
      this.form.fieldsCultivos.tipo_servicio = ''
      this.form.fieldsCultivos.estado_origen = 0
      this.form.fieldsCultivos.ciudad_origen = 0
    }
  },
  validations: {
    form: {
      fields: {
        nombre_contacto: { required, maxLength: maxLength(100) },
        giro_comercial: { required, minValue: minValue(1) },
        nombre_comercial: { required, maxLength: maxLength(100) },
        email: { required, maxLength: maxLength(100), email },
        telefono: { required, maxLength: maxLength(10), minLength: minLength(10) },
        codigo_postal: {maxLength: maxLength(5), minLength: minLength(5)},
        rfc: {minLength: minLength(12), maxLength: maxLength(13), rfc}
      }
    }
  }
}
</script>

<style scoped>
</style>
