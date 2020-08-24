<?php

namespace Nails\Currency\Factory;

use Nails\Currency\Exception\ExchangeException\MatrixException;
use Nails\Currency\Resource\Currency;

/**
 * Class ExchangeMatrix
 *
 * @package Nails\Currency\Factory
 */
class ExchangeMatrix implements \JsonSerializable
{
    /** @var Currency[] */
    protected $aCurrencies = [];

    /** @var \stdClass */
    protected $oMatrix;

    // --------------------------------------------------------------------------

    /**
     * ExchangeMatrix constructor.
     *
     * @param array          $aCurrencies The curencies for the matrix
     * @param \stdClass|null $oMatrix     A preconfigured matrix
     */
    public function __construct(array $aCurrencies = [], \stdClass $oMatrix = null)
    {
        $this->aCurrencies = $aCurrencies;

        if (empty($oMatrix)) {
            $this->oMatrix = (object) [];
            foreach ($aCurrencies as $oCurrency) {
                $this->oMatrix->{$oCurrency->code} = (object) [];
                foreach ($aCurrencies as $oCurrencyChild) {
                    $this->oMatrix->{$oCurrency->code}->{$oCurrencyChild->code} = null;
                }
            }
        } else {
            $this->oMatrix = $oMatrix;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the matrix
     *
     * @return \stdClass
     */
    public function getMatrix(): \stdClass
    {
        return $this->oMatrix;
    }

    // --------------------------------------------------------------------------

    /**
     * The data for JSON serialisation
     *
     * @return \stdClass
     */
    public function jsonSerialize()
    {
        return $this->getMatrix();
    }

    // --------------------------------------------------------------------------

    /**
     * Sets an exchange rate between two currencies
     *
     * @param Currency $oFrom The currency to exchange from
     * @param Currency $oTo   The currency to exchange to
     * @param float    $fRate The rate to use
     *
     * @return $this
     * @throws MatrixException
     */
    public function setRate(Currency $oFrom, Currency $oTo, float $fRate): self
    {
        $this->testCurrencyPairExisitInMatrix($oFrom, $oTo);

        $this->oMatrix->{$oFrom->code}->{$oTo->code} = $fRate;

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the exchange rate between two currencies
     *
     * @param Currency $oFrom The currency to exchange from
     * @param Currency $oTo   The currency to exchange to
     *
     * @return float
     * @throws MatrixException
     */
    public function getRate(Currency $oFrom, Currency $oTo): float
    {
        $this->testCurrencyPairExisitInMatrix($oFrom, $oTo);

        return $this->oMatrix->{$oFrom->code}->{$oTo->code};
    }

    // --------------------------------------------------------------------------

    /**
     * Tests a currency pair exists in the matrix
     *
     * @param Currency $oFrom The currency to exchange from
     * @param Currency $oTo   The currency to exchange to
     *
     * @throws MatrixException
     */
    protected function testCurrencyPairExisitInMatrix(Currency $oFrom, Currency $oTo): void
    {
        if (!property_exists($this->oMatrix, $oFrom->code)) {
            throw new MatrixException(
                sprintf(
                    'Currency "%s" is not supported in this exchange matrix',
                    $oFrom->code
                )
            );

        } elseif (!property_exists($this->oMatrix->{$oFrom->code}, $oTo->code)) {
            throw new MatrixException(
                sprintf(
                    'Currency "%s" is not supported in this exchange matrix',
                    $oTo->code
                )
            );
        }
    }
}
