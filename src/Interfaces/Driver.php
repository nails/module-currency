<?php

namespace Nails\Currency\Interfaces;

use Nails\Currency\Resource\Currency;

interface Driver
{
    /**
     * Returns the rate between two given currencies
     *
     * @param Currency $oFrom The from currency
     * @param Currency $oTo   The to currency
     *
     * @return float
     */
    public function getRate(Currency $oFrom, Currency $oTo): float;
}
