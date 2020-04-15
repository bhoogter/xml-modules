<?php

class source extends xml_file_base
{
    private static $instance;
    public static function instance() { return self::$instance != null ? self::$instance : (self::$instance = new source()); }
    private $bench, $totaltime;
    private $sources;
    public  $loaded;

    function __construct()
    {
        date_default_timezone_set('America/New_York');
        $this->bench = $this->milliseconds();
        php_logger::log("zoSource::Construct() -- LOADING");
		$this->sources = array();
        $d = $this->localize($this->config_dir());
		$this->load_sources();
        $this->localize($d);
        $this->loaded = true;
        php_logger::debug("zoSource::Construct() -- LOADED");
    }
    
    function __destruct()
    {
        php_logger::log("zoSource::Destruct() -- LOADED");
        $this->save_files();
        php_logger::debug("zoSource  *** Destructed ***");

        $this->totaltime = ($this->milliseconds($this->bench));
    }

    function type() {return "source"; }
    function load($src = '') {}
    function save($f = '', $style = 'auto') {}
    function can_save() { return false; }
    function merge($scan, $root = NULL, $item = NULL, $persist = NULL) { return false; }

    function config_dir() { return __DIR__;    }
    function include_handlers() { include_once("handlers.php"); }
    function include_functions() { include_once("functions.php"); }

    function localizeto($d) { $old = getcwd(); @chdir($d); return $old; }
    function localize($d = "") { if ($d == "") return $this->localizeto($d); @chdir($d); return false; }
    function stat_load() { if (!$this->AJAX) echo "This form was generated in " . $this->totaltime . " seconds"; }
    function stat_files() { print "<br/>zoSource Files"; foreach ($this->sources as $f) print "<br/>" . $f . "\n"; }
    function save_files()
    {
        foreach ($this->sources as $id => $f) {
            php_logger::log("zoSource::Destruct autosave - $f");
            if (!$this->source_loaded($id)) continue;
            if ($f->modified && $f->can_save()) $f->save();        // attempt save if appropriate.
            unset($f);
            unset($this->sources[$id]);
        }
	}
    
    function milliseconds($since = 0) { $mtime = microtime(); $mtime = explode(" ", $mtime); $mtime = $mtime[1] + $mtime[0]; return $mtime - $since; }
	function backtrace($die_msg = "")
	{
		if (!function_exists("debug_backtrace")) return;
		$t = debug_backtrace();
		foreach ($t as $a) {
			@print "<br/><b>" . $a['file'] . "-line #" . $a['line'] . ":</b> " . $a['function'] . "(" . implode(',', $a['args']) . ")\n";
		}
		if ($die_msg != "") die("<br/>" . $die_msg);
	}
	/////////////////////////////////////////////////////////////    
	//
	// function zdb($id)
	// {
	// 	//print "<br/>zoSource::zdb($id)";
	// 	if ($id == "wpdb") {
	// 		include_once("class-zobject-db-wpdb.php");
	// 		return new zobject_db_wpdb();
	// 	}
	// 	if (!is_array($dbs)) $dbs = array();
	// 	if ($dbs[$id] == null) {
	// 		if ($this->FetchDSPart($id, "@id") != $id) return null;
	// 		$t = $this->FetchDSpart($id, "@type");
    //
	// 		$db_host = $this->FetchDSPart($id, "@host");
	// 		$db_user = $this->FetchDSPart($id, "@user");
	// 		$db_pass = $this->FetchDSPart($id, "@pass");
	// 		$db_name = $this->FetchDSPart($id, "@dbname");
    //
	// 		//print "<br/>zoSource::zdb: type=$t, host=$db_host, user=$db_user, pass=$db_pass, name=$db_name";
	// 		switch ($t) {
	// 			case "odbc":
	// 				include_once("class-zobject-db-odbc.php");
	// 				//                    $dbs[$id] = $l;
	// 				break;
	// 			case "mysql":
	// 				include_once("class-zobject-db-mysql.php");
	// 				$l = new zobject_db_mysql($id, $db_host, $db_user, $db_pass, $db_name);
	// 				if (!$l->db) unset($l);
	// 				else $dbs[$id] = $l;
	// 				break;
	// 			default:
	// 				trigger_error("Unable to connect to desired datasource [" . $id . "] because the type is unknown: " . $t, E_USER_WARNING);
	// 		}
	// 	}
	// 	return $dbs[$id];
	// }
	//
    /////////////////////////////////////////////////////////////    
    function load_sources() { }
    function source_exists($id) { return isset($this->sources[$id]); }
	function source_loaded($id) { return $this->source_exists($id) && !is_string($this->sources[$id]); }
    function get_file_id($file)
    {
        foreach ($this->sources as $k => $f) if ($f->filename == $file) return $k;
        return "";
    }
    function add_source($id, $D, $force = false)
    {
        php_logger::log("zoSource::add_source($id, ...)");
        if (!$force && $this->source_exists($id)) return die("<br/>This id already exists: $id");
        return !!($this->sources[$id] = $D);
    }
    function add_file($file)
    {
        if (($f = $this->get_file_id($file)) != "") return $f;
        $this->add_source($id = uniqid(), $file);
        return $id;
    }
    function add_xml_source($id, $x, $force = false)
    {
        if (!$force && $this->source_exists($id)) return die("<br/>This id already exists: $id");
        $this->sources[$id] = $x;
        return true;
    }
    function remove_source($id, $save = true)
    {
        php_logger::log("remove_source($id)");
        if (!$this->source_exists($id)) return false;
        if ($save && $this->source_loaded($id)) @$this->sources[$id]->save();
        unset($this->sources[$id]);
    }
    function load_source($id, $f = "")
    {
        php_logger::log("source::load_source($id, $f)");
        if (!$this->source_exists($id)) return false;
        if ($this->source_loaded($id)) return true;
        if ($f != "")  $this->sources[$id] = $f;
        else $f = $this->sources[$id];

        php_logger::log("source::load_source - sourceid=$f");
        if (substr($f, 0, 6) == "mysql:") {
            $this->parse_db_args(substr($f, 6), $host, $user, $pass, $name);
            php_logger::log("Loading mysql source... host=$host, user=$user, pass=$pass, name=$name");
            $this->source[$id] = new mysql_db_source($host, $user, $pass, $name);
            php_logger::log("mysql source loaded");
        } else if (substr($f, 0, 3) == "mysql:") {
            $this->source[$id] = new odbc_db_source();
        } else {
            $this->sources[$id] = new xml_source($f);
        }
        return true;
    }
    function parse_db_args($str, &$host, &$user, &$pass, &$name) {
        $host = '';
        $user = 'root';
        $pass = '';
        $name = 'test';

        $p = explode("@", $str);
        $user_part = sizeof($p) >= 2 ? $p[0] : '';
        $host_part = sizeof($p) >= 2 ? $p[1] : $p[0];
// print "\n<br/>user_part=$user_part, host_part=$host_part";

        if ($user_part != '') {
            $r = explode(':', $user_part);
            $user = $r[0];
            $pass = sizeof($r) >= 2 ? $r[1] : '';
        }

        $s = explode("/", $host_part);

        $host = $s[0];
        $name = sizeof($s) >= 2 ? $s[1] : 'test';
// print "\n<br/>Parsing mysql opts... host=$host, user=$user, pass=$pass, name=$name";
    }
    function force_unknown_document($file)
    {
        php_logger::log("force_document_unkonwn($file)");
        $id = $this->add_file($file);
        return $this->get_source($id);
    }

    function get_source($id)
    {
        if (!$this->source_exists($id)) return null;
        if (!$this->source_loaded($id)) $this->load_source($id);
        return $this->sources[$id];
    }

    function get_source_doc($id)
    {
        if (!($x = $this->get_source($id))) return null;
        return $x->Doc;
    }
    function force_document($id, $file)
    {
        php_logger::log("force_document($id, $file)");
        if (!$this->source_exists($id)) $this->add_xml_source($id, new xml_source($file));
        return $this->get_source($id);
    }

    function initialize_datasource($id)
    {
        if ($this->get("//SYS/*/datasource[@id='$id']/@type") <> "xml") return false;
        $r = $this->get("//SYS/*/datasource[@id='$id']/@src");
        return $this->add_xml_source($id, new xml_file(FilePath('', $r)));
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
            $this->backtrace("SOURCE NOT FOUND: [$id]");
        $t[0] = "";
        $p = implode("/", $t);
        return true;
    }
    function nde($p)
    {
        if (!$this->path_split($id, $p)) return "";
        if (!($s = $this->get_source($id))) return "";
        return $s->fetch_node($p);
    }
    function nds($p)
    {
        if (!$this->path_split($id, $p)) return "";
        if (!($s = $this->get_source($id))) return "";
        return $s->fetch_nodes($p);
    }
    function def($p)
    {
        if (!$this->path_split($id, $p)) return "";
        if (!($s = $this->get_source($id))) return "";
        return $s->part_string($p);
    }
    function get($p)
    {
        php_logger::trace("source::get($p)");
        if (!$this->path_split($id, $p)) return "";
//print "\nid=$id, p=$p";
        if (!($s = $this->get_source($id))) return "";
//print "\nCalling on source $s";
        return $s->fetch_part($p);
    }
    function set($p, $x)
    {
        if (!$this->path_split($id, $p)) return "";
        if (!($s = $this->get_source($id))) return "";
        return $s->set_part($p, $x);
    }
    function lst($p)
    {
        if (!$this->path_split($id, $p)) return "";
        if (!($s = $this->get_source($id))) return "";
        return $s->fetch_list($p);
    }
    function cnt($p)
    {
        if (!$this->path_split($id, $p)) return "";
        if (!($s = $this->get_source($id))) return "";
        return $s->count_parts($p);
    }
    function del($p)
    {
        if (!$this->path_split($id, $p)) return "";
        if (!($s = $this->get_source($id))) return "";
        return $s->del($p);
    }
}

if (!function_exists("source")) {
    function source() { return source::instance(); }
}
