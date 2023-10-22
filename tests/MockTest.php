<?php
namespace Tests;

use Hal\ProtoMock\Mock;
use PHPUnit\Framework\TestCase;

class MockTest extends TestCase
{
    public function testMockIsAccessible()
    {
        $mocked = new Mock('file1');
        $mocked->will('my');
        $this->assertEquals('my', $mocked->getContent());
    }
}
