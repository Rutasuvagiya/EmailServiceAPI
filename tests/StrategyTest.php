<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\ProviderManager;
use App\Strategy\RoundRobinStrategy;
use App\Template\EmailTemplateManager;

class StrategyTest extends TestCase
{
    public function testEmptyProviderList()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Array cannot be empty.');
        $strategy = new RoundRobinStrategy(array());
    }
}
