<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// include_once('module_test.php');
require_once(__DIR__ . "/../src/class-xml-source.php");

class xml_source_test extends TestCase
{

	public function testCreateXmlSource(): void
	{ 
		$obj = new xml_source();
		$this->assertNotNull($obj);
    }

	public function testAccessSource(): void
	{ 
		$obj = new xml_source();
		$obj->add_source("XML1", __DIR__ . "/data/test-xml-01.xml");

		$result = $obj->get("//XML1//set[2]/x");
		$this->assertEquals("4", $result);
    }
}

// function zobject_source_test()
// {
// 	include_once('module_test.php');
// 	$S = new juniper_source();
// 	$A = true;
// 	zobject_test_header("ZOBJECT SOURCE");
// 	$testname = "Load Source Object";
// 	$testresult = $S->loaded;
// 	$testexpect = true;
// 	$testok = ($testresult == $testexpect);
// 	zobject_test_result($testname, $testresult, $testok, $A);

// 	$testname = "ZObject Count";
// 	$S = new juniper_source();
// 	$testresult = $S->cnt("//SYS/*/zobjectdef");
// 	$testexpect = 0;
// 	$testok = ($testresult > 1);
// 	zobject_test_result($testname, $testresult, $testok, $A);
// 	$testname = "DT cnt";
// 	$testresult = juniper()->cnt("//SYS/*/typedef");
// 	$testexpect = 1;
// 	$testok = ($testresult >= $testexpect);
// 	zobject_test_result($testname, $testresult, $testok, $A);
// 	$testname = "DT get";
// 	$testresult = juniper()->get("//SYS/*/typedef[@name='dateunix']/@format");
// 	$testexpect = "php:DressUnixDate";
// 	$testok = ($testresult == $testexpect);
// 	zobject_test_result($testname, $testresult, $testok, $A);

// 	$testname = "ObjF get";
// 	$testresult = juniper()->get("//SYS/*/zobjectdef[@name='EventSource']/fielddefs/fielddef[@id='id']/@allow-edit");
// 	$testexpect = "0";
// 	$testok = ($testresult == $testexpect);
// 	zobject_test_result($testname, $testresult, $testok, $A);

// 	$testname = "ObjF get 2";
// 	$testresult = juniper()->FetchObjFieldPart("EventSource", "id", "@allow-edit");
// 	$testexpect = "0";
// 	$testok = ($testresult == $testexpect);
// 	zobject_test_result($testname, $testresult, $testok, $A);

// 	zobject_test_footer();
// }
