<?php

require_once("phar://" . __DIR__ . "/../class-xml-file.phar/src/class-xml-file.php");
require_once(__DIR__ . "/class-source-classifier.php");

class xml_merge extends xml_source
{
    private $aPath;

    function type() { return "xml_merge"; }
    function save($dst = '', $style = 'auto') { }
    function load($src = '')
    {
        if ($this->NeedsUpdate()) {
            //print "<br />Reloading Modules...";
            $this->JoinModules();
        }

        $this->load($this->aPath);
    }

    private function ModuleList()
    {
        //print "<br/>ModuleList(), dir=" . juniper_module_dir();
        $h = opendir(juniper_module_dir());
        if ($h === false) echo "<br />No such directory: " . juniper_module_dir();
        $a = array();
        while (!(($f = readdir($h)) === false)) if (juniper_module::ModuleExists($f)) $a[] = $f;
        //print "<br/>modulecount=" . count($a);
        closedir($h);
        return $a;
    }

    private function NeedsUpdate()
    {
        $sysTime = 0;
        $sysTime = @filemtime($this->aPath);
        if (!$sysTime) return true;
        //print "<br/>sysTime=$sysTime, aPath=$this->aPath";
        //print_r($this->ModuleList());
        foreach ($this->ModuleList() as $m)    if (@filemtime(juniper_module::ModuleFile($m)) > $sysTime) return true;
    }

    private function JoinModules()
    {
        $Modules = array();
        $scan = array();
        //print_r($this->ModuleList());
        foreach ($this->ModuleList() as $m) {
            //print "<br/>m=$m";
            $M = new juniper_module($m);
            $M->load_module($m);
            $Modules[] = $M;
            //print "<br/>m=$m";
            foreach ($M->fetch_list('//module/module-specification/components/scan/@name') as $scan_item)
                $scan[] = $scan_item;
        }
        //print_r($scan);
        $x  = "";
        $x .= "<?xml version='1.0' encoding='iso-8859-1'?>\n";
        $x .= "<module>\n";

        foreach ($Modules as $M) {
            $MID = "module='" . $M->ID . "'";
            $x .= str_replace("<module-specification", "<module-specification $MID", $M->part_string("//module/module-specification"));
            foreach ($scan as $scan_item) {
                //print "<br/>scan_item=$scan_item..  ";print_r($M->part_string_list("//module/$scan_item"));
                foreach ($M->part_string_list("//module/$scan_item") as $item) {
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
}
