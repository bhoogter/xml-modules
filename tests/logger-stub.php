<?php

if (!class_exists("php_logger")) {
	class php_logger {
		static function headling(...$k) {}
		static function alert(...$k) {}
		static function info(...$k) {}
		static function call(...$k) {}
		static function result(...$k) {}
		static function log(...$k) {}
		static function debug(...$k) {}
		static function trace(...$k) {}
		static function dump(...$k) {}
    }
}