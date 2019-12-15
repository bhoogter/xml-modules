<?php
include_once("class-zobject-db.php");
class zobject_db_wpdb extends zobject_db
	{
	public function connect($host="", $user="", $pass="")	{}			
//  wpdb is always on
	public function close() {}
	public function execute($sql)
		{
		global $wpdb;
//print "<br/>db-wpdb::execute($sql)";
		$wpdb->show_errors();
		$result = $wpdb->query($sql);
		$wpdb->hide_errors();
		$ok = !!$result;
		return $ok;
		}
	public function execute_to_identity($sql)
		{
//print "<br/>db-wpdb::execute_to_identity($sql)";
		global $wpdb;
		$wpdb->show_errors();
		$result = $wpdb->query($sql);
		$ok = !!$result;
		if ($result)
			{
			$I= $wpdb->get_var("SELECT @@identity AS I");
			}
		$wpdb->hide_errors();
		return $I;
		}
		
	public function execute_to_array($sql, $keyfield="")
		{
//print "<br/>db-wpdb::execute_to_array($sql, $keyfield)";
		global $wpdb;
		$wpdb->show_errors();
		$x = $wpdb->get_results($sql, ARRAY_A);
		$wpdb->hide_errors();
		return $x;
		}
		
	public function execute_to_list($sql, $fieldnum=0)
		{
//print "<br/>db-wpdb::execute_to_list($sql, $fieldnum)";
		global $wpdb;
		$wpdb->show_errors();
		$x = $wpdb->get_col($sql, $fieldnum);
		$wpdb->hide_errors();
		return $x;
		}
	public function execute_to_value($sql, $fieldnum=0)
		{
//print "<br/>db-wpdb::execute_to_value($sql, $fieldnum)";
		global $wpdb;
		$wpdb->show_errors();
		$x = $wpdb->get_var($sql,$fieldnum);
		$wpdb->hide_errors();
		return $x;
		}
	public function execute_to_xml($sql, $Extra="", &$count=0)
		{
//print "<br/>db-wpdb::execute_to_xml($sql, $Extra, [$count])";
		global $wpdb;
		$wpdb->show_errors();
		$result = $wpdb->get_results($sql, ARRAY_A);
		$wpdb->hide_errors();
		if (!result) return "<?xml version='1.0' ?>\n<recordset 
$Extra count='0'>\n";
//print "<br/>result==";print_r($result);die();
		$rc = count($result);
		$x  = "<?xml version='1.0' ?>\n";
		$x .= "<recordset $Extra count='".$rc."'>\n";
		
		foreach($result as $n=>$row)
			{
			$count++;
			$x .= "    <row>\n";
			foreach($row as $a=>$b)
				{
//				$a = strtolower($a);
				$b = str_replace(array('&','<','>'), 
array('&amp;','&lt;','$gt;'), $b);
				$x = $x . "        <field 
id='$a'><![CDATA[$b]]></field>\n";
				}
			$x .= "    </row>\n";
			}
		$x = $x . "</recordset>";
//die($x);
		return $x;
		}
	}  //  class: zobject_db_wpdb	
?>

