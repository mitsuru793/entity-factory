<?php
declare(strict_types=1);

namespace Yahiru\EntityFactory\Tests;

class FakeCollection
{
    /** @var array */
    protected $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }
}
