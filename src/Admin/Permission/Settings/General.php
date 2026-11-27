<?php

namespace Nails\Currency\Admin\Permission\Settings;

use Nails\Admin\Interfaces\Permission;

class General implements Permission
{
    public function label(): string
    {
        return 'Can manage Currency settings';
    }

    public function group(): string
    {
        return 'Settings';
    }
}
