<?php

function test_header($Title)
	{
	include_once("class-source.php");
	print "<html>\n<head\n<title>$Title TEST SUITE</title>\n";
	zo_scripts();
	zo_css();
	print "</head>\n<body>\n";
	print "<table align='center' width='900' border='1' style='border-collapse:collapse;border-color:black;border-width:3'>";
	print "<tr><td colspan='3' align='center' bgcolor='gray'><font size='+3' color='white'>$Title MANIP TEST</font></td></tr>";
	print "<tr style='background-color:lightblue'><td>Test</td><td>Result</td><td align='center' width='50'>OK?</td></tr>";
	return true;
    }
    
function zobject_test_footer($A=true)
	{
	print "</table></body></html>";
	if (!$A) print "SOME FAILURES REPORTED";
	die();
	}
function zobject_test_result($n, $r, $k, &$A=false)
	{
	print "<tr><td>$n</td><td>";
	print_r($r);
	print "</td><td align='center'" . ($k?" bgcolor='lightgreen'><b>OK</b>":" bgcolor='pink'>ERROR") . " </td></tr>";
	$A = $A && $k;
	}
