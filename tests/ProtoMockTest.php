<?php
namespace Tests;

use Hal\ProtoMock\Mock;
use Hal\ProtoMock\ProtoMock;

class ProtoMockTest extends \PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        restore_error_handler();
        (new ProtoMock())->reset();
    }

    public function testMockerIsNotEnabledByDefault()
    {
        $filename = __DIR__ . '/data/example.txt';
        new ProtoMock();

        $content = file_get_contents($filename);
        $this->assertEquals('I am the original string', $content);
    }

    public function testICanEnableMockOnOnProtocol()
    {
        $filename = __DIR__ . '/data/example.txt';
        $mock = new ProtoMock();
        $mock->enable('file');
        //$mock->with($filename)->will('toto');

        $content = file_get_contents($filename);
        $this->assertEquals('I am the original string', $content);
    }

    public function testICanMockARequest()
    {
        $filename = __DIR__ . '/data/example.txt';
        $mock = new ProtoMock();
        $mock->enable('file');
        $mock->with($filename)->will('I am a mock');

        $content = file_get_contents($filename);
        $this->assertEquals('I am a mock', $content);
    }


    public function testICanMockARequestMultipleTime()
    {
        $filename = __DIR__ . '/data/example.txt';
        $mock = new ProtoMock();
        $mock->enable('file');
        $mock->with($filename)->will('I am a mock');

        $content = file_get_contents($filename);
        $this->assertEquals('I am a mock', $content);

        $content = file_get_contents($filename);
        $this->assertEquals('I am a mock', $content);
    }

    public function testICanMockARequestThenDisableMock()
    {
        $filename = __DIR__ . '/data/example.txt';
        $mock = new ProtoMock();
        $mock->enable('file');
        $mocked = $mock->with($filename)->will('I am a mock');

        $content = file_get_contents($filename);
        $this->assertEquals('I am a mock', $content);

        $mock->without($mocked);
        $content = file_get_contents($filename);

        $this->assertEquals('I am the original string', $content);
    }

    public function testICanDisableMocking()
    {
        $filename = __DIR__ . '/data/example.txt';
        $mock = new ProtoMock();
        $mock->enable('file');
        $mock->with($filename)->will('I am a mock');
        $mock->disable('file');

        $content = file_get_contents($filename);
        $this->assertEquals('I am the original string', $content);
    }


    public function testICanMockMultipleProtocols()
    {
        $filename = __DIR__ . '/data/example.txt';
        $mock = new ProtoMock();
        $mock->enable('file');
        $mock->enable('http');
        $mock->with($filename)->will('I am a mock from file');
        $mock->with('http://mybeautifulewebsite.fr')->will('I am a mock from http');

        $content = file_get_contents($filename);
        $this->assertEquals('I am a mock from file', $content);

        $content = file_get_contents('http://mybeautifulewebsite.fr');
        $this->assertEquals('I am a mock from http', $content);
    }

    public function testICanMockMultipleProtocolsAndDisableThem()
    {
        $filename = __DIR__ . '/data/example.txt';
        $mock = new ProtoMock();
        $mock->enable('file');
        $mock->enable('http');
        $mock->with($filename)->will('I am a mock from file');
        $mock->with('http://mybeautifulewebsite.fr')->will('I am a mock from http');

        $content = file_get_contents($filename);
        $this->assertEquals('I am a mock from file', $content);

        $content = file_get_contents('http://mybeautifulewebsite.fr');
        $this->assertEquals('I am a mock from http', $content);


        // disabling
        $mock->disable('file');
        $content = file_get_contents($filename);
        $this->assertEquals('I am the original string', $content);
    }

    public function testICanMockByRegex()
    {
        $mock = new ProtoMock();
        $mock->enable('file');

        $mock->matching('!.*.txt!')->will('I am a mock from regex');

        $content = file_get_contents('/nice/example.txt');
        $this->assertEquals('I am a mock from regex', $content);
    }

    /**
     * @group wip
     */
    public function testICanMockInsensitive()
    {
        $mock = new ProtoMock();
        $mock->enable('file');

        $mock->with('/file1.txt', Mock::MATCHING_EXACT_CASE_INSENSITIVE)->will('I am a mock from mock');

        $content = file_get_contents('/FILE1.txt');
        $this->assertEquals('I am a mock from mock', $content);
    }

    public function testICanUseCallableInOrderToBuildResponse()
    {
        $mock = new ProtoMock();
        $mock->enable('file');

        $mock->with('/myfile.txt')->will(function ($path) {
            return 'I am a mock from a callable with ' . $path;
        });

        $content = file_get_contents('/myfile.txt');
        $this->assertEquals('I am a mock from a callable with /myfile.txt', $content);
    }

    public function testMockCanFail()
    {
        $mock = new ProtoMock();
        $mock->enable('file');
        $mock->with('/myfile.txt')->willFail();

        try {
            $content = file_get_contents('/myfile.txt');
        } catch (\Throwable $e) {
            $this->assertEquals('file_get_contents(/myfile.txt): failed to open stream: No such file or directory', $e->getMessage());
            return;
        }

        throw new \LogicException('Warning was expected');
    }


    public function testMockCanFailDueToDns()
    {
        $mock = new ProtoMock();
        $mock->enable('file');
        ini_set('default_socket_timeout', 0);
        $mock->with('/myfile.txt')->willFailDueToDnsResolution();

        try {
            $content = file_get_contents('/myfile.txt');
        } catch (\Throwable $e) {
            $this->assertEquals('file_get_contents(): php_network_getaddresses: getaddrinfo failed: Name or service not known', $e->getMessage());
            return;
        }finally {
            ini_restore('default_socket_timeout');
        }

        throw new \LogicException('Warning was expected');
    }

    public function testFailingMockShouldReturnFalse()
    {
        $mock = new ProtoMock();
        $mock->enable('file');
        $mock->with('/myfile.txt')->willFail();

        set_error_handler(static function ($errno, $errstr) {
            $expectedWarnings = [
                'file_get_contents(/myfile.txt): failed to open stream: No such file or directory',
                'file_get_contents(/myfile.txt): failed to open stream: "Hal\ProtoMock\ProtoMock::stream_open" call failed',
            ];
            return in_array($errstr, $expectedWarnings);
        }, E_WARNING | E_USER_WARNING);

        $content = file_get_contents('/myfile.txt');
        $this->assertFalse($content);
    }
}
