<?php
declare(strict_types=1);

namespace Yahiru\EntityFactory;

use Yahiru\EntityFactory\Exception\InvalidArgumentException;

final class Builder
{
    /** @var callable */
    private $callable_for_make;

    /** @var array */
    private $recipes;

    /** @var array */
    private $using_recipes = [];

    /** @var string */
    private $locale;

    /** @var int */
    private $times = 1;

    public function __construct(callable $callable_for_make, array $recipes, string $locale)
    {
        $this->callable_for_make = $callable_for_make;
        $this->recipes = $recipes;
        $this->locale = $locale;

        $this->recipe(Factory::DEFAULT);
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function make(array $attributes = [])
    {
        if ($this->times === 1) {
            return call_user_func($this->callable_for_make, $this->buildAttributes($attributes));
        }

        $entities = [];
        $times = $this->times;
        while ($times--) {
            $entities[] = call_user_func($this->callable_for_make, $this->buildAttributes($attributes));
        }

        return $entities;
    }

    /**
     * @param int $times
     * @return Builder
     */
    public function times(int $times): self
    {
        if ($times < 1) {
            throw new InvalidArgumentException('times must be positive number. but given '.$times);
        }
        $this->times = $times;

        return $this;
    }

    /**
     * @param array $attributes
     * @return array
     */
    private function buildAttributes(array $attributes): array
    {
        $built = [];
        foreach ($this->using_recipes as $recipe) {
            $built = array_merge($built, $this->toAttributes($this->recipes[$recipe]));
        }

        return array_merge($built, $attributes);
    }

    /**
     * @param array|callable $recipe
     * @return array
     */
    private function toAttributes($recipe): array
    {
        if (is_array($recipe)) {
            return $recipe;
        }

        if (is_callable($recipe)) {
            return $recipe(\Faker\Factory::create($this->locale));
        }

        throw new InvalidArgumentException('recipe must be array or callable.');
    }

    /**
     * @param string $recipe
     * @return $this
     */
    public function recipe(string $recipe): self
    {
        if ( ! isset($this->recipes[$recipe])) {
            throw new InvalidArgumentException($recipe.' is not defined.');
        }
        $this->using_recipes[] = $recipe;

        return $this;
    }

    /**
     * @param string[] $recipes
     * @return $this
     */
    public function recipes(array $recipes): self
    {
        foreach ($recipes as $recipe) {
            $this->recipe($recipe);
        }

        return $this;
    }
}
