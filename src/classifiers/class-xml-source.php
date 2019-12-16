<?php

require_once("phar://" . __DIR__ . "/../class-xml-file.phar/src/class-xml-file.php");
require_once(__DIR__ . "/class-source-classifier.php");

class xml_source extends source_classifier
	{
	public $file;
    function type() { return "xml_source"; }

	function __construct()
	{
		$this->ID = "XMLFILE_" . uniqid();
		$n = func_num_args();
		$a = func_get_args();
		if ($n >= 1) $this->file = new xml_file($a[0]);
	}

	public function clear()
	{
		$this->Clear();

		$this->ID  = "";
		$this->longdesc = "";
		$this->shortdesc = "";
		$this->version = "";
		$this->file = new xml_file();
		return false;
	}

	function load($src = '') { return $this->load($src); }
	function save($dst = '', $style = 'auto') { return $this->save(); }

	function nde($p) { return $this->nde($p); }
	function nds($p) { return $this->nds($p); }
	function def($p) { return $this->def($p); }
	function get($p) { return $this->get($p); }
	function set($p, $v) { return $this->set($p, $v); }
	function lst($p) { return $this->lst($p); }
	function cnt($p) { return $this->cnt($p); }
}
