<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once(__DIR__ . "/../src/class-source.php");

class classifiers_test extends TestCase
{
    public function testLoadXmlSourceLoads() {
        $obj = new xml_source();
        $this->assertNotNull($obj);
        $this->assertEquals("xml_source", $obj->type());
    }

    public function testLoadXmlSourceLoadXml() {
        $obj = new xml_source(__DIR__ . 'data/test-xml-other.xml');
        $this->assertEquals($obj);

		$result = $obj->get("//set[@id='4']/y");
		$this->assertEquals("11", $result);
    }

    public function testLoadXmlMergeLoads() {
        $obj = new xml_merge();
        $this->assertNotNull($obj);
        $this->assertEquals("xml_merge", $obj->type());
    }

    public function testLoadXmlMergeTestScan() {
        $obj = new xml_merge('', 'data/test-xml-??.xml', 'information', 'set');
        $this->assertNotNull($obj);
        $this->assertEquals(4, $obj->cnt("//information/set"));
    }

}