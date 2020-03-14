<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;

class source_test extends TestCase
{

	public static function setUpBeforeClass()
    {
    }

	public function testCreateXmlSource(): void
	{
		$obj = new source();
		$this->assertNotNull($obj);
    }

	public function testAccessSource(): void
	{
		$obj = new source();
		$obj->add_source("XML1", __DIR__ . "/data/test-xml-01.xml");

		$result = $obj->get("//XML1//set[2]/x");
		$this->assertEquals("4", $result);
    }

	public function testSoureWithMulitpleEntries(): void
	{
		$obj = new source();
		$obj->add_source("XML1", __DIR__ . "/data/test-xml-01.xml");
		$obj->add_source("XML2", __DIR__ . "/data/test-xml-02.xml");
		$obj->add_source("OTHER", __DIR__ . "/data/test-xml-other.xml");

		$result = $obj->get("//XML1//set[@id='2']/x");
		$this->assertEquals("4", $result);

		$result = $obj->get("//XML2//set[@id='4']/y");
		$this->assertEquals("11", $result);

		$result = $obj->get("//OTHER///option[@name='option2']");
		$this->assertEquals("bbb", $result);
    }

	public function testSoureFunction(): void
	{ 
		$first_call = source();
		$second_call = source();
		$this->assertEquals($first_call, $second_call);
	}
	
}

