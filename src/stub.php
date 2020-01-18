<?php
// Place the dependency manager phar in the same directory ()
define('DEPENDENCY_MANAGER_PHAR', __DIR__ . "/php-dependency-manager.phar");
require_once("phar://" .    DEPENDENCY_MANAGER_PHAR . "/src/class-dependency-manager.php");
dependency_manager("source");

spl_autoload_register(function ($name) {
    $d = (strpos(__FILE__, ".phar") === false ? __DIR__ : "phar://" . __FILE__ . "/src");
    if ($name == "source") require_once($d . "/class-source.php");
    if ($name == "source_classifier") require_once($d . "/classifiers/class-source-classifier.php");
    if ($name == "xml_source") require_once($d . "/classifiers/class-xml-source.php");
    if ($name == "xml_merge") require_once($d . "/classifiers/class-xml-merge.php");
});

__HALT_COMPILER();
