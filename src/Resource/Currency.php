<?php

namespace Nails\Currency\Resource;

use Nails\Common\Resource;
use Nails\Currency\Constants;
use Nails\Factory;

/**
 * Class Currency
 *
 * @package Nails\Currency\Resource
 */
class Currency extends Resource
{
    /**
     * The currency's code
     *
     * @var string
     */
    public $code;

    /**
     * The currency's symbol
     *
     * @var string
     */
    public $symbol;

    /**
     * The currency's symbol's position
     *
     * @var string
     */
    public $symbol_position;

    /**
     * The currency's label
     *
     * @var string
     */
    public $label;

    /**
     * The currency's decimal precision
     *
     * @var int
     */
    public $decimal_precision;

    /**
     * The currency's decimal separator
     *
     * @var string
     */
    public $decimal_symbol;

    /**
     * The currency's thousands separator
     *
     * @var string
     */
    public $thousands_separator;

    // --------------------------------------------------------------------------

    /**
     * Formats an integer as this currency
     *
     * @param int  $iValue                The value to format
     * @param bool $bIncludeSymbolInclude the currency symbol
     *
     * @return string
     */
    public function format(int $iValue, bool $bIncludeSymbol = true): string
    {
        /** @var \Nails\Currency\Service\Currency $oCurrency */
        $oCurrency = Factory::service('Currency', Constants::MODULE_SLUG);
        return $oCurrency->format($this, $iValue / pow(10, $this->decimal_precision), $bIncludeSymbol);
    }
}
