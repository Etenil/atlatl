<?php

require_once(dirname(__DIR__) . '/module.php');

class MyModule extends atlatl\Module
{
    public $prop1;
    public $prop2;
}

class TestModule extends PHPUnit_Framework_TestCase
{
    function testPopulate()
    {
        $mod = new MyModule(array('prop1' => 'stuff1', 'prop2' => 'stuff2'));

        $this->assertEquals('stuff1', $mod->prop1);
        $this->assertEquals('stuff2', $mod->prop2);
    }
}

?>