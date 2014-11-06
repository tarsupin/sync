<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

class FeedCore_config {
	
	
/****** Plugin Variables ******/
	public $pluginType = "official";
	public $pluginName = "FeedCore";
	public $title = "Core Feed Plugin";
	public $version = 1.0;
	public $author = "Brint Paris";
	public $license = "UniFaction License";
	public $website = "http://unifaction.com";
	public $description = "Provides the core functionality for the feed system.";
	
	public $data = array();
	
	
/****** Install this plugin ******/
	public function install (
	)			// <bool> RETURNS TRUE on success, FALSE on failure.
	
	// $plugin->install();
	{
		Database::exec("
		CREATE TABLE IF NOT EXISTS `feed_data`
		(
			`id`					int(10)			unsigned	NOT NULL	AUTO_INCREMENT,
			
			`site_handle`			varchar(22)					NOT NULL	DEFAULT '',
			`primary_hashtag`		varchar(22)					NOT NULL	DEFAULT '',
			`hashtags`				varchar(250)				NOT NULL	DEFAULT '',
			
			`author_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`url`					varchar(100)				NOT NULL	DEFAULT '',
			`title`					varchar(72)					NOT NULL	DEFAULT '',
			`description`			varchar(255)				NOT NULL	DEFAULT '',
			`thumbnail`				varchar(100)				NOT NULL	DEFAULT '',
			
			`date_posted`			int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
		
		Database::exec("
		CREATE TABLE IF NOT EXISTS `feed_data_old`
		(
			`id`					int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			`site_handle`			varchar(22)					NOT NULL	DEFAULT '',
			`primary_hashtag`		varchar(22)					NOT NULL	DEFAULT '',
			`hashtags`				varchar(250)				NOT NULL	DEFAULT '',
			
			`author_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`url`					varchar(100)				NOT NULL	DEFAULT '',
			`title`					varchar(72)					NOT NULL	DEFAULT '',
			`description`			varchar(255)				NOT NULL	DEFAULT '',
			`thumbnail`				varchar(100)				NOT NULL	DEFAULT '',
			
			`date_posted`			int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			UNIQUE (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 PARTITION BY KEY(id) PARTITIONS 61;
		");
		
		Database::exec("
		CREATE TABLE IF NOT EXISTS `feed_posts`
		(
			`hashtag`				varchar(22)					NOT NULL	DEFAULT '',
			`feed_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			UNIQUE (`hashtag`, `feed_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 PARTITION BY KEY(hashtag) PARTITIONS 31;
		");
		
		return $this->isInstalled();
	}
	
	
/****** Check if the plugin was successfully installed ******/
	public static function isInstalled (
	)			// <bool> TRUE if successfully installed, FALSE if not.
	
	// $plugin->isInstalled();
	{
		// Make sure the newly installed tables exist
		$pass1 = DatabaseAdmin::columnsExist("feed_data", array("id", "url", "title"));
		$pass2 = DatabaseAdmin::columnsExist("feed_data_old", array("id", "url", "title"));
		$pass3 = DatabaseAdmin::columnsExist("feed_posts", array("hashtag", "feed_id"));
		
		return ($pass1 and $pass2 and $pass3);
	}
	
}
