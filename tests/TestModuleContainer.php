<?php

require_once(dirname(__DIR__) . '/module.php');
require_once(dirname(__DIR__) . '/modulecontainer.php');

class MyModule extends atlatl\Module
{
    function cook()
    {
    }
}

class TestModuleContainer extends PHPUnit_Framework_TestCase
{
    function testPopulate()
    {
        $mod1 = $this->getMock('MyModule');
        $mod1->expects($this->once())->method('cook');

        $mod2 = $this->getMock('MyModule');
        $mod2->expects($this->once())->method('cook');

        $mc = new atlatl\ModuleContainer();
        $mc->add_to_list('module1', $mod1);
        $mc->add_to_list('module2', $mod2);

        $mc->runMethod('cook');
    }
}

?>