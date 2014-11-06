<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

class AppPrivate_config {
	
	
/****** Plugin Variables ******/
	public $pluginType = "standard";
	public $pluginName = "AppPrivate";
	public $title = "Chat Plugin";
	public $version = 1.0;
	public $author = "Brint Paris";
	public $license = "UniFaction License";
	public $website = "http://unifaction.com";
	public $description = "Provides functionality for private chat rooms.";
	
	public $data = array();
	
	
/****** Install this plugin ******/
	public function install (
	)			// <bool> RETURNS TRUE on success, FALSE on failure.
	
	// $plugin->install();
	{
		Database::exec("
		CREATE TABLE IF NOT EXISTS `chat_channels_private`
		(
			`id`					int(10)			unsigned	NOT NULL	AUTO_INCREMENT,
			`channel`				varchar(22)					NOT NULL	DEFAULT '',
			`password`				varchar(32)					NOT NULL	DEFAULT '',
			
			PRIMARY KEY (`id`),
			UNIQUE (`channel`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
		
		Database::exec("
		CREATE TABLE IF NOT EXISTS `chat_messages_private`
		(
			`channel_id`			int(10)			unsigned	NOT NULL	DEFAULT '0',
			`date_posted`			double(18,4)	unsigned	NOT NULL	DEFAULT '0.0000',
			
			`uni_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`message`				varchar(250)				NOT NULL	DEFAULT '',
			
			UNIQUE (`channel_id`, `date_posted`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 PARTITION BY KEY (`channel_id`) PARTITIONS 61;
		");
		
		return $this->isInstalled();
	}
	
	
/****** Check if the plugin was successfully installed ******/
	public static function isInstalled (
	)			// <bool> TRUE if successfully installed, FALSE if not.
	
	// $plugin->isInstalled();
	{
		// Make sure the newly installed tables exist
		$pass1 = DatabaseAdmin::columnsExist("chat_channels_private", array("id", "channel"));
		$pass2 = DatabaseAdmin::columnsExist("chat_messages_private", array("channel_id", "date_posted"));
		
		return ($pass1 and $pass2);
	}
	
}
