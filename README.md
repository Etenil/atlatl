Atlatl
====
Author: Guillaume Pasquet (aka Etenil) <boss@etenil.net>

Atlatl is a fast and modular MVC framework for PHP

License
=======
Atlatl is licensed under GPLv3 license. See LICENSE file for further details.

Project Goals
============
The overall goal of this branch is to make a viable core for a bigger framework, with plug-ins support and MVC structure in place and built-in support for multiple applications.

Testability needs to be stressed with the introduction of Dependency-Injection and PHPUnit support.

Finally, security features will be introduced as PHP only provides very basic safety guards and this should be part of the core of any non-trivial project.

Usage
=====

Here is an example of how to use atlatl:

	require('loader.php');

	$app = new atlatl\Core('/atlatl/test.php');

    class TestModule extends atlatl\Module
	{
		protected $name;
		
		function hello()
		{
			return "Hello ".$this->name."!";
		}
	}

	class TestController extends atlatl\Controller
	{
		function test()
		{
			echo $this->modules->TestModule->hello();
		}
	}

	$app->loadModule('TestModule', array('name' => 'Guillaume'));
	$app->serve(array('/' => 'TestController::test'));


Credits
=======
Many thanks to Joe Topjian for the original [GluePHP](http://gluephp.com) code.
