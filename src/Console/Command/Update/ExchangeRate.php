<?php

namespace Nails\Currency\Console\Command\Update;

use Nails\Console\Command\Base;
use Nails\Currency\Constants;
use Nails\Currency\Exception\ExchangeException\DriverNotDefinedException;
use Nails\Currency\Service\Exchange;
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
        try {

            $oOutput->writeln('<comment>Updating currency matrix</comment>...');

            /** @var Exchange $oExchangeService */
            $oExchangeService = Factory::service('Exchange', Constants::MODULE_SLUG);

            try {
                $oExchangeService->updateMatrix($oOutput);
            } catch (DriverNotDefinedException $e) {
                /**
                 * Output a warning rather than allow the exception to bubble as
                 * the app might not actually need exchange functionality and we
                 * don't want this to be overly verbose... but we do want someone
                 * is debugging why exchange rates aren't being updated to be
                 * able to see why its not being updated easily.
                 */
                $oOutput->writeln('<error>Currency driver has not been defined</error>');
            }

            $oOutput->writeln('<comment>Currency matrix updated</comment>');
            return static::EXIT_CODE_SUCCESS;

        } catch (\Exception $e) {
            $oOutput->writeln('<error>An error occurred: ' . $e->getMessage() . '</error>');
            return static::EXIT_CODE_FAILURE;
        }
    }
}
