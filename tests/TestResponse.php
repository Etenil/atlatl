<?php

require_once(dirname(__DIR__) . '/response.php');

class TestResponse extends PHPUnit_Framework_TestCase
{
    function testBody()
    {
        $body = 'This is a body.';
        $resp = new atlatl\Response($body);
        $this->assertEquals($body, $resp->getBody());

        $body.= 'another piece';
        $resp->append('another piece');
        $this->assertEquals($body, $resp->getBody());
    }

    function testHeaders()
    {
        $resp = new atlatl\Response();
        $resp->setHeader('test1', 'stuff1')
            ->setHeader('test2', 'stuff2');

        $this->assertEquals('stuff1', $resp->getHeader('test1'));
        $this->assertEquals('stuff2', $resp->getHeader('test2'));

        $this->assertEquals(array(
                                'test1' => 'stuff1',
                                'test2' => 'stuff2'),
                            $resp->getHeaders());

        $this->assertFalse($resp->getHeader('test3'));

        $hdr = $resp->delHeader('test1');
        $this->assertEquals('stuff1', $hdr);
        $this->assertFalse($resp->getHeader('test1'));
    }
}

?>