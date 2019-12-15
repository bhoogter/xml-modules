<?php

include_once("class-zobject-db.php");
class zobject_db_mysql extends zobject_db
	{
	public function connect($host="", $user="", $pass="")
		{
		$this->type = "mysql";
		if ($this->id == "wpdb") {$this->db = $wpdb;return;}
//print "<br/>db-mysql::connect: id=".$this->id.", host=".$this->host.", user=".$this->user.", pass=".$this->pass."";
		if ($host != "") $this->host = $host;
		if ($user != "") $this->user = $user;
		if ($pass != "") $this->pass = $pass;
		if (($this->db = mysql_connect($this->host, $this->user, $this->pass, true))===false)
			{
			trigger_error("Unable to connect to datasource [".$this->id."]: ".mysql_error($this->db), E_USER_WARNING);
			$this->close();
			return;
			}
		if (!($r = mysql_select_db($this->name, $this->db)))
			{
			trigger_error("Unable to connect to desired database [".$this->name."] on datasource [".$this->id."]: ".mysql_error($this->db), E_USER_WARNING);
			$this->close();
			return;
			}
		}
	public function close() {@mysql_close($this->db);$this->db=0;}
	public function execute($sql)
		{
//print "<br/>db-mysql::execute($sql)";
		$result = mysql_query($sql,$this->db);
		$ok = !!$result;
		if ($result) mysql_free_result ($result);
		return $ok;
		}
	public function execute_to_identity($sql)
		{
//print "<br/>db-mysql::execute_to_identity($sql)";
		$result = mysql_query($sql,$this->db);
		$ok = !!$result;
		if ($ok)
			{
			$result = mysql_query("SELECT @@identity AS I");
			if ($r = mysql_fetch_row($result)) $I = $r[0];
			}
		if ($result) mysql_free_result ($result);
		return $I;
		}
		
	public function execute_to_array($sql, $keyfield="")
		{
//print "<br/>db-mysql::execute_to_array($sql, $keyfield)";
		$result = mysql_query($sql,$this->db);
		$ok = !!$result;
		if (!$ok) 
			{
			return array();
			}
		$res=array();
		$count = 0;
		while($result != 0 && $r = myqsl_fetch_array($result,  MYSQL_BOTH))
			{
			if (!isset($r[$keyfield])) $keyfield = "";
			if ($keyfield == "")
				$res[] = $r;
			else
				$res[$r[$keyfield]] = $r;
			$count = $count + 1;
			}
//print "<br/>recordcount=$count";
		$res["recordcount"]=$count;
		if ($result) mysql_free_result ($result);
		return $res;
		}
		
	public function execute_to_list($sql, $fieldnum=1)
		{
//print "<br/>db-mysql::execute_to_list($sql, $fieldnum)";
		$result = mysql_query($sql,$this->db);
		$ok = !!$result;
		if (!ok) 
			{
			return array();
			}
		
		$res=array();
		
		while($result != 0 && mysql_fetch_row($result)) 
			$res[]=mysql_result($result, $fieldnum);
		if ($result) mysql_free_result ($result);
		return $res;
		}
	public function execute_to_value($sql, $fieldnum=1)
		{
//print "<br/>db-mysql::execute_to_value($sql, $fieldnum)";
		$result = mysql_query($sql,$this->db);
		if ($result!=0 && mysql_fetch_row($result)) $res = mysql_result($result, $fieldnum);
		if ($result) mysql_free_result ($result);
		return $res;
		}
	public function execute_to_xml($sql, $Extra="", &$count=0)
		{
		$token = "@@RECORD_COUNT-".uniqid()."@@";
//print "<br/>db-mysql::execute_to_xml($sql, $Extra, [$count])";
		$result = mysql_query($sql, $this->db);
		if ($result===false) 
			{
//print "<br/>db-mysql::execute_to_xml: no result: ".mysql_error($this->db);
			return "";
			}
		
//		$rc = mysql_num_rows($result);
//		if ($rc==-1) $rc = $token;
		$x = "<?xml version='1.0' ?>\n<recordset $Extra count='$rc'>\n";
		
		$count = 0;
		$fc = mysql_num_fields($result);
		$fn = array();
		for ($i=0;$i<$fc;$i++)
			$fn[] = mysql_field_name($result, $i);
		while($r = mysql_fetch_assoc($result))
			{
			$count++;
			$x = $x . "    <row>\n";
			foreach($r as $a=>$b)
				{
				$a = strtolower($a);
				$b = str_replace(array('&','<','>'), array('&amp;','&lt;','$gt;'), $b);
				$x = $x . "        <field id='$a'><![CDATA[$b]]></field>\n";
				}
			$x = $x . "    </row>\n";
			}
		$x = $x . "</recordset>";
		if ($result) mysql_free_result ($result);
		if ($rc == $token) $x = str_replace($token, $count, $x);
//die($x);
		return $x;
		}
	}
