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
        return call_user_func($this->callable_for_make, $this->buildAttributes($attributes));
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
        return $this->recipes([$recipe]);
    }

    /**
     * @param string[] $recipes
     * @return $this
     */
    public function recipes(array $recipes): self
    {
        foreach ($recipes as $recipe) {
            if ( ! isset($this->recipes[$recipe])) {
                throw new InvalidArgumentException($recipe.' is not defined.');
            }
            $this->using_recipes[] = $recipe;
        }

        return $this;
    }
}
