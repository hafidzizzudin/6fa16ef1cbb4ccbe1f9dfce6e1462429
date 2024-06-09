<?php

namespace Src\System;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

class DateTime implements ClockInterface
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
