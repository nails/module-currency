<?php

namespace Nails\Currency\Settings;

use Nails\Common\Helper\Form;
use Nails\Common\Interfaces;
use Nails\Common\Service\FormValidation;
use Nails\Components\Setting;
use Nails\Currency\Constants;
use Nails\Currency\Service\Currency;
use Nails\Currency\Service\Driver;
use Nails\Currency\Service\Exchange;
use Nails\Factory;

/**
 * Class General
 *
 * @package Nails\Currency\Settings
 */
class General implements Interfaces\Component\Settings
{
    const KEY_ENABLED_CURRENCIES = 'aEnabledCurrencies';
    const KEY_MATRIX             = 'currency_matrix';
    const KEY_MATRIX_UPDATED     = 'currency_matrix_updated';

    // --------------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return 'Currency';
    }

    // --------------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function get(): array
    {
        /** @var Currency $oCurrency */
        $oCurrency = Factory::service('Currency', Constants::MODULE_SLUG);
        /** @var Driver $oDriverService */
        $oDriverService = Factory::service('CurrencyDriver', Constants::MODULE_SLUG);

        /** @var Setting $oFieldEnabledCurrencies */
        $oFieldEnabledCurrencies = Factory::factory('ComponentSetting');
        $oFieldEnabledCurrencies
            ->setKey(static::KEY_ENABLED_CURRENCIES . '[]')
            ->setType(Form::FIELD_DROPDOWN_MULTIPLE)
            ->setLabel('Currencies')
            ->setInfo('These currencies will be used by default wherever currencies are used')
            ->setClass('select2')
            ->setOptions($oCurrency->getAllFlat())
            ->setFieldset('Enabled Currencies')
            ->setValidation([
                FormValidation::RULE_REQUIRED,
            ]);

        /** @var Setting $oDriver */
        $oDriver = Factory::factory('ComponentSetting');
        $oDriver
            ->setKey($oDriverService->getSettingKey())
            ->setType($oDriverService->isMultiple()
                ? Form::FIELD_DROPDOWN_MULTIPLE
                : Form::FIELD_DROPDOWN
            )
            ->setLabel('Driver')
            ->setFieldset('Driver')
            ->setClass('select2')
            ->setOptions(['' => 'No Driver Selected'] + $oDriverService->getAllFlat())
            ->setValidation([
                FormValidation::RULE_REQUIRED,
            ]);

        /** @var Setting $oExchangeRateMatrix */
        $oExchangeRateMatrix = Factory::factory('ComponentSetting');
        $oExchangeRateMatrix
            ->setKey(static::KEY_MATRIX)
            ->setType(Form::FIELD_TEXTAREA)
            ->setLabel('Rate Matrix')
            ->setInfo('This is the matrix used when calculating currency conversions. It is updated automatically, or by using the <code>nails currency:update:exchangerate</code> command.')
            ->setInfoClass('alert alert-warning')
            ->setFieldset('Exchange Rates')
            ->setRenderFormatter(function ($mValue) {
                return json_encode($mValue, JSON_PRETTY_PRINT);
            })
            ->setIsReadOnly(true);

        /** @var Setting $oExchangeRateUpdated */
        $oExchangeRateUpdated = Factory::factory('ComponentSetting');
        $oExchangeRateUpdated
            ->setKey(static::KEY_MATRIX_UPDATED)
            ->setLabel('Last Updated')
            ->setFieldset('Exchange Rates')
            ->setIsReadOnly(true);

        return [
            $oFieldEnabledCurrencies,
            $oDriver,
            $oExchangeRateMatrix,
            $oExchangeRateUpdated,
        ];
    }
}
