<?hh if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Sync Site (Notifications) Installation
abstract class Install extends Installation {
	
	
/****** Plugin Variables ******/
	
	// These addon plugins will be selected for installation during the "addon" installation process:
	public static array <str, bool> $addonPlugins = array(	// <str:bool>
	//	"ExamplePlugin"		=> true
	//,	"AnotherPlugin"		=> true
	);
	
	
/****** App-Specific Installation Processes ******/
	public static function setup(
	): bool					// RETURNS <bool>
	
	{
		return true;
	}
}