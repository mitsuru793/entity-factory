<?php
declare(strict_types=1);

use \Yahiru\EntityFactory\Factory;

Factory::define(
    'define3',
    function (array $attr) {
        return $attr['name'];
    },
    ['name' => 'define3']
);
