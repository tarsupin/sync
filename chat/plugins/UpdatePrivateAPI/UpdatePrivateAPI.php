<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This API allows a user to add a friend.


------------------------------
------ Calling this API ------
------------------------------
	
	// Prepare the packet with details about adding the friend
	$packet = array(
		"channel"			=> $channel				// Channel to create (or update)
	,	"password"			=> $password			// The password to set (or update)
	,	"current_password"	=> $currentPassword		// Set this value if it's being updated.
	);
	
	// Run the API
	$success = Connect::to("sync_pchat" . $chatServID, "UpdatePrivateAPI", $packet);
	
*/

class UpdatePrivateAPI extends API {
	
	
/****** API Variables ******/
	public $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public $encryptType = "";			// <str> The encryption algorithm to use for response, or "" for no encryption.
	public $allowedSites = array("chat");		// <int:str> the sites to allow the API to connect with. Default is all sites.
	public $microCredits = 500;			// <int> The cost in microcredits (1/10000 of a credit) to access this API.
	public $minClearance = 6;			// <int> The clearance level required to use this API.
	
	
/****** Run the API ******/
	public function runAPI (
	)					// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// $this->runAPI()
	{
		// Make sure the appropriate information is received
		if(!isset($this->data['channel']) or !isset($this->data['password']))
		{
			return false;
		}
		
		// Prepare Values
		if(!isSanitized::variable($this->data['channel']))
		{
			return false;
		}
		
		if(!isSanitized::safeword($this->data['password'], "/+="))
		{
			return false;
		}
		
		// If you're updating an existing channel
		if(isset($this->data['current_password']))
		{
			return AppPrivate::updateChannel($this->data['channel'], $this->data['password'], $this->data['current_password']);
		}
		
		// If you're creating a new channel
		return (bool) AppPrivate::createChannel($this->data['channel'], $this->data['password']);
	}
}
