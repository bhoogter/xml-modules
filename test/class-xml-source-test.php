<?php

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
