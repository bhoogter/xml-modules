<?php

require_once("phar://" . __DIR__ . "/../class-xml-file.phar/src/class-xml-file.php");
require_once(__DIR__ . "/class-source-classifier.php");

class xml_merge extends xml_source
{
    public $aPath;
    public $modules;
    public $mScan;
    public $xml_root;
    public $xml_item;

    function __construct() {
        $this->aPath = __DIR__ . "/all.xml";
        $this->mScan = __DIR__ . "/modules";
        $this->modules = array();
        $this->mRoot = "modules";
        $this->mItem = "module";

        $n = func_num_args();
		$a = func_get_args();
		if ($n >= 1) $this->aPath = $a[0];
        
        if ($n >= 2 && is_array($a[1])) $this->modules = $a[1];
        if ($n >= 2 && is_string($a[1])) $this->mScan = $a[1];
        
        if ($n >= 3 && is_string($a[2])) $this->mRoot = $a[2];
        if ($n >= 4 && is_string($a[3])) $this->mItem = $a[3];

        $this->load();
    }

    function type() { return "xml_merge"; }
    function save($dst = '', $style = 'auto') { }
    function load($src = '')
    {
        if ($this->NeedsUpdate()) {
            //print "<br />Reloading Modules...";
            $xml = $this->JoinModules();
        }

        if (is_string($this->aPath) && file_exists($this->aPath)) parent::load($this->aPath);
        else if (is_string($xml)) parent::load($this->aPath);
    }

    function get_module_list()
    {
        if (is_array($this->modules)) return $this->modules;
        return glob($this->fScan);
    }

    private function NeedsUpdate()
    {
        if (!$this->aPath || $this->aPath = '') return true;
        $sysTime = 0;
        $sysTime = @filemtime($this->aPath);
        if (!$sysTime) return true;
        //print "<br/>sysTime=$sysTime, aPath=$this->aPath";
        //print_r($this->ModuleList());
        foreach ($this->get_module_list() as $m) 
            if (@filemtime($m) > $sysTime) return true;
        return false;
    }

    private function JoinModules()
    {
        $x  = "";
        $x .= "<?xml version='1.0' encoding='iso-8859-1'?>\n";
        $x .= "<$this->mRoot>\n";

        foreach ($this->get_module_list() as $m) {
            //print "<br/>m=$m";
            $M = new xml_source($m);
            $n = 0;
            while(true) {
                $n++;
                $s = $M->part_string("//$this->mRoot/$this->mItem[$n]");
                if ($s == "") break;
                $x .= $s;
            }
        }

        $x .= "</$this->mRoot>\n";

        $D = new xml_file($x);
        if (is_string($this->aPath) && $this->aPath != '') {
            if (!$D->can_save($this->aPath)) print "<br/>FAILED TO SAVE MASTER LIST";
            $D->save($this->aPath);
        }

        return $x;
    }
}
