<?php
declare(strict_types=1);

namespace Yahiru\EntityFactory\Tests;

use Faker\Generator;
use OverflowException;
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

    public function testRecursiveFileLoad()
    {
        Factory::load(__DIR__.'/defines');

        $this->assertSame('define1', Factory::of('define1')->make());
        $this->assertSame('define2', Factory::of('define2')->make());
        $this->assertSame('define3', Factory::of('define3')->make());
    }


    public function testGenerateUniqueValue()
    {
        Factory::define(
            'Number',
            function (array $attr) {
                return $attr['num'];
            },
            function (\Faker\Generator $faker): array {
                return [
                    'num' => $faker->unique->randomDigit,
                ];
            }
        );

        $nums = Factory::of('Number')->times(10)->make();
        $digits = range(0, 9);
        foreach ($nums as $num) {
            assert(array_key_exists($num, $digits));
            unset($digits[$num]);
        }
        $this->assertEmpty($digits);

        $this->expectException(OverflowException::class);
        Factory::of('Number')->times(11)->make();
    }
}
