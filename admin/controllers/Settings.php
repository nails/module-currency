<?php

/**
 * This class registers some handlers for Currency settings
 *
 * @package     Nails
 * @subpackage  module-currency
 * @category    AdminController
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Admin\Currency;

use Nails\Admin\Controller\Base;
use Nails\Admin\Helper;
use Nails\Common\Exception\NailsException;
use Nails\Common\Service\AppSetting;
use Nails\Common\Service\Database;
use Nails\Common\Service\FormValidation;
use Nails\Common\Service\Input;
use Nails\Commont\Service\Session;
use Nails\Currency\Constants;
use Nails\Currency\Service\Currency;
use Nails\Factory;

/**
 * Class Settings
 *
 * @package Nails\Admin\Currency
 */
class Settings extends Base
{
    /**
     * Announces this controller's navGroups
     *
     * @return stdClass
     */
    public static function announce()
    {
        $oNavGroup = Factory::factory('Nav', 'nails/module-admin');
        $oNavGroup->setLabel('Settings');
        $oNavGroup->setIcon('fa-wrench');

        if (userHasPermission('admin:currency:settings:*')) {
            $oNavGroup->addAction('Currency');
        }

        return $oNavGroup;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an array of permissions which can be configured for the user
     *
     * @return array
     */
    public static function permissions(): array
    {
        $aPermissions = parent::permissions();

        $aPermissions['enabled'] = 'Can change the enabled currencies';
        $aPermissions['driver']  = 'Can update driver settings';

        return $aPermissions;
    }

    // --------------------------------------------------------------------------

    /**
     * Manage invoice settings
     *
     * @return void
     */
    public function index()
    {
        if (!userHasPermission('admin:currency:settings:*')) {
            unauthorised();
        }

        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        /** @var Database $oDb */
        $oDb = Factory::service('Database');
        /** @var AppSetting $oAppSettingService */
        $oAppSettingService = Factory::service('AppSetting');
        /** @var Currency $oCurrency */
        $oCurrency = Factory::service('Currency', Constants::MODULE_SLUG);

        if ($oInput->post()) {

            try {

                /** @var FormValidation $oFormValidation */
                $oFormValidation = Factory::service('FormValidation');
                $oFormValidation
                    ->buildValidator([
                        'aEnabledCurrencies[]'    => ['required'],
                        'enabled_driver_currency' => ['required'],
                    ])
                    ->run();

                $aSettings = [
                    'aEnabledCurrencies'      => array_filter((array) $oInput->post('aEnabledCurrencies')),
                    'enabled_driver_currency' => $oInput->post('enabled_driver_currency'),
                ];

                if (!$oAppSettingService->set($aSettings, Constants::MODULE_SLUG)) {
                    throw new NailsException($oAppSettingService->lastError(), 1);
                }

                /** @var Session $oSession */
                $oSession = Factory::service('Session');
                $oSession->setFlashData('success', 'Currency settings were saved.');
                redirect('admin/currency/settings');

            } catch (\Exception $e) {
                $this->data['error'] = $e->getMessage();
            }
        }

        // --------------------------------------------------------------------------

        //  Get data
        $this->data['aSupported'] = $oCurrency->getAllFlat();
        $this->data['aSettings']  = appSetting(null, Constants::MODULE_SLUG, null, true);

        Helper::loadView('index');
    }
}
