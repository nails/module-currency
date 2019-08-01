<?php

/**
 * Provides an interface for handling currencies
 *
 * @package     Nails
 * @subpackage  module-currency
 * @category    Service
 * @author      Nails Dev Team
 */

namespace Nails\Currency\Service;

use Nails\Factory;
use Nails\Currency\Exception\CurrencyException;
use Nails\Currency\Resource;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Class Currency
 *
 * @package Nails\Currency\Service
 */
class Currency
{
    /**
     * All currencies supported by this system
     *
     * @var Resource\Currency[]
     */
    protected $aSupportedCurrencies = [];

    // --------------------------------------------------------------------------

    /**
     * All currencies supported by this system
     *
     * @var Resource\Currency[]
     */
    protected $aEnabledCurrencies = [];

    // --------------------------------------------------------------------------

    /**
     * Currency constructor.
     */
    public function __construct()
    {
        $aSupported = Factory::property('SupportedCurrencies', 'nails/module-currency');
        foreach ($aSupported as $oCurrency) {
            $this->aSupportedCurrencies[] = new Resource\Currency($oCurrency);
        }
        dd($this->aSupportedCurrencies);
        $aEnabled = appSetting('aEnabledCurrencies', 'nails/module-currency') ?? [];

        foreach ($aEnabled as $sCode) {
            $this->aEnabledCurrencies[] = $this->getByIsoCode($sCode);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all supported currencies
     *
     * @return Resource\Currency[]
     */
    public function getAll(): array
    {
        return $this->aSupportedCurrencies;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all enabled currencies
     *
     * @return Resource\Currency[]
     */
    public function getAllEnabled(): array
    {
        return $this->aEnabledCurrencies;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a currency by it's ISO 4217 code
     *
     * @param string $sCode The currency to get
     *
     * @return Resource\Currency
     * @throws CurrencyException
     */
    public function getByIsoCode(string $sCode): Resource\Currency
    {
        if (array_key_exists($sCode, $this->aSupportedCurrencies)) {
            return $this->aSupportedCurrencies[$sCode];
        }

        throw new CurrencyException('"' . $sCode . '" is not a valid currency code.');
    }

    // --------------------------------------------------------------------------

    /**
     * Formats a currency
     *
     * @param string $sCode          The currency to get
     * @param number $nValue         The value
     * @param bool   $bIncludeSymbol Include the currency symbol
     *
     * @return string
     * @throws CurrencyException
     */
    public function format(string $sCode, $nValue, bool $bIncludeSymbol = true)
    {
        $oCurrency = $this->getByIsoCode($sCode);
        $sOut      = number_format(
            $nValue,
            $oCurrency->decimal_precision,
            $oCurrency->decimal_symbol,
            $oCurrency->thousands_separator
        );

        if ($bIncludeSymbol) {
            if ($oCurrency->symbol_position == 'BEFORE') {
                $sOut = $oCurrency->symbol . $sOut;
            } else {
                $sOut = $sOut . $oCurrency->symbol;
            }
        }

        return $sOut;
    }
}
