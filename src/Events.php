<?php

/**
 * The class provides a summary of the events fired by this module
 *
 * @package     Nails
 * @subpackage  module-currency
 * @category    Events
 * @author      Nails Dev Team
 */

namespace Nails\Currency;

use Nails\Common\Events\Base;
use Nails\Currency\Factory\ExchangeMatrix;

/**
 * Class Events
 *
 * @package Nails\Currency
 */
class Events extends Base
{
    /**
     * Fired when the exchange matrix is updated
     *
     * @param ExchangeMatrix $oMatrix The updated exchange matrix
     */
    const EXCHANGE_MATRIX_UPDATED = 'EXCHANGE:MATRIX:UPDATED';
}
