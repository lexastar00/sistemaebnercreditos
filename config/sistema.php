<?php

return [

    /*
    | Cuenta inicial del Administrador. La crea AdminSeeder con «php artisan migrate --seed».
    | Se leen del archivo .env; nunca se escriben en el código.
    */
    'admin_email' => env('ADMIN_EMAIL'),
    'admin_password' => env('ADMIN_PASSWORD'),

    /*
    | Carrera de la División: dato institucional fijo en todos los documentos.
    | Se define aquí y en la tabla «carreras», no escrito en cada plantilla o pantalla.
    */
    'carrera' => 'Ingeniería en Sistemas Computacionales',

];
