<?php

abstract class db_source extends xml_file_base
	{
	public $id;
	public $db;

	protected $host, $user, $pass, $name;
	public function type() { return "db_source"; }
	function __construct()
		{
		$this->IDS = uniqid("SDB_");
		$n = func_num_args();
		$a = func_get_args();
		$i = 0;
		// $this->id   = ($n >= (++$i) && is_string($a[$i-1])) ? $a[$i-1] : "";
// print "<br/>n=$n, a=a, i=$i, id=".$this->id;print_r($a);
		$this->host = ($n >= (++$i) && is_string($a[$i-1])) ? $a[$i-1] : "";
		$this->user = ($n >= (++$i) && is_string($a[$i-1])) ? $a[$i-1] : "";
		$this->pass = ($n >= (++$i) && is_string($a[$i-1])) ? $a[$i-1] : "";
		$this->name = ($n >= (++$i) && is_string($a[$i-1])) ? $a[$i-1] : "";
// print "\n<br/>db-source::__constuct... host=$this->host, user=$this->user, pass=$this->pass, name=$this->name";

		if ($n >= 1) $this->connect();
		}
	function __destruct() { $this->close(); }

	function prepare_sql($s)	{return $s;}
	
	abstract public function connect($host="", $user="", $pass="");
	abstract public function close();
	abstract public function execute($SQL);
	abstract public function execute_to_identity($sql);
	abstract public function execute_to_array($sql, $keyfield="");
	abstract public function execute_to_list($sql, $keyfield="");
	abstract public function execute_to_value($sql, $keyfield="");
    abstract public function execute_to_xml($sql, $Extra="", &$Count=0);
    
    function nde($p) {
		print "\n<br/>NDE: db-wpdb(p=$p)";
	}
    function nds($p) {
		print "\n<br/>NDS: db-wpdb(p=$p)";
	}
    function def($p) {
		print "\n<br/>DEF: db-wpdb(p=$p)";
	}
	function get($p) {
		print "\n<br/>GET: db-wpdb(p=$p)";
	}
	function set($p, $x) {
		print "\n<br/>SET: db-wpdb(p=$p)";
	}
    function lst($p) {
		print "\n<br/>LST: db-wpdb(p=$p)";
	}
    function cnt($p) {
		print "\n<br/>CNT: db-wpdb(p=$p)";
	}
    function del($p) {
		print "\n<br/>DEL: db-wpdb(p=$p)";
    }
    
    function merge($scan, $root = NULL, $item = NULL, $persist = NULL) {}
    function load($src) {}
    function save($f = '', $style = 'auto') {}
    function can_save() { return false; }

    function get_property_list() { return array(); }
    function has_property($p) { return false; }
    function get_property($p) { return null; }
    function set_property($p, $v) { return null; }

	static function InterpretInteractiveSQLPart($F)
	{
		//print_r($f);
		$f = $F[1];
		switch ($f[0]) {
			case "*":
				$c = strpos($f, "?");
				$key = substr($f, 1, $c - 1);;
				if ("" != ($v = source()->KeyValue($key))) {
					$res = substr($f, $c + 1);
					$res = str_replace("@", $v, $res);
				} else
					$res = "";
				break;
			case ":":
				$c = strpos($f, ";");
				$key = substr($f, 1, $c - 1);
				$list = substr($f, $c + 1);
				$spl = explode(";", $list);
				$f = source()->KeyValue($key);
				//print "<br/>key=$key";
				//print "<br/>lst=$list";
				//print "<br/>spl=$spl";
				//print "<br/>f=$f";
				unset($ll);
				$la = "";
				$lb = "";
				foreach ($spl as $op) {
					//print "<br/>op=$op";
					$ll = explode(":", $op);
					$la = $ll[0];
					$lb = $ll[1];
					//print "<br/>la=$la";
					if ($la == "*" || $la == $f || ($la == "?" && $f != "")) {
						$res = $lb;
						break;
					}
				}
				break;
			default:
				$res = "";
		}
		return $res;
	}
	
	static function InterpretInteractiveSQL($s, $Args="")
		{
//print "<br/>InterpretInteractiveSQL($s, $Args)";
		$x = 0;
		$out = "";
		$out = preg_replace_callback( "\$[(][|](.*)[|][)]\$U", "zobject_db::InterpretInteractiveSQLPart", $s);
//print "<br/>out=$out";
		$out = source()->InterpretFields($out, false, "@@", true);
//print "<br/>out=$out";
		$out = source()->InterpretFields($out, false, "@", false, $Args);
//print "<br/>out=$out";
		return $out;
		}

	static function BuildZObjectQuery($sql, $v, $dm)
		{
//print "<br/>BuildZObjectQuery($SQL), ZN=".iOBJ()->name.", mode=".iOBJ()->mode;
		$n = iOBJ()->name;
		$m = iOBJ()->mode;
		if ($m=="create")
			{
			$fl = implode(", ", source()->FetchObjFields($n));
			$fv = "";
			foreach(source()->FetchObjFields($n) as $f)
				{
				$dt = source()->FetchObjFieldPart($n, $f, "@datatype");
				if ($dt=="") $dt="string";
				$dbt = source()->FetchDTPart($dt, "@db-type");
				if (in_array($dbt, array('char', 'string', 'text', 'memo', 'varchar', 'blob')))
					$fv .= (strlen($fv)?",":"") . "'" . str_replace("'", "\'", $v[$f]) . "'";
				else if ($dbt=="date")
					$fv .= (strlen($fv)?",":"") . date('Y-m-d H:i:s', strtotime($v[$f]));
				else
					$fv .= (strlen($fv)?",":"") . str_replace("'", "\'", $v[$f]);
				}
			$sql = str_replace("{*}", "$fl VALUES ($fv)", $sql);
			}
		if ($m=="edit")
			{
			$k = iOBJ()->options['index'];
//print "<br/>k=$k";
			$fv = "";
			foreach(source()->FetchObjFields($n) as $f)
				{
				if ($f == $k) continue;
				$dt = source()->FetchObjFieldPart($n, $f, "@datatype");
				if ($dt=="") $dt="string";
				$dbt = source()->FetchDTPart($dt, "@db-type");
				if ($dm=="wpdb" || $dm=="mysql") $F = "`" . $f . "`";
				else $F = $f;
		
				if (in_array($dbt, array('char', 'string', 'text', 'memo', 'varchar', 'blob')))
					$fv .= (strlen($fv)?",":"") . $F . "=" . "'" . str_replace("'", "\'", $v[$f]) . "'";
				else if ($dbt=="date")
					$fv .= (strlen($fv)?",":"") . $F . "=" . "'" . date('Y-m-d H:i:s', strtotime($v[$f])). "'";
				else
					if ($v[$f]!='') $fv .= (strlen($fv)?",":"") . $f . "=" . str_replace("'", "\'", $v[$f]);
				}
			$sql = str_replace("{*}", $fv, $sql);
			}
		return $sql;
		}
	}
?>

