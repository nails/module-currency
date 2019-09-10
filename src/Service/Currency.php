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

use Nails\Currency\Constants;
use Nails\Currency\Exception\CurrencyException;
use Nails\Currency\Resource;
use Nails\Factory;

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
     * All currencies supported by this system as a flat array
     *
     * @var string[]
     */
    protected $aSupportedCurrenciesFlat = [];

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
        $aSupported = Factory::property('SupportedCurrencies', Constants::MODULE_SLUG);
        foreach ($aSupported as $oCurrency) {
            $this->aSupportedCurrencies[$oCurrency->code]     = new Resource\Currency($oCurrency);
            $this->aSupportedCurrenciesFlat[$oCurrency->code] = $oCurrency->code . ' (' . $oCurrency->label . ')';
        }

        $aEnabled = appSetting('aEnabledCurrencies', Constants::MODULE_SLUG) ?? [];
        foreach ($aEnabled as $sCode) {
            $this->aEnabledCurrencies[$sCode] = $this->getByIsoCode($sCode);
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
     * Returns suppoted currencies as a flat array
     *
     * @return string[]
     */
    public function getAllFlat(): array
    {
        return $this->aSupportedCurrenciesFlat;
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
     * Determines whether a currency is a supported currency
     *
     * @param string|Resource\Currency $mCurrency The currency code or a currency resource
     *
     * @return bool
     * @throws CurrencyException
     */
    public function isSupported($mCurrency): bool
    {
        if ($mCurrency instanceof Resource\Currency) {
            return array_key_exists($mCurrency->code, $this->aSupportedCurrencies);
        } elseif (is_string($mCurrency)) {
            return array_key_exists($mCurrency, $this->aSupportedCurrencies);
        }

        throw new CurrencyException(
            'Argument must be an instant of ' . Resource\Currency::class . ' or a string'
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Determines whether a currency is an enabled currency
     *
     * @param string|Resource\Currency $mCurrency The currency code or a currency resource
     *
     * @return bool
     * @throws CurrencyException
     */
    public function isEnabled($mCurrency): bool
    {
        if ($mCurrency instanceof Resource\Currency) {
            return array_key_exists($mCurrency->code, $this->aEnabledCurrencies);
        } elseif (is_string($mCurrency)) {
            return array_key_exists($mCurrency, $this->aEnabledCurrencies);
        }

        throw new CurrencyException(
            'Argument must be an instant of ' . Resource\Currency::class . ' or a string'
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Formats a currency
     *
     * @param string|Resource\Currency $mCurrency      The currency code or a currency resource
     * @param number                   $nValue         The value
     * @param bool                     $bIncludeSymbol Include the currency symbol
     *
     * @return string
     * @throws CurrencyException
     */
    public function format($mCurrency, $nValue, bool $bIncludeSymbol = true)
    {
        if ($mCurrency instanceof Resource\Currency) {
            $oCurrency = $mCurrency;
        } elseif (is_string($mCurrency)) {
            $oCurrency = $this->getByIsoCode($mCurrency);
        }

        $sOut = number_format(
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
