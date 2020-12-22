<?php
namespace Transcrire;

return  [
    'view_manager' => [
        'template_path_stack' => [
           OMEKA_PATH . '/modules/Transcrire/view',
        ],
    ],
    'view_helpers' => [
        'factories' => [
            'transcrire' => Service\ViewHelper\TranscrireFactory::class,
        ],
    ],
];
