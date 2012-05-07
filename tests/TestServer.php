<?php

require_once(dirname(__DIR__) . '/server.php');

class TestServer extends PHPUnit_Framework_TestCase
{
	function testParse()
	{
		$server = new Movicon\Server(array('HTTP_HOST' => 'movicontest'));
		$this->assertEquals('movicontest', $server->getHost());
	}
}

?>