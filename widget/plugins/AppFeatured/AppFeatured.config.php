<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } 

class AppFeatured_config {
	
	
/****** Plugin Variables ******/
	public $pluginType = "standard";
	public $pluginName = "AppFeatured";
	public $title = "Featured Widget Handler";
	public $version = 1.0;
	public $author = "Brint Paris";
	public $license = "UniFaction License";
	public $website = "http://unifaction.com";
	public $description = "Provides a system for handling the featured widget.";
	
	public $data = array();
	
	
/****** Install this plugin ******/
	public function install (
	)			// <bool> RETURNS TRUE on success, FALSE on failure.
	
	// $plugin->install();
	{
		Database::exec("
		CREATE TABLE IF NOT EXISTS `widget_featured`
		(
			`id`					int(10)			unsigned	NOT NULL	AUTO_INCREMENT,
			
			`title`					varchar(22)					NOT NULL	DEFAULT '',
			`description`			varchar(72)					NOT NULL	DEFAULT '',
			`url`					varchar(100)				NOT NULL	DEFAULT '',
			
			`views`					int(10)			unsigned	NOT NULL	DEFAULT '0',
			`clicks`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			`date_created`			int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
		
		Database::exec("
		CREATE TABLE IF NOT EXISTS `widget_featured_pull`
		(
			`hashtag`				varchar(22)					NOT NULL	DEFAULT '',
			`category`				varchar(16)					NOT NULL	DEFAULT '',
			`verb`					varchar(16)					NOT NULL	DEFAULT '',
			
			`entry_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			UNIQUE (`hashtag`, `category`, `verb`, `entry_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
		
		return $this->isInstalled();
	}
	
	
/****** Check if this plugin was successfully installed ******/
	public static function isInstalled (
	)			// <bool> RETURNS TRUE on success, FALSE on failure.
	
	// $plugin->isInstalled();
	{
		// Make sure the newly installed tables exist
		$pass1 = DatabaseAdmin::columnsExist("widget_featured", array("id", "title"));
		$pass2 = DatabaseAdmin::columnsExist("widget_featured_pull", array("hashtag", "category"));
		
		return ($pass1 and $pass2);
	}
	
}