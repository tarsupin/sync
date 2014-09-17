<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

class PullNotifications_config {
	
	
/****** Plugin Variables ******/
	public $pluginType = "api";
	public $pluginName = "PullNotifications";
	public $title = "Notification Sync API";
	public $version = 1.0;
	public $author = "Brint Paris";
	public $license = "UniFaction License";
	public $website = "http://unifaction.com";
	public $description = "Provides a synchronization API for the sync site handling notifications.";
	
	public $data = array();
	
}