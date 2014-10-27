<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Sync Site (Notifications) Installation
abstract class Install extends Installation {
	
	
/****** Plugin Variables ******/
	
	// These addon plugins will be selected for installation during the "addon" installation process:
	public static $addonPlugins = array(	// <str:bool>
	//	"ExamplePlugin"		=> true
	//,	"AnotherPlugin"		=> true
	);
	
	
/****** App-Specific Installation Processes ******/
	public static function setup(
	)					// RETURNS <bool>
	
	{
		return true;
	}
}
