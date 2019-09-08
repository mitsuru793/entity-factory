<?php
declare(strict_types=1);

namespace Yahiru\EntityFactory\Tests;

use PHPUnit\Framework\TestCase;

final class FactoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCanBuildDefaultValue()
    {
        $entity = FakeEntityFactory::start()->make();
        $this->assertSame('testing name', $entity->getName());
    }

    public function testCanBuildGivenValue()
    {
        $entity = FakeEntityFactory::start()->make([
            'name' => 'testing name2',
        ]);
        $this->assertSame('testing name2', $entity->getName());
    }

    public function testCanBuildByRecipe()
    {
        $entity = FakeEntityFactory::start()->foo()->make();
        $this->assertSame('foo', $entity->getName());
    }

    public function testCanBuildWithFaker()
    {
        $entity = FakeEntityFactory::start()->randomName()->make();
        $this->assertInstanceOf(FakeEntity::class, $entity);
    }

    public function testCanMultipleBuild()
    {
        $entities = FakeEntityFactory::start()->times(2)->make();

        $this->assertTrue(is_array($entities));
        $this->assertSame(2, count($entities));
    }

    public function testCanIgnoreKey()
    {
        $entity = FakeEntityFactory::start()->make([
            'ignore' => 'testing'
        ]);
        $this->assertNull($entity->getIgnored());
    }

    public function testReturnOriginalCollection()
    {
        $factory = new class extends FakeEntityFactory {
            protected function newCollection(array $entities)
            {
                return new FakeCollection($entities);
            }
        };

        $entities = $factory->times(2)->make();
        $this->assertInstanceOf(FakeCollection::class, $entities);
    }
}
