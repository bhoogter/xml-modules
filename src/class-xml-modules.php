<?php
include_once("class-xml-file.php");
include_once("functions.php");
class juniper_module extends xml_file
	{
	public $ID;
	public $longdesc;
	public $shortdesc;
	public $version;
	
	public $Sources;
	public $scan;
	
	public function clear()
		{
		parent::Clear();
		
		$this->ID  ="";
		$this->longdesc = "";
		$this->shortdesc = "";
		$this->version = "";
		return false;
		}
		
	static function ModuleFile($m)	{return juniper_module_dir("$m/zmodule.xml");}
	static function ModuleExists($m)	{return file_exists(self::ModuleFile($m));}
	function load_module($m)
		{
//print "<br/>load($m), MF=".self::ModuleFile($m);
		if (!self::ModuleExists($m)) return false;
		if (!$this->load(self::ModuleFile($m))) return false;
		$this->ID = $m; // $this->fetch_part('/module/@name');
//print "<br/>ModuleID: (f=".self::ModuleFile($m).") " . $this->ID;
		$this->longdesc = $this->fetch_part('//module/module-specification/description/long');
		$this->shortdesc = $this->fetch_part('//module/module-specification/description/short');
		$this->version= $this->fetch_part('//module/module-specification/description/version');
		return true;
		}
		
	}
	
/////////////////////////////////////////////////////////////////////////////////////////////////
class juniper_modules extends xml_file
	{
	private $aPath;
		
	function __construct()
		{
		$this->aPath = juniper_sysdata_dir('all.xml');
		parent::__construct();
		
		if ($this->NeedsUpdate())
			{
//print "<br />Reloading Modules...";
			$this->JoinModules();
			}
			
		$this->load($this->aPath);
		}
	
	private function NeedsUpdate()
		{
		$sysTime = 0;
		$sysTime = @filemtime($this->aPath);
		if (!$sysTime) return true;
//print "<br/>sysTime=$sysTime, aPath=$this->aPath";
//print_r($this->ModuleList());
		foreach($this->ModuleList() as $m)	if (@filemtime(juniper_module::ModuleFile($m)) > $sysTime) return true;
		}
	private function ModuleList()
		{
//print "<br/>ModuleList(), dir=" . juniper_module_dir();
		$h = opendir(juniper_module_dir());
		if ($h===false) echo "<br />No such directory: ".juniper_module_dir();
		$a = array();
		while (!(($f=readdir($h)) === false)) if (juniper_module::ModuleExists($f)) $a[] = $f;
//print "<br/>modulecount=" . count($a);
		closedir($h);
		return $a;
		}
		
	private function JoinModules()
		{
		$Modules = array();
		$scan = array();
//print_r($this->ModuleList());
		foreach($this->ModuleList() as $m) 
			{
//print "<br/>m=$m";
			$M = new juniper_module($m);
			$M->load_module($m);
			$Modules[] = $M;
//print "<br/>m=$m";
			foreach($M->fetch_list('//module/module-specification/components/scan/@name') as $scan_item)
				$scan[] = $scan_item;
			}
//print_r($scan);
		$x  = "";
		$x .= "<?xml version='1.0' encoding='iso-8859-1'?>\n";
		$x .= "<module>\n";
		
		foreach($Modules as $M)
			{
			$MID = "module='" . $M->ID . "'";
			$x .= str_replace("<module-specification","<module-specification $MID", $M->part_string("//module/module-specification"));
			foreach($scan as $scan_item)
				{
//print "<br/>scan_item=$scan_item..  ";print_r($M->part_string_list("//module/$scan_item"));
				foreach($M->part_string_list("//module/$scan_item") as $item)
					{
//print "<br/>item=$item\n";
					$x .= preg_replace("%^([<]([-a-zA-Z])+)%", "\$1 $MID", $item);
					}
				}
			}
		$x = $x . "</module>\n";
//die($x);
		$D = new xml_file($x);
		if (!$D->can_save($this->aPath)) print "<br/>FAILED TO SAVE MASTER LIST";
		return $D->save($this->aPath);
		}
	}		// juniper_modules
function zobject_module_test()
	{
	include_once('module_test.php');
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
if ($_SERVER['SCRIPT_FILENAME']==__FILE__) zobject_module_test();
?>
