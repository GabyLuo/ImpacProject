<?php
use Phalcon\Mvc\Model;

class Folders extends Model
{
    public function initialize ()
    {
        $this->setSource('sys_folders');
        
        /* $this->hasMany(
            'id',
            'Archivos',
            'carpeta_padre',
            [
                'foreignKey' => [
                    'message' => 'Esta carpeta aún tiene archivos y/o carpetas relacionados',
                ]
            ]
        ); */
    }
}