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
		Database::exec("
		CREATE TABLE IF NOT EXISTS `sync_notifications`
		(
			`site_handle`			varchar(22)					NOT NULL	DEFAULT '',
			
			`uni_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`sender_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			`message`				varchar(150)				NOT NULL	DEFAULT '',
			`url`					varchar(64)					NOT NULL	DEFAULT '',
			
			`date_created`			int(10)			unsigned	NOT NULL	DEFAULT '0'
			
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
		
		// Make sure the newly installed tables exist
		return DatabaseAdmin::columnsExist("sync_notifications", array("uni_id", "message"));
	}
}
