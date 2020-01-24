<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class classifiers_test extends TestCase
{
    public function testLoadXmlSourceLoads()
    {
        $obj = new xml_source();
        $this->assertNotNull($obj);
        $this->assertEquals("xml_source", $obj->type());
    }

    public function testXmlFileLoad() 
    {
        $obj = new xml_file(__DIR__ . '/data/test-xml-other.xml');
        $result = $obj->get("//option[@name='option1']");
        $this->assertEquals("aaa", $result);
    }

    public function testLoadXmlSourceLoadXml()
    {
        $obj = new xml_source(__DIR__ . '/data/test-xml-other.xml');
        $result = $obj->get("//option[@name='option1']");
        $this->assertEquals("aaa", $result);
    }

    public function testLoadXmlMergeLoads()
    {
        $obj = new xml_merge();
        $this->assertNotNull($obj);
        $this->assertEquals("xml_merge", $obj->type());
    }

    public function testLoadXmlMergeTestScan()
    {
        $obj = new xml_merge('', __DIR__ . '/data/test-xml-??.xml', 'information', 'set');
        $this->assertEquals(4, $obj->cnt("/information/set"));
        $this->assertEquals(1, $obj->cnt("//information/set[@id='2']/z"));
    }
}
