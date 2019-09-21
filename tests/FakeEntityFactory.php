<?php
declare(strict_types=1);

namespace Yahiru\EntityFactory\Tests;

use Faker\Generator as Faker;
use Yahiru\EntityFactory\AbstractFactory;

/**
 * @method FakeEntity|FakeEntity[] make(array $attributes = [])
 */
class FakeEntityFactory extends AbstractFactory
{
    protected function class(): string
    {
        return FakeEntity::class;
    }

    public function default(Faker $faker): array
    {
        return [
            'name' => 'testing name'
        ];
    }

    public function foo(): self
    {
        $this->addRecipe([
            'name' => 'foo',
        ]);
        return $this;
    }

    public function uniqueNumber(): self
    {
        $this->addRecipe(function (Faker $faker) {
            return [
                'name' => $faker->unique()->randomDigit,
            ];
        });
        return $this;
    }

    public function randomName(): self
    {
        $this->addRecipe(function (Faker $faker) {
            return ['name' => $faker->name];
        });
        return $this;
    }
}
