<?php
/**
 * Local variables
 * @var \Phalcon\Mvc\Micro $app
 */


use Phalcon\Mvc\Micro\Collection as MicroCollection;


/**
 * Not found handler
 */

/* $app->before(function() use ($app) {
    $origin = $app->request->getHeader("ORIGIN") ? $app->request->getHeader("ORIGIN") : '*';
}); */

$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, 'Not Found')->sendHeaders();
    echo $app['view']->render('404');
});

$app->options('/{catch:(.*)}', function() use ($app) {
    $app->response->setHeader('Access-Control-Allow-Origin', '*');
    $app->response->setHeader('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,OPTIONS');
    $app->response->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Range, Content-Disposition, Content-Type, Authorization');
    $app->response->setHeader('Access-Control-Allow-Credentials', true);
    $app->response->sendHeaders();
    $app->response->setStatusCode(200, "OK")->send();
});

$app->get('/', function () use ($app) {
    echo "Se ha desactivado la seguridad por tokens!";
    return true;
});

/**
 * Add your routes here
 */

$auth = new ControllerCollection(new AuthController(), '/auth');
$auth->get('/', 'index');
$auth->post('/login', 'login');
$auth->get('/logout', 'logout');
$auth->get('/display/{slug}', 'show');
$app->mount($auth);


$clientes = new ControllerCollection(new ClientesController(), '/ventas/clientes');
$clientes->get('/', 'index');
$clientes->get('/getAll', 'getAll');
$clientes->get('/getAll_byName', 'getAll_byName');
$clientes->post('/save', 'save');
$clientes->put('/update', 'update');
$clientes->post('/delete', 'delete');
$clientes->post('/uploadLogo', 'uploadLogo');
$app->mount($clientes);

/* Direcciones de los clientes*/
$direcciones_cliente = new ControllerCollection(new VntDireccionController(), '/direcciones_cliente');
$direcciones_cliente->get('/getAll', 'getAll');
$direcciones_cliente->get('/getByCliente/{cliente_id:[0-9]+}', 'getByCliente');
$direcciones_cliente->post('/save', 'save');
$direcciones_cliente->put('/update', 'update');
$direcciones_cliente->post('/delete', 'delete');
$app->mount($direcciones_cliente);

$roles = new ControllerCollection(new RolesController(), '/roles');
$roles->get('/', 'index');
$roles->get('/get/{id:[0-9]+}', 'get');
$roles->get('/getAll', 'getAll');
$roles->post('/save', 'save');
$roles->put('/update', 'update');
$roles->post('/delete', 'delete');
$roles->post('/export', 'export');
$app->mount($roles);

/*$usuarios = new ControllerCollection(new UsuariosController(), '/usuarios');
$usuarios->get('/getAll', 'getAll');
$usuarios->post('/save', 'save');
$usuarios->put('/update', 'update');
$usuarios->post('/delete', 'delete');
$usuarios->post('/export', 'export');
$usuarios->post('/getIatExp', 'getIatExp');
$app->mount($usuarios);*/

$users = new ControllerCollection(new UsersController(), '/users');
$users->get('/getAll', 'getAll');
$users->get('/getClientes', 'getClientes');
$users->post('/save', 'save');
$users->put('/update', 'update');
$users->put('/updateProfile', 'updateProfile');
$users->put('/updateStatus', 'updateStatus');
$users->post('/delete', 'delete');
$users->put('/updatePassword', 'updatePassword');
$users->post('/recoverPassword', 'recoverPassword');
$users->get('/getProfile', 'getProfile');
$users->get('/getUser', 'getUser');
$users->post('/uploadImage', 'uploadImage');
$app->mount($users);


/* CONFIGURACIÓN */
$menus = new ControllerCollection(new MenusController(), '/menus');
$menus->get('/get/{id:[0-9]+}', 'get');
$menus->get('/getAll', 'getAll');
$menus->post('/save', 'save');
$menus->put('/update', 'update');
$menus->post('/delete', 'delete');
$menus->post('/export', 'export');
$app->mount($menus);

$menuItems = new ControllerCollection(new MenuItemsControllers(), '/menuItems');
$menuItems->get('/get/{id:[0-9]+}', 'get');
$menuItems->get('/getAll', 'getAll');
$menuItems->get('/getByMenu/{menu_id:[0-9]+}', 'getByMenu');
$menuItems->post('/save', 'save');
$menuItems->put('/update', 'update');
$menuItems->post('/delete', 'delete');
$menuItems->post('/export', 'export');
$app->mount($menuItems);

/* DOCUMENTOS */
$documentos =new ControllerCollection(new DocumentsController(),'/documents');
$documentos->post('/delete', 'delete');
$app->mount($documentos);

/* Programa */
$programas = new ControllerCollection(new ProgramaController(), '/programas');
$programas->get('/getAll', 'getAll');
$programas->post('/save', 'save');
$programas->put('/update', 'update');
$programas->post('/delete', 'delete');
$app->mount($programas);

/* Subprograma */
$subprogramas = new ControllerCollection(new SubprogramaController(), '/subprogramas');
$subprogramas->get('/getAll', 'getAll');
$subprogramas->post('/save', 'save');
$subprogramas->put('/update', 'update');
$subprogramas->post('/delete', 'delete');
$app->mount($subprogramas);

/* Recursos */
$recursos = new ControllerCollection(new RecursoController(), '/recursos');
$recursos->get('/getAll', 'getAll');
$recursos->get('/getBy/{id}', 'getBy');
$recursos->get('/getByYear/{year}/{presupuesto}/{sucursal}', 'getByYear');
$recursos->get('/getMontoComision/{tipo}/{porcentaje}/{aplica}/{contrato}/{base}/{proyecto}','getMontoComision');
$recursos->get('/getRecursosPerfiles', 'getRecursosPerfiles');
$recursos->post('/save', 'save');
$recursos->post('/create_from_licitacion', 'create_from_licitacion');
$recursos->put('/update', 'update');
$recursos->post('/delete', 'delete');
$app->mount($recursos);

/* Empresas */
$empresas = new ControllerCollection(new EmpresaController(), '/empresas');
$empresas->get('/getAll', 'getAll');
$empresas->get('/getByPadre/{padre}', 'getByPadre');
$empresas->post('/save', 'save');
$empresas->put('/update', 'update');
$empresas->post('/delete', 'delete');
$app->mount($empresas);

/* Proyectos */
$proyectos = new ControllerCollection(new ProyectoController(), '/proyectos');
$proyectos->get('/getAll', 'getAll');
$proyectos->post('/save', 'save');
$proyectos->get('/getByID/{id:[0-9]+}/{year}', 'getByID');
$proyectos->get('/getBy/{id}', 'getBy');
$proyectos->get('/getByRecurso/{id}', 'getByRecurso');
$proyectos->get('/getBy2/{id}', 'getBy2');
$proyectos->get('/getProyectosPerfiles/{year}', 'getProyectosPerfiles');
$proyectos->get('/getProyectosPerfilesByRecurso/{recurso}', 'getProyectosPerfilesByRecurso');
$proyectos->get('/getProyectosColaborador/{year}', 'getProyectosColaborador');
$proyectos->get('/getUsuariosByPresupuesto/{proyecto_id:[0-9]+}', 'getUsuariosByPresupuesto');
$proyectos->put('/update', 'update');
$proyectos->put('/updateMontos', 'updateMontos');
$proyectos->put('/updateStatus', 'updateStatus');
$proyectos->put('/updateFinalizado', 'updateFinalizado');
$proyectos->post('/delete', 'delete');
$app->mount($proyectos);

/* Contratos */
$contratos = new ControllerCollection(new ContratoController(), '/contratos');
$contratos->get('/getAll', 'getAll');
$contratos->get('/getByID/{id:[0-9]+}', 'getByID');
$contratos->get('/getOptions/{id:[0-9]+}', 'getOptions');
$contratos->get('/getFile/{nombre}/{ext}', 'getFile');
$contratos->post('/save', 'save');
$contratos->post('/uploadArchivoFinal', 'uploadArchivoFinal');
$contratos->put('/update', 'update');
$contratos->put('/deleteFile', 'deleteFile');
$contratos->post('/delete', 'delete');
$app->mount($contratos);

/* Contratos */
$facturasContratos = new ControllerCollection(new ContratoFacturas(), '/facturasContratos');
$facturasContratos->get('/getFacturas/{contrato:[0-9]+}', 'getFacturas');
$facturasContratos->get('/getFacturasByContrato/{contrato}', 'getFacturasByContrato');
$facturasContratos->get('/getFacturasByContratoandId/{proyecto}/{cliente}/{contrato}/{factura}/{year}/{status}/{empresa_id}', 'getFacturasByContratoandId');
$facturasContratos->post('/uploadArchivo', 'uploadArchivo');
$facturasContratos->post('/uploadArchivoPDF', 'uploadArchivoPDF');
$facturasContratos->put('/cancelarFactura', 'cancelarFactura');
$facturasContratos->put('/actualizarFactura', 'actualizarFactura');
$facturasContratos->post('/delete', 'delete');
$facturasContratos->get('/getFile/{nombre}/{ext}', 'getFile');
$app->mount($facturasContratos);

/* Contratos */
$facturasAbonos = new ControllerCollection(new AbonosFacturasController(), '/abonosFacturas');
$facturasAbonos->get('/getAll', 'getAll');
$facturasAbonos->get('/historialAbonos/{factura_id:[0-9]+}', 'historialAbonos');
$facturasAbonos->post('/filtrar', 'filtrar');
$facturasAbonos->post('/save', 'save');
$facturasAbonos->put('/update', 'update');
$facturasAbonos->post('/delete', 'delete');
$app->mount($facturasAbonos);

/* Remisiones */
$facturasRemisiones = new ControllerCollection(new RemisionesFacturasController(), '/remisionesFacturas');
$facturasRemisiones->get('/getFacturas/{remision}', 'getFacturas');
$facturasRemisiones->get('/getFacturasByContrato/{remision}', 'getFacturasByContrato');
$facturasRemisiones->get('/getFacturasByContratoandId/{cliente}/{empresa}/{year}/{status}', 'getFacturasByContratoandId');
$facturasRemisiones->get('/getFacturasDuplicadas/{cliente}/{empresa}/{year}/{status}', 'getFacturasDuplicadas');
$facturasRemisiones->get('/getFacturasDuplicadasCsv/{cliente}/{empresa}/{year}/{status}', 'getFacturasDuplicadasCsv');
$facturasRemisiones->post('/uploadArchivo', 'uploadArchivo');
$facturasRemisiones->post('/uploadArchivoMasivo', 'uploadArchivoMasivo');
$facturasRemisiones->post('/uploadAmortizacion', 'uploadAmortizacion');
$facturasRemisiones->post('/delete', 'delete');
$facturasRemisiones->get('/getFile/{nombre}/{ext}', 'getFile');
$facturasRemisiones->put('/agregarNombre', 'agregarNombre');
$facturasRemisiones->put('/agregarNombreFacturas', 'agregarNombreFacturas');
$facturasRemisiones->put('/getRemisionesRepetidas', 'getRemisionesRepetidas');
$app->mount($facturasRemisiones);

/* Contratos */
$abonosRemisiones = new ControllerCollection(new AbonosRemisionesController(), '/abonosRemisiones');
$abonosRemisiones->get('/getAll', 'getAll');
$abonosRemisiones->get('/historialAbonos/{factura_id:[0-9]+}', 'historialAbonos');
$abonosRemisiones->post('/filtrar', 'filtrar');
$abonosRemisiones->post('/save', 'save');
$abonosRemisiones->put('/update', 'update');
$abonosRemisiones->post('/delete', 'delete');
$app->mount($abonosRemisiones);

/* Estados*/
$estados = new ControllerCollection(new EstadoController(), '/estados');
$estados->get('/getAll', 'getAll');
$estados->post('/save', 'save');
$estados->put('/update', 'update');
$estados->post('/delete', 'delete');
$app->mount($estados);

/* Municipios*/
$municipios = new ControllerCollection(new MunicipioController(), '/municipios');
$municipios->get('/getAll', 'getAll');
$municipios->get('/getByEstado/{estado_id:[0-9]+}', 'getByEstado');
$municipios->post('/save', 'save');
$municipios->put('/update', 'update');
$municipios->post('/delete', 'delete');
$app->mount($municipios);

/* Direcciones*/
$direcciones = new ControllerCollection(new DireccionController(), '/direcciones');
$direcciones->get('/getAll', 'getAll');
$direcciones->get('/getByEmpresa/{empresa_id:[0-9]+}', 'getByEmpresa');
$direcciones->get('/getByEmpresaOptions/{empresa_id:[0-9]+}', 'getByEmpresaOptions');
$direcciones->post('/save', 'save');
$direcciones->put('/update', 'update');
$direcciones->post('/delete', 'delete');
$app->mount($direcciones);

/* Proveedores VNT */
$proveedores = new ControllerCollection(new ProveedorController(), '/proveedores');
$proveedores->get('/getAll', 'getAll');
$proveedores->get('/getByEmpresa/{empresa_id:[0-9]+}', 'getByEmpresa');
$proveedores->get('/getByCliente/{cliente_id:[0-9]+}', 'getByCliente');
$proveedores->get('/getFormato/{proveedor_id:[0-9]+}', 'getFormato');
$proveedores->post('/save', 'save');
$proveedores->post('/save_requisito', 'save_requisito');
$proveedores->put('/update', 'update');
$proveedores->put('/update_requisito', 'update_requisito');
$proveedores->post('/delete', 'delete');
$proveedores->post('/delete_requisito', 'delete_requisito');
$proveedores->post('/deleteFormato', 'deleteFormato');
$proveedores->post('/uploadArchivo', 'uploadArchivo');
$app->mount($proveedores);

/* Licitaciones */
$licitaciones = new ControllerCollection(new LicitacionController(), '/licitaciones');
$licitaciones->get('/getAll', 'getAll');
$licitaciones->get('/getByRecurso/{recurso_id:[0-9]+}', 'getByRecurso');
$licitaciones->get('/getEmpresaConcursanteByIdLicitacion/{id:[0-9]+}', 'getEmpresaConcursanteByIdLicitacion');
$licitaciones->get('/getFile/{id}/{ext}', 'getFile');
$licitaciones->get('/getFileFinal/{nombre}/{ext}', 'getFileFinal');
$licitaciones->post('/filtrar', 'filtrar');
$licitaciones->post('/save', 'save');
$licitaciones->post('/uploadArchivo', 'uploadArchivo');
$licitaciones->post('/uploadArchivoFinal', 'uploadArchivoFinal');
$licitaciones->put('/update', 'update');
$licitaciones->put('/deleteFile', 'deleteFile');
$licitaciones->put('/delete', 'delete');
$app->mount($licitaciones);

/* Licitaciones Respaldo*/
$licitaciones_respaldo = new ControllerCollection(new RespaldoController(), '/licitaciones_respaldo');
$licitaciones_respaldo->get('/getAll', 'getAll');
$licitaciones_respaldo->get('/getByLicitacion/{licitacion_id:[0-9]+}', 'getByLicitacion');
$licitaciones_respaldo->post('/save', 'save');
$licitaciones_respaldo->post('/delete', 'delete');
$app->mount($licitaciones_respaldo);


/* Carga csv */
$carga_csv = new ControllerCollection(new CargacsvController(), '/carga_csv');
$carga_csv->get('/getAll', 'getAll');
$carga_csv->get('/getAllActividades', 'getAllActividades');
$carga_csv->get('/getById/{id:[0-9]+}', 'getById');
$carga_csv->get('/getByProyecto/{proyecto_id:[0-9]+}', 'getByProyecto');
$carga_csv->get('/getActividadesOpt/{id:[0-9]+}', 'getActividadesOpt');
$carga_csv->get('/getByActividad/{id:[0-9]+}', 'getByActividad');
$carga_csv->get('/getPresupuestoActividad/{id:[0-9]+}', 'getPresupuestoActividad');
$carga_csv->get('/getPresupuestoActividadReal/{id:[0-9]+}', 'getPresupuestoActividadReal');
$carga_csv->post('/save', 'save');
$carga_csv->post('/uploadCsv', 'uploadCsv');
$carga_csv->put('/update_actividad', 'update_actividad');
$carga_csv->put('/update_costo', 'update_costo');
$carga_csv->put('/updateFinalizado', 'updateFinalizado');
$carga_csv->put('/updateVisible', 'updateVisible');
$carga_csv->put('/update_avance', 'update_avance');
$carga_csv->post('/delete_actividad', 'delete_actividad');
$carga_csv->post('/uploadArchivo', 'uploadArchivo');
$carga_csv->get('/getFilesByActividad/{id_actividad}', 'getFilesByActividad');
$carga_csv->get('/getFile/{id}', 'getFile');
$carga_csv->get('/exportCSV_actividades/{project}', 'exportCSV_actividades');
$carga_csv->post('/deleteFormatoActividad', 'deleteFormatoActividad');
$carga_csv->post('/delete', 'delete');
$carga_csv->post('/delete_single', 'delete_single');
$app->mount($carga_csv);

/*Gastos*/
$gastos = new ControllerCollection(new GastosSolicitudesController(), '/gastos');
$gastos->get('/getAll', 'getAll');
$gastos->get('/getByActividad/{actividad_id:[0-9]+}', 'getByActividad');
$gastos->get('/getById/{id:[0-9]+}', 'getById');
$gastos->get('/getBySolicitud/{id:[0-9]+}', 'getBySolicitud');
$gastos->post('/save', 'save');
$gastos->put('/update', 'update');
$gastos->put('/pagar', 'pagar');
$gastos->put('/comprobar', 'comprobar');
$gastos->put('/updateConcepto', 'updateConcepto');
$gastos->post('/delete', 'delete');
$app->mount($gastos);

/* Proveedor*/
$proveedor = new ControllerCollection(new ProveedoresController(), '/proveedor');
$proveedor->get('/getAll', 'getAll');
$proveedor->get('/getOptions', 'getOptions');
$proveedor->get('/getById/{id:[0-9]+}', 'getById');
$proveedor->get('/getCuentas/{id:[0-9]+}', 'getCuentas');
$proveedor->get('/exportCSV', 'exportCSV');
$proveedor->post('/save', 'save');
$proveedor->put('/update', 'update');
$proveedor->post('/delete', 'delete');
$proveedor->post('/uploadArchivo', 'uploadArchivo');
$proveedor->get('/getFile/{nombre}', 'getFile');
$app->mount($proveedor);

/* Log Levels */
$logs = new ControllerCollection(new LogLevelsController(), '/logs');
$logs->get('/getAll', 'getAll');
$logs->post('/save', 'save');
$logs->put('/update', 'update');
$logs->post('/delete', 'delete');
$app->mount($logs);;
/*Requisitos*/
$requisitos = new ControllerCollection(new RequisitosController(), '/requisitos');
$requisitos->get('/getAll', 'getAll');
$requisitos->get('/getByTramite/{cliente_id:[0-9]+}/{tramite}', 'getByTramite');
$requisitos->post('/save', 'save');
$requisitos->put('/update', 'update');
$requisitos->post('/delete', 'delete');
$app->mount($requisitos);

/*Padrón Requisitos*/
$padron_requisitos = new ControllerCollection(new PadronRequisitosController(), '/padron_requisitos');
$padron_requisitos->get('/getByProveedor/{proveedor_id:[0-9]+}/{cliente_id:[0-9]+}', 'getByProveedor');
$padron_requisitos->get('/getFile/{id}/{ext}', 'getFile');
$padron_requisitos->post('/uploadArchivo', 'uploadArchivo');
$padron_requisitos->post('/deleteFile', 'deleteFile');
$app->mount($padron_requisitos);

/* INFO LOGS */
$info_logs = new ControllerCollection(new LogsController(), '/info_logs');
$info_logs->get('/getAll', 'getAll');
$info_logs->post('/save', 'save');
$app->mount($info_logs);

/* Clientes requisitos */
$clientes_requisitos = new ControllerCollection(new ClientesRequisitosController(), '/clientes_requisitos');
$clientes_requisitos->post('/save', 'save');
$app->mount($clientes_requisitos);

/* Licitaciones requerimientos */
$licitaciones_requerimientos = new ControllerCollection(new RequerimientosController(), '/requerimientos');
$licitaciones_requerimientos->get('/getAll', 'getAll');
$licitaciones_requerimientos->get('/getBasesByLicitacion/{licitacion_id:[0-9]+}', 'getBasesByLicitacion');
$licitaciones_requerimientos->post('/deleteFormato', 'deleteFormato');
$licitaciones_requerimientos->post('/guardarArchivo', 'guardarArchivo');
$licitaciones_requerimientos->put('/update', 'update');
$licitaciones_requerimientos->post('/uploadArchivo', 'uploadArchivo');
$app->mount($licitaciones_requerimientos);

/* Licitaciones eventos */
$licitaciones_eventos = new ControllerCollection(new EventosController(), '/eventos');
$licitaciones_eventos->get('/getEventosByLicitacion/{licitacion_id:[0-9]+}', 'getEventosByLicitacion');
$licitaciones_eventos->post('/delete', 'delete');
$licitaciones_eventos->post('/save', 'save');
$licitaciones_eventos->put('/update', 'update');
$licitaciones_eventos->post('/uploadArchivo', 'uploadArchivo');
$licitaciones_eventos->get('/getFile/{id}/{ext}', 'getFile');
$licitaciones_eventos->post('/uploadDocumento', 'uploadDocumento');
$licitaciones_eventos->get('/getFileDocumentos/{id}/{ext}', 'getFileDocumentos');
$licitaciones_eventos->post('/deleteFormato', 'deleteFormato');
$app->mount($licitaciones_eventos);

/* Licitaciones propuestas */
$licitaciones_propuestas = new ControllerCollection(new PropuestasController(), '/licitaciones_propuestas');
$licitaciones_propuestas->get('/getPropuestasByLicitacion/{licitacion_id:[0-9]+}', 'getPropuestasByLicitacion');
$licitaciones_propuestas->post('/delete', 'delete');
$licitaciones_propuestas->post('/save', 'save');
$licitaciones_propuestas->put('/update', 'update');
$licitaciones_propuestas->post('/uploadArchivo', 'uploadArchivo');
$licitaciones_propuestas->get('/getFile/{id}/{ext}', 'getFile');
$app->mount($licitaciones_propuestas);

/* Licitaciones requisitos */
$licitaciones_requisitos = new ControllerCollection(new RequisitosLController(), '/licitaciones_requisitos');
$licitaciones_requisitos->get('/getRequisitosByLicitacion/{licitacion_id:[0-9]+}', 'getRequisitosByLicitacion');
$licitaciones_requisitos->post('/delete', 'delete');
$licitaciones_requisitos->post('/save', 'save');
$licitaciones_requisitos->put('/update', 'update');
$licitaciones_requisitos->post('/uploadArchivo', 'uploadArchivo');
$licitaciones_requisitos->get('/getFile/{id}/{ext}', 'getFile');
$app->mount($licitaciones_requisitos);

/* Perfiles expertos */
$perfiles = new ControllerCollection(new PerfilesExpertosController(), '/perfiles');
$perfiles->get('/getAll', 'getAll');
$perfiles->get('/filtrar/{estado}/{municipio}/{area}/{aptitudes}/{curso}', 'filtrar');
$perfiles->get('/getByProyecto/{proyecto_id:[0-9]+}', 'getByProyecto');
$perfiles->post('/save', 'save');
$perfiles->post('/uploadFoto', 'uploadFoto');
$perfiles->put('/update', 'update');
$perfiles->post('/delete', 'delete');
$app->mount($perfiles);

/* TITULOS PERFILES */
$titulos = new ControllerCollection(new TitulosPerfilesController(), '/titulos');
$titulos->get('/getByPerfil/{perfil_id}', 'getByPerfil');
$titulos->post('/uploadTitulo', 'uploadTitulo');
$titulos->get('/getFile/{id}/{ext}', 'getFile');
$titulos->post('/deleteFormato', 'deleteFormato');
$app->mount($titulos);

/* CEDULAS PERFILES */
$cedulas = new ControllerCollection(new CedulasPerfilesController(), '/cedulas');
$cedulas->get('/getByPerfil/{perfil_id}', 'getByPerfil');
$cedulas->post('/uploadCedula', 'uploadCedula');
$cedulas->get('/getFile/{id}/{ext}', 'getFile');
$cedulas->post('/deleteFormato', 'deleteFormato');
$app->mount($cedulas);

/* POSGRADOS PERFILES */
$posgrados = new ControllerCollection(new PosgradosPerfilesController(), '/posgrados');
$posgrados->get('/getAll', 'getAll');
$posgrados->get('/getByPerfil/{perfil_id}', 'getByPerfil');
$posgrados->post('/save', 'save');
$posgrados->post('/uploadDocumento', 'uploadDocumento');
$posgrados->get('/getFile/{id}/{ext}', 'getFile');
$posgrados->post('/deleteFormato', 'deleteFormato');
$posgrados->put('/update', 'update');
$posgrados->post('/delete', 'delete');
$app->mount($posgrados);

/* CURSOS PERFILES */
$cursos = new ControllerCollection(new CursosPerfilesController(), '/cursos');
$cursos->get('/getAll', 'getAll');
$cursos->get('/getByPerfil/{perfil_id}/{tipo}', 'getByPerfil');
$cursos->post('/save', 'save');
$cursos->post('/uploadDocumento', 'uploadDocumento');
$cursos->get('/getFile/{id}/{ext}', 'getFile');
$cursos->post('/deleteFormato', 'deleteFormato');
$cursos->put('/update', 'update');
$cursos->post('/delete', 'delete');
$app->mount($cursos);

/* EMPLEOS PERFILES */
$empleos = new ControllerCollection(new EmpleosPerfilesController(), '/empleos');
$empleos->get('/getAll', 'getAll');
$empleos->get('/getByPerfil/{perfil_id}', 'getByPerfil');
$empleos->post('/save', 'save');
$empleos->put('/update', 'update');
$empleos->post('/delete', 'delete');
$app->mount($empleos);

/* DOCUMENTOS PERFILES */
$documentos_perfiles = new ControllerCollection(new DocumentosPerfilesController(), '/documentos_perfiles');
$documentos_perfiles->get('/getAll', 'getAll');
$documentos_perfiles->get('/getByPerfil/{perfil_id}', 'getByPerfil');
$documentos_perfiles->get('/getFile/{id}/{ext}', 'getFile');
$documentos_perfiles->post('/uploadDocumento', 'uploadDocumento');
$documentos_perfiles->post('/deleteFormato', 'deleteFormato');
$app->mount($documentos_perfiles);

/* CARTAS PERFILES */
$cartas = new ControllerCollection(new CartasPerfilesController(), '/cartas');
$cartas->get('/getAll', 'getAll');
$cartas->get('/getByPerfil/{perfil_id}', 'getByPerfil');
$cartas->get('/getFile/{id}/{ext}', 'getFile');
$cartas->post('/uploadDocumento', 'uploadDocumento');
$cartas->post('/deleteFormato', 'deleteFormato');
$app->mount($cartas);

/* APTITUDES */
$aptitudes = new ControllerCollection(new AptitudesController(), '/aptitudes');
$aptitudes->get('/getAll', 'getAll');
$aptitudes->post('/save', 'save');
$aptitudes->put('/update', 'update');
$aptitudes->post('/delete', 'delete');
$app->mount($aptitudes);

/* APTITUDES PERFILES */
$aptitudes_perfiles = new ControllerCollection(new AptitudesPerfilesController(), '/aptitudes_perfiles');
$aptitudes_perfiles->get('/getAll', 'getAll');
$aptitudes_perfiles->get('/getByPerfil/{perfil_id}', 'getByPerfil');
$aptitudes_perfiles->post('/save', 'save');
$aptitudes_perfiles->post('/delete', 'delete');
$app->mount($aptitudes_perfiles);

/* AREAS */
$areas = new ControllerCollection(new AreasController(), '/areas');
$areas->get('/getAll', 'getAll');
$areas->put('/update', 'update');
$areas->post('/save', 'save');
$areas->post('/delete', 'delete');
$app->mount($areas);

/* PROYECTOS PERFILES */
$proyectos_perfiles = new ControllerCollection(new ProyectosPerfilesController(), '/proyectos_perfiles');
$proyectos_perfiles->get('/getAll', 'getAll');
$proyectos_perfiles->get('/getByPerfil/{perfil_id}', 'getByPerfil');
$proyectos_perfiles->get('/getByProject/{proyecto_id}', 'getByProject');
$proyectos_perfiles->post('/save', 'save');
$proyectos_perfiles->put('/update', 'update');
$proyectos_perfiles->post('/delete', 'delete');
$app->mount($proyectos_perfiles);

/* LICITACIONES PERFILES */
$licitaciones_perfiles = new ControllerCollection(new LicitacionesPerfilesController(), '/licitaciones_perfiles');
$licitaciones_perfiles->get('/getAll', 'getAll');
$licitaciones_perfiles->get('/getByPerfil/{perfil_id}', 'getByPerfil');
$licitaciones_perfiles->get('/getByLicitacion/{licitacion_id}', 'getByLicitacion');
$licitaciones_perfiles->post('/save', 'save');
$licitaciones_perfiles->put('/update', 'update');
$licitaciones_perfiles->post('/delete', 'delete');
$app->mount($licitaciones_perfiles);

/* SOLICITANTES PRESUPUESTO */
$solicitantes = new ControllerCollection(new SolicitantesController(), '/solicitantes');
$solicitantes->get('/getAll', 'getAll');
$solicitantes->get('/getByProyecto/{proyecto_id:[0-9]+}', 'getByProyecto');
$solicitantes->post('/save', 'save');
$solicitantes->put('/update', 'update');
$solicitantes->post('/delete', 'delete');
$app->mount($solicitantes);

/* AUTORIZADORES PRESUPUESTO */
$autorizadores = new ControllerCollection(new AutorizadoresController(), '/autorizadores');
$autorizadores->get('/getAll', 'getAll');
$autorizadores->get('/getByProyecto/{proyecto_id:[0-9]+}', 'getByProyecto');
$autorizadores->post('/save', 'save');
$autorizadores->put('/update', 'update');
$autorizadores->post('/delete', 'delete');
$app->mount($autorizadores);

/* RESPONSABLE PAGOS PRESUPUESTO */
$responsable_pagos = new ControllerCollection(new ResponsablePagosController(), '/responsable_pagos');
$responsable_pagos->get('/getAll', 'getAll');
$responsable_pagos->get('/getByProyecto/{proyecto_id:[0-9]+}', 'getByProyecto');
$responsable_pagos->post('/save', 'save');
$responsable_pagos->put('/update', 'update');
$responsable_pagos->post('/delete', 'delete');
$app->mount($responsable_pagos);

/* SOLICITUDES */
$solicitudes = new ControllerCollection(new SolicitudesOController(), '/solicitudes');
$solicitudes->get('/getAll', 'getAll');
$solicitudes->get('/getByProyecto/{proyecto_id:[0-9]+}', 'getByProyecto');
$solicitudes->get('/getById/{id:[0-9]+}', 'getById');
$solicitudes->get('/obtenerSigSolicitud', 'obtenerSigSolicitud');
$solicitudes->post('/filtrar', 'filtrar');
$solicitudes->post('/save', 'save');
$solicitudes->post('/rechazar', 'rechazar');
$solicitudes->post('/cancelar', 'cancelar');
$solicitudes->put('/update', 'update');
$solicitudes->put('/update_fechas', 'update_fechas');
$solicitudes->put('/updateEmpresa', 'updateEmpresa');
$solicitudes->put('/updateStatus', 'updateStatus');
$solicitudes->put('/updateClasificacion', 'updateClasificacion');
$solicitudes->put('/delete', 'delete');
$app->mount($solicitudes);

/* COLABORADORES PRESUPUESTO */
$colaboradores = new ControllerCollection(new ColaboradoresController(), '/colaboradores');
$colaboradores->get('/getAll', 'getAll');
$colaboradores->get('/getByProyecto/{proyecto_id:[0-9]+}', 'getByProyecto');
$colaboradores->post('/save', 'save');
$colaboradores->put('/update', 'update');
$colaboradores->post('/delete', 'delete');
$app->mount($colaboradores);

/* archivos solicitudes */
$archivos_solicitudes = new ControllerCollection(new ArchivosSolicitudesController(), '/archivos_solicitudes');
$archivos_solicitudes->get('/getAll', 'getAll');
$archivos_solicitudes->get('/getArchivosBySolicitud/{solicitud_id:[0-9]+}', 'getArchivosBySolicitud');
$archivos_solicitudes->post('/deleteFormato', 'deleteFormato');
$archivos_solicitudes->post('/uploadArchivo', 'uploadArchivo');
$archivos_solicitudes->get('/getFile/{id}/{ext}', 'getFile');
$app->mount($archivos_solicitudes);

/* Autorizaciones */
$autorizacionesPendientes = new ControllerCollection(new AutorizacionesPendientes(),'/autorizaciones');
$autorizacionesPendientes->get('/getAll','getAll');
$autorizacionesPendientes->post('/save','save');
$autorizacionesPendientes->post('/delete','delete');
$app->mount($autorizacionesPendientes);

$autorizacionesPagos = new ControllerCollection(new PagosAutorizacionesController(),'/pagos_autorizaciones');
$autorizacionesPagos->get('/getAll','getAll');
$autorizacionesPagos->post('/save','save');
$autorizacionesPagos->put('/pagar', 'pagar');
$autorizacionesPagos->post('/delete','delete');
$app->mount($autorizacionesPagos);

/* Mensajes */
$mensajes = new ControllerCollection(new MensajeController(),'/mensajes');
$mensajes->get('/getAll','getAll');
$mensajes->get('/getMensajesBySolicitud/{solicitud_id:[0-9]+}', 'getMensajesBySolicitud');
$mensajes->post('/save','save');
$app->mount($mensajes);

/* Notas */
$notas = new ControllerCollection(new NotasController(),'/notas');
$notas->get('/getAll','getAll');
$notas->get('/getNotasBySolicitud/{solicitud_id:[0-9]+}', 'getNotasBySolicitud');
$notas->post('/save','save');
$app->mount($notas);

/* Tipos de Gastos*/
$tipos = new ControllerCollection(new TiposGastosController(), '/tipos');
$tipos->get('/getAll', 'getAll');
$tipos->post('/save', 'save');
$tipos->put('/update', 'update');
$tipos->post('/delete', 'delete');
$app->mount($tipos);

/* Fianzas */
$fianzas = new ControllerCollection(new FianzasController(), '/fianzas');
$fianzas->get('/getAll', 'getAll');
$fianzas->post('/save', 'save');
$fianzas->put('/update', 'update');
$fianzas->post('/delete', 'delete');
$fianzas->post('/deleteFormato', 'deleteFormato');
$fianzas->post('/uploadArchivo', 'uploadArchivo');
$fianzas->put('/deleteFileFinal', 'deleteFileFinal');
$fianzas->post('/uploadArchivoFinal', 'uploadArchivoFinal');
$fianzas->get('/getFile/{id}/{ext}', 'getFile');
$fianzas->get('/getFileFinal/{nombre}/{ext}', 'getFileFinal');
$app->mount($fianzas);

/* Cotizaciones de los gastos */
$cotizaciones_gastos = new ControllerCollection(new CotizacionGastosController(), '/cotizaciones_gastos');
$cotizaciones_gastos->post('/deleteFormato', 'deleteFormato');
$cotizaciones_gastos->post('/uploadArchivo', 'uploadArchivo');
$cotizaciones_gastos->get('/getFile/{id}/{ext}', 'getFile');
$app->mount($cotizaciones_gastos);

/* Facturas de los gastos */
$facturas_gastos = new ControllerCollection(new FacturaGastosController(), '/facturas_gastos');
$facturas_gastos->post('/deleteFormato', 'deleteFormato');
$facturas_gastos->post('/uploadArchivo', 'uploadArchivo');
$facturas_gastos->get('/getFile/{id}/{ext}', 'getFile');
$app->mount($facturas_gastos);

/* Comprobantes de los gastos */
$comprobantes_gastos = new ControllerCollection(new ComprobanteGastosController(), '/comprobantes_gastos');
$comprobantes_gastos->post('/deleteFormato', 'deleteFormato');
$comprobantes_gastos->post('/uploadArchivo', 'uploadArchivo');
$comprobantes_gastos->get('/getFile/{id}/{ext}', 'getFile');
$app->mount($comprobantes_gastos);

/* Repositorio */
$carpetas = new ControllerCollection(new FoldersController(), '/carpetas');
$carpetas->get('/getAll', 'getAll');
$carpetas->get('/getByPadre/{padre:[0-9]+}', 'getByPadre');
$carpetas->post('/save', 'save');
$carpetas->put('/update', 'update');
$carpetas->post('/uploadArchivo', 'uploadArchivo');
$carpetas->get('/getFile/{id_file}/{id}/{ext}', 'getFile');
$carpetas->post('/delete', 'delete');
$carpetas->post('/deleteFile', 'deleteFile');
$app->mount($carpetas);

/* Dashboard */
$graficas =new ControllerCollection(new DashboardController(), '/graficas');
$graficas->get('/getDataLicitaciones', 'getDataLicitaciones');
$graficas->get('/getPresupuestoByProyecto/{id_proyecto}', 'getPresupuestoByProyecto');
$graficas->get('/getPresupuestoByActividad/{id_actividad}', 'getPresupuestoByActividad');
$graficas->get('/getEventos', 'getEventos');
$graficas->get('/getDataProjects/{id}/{role}/{lider_id}/{empresa_id}/{estado_id}/{municipio_id}/{year}/{tipo}/{finalizado}/{activo}', 'getDataProjects');
$graficas->get('/getFacturasByDays/{days}/{year}', 'getFacturasByDays');
$graficas->get('/getFacturas/{year}', 'getFacturas');
$graficas->get('/getGraficaDataFactura/{year}', 'getGraficaDataFactura');
$graficas->get('/getAdeudosByCliente/{id_cliente}/{year}', 'getAdeudosByCliente');
$graficas->get('/getProyectosByCliente/{id_cliente}', 'getProyectosByCliente');
$graficas->get('/getTopClientes/{year}', 'getTopClientes');
$graficas->get('/getDataCRM', 'getDataCRM');
$graficas->get('/getDataFinanzas/{year}/{mes}/{proyecto_id}/{project_id}/{sucursal_id}/{vendedor_id}/{concepto_id}/{subconcepto_id}', 'getDataFinanzas');
$app->mount($graficas);

/* Conceptos */
$conceptos = new ControllerCollection(new ConceptoController(), '/conceptos');
$conceptos->get('/getAll', 'getAll');
$conceptos->post('/save', 'save');
$conceptos->put('/update', 'update');
$conceptos->post('/delete', 'delete');
$app->mount($conceptos);

/* Subconceptos */
$subconceptos = new ControllerCollection(new SubconceptoController(), '/subconceptos');
$subconceptos->get('/getAll/{id}', 'getAll');
$subconceptos->get('/getByConcepto/{concepto_id}', 'getByConcepto');
$subconceptos->post('/save', 'save');
$subconceptos->put('/update', 'update');
$subconceptos->post('/delete', 'delete');
$app->mount($subconceptos);

/* Organismos */
$organismos = new ControllerCollection(new OrganismoController(), '/organismos');
$organismos->get('/getAll', 'getAll');
$organismos->get('/getByProyecto/{id}', 'getByProyecto');
$organismos->post('/save', 'save');
$organismos->put('/update', 'update');
$organismos->post('/delete', 'delete');
$app->mount($organismos);

/* Almacenes */
$almacenes = new ControllerCollection(new AlmacenesController(), '/almacenes');
$almacenes->get('/getAll', 'getAll');
$almacenes->get('/getByEmpresa/{empresa_id}', 'getByEmpresa');
$almacenes->post('/save', 'save');
$almacenes->put('/update', 'update');
$almacenes->post('/delete', 'delete');
$app->mount($almacenes);

/* CATEGORÍAS */
$tipos_articulos = new ControllerCollection(new TiposArticulosController(), '/tipos_articulos');
$tipos_articulos->get('/getAll', 'getAll');
$tipos_articulos->post('/save', 'save');
$tipos_articulos->put('/update', 'update');
$tipos_articulos->post('/delete', 'delete');
$app->mount($tipos_articulos);

/* Tipos de movimientos */
$tipos_movimientos = new ControllerCollection(new TiposMovimientosController(), '/tipos_movimientos');
$tipos_movimientos->get('/getAll', 'getAll');
$tipos_movimientos->post('/save', 'save');
$tipos_movimientos->put('/update', 'update');
$tipos_movimientos->post('/delete', 'delete');
$app->mount($tipos_movimientos);

/* Tipos de presentaciones */
$tipos_presentaciones = new ControllerCollection(new TiposPresentacionesController(), '/tipos_presentaciones');
$tipos_presentaciones->get('/getAll', 'getAll');
$tipos_presentaciones->post('/save', 'save');
$tipos_presentaciones->put('/update', 'update');
$tipos_presentaciones->post('/delete', 'delete');
$app->mount($tipos_presentaciones);

/* Productos */
$productos = new ControllerCollection(new ProductosController(), '/productos');
$productos->get('/getAll', 'getAll');
$productos->get('/getByCategoria/{categoria}', 'getByCategoria');
$productos->get('/getPresentacionByProducto/{id_producto}', 'getPresentacionByProducto');
$productos->get('/getProductoByLinea/{linea}', 'getProductoByLinea');
$productos->get('/getByCompra/{compra_id}', 'getByCompra');
$productos->post('/save', 'save');
$productos->put('/update', 'update');
$productos->post('/delete', 'delete');
$app->mount($productos);

/* Movimientos */
$movimientos = new ControllerCollection(new MovimientosController(), '/movimientos');
$movimientos->get('/getAll', 'getAll');
$movimientos->get('/getFolioConsecutivo/{tipo_movimiento}', 'getFolioConsecutivo');
$movimientos->get('/getexistenciasproducto', 'getexistenciasproducto');
$movimientos->post('/save', 'save');
$movimientos->put('/update', 'update');
$movimientos->post('/delete', 'delete');
$app->mount($movimientos);

/* Movimientos detalles */
$movimientos_detalles = new ControllerCollection(new MovimientosDetallesController(), '/movimientos_detalles');
$movimientos_detalles->get('/getAll', 'getAll');
$movimientos_detalles->get('/getByMovimiento/{movimiento_id}', 'getByMovimiento');
$movimientos_detalles->post('/save', 'save');
$movimientos_detalles->put('/update', 'update');
$movimientos_detalles->post('/delete', 'delete');
$app->mount($movimientos_detalles);

/* Líneas */
$lineas = new ControllerCollection(new LineasController(), '/lineas');
$lineas->get('/getAll', 'getAll');
$lineas->get('/getByCategoria/{categoria_id}', 'getByCategoria');
$lineas->post('/save', 'save');
$lineas->put('/update', 'update');
$lineas->post('/delete', 'delete');
$app->mount($lineas);

/* Bom */
$bom = new ControllerCollection(new BomController(), '/bom');
$bom->get('/getAll', 'getAll');
$bom->get('/getByProducto/{producto_id}', 'getByProducto');
$bom->post('/save', 'save');
$bom->put('/update', 'update');
$bom->post('/delete', 'delete');
$app->mount($bom);

/* Ordenes */
$ordenes = new ControllerCollection(new OrdenesController(), '/ordenes');
$ordenes->get('/getAll', 'getAll');
$ordenes->get('/getFolioConsecutivo/{tipo}', 'getFolioConsecutivo');
$ordenes->post('/save', 'save');
$ordenes->put('/update', 'update');
$ordenes->post('/delete', 'delete');
$app->mount($ordenes);

/* Detalles ordenes */
$ordenes_detalles = new ControllerCollection(new OrdenesDetallesController(), '/ordenes_detalles');
$ordenes_detalles->get('/getAll', 'getAll');
$ordenes_detalles->get('/getByOrden/{orden_id}/{almacen_origen}', 'getByOrden');
$ordenes_detalles->post('/save', 'save');
$ordenes_detalles->put('/update', 'update');
$ordenes_detalles->post('/delete', 'delete');
$app->mount($ordenes_detalles);

/* Aliados */
$aliados = new ControllerCollection(new AliadosController(), '/aliados');
$aliados->get('/getAll', 'getAll');
$aliados->post('/save', 'save');
$aliados->put('/update', 'update');
$aliados->post('/delete', 'delete');
$app->mount($aliados);

/* Comisiones */
$comisiones = new ControllerCollection(new ComisionesController(), '/comisiones');
$comisiones->get('/getAll', 'getAll');
$comisiones->get('/getByProyecto/{proyecto_id}', 'getByProyecto');
$comisiones->post('/save', 'save');
$comisiones->put('/update', 'update');
$comisiones->post('/delete', 'delete');
$app->mount($comisiones);

/* Abonos comisiones */
$comisionesAbonos = new ControllerCollection(new ComisionesAbonosController(), '/abonosComisiones');
$comisionesAbonos->get('/getAll', 'getAll');
$comisionesAbonos->get('/historialAbonos/{comision_id}', 'historialAbonos');
$comisionesAbonos->get('/filtrar/{proyecto_id}/{aliado_id}/{fecha_inicio}/{fecha_fin}/{vendedor_id}', 'filtrar');
$comisionesAbonos->get('/exportCSV_comisiones/{proyecto_id}/{aliado_id}/{fecha_inicio}/{fecha_fin}/{vendedor_id}', 'exportCSV_comisiones');
$comisionesAbonos->get('/exportCSV_pagos/{proyecto_id}/{aliado_id}/{fecha_inicio}/{fecha_fin}/{vendedor_id}', 'exportCSV_pagos');
$comisionesAbonos->post('/save', 'save');
$comisionesAbonos->put('/update', 'update');
$comisionesAbonos->post('/delete', 'delete');
$app->mount($comisionesAbonos);

$reportes = new ControllerCollection(new ReportesController(), '/reportes');
$reportes->get('/getReporte1', 'getReporte1');
$reportes->get('/getReporteAuditoria/{estado}/{municipio}/{tipo}', 'getReporteAuditoria');
$reportes->get('/exportCSV_reporteAuditoria/{estado}/{municipio}/{tipo}', 'exportCSV_reporteAuditoria');
$reportes->get('/getReporteGastos/{recurso}/{proyecto}/{proveedor_id}/{empresa_id}/{lider_id}/{concepto_id}/{subconcepto_id}/{factura}/{status}/{fecha_inicio}/{fecha_fin}/{comprobado}', 'getReporteGastos');
$reportes->get('/exportCSV_gastos/{recurso}/{proyecto}/{proveedor_id}/{empresa_id}/{lider_id}/{concepto_id}/{subconcepto_id}/{factura}/{status}/{fecha_inicio}/{fecha_fin}/{role}/{user_id}/{comprobado}','exportCSV_gastos');
$reportes->get('/reporteProjects/{lider_id}/{empresa_id}/{estado_id}/{municipio_id}/{year}', 'reporteProjects');
$reportes->get('/reporteProjectsMonto/{lider_id}/{empresa_id}/{estado_id}/{municipio_id}/{year}', 'reporteProjectsMonto');
$reportes->get('/reporteProjectsUtilidad/{lider_id}/{empresa_id}/{estado_id}/{municipio_id}/{year}', 'reporteProjectsUtilidad');
$reportes->get('/exportCSV_reporteProjects/{lider_id}/{empresa_id}/{estado_id}/{municipio_id}/{year}', 'exportCSV_reporteProjects');
$reportes->get('/exportCSV_reporteProjectsMonto/{lider_id}/{empresa_id}/{estado_id}/{municipio_id}/{year}', 'exportCSV_reporteProjectsMonto');
$reportes->get('/exportCSV_reporteProjectsUtilidad/{lider_id}/{empresa_id}/{estado_id}/{municipio_id}/{year}', 'exportCSV_reporteProjectsUtilidad');
$reportes->get('/getReporteCobranza/{recurso}/{cliente}/{empresa}/{year}', 'getReporteCobranza');
$reportes->get('/reporteLicitaciones/{estado}/{recurso}/{status}/{empresa}/{year}', 'reporteLicitaciones');
$reportes->get('/exportCSV_reporteLicitaciones/{estado}/{recurso}/{status}/{empresa}/{year}', 'exportCSV_reporteLicitaciones');
$reportes->get('/exportCSV_cobranza/{recurso}/{cliente}/{empresa}/{year}', 'exportCSV_cobranza');
$reportes->get('/getReporteProyectos/{estado}/{municipio}/{programa}/{empresa}/{contrato}', 'getReporteProyectos');
$reportes->get('/exportCSV_proyectos/{estado}/{municipio}/{programa}/{empresa}/{contrato}','exportCSV_proyectos');
$reportes->get('/getKardex/{almacen}/{producto}/{fecha_inicio}/{fecha_fin}','getKardex');
$reportes->get('/exportCSV_kardex/{almacen}/{producto}/{fecha_inicio}/{fecha_fin}', 'exportCSV_kardex');
$reportes->get('/existencias_productos/{almacen}/{producto}/{fecha_inicio}/{fecha_fin}', 'existencias_productos');
$reportes->get('/reporte_oportunidades/{ejecutivo_id}/{sector_id}/{subsector_id}/{status}/{empresa}/{tipo}/{orden}/{ente}/{metodo}/{estado}/{municipio}/{aliado_id}', 'reporte_oportunidades');
$reportes->get('/exportCSV_oportunidades/{ejecutivo_id}/{sector_id}/{subsector_id}/{status}/{empresa}/{tipo}/{orden}/{ente}/{metodo}/{estado}/{municipio}/{aliado_id}', 'exportCSV_oportunidades');
$reportes->get('/get_facturas/{cliente}/{empresa}/{folio}/{ffiscal}/{remision}/{fecha_inicio}/{fecha_fin}', 'get_facturas');
$reportes->get('/exportCSV_facturas/{cliente}/{empresa}/{folio}/{ffiscal}/{remision}/{fecha_inicio}/{fecha_fin}', 'exportCSV_facturas');
$reportes->get('/existencias_productos_pdf/{almacen}/{producto}/{fecha_inicio}/{fecha_fin}', 'existencias_productos_pdf');
$reportes->get('/existencias_productos_csv/{almacen}/{producto}/{fecha_inicio}/{fecha_fin}', 'existencias_productos_csv');
$app->mount($reportes);

$reportesPDF = new ControllerCollection(new ReportesPDFController(), '/reportesPDF');
$reportesPDF->get('/exportPDF_movimiento/{id}', 'exportPDF_movimiento');
$reportesPDF->get('/exportPDF_orden/{orden_id}', 'exportPDF_orden');
$app->mount($reportesPDF);

/* Remisiones */
$remisiones = new ControllerCollection(new RemisionesController(), '/remisiones');
$remisiones->get('/getAll', 'getAll');
$remisiones->get('/getBy/{empresa_id}/{cliente_id}/{status}', 'getBy');
$remisiones->get('/get/{id:[0-9]+}', 'get');
$remisiones->post('/save', 'save');
$remisiones->post('/saveWithFactura', 'saveWithFactura');
$remisiones->put('/update', 'update');
$remisiones->post('/delete', 'delete');
$remisiones->post('/saveProduct', 'saveProduct');
$remisiones->put('/updateProduct', 'updateProduct');
$remisiones->post('/deleteProduct', 'deleteProduct');
$remisiones->put('/updateFiscal', 'updateFiscal');
$remisiones->put('/cancelar', 'cancelar');
$remisiones->get('/getDetalles/{remision_id:[0-9]+}', 'getDetalles');
$remisiones->get('/getDatosFiscales/{remision_id:[0-9]+}', 'getDatosFiscales');
$remisiones->put('/changeStatus', 'changeStatus');
$remisiones->put('/timbrar', 'timbrar');
$remisiones->put('/timbrarPayment', 'timbrarPayment');
$remisiones->put('/cancelInvoice', 'cancelInvoice');
$remisiones->put('/cancelPago', 'cancelPago');
$remisiones->put('/checkInvoice', 'checkInvoice');
$remisiones->get('/getPagos/{remision_id:[0-9]+}', 'getPagos');
$remisiones->get('/keepChecking/{remision_id:[0-9]+}', 'keepChecking');
$remisiones->post('/savePago', 'savePago');
$remisiones->post('/deletePayment', 'deletePayment');
$remisiones->put('/checkPagos', 'checkPagos');
$remisiones->get('/getNextFolio/{remision_id:[0-9]+}/{serie}', 'getNextFolio');
$remisiones->get('/getFolioConsecutivo/{tipo}', 'getFolioConsecutivo');
$app->mount($remisiones);

/* Formas de pago */
$pf = new ControllerCollection(new SatPaymentFormsController(), '/payment-forms');
$pf->get('/get-select-options', 'getSelectOptions');
$app->mount($pf);
/*

/* Usos CFDI */
$ucfdi = new ControllerCollection(new SatUsoCFDIController(), '/uso_cfdi');
$ucfdi->get('/get-select-options', 'getSelectOptions');
$app->mount($ucfdi);
/*

/* Clave producto servicios */
$cprodserv = new ControllerCollection(new SatClaveProductosController(), '/clave_productos');
$cprodserv->get('/get-select-options/{search}', 'getSelectOptions');
$app->mount($cprodserv);

/* Crm */
$crm = new ControllerCollection(new CrmController(), '/crm');
$crm->get('/getAll', 'getAll');
$crm->post('/save', 'save');
$crm->put('/update', 'update');
$crm->post('/delete', 'delete');
$app->mount($crm);

/* Etapas crm */
$etapas = new ControllerCollection(new EtapasController(), '/etapas');
$etapas->get('/getAll', 'getAll');
$etapas->get('/getByCarril/{carril_id}', 'getByCarril');
$etapas->get('/getWithData/{carril_id}/{year}', 'getWithData');
$etapas->post('/save', 'save');
$etapas->put('/update', 'update');
$etapas->put('/updateCierre', 'updateCierre');
$etapas->post('/delete', 'delete');
$app->mount($etapas);

/* Prospectos crm */
$prospectos = new ControllerCollection(new ProspectosController(), '/prospectos');
$prospectos->get('/getAll', 'getAll');
$prospectos->get('/getById/{id}', 'getById');
$prospectos->get('/getBy/{id}', 'getBy');
$prospectos->post('/save', 'save');
$prospectos->put('/update', 'update');
$prospectos->post('/delete', 'delete');
$app->mount($prospectos);

/* Cultivos crm */
$cultivos = new ControllerCollection(new CultivosController(), '/cultivos');
$cultivos->get('/getAll', 'getAll');
$cultivos->get('/getById/{id}', 'getById');
$cultivos->post('/save', 'save');
$app->mount($cultivos);


/* Giros crm */
$giros = new ControllerCollection(new GirosController(), '/giros');
$giros->get('/getAll', 'getAll');
$giros->post('/save', 'save');
$giros->put('/update', 'update');
$giros->post('/delete', 'delete');
$app->mount($giros);

/* Oportunidades */
$oportunidades = new ControllerCollection(new OportunidadesController(), '/oportunidades');
$oportunidades->get('/getAll', 'getAll');
$oportunidades->get('/getHistorial/{id}', 'getHistorial');
$oportunidades->get('/getResumen/{year}', 'getResumen');
$oportunidades->get('/getByEjecutivo/{id}', 'getByEjecutivo');
$oportunidades->get('/getGanadas/{carril_id}/{year}', 'getGanadas');
$oportunidades->get('/getNoLogradas/{carril_id}/{year}', 'getNoLogradas');
$oportunidades->post('/save', 'save');
$oportunidades->put('/update', 'update');
$oportunidades->put('/updateEjecutivo', 'updateEjecutivo');
$oportunidades->put('/nextStep', 'nextStep');
$oportunidades->put('/previousStep', 'previousStep');
$oportunidades->put('/noLograda', 'noLograda');
$oportunidades->post('/delete', 'delete');
$oportunidades->get('/getComentariosByOportunidad/{oportunidad_id}', 'getComentariosByOportunidad');
$app->mount($oportunidades);

/* Actividades */
$actividades = new ControllerCollection(new ActividadesController(), '/actividades');
$actividades->get('/getAll', 'getAll');
$actividades->get('/getByEtapa/{id}', 'getByEtapa');
$actividades->post('/save', 'save');
$actividades->put('/update', 'update');
$actividades->put('/updateObligatorio', 'updateObligatorio');
$actividades->put('/updateLicitacion', 'updateLicitacion');
$actividades->put('/updateProyecto', 'updateProyecto');
$actividades->post('/delete', 'delete');
$app->mount($actividades);

/* Tareas */
$tareas = new ControllerCollection(new TareasController(), '/tareas');
$tareas->get('/getAll', 'getAll');
$tareas->get('/getBy/{id}', 'getBy');
$tareas->get('/getByOportunidad/{oportunidad_id}', 'getByOportunidad');
$tareas->get('/getByAllOportunidad/{oportunidad_id}', 'getByAllOportunidad');
$tareas->get('/getByEjecutivo', 'getByEjecutivo');
$tareas->post('/save', 'save');
$tareas->put('/update', 'update');
$tareas->put('/updateComplete', 'updateComplete');
$tareas->post('/delete', 'delete');
$app->mount($tareas);

/* Aliados Crm */
$aliadoscrm = new ControllerCollection(new AliadosCrmController(), '/aliadoscrm');
$aliadoscrm->get('/getAll', 'getAll');
$aliadoscrm->get('/getByOportunidad/{oportunidad_id}', 'getByOportunidad');
$aliadoscrm->post('/save', 'save');
$aliadoscrm->put('/update', 'update');
$aliadoscrm->post('/delete', 'delete');
$app->mount($aliadoscrm);

/* Archivos Tareas */
$archivos_tareas = new ControllerCollection(new ArchivosTareasController(), '/archivostareas');
$archivos_tareas->get('/getAll', 'getAll');
$archivos_tareas->get('/getByTarea/{id}', 'getByTarea');
$archivos_tareas->get('/getFile/{id}/{ext}', 'getFile');
$archivos_tareas->post('/uploadDocumento', 'uploadDocumento');
$archivos_tareas->post('/deleteFormato', 'deleteFormato');
$app->mount($archivos_tareas);

/* Vendedores */
$vendedores = new ControllerCollection(new VendedoresController(), '/vendedores');
$vendedores->get('/getAll', 'getAll');
$vendedores->get('/getGerentes', 'getGerentes');
$vendedores->put('/update', 'update');
$app->mount($vendedores);

/* Comentarios */
$comentarios = new ControllerCollection(new ComentariosController(), '/comentarios');
$comentarios->get('/getAll','getAll');
$comentarios->get('/getByTarea/{tarea_id}', 'getByTarea');
$comentarios->get('/getByOportunidad/{oportunidad_id}', 'getByOportunidad');
$comentarios->post('/save','save');
$comentarios->put('/update', 'update');
$comentarios->post('/delete', 'delete');
$comentarios->get('/getFile/{id}/{ext}', 'getFile');
$comentarios->post('/uploadDocumento', 'uploadDocumento');
$comentarios->post('/deleteFormato', 'deleteFormato');
$app->mount($comentarios);

/* Comentarios */
$compras = new ControllerCollection(new ComprasController(), '/compras');
$compras->get('/getAll','getAll');
$compras->get('/getFolioConsecutivo', 'getFolioConsecutivo');
$compras->get('/exportPDF_compra/{id_compra}', 'exportPDF_compra');
$compras->post('/save','save');
$compras->put('/update', 'update');
$compras->post('/delete', 'delete');
$app->mount($compras);

/* Compras detalles */
$compras_detalles = new ControllerCollection(new ComprasDetallesController(), '/compras_detalles');
$compras_detalles->get('/getAll', 'getAll');
$compras_detalles->get('/getByCompra/{compra_id}', 'getByCompra');
$compras_detalles->get('/exportCSV', 'exportCSV');
$compras_detalles->post('/cargarCSV', 'cargarCSV');
$compras_detalles->post('/save', 'save');
$compras_detalles->put('/update', 'update');
$compras_detalles->post('/delete', 'delete');
$compras_detalles->post('/deleteByCompra', 'deleteByCompra');
$app->mount($compras_detalles);

/* Recepciones */
$recepciones = new ControllerCollection(new RecepcionesController(), '/recepciones');
$recepciones->get('/getAll','getAll');
$recepciones->get('/getByCompra/{compra_id}', 'getByCompra');
$recepciones->get('/getFolioConsecutivo', 'getFolioConsecutivo');
$recepciones->post('/save','save');
$recepciones->put('/update', 'update');
$recepciones->post('/delete', 'delete');
$app->mount($recepciones);

/* Compras detalles */
$recepciones_detalles = new ControllerCollection(new RecepcionesDetallesController(), '/recepciones_detalles');
$recepciones_detalles->get('/getAll', 'getAll');
$recepciones_detalles->get('/getByRecepcion/{recepcion_id}', 'getByRecepcion');
$recepciones_detalles->post('/cargarCSV', 'cargarCSV');
$recepciones_detalles->post('/save', 'save');
$recepciones_detalles->put('/update', 'update');
$recepciones_detalles->post('/delete', 'delete');
$app->mount($recepciones_detalles);


/* Líneas */
$destinos = new ControllerCollection(new DestinosController(), '/destinos');
$destinos->get('/getAll', 'getAll');
$destinos->get('/getById/{id}', 'getById');
$destinos->post('/save', 'save');
$destinos->put('/update', 'update');
$destinos->post('/delete', 'delete');
$app->mount($destinos);

/* Sucursales*/
$sucursales = new ControllerCollection(new SucursalesEmpresasController(), '/sucursales');
$sucursales->get('/getAll', 'getAll');
$sucursales->get('/getByEmpresa/{empresa_id}', 'getByEmpresa');
$sucursales->get('/getByEmpresaOptions/{empresa_id}', 'getByEmpresaOptions');
$sucursales->post('/save', 'save');
$sucursales->put('/update', 'update');
$sucursales->post('/delete', 'delete');
$app->mount($sucursales);

/* Sucursales proyecto*/
$pmosucursales = new ControllerCollection(new PmoSucursalesController(), '/pmosucursales');
$pmosucursales->get('/getAll', 'getAll');
$pmosucursales->post('/save', 'save');
$pmosucursales->put('/update', 'update');
$pmosucursales->post('/delete', 'delete');
$app->mount($pmosucursales);

/* DOCUMENTOS PERFILES */
$documentos_comisiones = new ControllerCollection(new ComisionesDocumentosController(), '/documentos_comisiones');
$documentos_comisiones->get('/getAll', 'getAll');
$documentos_comisiones->get('/getFile/{id}/{ext}', 'getFile');
$documentos_comisiones->post('/uploadDocumento', 'uploadDocumento');
$documentos_comisiones->post('/deleteFormato', 'deleteFormato');
$app->mount($documentos_comisiones);

/* DOCUMENTOS ABONOS */
$documentos_abonos = new ControllerCollection(new AbonosDocumentosController(), '/documentos_abonos');
$documentos_abonos->get('/getAll', 'getAll');
$documentos_abonos->get('/getFile/{id}/{ext}', 'getFile');
$documentos_abonos->post('/uploadDocumento', 'uploadDocumento');
$documentos_abonos->post('/deleteFormato', 'deleteFormato');
$app->mount($documentos_abonos);

/* Cotizaciones */
$cotizaciones = new ControllerCollection(new CotizacionesController(), '/cotizaciones');
$cotizaciones->get('/getAll','getAll');
$cotizaciones->get('/getByOportunidad/{oportunidad_id}', 'getByOportunidad');
$cotizaciones->get('/exportPDF_cotizacion/{id_cotizacion}', 'exportPDF_cotizacion');
$cotizaciones->post('/save','save');
$cotizaciones->put('/update', 'update');
$cotizaciones->post('/delete', 'delete');
$app->mount($cotizaciones);

/* Cotizaciones detalles */
$cotizaciones_detalles = new ControllerCollection(new CotizacionesDetallesController(), '/cotizaciones_detalles');
$cotizaciones_detalles->get('/getAll', 'getAll');
$cotizaciones_detalles->get('/getByCotizacion/{cotizacion_id}', 'getByCotizacion');
$cotizaciones_detalles->post('/save', 'save');
$cotizaciones_detalles->put('/update', 'update');
$cotizaciones_detalles->post('/delete', 'delete');
$app->mount($cotizaciones_detalles);

$powerbi = new ControllerCollection(new PowerBiController(), '/powerbi');
$powerbi->get('/exportJSON', 'exportJSON');
$app->mount($powerbi);

/* Cuentas */
$cuentas = new ControllerCollection(new CuentasController(), '/cuentas');
$cuentas->get('/getAll', 'getAll');
$cuentas->post('/save', 'save');
$cuentas->put('/update', 'update');
$cuentas->post('/delete', 'delete');
$app->mount($cuentas);

$saldos = new ControllerCollection(new SaldosController(), '/saldos');
$saldos->get('/getAll', 'getAll');
$saldos->get('/getByCuenta/{cuenta_id}', 'getByCuenta');
$saldos->post('/save', 'save');
$saldos->put('/update', 'update');
$saldos->post('/delete', 'delete');
$app->mount($saldos);

$data = [
    '/lines'                => 'LinesController' ,
];

$routes = [];
foreach ($data as $key => $value) {
    $ctrl = new LazyControllerCollection($value, $key);
    $ctrl->get('/get/{id:[0-9]+}', 'get');
    $ctrl->get('/getAll', 'getAll');
    $ctrl->post('/save', 'save');
    $ctrl->put('/update', 'update');
    $ctrl->post('/delete', 'delete');
    $ctrl->get('/export', 'export');
    $routes[$key] = $ctrl;
}

foreach ($routes as $sr) {
    $app->mount($sr);
}
