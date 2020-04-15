<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;

class source_test extends TestCase
{
    private const XML1 = __DIR__ . "/data/test-xml-01.xml";
    private const XML2 = __DIR__ . "/data/test-xml-02.xml";
    private const XML_OTHER = __DIR__ . "/data/test-xml-other.xml";

	public static function setUpBeforeClass(): void
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
		$obj->add_source("XML1", self::XML1);

		$result = $obj->get("//XML1//set[2]/x");
		$this->assertEquals("4", $result);
    }

	public function testSoureWithMulitpleEntries(): void
	{
		$obj = new source();
		$obj->add_source("XML1", self::XML1);
		$obj->add_source("XML2", self::XML2);
		$obj->add_source("OTHER", self::XML_OTHER);

		$result = $obj->get("//XML1//set[@id='2']/x");
		$this->assertEquals("4", $result);

		$result = $obj->get("//XML2//set[@id='4']/y");
		$this->assertEquals("11", $result);

		$result = $obj->get("//OTHER///option[@name='option2']");
		$this->assertEquals("bbb", $result);
    }

	public function testMergedSource(): void
	{
        $obj = new source();
        $src = new xml_file();
        $src->merge(array(self::XML1, self::XML2), "information", "set");
		$obj->add_source("XML", $src);
		$obj->add_source("OTHER", self::XML_OTHER);

		$result = $obj->get("//XML//set[@id='2']/x");
		$this->assertEquals("4", $result);

		$result = $obj->get("//XML//set[@id='4']/y");
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

