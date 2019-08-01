<?php

namespace Nails\Currency\Resource;

use Nails\Common\Resource;

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
}
