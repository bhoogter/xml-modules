<?php
// Place the dependency manager phar in the same directory ()
define('DEPENDENCY_MANAGER_PHAR', __DIR__ . "/php-dependency-manager.phar");
require_once("phar://" . DEPENDENCY_MANAGER_PHAR . "/src/class-dependency-manager.php");
dependency_manager("source");

spl_autoload_register(function ($name) {
    if ($name == "source") require_once(__DIR__ . "/class-source.php");
    if ($name == "source_classifier") require_once(__DIR__ . "/class-source-classifier.php");
    if ($name == "xml_source") require_once(__DIR__ . "/class-xml-source.php");
    if ($name == "xml_merge") require_once(__DIR__ . "/class-xml-merge.php");
});
