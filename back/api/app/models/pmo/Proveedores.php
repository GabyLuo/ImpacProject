<?php
use Phalcon\Mvc\Model;

class Proveedores extends Model
{
    public function initialize ()
    {
        $this->setSource('pmo_proveedores');

        $this->hasMany(
			'id',
			'GastosSolicitudes',
			'proveedor_id',
			[
				'foreignKey' => [
					'message' => 'El proveedor aún tiene relaciones con la tabla gastos',
				]
			]
		);

		$this->hasMany(
			'id',
			'Movimientos',
			'proveedor_id',
			[
				'foreignKey' => [
					'message' => 'El proveedor aún tiene relaciones con los movimientos de entrada',
				]
			]
		);
    }
}