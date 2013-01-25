<?php

class UtilsTest extends PHPUnit_Framework_TestCase
{
    function testJoinPaths()
    {
        $this->assertEquals('/thing/stuff/thing2/stuff',
                            \atlatl\Utils::joinPaths('/thing/stuff', '/thing2/stuff'));
        $this->assertEquals('/thing/stuff/thing2/stuff',
                            \atlatl\Utils::joinPaths('/thing/stuff/', '/thing2/stuff'));
        $this->assertEquals('/thing/stuff/thing2/another stuff',
                            \atlatl\Utils::joinPaths('/thing/stuff', '/thing2/another stuff'));
        $this->assertEquals('http://thing/stuff/thing2/stuff',
                            \atlatl\Utils::joinPaths('http://thing/stuff', '/thing2/stuff'));
    }

    function testCleanFilename()
    {
        $this->assertEquals('abcdefgh', \atlatl\Utils::cleanFilename('abcdefgh'));
        $this->assertEquals('abcdefgh', \atlatl\Utils::cleanFilename('ábçdéfgh'));
        $this->assertEquals('abc_defgh', \atlatl\Utils::cleanFilename('abc defgh'));
        $this->assertEquals('abc_defgh', \atlatl\Utils::cleanFilename('abc$defgh'));
    }

    function testUniqueFilename()
    {
        touch('/tmp/stuff.test');
        $this->assertNotEquals('/tmp/stuff.test', \atlatl\Utils::uniqueFilename('/tmp', 'stuff', '.test'));
        $this->assertTrue(file_exists(\atlatl\Utils::uniqueFilename('/tmp', 'stuff', '.test')));

        $filepath = \atlatl\Utils::uniqueFilename('/tmp');
        $filename = basename($filepath);
        $this->assertTrue(file_exists('/tmp/' . $filename));

        $filename = basename(\atlatl\Utils::uniqueFilename('/tmp', 'stuff', '.test'));
        $this->assertRegExp('/^stuff[a-zA-Z0-9]{30}.test$/', $filename);

        $filename = basename(\atlatl\Utils::uniqueFilename('/tmp', 'stuff'));
        $this->assertRegExp('/^stuff[a-zA-Z0-9]{30}$/', $filename);

        $filename = basename(\atlatl\Utils::uniqueFilename('/tmp', '', '.test'));
        $this->assertRegExp('/^[a-zA-Z0-9]{30}.test$/', $filename);
    }
}

?>