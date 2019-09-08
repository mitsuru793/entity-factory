<?php
declare(strict_types=1);

namespace Yahiru\EntityFactory\Tests;

final class FakeEntity
{
    /** @var string */
    private $name;
    /** @var null|mixed */
    private $ignored = null;

    /**
     * FakeEntity constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getIgnored()
    {
        return $this->ignored;
    }
}
