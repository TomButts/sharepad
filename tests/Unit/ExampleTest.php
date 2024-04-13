<?php

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function testTheSuiteIsRunning(): void
    {
        $math = 2 + 2;

        $this->assertEquals(4, $math);
    }
}
