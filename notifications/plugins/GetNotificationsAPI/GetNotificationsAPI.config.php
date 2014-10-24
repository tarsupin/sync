<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

class GetNotificationsAPI_config {
	
	
/****** Plugin Variables ******/
	public $pluginType = "api";
	public $pluginName = "GetNotificationsAPI";
	public $title = "Retrieve Notifications API";
	public $version = 1.0;
	public $author = "Brint Paris";
	public $license = "UniFaction License";
	public $website = "http://unifaction.com";
	public $description = "Allows a site to pull notifications for a specific user.";
	
	public $data = array();
	
}