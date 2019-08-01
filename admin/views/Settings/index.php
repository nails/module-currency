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
                    'info'    => 'These currencies will be used by default wherver currencies are used',
                ]);
            },
        ] : null,
    ]));

    ?>
    <div class="admin-floating-controls">
        <button type="submit" class="btn btn-primary">
            Save Changes
        </button>
    </div>
    <?=form_close()?>
</div>
