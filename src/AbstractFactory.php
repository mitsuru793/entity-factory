<?php
declare(strict_types=1);

namespace Yahiru\EntityFactory;

use Faker\Generator as Faker;
use OutOfBoundsException;
use ReflectionClass;
use Yahiru\EntityFactory\Exception\InvalidRecipeException;

abstract class AbstractFactory
{
    protected $locale = 'en_US';
    /**
     * these keys are ignored when make an entity.
     * @var array
     */
    protected $ignoredKeys = [];
    private $times = 1;
    private $recipes = [];

    /**
     * @param mixed ...$args
     * @return static
     */
    public static function start(...$args)
    {
        return new static(...$args);
    }

    final public function times(int $times): self
    {
        $this->setTimes($times);
        return $this;
    }

    final protected function setTimes(int $times): void
    {
        if ($times < 1) {
            throw new OutOfBoundsException('times must be positive number. but given ' . $times);
        }

        $this->times = $times;
    }

    final public function store(array $attributes = [])
    {
        $entities = $this->makeEntities($attributes);

        if (!$this->shouldReturnMultiple()) {
            $this->persistEntity($entities[0]);
            return $entities[0];
        }

        foreach ($entities as $entity) {
            $this->persistEntity($entity);
        }

        return $this->newCollection($entities);
    }

    private function makeEntities(array $attributes): array
    {
        $faker = $this->getFaker();
        $entities = [];
        $times = $this->times;
        while ($times--) {
            $entities[] = $this->makeEntity(
                $this->buildAttributes($faker, $attributes)
            );
        }

        return $entities;
    }

    protected function getFaker(): Faker
    {
        return \Faker\Factory::create($this->locale);
    }

    /**
     * @param array $attributes
     * @return mixed
     * @throws \ReflectionException
     */
    protected function makeEntity(array $attributes)
    {
        $ref = new ReflectionClass($this->class());
        $instance = $ref->newInstanceWithoutConstructor();

        foreach ($attributes as $key => $attribute) {
            if ($this->hasIgnoredKey($key)) {
                continue;
            }
            $property = $ref->getProperty($key);

            $property->setAccessible(true);
            $property->setValue($instance, $attribute);
        }

        return $instance;
    }

    abstract protected function class(): string;

    private function hasIgnoredKey(string $key): bool
    {
        return in_array($key, $this->ignoredKeys, true);
    }

    private function buildAttributes(Faker $faker, array $attributes): array
    {
        $built = $this->default($faker);
        foreach ($this->recipes as $recipe) {
            $built = array_merge($built, $this->toAttributes($faker, $recipe));
        }

        return array_merge($built, $attributes);
    }

    abstract public function default(Faker $faker): array;

    /**
     * @param Faker $faker
     * @param array|callable $recipe
     * @return array
     * @throws InvalidRecipeException
     */
    private function toAttributes(Faker $faker, $recipe): array
    {
        if (is_array($recipe)) {
            return $recipe;
        }

        if (is_callable($recipe)) {
            return $recipe($faker);
        }

        throw new InvalidRecipeException('recipe must be array or callable.');
    }

    final protected function shouldReturnMultiple(): bool
    {
        return $this->times !== 1;
    }

    protected function persistEntity($entity): void
    {
        // should implements if you want to persist an entity.
    }

    protected function newCollection(array $entities)
    {
        return $entities;
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    final public function make(array $attributes = [])
    {
        $entities = $this->makeEntities($attributes);
        return $this->shouldReturnMultiple()
            ? $this->newCollection($entities)
            : $entities[0];
    }

    final public function attributes(array $attributes = []): array
    {
        return $this->buildAttributes($this->getFaker(), $attributes);
    }

    /**
     * @param array|callable $attribute
     */
    final protected function addRecipe($attribute): void
    {
        if (!is_array($attribute) && !is_callable($attribute)) {
            throw new InvalidRecipeException('recipe must be array or callable.');
        }

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
        $this->recipes[$backtrace[1]['function']] = $attribute;
    }
}
