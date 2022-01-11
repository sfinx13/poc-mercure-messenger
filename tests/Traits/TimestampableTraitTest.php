<?php

namespace App\Tests\Traits;

use App\Traits\TimestampableTrait;
use PHPUnit\Framework\TestCase;

class TimestampableTraitTest extends TestCase
{
    public function testCreatedAtIsInstanceOfDateTimeImmutable(): void
    {
        $anonymousClass = new class {
            use TimestampableTrait;
        };

        $anonymousClass->setCreatedAtValue();
        $anonymousClass->setUpdatedAtValue();

        $this->assertTrue($anonymousClass->getCreatedAt() instanceof \DateTimeImmutable);
        $this->assertTrue($anonymousClass->getUpdatedAt() instanceof \DateTimeImmutable);
    }
}
