<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } 

class AppMessages_config {
	
	
/****** Plugin Variables ******/
	public $pluginType = "standard";
	public $pluginName = "AppMessages";
	public $title = "Message Plugin";
	public $version = 1.0;
	public $author = "Brint Paris";
	public $license = "UniFaction License";
	public $website = "http://unifaction.com";
	public $description = "Provides a system for sending messages to each other.";
	
	public $data = array();
	
	
/****** Install this plugin ******/
	public function install (
	)			// <bool> RETURNS TRUE on success, FALSE on failure.
	
	// $plugin->install();
	{
		Database::exec("
		CREATE TABLE IF NOT EXISTS `user_messages_queue`
		(
			`uni_id_1`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`uni_id_2`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`date_updated`			double(18, 4)	unsigned	NOT NULL	DEFAULT '0.0000',
			
			UNIQUE (`uni_id_1`, `uni_id_2`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 PARTITION BY KEY(uni_id_1) PARTITIONS 7;
		");
		
		Database::exec("
		CREATE TABLE IF NOT EXISTS `user_messages`
		(
			`uni_id_1`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`uni_id_2`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`date_posted`			double(18,4)	unsigned	NOT NULL	DEFAULT '0.0000',
			`poster`				tinyint(1)		unsigned	NOT NULL	DEFAULT '0',
			`message`				varchar(250)				NOT NULL	DEFAULT '',
			
			UNIQUE (`uni_id_1`, `uni_id_2`, `date_posted`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 PARTITION BY KEY(uni_id_1) PARTITIONS 61;
		");
		
		Database::exec("
		CREATE TABLE IF NOT EXISTS `user_messages_old`
		(
			`uni_id_1`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`uni_id_2`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`date_posted`			double(18,4)	unsigned	NOT NULL	DEFAULT '0.0000',
			`poster`				tinyint(1)		unsigned	NOT NULL	DEFAULT '0',
			`message`				varchar(250)				NOT NULL	DEFAULT '',
			
			UNIQUE (`uni_id_1`, `uni_id_2`, `date_posted`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 PARTITION BY KEY(uni_id_1) PARTITIONS 61;
		");
		
		return $this->isInstalled();
	}
	
	
/****** Check if the plugin was successfully installed ******/
	public static function isInstalled (
	)			// <bool> TRUE if successfully installed, FALSE if not.
	
	// $plugin->isInstalled();
	{
		// Make sure the newly installed tables exist
		$pass1 = DatabaseAdmin::columnsExist("user_messages", array("uni_id_1", "uni_id_2"));
		$pass2 = DatabaseAdmin::columnsExist("user_messages_old", array("uni_id_1", "uni_id_2"));
		
		return ($pass1 and $pass2);
	}
	
}