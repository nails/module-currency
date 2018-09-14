<?php

/**
 * Provides an interface for querying and converting currencies
 *
 * @package     Nails
 * @subpackage  module-currency
 * @category    Library
 * @author      Nails Dev Team
 */

namespace Nails\Currency\Service;

use Nails\Factory;
use Nails\Currency\Exception\CurrencyException;

class Currency {

    /**
     * All currencies supported by this system
     * @var array
     */
    protected $aSupportedCurrencies;

    // --------------------------------------------------------------------------

    /**
     * All currencies supported by this system
     * @var array
     */
    protected $aEnabledCurrencies;

    // --------------------------------------------------------------------------

    /**
     * Currency constructor.
     */
    public function __construct()
    {
        $this->aSupportedCurrencies = Factory::property('SupportedCurrencies', 'nails/module-currency');
        $aEnabled                   = Factory::property('EnabledCurrencies', 'nails/module-currency');

        foreach ($aEnabled as $sCode) {
            $this->aEnabledCurrencies[] = $this->getByIsoCode($sCode);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->aSupportedCurrencies;
    }

    // --------------------------------------------------------------------------

    /**
     * @return array
     */
    public function getAllEnabled()
    {
        return $this->aEnabledCurrencies;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a currency by it's ISO 4217 code
     * @param  string $sCode The currency to get
     * @return \stdClass
     * @throws CurrencyException
     */
    public function getByIsoCode($sCode)
    {
        if (array_key_exists($sCode, $this->aSupportedCurrencies)) {
            return $this->aSupportedCurrencies[$sCode];
        } else {
            throw new CurrencyException('"' . $sCode . '" is not a valid currency code.');
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Formats a currency
     * @param  string  $sCode          The currency to get
     * @param  number  $nValue         The value
     * @param  boolean $bIncludeSymbol Include the currency symbol
     * @return string
     */
    public function format($sCode, $nValue, $bIncludeSymbol = true)
    {
        try {

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

        } catch (\Exception $e) {
            return '[CURRENCY FORMATTING FAILED: ' . $e->getMessage() . ']';
        }
    }
}
