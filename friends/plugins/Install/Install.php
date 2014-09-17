<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Sync Site (Friends) Installation
abstract class Install extends Installation {
	
	
/****** Plugin Variables ******/
	
	// These addon plugins will be selected for installation during the "addon" installation process:
	public static $addonPlugins = array(	// <str:bool>
	//	"ExamplePlugin"		=> true
	//,	"AnotherPlugin"		=> true
	);
	
	
/****** App-Specific Installation Processes ******/
	public static function setup(
	)					// RETURNS <bool> TRUE on success, FALSE on failure.
	
	{
		Database::exec("
		CREATE TABLE IF NOT EXISTS `sync_friends`
		(
			`site_handle`			varchar(22)					NOT NULL	DEFAULT '',
			
			`uni_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`friend_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			`view_clearance`		int(10)			unsigned	NOT NULL	DEFAULT '0',
			`interact_clearance`	int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			UNIQUE (`site_handle`, `uni_id`, `friend_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 PARTITION BY KEY(site_handle) PARTITIONS 31;
		");
		
		// Make sure the newly installed tables exist
		return DatabaseAdmin::columnsExist("sync_friends", array("site_handle", "uni_id", "friend_id"));
	}
}
