<?php

date_default_timezone_set('America/New_York');
include_once('system-config.php');
include_once('class-access.php');
include_once('class-module.php');
include_once('class-iobj.php');

class juniper_source
{
	public $current_page_template;
	private $iOBJa;
	private $files;
	private $dbs;
	public  $loaded;
	public $bench;
	public $AJAX;
	public $AJAXreferer;
	function __construct()
	{
		$mtime = microtime();
		$mtime = explode(" ", $mtime);
		$mtime = $mtime[1] + $mtime[0];
		$this->bench = $mtime;
		$this->include_functions();
		$this->include_handlers();
		//print "<br/>zoSource::Construct() -- LOADING";
		//$this->backtrace();
		$this->files = array();
		$d = $this->localize(juniper_dir());
		$this->add_xml_source("SYS", new juniper_modules());
		$this->localize($d);
		$this->AJAX = false;
		$this->iOBJa = array();
		$this->include_support_files("", "php", "startup");
		if ($this->get_option("WP")) $this->initialize_wp();
		$this->loaded = true;
		//print "<br/>zoSource::Construct() -- LOADED";
	}
	function include_handlers()
	{
		include_once("handlers.php");
	}
	function include_functions()
	{
		include_once("functions.php");
	}
	function __destruct()
	{
		//print "<br/>zoSource::Destruct()";
		//$this->backtrace();
		$this->save_files();
		//print "<br/>zoSource *** Destructed ***";
		$mtime = microtime();
		$mtime = explode(" ", $mtime);
		$mtime = $mtime[1] + $mtime[0];
		$endtime = $mtime;
		$totaltime = ($endtime - $this->bench);
	}
	function localizeto($d)
	{
		$old = getcwd();
		@chdir($d);
		return $old;
	}
	function localize($d = "")
	{
		if ($d == "") return $this->localizeto($d);
		@chdir($d);
		return false;
	}
	function stat_load()
	{
		if (!$this->AJAX) echo "This form was generated in " . $totaltime . " seconds";
	}
	function stat_files()
	{
		print "<br/>zoSource Files";
		foreach ($this->files as $f) print "<br/>" . $f . "\n";
	}
	function save_files()
	{
		foreach ($this->files as $id => $f) {
			//print "<br/>zoSource::Destruct autosave - $f";
			if ($f->modified && $f->can_save()) $f->save();		// attempt save if appropriate.
			unset($f);
			unset($this->files[$id]);
		}
	}
	//	function zdb($id)
	//		{
	////print "<br/>zoSource::zdb($id)";
	//		if ($id=="wpdb") 
	//			{
	//			include_once("class-zobject-db-wpdb.php");
	//			return new zobject_db_wpdb();
	//			}
	//		if (!is_array($dbs)) $dbs = array();
	//		if ($dbs[$id]==null)
	//			{
	//			if ($this->FetchDSPart($id, "@id")!=$id) return null;
	//			$t = $this->FetchDSpart($id, "@type");
	//
	//			$db_host = $this->FetchDSPart($id, "@host");
	//			$db_user = $this->FetchDSPart($id, "@user");
	//			$db_pass = $this->FetchDSPart($id, "@pass");
	//			$db_name = $this->FetchDSPart($id, "@dbname");
	//
	////print "<br/>zoSource::zdb: type=$t, host=$db_host, user=$db_user, pass=$db_pass, name=$db_name";
	//			switch($t)
	//				{
	//				case "odbc":
	//					include_once("class-zobject-db-odbc.php");
	////					$dbs[$id] = $l;
	//					break;
	//				case "mysql":
	//					include_once("class-zobject-db-mysql.php");
	//					$l = new zobject_db_mysql($id, $db_host, $db_user, $db_pass, $db_name);
	//					if (!$l->db) unset($l); else $dbs[$id] = $l;
	//					break;
	//				default:
	//					trigger_error("Unable to connect to desired datasource [".$id."] because the type is unknown: ".$t, E_USER_WARNING);
	//				}
	//			}
	//		return $dbs[$id];
	//		}
	//
	function source_exists($id)
	{
		//print "<br/>SE: ".$id;
		if ($id == "RESULT") return $this->result() != null;
		return isset($this->files[$id]);
	}
	function force_document($id, $file)
	{
		//print "<br/>force_document($id, $file)";
		if (!$this->source_exists($id)) $this->add_xml_source($id, new xml_file($file));
		return $this->get_source($id);
	}
	function force_unknown_document($file)
	{
		//print "<br/>force_document_unkonwn($file)";
		$id = $this->add_file($file);
		return $this->get_source($id);
	}
	function load_source($id, $f)
	{
		if ($this->source_exists($id)) return true;
		$this->files[$id] = new xml_file($f);
		return true;
	}
	function add_source($id, $D)
	{
		//print "<br/>JUNIPER_SOURCE::add_source($id, ...)";
		if ($this->source_exists($id)) return die("<br/>This id already exists: $id");
		$x = new xml_file($D);
		$this->files[$id] = $x;
		return true;
	}
	function add_xml_source($id, $x)
	{
		if ($this->source_exists($id)) return die("<br/>This id already exists: $id");
		$this->files[$id] = $x;
		return true;
	}
	function remove_source($id)
	{
		//print "<br/>remove_source($id)";
		if (!$this->source_exists($id)) return false;
		unset($this->files[$id]);
	}
	function get_file_id($file)
	{
		foreach ($this->files as $k => $f) if ($f->filename == $file) return $k;
		return "";
	}
	function add_file($file)
	{
		if (($f = $this->get_file_id($file)) != "") return $f;
		$this->add_source($id = uniqid(), $file);
		return $id;
	}

	function count_iobj()
	{
		return is_array($this->iOBJa) ? count($this->iOBJa) : 0;
	}
	function add_iobj($i)
	{
		if (!$this->count_iobj()) $this->iOBJa = array();
		array_push($this->iOBJa, $i);
		return true;
	}
	function remove_iobj($i)
	{
		if ($this->count_iobj()) {
			foreach ($this->iOBJa as $a => $b) if ($i->gid() == $b->gid()) {
				unset($this->iOBJa[$a]);
				break;
			}
		}
	}
	function get_iobj($gid)
	{
		if ($this->count_iobj()) {
			foreach ($this->iOBJa as $b)
				if (method_exists($b, 'gid') && $b->gid() == $gid) return $b;
			if (($x = strlen($gid)) > 0)
				foreach ($this->iOBJa as $b)
					if (method_exists($b, 'gid') && substr($b->gid(), 0, $x) == $gid) return $b;
		}
	}
	function dump_iOBJ()
	{
		print "<br/>DUMP IOBJ, Count=" . $this->count_iobj() . "\n";
		$n = 0;
		if (!is_array($this->iOBJa)) return;
		print "<br/>iOBJ Array:";
		print_r($this->iOBJa);
		foreach ($this->iOBJa as $b)	print "<br/>" . (++$n) . ": " . (method_exists($b, 'gid') ? $b->gid() : 'UNKNOWN(' . get_class($b) . ')');
	}
	function iOBJ()
	{
		if (!$this->count_iobj()) $this->iOBJa = array();
		$x = array_pop($this->iOBJa);
		array_push($this->iOBJa, $x);
		return $x;
	}
	function iOBJ2()
	{
		if (!$this->count_iobj()) $this->iOBJa = array();
		$x = array_pop($this->iOBJa);
		$y = array_pop($this->iOBJa);
		if ($y) array_push($this->iOBJa, $y);
		if ($x) array_push($this->iOBJa, $x);
		return $y;
	}
	function result()
	{
		return ($x = $this->iOBJ()) ? $x->result : null;
	}
	function result2()
	{
		return ($x = $this->iOBJ2()) ? $x->result : null;
	}
	function resultDoc()
	{
		return ($x = $this->result()) ? $x->Doc : null;
	}
	function resultDoc2()
	{
		return ($x = $this->result2()) ? $x->Doc : null;
	}
	function get_source($id)
	{
		if ($id == "RESULT") return $this->result();
		if (($x = $this->get_iobj($id))) return $x->result;
		return $this->source_exists($id) ? $this->files[$id] : null;
	}
	function get_source_doc($id)
	{
		if (!($x = $this->get_source($id))) return null;
		return $x->Doc;
	}
	private function is_path($p)
	{
		return substr($p, 0, 2) == "//";
	}
	private function path_split(&$id, &$p)
	{
		//print "<br/>path_split($id, $p)";
		if (!$this->is_path($p)) return false;
		$p = substr($p, 2);
		$t = explode("/", $p);
		$id = $t[0];
		//print "<br/>path_split: id=$id";
		if (!$this->source_exists($id) && !$this->initialize_datasource($id))
			$this->backtrace("DATASOURCE [$id] NOT FOUND");
		$t[0] = "";
		$p = implode("/", $t);
		return true;
	}
	function node($p)
	{
		if (!$this->path_split($id, $p)) return "";
		$s = $this->get_source($id);
		return $s->fetch_node($p);
	}
	function nodes($p)
	{
		if (!$this->path_split($id, $p)) return "";
		$s = $this->get_source($id);
		return $s->fetch_nodes($p);
	}
	function def($p)
	{
		if (!$this->path_split($id, $p)) return "";
		$s = $this->get_source($id);
		return $s->part_string($p);
	}
	function get($p)
	{
		if (!$this->path_split($id, $p)) return "";
		$s = $this->get_source($id);
		return $s->fetch_part($p);
	}
	function set($p, $x)
	{
		if (!$this->path_split($id, $p)) return "";
		$s = $this->get_source($id);
		return $s->set_part($p, $x);
	}
	function lst($p)
	{
		//print "<br/>lst($p)";
		if (!$this->path_split($id, $p)) return "";
		$s = $this->get_source($id);
		return $s->fetch_list($p);
	}
	function cnt($p)
	{
		//print "<br/>cnt($p)";
		if (!$this->path_split($id, $p)) return "";
		//print "<br/>SOURCE::cnt: ID=$id, Path=$p";
		$s = $this->get_source($id);
		return $s->count_parts($p);
	}
	function FetchPagePart($PName, $El)
	{
		if ($PName == "") {
			$this->backtrace();
			return die("<br/><font style='font-weight:bold;font-size:20'>DIE:</font> <u>No ZName in FetchPagePart</u> El=$El");
		}
		if ($El   == "") {
			$this->backtrace();
			return die("<br/><font style='font-weight:bold;font-size:20'>DIE:</font> <u>No El in FetchPagePart</u>");
		}
		return $this->get("//SYS/*/pagedef[@id='$PName']/$El");
	}
	function FetchPageDefString($PName)
	{
		$k = $this->def("//SYS/*/pagedef[@id='$PName']");
		if ($k == "" && $PName == "") $k = $this->def("//SYS/*/pagedef[@default!='']");
		return k;
	}
	function ObjectList()
	{
		return $this->lst("//SYS/*/zobjectdef/@name");
	}
	function FetchDSPart($ZID, $El)
	{
		//print "<br/>FetchDSPart($ZID, $El)";
		if ($ZID == "") {
			$this->backtrace();
			die("<br/><font style='font-weight:bold;font-size:20'>DIE:</font> <u>No ZID in FetchDSPart</u> El=$El");
		}
		return $this->get("//SYS/*/datasource[@id='$ZID']/$El");
	}
	function FetchObjPart($ZName, $El)
	{
		//print "<br/>FetchObjPart($ZName, $El)";
		if ($ZName == "") {
			$this->backtrace();
			die("<br/><font style='font-weight:bold;font-size:20'>DIE:</font> <u>No ZName in FetchObjPart</u> El=$El");
		}
		return $this->get("//SYS/*/zobjectdef[@name='$ZName']/$El");
	}
	function FetchObjFieldPart($ZName, $fid, $El)
	{
		//print "<br/>FetchObjFieldPart($ZName, $fid, $El)";
		if ($ZName == "") {
			$this->backtrace();
			die("<br/><font style='font-weight:bold;font-size:20'>DIE:</font> <u>No ZName in FetchObjFieldPart</u>, fid=$fid, El=$El");
		}
		if ($fid == "") {
			$this->backtrace();
			die("<br/><font style='font-weight:bold;font-size:20'>DIE:</font> <u>No fid in FetchObjFieldPart</u>, ZName=$ZName, El=$El");
		}
		$t = $this->get("//SYS//zobjectdef[@name='$ZName']/fielddefs/fielddef[@id='$fid']/$El");
		return $t;
	}
	function FetchObjDefString($ZName)
	{
		if ($ZName == "") {
			$this->backtrace();
			die("<br/><font style='font-weight:bold;font-size:20'>DIE:</font> <u>No ZName in FetchObjDefString</u>");
		}
		return $this->def("//SYS//zobjectdef[@name='$ZName']");
	}
	function FetchObjFields($ZName)
	{
		//print "<br/>FetchObjFields($ZName)";
		if ($ZName == "") {
			$this->backtrace();
			die("<br/><font style='font-weight:bold;font-size:20'>DIE:</font> <u>No ZName in FetchObjFields</u>");
		}
		return $this->lst("//SYS//zobjectdef[@name='$ZName']/fielddefs/fielddef/@id");
	}
	function FetchObjFieldDefault($ZName, $fid)
	{
		if ($ZName == "") {
			$this->backtrace();
			die("<br/><font style='font-weight:bold;font-size:20'>DIE:</font> <u>No ZName in FetchObjFieldDefault</u>");
		}
		if ($fid == "") {
			$this->backtrace();
			die("<br/><font style='font-weight:bold;font-size:20'>DIE:</font> <u>No fid in FetchObjFieldDefault</u>, ZName=$ZName");
		}
		$d = FetchObjFieldPart($ZName, $fid, "@default");
		return $this->php_hook($d);
	}
	function FetchObjFieldCategories($ZName)
	{
		if ($ZName == "") {
			$this->backtrace();
			die("<br/><font style='font-weight:bold;font-size:20'>DIE:</font> <u>No ZName in FetchObjFieldCategories</u>");
		}
		//print "<br/>FetchObjFieldCategories($ZName)";
		$k = array();

		foreach ($this->FetchObjFields($ZName) as $l) {
			$cat = $this->FetchObjFieldPart($ZName, $l, "@category");
			if ($cat == "") $cat = "general";
			$k[$cat] = 1;
		}
		//print_r($k);
		$x = "<categories>";
		if (isset($cat["general"])) {
			unset($k["general"]);
			$x = $x . "<category>general</category>";
		}
		foreach ($k as $l => $a) $x = $x . "<category>" . $l . "</category>";
		$x = $x . "</categories>";
		//print "<br/>".ESK($x);

		$D = new DOMDocument;
		$D->loadXML($x);
		return $D;
	}
	function FetchActPart($Act, $El = "")
	{
		//print "<br/>FetchActPart($Act, $El)";
		if ($Act == "") {
			$this->backtrace();
			die("<br/><font style='font-weight:bold;font-size:20'>DIE:</font> <u>No Act in FetchActPart</u>");
		}
		return $this->get("//SYS/*/zactiondef[@name='$Act']" . ($El == "" ? "" : "/$El"));
	}

	function FetchActRulePart($Act, $R, $El)
	{
		//print "<br/>FetchActRulePart($Act, $R, $El)";
		if ($Act == "") {
			$this->backtrace();
			die("<br/><font style='font-weight:bold;font-size:20'>DIE:</font> <u>No Act in FetchActRulePart($Act, $R, $El)</u>");
		}
		//	if ($R=="") die("<br/><font style='font-weight:bold;font-size:20'>DIE:</font> <u>No Rule in FetchActRulePart($Act, $R, $El)</u>");
		return $this->get("//SYS/*/zactiondef[@name='$Act']/action[@value='$R']" . ($El == "" ? "" : "/") . "$El");
	}

	function FetchDTPart($T, $El)
	{
		//print "<br/>FetchDTPart($T, $El)";
		//	if ($T=="") die("<br/><font style='font-weight:bold;font-size:20'>DIE:</font> <u>No data-type in FetchDTPart</u>");
		return $this->get("//SYS/*/typedef[@name='$T']/$El");
	}
	public static function FetchSpecPart($Module, $El)
	{
		if ($Module == "") {
			juniper()->backtrace("<br/><font style='font-weight:bold;font-size:20'>DIE:</font> <u>No Module in FetchSpecPart</u> El=$El");
		}
		if ($El   == "") {
			juniper()->backtrace("<br/><font style='font-weight:bold;font-size:20'>DIE:</font> <u>No El in FetchSpecPart</u>");
		}
		//print "<br/>juniper_module::FetchSpecPart($Module, $El), Q: //SYS/*/module-specification[@name='$Module']/$El";
		return juniper()->get("//SYS/*/module-specification[@module='$Module']/$El");
	}
	function initialize_wp()
	{
		foreach ($this->lst("//SYS/*/module-specification/components/element/@name") as $el)
			add_shortcode($el, 'zobjects_handle_shortcodes');
	}
	function initialize_datasource($id)
	{
		if ($this->get("//SYS/*/datasource[@id='$id']/@type") <> "xml") return false;
		$r = $this->get("//SYS/*/datasource[@id='$id']/@src");
		return $this->add_xml_source($id, new xml_file(FilePath('', $r)));
	}
	function include_styles()
	{
		$this->include_support_files('', 'css');
	}
	function include_scripts()
	{
		$this->include_support_files('', 'js');
	}
	function get_scripts()
	{
		$s = '<list>';
		foreach ($this->nodes("//SYS/*/file[@type='js']") as $n) {
			$f = $n->getAttribute('src');
			$m = $n->getAttribute('module');
			$s .= "<script type='" . DetectScriptType($f) . "' src='" . ExtendURL(juniper_module_url("$m/$f"), '', true) . "'>&#160;</script>\n";
		}
		$s .= "</list>";
		//print $s;
		return xml_file::XMLToDoc($s);
	}
	function get_styles()
	{
		$s = '<list>';
		foreach ($this->nodes("//SYS/*/file[@type='css']") as $n) {
			$f = $n->getAttribute('src');
			$m = $n->getAttribute('module');
			$s .= "<link type='text/css' rel='stylesheet' href='" . ExtendURL(juniper_module_url("$m/$f"), '', true) . "' />\n";
		}
		$s .= "</list>";
		//print $s;
		return xml_file::XMLToDoc($s);
	}
	function include_support_files($module = '', $type = 'php', $mode = '', $file_id = '')
	{
		//print "<br/> zosource::include_support_files($module, $type, $mode, $file_id)\n";
		//if ($module=="zsite" && $type="php") $this->backtrace("where");
		if ($file_id != "") {
			$p = "//SYS/*/file[@id='$file_id']";
			if ($module != "") $p .= "[@module='$module']";
			if ($type != "")   $p .= "[@type='$type']";
			if ($mode != "")   $p .= "[@mode='$mode']";
			$s = $this->get($p . '/@src');
			//print "<br/>include_support_files, SPECIFIC FILE: $file_id, p=$p/@src, s=$s";
			$f = juniper_module_dir("/$module/$s");
			$F = juniper_module_url("/$module/$s");
			//print "<br/>$f";
			switch ($type) {
				case "php":
					//print "<br/>SUPPORT FILE: including PHP file: ".$f;
					include_once($f);
					break;
				case 'css':
					wp_register_style($itm, $F);
					wp_enqueue_style($itm);
					break;
				case 'js':
					if ($d != "") $da = explode($d, ",");
					else $da = array();
					//					wp_register_script( $itm, $F, $da);
					wp_register_script($itm, $F);
					wp_enqueue_script($itm);
					break;
			}
			return;
		}

		if ($module == "") {
			//print "<br/>Modules: ";print_r(array_unique ( $this->lst("//SYS/*/file/@module") ));
			foreach (array_unique($this->lst("//SYS/*/file/@module")) as $m)
				if ($m != "") $this->include_support_files($m, $type, $mode);
			return;
		}
		$p = "//SYS/*/file[@module='$module']/@id";
		//print_r($this->lst($p));
		foreach ($this->lst($p) as $itm) {
			$t = $this->get("//SYS/*/file[@module='$module'][@id='$itm']/@type");
			if ($type != "" && $t != $type) continue;
			$s = $this->get("//SYS/*/file[@module='$module'][@id='$itm']/@src");
			$m = $this->get("//SYS/*/file[@module='$module'][@id='$itm']/@mode");
			if ($mode != "" && $mode != $m) continue;
			$f = juniper_module_dir("/$module/$s");
			$F = juniper_module_url("/$module/$s");
			$d = $this->get("//SYS/*/file[@module='$module'][@id='$itm']/@dependancies");
			//print "<br/>zosource..  checking support file: $f ($t)\n";
			if (file_exists($f)) {
				//print "<br/>zosource::include_support files:  $itm ($t): $f [$d]\n";
				switch ($t) {
					case 'php':
						include_once($f);
						break;
					case 'css':
						wp_register_style($itm, $F);
						wp_enqueue_style($itm);
						break;
					case 'js':
						if ($d != "") $da = explode($d, ",");
						else $da = array();
						//						wp_register_script( $itm, $F, $da);
						wp_register_script($itm, $F);
						wp_enqueue_script($itm);
						break;
					default:
						break;
				}
			}
		}
	}
	function get_option($f)
	{
		switch (strtoupper($f)) {
			case "WP":
				return defined("WP_CONTENT_DIR") ? "1" : "0";
			case "ADMIN":
				return YesNoVal(juniper_querystring::get_querystring_var("_ADMIN"), false) ? "1" : "0";
			case "SUBZ":
				return YesNoVal(juniper_querystring::get_querystring_var("_ADMIN"), false) ? "1" : "0";
			default:
				return "";
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////
	function empty_node_list()
	{
		$D = new DOMDocument;
		$D->loadXML("<?xml version='1.0' ?>\n<div></div>");
		return $D;
	}
	function is_php_hook($f)
	{
		return is_string($f) && substr($f, 0, 4) == "php:";
	}
	function php_hook_has_args($f)
	{
		return $this->is_php_hook($f) && strpos($f, ",") !== false;
	}
	function php_hook($f, $args = "", $callarray = false)
	{
		//print "<br/>zoSource::php_hook($f, $args)";
		if (!$this->is_php_hook($f)) return $f;
		if ($args == "") $args = iOBJ()->args;
		$s = substr($f, 4);
		$S = split(",", $s);
		$s = $S[0];
		if (!is_callable($s)) {
			print "<br/>php_hook [" . $s . "] is not callable.";
			$this->backtrace();
			return "";
		}
		if (count($S) > 1) {
			$a = array();
			$n = 0;
			foreach ($S as $ss) {
				if (++$n == 1) continue;
				if ($ss[0] != "@") $v = $ss;
				else {
					$sst = substr($ss, 1);
					if (is_array($args)) $v = $args[$sst];
					else $v = juniper_querystring::get_querystring_var($args, $sst);
				}
				$a[] = $v;
			}
			$args = $a;
			//print "<br/>";print_r($a);
			$callarray = true;
		}
		//print "<br/>php_hook.......$s, callarray=".($callarray?"Y":"N").", is_array(args)?".(is_array($args)?"Y":"N").", v=".((!$callarray && is_array($args))?"Y":"N");print_r($args);
		if (!$callarray && is_array($args))
			$x = call_user_func($s, $args);
		else
			$x = call_user_func_array($s, $args);
		return $x;


		if (strpos($s, "return") === false) $s = "return $s";
		if (strpos($s, "(") === false) $s = $s . '($args)';
		if (substr($s, strlen($s) - 1) != ";") $s = "$s;";
		//print "<br/>zoSource::php_hook($f) ==>> $s";
		$x = eval($s);
		//print "<br/>php_hook result: $x";
		//  catch errors?
		//		if ($x === false)
		//			{
		//			print "<br/><span style='background:red;color:white;'>WARNING: </span>$e->getMessage()<br/>";
		//			$this->backtrace();
		//			$x = null;
		//			}
		return $x;
	}
	function KeyValue($k, $Args = "", $alt = "")
	{
		//print "<br/>KeyValue($k, $Args, $alt)\n";
		//		if ($k=='#USERNAME') return GetCurrentUsername();
		$v = @$_REQUEST[$k];
		if ($Args == "" && $this->iOBJ() != null) $Args = $this->iOBJ()->args;
		//print "<br/>args=$Args";
		if ($v == "" && $Args != "") $v = juniper_querystring::get_querystring_var($Args, $k);
		if ($v == "" && $this->iOBJ()) $v = $this->iOBJ()->arg($k);
		if ($v == "" && $this->iOBJ() && method_exists($this->iOBJ(), 'result_field')) $v = $this->iOBJ()->result_field($k);
		if ($v == "" && $this->iOBJ2()) $v = $this->iOBJ2()->arg($k);
		if ($v == "" && $this->iOBJ2() && method_exists($this->iOBJ2(), 'result_field')) $v = $this->iOBJ2()->result_field($k);		// previous object...  ?
		if ($v == "" && $alt != "") $v = $alt;
		//print "<br/>KeyValue($k, $Args, $alt) == $v";
		return $v;
	}
	function InterpretFields($f, $auto_quote = false, $token = "@")
	{
		$counter = 0;
		//print "<br/>InterpretFields($f, $auto_quote, $token)";

		$l = strlen($token);
		if ($auto_quote)
			$cb = create_function('$matches', "return \"'\".juniper()->KeyValue(substr(\$matches[0],$l)).\"'\";");
		else
			$cb = create_function('$matches', "return juniper()->KeyValue(substr(\$matches[0],$l));");
		$f = preg_replace_callback('/' . $token . "[a-zA-Z0-9_]+" . '/i', $cb, $f);
		//print "<br/>InterpretFields: $f";
		return $f;
	}
	function backtrace($die_msg = "")
	{
		if (!function_exists("debug_backtrace")) return;
		$t = debug_backtrace();
		foreach ($t as $a) {
			@print "<br/><b>" . $a['file'] . "-line #" . $a['line'] . ":</b> " . $a['function'] . "(" . implode(',', $a['args']) . ")\n";
		}
		if ($die_msg != "") die("<br/>" . $die_msg);
	}
	function log_request($type = "")
	{
		switch ($type) {
			case "ajax":
				$f = $f = juniper_dir("/_request-ajax.adj");
				break;
			default:
				$f = $f = juniper_dir("/_request.adj");
				break;
		}
		@file_put_contents($f, juniper_current_page() . "\n", FILE_APPEND);
		if (@$_REQUEST['_ZA'] != '') {
			file_put_contents($f, "     ZA: " . juniper_current_page(true) . "&" . $this->decode_args($_REQUEST['_ZA']) . "\n", FILE_APPEND);
			file_put_contents($f, "    _ZA=" . $this->decode_args($_REQUEST['_ZA']) . "\n", FILE_APPEND);
			if (count($_POST)) file_put_contents($f, "   POST: " . juniper_current_page(false) . "?" . http_build_query($_POST) . "\n", FILE_APPEND);
		}
	}
	function args_prefix()
	{
		return '@@';
	}
	//BASE64 characters are.... including trailing pad (65):  "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/="
	function encode_args($a)
	{
		return $this->args_prefix() . base64_encode(str_rot13($a));
	}
	function decode_args($a)
	{
		//print "<br/>decode_args($a)";
		$p = $this->args_prefix();
		$n = strlen($p);
		// this is the only real algorithm... as long as it matches the encode and is reversible, it is fine to change...
		//print "<br/>substr($a,0,$n)";
		if (substr($a, 0, $n) == $p) return str_rot13(base64_decode(substr($a, $n)));
		//print "<br/>;lkj;lj.........";
		// it may have been urlencode'd somewhere...
		$S = urlencode($p);
		$m = strlen($S);
		if (substr($a, 0, m) == $S) return $this->decode_args(urldecode($a));
		// otherwise, decoding an unencoded string does nothing!
		return $a;
	}
	function Warning($m, $s = "source unkonwn", $endresponse = false)
	{
		print "<br/><span style='background:red;color:white;'>WARNING: </span>$s: $m<br/>\n";
		if ($endresponse) die();
		return false;
	}
	function HandledElementsArray()
	{
		return $this->lst("//SYS/*/module-specification/components/element/@name");
	}
	function HandledElements()
	{
		return "," . implode(",", $this->HandledElementsArray()) . ",";
	}
	function ElementIsHandled($e)
	{
		return in_array($e, $this->HandledElementsArray());
	}
	function PerfectElementsArray()
	{
		return $this->lst("//SYS/*/module-specification/components/perfect/@name");
	}
	function PerfectElements()
	{
		return "," . implode(",", $this->PerfectElementsArray()) . ",";
	}
	function ElementIsPerfect($e)
	{
		return in_array($e, $this->PerfectElementsArray());
	}
	function PerfectHandler($name, $node)
	{
		//print "<br/>PerfectHandler($name, ...)";
		//print "<br/>".get_class($node);
		if (is_array($node)) $node = $node[0];
		$a = array();
		$s = $this->nodes("//SYS/*/module-specification/components/perfect[@name='$name']");
		foreach ($s as $n) $a[$n->getAttribute("render")] = (intval($n->getAttribute('priority')) || 50);
		asort($a);
		//print_r($a);
		$p = array('name' => $name, 'node' => $node);
		//print_r($p);
		foreach ($a as $func => $priority) {
			$x = $this->php_hook($func, $p);
			if ($x) $node = $x;
		}
		//print "<br/>".get_class($node);
		return $node;
	}
	function NodeHandler($n, $node)
	{
		//print "<br/>NodeHandler($n, ...)";
		//$s = $node->ownerDocument->saveXML($node);print "<br/>NodeHandler($n, ...):". $s;
		if (is_array($node)) $node = $node[0];
		//print_r($node);
		$a = array();
		foreach ($node->attributes as $att) $a[$att->nodeName] = $att->nodeValue;
		$args = $node->getAttribute('args');
		if ($args != '') {
			parse_str($args, $b);
			$a = array_merge($a, $b);
		}
		//		$a['_node_string_complete'] = xml_file::NodeToString($node, 'all');
		//		$a['_node_string_opening']  = xml_file::NodeToString($node, 'open');
		//		$a['_node_string_closing']  = xml_file::NodeToString($node, 'close');
		//		$a['_node_string_contents'] = xml_file::NodeToString($node, 'contents');
		$a['_node'] = $node;
		//print "<br/>render params: ";print_r($a);
		return $this->render($n, $a);
	}
	function ElementObject($object, $asString = false, $load_support = true)
	{
		$el = $this->get("//SYS/*/module-specification/components/element[@name='$object']/@name");
		if (!$el) return $this->Warning("Item Name [$object] cannot be found.", "zoSource::ElementObject");
		$module = $this->get("//SYS/*/module-specification[components/element/@name='$object']/@module");
		if ($load_support && $module) $this->include_support_files($module, "php", "autoload");
		$srf = $this->get("//SYS/*/module-specification/components/element[@name='$object']/@src");
		if ($srf != "") $this->include_support_files($module, "php", "", $srf);
		//		$src = "";
		//		if ($asString)
		//			$src = $this->get("//SYS/*/module-specification/components/element[@name='$object']/@renderstring");
		//		if ($src=="")
		$src = $this->get("//SYS/*/module-specification/components/element[@name='$object']/@render");
		if ($this->is_php_hook($src)) return $src;
		if (class_exists($src)) return new $src;

		return $this->Warning("Item Render [$src] for element [$object] does not exist", "zoSoruce::ElementObject");
	}
	function render($type, $params = null, $arg_string = '', $asString = false)
	{
		//print "<br/>juniper::render($type, ".param_string($params).", $arg_string, $asString)";
		$d = $this->localize(juniper_dir());
		$o = $this->ElementObject($type);

		if (is_object($o)) {
			//print "<br/>juniper::render(), iOBJ(".get_class($o).")";
			$this->add_iobj($o);
			ob_start();
			$res = $o->render($params, $arg_string);
			$res_buff = ob_get_clean();
			print $res_buff;
			$this->remove_iobj($o);
			//print "<hr/>$res<hr/>";
			if (is_null($res)) {
				if ($res_buff != '') $res = $res_buff;
			}
		} else if ($this->is_php_hook($o)) {
			//print "<br/>NodeRender, PHP=" . $o;
			$this->dump_iOBJ();
			if ($this->php_hook_has_args($o)) {
				$a = "?";
				foreach ($params as $b => $c) $a .= "&" . $b . "=" . $c;
				//print "<br/>a=$a";
				ob_start();
				$res = $this->php_hook($o, $a);
				$res_buff = ob_get_clean();
				print $res_buff;
				if (is_null($res)) {
					if ($res_buff != '') $res = $res_buff;
					else if ($params) $res = xml_file::DocElToDoc($params['_node']);
				}
			} else {
				$b = @array(0 => $a['name'], 1 => $a['mode'], 2 => $a['args']);
				ob_start();
				$res = $this->php_hook($o, $b, true);
				$res_buff = ob_get_clean();
				print $res_buff;
				if (is_null($res)) {
					if ($res_buff != '') $res = $res_buff;
					else if ($params) $res = xml_file::DocElToDoc($params['_node']);
				}
			}
		} else
			$res = $this->empty_node_list();
		if ($asString) {
			if (is_object($res)) $res = $res->saveXML();
			return $res;
		}
		if (is_string($res) && $res != '') $res = xml_file::XMLToDoc($res);
		if ($res) $res = $this->ProcessDocumentElements($res);
		//if ($type=="dashboard")
		//{$X = new xml_file($res);print $X;print $X->saveXML();}
		$this->localize($d);
		return $res;
	}
	function ProcessDocumentElements($D)
	{
		//print "<br/>ProcessDocumentElements(...)";
		return xml_file::transform_static($D, juniper_dir('include/render-nodes.xsl'));
	}
	function ProcessDocumentPerfect($D)
	{
		//		return $D;
		//print "<br/>ProcessDocumentElements(...)";
		return xml_file::transform_static($D, juniper_dir('include/perfect.xsl'));
	}
	function execute($name = "", $mode = "", $args = "")
	{
		$d = $this->localize(juniper_dir());
		$o = $this->ElementObject($name);
		if ($o) $result = $o->execute($name, $mode, $args);

		$this->localize($d);
		return $result;
	}
	function xsave()
	{
		$d = $this->localize(juniper_dir());
		$o = $this->ElementObject($name);
		if ($o) $result = $o->save($zn = "", $zm = "", $args = "");

		$this->localize($d);
		return $result;
	}
	function save($zn = "", $zm = "", $args = "")
	{
		$d = $this->localize(juniper_dir());
		//print "<br/>zoSource::save($zn, $zm, $args)";
		if ($args == "") $args = $this->decode_args(@$_REQUEST['_ZA']);
		if ($zn == "") $zn = @$_REQUEST['_ZN'];
		if ($zn == "") $zn = juniper_querystring::get_querystring_var($args, '_ZN');
		if ($zm == "") $zm = @$_REQUEST['_ZM'];
		if ($zm == "") $zm = juniper_querystring::get_querystring_var($args, '_ZM');
		//print "<br/>zoSource::save($zn, $zm, $args)";
		$fsc = @$_REQUEST['_FSC'];
		if (!$this->form_source_check("juniper", $fsc)) die("Security Check failed, FSC: $fsc");
		include_once("modules/zobject/components/class-zobject.php");  // ###  Serialize
		$x = new zobject();
		$Target = $x->save($zn, $zm, $args);
		unset($x);
		if (!$this->AJAX) {
			$this->save_files();
			if ($Target == "") $Target = juniper_current_page();
			//$this->stat_files();
			//print "<br/>Target=$Target";die();
			header("Location: $Target");
			die();
		}
		$this->localize($d);
		return true;
	}

	function form_source_check($a = "juniper", $v = "")
	{
		if ($this->get_option("ISWP")) {
			if ($v) return wp_verify_nonce($v, $a);
			return wp_create_nonce('$a');
		}
		return "$a-" . uniqid();
	}
	function remote_validation_url($fid)
	{
		if (!is_php_hook($this->FetchObjFieldPart(iOBJ()->name, $fid, "@remote"))) return "";
		$s = $this->ajaxURL('validate', "?action=juniper-validate-field&_ZN=" . iOBJ()->name . "&F=$fid");
		return $s;
	}
	function remote_validation()
	{
		if (($zn = $_REQUEST['_ZN']) == "") die("false");
		if (($f = $_REQUEST['F']) == "") die("false");
		$r = $this->FetchObjFieldPart($zn, $f, "@remote");
		if ($r == "") die("true");				// no validation means all is ok
		$m = $this->FetchObjPart($zn, "@module");
		if ($m) $this->include_support_files($m);
		$x = $this->php_hook($r);
		if (!x) die("false");
		if (is_string($x)) {
			if ($x[0] != "\"") $x = '"' . str_replace('"', '\"', $x) . '"';
			die($x);
		}
		die("true");
	}
	function ajaxToken($a)
	{
		if ($this->AJAX) $c = $this->AJAXreferer;
		else $c = juniper_current_page();
		$r = $this->get_timedkey($a) . $c;
		$r = $this->encode_args($r);
		return $r;
	}
	function ajaxURL($action, $q = '')
	{
		if (substr($q, 0, 1) != '?') $q = '?' . $q;
		$q = juniper_querystring::add($q, 'ajax-action', $action);
		$q = juniper_querystring::add($q, '_token', $this->ajaxToken($action));
		//print "<br/>ajaxURL, qs: $q";
		return juniper_url("/juniperAjax.php$q");
	}
	function ajax()
	{
		$this->AJAX = true;
		$f = $_REQUEST;
		//print "<br/>juniper::ajax()";print_r($f);
		if (($a = @$f['ajax-action']) == '') return false;
		$token = @$f['_token'];
		if ($token == "") return "<div>AJAX Security Check Failed, ErrNoToken.</div>";
		//print "<br/>ajax token=$token";
		$token = $this->decode_args($token);
		//print "<br/>ajax token=$token";
		$timedkey = substr($token, 0, $this->timedkey_length());
		//print "<br/>timedkey=$timedkey";
		$referer = substr($token, $this->timedkey_length());
		if (substr($referer, 0, 4) != "http")  return "<div>AJAX Security Check Failed, ErrNoReference.</div>";
		if ($a != "create-nonce")
			if (!$this->verify_timedkey($timedkey, $a)) return "<div>AJAX Security Check Failed, ErrFailedCheck.</div>";
		$this->AJAXreferer = $referer;
		$f['_referer'] = $referer;
		unset($f['_token']);
		//print "<br/>ajax OK";
		if ($this->ElementIsHandled($a)) {
			$s = $this->decode_args(@$f['src']);
			$s = str_replace(',', '&', $s);
			//print "<br>ajax..  s=$s";
			parse_str($s, $params);
			$t = $this->decode_args(@$f['args']);
			return $this->render($a, $params, $t, true);
		}
		$render = $this->get("//SYS/*/module-specification/components/ajax[@action='$a']/@render");
		//print "<br/>render=$render";
		if (!$render) return "<div>Unrecognized AJAX request: $a</div>";
		if (!$this->is_php_hook($render)) juniperDie("Invalid AJAX Render: " . $render);
		$res = $this->php_hook($render, $f);

		if (!is_string($res)) juniperDie("AJAX Renderer did not return a string.");
		return $res;
	}
	private function salt_numeral($hash)
	{
		for ($i = 0, $n = strlen($hash); $i < $n; $i++) {
			$o = substr($hash, $i, 1);
			$n = ($n * $o) % SALT_CT;
		}
		return $n + 1;
	}
	private function get_salt($type, $purpose)
	{
		$data = $type . "." . $purpose;
		$hash = hash_hmac('md5', $data, SALT_00);
		$n = $this->salt_numeral($hash);
		return constant('SALT_' . str_pad($n, 2, '0', STR_PAD_LEFT));
	}
	private function timedkey_timer($duration = 12)
	{
		return intval(time() / (3600));
	}
	private function timedkey_length()
	{
		return 10;
	}
	private function timedkey_part($hash)
	{
		return substr($hash . $hash . $hash . $hash, strlen($hash) * 3 - $this->salt_numeral($hash), $this->timedkey_length());
	}
	function get_timedkey($action = 'none')
	{
		//print "<br/>get_timedkey($action)";
		$salt = $this->get_salt('timedkey', $action);
		//print "<br/>salt=$salt";
		//print "<br/>timer=".$this->timedkey_timer();
		$data = $action . "," . $this->timedkey_timer() . "," . "uid";
		//print "<br/>data=$data";
		$hash = hash_hmac('md5', $data, $salt);
		//print "<br/>hash=$hash";
		$part = $this->timedkey_part($hash);
		//print "<br/>part=$part";
		//print "<br/>";
		return $part;
	}
	function verify_timedkey($timedkey, $action, $duration = 12)
	{
		//print "<br/>verify_timedkey($timedkey, $action, $duration)";
		$salt = $this->get_salt('timedkey', $action);
		//print "<br/>salt=$salt";
		$nt = $this->timedkey_timer();
		//print "<br/>nt=$nt";
		for ($i = 0; $i < $duration; $i++) {
			//print "<br/>Check Hour: -".$i;
			$data = $action . "," . ($nt - $i) . "," . "uid";
			//print "<br/>data=$data";
			$hash = hash_hmac('md5', $data, $salt);
			//print "<br/>hash=$hash";
			$check = $this->timedkey_part($hash);
			//print "<br/>check=$check";
			if ($timedkey == $check) return true;
		}
		return false;
	}
}  //  CLASS: juniper_source
/////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////
function juniperDie($m)
{
	if (function_exists("debug_backtrace")) {
		$t = debug_backtrace();
		foreach ($t as $a)
			@print "<br/><b>" . $a['file'] . "-line #" . $a['line'] . ":</b> " . $a['function'] . "(" . implode(',', $a['args']) . ")\n";
	}
	print "<br/>";
	print "<br/>$m";
	die();
}
function juniper()
{
	global $_junperSource;
	if (!isset($_junperSource)) $_junperSource = new juniper_source;
	return $_junperSource;
}
function zoGet($p)
{
	return juniper()->get($p);
}
function zoSet($p, $v)
{
	return juniper()->set($p, $v);
}
function zoCnt($p)
{
	return juniper()->cnt($p);
}
function zoLst($p)
{
	return juniper()->lst($p);
}
function iOBJ()
{
	return juniper()->iOBJ();
}
function iOBJ2()
{
	return juniper()->iOBJ2();
}
