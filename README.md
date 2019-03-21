
## Installation

```bash
composer require yahiru/entity-generator
```

## Basic Usage

```php
<?php

require '/path/to/autoload.php';

use Yahiru\EntityFactory\Factory;

Factory::define(
    User::class,
    // define how to generate
    function (array $attributes) {
        return new User(
            $attributes['name'],
            $attributes['email']
        );
    },
    // define default values
    ['name' => 'foo', 'email' => 'bar@domain.com']
);

$user = Factory::of(User::class)->make();
```

## Using Recipe

```php
<?php

Factory::addRecipe(Uer::class, 'foo', [
    'name' => 'foo'
]);

$user = Factory::of(User::class)->recipe('foo')->make();
echo $user->name; // foo
```

## With Faker

```php
<?php

Factory::setLocale('ja_JP'); // default 'en_US'

Factory::define(
    User::class,
    function (array $attributes) {
        return new User(
            $attributes['name'],
            $attributes['email']
        );
    },
    function (\Faker\Generator $faker) {
        return [
            'name' => $faker->name,
            'email' => $faker->safeEmail,
        ];
    }
);

Factory::addRecipe(User::class, 'foo', function (\Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->safeEmail,
    ];
});
```
