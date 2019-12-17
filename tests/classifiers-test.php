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

    public function testLoadXmlMergeLoads() {
        $obj = new xml_merge();
        $this->assertNotNull($obj);
        $this->assertEquals("xml_merge", $obj->type());
    }
}