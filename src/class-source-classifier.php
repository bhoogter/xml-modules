<?php

abstract class source_classifier {
    abstract function nde($p);
    function node($p) { return $this->nde($p); }
    function fetch_node($p) { return $this->nde($p); }
    abstract function nodes($p);
    function fetch_nodes($p) { return $this->nodes($p); }
    function nds($p) { return $this->nodes($p); }
    abstract function def($p);
    function part_string($p) { return $this->def($p); }
    abstract function get($p);
    function fetch_part($p) { return $this->get($p); }
    abstract function set($p, $x);
    function set_part($p) { return $this->set($p); }
    abstract function lst($p);
    function fetch_list($p) { return $this->lst($p); }
    abstract function cnt($p);
    function count_parts($p) { return $this->cnt($p); }
}
