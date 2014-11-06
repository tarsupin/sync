<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

class FollowHashtagAPI_config {
	
	
/****** Plugin Variables ******/
	public $pluginType = "api";
	public $pluginName = "FollowHashtagAPI";
	public $title = "Hashtag Tracking";
	public $version = 1.0;
	public $author = "Brint Paris";
	public $license = "UniFaction License";
	public $website = "http://unifaction.com";
	public $description = "Allows hashtags to be followed or un-followed.";
	
	public $data = array();
	
}