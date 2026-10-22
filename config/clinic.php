<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Identidad de la clínica
    |--------------------------------------------------------------------------
    |
    | Datos compartidos por la landing y el panel de administración.
    |
    */

    'name' => 'LuDa Dental',

    'tagline' => 'Clínica de Especialidades Odontológicas',

    /*
    |--------------------------------------------------------------------------
    | Logotipos
    |--------------------------------------------------------------------------
    |
    | Rutas base dentro de /public. Se detecta automáticamente la extensión
    | (svg, png, webp, jpg, jpeg o avif). Si el archivo no existe, la interfaz
    | muestra el logotipo de texto como respaldo.
    |
    */

    'logos' => [
        'header' => 'images/mi-logo-principal',
        'footer' => 'images/mi-logo-blanco',
    ],

    'timezone' => 'America/Mexico_City',

    /*
    |--------------------------------------------------------------------------
    | Contacto
    |--------------------------------------------------------------------------
    |
    | El número de WhatsApp se guarda en formato internacional, sin signos ni
    | espacios, listo para construir enlaces https://wa.me/{numero}.
    |
    */

    'whatsapp' => '523317118089',

    'phones' => [
        ['label' => '33 1711 8089', 'number' => '3317118089'],
        ['label' => '33 3968 5021', 'number' => '3339685021'],
    ],

    'address' => [
        'line1' => 'Plaza Península',
        'line2' => 'Zapopan, Jalisco, México',
        'reference' => 'Confirma con nosotros el local exacto dentro de la plaza al agendar tu cita.',
    ],

    'map' => [
        'embed_url' => 'https://www.google.com/maps?q=Plaza%20Pen%C3%ADnsula%2C%20Zapopan%2C%20Jalisco&output=embed',
        'link' => 'https://www.google.com/maps/search/?api=1&query=Plaza%20Pen%C3%ADnsula%2C%20Zapopan%2C%20Jalisco',
    ],

];
