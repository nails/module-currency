<?php

/**
 * The ExchangeRate Cron task
 *
 * @package  Nails\Currency
 * @category Task
 */

namespace Nails\Currency\Cron\Task\Update;

use Nails\Cron\Task\Base;

/**
 * Class ExchangeRate
 *
 * @package Nails\Currency\Cron\Task\Trash
 */
class ExchangeRate extends Base
{
    /**
     * The cron expression of when to run
     *
     * @var string
     */
    const CRON_EXPRESSION = '5 * * * *';

    /**
     * The console command to execute
     *
     * @var string
     */
    const CONSOLE_COMMAND = 'currency:update:exchangerate';
}
