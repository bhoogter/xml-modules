<?php
	function DBExecute($SQL)
		{
//print "<br/>DBExecute($SQL)";
		$conn = odbc_connect(DB_DNS,DB_USR,DB_PWD);
		$result = odbc_exec($conn,$SQL);
		$ok = !!$result;
		if ($result) odbc_free_result ($result);
		odbc_close($conn);
		return $ok;
		}
	function DBExecuteToIDENTITY($SQL)
		{
//print "<br/>DBExecute($SQL)";
		$conn = odbc_connect(DB_DNS,DB_USR,DB_PWD);
		$result = odbc_exec($conn,$SQL);
		$ok = !!$result;
		if ($ok)
			{
//			$result = odbc_exec("SELECT @@identity AS I");
//			if (odbc_fetch_row($result)) $I = odbc_result($result, "I");
			}
		if ($result) odbc_free_result ($result);
		odbc_close($conn);
		return $I;
		}
		
	function DBExecuteToArray($SQL, $keyfield="")
		{
		$conn = odbc_connect(DB_DNS,DB_USR,DB_PWD);
//print "<br/>DBExecuteToArray($SQL, $keyfield)";
		$result = odbc_exec($conn,$SQL);
		
		$res=array();
		
		$count = 0;
		while($result != 0 && odbc_fetch_row($result))
			{
			$x = array();
			for($i=1;$i<=odbc_num_fields($result);$i++)
				{
				$fn = odbc_field_name($result, $i);
				$fv = odbc_result($result,$i);
				$x[$fn]=$fv;
				}
			if ($keyfield=="")
				$res[$count]=$x;
			else
				{
//print "<br/>keyfield=$keyfield<br/>x=".$x[$keyfield];
				$res[$x[$keyfield]]=$x;
				}
			$count = $count + 1;
			}
//print "<br/>recordcount=$count";
		$res["recordcount"]=$count;
		if ($result) odbc_free_result ($result);
		odbc_close($conn);
		return $res;
		}
		
	function DBExecuteToList($SQL, $fieldnum=1)
		{
		$conn = odbc_connect(DB_DNS,DB_USR,DB_PWD);
//print "<br/>DBExecuteToList($SQL, $fieldnum)";
		$result = odbc_exec($conn,$SQL);
		
		$res=array();
		$i = 0;
		
		$count = 0;
		while($result != 0 && odbc_fetch_row($result)) 
$res[$i]=odbc_result($result, $fieldnum);
		if ($result) odbc_free_result ($result);
		odbc_close($conn);
		return $res;
		}
	function DBExecuteToValue($SQL, $fieldnum=1)
		{
		$conn = odbc_connect(DB_DNS,DB_USR,DB_PWD);
//print "<br/>DBExecuteToValue($SQL, $fieldnum)";
		$result = odbc_exec($conn,$SQL);
		if ($result!=0 && odbc_fetch_row($result)) $res = odbc_result($result, $fieldnum);
		if ($result) odbc_free_result ($result);
		odbc_close($conn);
		return $res;
		}
	function DBExecuteToXML($SQL, $Extra="", &$Count=0)
		{
		$token = "@@RECORD_COUNT-".uniqid()."@@";
		$conn = odbc_connect(DB_DNS,DB_USR,DB_PWD);
//print "<br/>DBExecuteToSQL($SQL, $Extra, [$Count])";
		$result = odbc_exec($conn,$SQL);
		
		$rc = odbc_num_rows($result);
		if ($rc==-1) $rc=$token;
		$x = XMLHeader() . "<recordset $Extra count='$rc'>\n";
		
		$count = 0;
		while($result != 0 && odbc_fetch_row($result))
			{
			$count = $count + 1;
			$x = $x . "    <row>\n";
			for($i=1;$i<=odbc_num_fields($result);$i++)
				{
				$fn = odbc_field_name($result, $i);
				$fv = odbc_result($result,$i);
				$fv = str_replace(array('&','<','>'), array('&amp;','&lt;','$gt;'), $fv);
				$x = $x . "        <field id='$fn'><![CDATA[$fv]]></field>\n";
				}
			$x = $x . "    </row>\n";
			}
		$x = $x . "</recordset>";
		if ($result) odbc_free_result ($result);
		odbc_close($conn);
		$Count = $rc;
		if ($rc == $token) $x = str_replace($token, $count, $x);
		return $x;
		}
