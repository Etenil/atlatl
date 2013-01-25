<?php

class RequestTest extends PHPUnit_Framework_TestCase
{
    function testGet()
    {
        $get = array('test1' => 'stuff1',
                     'test2' => 'stuff2');
        $req = new atlatl\Request($get, array());
        $this->assertEquals('stuff1', $req->get('test1'));
        $this->assertEquals('stuff2', $req->get('test2'));
        $this->assertEquals(false, $req->get('test3'));
        $this->assertEquals('stuff3', $req->get('test3', 'stuff3'));
    }

    function testPost()
    {
        $post = array('test1' => 'stuff1',
                      'test2' => 'stuff2');
        $req = new atlatl\Request(array(), $post);
        $this->assertEquals('stuff1', $req->post('test1'));
        $this->assertEquals('stuff2', $req->post('test2'));
        $this->assertEquals(false, $req->post('test3'));
        $this->assertEquals('stuff3', $req->post('test3', 'stuff3'));
    }
}

?>