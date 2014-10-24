<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

class AddNotificationAPI_config {
	
	
/****** Plugin Variables ******/
	public $pluginType = "api";
	public $pluginName = "AddNotificationAPI";
	public $title = "Add Notification API";
	public $version = 1.0;
	public $author = "Brint Paris";
	public $license = "UniFaction License";
	public $website = "http://unifaction.com";
	public $description = "Allows another site to push a notification for a user.";
	
	public $data = array();
	
}