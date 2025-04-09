<?php

namespace vendor\CommonFunction\Tests;

use PHPUnit\Framework\TestCase;
use vendor\CommonFunction\Common;

class CommonTest extends TestCase
{
    public function testgetTime()
    {
        $time = Common::getTime();
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $time);
    }
}