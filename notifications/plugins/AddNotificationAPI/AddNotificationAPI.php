<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This API allows a notification to be added to a user's notification list.


------------------------------
------ Calling this API ------
------------------------------
	
	// Prepare the Packet with list of notifications
	$packet = array("uni_id" => $uniID, "url" => $url, "message" => $message);
	
	// Run the API
	Connect::to("sync_notifications", "AddNotificationAPI", $packet);
	
*/

class AddNotificationAPI extends API {
	
	
/****** API Variables ******/
	public $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public $encryptType = "";			// <str> The encryption algorithm to use for response, or "" for no encryption.
	public $allowedSites = array();		// <int:str> the sites to allow the API to connect with. Default is all sites.
	public $microCredits = 25;			// <int> The cost in microcredits (1/10000 of a credit) to access this API.
	public $minClearance = 0;			// <int> The clearance level required to use this API.
	
	
/****** Run the API ******/
	public function runAPI (
	)					// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// $this->runAPI()
	{
		// Make sure the appropriate information is received
		if(!isset($this->data['uni_id']) or !isset($this->data['url']) or !isset($this->data['message']))
		{
			return false;
		}
		
		return AppNotifications::add((int) $this->data['uni_id'], $this->data['url'], $this->data['message']);
	}
}
