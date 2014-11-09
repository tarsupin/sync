<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

class MyFeedAPI_config {
	
	
/****** Plugin Variables ******/
	public $pluginType = "api";
	public $pluginName = "MyFeedAPI";
	public $title = "Retrieve Feed API";
	public $version = 1.0;
	public $author = "Brint Paris";
	public $license = "UniFaction License";
	public $website = "http://unifaction.com";
	public $description = "Allows feeds to be retrieved from external sites.";
	
	public $data = array();
	
}