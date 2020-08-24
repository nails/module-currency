<?php

/**
 * This service manages the Currency drivers
 *
 * @package     Nails
 * @subpackage  module-currency-code
 * @category    Service
 * @author      Nails Dev Team
 */

namespace Nails\Currency\Service;

use Nails\Currency\Constants;
use Nails\Common\Model\BaseDriver;

/**
 * Class Driver
 *
 * @package Nails\Currency\Service
 */
class Driver extends BaseDriver
{
    protected $sModule         = Constants::MODULE_SLUG;
    protected $sType           = 'currency';
    protected $bEnableMultiple = false;
}
