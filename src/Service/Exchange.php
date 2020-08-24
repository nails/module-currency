<?php

/**
 * Provides an interface for handling currency exchange
 *
 * @package     Nails
 * @subpackage  module-currency
 * @category    Service
 * @author      Nails Dev Team
 */

namespace Nails\Currency\Service;

use Nails\Common\Exception\FactoryException;
use Nails\Currency\Constants;
use Nails\Currency\Exception\CurrencyException;
use Nails\Currency\Exception\ExchangeException\DriverNotDefinedException;
use Nails\Currency\Factory\ExchangeMatrix;
use Nails\Factory;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Exchange
 *
 * @package Nails\Currency\Service
 */
class Exchange
{
    /** @var Currency */
    protected $oCurrency;

    // --------------------------------------------------------------------------

    /**
     * Exchange constructor.
     *
     * @param Currency|null $oCurrencyService The currency service to use
     *
     * @throws FactoryException
     */
    public function __construct(Currency $oCurrencyService = null)
    {
        $this->oCurrency = $oCurrencyService ?? Factory::service('Currency', Constants::MODULE_SLUG);
    }

    // --------------------------------------------------------------------------

    /**
     * Exchanges a value from one currency to another
     *
     * @param number                   $nValue        The value to exchange
     * @param Resource\Currency|string $mCurrencyFrom The currency to exchange from
     * @param Resource\Currency|string $mCurrencyTo   The currency to exchange to
     *
     * @throws CurrencyException
     */
    public function exchange($nValue, $mCurrencyFrom, $mCurrencyTo)
    {
        $oCurrencyFrom = $this->oCurrency->inferCurrency($mCurrencyFrom, __METHOD__);
        $oCurrencyTo   = $this->oCurrency->inferCurrency($mCurrencyTo, __METHOD__);
        $fRate         = $this->getRate($oCurrencyFrom, $oCurrencyTo);

        dd($nValue, $oCurrencyFrom, $oCurrencyTo, $fRate);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the exchange rate for a given currency pair
     *
     * @param Resource\Currency|string $mCurrencyFrom The currency to exchange from
     * @param Resource\Currency|string $mCurrencyTo   The currency to exchange to
     *
     * @return float
     * @throws CurrencyException
     */
    public function getRate($mCurrencyFrom, $mCurrencyTo): float
    {
        $oCurrencyFrom = $this->oCurrency->inferCurrency($mCurrencyFrom, __METHOD__);
        $oCurrencyTo   = $this->oCurrency->inferCurrency($mCurrencyTo, __METHOD__);
    }

    // --------------------------------------------------------------------------

    /**
     * Updates the currency matrix
     *
     * @param OutputInterface|null $oOutput
     *
     * @return $this
     * @throws DriverNotDefinedException
     * @throws FactoryException
     */
    public function updateMatrix(OutputInterface $oOutput = null): self
    {
        $LOG = function (string $sMessage) use ($oOutput) {
            if ($oOutput) {
                $oOutput->writeln($sMessage);
            }
        };

        $oDriver     = $this->getDriver();
        $aCurrencies = $this->oCurrency->getAllEnabled();

        /** @var ExchangeMatrix $oMatrix */
        $oMatrix = Factory::factory('ExchangeMatrix', Constants::MODULE_SLUG, $aCurrencies);

        foreach ($oMatrix->getMatrix() as $sFrom => $aTo) {

            $oFrom = $this->oCurrency->getByIsoCode($sFrom);

            foreach ($aTo as $sTo => $fRate) {

                $oTo = $this->oCurrency->getByIsoCode($sTo);

                if ($oFrom === $oTo) {
                    $fRate = 1;
                } else {
                    try {
                        $fRate = $oDriver->getRate($oFrom, $oTo);
                    } catch (\Exception $e) {

                        $LOG(sprintf(
                            '<error>ERROR:</error> Exception caught whilst calculating rate %s -> %s: %s',
                            $sFrom,
                            $sTo,
                            $e->getMessage()
                        ));
                        continue;
                    }
                }

                $LOG(sprintf(
                    'Setting rate %s -> %s to %s',
                    $sFrom,
                    $sTo,
                    $fRate
                ));

                $oMatrix->setRate($oFrom, $oTo, $fRate);
            }
        }

        $LOG('Saving matrix data to app settings');
        setAppSetting('currency_matrix', Constants::MODULE_SLUG, $oMatrix);
        setAppSetting('currency_matrix_updated', Constants::MODULE_SLUG, Factory::factory('DateTime')->format('Y-m-d H:i:s'));

        $LOG('Refreshing app settings');
        appSetting('currency_matrix', Constants::MODULE_SLUG, null, true);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the enabled currency driver
     *
     * @return \Nails\Currency\Interfaces\Driver
     * @throws DriverNotDefinedException
     * @throws FactoryException
     */
    protected function getDriver(): \Nails\Currency\Interfaces\Driver
    {
        /** @var Driver $oDriverService */
        $oDriverService = Factory::service('CurrencyDriver', Constants::MODULE_SLUG);
        $oDriver        = $oDriverService->getEnabled();

        if (empty($oDriver)) {
            throw new DriverNotDefinedException(
                'No currency driver has been defined.'
            );
        }

        return $oDriverService->getInstance($oDriver);
    }
}
