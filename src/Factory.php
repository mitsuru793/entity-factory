<?php
declare(strict_types=1);

namespace Yahiru\EntityFactory;

use Yahiru\EntityFactory\Exception\InvalidArgumentException;

final class Factory
{
    /** @var array */
    private static $builders = [];

    /** @var array */
    private static $recipes = [];

    /** @var string */
    private static $locale = 'en_US';

    const DEFAULT = 'default';

    /**
     * @param string $class
     * @param callable $callable
     * @param array|callable $default_recipe
     */
    public static function define(string $class, callable $callable, $default_recipe)
    {
        self::$builders[$class] = $callable;
        self::addRecipe($class, self::DEFAULT, $default_recipe);
    }

    /**
     * @param string $class
     * @param string $name
     * @param callable|array $recipe
     */
    public static function addRecipe(string $class, string $name, $recipe)
    {
        self::$recipes[$class][$name] = $recipe;
    }

    /**
     * @param string $class
     * @return Builder
     */
    public static function of(string $class)
    {
        if (! isset(self::$builders[$class])) {
            throw new InvalidArgumentException($class.' is not defined.');
        }
        return new Builder(self::$builders[$class], self::$recipes[$class], self::getLocale());
    }

    public static function setLocale(string $new_locale)
    {
        self::$locale = $new_locale;
    }

    public static function getLocale(): string
    {
        return self::$locale;
    }
}
