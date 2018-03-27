<?php
namespace Hal\ProtoMock;

/**
 * Class Mock
 * @package Hal\ProtoMock
 */
/**
 * Class Mocks
 * @package Hal\ProtoMock
 */
class Mocks
{

    /**
     * @var Mock[]
     */
    protected $mocks;

    /**
     * Mocks constructor.
     */
    public function __construct()
    {
        $this->mocks = new \SplObjectStorage();
    }

    /**
     * @param Mock $mock
     * @return $this
     */
    public function attach(Mock $mock)
    {
        $this->mocks->attach($mock);
        return $this;
    }

    /**
     * @param Mock $mock
     * @return $this
     */
    public function detach(Mock $mock)
    {
        $this->mocks->detach($mock);
        return $this;
    }

    /**
     * @param $path
     * @return Mock|null
     */
    public function get($path)
    {
        foreach ($this->mocks as $mock) {
            if ($mock->supports($path)) {
                return $mock;
            }
        }
        return null;
    }

    /**
     * @param $path
     * @return bool
     */
    public function supports($path)
    {
        foreach ($this->mocks as $mock) {
            if ($mock->supports($path)) {
                return true;
            }
        }
        return false;
    }
}
