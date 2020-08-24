<?php

use Nails\Currency\Factory;
use Nails\Currency\Service;

return [
    'services'  => [
        'Currency'       => function (): Service\Currency {
            if (class_exists('\App\Currency\Service\Currency')) {
                return new \App\Currency\Service\Currency();
            } else {
                return new Service\Currency();
            }
        },
        'Exchange'       => function (
            Service\Currency $oCurrencyService = null,
            Factory\ExchangeMatrix $oMatrix = null
        ): Service\Exchange {
            if (class_exists('\App\Currency\Service\Exchange')) {
                return new \App\Currency\Service\Exchange($oCurrencyService, $oMatrix);
            } else {
                return new Service\Exchange($oCurrencyService, $oMatrix);
            }
        },
        'CurrencyDriver' => function () {
            if (class_exists('\App\Currency\Service\Driver')) {
                return new \App\Currency\Service\Driver();
            } else {
                return new \Nails\Currency\Service\Driver();
            }
        },
    ],
    'factories' => [
        'ExchangeMatrix' => function (array $aCurrencies, \stdClass $oMatrix = null) {
            if (class_exists('\App\Currency\Factory\ExchangeMatrix')) {
                return new \App\Currency\Factory\ExchangeMatrix($aCurrencies, $oMatrix);
            } else {
                return new \Nails\Currency\Factory\ExchangeMatrix($aCurrencies, $oMatrix);
            }
        },
    ],
];
