<?php

class xml_module extends xml_file
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
		
	static function ModuleFile($m)		{return juniper_module_dir("$m/zmodule.xml");}
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
