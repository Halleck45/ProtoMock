<?php
namespace Hal\ProtoMock;

/**
 * Class Mock
 * @package Hal\ProtoMock
 */
class Mock {

    /**
     * @var
     */
    private $path;

    /**
     * @var
     */
    private $expectedResponse;

    /**
     * Mock constructor.
     * @param $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @param $response
     */
    public function will($response)
    {
        $this->expectedResponse = $response;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->expectedResponse;
    }
}
