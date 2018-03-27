<?php

namespace Hal\ProtoMock;

/**
 * Class ProtoMock
 * @package Hal\ProtoMock
 */
class ProtoMock
{

    /**
     * @var array
     */
    protected static $protocols = [];

    /**
     * @var resource
     */
    protected $handler;

    /**
     * @var array
     */
    protected static $mockedResponses = [];

    /**
     * @var string
     */
    protected $tempFile;


    /**
     * @param $protocol
     * @return $this
     */
    public function enable($protocol)
    {
        static::$protocols[] = $protocol;
        stream_wrapper_unregister($protocol);
        stream_wrapper_register($protocol, static::class);
        return $this;
    }

    /**
     * @return $this
     */
    public function disable($protocol)
    {
        $k = array_search($protocol, static::$protocols);
        if(false === $k) {
            return $this;
        }

        stream_wrapper_unregister($protocol);
        stream_wrapper_restore($protocol);
        unset(static::$protocols[$k]);
        return $this;
    }

    /**
     * @return $this
     */
    protected function restoreDefaults() {
        foreach(static::$protocols as $protocol) {
            stream_wrapper_unregister($protocol);
            stream_wrapper_restore($protocol);
        }
        return $this;
    }

    /**
     * @return $this
     */
    protected function restoreCustoms() {
        foreach(static::$protocols as $protocol) {
            stream_wrapper_unregister($protocol);
            stream_wrapper_register($protocol, static::class);
        }
        return $this;
    }

    /**
     * @param $url
     * @return mixed
     */
    public function with($url)
    {
        static::$mockedResponses[$url] = new Mock($url);
        return static::$mockedResponses[$url];
    }

    /**
     * @param $url
     * @return $this
     */
    public function without($url)
    {
        if(isset(static::$mockedResponses[$url])) {
            unset(static::$mockedResponses[$url]);
        }
        return $this;
    }

    /**
     * @param $path
     * @param string $mode
     * @return resource
     */
    public function stream_open($path, $mode = 'r')
    {

        $this->restoreDefaults();

        if (isset(static::$mockedResponses[$path])) {
            $content = static::$mockedResponses[$path]->getContent();
            $this->tempFile = tempnam(sys_get_temp_dir(), 'mock');
            file_put_contents($this->tempFile, $content);
            $path = $this->tempFile;
        }

        $this->handler = fopen($path, $mode);
        $this->restoreCustoms();
        return $this->handler;
    }

    /**
     * @return array
     */
    public function stream_stat()
    {
        return fstat($this->handler);
    }

    /**
     * @return bool
     */
    public function stream_eof()
    {
        return feof($this->handler);
    }

    /**
     * @param $length
     * @return string
     */
    public function stream_read($length)
    {
        return fread($this->handler, $length);
    }

    /**
     * @return bool
     */
    public function stream_close()
    {
        if ($this->tempFile) {
            $this->restoreDefaults();
            unlink($this->tempFile);
            $this->restoreCustoms();
        }
        return fclose($this->handler);
    }

    public function url_stat()
    {
        return stat($this->handler);
    }
}