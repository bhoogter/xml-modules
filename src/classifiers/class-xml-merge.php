abstract class zobject_db
	{
	public $gid;
	public $type;
	public $id;
	public $db;
	protected $host, $user, $pass, $name;
	function __construct()
		{
		$this->gid = uniqid("ZDB_");
		$n = func_num_args();
		$a = func_get_args();
		$i = 0;
		$this->id   = ($n >= (++$i) && is_string($a[$i-1])) ? 
$a[$i-1] : "";
//print "<br/>n=$n, a=$a, i=$i, id=".$this->id;
		$this->host = ($n >= (++$i) && is_string($a[$i-1])) ? 
$a[$i-1] : "";
		$this->user = ($n >= (++$i) && is_string($a[$i-1])) ? 
$a[$i-1] : "";
		$this->pass = ($n >= (++$i) && is_string($a[$i-1])) ? 
$a[$i-1] : "";
		$this->name = ($n >= (++$i) && is_string($a[$i-1])) ? 
$a[$i-1] : "";
		if ($n >= 1) $this->connect();
		}
	function __destruct() {$this->close();}
	function prepare_sql($s)	{return $s;}
	abstract public function connect($host="", $user="", $pass="");
	abstract public function close();
	abstract public function execute($SQL);
	abstract public function execute_to_identity($sql);
	abstract public function execute_to_array($sql, $keyfield="");
	abstract public function execute_to_list($sql, $keyfield="");
	abstract public function execute_to_value($sql, $keyfield="");
	abstract public function execute_to_xml($sql, $Extra="", $Count=0);
	static function InterpretInteractiveSQLPart($F)
		{
//print_r($f);
		$f = $F[1];
		switch($f[0])
			{
			case "*":
				$c = strpos($f, "?");
				$key = substr($f, 1, $c - 1);
				;
				if ("" != ($v = 
juniper()->KeyValue($key)))
					{
					$res = substr($f, $c + 1);
					$res = str_replace("@", $v, 
$res);
					}
				else
					$res = "";
				break;
			case ":":
				$c = strpos($f, ";");
				$key = substr($f, 1, $c - 1);
				$list = substr($f, $c + 1);
				$spl = explode(";", $list);
				$f = juniper()->KeyValue($key);
//print "<br/>key=$key";
//print "<br/>lst=$list";
//print "<br/>spl=$spl";
//print "<br/>f=$f";
				unset($ll);
				$la = "";
				$lb = "";
				foreach($spl as $op)
					{
//print "<br/>op=$op";
					$ll = explode(":", $op);
					$la = $ll[0];
					$lb = $ll[1];
//print "<br/>la=$la";
					if ($la == "*" || $la == $f || 
($la== "?" && $f != ""))
						{$res = $lb;break;}
					}
				break;
			default:			$res = "";
			}
		return $res;
		}
	static function InterpretInteractiveSQL($s, $Args="")
		{
//print "<br/>InterpretInteractiveSQL($s, $Args)";
		$x = 0;
		$out = "";
		$out = preg_replace_callback( "\$[(][|](.*)[|][)]\$U", 
"zobject_db::InterpretInteractiveSQLPart", $s);
//print "<br/>out=$out";
		$out = juniper()->InterpretFields($out, false, "@@", 
true);
//print "<br/>out=$out";
		$out = juniper()->InterpretFields($out, false, "@", 
false, $Args);
//print "<br/>out=$out";
		return $out;
		}
	static function BuildZObjectQuery($sql, $v, $dm)
		{
//print "<br/>BuildZObjectQuery($SQL), ZN=".iOBJ()->name.", 
mode=".iOBJ()->mode;
		$n = iOBJ()->name;
		$m = iOBJ()->mode;
		if ($m=="create")
			{
			$fl = implode(", ", 
juniper()->FetchObjFields($n));
			$fv = "";
			foreach(juniper()->FetchObjFields($n) as $f)
				{
				$dt = juniper()->FetchObjFieldPart($n, 
$f, "@datatype");
				if ($dt=="") $dt="string";
				$dbt = juniper()->FetchDTPart($dt, 
"@db-type");
				if (in_array($dbt, array('char', 
'string', 'text', 'memo', 'varchar', 'blob')))
					$fv .= (strlen($fv)?",":"") . 
"'" . str_replace("'", "\'", $v[$f]) . "'";
				else if ($dbt=="date")
					$fv .= (strlen($fv)?",":"") . 
date('Y-m-d H:i:s', strtotime($v[$f]));
				else
					$fv .= (strlen($fv)?",":"") . 
str_replace("'", "\'", $v[$f]);
				}
			$sql = str_replace("{*}", "$fl VALUES ($fv)", 
$sql);
			}
		if ($m=="edit")
			{
			$k = iOBJ()->options['index'];
//print "<br/>k=$k";
			$fv = "";
			foreach(juniper()->FetchObjFields($n) as $f)
				{
				if ($f == $k) continue;
				$dt = juniper()->FetchObjFieldPart($n, 
$f, "@datatype");
				if ($dt=="") $dt="string";
				$dbt = juniper()->FetchDTPart($dt, 
"@db-type");
				if ($dm=="wpdb" || $dm=="mysql") $F = 
"`" . $f . "`";
				else $F = $f;
		
				if (in_array($dbt, array('char', 
'string', 'text', 'memo', 'varchar', 'blob')))
					$fv .= (strlen($fv)?",":"") . $F 
. "=" . "'" . str_replace("'", "\'", $v[$f]) . "'";
				else if ($dbt=="date")
					$fv .= (strlen($fv)?",":"") . $F 
. "=" . "'" . date('Y-m-d H:i:s', strtotime($v[$f])). "'";
				else
					if ($v[$f]!='') $fv .= 
(strlen($fv)?",":"") . $f . "=" . str_replace("'", "\'", $v[$f]);
				}
			$sql = str_replace("{*}", $fv, $sql);
			}
		return $sql;
		}
	}		//  class: zobject_db
?>

