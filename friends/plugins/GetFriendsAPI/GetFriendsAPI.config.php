<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

class GetFriendsAPI_config {
	
	
/****** Plugin Variables ******/
	public $pluginType = "api";
	public $pluginName = "GetFriendsAPI";
	public $title = "Get Friends API";
	public $version = 1.0;
	public $author = "Brint Paris";
	public $license = "UniFaction License";
	public $website = "http://unifaction.com";
	public $description = "Allows a site to pull a list of friends for a user.";
	
	public $data = array();
	
}