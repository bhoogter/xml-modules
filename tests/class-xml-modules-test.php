<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// include_once('module_test.php');
require_once(__DIR__ . "/../src/class-xml-modules.php");

class xml_file_test extends TestCase
{

	public function testCreateXmlModules(): void
	{ 
		$obj = new xml_modules();
		$this->assertNotNull($obj);
	}

	function zobject_module_test()
	{
		$M = new juniper_module();
		$M->load("event-gatherer");
		$X = new juniper_modules;
		$A = zobject_test_header("ZOBJ MODULE");
		$testname = "Load Module Object";
		$testresult = $M->loaded;
		$testexpect = true;
		$testok = ($testresult == $testexpect);
		zobject_test_result($testname, $testresult, $testok, $A);

		$testname = "Module ID";
		$testresult = $M->ID;
		$testexpect = "EventGatherer";
		$testok = ($testresult == $testexpect);
		zobject_test_result($testname, $testresult, $testok, $A);

		$testname = "Event Gatherer Module Object Zobj Count";
		$testresult = count($M->Objects);
		$testexpect = true;
		$testok = ($testresult == $testexpect);
		zobject_test_result($testname, $testresult, $testok, $A);

		$testname = "Event Gatherer Module Object Zact Count";
		$testresult = count($M->Actions);
		$testexpect = true;
		$testok = ($testresult == $testexpect);
		zobject_test_result($testname, $testresult, $testok, $A);

		$testname = "Event Gatherer Module Object Zds Count";
		$testresult = count($M->DataSources);
		$testexpect = true;
		$testok = ($testresult == $testexpect);
		zobject_test_result($testname, $testresult, $testok, $A);

		$testname = "Load Module List Object";
		$testresult = $X->loaded;
		$testexpect = true;
		$testok = ($testresult == $testexpect);
		zobject_test_result($testname, $testresult, $testok, $A);

		$testname = "ZObject Count";
		$testresult = $X->count_parts("/*/zobjectdef");
		$testexpect = 0;
		$testok = ($testresult > 1);
		zobject_test_result($testname, $testresult, $testok, $A);

		$testname = "ZAction Count";
		$testresult = $X->count_parts("/*/zactiondef");
		$testexpect = 0;
		$testok = ($testresult > 1);
		zobject_test_result($testname, $testresult, $testok, $A);

		$testname = "ZPage Count";
		$testresult = $X->count_parts("/*/zpagedef");
		$testexpect = 0;
		$testok = ($testresult > 1);
		zobject_test_result($testname, $testresult, $testok, $A);

		$testname = "ZDataSource Count";
		$testresult = $X->count_parts("/*/datasource");
		$testexpect = 0;
		$testok = ($testresult > 1);
		zobject_test_result($testname, $testresult, $testok, $A);

		zobject_test_footer($A);
	}
}
