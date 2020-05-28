<?php

use Nails\Currency\Service;

return [
    'services' => [
        'Currency' => function (): Service\Currency {
            if (class_exists('\App\Currency\Service\Currency')) {
                return new \App\Currency\Service\Currency();
            } else {
                return new Service\Currency();
            }
        },
    ],
];
