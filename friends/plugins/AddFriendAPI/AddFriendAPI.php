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
		"uni_id"		=> $uniID
	,	"friend_id"		=> $friendID
	,	"request"		=> false			// Set to TRUE if this is a request, FALSE if not.
	);
	
	// Run the API
	Connect::to("sync_friends", "AddFriendAPI", $packet);
	
*/

class AddFriendAPI extends API {
	
	
/****** API Variables ******/
	public $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public $encryptType = "";			// <str> The encryption algorithm to use for response, or "" for no encryption.
	public $allowedSites = array();		// <int:str> the sites to allow the API to connect with. Default is all sites.
	public $microCredits = 25;			// <int> The cost in microcredits (1/10000 of a credit) to access this API.
	public $minClearance = 8;			// <int> The clearance level required to use this API.
	
	
/****** Run the API ******/
	public function runAPI (
	)					// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// $this->runAPI()
	{
		// Make sure the appropriate information is received
		if(!isset($this->data['uni_id']) or !isset($this->data['friend_id']))
		{
			return false;
		}
		
		if(!isset($this->data['request']))
		{
			$this->data['request'] = true;
		}
		
		// Run a friend request (if applicable)
		if($this->data['request'])
		{
			return AppFriends::request($this->data['uni_id'], $this->data['friend_id']);
		}
		
		// Force a friend update (if not a request)
		return AppFriends::add($this->data['uni_id'], $this->data['friend_id']);
	}
}
