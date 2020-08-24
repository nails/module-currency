<?php

use Nails\Currency\Service;

return [
    'services' => [
        'Currency'       => function (): Service\Currency {
            if (class_exists('\App\Currency\Service\Currency')) {
                return new \App\Currency\Service\Currency();
            } else {
                return new Service\Currency();
            }
        },
        'Exchange'       => function (Service\Currency $oCurrencyService = null): Service\Exchange {
            if (class_exists('\App\Currency\Service\Exchange')) {
                return new \App\Currency\Service\Exchange($oCurrencyService);
            } else {
                return new Service\Exchange($oCurrencyService);
            }
        },
        'CurrencyDriver' => function () {
            if (class_exists('\App\Currency\Service\Currency')) {
                return new \App\Currency\Service\Driver();
            } else {
                return new \Nails\Currency\Service\Driver();
            }
        },
    ],
];
