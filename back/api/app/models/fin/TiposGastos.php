<?php
use Phalcon\Mvc\Model;

class TiposGastos extends Model
{
    public function initialize ()
    {
        $this->setSource('fin_tipos_gastos');
        
        $this->hasMany(
            'id',
            'GastosSolicitudes',
            'tipo',
            [
                'foreignKey' => [
                    'message' => 'Este tipo de gasto aún tiene gastos relacionados',
                ]
            ]
        );
    }
}