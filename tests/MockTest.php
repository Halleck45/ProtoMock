<?php
namespace Tests;

use Hal\ProtoMock\Mock;
use Hal\ProtoMock\ProtoMock;

class MockTest extends \PHPUnit_Framework_TestCase {

    public function testMockIsAccessible()
    {
        $mocked = new Mock('file1');
        $mocked->will('my');
        $this->assertEquals('my', $mocked->getContent());
    }

}