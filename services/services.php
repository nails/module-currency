<?php

return array(
    'services' => array(
        'Currency' => function () {
            if (class_exists('\App\Currency\Library\Currency')) {
                return new \App\Currency\Library\Currency();
            } else {
                return new \Nails\Currency\Library\Currency();
            }
        }
    )
);
