<?php
namespace Hal\ProtoMock;

/**
 * Class Mock
 * @package Hal\ProtoMock
 */
class Mock
{

    /**
     * flag for exact matching (example : absolute path)
     */
    const MATCHING_EXACT = 1;

    /**
     * flag for exact matching, but insensitive case  (example : absolute path)
     */
    const MATCHING_EXACT_CASE_INSENSITIVE = 2;

    /**
     * flag for regex (example: ^http://mywebsite.fr/user/(\d+) )
     */
    const MATCHING_REGEX = 10;

    /**
     * @var
     */
    private $path;

    /**
     * @var integer
     */
    private $matchingMode;

    /**
     * @var
     */
    private $expectedResponse;

    /**
     * Mock constructor.
     * @param $path
     */
    public function __construct($path, $matchingMoe = self::MATCHING_EXACT)
    {
        $this->path = $path;
        $this->matchingMode = $matchingMoe;
    }

    /**
     * @param $response
     * @return $this
     */
    public function will($response)
    {
        $this->expectedResponse = $response;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        if(is_callable($this->expectedResponse)) {
            return call_user_func($this->expectedResponse, $this->path);
        }
        return $this->expectedResponse;
    }

    /**
     * @param $value
     * @return bool
     */
    public function supports($value)
    {
        switch ($this->matchingMode) {
            case self::MATCHING_EXACT:
                return $this->path == $value;
            case self::MATCHING_EXACT_CASE_INSENSITIVE:
                return strtolower($this->path) == strtolower($value);
            case self::MATCHING_REGEX:
                return false !== preg_match($this->path, $value);
        }
        return false;
    }
}
