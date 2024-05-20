<template>
  <q-page>
      <div v-if="views.grid">
                  <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-6">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" label="Perfiles expertos"/>
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

          <!-- -->
           <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md" style="padding: 1%;">
            <div class="row q-mt-lg" >
                <div class="col-sm-4">
                  <q-field icon="fas fa-globe" icon-color="dark">
                    <q-select v-model="form_busqueda.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios2()"></q-select>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="fas fa-map-pin" icon-color="dark">
                    <q-select v-model="form_busqueda.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="form_busqueda.municipiosOptions" filter></q-select>
                  </q-field>
                </div>
                <div class="col-sm-4">
                  <q-field icon="fas fa-globe" icon-color="dark">
                    <q-select v-model="form_busqueda.fields.area_id" inverted color="dark" float-label="Área" :options="areasOptions" filter></q-select>
                  </q-field>
                </div>
                <div class="col-sm-6">
                  <q-field icon="grade" icon-color="dark">
                    <q-chips-input v-model="form_busqueda.fields.aptitudes" inverted color="dark" float-label="Aptitudes" @duplicate="duplicate_busqueda">
                      <q-autocomplete @search="search_busqueda" @selected="selected_busqueda"/>
                    </q-chips-input>
                  </q-field>
                </div>
                <div class="col-sm-6">
                  <q-field icon="school" icon-color="dark">
                    <q-input upper-case v-model="form_busqueda.fields.curso" type="text" inverted color="dark" float-label="Nombre del posgrado, curso adquirido o impartido" maxlength="300" />
                  </q-field>
                </div>
              </div>
              <div class="row justify-end" style="text-align: right; padding-top: 1%; padding-bottom: 1%;">
                <q-btn @click="filtrarP()" inverted color="orange" icon-right="fas fa-filter" :loading="loadingButton">Filtrar</q-btn>
                <q-btn @click="borrar()" color="red" icon="loop" style="margin-left:15px;">Limpiar</q-btn>
              </div>

              <!-- -->
          <div class="row q-mt-sm" style="margin-top:10px;">
            <div class="row" style="width:60vw;">
              <q-search hide-underline color="secondary" v-model="form.filter" class="col-12" />
            </div>
            <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll">
              <q-table id="sticky-table"
                :data="form.data"
                :columns="form_busqueda.fields.aptitudes.length === 0 ? form.columns : form.columnsAptitudes"
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
                    <q-td key="nombre" :props="props">{{ props.row.nombre }} {{ props.row.apellido_paterno }} {{ props.row.apellido_materno }}</q-td>
                    <q-td key="licenciatura" :props="props">{{ props.row.licenciatura }}</q-td>
                    <q-td key="area" :props="props">{{ props.row.area }}</q-td>
                    <q-td key="aptitudes" :props="props" v-if="form_busqueda.fields.aptitudes.length > 0">
                      <div v-for="apt in props.row.aptitudes" :key="apt.id" style="display: inline-grid; padding-right: 5px; padding-bottom: 10px;">
                        <q-chip dense icon-right="grade" color="pink">{{ apt.aptitud }}</q-chip>
                      </div>
                    </q-td>
                    <q-td key="acciones" style="text-align: left;" :props="props">
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

    <!--Crear un programa-->

      <div class="row" v-if="views.create">
        <div class="col-sm-12">
          <div class="q-pa-sm panel-header">
          <div class="row">
          <div class="col-sm-8">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" @click.native="setView('grid')" label="Perfiles expertos"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" :label="form.fields.nombre"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-4 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" />
              <q-btn @click="save()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
            </div>
          </div>
        </div>
        </div>
          <!-- <div class="row justify-between">
            <div class="col-sm-6">
              <q-btn @click="atras()" class="btn_regresar" icon="fa-arrow-left" />
            </div>
            <div class="col-sm-6 pull-right">
              <q-btn @click="save()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
            </div>
            <div class="col-sm-12" style="padding-top: 20px;">
              <h5 style="margin: 7px 0; font-weight: bold">NUEVO PERFIL</h5>
            </div>
          </div> -->
          <!-- tabs -->
          <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md" style="padding: 1%;">
          <q-tabs v-model="tab" class="shadow-1" inverted animated swipeable color="teal" align="justify" style="margin-top: 20px;">
            <q-tab default name="general" slot="title" icon="person" label="Información general"/>
            <q-tab disable name="formacion" slot="title" icon="school" label="Formación"/>
            <q-tab disable name="experiencia" slot="title" icon="work" label="Experiencia laboral"/>
            <q-tab disable name="documentos" slot="title" icon="folder" label="Documentos"/>
            <q-tab disable name="aptitudes" slot="title" icon="grade" label="Aptitudes"/>
            <q-tab disable name="participacion" slot="title" icon="business_center" label="Participación"/>

            <q-tab-pane name="general">
              <div class="row q-mt-lg subtitulos">
                Datos generales
              </div>
              <div class="row q-mt-lg">
                <div class="col-sm-3">
                  <q-field icon="person"  icon-color="dark" :error="$v.form.fields.nombre.$error" error-label="Ingrese un nombre">
                    <q-input upper-case v-model="form.fields.nombre" type="text" inverted color="dark" float-label="Nombre(s)" maxlength="50" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="person"  icon-color="dark" :error="$v.form.fields.apellido_paterno.$error" error-label="Ingrese el apellido paterno">
                    <q-input upper-case v-model="form.fields.apellido_paterno" type="text" inverted color="dark" float-label="Apellido paterno" maxlength="50" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="person"  icon-color="dark" :error="$v.form.fields.apellido_materno.$error" error-label="Ingrese el apellido materno">
                    <q-input upper-case v-model="form.fields.apellido_materno" type="text" inverted color="dark" float-label="Apellido materno" maxlength="50" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-id-card" icon-color="dark">
                    <q-input upper-case v-model="form.fields.rfc" inverted color="dark" float-label="RFC"/>
                  </q-field>
                </div>
              </div>
              <div class="row q-mt-lg">
                <div class="col-sm-3">
                  <q-field icon="fas fa-id-badge" icon-color="dark" :error="$v.form.fields.curp.$error" error-label="Ingrese una CURP válida">
                    <q-input upper-case v-model="form.fields.curp" inverted color="dark" float-label="CURP"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="phone" icon-color="dark" :error="$v.form.fields.telefono.$error" error-label="Verifique el número telefónico">
                    <q-input  @keyup="isNumber($event,'telefono')" upper-case v-model="form.fields.telefono" inverted color="dark" float-label="Teléfono"/>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="email" icon-color="dark" :error="$v.form.fields.email.$error" error-label="Por favor ingrese un email válido">
                    <q-input v-model="form.fields.email" type="email" inverted color="dark" float-label="Correo electrónico" maxlength="100" />
                  </q-field>
                </div>
              </div>
              <div class="row q-mt-lg subtitulos">
                Domicilio
              </div>
              <div class="row q-mt-lg">
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
                  <q-field icon="fas fa-hashtag" icon-color="dark">
                    <q-input upper-case v-model="form.fields.numero_exterior" type="text" inverted color="dark" float-label="Número exterior" maxlength="20" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-hashtag" icon-color="dark">
                    <q-input upper-case v-model="form.fields.numero_interior" type="text" inverted color="dark" float-label="Número interior" maxlength="20" />
                  </q-field>
                </div>
              </div>
              <div class="row q-mt-lg">
                <div class="col-sm-3">
                  <q-field icon="room" icon-color="dark">
                    <q-input v-model="form.fields.codigo_postal" type="number" inverted color="dark" float-label="C.P." maxlength="5" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-globe" icon-color="dark">
                    <q-select v-model="form.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()"></q-select>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-map-pin" icon-color="dark">
                    <q-select v-model="form.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="municipiosOptions" filter></q-select>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-images" icon-color="dark">
                    <q-uploader multiple inverted color="dark" stack-label="Fotografía"
                    extensions=".jpg, .jpeg, .png" url="" ref="fileInputFormato" @click="$refs.fileInputFormato.click()" :hide-upload-button="true"
                    />
                  </q-field>
                </div>
              </div>
            </q-tab-pane>
            <q-tab-pane name="formacion">
            </q-tab-pane>
            <q-tab-pane name="experiencia">
            </q-tab-pane>
            <q-tab-pane name="documentos">
            </q-tab-pane>
            <q-tab-pane name="aptitudes">
            </q-tab-pane>
          </q-tabs>
        </div>
      </div>
      </div>
      </div>
      </div>

      <!-- Modal editar PROGRAMA -->
      <div class="row" v-if="views.update">
        <div class="col-sm-12">
          <!-- <div class="row justify-between">
            <div class="col-sm-6">
              <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" />
            </div>
            <div class="col-sm-6 pull-right">
              <q-btn @click="update()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
            </div>
            <div class="col-sm-12" style="padding-top: 20px;">
              <h5 style="margin: 7px 0; font-weight: bold">MODIFICAR PERFIL - {{ form.fields.nombre }}</h5>
            </div>
          </div> -->
           <div class="q-pa-sm panel-header">
        <div class="row">
          <div class="col-sm-8">
            <div class="q-pa-sm q-gutter-sm">
              <q-breadcrumbs align="left">
                <q-breadcrumbs-el label="" icon="home" to="/dashboard"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" @click.native="setView('grid')" label="Perfiles expertos"/>
                <q-breadcrumbs-el class="q-ml-sm grey-8 fs28 page-title" :label="form.fields.nombre"/>
              </q-breadcrumbs>
            </div>
          </div>
          <div class="col-sm-4 pull-right">
            <div class="q-pa-sm q-gutter-sm">
              <q-btn @click="setView('grid')" class="btn_regresar" icon="fa-arrow-left" />
              <q-btn @click="update()" class="btn_guardar" icon-right="save" :loading="loadingButton">Guardar</q-btn>
            </div>
          </div>
        </div>
      </div>
      <div class="q-pa-md bg-grey-3">
        <div class="row bg-white border-panel">
          <div class="col q-pa-md" style="padding: 1%;">
          <q-tabs v-model="tab" class="shadow-1" inverted animated swipeable color="teal" align="justify" style="margin-top: 20px;">
            <q-tab default name="general" slot="title" icon="person" label="Información general"/>
            <q-tab name="formacion" slot="title" icon="school" label="Formación" @click="cargarDocs()"/>
            <q-tab name="experiencia" slot="title" icon="work" label="Experiencia laboral"/>
            <q-tab name="documentos" slot="title" icon="folder" label="Documentos"/>
            <q-tab name="aptitudes" slot="title" icon="grade" label="Aptitudes"/>
            <q-tab name="participacion" slot="title" icon="business_center" label="Participación" @click="cargarLicitaciones()"/>

            <q-tab-pane name="general">
              <div class="row q-mt-lg">
                <div class="col-sm-2">
                  <!-- <div style="min-height: 200px;max-height: 200px; min-width: 190px;max-width: 190px;border: solid;"> -->
                    <img :src="form.fields.foto" style="min-height: 200px;max-height: 200px; min-width: 190px;max-width: 190px;border: 2px solid;">
                    <q-popover>
                      <q-list link separator class="scroll" style="min-width: 100px">
                        <q-item v-close-overlay @click.native="$refs.fotoactInput.click()">
                          <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="fotoactInput" accept=".png, .jpg, .jpeg, .PNG, .JPG, .JPEG" @change="uploadFotoFile()" />Actualizar foto
                          <!-- <q-btn small flat @click="$refs.fotoactInput.click()" :loading="loadingButton">Actualizar foto
                          </q-btn> -->
                        </q-item>
                      </q-list>
                    </q-popover>
                  <!-- </div> -->
                </div>
                <div class="col-sm-10">
                  <div class="row q-mt-lg subtitulos" style="padding-left: 1%;">
                    Datos generales
                  </div>
                  <div class="row q-mt-lg">
                    <div class="col-sm-3">
                      <q-field icon="person"  icon-color="dark" :error="$v.form.fields.nombre.$error" error-label="Ingrese un nombre">
                        <q-input upper-case v-model="form.fields.nombre" type="text" inverted color="dark" float-label="Nombre(s)" maxlength="50" />
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="person"  icon-color="dark" :error="$v.form.fields.apellido_paterno.$error" error-label="Ingrese el apellido paterno">
                        <q-input upper-case v-model="form.fields.apellido_paterno" type="text" inverted color="dark" float-label="Apellido paterno" maxlength="50" />
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="person"  icon-color="dark" :error="$v.form.fields.apellido_materno.$error" error-label="Ingrese el apellido materno">
                        <q-input upper-case v-model="form.fields.apellido_materno" type="text" inverted color="dark" float-label="Apellido materno" maxlength="50" />
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="fas fa-id-card" icon-color="dark">
                        <q-input upper-case v-model="form.fields.rfc" inverted color="dark" float-label="RFC"/>
                      </q-field>
                    </div>
                  </div>
                  <div class="row q-mt-lg">
                    <div class="col-sm-3">
                      <q-field icon="fas fa-id-badge" icon-color="dark" :error="$v.form.fields.curp.$error" error-label="Ingrese una CURP válida">
                        <q-input upper-case v-model="form.fields.curp" inverted color="dark" float-label="CURP"/>
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="phone" icon-color="dark" :error="$v.form.fields.telefono.$error" error-label="Verifique el número telefónico">
                        <q-input  @keyup="isNumber($event,'telefono')" upper-case v-model="form.fields.telefono" inverted color="dark" float-label="Teléfono"/>
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="email" icon-color="dark" :error="$v.form.fields.email.$error" error-label="Por favor ingrese un email válido">
                        <q-input v-model="form.fields.email" type="email" inverted color="dark" float-label="Correo electrónico" maxlength="100" />
                      </q-field>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row q-mt-lg subtitulos">
                Domicilio
              </div>
              <div class="row q-mt-lg">
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
                  <q-field icon="fas fa-hashtag" icon-color="dark">
                    <q-input upper-case v-model="form.fields.numero_exterior" type="text" inverted color="dark" float-label="Número exterior" maxlength="20" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-hashtag" icon-color="dark">
                    <q-input upper-case v-model="form.fields.numero_interior" type="text" inverted color="dark" float-label="Número interior" maxlength="20" />
                  </q-field>
                </div>
              </div>
              <div class="row q-mt-lg">
                <div class="col-sm-3">
                  <q-field icon="room" icon-color="dark">
                    <q-input v-model="form.fields.codigo_postal" type="number" inverted color="dark" float-label="C.P." maxlength="5" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-globe" icon-color="dark">
                    <q-select v-model="form.fields.estado_id" inverted color="dark" float-label="Estado" :options="estadosOptions" filter @input="cargaMunicipios()"></q-select>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="fas fa-map-pin" icon-color="dark">
                    <q-select v-model="form.fields.municipio_id" inverted color="dark" float-label="Municipio" :options="municipiosOptions" filter></q-select>
                  </q-field>
                </div>
              </div>
            </q-tab-pane>
            <q-tab-pane name="formacion">
              <div class="row q-mt-lg subtitulos">
                Licenciatura
              </div>
              <div class="row q-mt-lg">
                <div class="col-sm-3">
                  <q-field icon="fas fa-globe" icon-color="dark">
                    <q-select v-model="form.fields.area_id" inverted color="dark" float-label="Área" :options="areasOptions" filter></q-select>
                  </q-field>
                </div>
                <div class="col-sm-6">
                  <q-field icon="school" icon-color="dark" :error="$v.form.fields.licenciatura.$error" error-label="Revise el nombre">
                    <q-input upper-case v-model="form.fields.licenciatura" type="text" inverted color="dark" float-label="Título" maxlength="200" />
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="business" icon-color="dark" :error="$v.form.fields.universidad.$error" error-label="Revise el nombre de la universidad">
                    <q-input upper-case v-model="form.fields.universidad" type="text" inverted color="dark" float-label="Universidad" maxlength="200" />
                  </q-field>
                </div>
              </div>
              <div class="row q-mt-lg">
                <div class="col-sm-3">
                  <q-field icon="date_range" icon-color="dark">
                    <q-datetime v-model="form.fields.fecha_egreso" type="date" inverted color="dark" float-label="Fecha egreso" align="center"></q-datetime>
                  </q-field>
                </div>
                <div class="col-sm-3">
                  <q-field icon="dns" icon-color="dark">
                    <q-input upper-case v-model="form.fields.cedula" type="text" inverted color="dark" float-label="Cédula profesional" maxlength="50" />
                  </q-field>
                </div>
                <div class="col-sm-6">
                  <div class="row col-sm-12 pull-right" style="padding-top: 2%;">
                  <div class="col-sm-6">
                  <q-field>
                    <q-icon name="fas fa-id-badge" color="dark" />
                    <input style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="fileInputTitulo" accept=".png, .jpg, .jpeg, .pdf,.docx" @change="uploadTituloProfesional()" />
                    <q-btn  @click="$refs.fileInputTitulo.click()" class="btn_atach" icon-right="attach_file" :loading="loadingButton">Subir título</q-btn>
                  <div style="display: inline-grid;" v-for="doc in form.fields.titulos" :key="doc.id">
                                  <q-btn small flat :icon="doc.icon" :color="doc.color">
                                    <q-tooltip>{{ doc.archivo }}</q-tooltip>
                                    <q-popover>
                                      <q-list link separator class="scroll" style="min-width: 100px">
                                        <q-item v-close-overlay @click.native="verTitulo(doc.id, doc.extension, 'titulo')" v-if="doc.extension !== 'docx' && doc.extension !== 'xml' && doc.extension !== 'xlsx'">
                                          <q-item-main label="Ver"/>
                                        </q-item>
                                        <q-item v-close-overlay @click.native="descargarTitulo(doc.id, 'titulo')">
                                          <q-item-main label="Descargar"/>
                                        </q-item>
                                        <q-item v-close-overlay @click.native="deleteTitulo(doc.id, 'titulo')">
                                          <q-item-main label="Eliminar"/>
                                        </q-item>
                                      </q-list>
                                    </q-popover>
                                  </q-btn>
                                </div>
                                                  </q-field>
                </div>
                <div class="col-sm-6">
                  <q-field>
                    <q-icon name="fas fa-id-badge" color="dark" />
                    <input style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="fileInputCedula" accept=".png, .jpg, .jpeg, .pdf,.docx" @change="uploadCedulaProfesional()" />
                    <q-btn  @click="$refs.fileInputCedula.click()" class="btn_atach" icon-right="attach_file" :loading="loadingButton">Subir cédula</q-btn>
                    <div style="display: inline-grid;" v-for="doc in form.fields.cedulas" :key="doc.id">
                                  <q-btn small flat :icon="doc.icon" :color="doc.color">
                                    <q-tooltip>{{ doc.archivo }}</q-tooltip>
                                    <q-popover>
                                      <q-list link separator class="scroll" style="min-width: 100px">
                                        <q-item v-close-overlay @click.native="verTitulo(doc.id, doc.extension, 'cedula')" v-if="doc.extension !== 'docx' && doc.extension !== 'xml' && doc.extension !== 'xlsx'">
                                          <q-item-main label="Ver"/>
                                        </q-item>
                                        <q-item v-close-overlay @click.native="descargarCedula(doc.id, 'cedula')">
                                          <q-item-main label="Descargar"/>
                                        </q-item>
                                        <q-item v-close-overlay @click.native="deleteCedula(doc.id, 'cedula')">
                                          <q-item-main label="Eliminar"/>
                                        </q-item>
                                      </q-list>
                                    </q-popover>
                                  </q-btn>
                                </div>
                  </q-field>
                </div>
                </div>
                </div>
              </div>

              <!-- -->
              <q-tabs v-model="tab2" class="shadow-1" inverted animated swipeable color="black" align="justify" style="margin-top: 30px;">
                <q-tab default name="posgrados" slot="title" icon="folder" label="Posgrados"/>
                <q-tab name="cursos" slot="title" icon="folder_open" label="Currícula"/>

                <q-tab-pane name="posgrados">
                  <div class="row q-mt-lg subtitulos">
                    Posgrados
                  </div>
                  <div class="row q-mt-lg">
                    <div class="col-sm-4">
                      <q-field icon="school" icon-color="dark" :error="$v.form_posgrados.fields.nombre.$error" error-label="Revise el nombre">
                        <q-input upper-case v-model="form_posgrados.fields.nombre" type="text" inverted color="dark" float-label="Nombre del posgrado" maxlength="300" />
                      </q-field>
                    </div>
                    <div class="col-sm-4">
                      <q-field icon="business" icon-color="dark" :error="$v.form_posgrados.fields.universidad.$error" error-label="Revise el nombre de la universidad">
                        <q-input upper-case v-model="form_posgrados.fields.universidad" type="text" inverted color="dark" float-label="Universidad" maxlength="300" />
                      </q-field>
                    </div>
                    <div class="col-sm-4">
                      <q-field icon="date_range" icon-color="dark" :error="$v.form_posgrados.fields.fecha_egreso.$error" error-label="Elija una fecha">
                        <q-datetime format="DD-MM-YYYY" v-model="form_posgrados.fields.fecha_egreso" type="date" inverted color="dark" float-label="Fecha egreso" align="center"></q-datetime>
                      </q-field>
                    </div>
                  </div>
                  <div class="row justify-end">
                    <div class="col-sm-12" style="text-align: right">
                      <q-btn @click="save_posgrado()" class="btn_guardar" icon-right="save" :loading="loadingButton" v-if="viewsPosgrado.create">Guardar</q-btn>
                    </div>
                    <div class="col-sm-12" style="text-align: right">
                      <q-btn @click="update_posgrado()" color="orange" icon-right="save" :loading="loadingButton" v-if="viewsPosgrado.update">Actualizar</q-btn>
                    </div>
                  </div>
                  <div class="row q-mt-lg">
                    <!-- tabal posgrados -->
                      <div class="row" style="width:60vw;">
                        <q-search hide-underline color="secondary" v-model="form.filter"/>
                      </div>
                      <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                        <q-table id="sticky-table"
                          :data="form_posgrados.data"
                          :columns="form_posgrados.columns"
                          :selected.sync="form_posgrados.selected"
                          :filter="form_posgrados.filter"
                          color="positive"
                          title=""
                          :dense="true"
                          :pagination.sync="form_posgrados.pagination"
                          :loading="form_posgrados.loading"
                          :rows-per-page-options="form_posgrados.rowsOptions">
                          <div>
                          </div>
                          <template slot="body" slot-scope="props">
                            <q-tr :props="props">
                              <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                              <q-td key="universidad" :props="props">{{ props.row.universidad }}</q-td>
                              <q-td key="fecha_egreso" :props="props">{{ props.row.fecha_egreso }}</q-td>
                              <q-td key="titulo" :props="props">
                                <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="tituloPosgradoInput" accept=".png, .jpg, .jpeg, .docx, .pdf" @change="uploadTituloPosgrado()" />
                                <q-btn small flat @click="selectedTituloPosgrado(props.row)" color="green-9" :loading="loadingButton">
                                  <q-icon name="cloud_upload" />&nbsp;
                                  <q-tooltip>Subir título o certificado</q-tooltip>
                                </q-btn>
                                <div style="display: inline;" v-for="doc in props.row.documentos" :key="doc.id">
                                  <q-btn small flat :icon="doc.icon" :color="doc.color">
                                    <q-tooltip>{{ doc.archivo }}</q-tooltip>
                                    <q-popover>
                                      <q-list link separator class="scroll" style="min-width: 100px">
                                        <q-item v-close-overlay @click.native="verTituloPosgrado(doc.id, doc.extension)" v-if="doc.extension !== 'docx' && doc.extension !== 'xml' && doc.extension !== 'xlsx'">
                                          <q-item-main label="Ver"/>
                                        </q-item>
                                        <q-item v-close-overlay @click.native="descargarTituloPosgrado(doc.id, doc.extension)">
                                          <q-item-main label="Descargar"/>
                                        </q-item>
                                        <q-item v-close-overlay @click.native="deleteRowTituloPosgrado(doc.id)">
                                          <q-item-main label="Eliminar"/>
                                        </q-item>
                                      </q-list>
                                    </q-popover>
                                  </q-btn>
                                </div>
                              </q-td>
                              <q-td key="acciones" :props="props">
                                <q-btn small flat @click="editRowPosgrado(props.row)" color="blue-6" icon="edit">
                                  <q-tooltip>Editar</q-tooltip>
                                </q-btn>
                                <q-btn small flat @click="deleteSingleRowPosgrado(props.row.id)" color="negative" icon="delete">
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
                    <!-- -->
                  </div>
                </q-tab-pane>
                <q-tab-pane name="cursos">
                  <div class="row q-mt-lg subtitulos">
                    Cursos
                  </div>
                  <div class="row q-mt-lg">
                    <div class="col-sm-3">
                      <q-field icon="school" icon-color="dark" :error="$v.form_cursos.fields.nombre.$error" error-label="Revise el nombre">
                        <q-input upper-case v-model="form_cursos.fields.nombre" type="text" inverted color="dark" float-label="Nombre del curso" maxlength="300" />
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="business" icon-color="dark" :error="$v.form_cursos.fields.universidad.$error" error-label="Revise el nombre de la universidad">
                        <q-input upper-case v-model="form_cursos.fields.universidad" type="text" inverted color="dark" float-label="Universidad" maxlength="300" />
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="date_range" icon-color="dark" :error="$v.form_cursos.fields.fecha.$error" error-label="Elija una fecha">
                        <q-datetime format="DD-MM-YYYY" v-model="form_cursos.fields.fecha" type="date" inverted color="dark" float-label="Fecha inicio" align="center"></q-datetime>
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="date_range" icon-color="dark" :error="$v.form_cursos.fields.fecha_egreso.$error" error-label="Elija una fecha">
                        <q-datetime format="DD-MM-YYYY" v-model="form_cursos.fields.fecha_egreso" type="date" inverted color="dark" float-label="Fecha fin" align="center"></q-datetime>
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="av_timer" icon-color="dark">
                        <q-input upper-case v-model="form_cursos.fields.horas" type="number" inverted color="dark" float-label="Horas" maxlength="100" />
                      </q-field>
                    </div>
                  </div>
                  <div class="row justify-end">
                    <div class="col-sm-12" style="text-align: right">
                      <q-btn @click="save_curso()" class="btn_guardar" icon-right="save" :loading="loadingButton" v-if="viewsCurso.create">Guardar</q-btn>
                    </div>
                    <div class="col-sm-12" style="text-align: right">
                      <q-btn @click="update_curso()" color="orange" icon-right="save" :loading="loadingButton" v-if="viewsCurso.update">Actualizar</q-btn>
                    </div>
                  </div>
                  <div class="row q-mt-lg">
                    <!-- tabal posgrados -->
                      <div class="row" style="width:60vw;">
                        <q-search hide-underline color="secondary" v-model="form.filter"/>
                      </div>
                      <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                        <q-table id="sticky-table"
                          :data="form_cursos.data"
                          :columns="form_cursos.columns"
                          :selected.sync="form_cursos.selected"
                          :filter="form_cursos.filter"
                          color="positive"
                          title=""
                          :dense="true"
                          :pagination.sync="form_cursos.pagination"
                          :loading="form_cursos.loading"
                          :rows-per-page-options="form_cursos.rowsOptions">
                          <div>
                          </div>
                          <template slot="body" slot-scope="props">
                            <q-tr :props="props">
                              <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                              <q-td key="universidad" :props="props">{{ props.row.universidad }}</q-td>
                              <q-td key="fecha" :props="props">{{ props.row.fecha }}</q-td>
                              <q-td key="fecha_egreso" :props="props">{{ props.row.fecha_egreso }}</q-td>
                              <q-td key="horas" style="text-align: right;" :props="props">{{ props.row.horas }}</q-td>
                              <q-td key="titulo" :props="props">
                                <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="tituloCursoInput" accept=".png, .jpg, .jpeg, .docx, .pdf" @change="uploadTituloCurso()" />
                                <q-btn small flat @click="selectedTituloCurso(props.row)" color="green-9" :loading="loadingButton">
                                  <q-icon name="cloud_upload" />&nbsp;
                                  <q-tooltip>Subir documento</q-tooltip>
                                </q-btn>
                                <div style="display: inline;" v-for="doc in props.row.documentos" :key="doc.id">
                                  <q-btn small flat :icon="doc.icon" :color="doc.color">
                                    <q-tooltip>{{ doc.archivo }}</q-tooltip>
                                    <q-popover>
                                      <q-list link separator class="scroll" style="min-width: 100px">
                                        <q-item v-close-overlay @click.native="verTituloCurso(doc.id, doc.extension)" v-if="doc.extension !== 'docx' && doc.extension !== 'xml' && doc.extension !== 'xlsx'">
                                          <q-item-main label="Ver"/>
                                        </q-item>
                                        <q-item v-close-overlay @click.native="descargarTituloCurso(doc.id, doc.extension)">
                                          <q-item-main label="Descargar"/>
                                        </q-item>
                                        <q-item v-close-overlay @click.native="deleteRowTituloCurso(doc.id)">
                                          <q-item-main label="Eliminar"/>
                                        </q-item>
                                      </q-list>
                                    </q-popover>
                                  </q-btn>
                                </div>
                              </q-td>
                              <q-td key="acciones" :props="props">
                                <q-btn small flat @click="editRowCurso(props.row)" color="blue-6" icon="edit">
                                  <q-tooltip>Editar</q-tooltip>
                                </q-btn>
                                <q-btn small flat @click="deleteSingleRowCurso(props.row.id)" color="negative" icon="delete">
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
                    <!-- -->
                  </div>
                </q-tab-pane>
              </q-tabs>
              <!-- -->
            </q-tab-pane>
            <q-tab-pane name="experiencia" style="padding: 0;">
              <q-tabs v-model="tab3" class="shadow-1" inverted animated swipeable color="black" align="justify">
                <q-tab default name="empleos" slot="title" icon="work" label="Empleos"/>
                <q-tab name="cursos" slot="title" icon="folder_open" label="Cursos impartidos"/>

                <q-tab-pane name="empleos">
                  <div class="row q-mt-lg subtitulos">
                    Experiencia laboral
                  </div>
                  <div class="row q-mt-lg">
                    <div class="col-sm-3">
                      <q-field icon="business" icon-color="dark" :error="$v.form_empleos.fields.empresa.$error" error-label="Revise el nombre de la empresa">
                        <q-input upper-case v-model="form_empleos.fields.empresa" type="text" inverted color="dark" float-label="Nombre de la empresa" maxlength="200" />
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="person" icon-color="dark" :error="$v.form_empleos.fields.puesto.$error" error-label="Revise el nombre del puesto">
                        <q-input upper-case v-model="form_empleos.fields.puesto" type="text" inverted color="dark" float-label="Puesto" maxlength="200" />
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="date_range" icon-color="dark" :error="$v.form_empleos.fields.fecha_ingreso.$error" error-label="Elija una fecha">
                        <q-datetime format="DD/MM/YYYY" v-model="form_empleos.fields.fecha_ingreso" type="date" inverted color="dark" float-label="Fecha ingreso" align="center"></q-datetime>
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="date_range" icon-color="dark">
                        <q-datetime format="DD/MM/YYYY" v-model="form_empleos.fields.fecha_egreso" type="date" inverted color="dark" float-label="Fecha baja" align="center"></q-datetime>
                      </q-field>
                    </div>
                  </div>
                  <div class="row justify-end" style="padding-top: 1%;">
                    <div class="col-sm-12" style="text-align: right">
                      <q-btn @click="save_empleo()" class="btn_guardar" icon-right="save" :loading="loadingButton" v-if="viewsEmpleo.create">Guardar</q-btn>
                    </div>
                    <div class="col-sm-12" style="text-align: right">
                      <q-btn @click="update_empleo()" color="orange" icon-right="save" :loading="loadingButton" v-if="viewsEmpleo.update">Actualizar</q-btn>
                    </div>
                  </div>
                  <div class="row q-mt-lg">
                    <!-- tabal posgrados -->
                      <div class="row" style="width:60vw;">
                        <q-search hide-underline color="secondary" v-model="form.filter"/>
                      </div>
                      <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                        <q-table id="sticky-table"
                          :data="form_empleos.data"
                          :columns="form_empleos.columns"
                          :selected.sync="form_empleos.selected"
                          :filter="form_empleos.filter"
                          color="positive"
                          title=""
                          :dense="true"
                          :pagination.sync="form_empleos.pagination"
                          :loading="form_empleos.loading"
                          :rows-per-page-options="form_empleos.rowsOptions">
                          <div>
                          </div>
                          <template slot="body" slot-scope="props">
                            <q-tr :props="props">
                              <q-td key="empresa" :props="props">{{ props.row.empresa }}</q-td>
                              <q-td key="puesto" :props="props">{{ props.row.puesto }}</q-td>
                              <q-td key="fecha_ingreso" style="text-align: center;" :props="props">{{ props.row.fecha_ingreso }}</q-td>
                              <q-td key="fecha_egreso" style="text-align: center;" :props="props">{{ props.row.fecha_egreso }}</q-td>
                              <q-td key="laborando" :props="props">
                                <q-checkbox readonly v-model="props.row.laborando" color="amber"/>
                              </q-td>
                              <q-td key="cartas" :props="props">
                                <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="cartasInput" accept=".png, .jpg, .jpeg, .docx, .pdf, .PNG, .JPG, .JPEG, .DOCX, .PDF" @change="uploadCartas()" />
                                <q-btn small flat @click="selectedCarta(props.row)" color="green-9" :loading="loadingButton">
                                  <q-icon name="cloud_upload" />&nbsp;
                                  <q-tooltip>Subir carta</q-tooltip>
                                </q-btn>
                                <div style="display: inline;" v-for="car in props.row.cartas" :key="car.id">
                                  <q-btn small flat :icon="car.icon" :color="car.color">
                                    <q-tooltip>{{ car.archivo }}</q-tooltip>
                                    <q-popover>
                                      <q-list link separator class="scroll" style="min-width: 100px">
                                        <q-item v-close-overlay @click.native="verCarta(car)" v-if="car.extension !== 'docx' && car.extension !== 'xml' && car.extension !== 'xlsx' && car.extension !== 'XLSX' && car.extension !== 'DOCX'">
                                          <q-item-main label="Ver"/>
                                        </q-item>
                                        <q-item v-close-overlay @click.native="descargarCarta(car.id, car.extension)">
                                          <q-item-main label="Descargar"/>
                                        </q-item>
                                        <q-item v-close-overlay @click.native="deleteCartas(car.id)">
                                          <q-item-main label="Eliminar"/>
                                        </q-item>
                                      </q-list>
                                    </q-popover>
                                  </q-btn>
                                </div>
                              </q-td>
                              <q-td key="acciones" :props="props">
                                <q-btn small flat @click="editRowEmpleo(props.row)" color="blue-6" icon="edit">
                                  <q-tooltip>Editar</q-tooltip>
                                </q-btn>
                                <q-btn small flat @click="deleteSingleRowEmpleo(props.row.id)" color="negative" icon="delete">
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
                    <!-- -->
                  </div>
                </q-tab-pane>
                <q-tab-pane name="cursos">
                  <div class="row q-mt-lg subtitulos">
                    Cursos impartidos
                  </div>
                  <div class="row q-mt-lg">
                    <div class="col-sm-3">
                      <q-field icon="school" icon-color="dark" :error="$v.form_cursos_impartidos.fields.nombre.$error" error-label="Revise el nombre">
                        <q-input upper-case v-model="form_cursos_impartidos.fields.nombre" type="text" inverted color="dark" float-label="Nombre del curso" maxlength="300" />
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="business" icon-color="dark" :error="$v.form_cursos_impartidos.fields.universidad.$error" error-label="Revise el nombre de la universidad">
                        <q-input upper-case v-model="form_cursos_impartidos.fields.universidad" type="text" inverted color="dark" float-label="Universidad" maxlength="300" />
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="date_range" icon-color="dark" :error="$v.form_cursos_impartidos.fields.fecha.$error" error-label="Elija una fecha">
                        <q-datetime format="DD/MM/YYYY" v-model="form_cursos_impartidos.fields.fecha" type="date" inverted color="dark" float-label="Fecha inicio" align="center"></q-datetime>
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="date_range" icon-color="dark" :error="$v.form_cursos_impartidos.fields.fecha_egreso.$error" error-label="Elija una fecha">
                        <q-datetime format="DD/MM/YYYY" v-model="form_cursos_impartidos.fields.fecha_egreso" type="date" inverted color="dark" float-label="Fecha fin" align="center"></q-datetime>
                      </q-field>
                    </div>
                    <div class="col-sm-3">
                      <q-field icon="av_timer" icon-color="dark">
                        <q-input upper-case v-model="form_cursos_impartidos.fields.horas" type="number" inverted color="dark" float-label="Horas" maxlength="100" />
                      </q-field>
                    </div>
                  </div>
                  <div class="row justify-end">
                    <div class="col-sm-12" style="text-align: right">
                      <q-btn @click="save_curso_impartido()" class="btn_guardar" icon-right="save" :loading="loadingButton" v-if="viewsCursoImpartido.create">Guardar</q-btn>
                    </div>
                    <div class="col-sm-12" style="text-align: right">
                      <q-btn @click="update_curso_impartido()" color="orange" icon-right="save" :loading="loadingButton" v-if="viewsCursoImpartido.update">Actualizar</q-btn>
                    </div>
                  </div>
                  <div class="row q-mt-lg">
                    <!-- tabal posgrados -->
                      <div class="row" style="width:60vw;">
                        <q-search hide-underline color="secondary" v-model="form.filter"/>
                      </div>
                      <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                        <q-table id="sticky-table"
                          :data="form_cursos_impartidos.data"
                          :columns="form_cursos_impartidos.columns"
                          :selected.sync="form_cursos_impartidos.selected"
                          :filter="form_cursos_impartidos.filter"
                          color="positive"
                          title=""
                          :dense="true"
                          :pagination.sync="form_cursos_impartidos.pagination"
                          :loading="form_cursos_impartidos.loading"
                          :rows-per-page-options="form_cursos_impartidos.rowsOptions">
                          <div>
                          </div>
                          <template slot="body" slot-scope="props">
                            <q-tr :props="props">
                              <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                              <q-td key="universidad" :props="props">{{ props.row.universidad }}</q-td>
                              <q-td key="fecha" :props="props">{{ props.row.fecha }}</q-td>
                              <q-td key="fecha_egreso" :props="props">{{ props.row.fecha_egreso }}</q-td>
                              <q-td key="horas" :props="props">{{ props.row.horas }}</q-td>
                              <q-td key="titulo" :props="props">
                                <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="tituloCursoImpartido" accept=".png, .jpg, .jpeg, .docx, .pdf" @change="uploadTituloCursoImpartido()" />
                                <q-btn small flat @click="selectedTituloCursoImpartido(props.row)" color="green-9" :loading="loadingButton">
                                  <q-icon name="cloud_upload" />&nbsp;
                                  <q-tooltip>Subir documento</q-tooltip>
                                </q-btn>
                                <div style="display: inline;" v-for="doc in props.row.documentos" :key="doc.id">
                                  <q-btn small flat :icon="doc.icon" :color="doc.color">
                                    <q-tooltip>{{ doc.archivo }}</q-tooltip>
                                    <q-popover>
                                      <q-list link separator class="scroll" style="min-width: 100px">
                                        <q-item v-close-overlay @click.native="verTituloCurso(doc.id, doc.extension)" v-if="doc.extension !== 'docx' && doc.extension !== 'xml' && doc.extension !== 'xlsx'">
                                          <q-item-main label="Ver"/>
                                        </q-item>
                                        <q-item v-close-overlay @click.native="descargarTituloCurso(doc.id, doc.extension)">
                                          <q-item-main label="Descargar"/>
                                        </q-item>
                                        <q-item v-close-overlay @click.native="deleteRowTituloCurso(doc.id)">
                                          <q-item-main label="Eliminar"/>
                                        </q-item>
                                      </q-list>
                                    </q-popover>
                                  </q-btn>
                                </div>
                              </q-td>
                              <q-td key="acciones" :props="props">
                                <q-btn small flat @click="editRowCursoImpartido(props.row)" color="blue-6" icon="edit">
                                  <q-tooltip>Editar</q-tooltip>
                                </q-btn>
                                <q-btn small flat @click="deleteSingleRowCurso(props.row.id)" color="negative" icon="delete">
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
                    <!-- -->
                  </div>
                </q-tab-pane>
              </q-tabs>
            </q-tab-pane>
            <q-tab-pane name="documentos">
              <!-- <q-tabs v-model="tab3" class="shadow-1" inverted animated swipeable color="dark" align="justify">
                <q-tab default name="cartas" slot="title" icon="description" label="Cartas de recomendación"/>
                <q-tab name="generales" slot="title" icon="insert_drive_file" label="Generales"/>

                <q-tab-pane name="cartas">
                  <div class="row q-mt-lg">
                  </div>
                  <div class="row">
                    <div class="col-sm-3"></div>
                      <div class="col-sm-6">
                        <q-table
                          :data="form_cartas.data"
                          :columns="form_cartas.columns"
                          title=""
                          :selected.sync="form_cartas.selected"
                          :dense="true"
                          :pagination.sync="form_cartas.pagination"
                          :loading="form_cartas.loading">
                          <template slot="body" slot-scope="props">
                            <q-tr :props="props">
                              <q-td key="tipo" :props="props">{{ props.row.tipo }}</q-td>
                              <q-td key="acciones" :props="props">
                                <div style="display: inline;" v-for="car in props.row.cartas" :key="car.id">
                                  <q-btn small flat :icon="car.icon" :color="car.color">
                                    <q-tooltip>{{ car.archivo }}</q-tooltip>
                                    <q-popover>
                                      <q-list link separator class="scroll" style="min-width: 100px">
                                        <q-item v-close-overlay @click.native="verCarta(car)" v-if="car.extension !== 'docx' && car.extension !== 'xml' && car.extension !== 'xlsx' && car.extension !== 'XLSX' && car.extension !== 'DOCX'">
                                          <q-item-main label="Ver"/>
                                        </q-item>
                                        <q-item v-close-overlay @click.native="descargarCarta(car.id, car.extension)">
                                          <q-item-main label="Descargar"/>
                                        </q-item>
                                        <q-item v-close-overlay @click.native="deleteCartas(car.id)">
                                          <q-item-main label="Eliminar"/>
                                        </q-item>
                                      </q-list>
                                    </q-popover>
                                  </q-btn>
                                </div>
                                <input id="archivo" style="display:none" inverted color="dark" stack-label="Archivo" type="file" name="" value="" ref="cartasInput" accept=".png, .jpg, .jpeg, .docx, .pdf, .PNG, .JPG, .JPEG, .DOCX, .PDF" @change="uploadCartas()" />
                                <q-btn small flat @click="selectedCarta(props.row)" color="green-9" :loading="loadingButton">
                                  <q-icon name="cloud_upload" />&nbsp;
                                  <q-tooltip>Subir carta</q-tooltip>
                                </q-btn>
                              </q-td>
                            </q-tr>
                          </template>
                        </q-table>
                        <q-inner-loading :visible="form_cartas.loading">
                          <q-spinner-dots size="64px" color="primary" />
                        </q-inner-loading>
                      </div>
                    <div class="col-sm-3"></div>
                  </div>
                </q-tab-pane>
                <q-tab-pane name="generales"> -->
                  <div class="row q-mt-lg">
                    <div class="col-sm-4"></div>
                    <div class="col-sm-4">
                      <q-field inverted color="dark" :error="$v.form_documentos.fields.descripcion.$error" error-label="Escriba un nombre o descripción">
                        <q-input upper-case v-model="form_documentos.fields.descripcion" type="text" inverted color="dark" float-label="Nombre archivo" maxlength="200" />
                      </q-field>
                    </div>
                    <div class="col-sm-4"></div>
                  </div>
                  <div class="row">
                    <div class="col-sm-4"></div>
                    <div class="col-sm-2">
                      <q-field>
                        <input style="display:none" inverted color="dark" stack-label="Logo" @input="checkDocumentos()" type="file" name="" value="" ref="fileDocumentos" accept=".jpg,.jpeg,.png,.pdf,.docx" />
                        <q-btn v-if="form_documentos.showSelectedFile !== true"  @click="$refs.fileDocumentos.click()" color="tertiary" icon="fa fa-upload" :loading="loadingButton">&nbsp;Examinar</q-btn>
                        <q-btn v-else @click="$refs.fileDocumentos.click()" color="green" icon="fa fa-check-circle" :loading="loadingButton">&nbsp;Examinar</q-btn>
                        <!--                      <i class="fa fa-check-circle" v-if="showSelectedFile === true" aria-hidden="true" style="color: #21ba45; font-size: 20px; text-align: center; text-wrap: avoid"></i>-->
                      </q-field>
                    </div>
                    <div class="col-sm-2">
                      <q-btn @click="uploadDocumentos()" style="float: right" inverted color="green" icon-right="save" :loading="loadingButton">Guardar</q-btn>
                    </div>
                  </div>
                  <div class="row q-mt-lg">
                  </div>
                  <div class="row">
                    <div class="col-sm-3"></div>
                      <div class="col-sm-6">
                        <q-table
                          :data="form_documentos.data"
                          :columns="form_documentos.columns"
                          title="Documentos"
                          :selected.sync="form_documentos.selected"
                          :dense="true"
                          :pagination.sync="form_documentos.pagination"
                          :loading="form_documentos.loading">
                          <template slot="body" slot-scope="props">
                            <q-tr :props="props">
                              <q-td key="descripcion" :props="props">{{ props.row.descripcion }}</q-td>
                              <q-td key="acciones" :props="props">
                                <q-btn small flat @click="verDocumento(props.row.id, props.row.extension)" color="green" icon="fas fa-eye">
                                  <q-tooltip>Ver</q-tooltip>
                                </q-btn>
                                <q-btn small flat @click="descargarDocumento(props.row.id, props.row.extension)" color="green" icon="fas fa-download">
                                  <q-tooltip>Descargar</q-tooltip>
                                </q-btn>
                                <q-btn small flat @click="deleteDocumento(props.row.id)" color="negative" icon="delete">
                                  <q-tooltip>Eliminar</q-tooltip>
                                </q-btn>
                              </q-td>
                            </q-tr>
                          </template>
                        </q-table>
                        <q-inner-loading :visible="form_documentos.loading">
                          <q-spinner-dots size="64px" color="primary" />
                        </q-inner-loading>
                      </div>
                    <div class="col-sm-3"></div>
                  </div>
                <!-- </q-tab-pane>
              </q-tabs> -->
            </q-tab-pane>
            <q-tab-pane name="aptitudes">
              <div class="row q-mt-lg" style="padding-left: 15px;">
                <div class="col-sm-12">
                  <!-- <q-chips-input v-model="form_aptitudes.aptitudes_perfiles" stack-label="Aptitudes"> -->
                    <!-- <q-autocomplete @search="search" @selected="selected" @input="nuevoItem" @remove="eliminarItem"/> -->
                  <!-- </q-chips-input> -->
                  <div v-for="apt in form_aptitudes.aptitudes_perfiles" :key="apt.id" style="display: inline-grid; padding-right: 5px; padding-bottom: 10px;"><q-chip style="border-radius: 0.25rem;" @hide="remove(apt.id)" closable color="purple">{{ apt.aptitud }}</q-chip></div>
                </div>
                <div class="col-sm-3">
                  <q-search v-model="form_aptitudes.aptitud" inverted color="dark" placeholder="Escriba el nombre de la aptitud">
                    <q-autocomplete @search="search" @selected="selected" />
                  </q-search>
                </div>
                <div class="col-sm-2" style="padding-left: 10px;">
                  <q-btn @click="agregarAptitud()" color="green" icon="add" class="btn_guardar">Agregar aptitud</q-btn>
                </div>
              </div>
            </q-tab-pane>
            <q-tab-pane name="participacion">
              <div class="row q-mt-lg subtitulos">
                Licitaciones
              </div>
              <div class="row q-mt-lg">
                    <!-- tabal posgrados -->
                      <div class="row" style="width:60vw;">
                        <q-search hide-underline color="secondary" v-model="form.filter"/>
                      </div>
                      <div class="col-sm-12 q-mt-sm" id="sticky-table-scroll-corto">
                        <q-table id="sticky-table"
                          :data="form_participacion_licitaciones.data"
                          :columns="form_participacion_licitaciones.columns"
                          :selected.sync="form_participacion_licitaciones.selected"
                          :filter="form_participacion_licitaciones.filter"
                          color="positive"
                          title=""
                          :dense="true"
                          :pagination.sync="form_participacion_licitaciones.pagination"
                          :loading="form_participacion_licitaciones.loading"
                          :rows-per-page-options="form_participacion_licitaciones.rowsOptions">
                          <div>
                          </div>
                          <template slot="body" slot-scope="props">
                            <q-tr :props="props">
                              <q-td key="folio" :props="props">{{ props.row.folio }}</q-td>
                              <q-td key="descripcion" :props="props">{{ props.row.descripcion }}</q-td>
                              <q-td key="participacion" :props="props">{{ props.row.participacion }}</q-td>
                              <q-td key="created" :props="props">{{ props.row.created }}</q-td>
                            </q-tr>
                          </template>
                        </q-table>
                        <q-inner-loading :visible="form.loading">
                          <q-spinner-dots size="64px" color="primary" />
                        </q-inner-loading>
                      </div>
                    <!-- -->
                  </div>
            </q-tab-pane>
          </q-tabs>
          </div>
          </div>
          </div>

        </div>
      </div>

      <q-modal v-if="informacion" style="background-color: rgba(0,0,0,0.7);" v-model="informacion" :content-css="{width: '40vw', height: '200px'}">
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
          <div class="row q-mt-lg">
            <div class="col-sm-12" v-if="objetoInformacion!==null">
              <div style="font-weight:bold;font-size:1.2em;text-align:center;">{{objetoInformacion.nombre}}</div>
            </div>
          </div>
        </q-modal-layout>
      </q-modal>
  </q-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { required, maxLength, minLength, email, helpers } from 'vuelidate/lib/validators'
import { filter } from 'quasar'
import moment from 'moment'
import axios from 'axios'
// const rfc = helpers.regex('rfc', /[A-ZÑ&]{3,4}\d{6}[A-V1-9][A-Z1-9][0-9A]/)
const curp = helpers.regex('curp', /^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$/)

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
        if (propiedades[i].toUpperCase() === 'admin'.toUpperCase() || propiedades[i].toUpperCase() === 'ROOT'.toUpperCase() || propiedades[i].toUpperCase() === 'CLIENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'LIDER'.toUpperCase() || propiedades[i].toUpperCase() === 'COORDINADOR'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE DE OPERACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'PMO'.toUpperCase() || propiedades[i].toUpperCase() === 'FINANZAS'.toUpperCase() || propiedades[i].toUpperCase() === 'OPERACIÓN'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES'.toUpperCase() || propiedades[i].toUpperCase() === 'LICITACIONES - SOLICITUDES'.toUpperCase() || propiedades[i].toUpperCase() === 'GERENTE'.toUpperCase() || propiedades[i].toUpperCase() === 'AUXILIAR'.toUpperCase() || propiedades[i].toUpperCase() === 'COBRANZA'.toUpperCase() || propiedades[i].toUpperCase() === 'VENTAS'.toUpperCase() || propiedades[i].toUpperCase() === 'Coordinador GDP'.toUpperCase()) {
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
      tab: 'general',
      tab2: 'posgrados',
      tab3: 'empleos',
      tab4: 'licitaciones',
      loadingButton: false,
      informacion: false,
      objetoInformacion: null,
      municipiosOptions: [{label: '---Seleccione---', value: 0}],
      views: {
        grid: true,
        create: false,
        update: false
      },
      viewsPosgrado: {
        grid: true,
        create: false,
        update: false
      },
      viewsCurso: {
        grid: true,
        create: false,
        update: false
      },
      viewsCursoImpartido: {
        grid: true,
        create: false,
        update: false
      },
      viewsEmpleo: {
        grid: true,
        create: false,
        update: false
      },
      form_busqueda: {
        aptitudes_data: [],
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
      form: {
        data: [],
        fields: {
          id: 0,
          apellido_paterno: '',
          apellido_materno: '',
          nombre: '',
          telefono: '',
          rfc: '',
          curp: '',
          calle: '',
          numero_exterior: 0,
          numero_interior: 0,
          colonia: '',
          codigo_postal: '',
          estado_id: 0,
          municipio_id: 0,
          email: '',
          foto: '',
          licenciatura: '',
          fecha_egreso: '',
          universidad: '',
          cedula: '',
          titulo_certificado: '',
          extension: '',
          cedula_archivo: '',
          extension_cedula: '',
          area_id: 0,
          titulos: [],
          cedulas: []
        },
        // data: [],
        columnsAptitudes: [
          { name: 'nombre', label: 'Nombre', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'licenciatura', label: 'Licenciatura', field: 'licenciatura', sortable: true, type: 'string', align: 'left' },
          { name: 'area', label: 'Área', field: 'area', sortable: true, type: 'string', align: 'left' },
          { name: 'aptitudes', label: 'Aptitudes', field: 'aptitudes', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        columns: [
          { name: 'nombre', label: 'Nombre', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'licenciatura', label: 'Licenciatura', field: 'licenciatura', sortable: true, type: 'string', align: 'left' },
          { name: 'area', label: 'Área', field: 'area', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_posgrados: {
        data: [],
        fields: {
          id: 0,
          perfil_id: 0,
          nombre: '',
          universidad: '',
          fecha_egreso: ''
        },
        columns: [
          { name: 'nombre', label: 'Nombre', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'universidad', label: 'Universidad', field: 'universidad', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha_egreso', label: 'Fecha egreso', field: 'fecha_egreso', sortable: true, type: 'string', align: 'left' },
          { name: 'titulo', label: 'Documentos', field: 'titulo', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_cursos: {
        data: [],
        fields: {
          id: 0,
          perfil_id: 0,
          nombre: '',
          universidad: '',
          fecha: '',
          fecha_egreso: '',
          tipo: '',
          horas: 0
        },
        columns: [
          { name: 'nombre', label: 'Nombre', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'universidad', label: 'Universidad', field: 'universidad', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha', label: 'Fecha inicio', field: 'fecha', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha_egreso', label: 'Fecha fin', field: 'fecha_egreso', sortable: true, type: 'string', align: 'left' },
          { name: 'horas', label: 'Horas', field: 'horas', sortable: true, type: 'string', align: 'left' },
          { name: 'titulo', label: 'Documentos', field: 'titulo', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_cursos_impartidos: {
        data: [],
        fields: {
          id: 0,
          perfil_id: 0,
          nombre: '',
          universidad: '',
          fecha: '',
          fecha_egreso: '',
          tipo: '',
          horas: 0
        },
        columns: [
          { name: 'nombre', label: 'Nombre', field: 'nombre', sortable: true, type: 'string', align: 'left' },
          { name: 'universidad', label: 'Universidad', field: 'universidad', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha', label: 'Fecha inicio', field: 'fecha', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha_egreso', label: 'Fecha fin', field: 'fecha_egreso', sortable: true, type: 'string', align: 'left' },
          { name: 'horas', label: 'Horas', field: 'horas', sortable: true, type: 'string', align: 'left' },
          { name: 'titulo', label: 'Documentos', field: 'titulo', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_empleos: {
        data: [],
        fields: {
          id: 0,
          perfil_id: 0,
          empresa: '',
          puesto: '',
          fecha_ingreso: '',
          fecha_egreso: '',
          laborando: 'NO'
        },
        columns: [
          { name: 'empresa', label: 'Empresa', field: 'empresa', sortable: true, type: 'string', align: 'left' },
          { name: 'puesto', label: 'Puesto', field: 'puesto', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha_ingreso', label: 'Fecha ingreso', field: 'fecha_ingreso', sortable: true, type: 'string', align: 'left' },
          { name: 'fecha_egreso', label: 'Fecha baja', field: 'fecha_egreso', sortable: true, type: 'string', align: 'left' },
          { name: 'laborando', label: 'Laborando actualmente', field: 'laborando', sortable: true, type: 'string', align: 'left' },
          { name: 'cartas', label: 'Cartas de recomendación', field: 'cartas', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_cartas: {
        data: [],
        columns: [
          { name: 'tipo', label: 'Tipo', field: 'tipo', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      form_documentos: {
        data: [],
        showSelectedFile: false,
        fields: {
          id: 0,
          perfil_id: 0,
          descripcion: '',
          extension: ''
        },
        columns: [
          { name: 'descripcion', label: 'Nombre', field: 'descripcion', sortable: true, type: 'string', align: 'left' },
          { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
        ],
        rowsOptions: [3, 5, 7, 10, 15, 20, 25, 50],
        pagination: { rowsPerPage: 50 },
        selected: [],
        filter: '',
        loading: false
      },
      terms: '',
      form_aptitudes: {
        data: [],
        aptitud: '',
        aptitudes_perfiles: []
      },
      form_participacion_licitaciones: {
        data: [],
        columns: [
          { name: 'folio', label: 'Folio', field: 'folio', sortable: true, type: 'string', align: 'left' },
          { name: 'descripcion', label: 'Descripción', field: 'descripcion', sortable: true, type: 'string', align: 'left' },
          { name: 'participacion', label: 'Participación', field: 'participacion', sortable: true, type: 'string', align: 'left' },
          { name: 'created', label: 'Asignado', field: 'created', sortable: true, type: 'string', align: 'left' }
          // { name: 'acciones', label: 'Acciones', field: 'acciones', sortable: false, type: 'string', align: 'right' }
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
      perfiles: 'per/perfiles/perfiles',
      aptitudes: 'per/aptitudes/aptitudes'
    }),
    estadosOptions () {
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
    areasOptions () {
      let areas = this.$store.getters['per/areas/selectOptions']
      areas.push({label: '---Seleccione---', value: 0})
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
    }
  },
  methods: {
    ...mapActions({
      getPerfiles: 'per/perfiles/refresh',
      savePerfil: 'per/perfiles/save',
      updatePerfil: 'per/perfiles/update',
      getTitulosByPerfil: 'per/titulos/getByPerfil',
      getCedulasByPerfil: 'per/cedulas/getByPerfil',
      deleteTituloProfesional: 'per/titulos/deleteFormato',
      deleteCedulaProfesional: 'per/cedulas/deleteFormato',
      deletePerfil: 'per/perfiles/delete',
      getEstados: 'vnt/estado/refresh',
      getMunicipiosByEstado: 'vnt/municipio/getByEstado',
      getAreas: 'per/areas/refresh',
      getPosgradosByPerfil: 'per/posgrados/getByPerfil',
      savePosgrado: 'per/posgrados/save',
      updatePosgrado: 'per/posgrados/update',
      deletePosgrado: 'per/posgrados/delete',
      deleteTituloPosgrado: 'per/posgrados/deleteFormato',
      getCursosByPerfil: 'per/cursos/getByPerfil',
      saveCurso: 'per/cursos/save',
      updateCurso: 'per/cursos/update',
      deleteCurso: 'per/cursos/delete',
      deleteTituloCurso: 'per/cursos/deleteFormato',
      getEmpleosByPerfil: 'per/empleos/getByPerfil',
      saveEmpleo: 'per/empleos/save',
      updateEmpleo: 'per/empleos/update',
      deleteEmpleo: 'per/empleos/delete',
      getCartasByPerfil: 'per/cartas/getByPerfil',
      deleteCarta: 'per/cartas/deleteFormato',
      getDocumentosByPerfil: 'per/documentos/getByPerfil',
      deleteDocumentoPerfil: 'per/documentos/deleteFormato',
      getAptitudes: 'per/aptitudes/refresh',
      getAptitudesByPerfil: 'per/aptitudes_perfiles/getByPerfil',
      saveAptitudByPerfil: 'per/aptitudes_perfiles/save',
      deleteAptitudByPerfil: 'per/aptitudes_perfiles/delete',
      filtrarPerfiles: 'per/perfiles/filtrar',
      getLicitacionesByPerfil: 'per/licitaciones_perfiles/getByPerfil'
    }),
    async loadAll () {
      this.form.loading = true
      await this.getEstados()
      await this.getAreas()
      await this.cargarAptitudesBusqueda()
      await this.filtrarP()
      this.form.loading = false
    },
    setView (view) {
      this.views.grid = false
      this.views.create = false
      this.views.update = false
      this.views[view] = true
    },
    setViewPosgrado (view) {
      this.viewsPosgrado.grid = false
      this.viewsPosgrado.create = false
      this.viewsPosgrado.update = false
      this.viewsPosgrado[view] = true
    },
    setViewCurso (view) {
      this.viewsCurso.grid = false
      this.viewsCurso.create = false
      this.viewsCurso.update = false
      this.viewsCurso[view] = true
    },
    setViewCursoImpartido (view) {
      this.viewsCursoImpartido.grid = false
      this.viewsCursoImpartido.create = false
      this.viewsCursoImpartido.update = false
      this.viewsCursoImpartido[view] = true
    },
    setViewEmpleo (view) {
      this.viewsEmpleo.grid = false
      this.viewsEmpleo.create = false
      this.viewsEmpleo.update = false
      this.viewsEmpleo[view] = true
    },
    isNumber (evt, input) {
      switch (input) {
        case 'telefono':
          this.form.fields.telefono = this.form.fields.telefono.replace(/[^0-9.]/g, '')
          this.$v.form.fields.telefono.$touch()
          break
        case 'numero_exterior':
          this.form.fields.numero_exterior = this.form.fields.numero_exterior.replace(/[^0-9.]/g, '')
          this.$v.form.fields.numero_exterior.$touch()
          break
        case 'numero_interior':
          this.form.fields.numero_interior = this.form.fields.numero_interior.replace(/[^0-9.]/g, '')
          this.$v.form.fields.numero_interior.$touch()
          break
        case 'codigo_postal':
          this.form.fields.codigo_postal = this.form.fields.codigo_postal.replace(/[^0-9.]/g, '')
          this.$v.form.fields.codigo_postal.$touch()
          break
        default:
          break
      }
    },
    atras () {
      this.loadAll()
      this.setView('grid')
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
    async cargaMunicipios2 () {
      this.form_busqueda.municipiosOptions = []
      this.form_busqueda.fields.municipio_id = 0
      await this.getMunicipiosByEstado({estado_id: this.form_busqueda.fields.estado_id}).then(({data}) => {
        this.form_busqueda.municipiosOptions.push({label: '---Seleccione---', value: 0})
        if (data.municipios.length > 0) {
          data.municipios.forEach(municipio => {
            this.form_busqueda.municipiosOptions.push({label: municipio.nombre, value: municipio.id})
          })
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async all_data_by_perfil () {
      this.$q.loading.show({message: 'Cargando...'})
      this.limpiarPosgrado()
      await this.getPosgrados()
      this.limpiarCurso()
      await this.getCursos()
      this.limpiarCursoImpartido()
      await this.getCursosImpartidos()
      this.limpiarEmpleo()
      await this.getEmpleos()
      await this.getDocumentos()
      await this.getCartas()
      await this.cargarAptitudes()
      await this.cargarAptitudesByPerfil()
      this.$q.loading.hide()
    },
    save_perfil () {
      this.form.fields.nombre = this.form.fields.nombre.trim()
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        this.savePerfil(params).then(({data}) => {
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
            // this.loadAll()
            this.form.fields.id = data.id
            if (data.foto === null) {
              this.form.fields.foto = ''
            } else {
              this.form.fields.foto = process.env.API + '/public/assets/fotos_perfiles/' + data.foto + '?' + Math.random()
            }
            this.all_data_by_perfil()
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
    update () {
      this.form.fields.nombre = this.form.fields.nombre.trim()
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        let params = this.form.fields
        this.updatePerfil(params).then(({data}) => {
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
            // this.loadAll()
            // this.setView('grid')
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
      this.tab = 'general'
      this.form.fields.nombre = row.nombre
      this.form.fields.id = row.id
      this.form.fields.apellido_paterno = row.apellido_paterno
      this.form.fields.apellido_materno = row.apellido_materno
      this.form.fields.nombre = row.nombre
      this.form.fields.telefono = row.telefono
      this.form.fields.rfc = row.rfc
      this.form.fields.curp = row.curp
      this.form.fields.calle = row.calle
      this.form.fields.numero_exterior = row.numero_exterior
      this.form.fields.numero_interior = row.numero_interior
      this.form.fields.colonia = row.colonia
      this.form.fields.codigo_postal = row.codigo_postal
      this.form.fields.estado_id = row.estado_id
      this.form.fields.municipio_id = row.municipio_id
      this.form.fields.email = row.email
      if (row.foto === null) {
        this.form.fields.foto = ''
      } else {
        this.form.fields.foto = process.env.API + '/public/assets/fotos_perfiles/' + row.foto + '?' + Math.random()
      }
      this.form.fields.licenciatura = row.licenciatura
      this.form.fields.cedula = row.cedula
      this.form.fields.universidad = row.universidad
      this.form.fields.fecha_egreso = row.fecha_egreso
      this.form.fields.titulo_certificado = row.titulo_certificado
      this.form.fields.extension = row.extension
      this.form.fields.cedula_archivo = row.cedula_archivo
      this.form.fields.extension_cedula = row.extension_cedula
      this.form.fields.area_id = row.area_id
      await this.cargaMunicipios()
      this.limpiarPosgrado()
      await this.getPosgrados()
      this.limpiarCurso()
      await this.getCursos()
      this.limpiarCursoImpartido()
      await this.getCursosImpartidos()
      this.limpiarEmpleo()
      await this.getEmpleos()
      await this.getDocumentos()
      await this.getCartas()
      await this.cargarAptitudes()
      await this.cargarAptitudesByPerfil()
      console.log(this.form_aptitudes.lista_aptitudes)
      console.log(this.form_aptitudes.terms)
      console.log(this.form_aptitudes.aptitudes_perfiles)
      this.$q.loading.hide()
      this.setView('update')
    },
    deleteSingleRow (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este perfil?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete(id)
      }).catch(() => {})
    },
    delete (id) {
      let params = {id: id}
      this.deletePerfil(params).then(({data}) => {
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
      this.tab = 'general'
      this.$v.form.$reset()
      this.form.fields.id = 0
      this.form.fields.apellido_paterno = ''
      this.form.fields.apellido_materno = ''
      this.form.fields.nombre = ''
      this.form.fields.telefono = ''
      this.form.fields.rfc = ''
      this.form.fields.curp = ''
      this.form.fields.calle = ''
      this.form.fields.numero_exterior = ''
      this.form.fields.numero_interior = ''
      this.form.fields.colonia = ''
      this.form.fields.codigo_postal = ''
      this.form.fields.estado_id = 0
      this.form.fields.municipio_id = 0
      this.form.fields.email = ''
      this.form.fields.foto = ''
      this.form.fields.licenciatura = ''
      this.form.fields.cedula = ''
      this.form.fields.universidad = ''
      this.form.fields.fecha_egreso = ''
      this.form.fields.titulo_certificado = null
      this.form.fields.extension = ''
      this.form.fields.cedula_archivo = null
      this.form.fields.extension_cedula = ''
      this.form.fields.area_id = 0
      this.setView('create')
    },
    clickFila (row) {
      this.informacion = true
      this.objetoInformacion = row
    },
    async save () {
      let file = this.$refs.fileInputFormato.files[0]
      if (file !== null && file !== undefined) {
        this.form.fields.logo = file.name
      }
      this.$v.form.$touch()
      if (!this.$v.form.$error) {
        this.loadingButton = true
        if (file === null || file === undefined) {
          this.save_perfil()
        } else {
          let formData = new FormData()
          if (file.type.split('/')[1] === 'jpg' || file.type.split('/')[1] === 'jpeg' || file.type.split('/')[1] === 'png') {
            formData.append('file', file)
            formData.append('apellido_paterno', this.form.fields.apellido_paterno)
            formData.append('apellido_materno', this.form.fields.apellido_materno)
            formData.append('nombre', this.form.fields.nombre)
            formData.append('telefono', this.form.fields.telefono)
            formData.append('rfc', this.form.fields.rfc)
            formData.append('curp', this.form.fields.curp)
            formData.append('calle', this.form.fields.calle)
            formData.append('numero_exterior', this.form.fields.numero_exterior)
            formData.append('numero_interior', this.form.fields.numero_interior)
            formData.append('colonia', this.form.fields.colonia)
            formData.append('codigo_postal', this.form.fields.codigo_postal)
            formData.append('estado_id', this.form.fields.estado_id)
            formData.append('municipio_id', this.form.fields.municipio_id)
            formData.append('email', this.form.fields.email)
            formData.append('foto', this.form.fields.foto)
            axios.post('perfiles/save', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              if (response.data.result === 'success') {
                this.$q.notify({
                // only required parameter is the message:
                  message: response.data.message.content,
                  timeout: 3000,
                  type: 'green',
                  textColor: 'white', // if default 'white' doesn't fits
                  icon: 'done',
                  position: 'top-right' // 'top', 'left', 'bottom-left' etc
                })
                // this.loadAll()
                this.form.fields.id = response.data.id
                if (response.data.foto === null) {
                  this.form.fields.foto = ''
                } else {
                  this.form.fields.foto = process.env.API + '/public/assets/fotos_perfiles/' + response.data.foto + '?' + Math.random()
                }
                this.all_data_by_perfil()
                this.setView('update')
              } else {
                if (response.data.err !== '') {
                  this.$showMessage('Errores en archivo', response.data.err)
                }
              }
              this.loadingButton = false
            }).catch(error => {
              console.error(error)
            })
          } else {
            this.$showMessage('Error', 'No ha seleccionado un archivo con las extensión .jpg, .jpeg o .png')
          }
        }
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    uploadFotoFile () {
      try {
        let file = this.$refs.fotoactInput.files[0]
        let formData = new FormData()
        formData.append('file', file)
        formData.append('nombre', file.name)
        formData.append('id', this.form.fields.id)
        formData.append('curp', this.form.fields.curp)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            //
            axios.post('perfiles/uploadFoto', formData, {
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
                this.form.fields.foto = process.env.API + '/public/assets/fotos_perfiles/' + response.data.foto + '?' + Math.random()
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
            //
          } else {
            this.$showMessage('Error', 'No ha seleccionado un archivo con la extensión .jpg, .jpeg, .png')
          }
        } else {
          this.$showMessage('Error', 'No ha seleccionado un archivo con la extensión .jpg, .jpeg, .png')
        }
      } catch (error) {
        console.log(error)
      }
    },
    async getTitulos () {
      this.form.fields.titulos = []
      await this.getTitulosByPerfil({perfil_id: this.form.fields.id}).then(({data}) => {
        this.form.fields.titulos = data.titulos
      }).catch(error => {
        console.error(error)
      })
    },
    uploadTituloProfesional () {
      try {
        this.loadingButton = true
        let file = this.$refs.fileInputTitulo.files[0]
        let formData = new FormData()
        formData.append('file', file)
        formData.append('nombre', file.name)
        formData.append('tipo', 'titulo')
        formData.append('id', this.form.fields.id)
        if (file === null || file === undefined) {
          this.loadingButton = false
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('titulos/uploadTitulo', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              if (response.data.result === 'success') {
                this.$q.notify({
                  // only required parameter is the message:
                  message: response.data.message.content,
                  timeout: 3000,
                  type: 'green',
                  textColor: 'white', // if default 'white' doesn't fits
                  icon: 'fas fa-check',
                  position: 'top-right' // 'top', 'left', 'bottom-left' etc
                })
                this.getTitulos()
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
          this.$showMessage('Error', 'No ha seleccionado un archivo con las extensión .docx o .pdf')
        }
      } catch (error) {
        this.loadingButton = false
      }
    },
    async getCedulas () {
      this.form.fields.cedulas = []
      await this.getCedulasByPerfil({perfil_id: this.form.fields.id}).then(({data}) => {
        this.form.fields.cedulas = data.cedulas
      }).catch(error => {
        console.error(error)
      })
    },
    cargarDocs () {
      this.getTitulos()
      this.getCedulas()
    },
    uploadCedulaProfesional () {
      try {
        this.loadingButton = true
        let file = this.$refs.fileInputCedula.files[0]
        let formData = new FormData()
        formData.append('file', file)
        formData.append('nombre', file.name)
        formData.append('tipo', 'cedula')
        formData.append('id', this.form.fields.id)
        if (file === null || file === undefined) {
          this.loadingButton = false
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('cedulas/uploadCedula', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then(response => {
              if (response.data.result === 'success') {
                this.$q.notify({
                  // only required parameter is the message:
                  message: response.data.message.content,
                  timeout: 3000,
                  type: 'green',
                  textColor: 'white', // if default 'white' doesn't fits
                  icon: 'fas fa-check',
                  position: 'top-right' // 'top', 'left', 'bottom-left' etc
                })
                this.getCedulas()
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
          this.$showMessage('Error', 'No ha seleccionado un archivo con las extensión .docx o .pdf')
        }
      } catch (error) {
        this.loadingButton = false
      }
    },
    verTitulo (id, ext, tipo) {
      if (tipo === 'titulo') {
        let uri = process.env.API + '/public/assets/titulos_perfiles/' + id + '.' + ext
        window.open(uri, '_blank')
      } else {
        let uri = process.env.API + '/public/assets/cedulas_perfiles/' + id + '.' + ext
        window.open(uri, '_blank')
      }
    },
    descargarTitulo (id, ext) {
      let uri = process.env.API + `titulos/getFile/${id}/${ext}`
      window.open(uri, '_blank')
    },
    descargarCedula (id, ext) {
      let uri = process.env.API + `cedulas/getFile/${id}/${ext}`
      window.open(uri, '_blank')
    },
    deleteTitulo (id, tipo) {
      this.$q.dialog({
        title: 'Confirmar acción',
        message: '¿Desea eliminar el archivo?',
        ok: 'Eliminar',
        cancel: 'Cancelar',
        preventClose: true
      }).then(() => {
        this.delete_titulo(id, tipo)
      }).catch(() => {
      })
    },
    delete_titulo (id, tipo) {
      this.deleteTituloProfesional({id: id, tipo: tipo}).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'delete', position: 'top-right'
          })
          this.getTitulos()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    deleteCedula (id, tipo) {
      this.$q.dialog({
        title: 'Confirmar acción',
        message: '¿Desea eliminar el archivo?',
        ok: 'Eliminar',
        cancel: 'Cancelar',
        preventClose: true
      }).then(() => {
        this.delete_cedula(id, tipo)
      }).catch(() => {
      })
    },
    delete_cedula (id, tipo) {
      this.deleteCedulaProfesional({id: id, tipo: tipo}).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'delete', position: 'top-right'
          })
          this.getCedulas()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async getPosgrados () {
      this.form_posgrados.data = []
      await this.getPosgradosByPerfil({perfil_id: this.form.fields.id}).then(({data}) => {
        this.form_posgrados.data = data.posgrados
      }).catch(error => {
        console.error(error)
      })
    },
    limpiarPosgrado () {
      this.form_posgrados.fields.nombre = ''
      this.form_posgrados.fields.universidad = ''
      this.form_posgrados.fields.fecha_egreso = ''
      this.form_posgrados.fields.perfil_id = this.form.fields.id
      this.$v.form_posgrados.$reset()
      this.setViewPosgrado('create')
    },
    save_posgrado () {
      this.$v.form_posgrados.$touch()
      if (!this.$v.form_posgrados.$error) {
        this.loadingButton = true
        let params = this.form_posgrados.fields
        this.savePosgrado(params).then(({data}) => {
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
            this.limpiarPosgrado()
            this.getPosgrados()
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
    editRowPosgrado (row) {
      this.form_posgrados.fields.nombre = row.nombre
      this.form_posgrados.fields.universidad = row.universidad
      this.form_posgrados.fields.fecha_egreso = moment(String(row.fecha_egreso)).utcOffset('-04:00:00', false).format('YYYY-MM-DD HH:mm:ss')
      this.form_posgrados.fields.id = row.id
      this.form_posgrados.fields.perfil_id = this.form.fields.id
      this.setViewPosgrado('update')
    },
    update_posgrado () {
      this.$v.form_posgrados.$touch()
      if (!this.$v.form_posgrados.$error) {
        this.loadingButton = true
        let params = this.form_posgrados.fields
        this.updatePosgrado(params).then(({data}) => {
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
            this.limpiarPosgrado()
            this.getPosgrados()
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
    deleteSingleRowPosgrado (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este posgrado?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete_posgrado(id)
      }).catch(() => {})
    },
    delete_posgrado (id) {
      let params = {id: id}
      this.deletePosgrado(params).then(({data}) => {
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
          this.getPosgrados()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    selectedTituloPosgrado (row) {
      this.$refs.tituloPosgradoInput.value = ''
      this.registro_posgrado = row
      this.$refs.tituloPosgradoInput.click()
    },
    uploadTituloPosgrado () {
      try {
        let file = this.$refs.tituloPosgradoInput.files[0]
        let formData = new FormData()
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('id', this.registro_posgrado.id)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('posgrados/uploadDocumento', formData, {
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
                this.getPosgrados()
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
    verTituloPosgrado (id, ext) {
      let uri = process.env.API + '/public/assets/posgrados_perfiles/' + id + '.' + ext
      window.open(uri, '_blank')
    },
    descargarTituloPosgrado (id, ext) {
      let uri = process.env.API + `posgrados/getFile/${id}/${ext}`
      window.open(uri, '_blank')
    },
    deleteRowTituloPosgrado (id) {
      this.$q.dialog({
        title: 'Confirmar acción',
        message: '¿Desea eliminar el archivo?',
        ok: 'Eliminar',
        cancel: 'Cancelar',
        preventClose: true
      }).then(() => {
        this.delete_titulo_posgrado(id)
      }).catch(() => {
      })
    },
    delete_titulo_posgrado (id) {
      this.deleteTituloPosgrado({id: id}).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'delete', position: 'top-right'
          })
          this.getPosgrados()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async getCursos () {
      this.form_cursos.data = []
      await this.getCursosByPerfil({perfil_id: this.form.fields.id, tipo: 'recibido'}).then(({data}) => {
        this.form_cursos.data = data.cursos
      }).catch(error => {
        console.error(error)
      })
    },
    async getCursosImpartidos () {
      this.form_cursos_impartidos.data = []
      await this.getCursosByPerfil({perfil_id: this.form.fields.id, tipo: 'impartido'}).then(({data}) => {
        this.form_cursos_impartidos.data = data.cursos
      }).catch(error => {
        console.error(error)
      })
    },
    limpiarCurso () {
      this.form_cursos.fields.nombre = ''
      this.form_cursos.fields.universidad = ''
      this.form_cursos.fields.fecha_egreso = ''
      this.form_cursos.fields.fecha = ''
      this.form_cursos.fields.perfil_id = this.form.fields.id
      this.form_cursos.fields.tipo = 'recibido'
      this.form_cursos.fields.horas = 0
      this.$v.form_cursos.$reset()
      this.setViewCurso('create')
    },
    limpiarCursoImpartido () {
      this.form_cursos_impartidos.fields.nombre = ''
      this.form_cursos_impartidos.fields.universidad = ''
      this.form_cursos_impartidos.fields.fecha_egreso = ''
      this.form_cursos_impartidos.fields.fecha = ''
      this.form_cursos_impartidos.fields.perfil_id = this.form.fields.id
      this.form_cursos_impartidos.fields.tipo = 'impartido'
      this.form_cursos_impartidos.fields.horas = 0
      this.$v.form_cursos_impartidos.$reset()
      this.setViewCursoImpartido('create')
    },
    save_curso () {
      this.$v.form_cursos.$touch()
      if (!this.$v.form_cursos.$error) {
        this.loadingButton = true
        let params = this.form_cursos.fields
        this.saveCurso(params).then(({data}) => {
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
            this.limpiarCurso()
            this.getCursos()
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
    save_curso_impartido () {
      this.$v.form_cursos_impartidos.$touch()
      if (!this.$v.form_cursos_impartidos.$error) {
        this.loadingButton = true
        let params = this.form_cursos_impartidos.fields
        this.saveCurso(params).then(({data}) => {
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
            this.limpiarCursoImpartido()
            this.getCursosImpartidos()
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
    editRowCurso (row) {
      this.form_cursos.fields.nombre = row.nombre
      this.form_cursos.fields.universidad = row.universidad
      this.form_cursos.fields.fecha = moment(String(row.fecha)).utcOffset('-04:00:00', false).format('DD/MM/YYYY HH:mm:ss')
      this.form_cursos.fields.fecha_egreso = moment(String(row.fecha_egreso)).utcOffset('-04:00:00', false).format('DD/MM/YYYY HH:mm:ss')
      this.form_cursos.fields.id = row.id
      this.form_cursos.fields.perfil_id = this.form.fields.id
      this.form_cursos.fields.tipo = 'recibido'
      this.form_cursos.fields.horas = row.horas
      this.setViewCurso('update')
    },
    editRowCursoImpartido (row) {
      this.form_cursos_impartidos.fields.nombre = row.nombre
      this.form_cursos_impartidos.fields.universidad = row.universidad
      this.form_cursos_impartidos.fields.fecha = moment(String(row.fecha)).utcOffset('-04:00:00', false).format('DD/MM/YYYY')
      this.form_cursos_impartidos.fields.fecha_egreso = moment(String(row.fecha_egreso)).utcOffset('-04:00:00', false).format('DD/MM/YYYY')
      this.form_cursos_impartidos.fields.id = row.id
      this.form_cursos_impartidos.fields.perfil_id = this.form.fields.id
      this.form_cursos_impartidos.fields.tipo = 'impartido'
      this.form_cursos_impartidos.fields.horas = row.horas
      this.setViewCursoImpartido('update')
    },
    update_curso () {
      this.$v.form_cursos.$touch()
      if (!this.$v.form_cursos.$error) {
        this.loadingButton = true
        let params = this.form_cursos.fields
        this.updateCurso(params).then(({data}) => {
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
            this.limpiarCurso()
            this.getCursos()
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
    update_curso_impartido () {
      this.$v.form_cursos_impartidos.$touch()
      if (!this.$v.form_cursos_impartidos.$error) {
        this.loadingButton = true
        let params = this.form_cursos_impartidos.fields
        this.updateCurso(params).then(({data}) => {
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
            this.limpiarCursoImpartido()
            this.getCursosImpartidos()
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
    deleteSingleRowCurso (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este curso?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete_curso(id)
      }).catch(() => {})
    },
    delete_curso (id) {
      let params = {id: id}
      this.deleteCurso(params).then(({data}) => {
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
          this.getCursos()
          this.getCursosImpartidos()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    selectedTituloCurso (row) {
      this.$refs.tituloCursoInput.value = ''
      this.registro_curso = row
      this.$refs.tituloCursoInput.click()
    },
    selectedTituloCursoImpartido (row) {
      this.$refs.tituloCursoImpartido.value = ''
      this.registro_curso = row
      this.$refs.tituloCursoImpartido.click()
    },
    uploadTituloCurso () {
      try {
        let file = this.$refs.tituloCursoInput.files[0]
        let formData = new FormData()
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('id', this.registro_curso.id)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('cursos/uploadDocumento', formData, {
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
                this.getCursos()
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
    uploadTituloCursoImpartido () {
      try {
        let file = this.$refs.tituloCursoImpartido.files[0]
        let formData = new FormData()
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('id', this.registro_curso.id)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('cursos/uploadDocumento', formData, {
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
                this.getCursosImpartidos()
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
    verTituloCurso (id, ext) {
      let uri = process.env.API + '/public/assets/cursos_perfiles/' + id + '.' + ext
      window.open(uri, '_blank')
    },
    descargarTituloCurso (id, ext) {
      let uri = process.env.API + `cursos/getFile/${id}/${ext}`
      window.open(uri, '_blank')
    },
    deleteRowTituloCurso (id) {
      this.$q.dialog({
        title: 'Confirmar acción',
        message: '¿Desea eliminar el archivo?',
        ok: 'Eliminar',
        cancel: 'Cancelar',
        preventClose: true
      }).then(() => {
        this.delete_titulo_curso(id)
      }).catch(() => {
      })
    },
    delete_titulo_curso (id) {
      this.deleteTituloCurso({id: id}).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'delete', position: 'top-right'
          })
          this.getCursos()
          this.getCursosImpartidos()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async getEmpleos () {
      this.form_empleos.data = []
      await this.getEmpleosByPerfil({perfil_id: this.form.fields.id}).then(({data}) => {
        this.form_empleos.data = data.empleos
      }).catch(error => {
        console.error(error)
      })
    },
    limpiarEmpleo () {
      this.form_empleos.fields.id = 0
      this.form_empleos.fields.empresa = ''
      this.form_empleos.fields.puesto = ''
      this.form_empleos.fields.fecha_ingreso = ''
      this.form_empleos.fields.fecha_egreso = ''
      this.form_empleos.fields.perfil_id = this.form.fields.id
      this.$v.form_empleos.$reset()
      this.setViewEmpleo('create')
    },
    save_empleo () {
      this.$v.form_empleos.$touch()
      if (!this.$v.form_empleos.$error) {
        this.loadingButton = true
        let params = this.form_empleos.fields
        this.saveEmpleo(params).then(({data}) => {
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
            this.limpiarEmpleo()
            this.getEmpleos()
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
    editRowEmpleo (row) {
      this.form_empleos.fields.empresa = row.empresa
      this.form_empleos.fields.puesto = row.puesto
      this.form_empleos.fields.fecha_ingreso = moment(String(row.fecha_ingreso)).utcOffset('-04:00:00', false).format('DD/MM/YYYY HH:mm:ss')
      this.form_empleos.fields.fecha_egreso = moment(String(row.fecha_egreso)).utcOffset('-04:00:00', false).format('DD/MM/YYYY HH:mm:ss')
      this.form_empleos.fields.id = row.id
      this.form_empleos.fields.perfil_id = this.form.fields.id
      this.setViewEmpleo('update')
    },
    update_empleo () {
      this.$v.form_empleos.$touch()
      if (!this.$v.form_empleos.$error) {
        this.loadingButton = true
        let params = this.form_empleos.fields
        this.updateEmpleo(params).then(({data}) => {
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
            this.limpiarEmpleo()
            this.getEmpleos()
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
    deleteSingleRowEmpleo (id) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea borrar este empleo?',
        ok: 'Aceptar',
        cancel: 'Cancelar'
      }).then(() => {
        this.delete_empleo(id)
      }).catch(() => {})
    },
    delete_empleo (id) {
      let params = {id: id}
      this.deleteEmpleo(params).then(({data}) => {
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
          this.getEmpleos()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async getCartas () {
      this.form_cartas.data = []
      await this.getCartasByPerfil({perfil_id: this.form.fields.id}).then(({data}) => {
        this.form_cartas.data = data.cartas
      }).catch(error => {
        console.error(error)
      })
    },
    selectedCarta (row) {
      this.$refs.cartasInput.value = ''
      this.registro_carta = row
      this.$refs.cartasInput.click()
    },
    uploadCartas () {
      try {
        let file = this.$refs.cartasInput.files[0]
        let formData = new FormData()
        formData.append('file', file)
        formData.append('nombre', file.name) // nombre del archivo
        formData.append('empleo_id', this.registro_carta.id)
        formData.append('perfil_id', this.form.fields.id)
        if (file === null || file === undefined) {
        } else if (file.type) {
          if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
            this.$q.loading.show({message: 'Subiendo archivo...'})
            axios.post('cartas/uploadDocumento', formData, {
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
                this.getEmpleos()
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
    verCarta (carta) {
      let uri = process.env.API + '/public/assets/cartas_perfiles/' + carta.id + '.' + carta.extension
      window.open(uri, '_blank')
    },
    descargarCarta (id, ext) {
      let uri = process.env.API + `cartas/getFile/${id}/${ext}`
      window.open(uri, '_blank')
    },
    deleteCartas (id) {
      this.$q.dialog({
        title: 'Confirmar acción',
        message: '¿Desea eliminar el archivo?',
        ok: 'Eliminar',
        cancel: 'Cancelar',
        preventClose: true
      }).then(() => {
        this.delete_carta(id)
      }).catch(() => {
      })
    },
    delete_carta (id) {
      this.deleteCarta({id: id}).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'delete', position: 'top-right'
          })
          this.getEmpleos()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async getDocumentos () {
      this.$v.form_documentos.fields.descripcion.$reset()
      this.form_documentos.data = []
      await this.getDocumentosByPerfil({perfil_id: this.form.fields.id}).then(({data}) => {
        this.form_documentos.data = data.documentos
      }).catch(error => {
        console.error(error)
      })
    },
    checkDocumentos () {
      if (this.$refs.fileDocumentos.files !== null) {
        this.form_documentos.showSelectedFile = true
      }
    },
    uploadDocumentos () {
      this.$v.form_documentos.fields.descripcion.$touch()
      if (!this.$v.form_documentos.fields.descripcion.$error) {
        try {
          this.loadingButton = true
          let file = this.$refs.fileDocumentos.files[0]
          let formData = new FormData()
          formData.append('file', file)
          formData.append('perfil_id', this.form.fields.id)
          formData.append('descripcion', this.form_documentos.fields.descripcion)
          if (file === null || file === undefined || file === '') {
            this.$q.notify({
              // only required parameter is the message:
              message: 'Seleccione un archivo',
              timeout: 3000,
              type: 'orange',
              textColor: 'white', // if default 'white' doesn't fits
              icon: 'warning',
              position: 'top-right' // 'top', 'left', 'bottom-left' etc
            })
            this.loadingButton = false
          } else if (file.name) {
            if (file.type.split('/')[1].toLowerCase() === 'jpg' || file.type.split('/')[1].toLowerCase() === 'jpeg' || file.type.split('/')[1].toLowerCase() === 'png' || file.type.split('/')[1].toLowerCase() === 'pdf' || file.type.split('/')[1].toLowerCase() === 'xml' || file.type.split('/')[1].toLowerCase() === 'vnd.openxmlformats-officedocument.wordprocessingml.document') {
              this.$q.loading.show({message: 'Subiendo archivo...'})
              axios.post('documentos_perfiles/uploadDocumento', formData, {
                headers: {
                  'Content-Type': 'multipart/form-data'
                }
              }).then(response => {
                if (response.data.result === 'success') {
                  this.$q.notify({
                    // only required parameter is the message:
                    message: response.data.message.content,
                    timeout: 3000,
                    type: 'green',
                    textColor: 'white', // if default 'white' doesn't fits
                    icon: 'fas fa-check',
                    position: 'top-right' // 'top', 'left', 'bottom-left' etc
                  })
                  this.getDocumentos()
                  this.form_documentos.showSelectedFile = false
                  this.form_documentos.fields.descripcion = ''
                  this.$refs.fileDocumentos.files[0] = undefined
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
            this.$showMessage('Error', 'No ha seleccionado un archivo con las extensión .jpg, .jpeg, .png, .docx o .pdf')
          }
        } catch (error) {
          this.loadingButton = false
        }
      } else {
        this.$showMessage('¡Advertencia!', 'Por favor revise los campos.')
      }
    },
    verDocumento (id, ext) {
      let uri = process.env.API + '/public/assets/documentos_perfiles/' + id + '.' + ext
      window.open(uri, '_blank')
    },
    descargarDocumento (id, ext) {
      let uri = process.env.API + `documentos_perfiles/getFile/${id}/${ext}`
      window.open(uri, '_blank')
    },
    deleteDocumento (id) {
      this.$q.dialog({
        title: 'Confirmar acción',
        message: '¿Desea eliminar el archivo?',
        ok: 'Eliminar',
        cancel: 'Cancelar',
        preventClose: true
      }).then(() => {
        this.delete_documento(id)
      }).catch(() => {
      })
    },
    delete_documento (id) {
      this.deleteDocumentoPerfil({id: id}).then(({data}) => {
        if (data.result === 'success') {
          this.$q.notify({
            message: data.message.content, timeout: 3000, type: 'green', textColor: 'white', icon: 'delete', position: 'top-right'
          })
          this.getDocumentos()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    async cargarAptitudes () {
      this.form_aptitudes.data = []
      await this.getAptitudes().then(({data}) => {
        this.form_aptitudes.data = data.aptitudes
      }).catch(error => {
        console.error(error)
      })
    },
    async cargarAptitudesByPerfil () {
      this.form_aptitudes.aptitudes_perfiles = []
      await this.getAptitudesByPerfil({perfil_id: this.form.fields.id}).then(({data}) => {
        this.form_aptitudes.aptitudes_perfiles = data.aptitudes_perfiles
      }).catch(error => {
        console.error(error)
      })
    },
    parseAptitudes () {
      return this.form_aptitudes.data.map(aptitud => {
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
      this.evaluarAptitud(item.label)
      this.form_aptitudes.aptitud = ''
      // console.log(this.form_aptitudes.aptitudes_perfiles)
      // console.log(this.form_aptitudes.aptitud)
    },
    duplicate (label) {
      this.$q.notify(`"${label}" already in list`)
    },
    nuevoItem (value) {
      console.log(value)
      this.$q.notify(`"${value}" already in listttttttt`)
    },
    quitar (index) {
      this.$q.notify(`"${index}" already in liste`)
    },
    remove (index) {
      this.$q.dialog({
        title: 'Confirmar acción',
        message: '¿Desea eliminar la aptitud?',
        ok: 'Eliminar',
        cancel: 'Cancelar',
        preventClose: true
      }).then(() => {
        this.delete_aptitud(index)
      }).catch(() => {
      })
    },
    delete_aptitud (index) {
      this.deleteAptitudByPerfil({id: index}).then(({data}) => {
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
          this.cargarAptitudesByPerfil()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    agregarAptitud () {
      this.evaluarAptitud(this.form_aptitudes.aptitud)
      this.form_aptitudes.aptitud = ''
    },
    evaluarAptitud (value) {
      this.saveAptitudByPerfil({perfil_id: this.form.fields.id, aptitud: value}).then(({data}) => {
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
          this.cargarAptitudes()
          this.cargarAptitudesByPerfil()
        } else {
          this.$showMessage(data.message.title, data.message.content)
        }
      }).catch(error => {
        console.error(error)
      })
    },
    // toda la parte de filtrado y búsqueda //
    async cargarAptitudesBusqueda () {
      this.form_busqueda.aptitudes_data = []
      await this.getAptitudes().then(({data}) => {
        this.form_busqueda.aptitudes_data = data.aptitudes
      }).catch(error => {
        console.error(error)
      })
    },
    parseAptitudesBusqueda () {
      return this.form_busqueda.aptitudes_data.map(aptitud => {
        return {
          label: aptitud.nombre,
          value: aptitud.nombre
        }
      })
    },
    search_busqueda (terms, done) {
      setTimeout(() => {
        done(filter(terms, {field: 'value', list: this.parseAptitudesBusqueda()}))
      }, 500)
    },
    selected_busqueda (item) {
      // this.$q.notify(`Se ha agregado "${item.label}"`)
      console.log(this.form_busqueda.fields.aptitudes)
      // console.log(this.form_aptitudes.aptitud)
    },
    duplicate_busqueda (label) {
      this.$q.notify(`"${label}" ya se encuentra en el filtro de aptitudes`)
    },
    async filtrarP () {
      this.curso_var = this.form_busqueda.fields.curso
      if (this.curso_var.trim() === '') {
        this.curso_var = 'all'
      }
      if (this.form_busqueda.fields.aptitudes.length !== 0) {
        this.form.data = []
        await this.filtrarPerfiles({estado: this.form_busqueda.fields.estado_id, municipio: this.form_busqueda.fields.municipio_id, area: this.form_busqueda.fields.area_id, aptitudes: this.form_busqueda.fields.aptitudes, curso: this.curso_var}).then(({data}) => {
          this.form.data = data.perfiles
        }).catch(error => {
          console.error(error)
        })
      } else {
        this.form.data = []
        await this.filtrarPerfiles({estado: this.form_busqueda.fields.estado_id, municipio: this.form_busqueda.fields.municipio_id, area: this.form_busqueda.fields.area_id, aptitudes: this.form_busqueda.fields.aptitudes_param, curso: this.curso_var}).then(({data}) => {
          this.form.data = data.perfiles
        }).catch(error => {
          console.error(error)
        })
      }
    },
    borrar () {
      this.form_busqueda.fields.estado_id = 0
      this.form_busqueda.fields.municipio_id = 0
      this.form_busqueda.fields.area_id = 0
      this.form_busqueda.fields.aptitudes = []
      this.form_busqueda.fields.curso = ''
      this.filtrarP()
    },
    async cargarLicitaciones () {
      this.form_participacion_licitaciones.data = []
      await this.getLicitacionesByPerfil({perfil_id: this.form.fields.id}).then(({data}) => {
        this.form_participacion_licitaciones.data = data.licitaciones_perfiles
      }).catch(error => {
        console.error(error)
      })
    }
  },
  validations: {
    form: {
      fields: {
        apellido_paterno: { required, maxLength: maxLength(50), minLength: minLength(1) },
        apellido_materno: { maxLength: maxLength(50), minLength: minLength(1) },
        nombre: { required, maxLength: maxLength(50), minLength: minLength(1) },
        curp: {maxLength: maxLength(18), minLength: minLength(18), curp},
        // rfc: {minLength: minLength(12), maxLength: maxLength(13), rfc},
        email: { maxLength: maxLength(100), email },
        telefono: {maxLength: maxLength(10), minLength: minLength(10)},
        codigo_postal: {maxLength: maxLength(5), minLength: minLength(5)},
        licenciatura: {maxLength: maxLength(100)},
        universidad: {maxLength: maxLength(100)}
      }
    },
    form_posgrados: {
      fields: {
        universidad: { required, maxLength: maxLength(200), minLength: minLength(1) },
        fecha_egreso: { required, minLength: minLength(3) },
        nombre: { required, maxLength: maxLength(200), minLength: minLength(1) }
      }
    },
    form_cursos: {
      fields: {
        universidad: { required, maxLength: maxLength(200), minLength: minLength(1) },
        fecha_egreso: { required, minLength: minLength(3) },
        fecha: { required, minLength: minLength(3) },
        nombre: { required, maxLength: maxLength(200), minLength: minLength(1) }
      }
    },
    form_cursos_impartidos: {
      fields: {
        universidad: { required, maxLength: maxLength(200), minLength: minLength(1) },
        fecha_egreso: { required, minLength: minLength(3) },
        fecha: { required, minLength: minLength(3) },
        nombre: { required, maxLength: maxLength(200), minLength: minLength(1) }
      }
    },
    form_empleos: {
      fields: {
        empresa: { required, maxLength: maxLength(200), minLength: minLength(1) },
        puesto: { required, maxLength: maxLength(200), minLength: minLength(1) },
        fecha_ingreso: { required, minLength: minLength(3) }
      }
    },
    form_documentos: {
      fields: {
        descripcion: { required, maxLength: maxLength(100), minLength: minLength(1) }
      }
    }
  }
}
</script>

<style lang="stylus" scoped>
</style>
