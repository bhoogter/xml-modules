<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class classifiers_test extends TestCase
{
    public function testLoadXmlSourceLoads()
    {
        $obj = new xml_source();
        $this->assertNotNull($obj);
        $typ = $obj->type();
        $gid = $obj->gid;

        $this->assertEquals(str_replace("_", "", strtoupper($typ)) . "_", substr($gid, 0, strlen($typ)));
        // $this->assertEquals("xml_source", $typ);
        echo $obj->gid;
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

    public function testLoadXmlMergeTestScan()
    {
        $obj = new xml_file();
        $obj->merge(__DIR__ . '/data/test-xml-??.xml', 'information', 'set');
        $this->assertEquals(4, $obj->cnt("/information/set"));
        $this->assertEquals(1, $obj->cnt("//information/set[@id='2']/z"));
    }
}
