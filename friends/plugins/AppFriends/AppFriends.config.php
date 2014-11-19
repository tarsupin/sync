<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } 

class AppFriends_config {
	
	
/****** Plugin Variables ******/
	public $pluginType = "standard";
	public $pluginName = "AppFriends";
	public $title = "Friend Plugin";
	public $version = 1.0;
	public $author = "Brint Paris";
	public $license = "UniFaction License";
	public $website = "http://unifaction.com";
	public $description = "Provides tools for working with friends on the sync site.";
	
	public $data = array();
	
	
/****** Install this plugin ******/
	public function install (
	)			// <bool> RETURNS TRUE on success, FALSE on failure.
	
	// $plugin->install();
	{
		Database::exec("
		CREATE TABLE IF NOT EXISTS `friends_active`
		(
			`uni_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`friend_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			UNIQUE (`uni_id`, `friend_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 PARTITION BY KEY(uni_id) PARTITIONS 61;
		");
		
		Database::exec("
		CREATE TABLE IF NOT EXISTS `friends_list`
		(
			`uni_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			`friend_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`strength`				mediumint(6)	unsigned	NOT NULL	DEFAULT '0',
			
			UNIQUE (`uni_id`, `friend_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 PARTITION BY KEY(uni_id) PARTITIONS 61;
		");
		
		Database::exec("
		CREATE TABLE IF NOT EXISTS `users_activity`
		(
			`uni_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`last_activity`			int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			UNIQUE (`uni_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 PARTITION BY KEY(uni_id) PARTITIONS 61;
		");
		
		return $this->isInstalled();
	}
	
	
/****** Check if the plugin was successfully installed ******/
	public static function isInstalled (
	)			// <bool> TRUE if successfully installed, FALSE if not.
	
	// $plugin->isInstalled();
	{
		// Make sure the newly installed tables exist
		$pass1 = DatabaseAdmin::columnsExist("friends_active", array("uni_id", "friend_id"));
		$pass2 = DatabaseAdmin::columnsExist("friends_list", array("uni_id", "friend_id"));
		$pass3 = DatabaseAdmin::columnsExist("friends_requests", array("uni_id", "friend_id"));
		$pass4 = DatabaseAdmin::columnsExist("users_activity", array("uni_id", "last_activity"));
		
		return ($pass1 and $pass2 and $pass3 and $pass4);
	}
	
}