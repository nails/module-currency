<?php

/**
 * Provides an interface for handling currency exchange
 *
 * @package     Nails
 * @subpackage  module-currency
 * @category    Service
 * @author      Nails Dev Team
 */

namespace Nails\Currency\Service;

use Nails\Common\Exception\FactoryException;
use Nails\Currency\Constants;
use Nails\Currency\Exception\CurrencyException;
use Nails\Factory;

/**
 * Class Exchange
 *
 * @package Nails\Currency\Service
 */
class Exchange
{
    /** @var Currency */
    protected $oCurrency;

    // --------------------------------------------------------------------------

    /**
     * Exchange constructor.
     *
     * @param Currency|null $oCurrencyService The currency service to use
     *
     * @throws FactoryException
     */
    public function __construct(Currency $oCurrencyService = null)
    {
        $this->oCurrency = $oCurrencyService ?? Factory::service('Currency', Constants::MODULE_SLUG);
    }

    // --------------------------------------------------------------------------

    /**
     * Exchanges a value from one currency to another
     *
     * @param number                   $nValue        The value to exchange
     * @param Resource\Currency|string $mCurrencyFrom The currency to exchange from
     * @param Resource\Currency|string $mCurrencyTo   The currency to exchange to
     *
     * @throws CurrencyException
     */
    public function exchange($nValue, $mCurrencyFrom, $mCurrencyTo)
    {
        $oCurrencyFrom = $this->oCurrency->inferCurrency($mCurrencyFrom, __METHOD__);
        $oCurrencyTo   = $this->oCurrency->inferCurrency($mCurrencyTo, __METHOD__);
        $fRate         = $this->getRate($oCurrencyFrom, $oCurrencyTo);

        dd($nValue, $oCurrencyFrom, $oCurrencyTo, $fRate);
    }

    // --------------------------------------------------------------------------

    public function getRate($mCurrencyFrom, $mCurrencyTo): float
    {
        $oCurrencyFrom = $this->oCurrency->inferCurrency($mCurrencyFrom, __METHOD__);
        $oCurrencyTo   = $this->oCurrency->inferCurrency($mCurrencyTo, __METHOD__);
    }
}
