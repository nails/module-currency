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
use Nails\Common\Service\Event;
use Nails\Currency\Constants;
use Nails\Currency\Events;
use Nails\Currency\Exception\CurrencyException;
use Nails\Currency\Exception\ExchangeException\DriverNotDefinedException;
use Nails\Currency\Exception\ExchangeException\MatrixException;
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
    /** @var string */
    const SETTING_MATRIX = 'currency_matrix';

    /** @var string */
    const SETTING_MATRIX_UPDATED = 'currency_matrix_updated';

    // --------------------------------------------------------------------------

    /** @var Currency */
    protected $oCurrency;

    /** @var ExchangeMatrix */
    protected $oMatrix;

    // --------------------------------------------------------------------------

    /**
     * Exchange constructor.
     *
     * @param Currency|null       $oCurrencyService The currency service to use
     * @param ExchangeMatrix|null $oExchangeMatrix  The exchange matrix to use
     *
     * @throws FactoryException
     */
    public function __construct(
        Currency $oCurrencyService = null,
        ExchangeMatrix $oExchangeMatrix = null
    ) {
        $this->oCurrency = $oCurrencyService ?? Factory::service('Currency', Constants::MODULE_SLUG);
        $this->oMatrix   = $oExchangeMatrix ?? Factory::factory(
                'ExchangeMatrix',
                Constants::MODULE_SLUG,
                $this->oCurrency->getAllEnabled(),
                appSetting(static::SETTING_MATRIX, Constants::MODULE_SLUG)
            );
    }

    // --------------------------------------------------------------------------

    /**
     * Exchanges a value from one currency to another
     *
     * @param int                      $iValue        The value to exchange (in smallest unit)
     * @param Resource\Currency|string $mCurrencyFrom The currency to exchange from
     * @param Resource\Currency|string $mCurrencyTo   The currency to exchange to
     *
     * @throws CurrencyException
     */
    public function exchange($iValue, $mCurrencyFrom, $mCurrencyTo, bool $bRound = true)
    {
        $oCurrencyFrom = $this->oCurrency->inferCurrency($mCurrencyFrom, __METHOD__);
        $oCurrencyTo   = $this->oCurrency->inferCurrency($mCurrencyTo, __METHOD__);
        $fRate         = $this->getRate($oCurrencyFrom, $oCurrencyTo);

        return $bRound ? round($iValue * $fRate) : $iValue * $fRate;
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

        return $this->oMatrix->getRate($oCurrencyFrom, $oCurrencyTo);
    }

    // --------------------------------------------------------------------------

    /**
     * Updates the currency matrix
     *
     * @param OutputInterface|null $oOutput An output interface to log to
     *
     * @return $this
     * @throws CurrencyException
     * @throws DriverNotDefinedException
     * @throws FactoryException
     * @throws MatrixException
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

        $this->oMatrix = $oMatrix;

        $LOG('Saving matrix data to app settings');
        setAppSetting(static::SETTING_MATRIX, Constants::MODULE_SLUG, $oMatrix);
        setAppSetting(static::SETTING_MATRIX_UPDATED, Constants::MODULE_SLUG, Factory::factory('DateTime')->format('Y-m-d H:i:s'));

        $LOG('Refreshing app settings');
        appSetting(static::SETTING_MATRIX, Constants::MODULE_SLUG, null, true);

        /** @var Event $oEventService */
        $oEventService = Factory::service('Event');
        $oEventService->trigger(Events::EXCHANGE_MATRIX_UPDATED, Events::getEventNamespace(), [$oMatrix]);

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
