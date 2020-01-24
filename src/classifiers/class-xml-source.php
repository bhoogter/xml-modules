<?php

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
		$this->file = null;
		return false;
	}

	function load($src = '') { $this->file = new xml_file($src); return true; }
	function loadXML($xml) { $this->file = new xml_file($xml); return true; }
	function save($dst = '', $style = 'auto') { return $this->file != null ? $this->file->save($dst, $style) : false; }
	function saveXML($style = 'auto') { return $this->file != null ? $this->file->saveXML($style) : false; }
	function can_save() { return $this->file != null ? $this->file->can_save() : false; }

	function nde($p) { return $this->file != null ? $this->file->nde($p) : false; }
	function nds($p) { return $this->file != null ? $this->file->nds($p) : false; }
	function def($p) { return $this->file != null ? $this->file->def($p) : false; }
	function get($p) { return $this->file != null ? $this->file->get($p) : false; }
	function set($p, $v) { return $this->file != null ? $this->file->set($p, $v) : false; }
	function lst($p) { return $this->file != null ? $this->file->lst($p) : false; }
	function cnt($p) { return $this->file != null ? $this->file->cnt($p) : false; }
	function del($p) { return $this->file != null ? $this->file->del($p) : false; }
}
