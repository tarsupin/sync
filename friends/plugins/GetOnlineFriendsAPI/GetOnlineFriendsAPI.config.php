<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

class GetOnlineFriendsAPI_config {
	
	
/****** Plugin Variables ******/
	public $pluginType = "api";
	public $pluginName = "GetOnlineFriendsAPI";
	public $title = "Get Friends API";
	public $version = 1.0;
	public $author = "Brint Paris";
	public $license = "UniFaction License";
	public $website = "http://unifaction.com";
	public $description = "Allows a site to pull a list of online friends for a user.";
	
	public $data = array();
	
}