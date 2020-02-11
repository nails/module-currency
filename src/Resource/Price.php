<?php

namespace Nails\Currency\Resource;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Resource;
use Nails\Currency\Constants;
use Nails\Currency\Exception\CurrencyException;
use Nails\Factory;

/**
 * Class Price
 *
 * @package Nails\Currency\Resource
 */
class Price extends Resource
{
    /** @var int */
    public $price;

    /** @var Currency */
    public $currency;

    /** @var string */
    public $formatted;

    // --------------------------------------------------------------------------

    /**
     * Price constructor.
     *
     * @param array $mObj
     *
     * @throws FactoryException
     * @throws CurrencyException
     */
    public function __construct($mObj = [])
    {
        parent::__construct($mObj);

        /** @var \Nails\Currency\Service\Currency $oCurrency */
        $oCurrency = Factory::service('Currency', Constants::MODULE_SLUG);

        //  @todo (Pablo - 2020-02-11) - Remove the `pow()` when `format()` accepts an integer
        $this->formatted = $oCurrency->format(
            $this->currency,
            $this->price / pow(10, $this->currency->decimal_precision)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Converts the price object to a string when cast
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->price;
    }
}
