<?php

require_once(dirname(__DIR__) . '/server.php');

class TestServer extends PHPUnit_Framework_TestCase
{
	function testParse()
	{
        $server_vars = array ('USER' => 'www-data',
                              'HOME' => '/var/www',
                              'FCGI_ROLE' => 'RESPONDER',
                              'SCRIPT_FILENAME' => '/var/www/test.php',
                              'QUERY_STRING' => '',
                              'REQUEST_METHOD' => 'get',
                              'CONTENT_TYPE' => '',
                              'CONTENT_LENGTH' => '',
                              'SCRIPT_NAME' => '/test.php',
                              'REQUEST_URI' => '/test.php',
                              'DOCUMENT_URI' => '/test.php',
                              'DOCUMENT_ROOT' => '/var/www',
                              'SERVER_PROTOCOL' => 'HTTP/1.1',
                              'GATEWAY_INTERFACE' => 'CGI/1.1',
                              'SERVER_SOFTWARE' => 'nginx/1.1.19',
                              'REMOTE_ADDR' => '127.0.0.1',
                              'REMOTE_PORT' => '44282',
                              'SERVER_ADDR' => '127.0.0.1',
                              'SERVER_PORT' => '80',
                              'SERVER_NAME' => '',
                              'HTTPS' => '',
                              'REDIRECT_STATUS' => '200',
                              'APPLICATION_ENV' => 'development',
                              'HTTP_HOST' => 'movicontest',
                              'HTTP_USER_AGENT' => 'PHPUNIT',
                              'HTTP_ACCEPT' => 'text/html,application/xhtml+xml;q=0.9,*/*;q=0.8',
                              'HTTP_ACCEPT_LANGUAGE' => 'en-gb,en;q=0.5',
                              'HTTP_ACCEPT_ENCODING' => 'gzip, deflate',
                              'HTTP_CONNECTION' => 'keep-alive',
                              'HTTP_CACHE_CONTROL' => 'max-age=0',
                              'PHP_SELF' => '/test.php',
                              'REQUEST_TIME' => 1336489852);
		$server = new atlatl\Server($server_vars);

		$this->assertEquals('movicontest', $server->getHost());
        $this->assertEquals('/var/www/test.php', $server->getFilename());
        $this->assertEquals('GET', $server->getMethod());
        $this->assertEquals('/test.php', $server->getScriptname());
        $this->assertEquals('/test.php', $server->getUri());
        $this->assertEquals('HTTP/1.1', $server->getProtocol());
        $this->assertEquals('nginx/1.1.19', $server->getSoftware());
        $this->assertEquals('127.0.0.1', $server->getRemoteAddr());
        $this->assertEquals('44282', $server->getRemotePort());
        $this->assertEquals('127.0.0.1', $server->getAddress());
        $this->assertEquals('80', $server->getPort());
        $this->assertEquals('', $server->getName());
        $this->assertEquals('movicontest', $server->getHost());
        $this->assertEquals('PHPUNIT', $server->getUserAgent());
        $this->assertEquals(array('text/html','application/xhtml+xml'), $server->getAccept());
        $this->assertEquals(array('en-gb','en'), $server->getLanguages());
        $this->assertEquals(array('gzip','deflate'), $server->getEncodings());
        $this->assertEquals('keep-alive', $server->getConnection());
        $this->assertEquals('1336489852', $server->getTime());
	}
}

?>