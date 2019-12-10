<?php

date_default_timezone_set('America/New_York');
include_once('class-module.php');

class juniper_source
{
    private $bench;
    private $sources;
    public  $loaded;

    function __construct()
    {
        $mtime = microtime();
        $mtime = explode(" ", $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $this->bench = $mtime;
        $this->localize(__DIR__);
        //print "<br/>zoSource::Construct() -- LOADING";
        //$this->backtrace();
        $this->sources = array();
        $this->add_xml_source("SYS", new juniper_modules());
        $this->localize($d);
        $this->loaded = true;
        //print "<br/>zoSource::Construct() -- LOADED";
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

    function config_dir()    {        return __DIR__;    }
    function include_handlers()    { include_once("handlers.php"); }
    function include_functions()     { include_once("functions.php"); }

    function localizeto($d)    {        $old = getcwd();        @chdir($d);        return $old;    }
    function localize($d = "")    { if ($d == "") return $this->localizeto($d); @chdir($d); return false; }
    function stat_load()    {        if (!$this->AJAX) echo "This form was generated in " . $totaltime . " seconds";    }
    function stat_files()
    {
        print "<br/>zoSource Files";
        foreach ($this->sources as $f) print "<br/>" . $f . "\n";
    }
    function save_files()
    {
        foreach ($this->sources as $id => $f) {
            //print "<br/>zoSource::Destruct autosave - $f";
            if ($f->modified && $f->can_save()) $f->save();        // attempt save if appropriate.
            unset($f);
            unset($this->sources[$id]);
        }
    }
    //    function zdb($id)
    //        {
    ////print "<br/>zoSource::zdb($id)";
    //        if ($id=="wpdb") 
    //            {
    //            include_once("class-zobject-db-wpdb.php");
    //            return new zobject_db_wpdb();
    //            }
    //        if (!is_array($dbs)) $dbs = array();
    //        if ($dbs[$id]==null)
    //            {
    //            if ($this->FetchDSPart($id, "@id")!=$id) return null;
    //            $t = $this->FetchDSpart($id, "@type");
    //
    //            $db_host = $this->FetchDSPart($id, "@host");
    //            $db_user = $this->FetchDSPart($id, "@user");
    //            $db_pass = $this->FetchDSPart($id, "@pass");
    //            $db_name = $this->FetchDSPart($id, "@dbname");
    //
    ////print "<br/>zoSource::zdb: type=$t, host=$db_host, user=$db_user, pass=$db_pass, name=$db_name";
    //            switch($t)
    //                {
    //                case "odbc":
    //                    include_once("class-zobject-db-odbc.php");
    ////                    $dbs[$id] = $l;
    //                    break;
    //                case "mysql":
    //                    include_once("class-zobject-db-mysql.php");
    //                    $l = new zobject_db_mysql($id, $db_host, $db_user, $db_pass, $db_name);
    //                    if (!$l->db) unset($l); else $dbs[$id] = $l;
    //                    break;
    //                default:
    //                    trigger_error("Unable to connect to desired datasource [".$id."] because the type is unknown: ".$t, E_USER_WARNING);
    //                }
    //            }
    //        return $dbs[$id];
    //        }
    //
    function source_exists($id) { return isset($this->sources[$id]); }
	function source_loaded($id) { return $this->source_exists($id) && !is_string($this->sources[$id]); }
    function get_file_id($file)
    {
        foreach ($this->sources as $k => $f) if ($f->filename == $file) return $k;
        return "";
    }
    function add_source($id, $D)
    {
        //print "<br/>JUNIPER_SOURCE::add_source($id, ...)";
        if ($this->source_exists($id)) return die("<br/>This id already exists: $id");
        return !!($this->sources[$id] = $D);
    }
    function add_file($file)
    {
        if (($f = $this->get_file_id($file)) != "") return $f;
        $this->add_source($id = uniqid(), $file);
        return $id;
    }
    function add_xml_source($id, $x)
    {
        if ($this->source_exists($id)) return die("<br/>This id already exists: $id");
        $this->sources[$id] = $x;
        return true;
    }
    function remove_source($id, $save=true)
    {
        //print "<br/>remove_source($id)";
		if (!$this->source_exists($id)) return false;
		if ($save && $this->source_loaded($id)) @$this->sources[$id]->save();
        unset($this->sources[$id]);
    }
    function load_source($id, $f = "")
    {
		if (!$this->source_exists($id)) return false;
		if ($this->source_loaded($id)) return true;
		if ($f == "") $f = $this->sources[$id];
        $this->sources[$id] = new xml_file($f);
        return true;
    }
    function force_unknown_document($file)
    {
        //print "<br/>force_document_unkonwn($file)";
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
        //print "<br/>force_document($id, $file)";
        if (!$this->source_exists($id)) $this->add_xml_source($id, new xml_file($file));
        return $this->get_source($id);
    }

	private function is_path($p)     {        return substr($p, 0, 2) == "//";	}
	
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
}
