<?php
use Phalcon\Mvc\Model;

class GastosSolicitudes extends Model
{
    public function initialize ()
    {
        $this->setSource('fin_gastos');

        $this->belongsTo('actividad_id', 'Actividades', 'id');
        $this->belongsTo('proveedor_id', 'Proveedores', 'id');

        $this->hasMany(
            'id',
            'CotizacionGastos',
            'gasto_id',
            [
                'foreignKey' => [
                    'message' => 'El gasto aún tiene cotizaciones relacionadas, para continuar debe eliminar todos los archivos.',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'FacturaGastos',
            'gasto_id',
            [
                'foreignKey' => [
                    'message' => 'El gasto aún tiene facturas relacionadas, para continuar debe eliminar todos los archivos.',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'ComprobanteGastos',
            'gasto_id',
            [
                'foreignKey' => [
                    'message' => 'El gasto aún tiene comprobantes relacionados, para continuar debe eliminar todos los archivos.',
                ]
            ]
        );

    }
}