<?php

namespace App\Tests\Traits;

use App\Traits\ActivableTrait;
use PHPUnit\Framework\TestCase;

class ActivableTraitTest extends TestCase
{
    public function testGetterAndSetterTrait()
    {
        $anonymousClass = new class {
            use ActivableTrait;
        };

        $anonymousClass->setActive(true);

        $this->assertTrue($anonymousClass->isActive(), true);

    }
}