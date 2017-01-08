<?php
namespace Database;

class Config {
	static $confArray;
// заменить на трейт для геттеров и сеттеров
	public static function get($name){
		return self::$confArray[$name];
	}

	public static function set($name, $value){
		self::$confArray[$name] = $value;
	}

}

Config::set('db.host', '127.0.0.1');
//Config::set('db.port', '5432');
Config::set('db.basename', 'extjs_store');
Config::set('db.user', 'root');
Config::set('db.password', 'tech88');