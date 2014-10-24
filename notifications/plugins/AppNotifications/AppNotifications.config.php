<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } 

class AppNotifications_config {
	
	
/****** Plugin Variables ******/
	public $pluginType = "standard";
	public $pluginName = "AppNotifications";
	public $title = "Notifications Plugin";
	public $version = 1.0;
	public $author = "Brint Paris";
	public $license = "UniFaction License";
	public $website = "http://unifaction.com";
	public $description = "Provides tools for working with notifications on the sync site.";
	
	public $data = array();
	
	
/****** Install this plugin ******/
	public function install (
	)			// <bool> RETURNS TRUE on success, FALSE on failure.
	
	// $plugin->install();
	{
		Database::exec("
		CREATE TABLE IF NOT EXISTS `notifications`
		(
			`uni_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			`url`					varchar(100)				NOT NULL	DEFAULT '',
			`message`				varchar(120)				NOT NULL	DEFAULT '',
			
			`date_created`			int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			INDEX (`uni_id`, `date_created`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 PARTITION BY KEY(uni_id) PARTITIONS 61;
		");
		
		// Update the users table with a notification count
		DatabaseAdmin::addColumn("users", "notify_count", "tinyint(3) unsigned not null", 0);
		
		return $this->isInstalled();
	}
	
	
/****** Check if the plugin was successfully installed ******/
	public static function isInstalled (
	)			// <bool> TRUE if successfully installed, FALSE if not.
	
	// $plugin->isInstalled();
	{
		// Make sure the newly installed tables exist
		$pass1 = DatabaseAdmin::columnsExist("notifications", array("uni_id", "url", "message"));
		$pass2 = DatabaseAdmin::columnsExist("users", array("notify_count"));
		
		return ($pass1 and $pass2);
	}
	
}