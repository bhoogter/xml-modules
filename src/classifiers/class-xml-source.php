<?php

require_once("phar://" . __DIR__ . "/../class-xml-file.phar/src/class-xml-file.php");
require_once("class-source-classifier.php");

class xml_source extends xml_file implements source_classifier
	{
	public $ID;
	public $longdesc;
	public $shortdesc;
	public $version;

	public function clear()
		{
		parent::Clear();
		
		$this->ID  ="";
		$this->longdesc = "";
		$this->shortdesc = "";
		$this->version = "";
		return false;
		}

	function type() { return "xml"; }

	function load($src = '') { return parent::load($src); }
	function save($dst = '', $style = 'auto') { return parent::save(); }

	function nde($p) { return parent::nde($p); }
	function nds($p) { return parent::nds($p); }
	function def($p) { return parent::def($p); }
	function get($p) { return parent::get($p); }
	function set($p, $v) { return parent::set($p, $v); }
	function lst($p) { return parent::lst($p); }
	function cnt($p) { return parent::cnt($p); }
}
