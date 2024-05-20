import layoutDefault from 'layouts/Default'
import layoutSimple from 'layouts/Simple'

function route (path, layout, component) {
  return { path: path, component: layout, children: [{ path: '', component: component }] }
}

export default [
  /* TODOS */
  route('/', layoutSimple, () => import('pages/Login')),
  route('/recover', layoutSimple, () => import('pages/Recover')),
  route('/dashboard', layoutDefault, () => import('pages/Dashboard')),
  route('/profile', layoutDefault, () => import('pages/Profile')),
  route('/changePassword', layoutDefault, () => import('pages/ChangePassword')),

  /* SISTEMA */
  route('/usuarios', layoutDefault, () => import('pages/sys/Users')),
  route('/roles', layoutDefault, () => import('pages/sys/Roles')),

  /* CATALOGOS */
  /* Aqui esta la tabla vnt_clientes */
  route('/clientes', layoutDefault, () => import('pages/ventas/Clientes')),

  /* CONFIGURACIÓN */
  route('/menus', layoutDefault, () => import('pages/sys/Menus')),

  /* PROGRAMAS */
  route('/programas', layoutDefault, () => import('pages/vnt/Programas')),

  /* SUBPROGRAMAS */
  route('/subprogramas', layoutDefault, () => import('pages/vnt/Subprogramas')),

  /* PROYECTOS */
  route('/proyectos', layoutDefault, () => import('pages/vnt/Recursos')),
  route('/proyectos/:proyecto/:project', layoutDefault, () => import('pages/vnt/Recursos')),

  /* EMPRESAS */
  route('/empresas', layoutDefault, () => import('pages/vnt/Empresas')),

  /* PRESUPUESTOS */
  route('/presupuestos', layoutDefault, () => import('pages/pmo/Proyectos')),
  route('/presupuestos/:id', layoutDefault, () => import('pages/pmo/Proyectos')),

  /* SUCURSALES */
  route('/sucursales', layoutDefault, () => import('pages/pmo/Sucursales')),

  /* EJECUCIÓN DE PRESUPUESTOS */
  route('/ejecucion_presupuestos', layoutDefault, () => import('pages/fin/Gastos')),
  route('/ejecucion_presupuestos/:id/:accion', layoutDefault, () => import('pages/fin/Gastos')),
  route('/ejecucion_presupuestos/:id/:accion/:compra/:proveedor/:empresa', layoutDefault, () => import('pages/fin/Gastos')),

  /* CONTRATOS */
  route('/contratos', layoutDefault, () => import('pages/vnt/Contratos')),
  route('/contratos/:id', layoutDefault, () => import('pages/vnt/Contratos')),

  /* ESTADOS */
  route('/estados', layoutDefault, () => import('pages/vnt/Estados')),

  /* MUNICIPIOS */
  route('/municipios', layoutDefault, () => import('pages/vnt/Municipios')),

  /* PROVEEDORES */
  route('/padron_proveedores', layoutDefault, () => import('pages/vnt/Proveedores')),

  /* LICITACIONES */
  route('/licitaciones', layoutDefault, () => import('pages/lic/Licitaciones')),

  /* PERFILES EXPERTOS */
  route('/perfiles_expertos', layoutDefault, () => import('pages/lic/Perfiles')),

  /* PROVEEDORES GASTOS */
  route('/proveedor', layoutDefault, () => import('pages/pmo/Proveedores')),

  /* Log Levels */
  route('/logs', layoutDefault, () => import('pages/sys/LogLevels')),

  /* REQUISITOS */
  route('/requisitos', layoutDefault, () => import('pages/exp/Requisitos')),

  /* GASTOS PENDIENTES */
  route('/gastos_pendientes', layoutDefault, () => import('pages/pmo/GastosPendientes')),

  /* GASTOS ESPECIALES */
  route('/gastos_ependientes', layoutDefault, () => import('pages/pmo/GastosEspeciales')),

  /* GASTOS APROBADOS */
  route('/reporteLogs', layoutDefault, () => import('pages/sys/ReporteLogs')),

  /* AUTORIZACIONES */
  route('/autorizaciones', layoutDefault, () => import('pages/fin/Autorizaciones')),

  /* TIPOS DE GASTOS */
  route('/tipos_gastos', layoutDefault, () => import('pages/fin/TiposGastos')),

  /* FIANZAS */
  route('/fianzas', layoutDefault, () => import('pages/lic/Fianzas')),

  /* CARPETAS */
  route('/repositorio', layoutDefault, () => import('pages/sys/Folders')),

  /* ABONOS */
  route('/abonos', layoutDefault, () => import('pages/vnt/Abonos')),

  /* CONCEPTOS */
  route('/conceptos', layoutDefault, () => import('pages/vnt/Conceptos')),

  /* SUBCONCEPTOS */
  route('/subconceptos', layoutDefault, () => import('pages/vnt/Subconceptos')),

  /* PERFILES EXPERTOS */
  route('/aptitudes', layoutDefault, () => import('pages/per/Aptitudes.vue')),
  route('/areas', layoutDefault, () => import('pages/per/Areas.vue')),

  /* INVENTARIOS */
  route('/almacenes', layoutDefault, () => import('pages/inv/Almacenes')),

  /* TIPOS DE ARTÍCULOS */
  route('/tipos_articulos', layoutDefault, () => import('pages/inv/TiposArticulos')),

  /* TIPOS DE MOVIMIENTOS */
  route('/tipos_movimientos', layoutDefault, () => import('pages/inv/TiposMovimientos')),

  /* TIPOS DE PRESENTACIONES */
  route('/tipos_presentaciones', layoutDefault, () => import('pages/inv/TiposPresentaciones')),

  /* PRODUCTOS */
  route('/productos', layoutDefault, () => import('pages/inv/Productos')),

  /* MOVIMIENTOS */
  route('/movimientos', layoutDefault, () => import('pages/inv/Movimientos')),

  /* LINEAS */
  route('/lineas', layoutDefault, () => import('pages/inv/Lineas')),

  /* REMISIONES */
  route('/remisiones', layoutDefault, () => import('pages/vnt/Remisiones')),

  /* ORDENES */
  route('/ordenes', layoutDefault, () => import('pages/pro/Ordenes')),

  /* ALIADOS */
  route('/aliados', layoutDefault, () => import('pages/com/Aliados')),

  /* ABONOS FACTURAS */
  route('/abonos_comisiones', layoutDefault, () => import('pages/com/AbonosComisiones')),

  /* PLANTILLA */
  route('/plantilla', layoutDefault, () => import('pages/com/Plantilla')),

  /* CRM */
  route('/crm', layoutDefault, () => import('pages/crm/Crm')),
  /* CATALOGO DESTINOS */
  route('/destinos', layoutDefault, () => import('pages/crm/Destinos')),

  /* PROSPECTOS */
  route('/prospectos', layoutDefault, () => import('pages/crm/Prospectos')),

  /* GIROS */
  route('/giros', layoutDefault, () => import('pages/crm/Giros')),

  /* PROSPECCIÓN */
  route('/prospeccion', layoutDefault, () => import('pages/crm/Prospeccion')),

  /* PROSPECCIÓN */
  route('/vendedores', layoutDefault, () => import('pages/crm/Vendedores')),

  /* REMISIONES MASIVAS */
  route('/remisiones_masivas', layoutDefault, () => import('pages/vnt/RemisionesMasivas')),

  /* RESUMEN PROSPECCIÓN */
  route('/resumen', layoutDefault, () => import('pages/crm/Resumen')),

  /* RESUMEN CRM */
  route('/resumen_crm', layoutDefault, () => import('pages/crm/DashboardCrm')),

  /* PROSPECCIÓN */
  route('/compras', layoutDefault, () => import('pages/cmp/Compras')),

  /* REPORTES */
  route('/reporte_projects', layoutDefault, () => import('pages/rpt/ReporteProjects')),
  route('/reporte_projects2', layoutDefault, () => import('pages/rpt/ReporteProjects2')),
  route('/reporte_projects_utilidad', layoutDefault, () => import('pages/rpt/ReporteProjectsUtilidad')),
  route('/reporte_gastos', layoutDefault, () => import('pages/rpt/ReporteGastos')),
  route('/reporte_cobranza', layoutDefault, () => import('pages/rpt/ReporteCobranza')),
  route('/reporte_proyectos', layoutDefault, () => import('pages/rpt/ReporteProyectos')),
  route('/reporte_presupuesto_projects', layoutDefault, () => import('pages/rpt/ReporteProjectsPorcentaje')),
  route('/reporte_presupuesto_monto', layoutDefault, () => import('pages/rpt/ReporteProjectsPresupuesto')),
  route('/reporte_licitaciones', layoutDefault, () => import('pages/rpt/ReporteLicitaciones')),
  route('/kardex', layoutDefault, () => import('pages/rpt/Kardex')),
  route('/existencias', layoutDefault, () => import('pages/rpt/Existencias')),
  route('/auditoria', layoutDefault, () => import('pages/rpt/Auditoria')),
  route('/reporte_oportunidades', layoutDefault, () => import('pages/rpt/Oportunidades')),
  route('/reporte_facturas', layoutDefault, () => import('pages/rpt/Facturas')),

  /* DEVOLUCIONES */
  route('/devoluciones', layoutDefault, () => import('pages/fin/Devoluciones')),

  /* CUENTAS */
  route('/cuentas', layoutDefault, () => import('pages/vnt/Cuentas')),

  {
    path: '*',
    component: () => import('pages/404')
  }
]
