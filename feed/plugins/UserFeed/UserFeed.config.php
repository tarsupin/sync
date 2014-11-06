<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

class UserFeed_config {
	
	
/****** Plugin Variables ******/
	public $pluginType = "official";
	public $pluginName = "UserFeed";
	public $title = "User Feed Plugin";
	public $version = 1.0;
	public $author = "Brint Paris";
	public $license = "UniFaction License";
	public $website = "http://unifaction.com";
	public $description = "Provides methods of handling the user feed.";
	
	public $data = array();
	
	
/****** Install this plugin ******/
	public function install (
	)			// <bool> RETURNS TRUE on success, FALSE on failure.
	
	// $plugin->install();
	{
		Database::exec("
		CREATE TABLE IF NOT EXISTS `feed_following`
		(
			`uni_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`hashtag`				varchar(22)					NOT NULL	DEFAULT '',
			
			UNIQUE (`uni_id`, `hashtag`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 PARTITION BY KEY(uni_id) PARTITIONS 31;
		");
		
		Database::exec("
		CREATE TABLE IF NOT EXISTS `feed_display`
		(
			`uni_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`feed_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			UNIQUE (`uni_id`, `feed_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 PARTITION BY KEY(uni_id) PARTITIONS 31;
		");
		
		// Add a column to the users table
		DatabaseAdmin::addColumn("users", "last_feed_update", "int(10) unsigned not null", 0);
		DatabaseAdmin::addColumn("users", "last_feed_id", "int(10) unsigned not null", 0);
		
		return $this->isInstalled();
	}
	
	
/****** Check if the plugin was successfully installed ******/
	public static function isInstalled (
	)			// <bool> TRUE if successfully installed, FALSE if not.
	
	// $plugin->isInstalled();
	{
		// Make sure the newly installed tables exist
		$pass1 = DatabaseAdmin::columnsExist("feed_following", array("uni_id", "hashtag"));
		$pass2 = DatabaseAdmin::columnsExist("feed_display", array("uni_id", "feed_id"));
		$pass3 = DatabaseAdmin::columnsExist("users", array("last_feed_update", "last_feed_id"));
		
		return ($pass1 and $pass2 and $pass3);
	}
	
}
