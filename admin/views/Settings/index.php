<?php

/** @var \Nails\Common\Service\Input $oInput */
$oInput = \Nails\Factory::service('Input');

?>
<div class="group-currency settings">
    <?php

    echo form_open();
    echo \Nails\Admin\Helper::tabs(array_filter([
        userHasPermission('admin:currency:settings:enabled') ? [
            'label'   => 'Enabled Currencies',
            'content' => function () use ($aSupported, $aSettings) {
                echo form_field_dropdown_multiple([
                    'key'     => 'aEnabledCurrencies[]',
                    'label'   => 'Currencies',
                    'options' => $aSupported,
                    'default' => getFromArray('aEnabledCurrencies', $aSettings, []),
                    'class'   => 'select2',
                    'info'    => 'These currencies will be used by default wherever currencies are used',
                ]);
            },
        ] : null,
        userHasPermission('admin:currency:settings:driver') ? [
            'label'   => 'Currency Driver',
            'content' => function () use ($aSupported, $aSettings) {
                echo Nails\Admin\Helper::loadSettingsDriverTable(
                    'CurrencyDriver',
                    \Nails\Currency\Constants::MODULE_SLUG
                );
            },
        ] : null,
        [
            'label'   => 'Exchange Rates',
            'content' => function () use ($aSupported, $aSettings) {

                if (!empty($aSettings['currency_matrix_updated'])) {
                    ?>
                    <p class="alert alert-info">
                        Exchange rates were last updated: <?=niceTime($aSettings['currency_matrix_updated'])?>
                        (<?=toUserDatetime($aSettings['currency_matrix_updated'])?>)
                    </p>
                    <?php
                }
                if (!empty($aSettings['currency_matrix'])) {
                    ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Currency</th>
                                <?php
                                foreach ($aSettings['currency_matrix'] as $sCurrency => $aMatrix) {
                                    ?>
                                    <th><?=$sCurrency?></th>
                                    <?php
                                }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($aSettings['currency_matrix'] as $sCurrency => $aMatrix) {
                                ?>
                                <tr>
                                    <td><?=$sCurrency?></td>
                                    <?php
                                    foreach ($aMatrix as $sCurrencyChild => $fRate) {
                                        ?>
                                        <td><?=$fRate?></td>
                                        <?php
                                    }
                                    ?>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php
                } else {
                    ?>
                    <p class="alert alert-warning">
                        <strong>No currency exchange data is avaialble.</strong>
                        <br>Update the exchange matrix by executing <code>nails currency:update:exchangerate</code>
                    </p>
                    <?php
                }
            },
        ],
    ]));

    ?>
    <div class="admin-floating-controls">
        <button type="submit" class="btn btn-primary">
            Save Changes
        </button>
    </div>
    <?=form_close()?>
</div>
