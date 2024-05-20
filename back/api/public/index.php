<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Micro;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;

error_reporting(E_ALL);

define('APP_PATH', realpath('..'));

try {

    $di = new FactoryDefault();

    /**
     * Include Services
     */
    include APP_PATH . '/config/services.php';

    /**
     * Include composer autoloader
     */
    require APP_PATH . '/vendor/autoload.php';

    /**
     * Include Access Control List
     */
    // require APP_PATH . "/config/acl.php";

    /**
     * Call the autoloader service.  We don't need to keep the results.
     */
    $di->getLoader();

    // Create a events manager
    $eventsManager = new EventsManager();

    $eventsManager->attach(
        'micro:beforeExecuteRoute',
        function (Event $event, $app) {
            $app->response
                ->setHeader('Access-Control-Allow-Origin', '*')
                ->setHeader('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,OPTIONS')
                ->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Range, Content-Disposition, Content-Type, Authorization, Access-Control-Allow-Origin')
                ->setHeader('Access-Control-Allow-Credentials', true);

            if($app->request->isOptions()) {            
                return true;
            }
            
            if ($app->request->getURI() === "/auth/login") {
                return true;
            }
            if ($app->request->getURI() === '/users/recoverPassword') {
                return true;
            }
            if (strpos($app->request->getURI(),'/unidades/exportPdf')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/servicios/exportPDF_mantenimientos')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/servicios/exportPDF_proxServicios')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/servicios/export')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/servicios/exportCSV_proxServicios')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/servicios/exportPDF_consumoLitros')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/servicios/exportCSV_consumoLitros')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(), '/proveedor/getFile') !== false) {
                return true;
            }            
            if (strpos($app->request->getURI(), '/facturasContratos/getFile') !== false) {
                return true;
            }
            if (strpos($app->request->getURI(), '/cotizaciones_gastos/getFile') !== false) {
                return true;
            }
            if (strpos($app->request->getURI(), '/facturas_gastos/getFile') !== false) {
                return true;
            }
            if (strpos($app->request->getURI(), '/comprobantes_gastos/getFile') !== false) {
                return true;
            }
            if (strpos($app->request->getURI(), '/carpetas/getFile') !== false) {
                return true;
            }
            if (strpos($app->request->getURI(), '/fianzas/getFile') !== false) {
                return true;
            }
            if (strpos($app->request->getURI(), '/eventos/getFile') !== false) {
                return true;
            }
            if (strpos($app->request->getURI(), '/licitaciones_requisitos/getFile') !== false) {
                return true;
            }
            if (strpos($app->request->getURI(), '/licitaciones_propuestas/getFile') !== false) {
                return true;
            }
            if (strpos($app->request->getURI(), '/licitaciones/getFile') !== false) {
                return true;
            }
            if (strpos($app->request->getURI(), '/archivos_solicitudes/getFile') !== false) {
                return true;
            }
            if (strpos($app->request->getURI(), '/carga_csv/getFile') !== false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/reportes/exportCSV_gastos')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/reportes/exportCSV_cobranza')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/reportes/exportCSV_proyectos')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(), '/padron_requisitos/getFile') !== false) {
                return true;
            }
            if (strpos($app->request->getURI(), '/contratos/getFile') !== false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/reportes/exportCSV_reporteProjects')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/reportes/exportCSV_reporteProjectsMonto')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/posgrados/getFile')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/cursos/getFile')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/perfiles/getFile')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/documentos_perfiles/getFile')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/cartas/getFile')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/titulos/getFile')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/cedulas/getFile')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/reportes/exportCSV_reporteLicitaciones')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/reportes/exportCSV_kardex')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/carga_csv/exportCSV_actividades')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/movimientos/getexistenciasproducto')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/reportes/exportCSV_reporteAuditoria')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/reportesPDF/exportPDF_movimiento')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/reportesPDF/exportPDF_orden')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/remisionesFacturas/getFile')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/abonosComisiones/exportCSV_comisiones')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/proveedor/exportCSV')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/archivostareas/getFile')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/remisionesFacturas/agregarNombre')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/remisionesFacturas/getFacturasDuplicadasCsv')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/reportes/exportCSV_facturas')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/comentarios/getFile')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/reportes/exportCSV_oportunidades')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/compras/exportPDF_compra')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/compras_detalles/exportCSV')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/abonosComisiones/exportCSV_pagos')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/reportes/existencias_productos_pdf')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/reportes/existencias_productos_csv')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/documentos_comisiones/getFile')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/documentos_abonos/getFile')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/cotizaciones/exportPDF_cotizacion')!==false) {
                return true;
            }
            if (strpos($app->request->getURI(),'/powerbi/exportJSON')!==false) {
                return true;
            }
            $valid = Auth::validateRequest($app->request, $app->config->jwtkey);

            if(!$valid){
                $app->response->setStatusCode(401, 'Unauthorized');
                $app->response->setContentType('application/json');
                $app->response->setJsonContent('Access is not authorized');
                $app->response->send();
                return false;
            }
            return true;
        }
    );

    /**
     * Starting the application
     * Assign service locator to the application
     */
    $app = new Micro($di);

    !$app->session->isStarted() && $app->session->start();


    $app->setEventsManager($eventsManager);


    /**
     * Include Application
     */
    include APP_PATH . '/app.php';

    /**
     * Handle the request
     */
    $app->handle();

} catch (\Exception $e) {
    // echo json_encode(['result' => 'error', 'message' => $e->getMessage(), 'details' => $e->getTraceAsString()]);
    $response = new \Phalcon\Http\Response();
    $response->setStatusCode(200, 'OK');
    $response->setJsonContent(['result' => 'error', 'message' => $e->getMessage(), 'details' => $e->getTraceAsString()]);
    $response->send();
}
