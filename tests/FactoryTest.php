<?php
declare(strict_types=1);

namespace Yahiru\EntityFactory\Tests;

use Faker\Generator;
use PHPUnit\Framework\TestCase;
use Yahiru\EntityFactory\Factory;

final class FactoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::define(
            FakeEntity::class,
            function ($attributes) {
                return new FakeEntity($attributes['name']);
            },
            ['name' => 'testing']
        );
    }

    public function testCanBuildGivenValue()
    {
        $entity = Factory::of(FakeEntity::class)->make([
            'name' => 'testing name2',
        ]);
        $this->assertSame('testing name2', $entity->getName());
    }

    public function testCanBuildByRecipe()
    {
        Factory::addRecipe(FakeEntity::class, 'foo', [
            'name' => 'testing name2'
        ]);

        $entity = Factory::of(FakeEntity::class)->recipe('foo')->make();
        $this->assertSame('testing name2', $entity->getName());
    }

    public function testCanBuildWithFaker()
    {
        Factory::addRecipe(FakeEntity::class, 'foo', function (Generator $faker) {
            return [
                'name' => $faker->name
            ];
        });

        $entity = Factory::of(FakeEntity::class)->recipe('foo')->make();
        $this->assertInstanceOf(FakeEntity::class, $entity);
    }

    public function testCanMultipleBuild()
    {
        $entities = Factory::of(FakeEntity::class)->times(2)->recipe('foo')->make();

        $this->assertTrue(is_array($entities));
        $this->assertSame(2, count($entities));
    }
}
