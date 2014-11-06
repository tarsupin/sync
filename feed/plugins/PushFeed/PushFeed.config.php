<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

class PushFeed_config {
	
	
/****** Plugin Variables ******/
	public $pluginType = "api";
	public $pluginName = "PushFeed";
	public $title = "Feed Push API";
	public $version = 1.0;
	public $author = "Brint Paris";
	public $license = "UniFaction License";
	public $website = "http://unifaction.com";
	public $description = "Allows content to be pushed to the feed system.";
	
	public $data = array();
	
}