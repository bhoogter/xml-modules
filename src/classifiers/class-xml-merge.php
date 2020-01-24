<?php

class xml_merge extends xml_source
{
    public $aPath;
    public $modules;
    public $mScan;
    public $xml_root;
    public $xml_item;

    function __construct() {
        parent::__construct();
        $this->aPath = __DIR__ . "/all.xml";
        $this->mScan = __DIR__ . "/modules";
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
        $xml = '';
        if ($this->NeedsUpdate()) {
            // print "\n<br />Reloading Modules...";
            $xml = $this->JoinModules();
        }

        if (is_string($this->aPath) && file_exists($this->aPath)) {
// print "\nLoading aPath..";
            parent::load($this->aPath);
        } else if (is_string($xml)) {
// print "\nLoading xml... " . substr($xml, 0, 25) . "...";
            parent::loadXML($xml);
        }
    }

    function get_module_list()
    {
// print "\nmodules=";print_r($this->modules);
        if (is_array($this->modules)) return $this->modules;
        $file_list = glob($this->mScan);
// print "\nmScan---------------\n";print_r($file_list);print "\nmScan---------------\n";
        return $file_list;
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

// print "\n<br/>list="; print_r($this->get_module_list());
        foreach ($this->get_module_list() as $m) {
// print "\n<br/>m=$m";
            $M = new xml_source($m);
            $n = 0;
            while(true) {
                $n++;
                $s = $M->part_string("/$this->mRoot/$this->mItem[$n]");
                if ($s == "") break;
// print "\n<br/>s=$s";
                $x .= $s;
            }
        }

        $x .= "</$this->mRoot>\n";

        if (is_string($this->aPath) && $this->aPath != '') {
            $D = new xml_file($x);
            if (!$D->can_save($this->aPath)) print "<br/>FAILED TO SAVE MASTER LIST";
            $D->save($this->aPath);
        }

// print "\n<br/>x=$x";
        return $x;
    }
}
