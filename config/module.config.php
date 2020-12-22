<?php
namespace Transcrire;

return  [
    'view_manager' => [
        'template_path_stack' => [
            OMEKA_PATH . '/themes/transcrire/view/transcrire',
        ],
    ],
    'view_helpers' => [
        'factories' => [
            'transcrire' => Service\ViewHelper\TranscrireFactory::class,
        ],
    ],
];
