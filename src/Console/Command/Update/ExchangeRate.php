<?php

namespace Nails\Currency\Console\Command\Update;

use Nails\Console\Command\Base;
use Nails\Currency\Constants;
use Nails\Currency\Service\Driver;
use Nails\Factory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExchangeRate extends Base
{
    /**
     * Configures the command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('currency:update:exchangerate')
            ->setDescription('Updates the system exchange rates using the configured currency driver.');
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the command
     *
     * @param InputInterface  $oInput  The Input Interface provided by Symfony
     * @param OutputInterface $oOutput The Output Interface provided by Symfony
     *
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        /** @var Driver $oDriverService */
        $oDriverService = Factory::service('CurrencyDriver', Constants::MODULE_SLUG);
        $oDriver        = $oDriverService->getEnabled();

        if (empty($oDriver)) {
            $oOutput->writeln('<error>Currency driver is not configured</error>');
            return static::EXIT_CODE_FAILURE;
        }

        dd($oDriver);
    }
}
